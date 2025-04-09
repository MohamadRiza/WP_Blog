<?php
/**
 * GmailHandler.php
 *
 * Handles sending emails using Gmail with the Google API PHP Client.
 *
 * @package SureMails\Inc\Emails\Providers\Gmail
 */

namespace SureMails\Inc\Emails\Providers\GMAIL;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use SureMails\Inc\ConnectionManager;
use SureMails\Inc\Emails\Handler\ConnectionHandler;
use SureMails\Inc\Settings;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GmailHandler
 *
 * Implements the ConnectionHandler to handle Gmail email sending and authentication.
 */
class GmailHandler implements ConnectionHandler {

	/**
	 * Gmail connection data.
	 *
	 * @var array
	 */
	private $connection_data;

	/**
	 * Gmail service.
	 *
	 * @var Gmail
	 */
	private $gmail_service;

	/**
	 * Gmail message.
	 *
	 * @var Message
	 */
	private $gmail_message;

	/**
	 * Google API Client.
	 *
	 * @var Client
	 */
	private $client;
	/**
	 * Constructor.
	 *
	 * Initializes connection data.
	 *
	 * @param array $connection_data The connection details.
	 */
	public function __construct( array $connection_data ) {
		$this->connection_data = $connection_data;
		$this->client          = new Client();
		$this->client->setClientId( $this->connection_data['client_id'] );
		$this->client->setClientSecret( $this->connection_data['client_secret'] );
	}

	/**
	 * Authenticate the Gmail connection.
	 *
	 * This method handles the entire OAuth flow.
	 *
	 * @return void|array
	 */
	public function authenticate() {

		$result = [
			'success' => false,
			'message' => __( 'Failed to authenticate with Gmail.', 'suremails' ),

		];
		$tokens    = [];
		$auth_code = $this->connection_data['auth_code'] ?? '';
		if ( ! empty( $auth_code ) ) {
			$body   = [
				'code'          => $auth_code,
				'grant_type'    => 'authorization_code',
				'redirect_uri'  => admin_url( 'options-general.php?page=suremail' ),
				'client_id'     => $this->connection_data['client_id'],
				'client_secret' => $this->connection_data['client_secret'],
			];
			$tokens = $this->api_call( 'https://accounts.google.com/o/oauth2/token', $body, 'POST' );

			if ( is_wp_error( $tokens ) ) {
				$result['message'] = $tokens->get_error_message();
				return $result;
			}
		} elseif ( ! empty( $this->connection_data['refresh_token'] ) ) {
			$new_tokens = $this->get_new_token();
			if ( isset( $new_tokens['success'] ) && $new_tokens['success'] === false ) {
				return $result;
			}
			$tokens = $new_tokens;
		} else {
			$result['message'] = __( 'No authorization code or refresh token provided. Please authenticate first.', 'suremails' );
			return $result;
		}
		if ( ! is_array( $tokens ) || ! isset( $tokens['expires_in'], $tokens['access_token'], $tokens['refresh_token'] ) ) {
			$result['message'] = __( 'Failed to retrieve authentication tokens. Please try to re-authenticate', 'suremails' );
			return $result;
		}
		$result                 = array_merge( $result, $tokens );
		$result['expire_stamp'] = time() + $tokens['expires_in'];
		$result['success']      = true;
		$result['message']      = __( 'Successfully authenticated with Gmail.', 'suremails' );
		return $result;
	}

	/**
	 * Send email using Gmail via the Google API Client.
	 *
	 * @param array $atts           Email attributes.
	 * @param int   $log_id         Log ID.
	 * @param array $connection_data     Connection data.
	 * @param array $processed_data Processed email data.
	 *
	 * @return array The result of the sending attempt.
	 */
	public function send( array $atts, $log_id, array $connection_data, $processed_data ) {

		$result   = [
			'success' => false,
			'message' => __( 'Failed to send email via Gmail.', 'suremails' ),
		];
		$response = $this->check_tokens();
		if ( isset( $response['success'] ) && $response['success'] === false ) {
			return $response;
		}

		$phpmailer = ConnectionManager::instance()->get_phpmailer();
		$phpmailer->setFrom( $connection_data['from_email'], $connection_data['from_name'] );
		if ( ! empty( $this->connection_data['return_path'] ) && $this->connection_data['return_path'] === true ) {
			$phpmailer->Sender = $connection_data['from_email']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		$phpmailer->preSend();
		$raw_message         = $phpmailer->getSentMIMEMessage();
		$encoded_raw_message = base64_encode( $raw_message );
		// Convert to URL-safe Base64 encoding.
		$encoded_raw_message = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], $encoded_raw_message );

