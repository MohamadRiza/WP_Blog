<?php
/**
 * SMTP2GOHandler.php
 *
 * Handles sending emails using SMTP2GO.
 *
 * @package SureMails\Inc\Emails\Providers\SMTP2GO
 */

namespace SureMails\Inc\Emails\Providers\SMTP2GO;

use Exception;
use SureMails\Inc\Emails\Handler\ConnectionHandler;
use SureMails\Inc\Emails\ProviderHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class SMTP2GOHandler
 *
 * Implements the ConnectionHandler to handle SMTP2GO email sending and authentication.
 */
class Smtp2goHandler implements ConnectionHandler {

	/**
	 * SMTP2GO connection data.
	 *
	 * @var array
	 */
	protected $connection_data;

	/**
	 * SMTP2GO API endpoint.
	 *
	 * @var string
	 */
	private $api_url = 'https://api.smtp2go.com/v3/email/send';

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
	 * Authenticate the SMTP2GO connection by verifying the API key.
	 *
	 * @return array The result of the authentication attempt.
	 */
	public function authenticate() {
		$result = [
			'success'    => false,
			'message'    => '',
			'error_code' => 200,
		];

		try {
			$from_email = $this->connection_data['from_email'] ?? '';
			if ( empty( $from_email ) ) {
				$result['message'] = __( 'from_email is not provided.', 'suremails' );
				return $result;
			}

			$headers = $this->get_request_headers();

			// Check single sender emails.
			$single_sender_url = 'https://api.smtp2go.com/v3/single_sender_emails/view';
			$response          = wp_safe_remote_post(
				$single_sender_url,
				[
					'headers' => $headers,
					'timeout' => 10,
				]
			);
			if ( is_wp_error( $response ) ) {
				$result['message'] = __( 'SMTP2GO authentication failed: ', 'suremails' ) . $response->get_error_message();
				return $result;
			}
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $data['data']['senders'] ) && is_array( $data['data']['senders'] ) ) {
				foreach ( $data['data']['senders'] as $sender ) {
					if ( isset( $sender['email_address'] ) && strtolower( $sender['email_address'] ) === strtolower( $from_email ) ) {
						if ( ! empty( $sender['verified'] ) && true === $sender['verified'] ) {
							$result['success'] = true;
							$result['message'] = __( 'SMTP2GO authentication successful.', 'suremails' );
							return $result;
						}
						break;
					}
				}
			}

