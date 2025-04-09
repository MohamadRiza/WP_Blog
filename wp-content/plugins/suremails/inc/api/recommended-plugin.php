<?php
/**
 * RecommendedPlugin Class
 *
 * This file contains the logic for handling recommended plugin installations.
 *
 * @package SureMails\Admin
 */

namespace SureMails\Inc\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use SureMails\Inc\Traits\Instance;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class RecommendedPlugin
 *
 * Main class for handling recommended plugin installations.
 */
class RecommendedPlugin extends Api_Base {
	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/dashboard-data';

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// Route for fetching installed and active plugins.
		register_rest_route(
			$this->get_api_namespace(),
			'/installed-plugins',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_installed_plugins' ],
				'permission_callback' => [ $this, 'check_install_plugin_permissions' ],
			]
		);
	}

	/**
	 * Check permissions for the REST API endpoints.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request The REST request object.
	 * @return bool|WP_Error True if the user has permission, otherwise WP_Error.
	 */
	public function check_install_plugin_permissions( $request ) {
		// Check if user has permission to install or activate plugins.
		if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'activate_plugins' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permissions to perform this action.', 'suremails' ),
				[ 'status' => 403 ]
			);
		}

		// Retrieve the nonce from the header, defaulting to an empty string if not set.
		$nonce = $request->get_header( 'X-WP-Nonce' ) ?? '';

		// Verify nonce.
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Invalid nonce.', 'suremails' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Get the list of installed and active plugins.
	 *
	 * @return WP_REST_Response The REST API response.
	 */
	public function get_installed_plugins() {
		// Include necessary WordPress files for plugin functions.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get all installed plugins.
		$all_plugins = get_plugins();
		$installed   = [];
		$active      = [];

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$slug        = dirname( $plugin_file );
			$installed[] = $slug;

			if ( is_plugin_active( $plugin_file ) ) {
				$active[] = $slug;
			}
		}

		return new WP_REST_Response(
			[
				'success' => true,
				'plugins' => [
					'installed' => $installed,
					'active'    => $active,
				],
			],
			200
		);
	}
}

// Instantiate the singleton instance of RecommendedPlugin.
RecommendedPlugin::instance();
