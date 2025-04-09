<?php
/**
 * Sendgrid.php
 *
 * Handles sending emails using SendGrid.
 *
 * @package SureMails\Inc\Emails\Providers\SendGrid
 */

namespace SureMails\Inc\Emails\Providers\SENDGRID;

use SureMails\Inc\Emails\Handler\ConnectionHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Sendgrid
 *
 * Implements the ConnectionHandler to handle SendGrid email sending and authentication.
 */
class SendgridHandler implements ConnectionHandler {

	/**
	 * SendGrid connection data.
	 *
	 * @var array
	 */
	protected $connection_data;

	/**
	 * SendGrid Base API URL.
	 *
	 * @var string
	 */
	private $base_url = 'https://api.sendgrid.com/v3';

	/**
	 * SendGrid API endpoint for sending emails.
	 *
	 * @var string
	 */
	private $api_url = 'https://api.sendgrid.com/v3/mail/send';

	/**
	 * Constructor.
	 *
	 * Initializes connection data.
	 *
	 * @param array $connection_data The connection details.
	 */
	public function __construct( array $connection_data ) {
		$this->connection_data = $connection_data;
	}

	/**
	 * Get headers for the SendGrid connection.
	 *
	 * @return array The headers for the SendGrid connection.
	 * @param string $api_key The API key for the SendGrid connection.
	 * @since 1.0.1
	 */
	public function get_headers( $api_key ) {
		return [
			'Authorization' => 'Bearer ' . sanitize_text_field( $api_key ),
			'Content-Type'  => 'application/json',
		];
	}

	/**
	 * Authenticate the SendGrid connection by verifying the API key.
	 *
	 * @return array The result of the authentication attempt.
	 */
	public function authenticate() {
		if ( empty( $this->connection_data['api_key'] ) || empty( $this->connection_data['from_email'] ) ) {
			return [
				'success'    => false,
				'message'    => __( 'API key or From Email is missing in the connection data.', 'suremails' ),
				'error_code' => 400,
			];
		}

		$from_email = sanitize_email( $this->connection_data['from_email'] );

		// Check if the sender email is verified.
		$verified_senders = $this->get_api_data( $this->base_url . '/verified_senders', $this->get_headers( $this->connection_data['api_key'] ) );

		if ( is_wp_error( $verified_senders ) ) {
			return [
				'success'    => false,
				'message'    => __( 'Failed to retrieve verified senders: ', 'suremails' ) . $verified_senders->get_error_message(),
				'error_code' => $verified_senders->get_error_code(),
			];
		}

		$sender_verified = false;
		if ( isset( $verified_senders['results'] ) && is_array( $verified_senders['results'] ) ) {
			foreach ( $verified_senders['results'] as $sender ) {
				if ( isset( $sender['from_email'] ) && strtolower( $sender['from_email'] ) === strtolower( $from_email ) && isset( $sender['verified'] ) && $sender['verified'] ) {
					$sender_verified = true;
					break;
				}
			}
		}

		if ( $sender_verified ) {
			return [
				'success'    => true,
				'message'    => __( 'SendGrid connection authenticated successfully. Sender email is verified.', 'suremails' ),
				'error_code' => 200,
			];
		}

		$parts  = explode( '@', $from_email );
		$domain = isset( $parts[1] ) ? trim( $parts[1] ) : '';

		if ( empty( $domain ) ) {
			return [
				'success'    => false,
				'message'    => __( 'Invalid sender email format. Domain missing.', 'suremails' ),
				'error_code' => 400,
			];
		}

		$domains_response = $this->get_api_data( $this->base_url . '/whitelabel/domains', $this->get_headers( $this->connection_data['api_key'] ) );

		if ( is_wp_error( $domains_response ) ) {
			return [
				'success'    => false,
				'message'    => __( 'Failed to retrieve whitelabel domains: ', 'suremails' ) . $domains_response->get_error_message(),
				'error_code' => $domains_response->get_error_code(),
			];
		}

		$domain_verified = false;
		if ( is_array( $domains_response ) ) {
			foreach ( $domains_response as $whitelabel_domain ) {
				if ( isset( $whitelabel_domain['domain'] ) && strtolower( $whitelabel_domain['domain'] ) === strtolower( $domain ) && isset( $whitelabel_domain['valid'] ) && $whitelabel_domain['valid'] ) {
					$domain_verified = true;
					break;
				}
			}
		}

		if ( ! $domain_verified ) {
			return [
				'success'    => false,
				'message'    => __( 'SendGrid authentication failed: Sender email or domain is not verified.', 'suremails' ),
				'error_code' => rest_authorization_required_code(),
			];
		}

		return [
			'success'    => true,
			'message'    => __( 'SendGrid connection authenticated successfully. Domain of sender email is verified.', 'suremails' ),
			'error_code' => 200,
		];
	}