			$domain_url = 'https://api.smtp2go.com/v3/domain/view';
			$response   = wp_safe_remote_post(
				$domain_url,
				[
					'headers' => $headers,
					'timeout' => 10,
				]
			);
			if ( is_wp_error( $response ) ) {
				$result['message'] = __( 'SMTP2GO authentication failed: ', 'suremails' ) . $response->get_error_message();
				return $result;
			}
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $data['data']['domains'] ) && is_array( $data['data']['domains'] ) ) {
				$email_domain = strrchr( $from_email, '@' );
				$email_domain = $email_domain !== false ? substr( $email_domain, 1 ) : '';
				foreach ( $data['data']['domains'] as $domain_entry ) {
					if ( isset( $domain_entry['domain']['domain'] ) ) {
						$domain_val = strtolower( $domain_entry['domain']['domain'] );
						$fulldomain = isset( $domain_entry['domain']['fulldomain'] ) ? strtolower( $domain_entry['domain']['fulldomain'] ) : '';
						if ( strtolower( $email_domain ) === $domain_val || strtolower( $email_domain ) === $fulldomain ) {
							if ( isset( $domain_entry['trackers'] ) && is_array( $domain_entry['trackers'] ) ) {
								foreach ( $domain_entry['trackers'] as $tracker ) {
									if ( ! empty( $tracker['enabled'] ) && true === $tracker['enabled'] ) {
										$result['success'] = true;
										$result['message'] = __( 'SMTP2GO authentication successful via domain verification.', 'suremails' );
										return $result;
									}
								}
							}
						}
					}
				}
				$result['message'] = __( 'SMTP2GO authentication failed: The from_email or domain is not verified.', 'suremails' );
			} else {
				$result['message'] = __( 'SMTP2GO authentication failed: Unable to retrieve data. Please check if you have entered the correct API key.', 'suremails' );
			}
		} catch ( Exception $e ) {
			$result['message'] = __( 'SMTP2GO authentication failed: ', 'suremails' ) . $e->getMessage();
		}

		return $result;
	}

	/**
	 * Send an email via SMTP2GO, including attachments if provided.
	 *
	 * This method builds the payload according to the following parameter schema:
	 *
	 * - sender*         : string (email address to send from)
	 * - to*             : array of strings (email addresses to send to)
	 * - cc              : array of strings (email addresses to cc)
	 * - bcc             : array of strings (email addresses to bcc)
	 * - subject*        : string (subject of the email)
	 * - html_body       : string (HTML encoded email body)
	 * - text_body       : string (plain text email body)
	 * - custom_headers  : array of objects (custom header objects)
	 * - attachments     : array of objects (attachment objects)
	 *
	 * @param array $atts           The email attributes (subject, message, html_body, text_body, etc.).
	 * @param int   $log_id         The log ID for the email.
	 * @param array $connection     The connection details.
	 * @param array $processed_data The processed email data (recipients, headers, attachments, etc.).
	 *
	 * @return array The result of the email send operation.
	 * @throws Exception If the email payload cannot be encoded to JSON.
	 */
	public function send( array $atts, $log_id, array $connection, $processed_data ) {
		$result = [
			'success' => false,
			'message' => '',
			'send'    => false,
		];

		$email_payload = [
			'sender'    => $connection['from_name'] . ' <' . $connection['from_email'] . '>',
			'to'        => $this->process_recipients( $processed_data['to'] ),
			'cc'        => $this->process_recipients( $processed_data['headers']['cc'] ?? [] ),
			'bcc'       => $this->process_recipients( $processed_data['headers']['bcc'] ?? [] ),
			'subject'   => sanitize_text_field( $atts['subject'] ?? '' ),
			'text_body' => wp_strip_all_tags( $atts['message'] ?? '' ),
		];

		$content_type = $processed_data['headers']['content_type'];
		if ( ! empty( $content_type ) && 'text/html' === strtolower( $content_type ) ) {
			$email_payload['html_body'] = $atts['message'];
		}

		if ( ! empty( $processed_data['headers']['reply_to'] ) ) {
			$reply_to                        = reset( $processed_data['headers']['reply_to'] );
			$email_payload['custom_headers'] = [
				[
					'header' => 'Reply-To',
					'value'  => $reply_to['email'],
				],
			];
		}

		if ( ! empty( $processed_data['attachments'] ) ) {
			$email_payload['attachments'] = $this->get_attachments( $processed_data['attachments'] );
		}

		$json_payload = wp_json_encode( $email_payload );
		if ( false === $json_payload ) {
			throw new Exception( __( 'Failed to encode email payload to JSON.', 'suremails' ) );
		}

		$request_headers = $this->get_request_headers();
		$params          = [
			'body'        => $json_payload,
			'headers'     => $request_headers,
			'timeout'     => 30,
			'httpversion' => '1.1',
			'blocking'    => true,
		];

		try {
			$response = wp_safe_remote_post( $this->api_url, $params );

			if ( is_wp_error( $response ) ) {
				$result['message']    = __( 'Email sending failed via SMTP2GO. Error: ', 'suremails' ) . $response->get_error_message();
				$result['error_code'] = $response->get_error_code();
				return $result;
			}
				$response_body   = wp_remote_retrieve_body( $response );
				$response_code   = wp_remote_retrieve_response_code( $response );
				$server_response = json_decode( $response_body, true );

			if ( 200 === $response_code ) {
				$result['success']  = true;
				$result['message']  = __( 'Email sent successfully via SMTP2GO.', 'suremails' );
				$result['send']     = true;
				$result['email_id'] = $server_response['email_id'] ?? '';
			} else {
				$error_message     = ! empty( $server_response['error'] ) ? $server_response['error'] : __( 'SMTP2GO Server Error', 'suremails' );
				$result['message'] = __( 'Email sending failed via SMTP2GO. Error: ', 'suremails' ) . $error_message;

				$result['error_code'] = $response_code;
			}
		} catch ( Exception $e ) {
			$result['message']    = __( 'Email sending failed via SMTP2GO. Error: ', 'suremails' ) . $e->getMessage();
			$result['error_code'] = 500;
		}

		return $result;
	}

	/**
	 * Return the option configuration for SMTP2GO.
	 *
	 * @return array
	 */
	public static function get_options() {
		return [
			'title'             => __( 'SMTP2GO Connection', 'suremails' ),
			'description'       => __( 'Enter the details below to connect with your SMTP2GO account.', 'suremails' ),
			'fields'            => self::get_specific_fields(),
			'display_name'      => __( 'SMTP2GO', 'suremails' ),
			'icon'              => 'SMTP2GoIcon',
			'provider_type'     => 'free',
			'field_sequence'    => [ 'connection_title', 'api_key', 'from_email', 'force_from_email', 'from_name', 'force_from_name', 'priority' ],
			'provider_sequence' => 50,
		];
	}

	/**
	 * Get the specific schema fields for SMTP2GO.
	 *
	 * @return array
	 */
	public static function get_specific_fields() {
		return [
			'api_key' => [
				'required'    => true,
				'datatype'    => 'string',
				'help_text'   => sprintf(       // translators: %s: https://app.smtp2go.com/login/ URL.
					__( 'Click on this link to generate an API Key from SMTP2GO - %1$sCreate API Key%2$s', 'suremails' ),
					'<a href="' . esc_url( 'https://app.smtp2go.com/sending/apikeys/' ) . '" target="_blank">',
					'</a>'
				),
				'label'       => __( 'API Key', 'suremails' ),
				'input_type'  => 'password',
				'placeholder' => __( 'Enter your SMTP2GO API Key', 'suremails' ),
				'encrypt'     => true,
			],
		];
	}

	/**
	 * Reusable function to process recipient arrays.
	 *
	 * This function handles recipients provided as either an array (with keys 'email' or 'from_email' and optional 'name')
	 * or as a plain email string. If a name is provided, it formats the recipient as "Name <email>".
	 *
	 * @param array $recipients Array of recipients.
	 * @return array Processed array of email strings.
	 */
	private static function process_recipients( $recipients ) {
		$result = [];
		if ( ! empty( $recipients ) && is_array( $recipients ) ) {
			foreach ( $recipients as $recipient ) {
				if ( is_array( $recipient ) ) {
					$email = isset( $recipient['email'] ) ? sanitize_email( $recipient['email'] ) : ( isset( $recipient['from_email'] ) ? sanitize_email( $recipient['from_email'] ) : '' );
					$name  = isset( $recipient['name'] ) ? sanitize_text_field( $recipient['name'] ) : '';
					if ( ! empty( $email ) ) {
						$result[] = ! empty( $name ) ? $name . ' <' . $email . '>' : $email;
					}
				} elseif ( is_string( $recipient ) ) {
					$result[] = sanitize_email( $recipient );
				}
			}
		}
		return $result;
	}

	/**
	 * Process attachments by reading the file, encoding its contents in base64 and preparing the attachment array.
	 *
	 * @param array $attachments Array of attachment file paths.
	 * @return array
	 */
	private function get_attachments( $attachments ) {
		$result = [];
		foreach ( $attachments as $attachment ) {
			$attachment_values = ProviderHelper::get_attachment( $attachment );

			if ( ! $attachment_values ) {
				continue;
			}

			$result[] = [
				'filename' => $attachment_values['name'] ?? '',
				'fileblob' => $attachment_values['blob'] ?? '',
				'mimetype' => $attachment_values['type'] ?? '',
			];
		}
		return $result;
	}

	/**
	 * Build the request headers for the API request.
	 *
	 * @return array
	 */
	private function get_request_headers() {
		return [
			'Content-Type'      => 'application/json',
			'X-Smtp2go-Api-Key' => sanitize_text_field( $this->connection_data['api_key'] ),
			'timeout'           => 30,
			'httpversion'       => '1.1',
			'blocking'          => true,
		];
	}
}
