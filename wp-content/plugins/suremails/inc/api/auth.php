<?php
/**
 * Auth class
 *
 * Handles authentication API requests for the SureMails plugin.
 *
 * @package SureMails\Inc\API
 */

namespace SureMails\Inc\API;

use SureMails\Inc\Traits\Instance;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Auth
 *
 * @since 0.0.1
 */
class Auth extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/get-auth-url';

	/**
	 * Register API routes.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function register_routes() {
		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			$this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::CREATABLE, // POST method.
					'callback'            => [ $this, 'get_auth_url' ],
					'permission_callback' => [ $this, 'validate_permission' ],
				],
			]
		);
	}

	/**
	 * Retrieves the auth URL based on the provider.
	 *
	 * @param \WP_REST_Request<array<string, mixed>> $request The REST request instance.
	 * @return WP_REST_Response Returns the auth URL or an error.
	 */
	public function get_auth_url( $request ) {
		$params = $request->get_json_params();

		$provider = isset( $params['provider'] ) ? sanitize_text_field( $params['provider'] ) : '';

		if ( strtolower( $provider ) === 'gmail' ) {
			$reponse = $this->get_gmail_auth_url( $params );
			return new WP_REST_Response( $reponse, 200 );
		}

		return new WP_REST_Response( [ 'error' => 'Unsupported provider.' ], 400 );
	}

	/**
	 * Generates the Gmail authorization URL.
	 *
	 * Validates the provided client credentials and returns the Gmail auth URL.
	 *
	 * @param array $params The parameters passed in the API request.
	 * @return WP_REST_Response|array Returns the Gmail auth URL or an error response.
	 */
	private function get_gmail_auth_url( $params ) {

		$client_id     = isset( $params['client_id'] ) ? sanitize_text_field( $params['client_id'] ) : '';
		$client_secret = isset( $params['client_secret'] ) ? sanitize_text_field( $params['client_secret'] ) : '';

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new WP_REST_Response( [ 'error' => 'Client ID and Client Secret are required.' ], 400 );
		}

		$redirect_uri = admin_url( 'options-general.php?page=suremail' );
		// Construct the Gmail authorization URL.
		$auth_url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query(
			[
				'client_id'              => $client_id,
				'redirect_uri'           => $redirect_uri,
				'response_type'          => 'code',
				'scope'                  => 'https://mail.google.com/',
				'state'                  => 'gmail',
				'access_type'            => 'offline',
				'approval_prompt'        => 'force',
				'include_granted_scopes' => 'true',
			]
		);

		return [
			'auth_url' => $auth_url,
		];
	}
}

// Instantiate the Auth class to register the routes.
Auth::instance();