	/**
	 * Send an email via SendGrid, including attachments if provided.
	 *
	 * @param array $atts        The email attributes, such as 'to', 'from', 'subject', 'message', 'headers', 'attachments', etc.
	 * @param int   $log_id      The log ID for the email.
	 * @param array $connection  The connection details.
	 * @param array $processed_data The processed email data.
	 * @return array             The result of the email send operation.
	 * @throws \Exception If the email payload cannot be encoded to JSON.
	 */
	public function send( array $atts, $log_id, array $connection, $processed_data ) {
		$result = [
			'success' => false,
			'message' => '',
			'send'    => false,
		];

		$email_payload = [
			'personalizations' => [],
			'from'             => [
				'email' => sanitize_email( $connection['from_email'] ),
				'name'  => ! empty( $connection['from_name'] ) ? sanitize_text_field( $connection['from_name'] ) : __( 'WordPress', 'suremails' ),
			],
			'subject'          => sanitize_text_field( $atts['subject'] ?? '' ),
			'content'          => [],
		];

		// Prepare recipients.
		$email_payload['personalizations'][] = [
			'to' => $processed_data['to'] ?? [],
		];

		// Add CC and BCC if provided.
		if ( ! empty( $processed_data['headers']['cc'] ) ) {
			$email_payload['personalizations'][0]['cc'] = $processed_data['headers']['cc'];
		}
		if ( ! empty( $processed_data['headers']['bcc'] ) ) {
			$email_payload['personalizations'][0]['bcc'] = $processed_data['headers']['bcc'];
		}

		// Add content based on content type.
		$is_html                    = isset( $processed_data['headers']['content_type'] ) && strtolower( $processed_data['headers']['content_type'] ) === 'text/html';
		$email_payload['content'][] = [
			'type'  => $is_html ? 'text/html' : 'text/plain',
			'value' => $is_html ? $atts['message'] : wp_strip_all_tags( $atts['message'] ),
		];

		// Handle reply-to information.
		$reply_to = $processed_data['headers']['reply_to'] ?? [];
		if ( ! empty( $reply_to ) ) {
			if ( is_array( $reply_to ) && count( $reply_to ) > 1 ) {
				$email_payload['reply_to_list'] = array_map(
					static function ( $email ) {
						return [
							'email' => sanitize_email( $email['email'] ),
							'name'  => isset( $email['name'] ) ? sanitize_text_field( $email['name'] ) : '',
						];
					},
					$reply_to
				);

			} else {

				$single_reply_to           = reset( $reply_to );
				$email_payload['reply_to'] = [
					'email' => sanitize_email( $single_reply_to['email'] ),
					'name'  => isset( $single_reply_to['name'] ) ? sanitize_text_field( $single_reply_to['name'] ) : '',
				];
			}
		}

		if ( ! empty( $processed_data['attachments'] ) ) {
			$email_payload['attachments'] = array_filter(
				array_map(
					static function ( $attachment ) {
						$file_path = sanitize_text_field( $attachment );
						if ( $file_path !== false ) {
							if ( is_file( $attachment ) && file_exists( $file_path ) && is_readable( $file_path ) ) {
								$file_content = file_get_contents( $attachment );
								if ( $file_content !== false ) {
									$file_name  = basename( $attachment );
									$content_id = wp_hash( $attachment );
									$mime_type  = mime_content_type( $attachment );
									return [
										'type'        => $mime_type,
										'filename'    => $file_name,
										'disposition' => 'attachment',
										'content_id'  => $content_id,
										'content'     => base64_encode( $file_content ),
									];
								}
							}
						}
						return null; // Skip invalid or unreadable files.
					},
					$processed_data['attachments']
				)
			);
		}

		// Send email via SendGrid API.
		try {
			$json_payload = wp_json_encode( $email_payload );
			if ( $json_payload === false ) {
				throw new \Exception( __( 'Failed to encode email payload to JSON.', 'suremails' ) );
			}
			$response = wp_safe_remote_post(
				$this->api_url,
				[
					'headers' => $this->get_headers( $connection['api_key'] ),
					'body'    => $json_payload,
				]
			);

			if ( is_wp_error( $response ) ) {
				$result['message']    = __( 'SendGrid send failed: ', 'suremails' ) . $response->get_error_message();
				$result['error_code'] = $response->get_error_code();
				return $result;
			}

			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code === 202 ) { // Accepted.
				$result['success'] = true;
				$result['message'] = __( 'Email sent successfully via SendGrid.', 'suremails' );
				$result['send']    = true;
			} else {
				$response_body        = wp_remote_retrieve_body( $response );
				$decoded_body         = json_decode( $response_body, true );
				$error_message        = $decoded_body['errors'][0]['message'] ?? __( 'Unknown error.', 'suremails' );
				$result['message']    = __( 'SendGrid send failed: ', 'suremails' ) . $error_message;
				$result['error_code'] = $response_code;
			}
		} catch ( \Exception $e ) {
			$result['message']    = __( 'SendGrid send failed: ', 'suremails' ) . $e->getMessage();
			$result['error_code'] = 500;
		}