		$token = [
			'access_token'  => $this->connection_data['access_token'],
			'expires_in'    => $this->connection_data['expire_stamp'] - time(),
			'refresh_token' => $this->connection_data['refresh_token'],
		];
		$this->client->setAccessToken( $token );
		$this->gmail_service = new Gmail( $this->client );
		$this->gmail_message = new Message();
		$this->gmail_message->setRaw( $encoded_raw_message );

		try {
			$result = $this->gmail_service->users_messages->send( 'me', $this->gmail_message );
			if ( isset( $result->id ) ) {
				return [
					'success'  => true,
					'message'  => __( 'Email sent successfully via Gmail.', 'suremails' ),
					'email_id' => $result->id,
				];
			}
				return [
					'success' => false,
					'message' => __( 'Failed to send email via Gmail.', 'suremails' ),
				];

		} catch ( \Exception $e ) {
			return [
				'success' => false,
				'message' => __( 'Error sending email via Gmail: ', 'suremails' ) . $e->getMessage(),
			];
		}
	}

	/**
	 * Get the Gmail connection options.
	 *
	 * @return array The Gmail connection options.
	 */
	public static function get_options() {
		return [
			'title'             => __( 'Gmail Connection', 'suremails' ),
			'description'       => __( 'Enter the details below to connect with your Gmail account.', 'suremails' ),
			'fields'            => self::get_specific_fields(),
			'icon'              => 'GmailIcon',
			'display_name'      => __( 'Google Workspace / Gmail', 'suremails' ),
			'provider_type'     => PHP_VERSION_ID >= 80100 ? 'free' : 'not_compatible',
			'field_sequence'    => [
				'connection_title',
				'client_id',
				'client_secret',
				'redirect_url',
				'auth_button',
				'from_email',
				'force_from_email',
				'return_path',
				'from_name',
				'force_from_name',
				'priority',
				'auth_code',
			],
			'provider_sequence' => 27,
			'prerequisite'      => __( 'This provider does not work with your version of PHP. Please upgrade to PHP 8.1 or higher to use this provider.', 'suremails' ),
		];
	}

	/**
	 * Get the Gmail connection specific fields.
	 *
	 * @return array The Gmail specific fields.
	 */
	public static function get_specific_fields() {
		$redirect_uri = admin_url( 'options-general.php?page=suremail' );

		return [
			'client_id'     => [
				'required'    => true,
				'datatype'    => 'string',
				'label'       => __( 'Client ID', 'suremails' ),
				'input_type'  => 'text',
				'placeholder' => __( 'Enter your Gmail Client ID', 'suremails' ),
				'help_text'   => sprintf(       // translators: %s:  URL.
					__( 'Get Client ID and Secret ID from Google Cloud Platform. Follow the Gmail %s', 'suremails' ),
					'<a href="' . esc_url( 'https://suremails.com/docs/gmail?utm_campaign=suremails&utm_medium=suremails-dashboard' ) . '" target="_blank">' . __( 'documentation.', 'suremails' ) . '</a>'
				),
			],
			'client_secret' => [
				'required'    => true,
				'datatype'    => 'string',
				'label'       => __( 'Client Secret', 'suremails' ),
				'input_type'  => 'password',
				'placeholder' => __( 'Enter your Gmail Client Secret', 'suremails' ),
				'encrypt'     => true,

			],
			'auth_code'     => [
				'required'    => false,
				'datatype'    => 'string',
				'input_type'  => 'password',
				'placeholder' => __( 'Paste the authorization code or refresh token here.', 'suremails' ),
				'encrypt'     => true,
				'class_name'  => 'hidden',
			],
			'redirect_url'  => [
				'required'    => false,
				'datatype'    => 'string',
				'label'       => __( 'Redirect URI', 'suremails' ),
				'input_type'  => 'text',
				'read_only'   => true,
				'default'     => $redirect_uri,
				'help_text'   => __( 'Copy the above URL and add it to the "Authorized Redirect URIs" section in your Google Cloud Project. Ensure the URL matches exactly.', 'suremails' ),
				'copy_button' => true,
			],
			'auth_button'   => [
				'required'        => false,
				'datatype'        => 'string',
				'input_type'      => 'button',
				'button_text'     => __( 'Authenticate with Google', 'suremails' ),
				'alt_button_text' => __( 'Click here to re-authenticate', 'suremails' ),
				'on_click'        => [
					'params' => [
						'provider' => 'gmail',
						'client_id',
						'client_secret',
					],
				],
				'size'            => 'sm',
			],
			'return_path'   => [
				'default'     => true,
				'required'    => false,
				'datatype'    => 'boolean',
				'help_text'   => __( 'The Return Path is where bounce messages (failed delivery notices) are sent. Enable this to receive bounce notifications at the "From Email" address if delivery fails.', 'suremails' ),
				'label'       => __( 'Return Path', 'suremails' ),
				'input_type'  => 'checkbox',
				'placeholder' => __( 'Enter Return Path', 'suremails' ),
				'depends_on'  => [ 'from_email' ],
			],
			'refresh_token' => [
				'datatype'   => 'string',
				'input_type' => 'password',
				'encrypt'    => true,
			],
			'access_token'  => [
				'datatype' => 'string',
				'encrypt'  => true,
			],
		];
	}

	/**
	 * Get the Gmail connection data.
	 *
	 * @param string $url   The URL to make the API call to.
	 * @param array  $body The body arguments to send with the API call.
	 * @param string $type The type of request to make.
	 * @since 1.4.0
	 *
	 * @return array|WP_Error The Gmail connection data.
	 */
	private function api_call( $url, $body, $type = 'GET' ) {
			$headers = [
				'Content-Type'              => 'application/http',
				'Content-Transfer-Encoding' => 'binary',
				'MIME-Version'              => '1.0',
				'timeout'                   => 10,
			];

			$args = [
				'headers' => $headers,
			];
			if ( $body && is_array( $body ) ) {
				$body = wp_json_encode( $body );
				if ( $body === false ) {
					return new WP_Error( 422, __( 'Failed to encode body to JSON.', 'suremails' ) );
				}
				$args['body'] = $body;
			}

			$args['method'] = $type;

			$request = null;
			if ( is_array( $args ) ) {
				$request = wp_safe_remote_request( $url, $args );
			}

			if ( is_wp_error( $request ) ) {
				$message = $request->get_error_message();
				return new WP_Error( 422, $message );
			}

			$body = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! empty( $body['error'] ) ) {
				$error = __( 'Unknow error from Gmail API.', 'suremails' );
				if ( isset( $body['error_description'] ) ) {
					$error = $body['error_description'];
				} elseif ( ! empty( $body['error']['message'] ) ) {
					$error = $body['error']['message'];
				}
				return new WP_Error( 422, $error );
			}

			return $body;
	}

	/**
	 * Check the tokens and refresh if necessary.
	 *
	 * @since 1.4.0
	 *
	 * @return array The result of the token check.
	 */
	private function check_tokens() {
		$result = [
			'success' => false,
			'message' => __( 'Failed to get new token from Gmail API', 'suremails' ),
		];
		if ( empty( $this->connection_data['refresh_token'] ) || empty( $this->connection_data['access_token'] ) || empty( $this->connection_data['expire_stamp'] ) ) {
			return $result;
		}
		$expiring = $this->connection_data['expire_stamp'] - 500;
		if ( $expiring < time() ) {
			$new_tokens = $this->client->refreshToken( $this->connection_data['refresh_token'] );
			if ( ! empty( $new_tokens['error'] ) ) {
				$error_description = $new_tokens['error_description'] ?? __( 'Failed to authenticate', 'suremails' );
				// translators: %s: Error description.
				$result['message'] = sprintf( __( 'Failed to get new token from Gmail API: %s', 'suremails' ), $error_description );
				return $result;
			}
			$token = [];
			if ( ! empty( $new_tokens['access_token'] ) ) {
				$token['access_token'] = $new_tokens['access_token'];
			}
			if ( ! empty( $new_tokens['expires_in'] ) ) {
				$token['expires_in'] = $new_tokens['expires_in'];
			}
			if ( ! empty( $new_tokens['refresh_token'] ) ) {
				$token['refresh_token'] = $new_tokens['refresh_token'];
			}
			$this->client->setAccessToken( $token );
			$this->connection_data['access_token']  = $new_tokens['access_token'];
			$this->connection_data['expire_stamp']  = time() + $new_tokens['expires_in'];
			$this->connection_data['expires_in']    = $new_tokens['expires_in'];
			$this->connection_data['refresh_token'] = $new_tokens['refresh_token'];
			Settings::instance()->update_connection( $this->connection_data );

		}

		$result['success'] = true;
		$result['message'] = __( 'Successfully added new tokens.', 'suremails' );

		return $result;
	}

	/**
	 * Get the Gmail connection data.
	 *
	 * @since 1.4.0
	 * @return array The Gmail connection data.
	 */
	private function get_new_token() {
		$result = [
			'success' => false,
			'message' => __( 'Failed to get new token from Gmail API.', 'suremails' ),
		];
		$this->client->refreshToken( $this->connection_data['refresh_token'] );
		$new_tokens = $this->client->getAccessToken();
		if ( ! empty( $new_tokens['error'] ) ) {
			$error_description = $new_tokens['error_description'] ?? __( 'Failed to get new token from Gmail API.', 'suremails' );
			// translators: %s: Error description.
			$result['message'] = sprintf( __( 'Failed to get new token from Gmail API: %s', 'suremails' ), $error_description );
			return $result;
		}
		$result['success'] = true;
		return $new_tokens;
	}
}