		return $result;
	}

	/**
	 * Return the option configuration for SendGrid.
	 *
	 * @return array
	 */
	public static function get_options() {
		return [
			'title'             => __( 'SendGrid Connection', 'suremails' ),
			'description'       => __( 'Enter the details below to connect with your SendGrid account.', 'suremails' ),
			'fields'            => self::get_specific_fields(),
			'display_name'      => __( 'SendGrid', 'suremails' ),
			'icon'              => 'SendGridIcon',
			'provider_type'     => 'free',
			'field_sequence'    => [ 'connection_title', 'api_key', 'from_email', 'force_from_email', 'from_name', 'force_from_name', 'priority' ],
			'provider_sequence' => 40,
		];
	}

	/**
	 * Get the specific schema fields for SendGrid.
	 *
	 * @return array
	 */
	public static function get_specific_fields() {
		return [
			'api_key' => [
				'required'    => true,
				'datatype'    => 'string',
				'help_text'   => '',
				'label'       => __( 'API Key', 'suremails' ),
				'input_type'  => 'password',
				'placeholder' => __( 'Enter your SendGrid API Key', 'suremails' ),
				'encrypt'     => true,
			],
		];
	}

	/**
	 * Retrieve data from SendGrid API using GET requests.
	 *
	 * @param string $url     The full API URL.
	 * @param array  $headers The request headers.
	 * @return array|\WP_Error The decoded response data or a WP_Error on failure.
	 */
	private function get_api_data( $url, array $headers ) {
		$response = wp_remote_get(
			$url,
			[
				'headers' => $headers,
				'timeout' => 30,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( in_array( $response_code, [ 401, 403 ], true ) ) {
			return new \WP_Error( 'unauthorized', __( 'Unauthorized: API key invalid or insufficient permissions.', 'suremails' ), $response_code );
		}

		if ( $response_code >= 400 ) {
			return new \WP_Error( 'api_error', __( 'SendGrid API returned an error.', 'suremails' ), $response_code );
		}

		$body         = wp_remote_retrieve_body( $response );
		$decoded_body = json_decode( $body, true );

		if ( null === $decoded_body ) {
			return new \WP_Error( 'json_decode_error', __( 'Failed to decode JSON response from SendGrid.', 'suremails' ), $response_code );
		}

		return $decoded_body;
	}

}
