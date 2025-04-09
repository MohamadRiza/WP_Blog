<?php
/**
 * DeleteLogs class
 *
 * Handles the REST API endpoint to delete logs.
 *
 * @package SureMails\Inc\API
 */

namespace SureMails\Inc\API;

use SureMails\Inc\DB\EmailLog;
use SureMails\Inc\Traits\Instance;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class DeleteLogs
 *
 * Handles the `/delete-logs` REST API endpoint.
 */
class DeleteLogs extends Api_Base {
	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/delete-logs';

	/**
	 * Register API routes.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->get_api_namespace(),
			$this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'delete_email_log' ],
					'permission_callback' => [ $this, 'validate_permission' ],
					'args'                => [
						'log_ids' => [
							'required'          => true,
							'type'              => 'array',
							'sanitize_callback' => static function ( $param ) {
								// Sanitize each value in the array.
								return array_map( 'absint', $param );
							},
						],
					],
				],
			]
		);
	}

	/**
	 * Delete email logs based on provided log IDs.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request The REST request object.
	 * @return WP_REST_Response The REST API response.
	 */
	public function delete_email_log( $request ) {
		// Retrieve and validate log IDs from the request.
		$log_ids = $request->get_param( 'log_ids' );

		// Attempt to delete multiple logs using the updated 'delete' method.
		$deleted_count = EmailLog::instance()->delete(
			[
				'ids' => $log_ids,
			]
		);

		if ( $deleted_count === false ) {
			return new WP_REST_Response(
				[
					'success' => false,
					'message' => 'Failed to delete the provided log IDs.',
				],
				500
			);
		}

		if ( count( $log_ids ) === $deleted_count ) {
			$message = "{$deleted_count} log(s) deleted successfully.";
		} else {
			$remaining = count( $log_ids ) - $deleted_count;
			$message   = "{$deleted_count} log(s) deleted successfully. {$remaining} log(s) could not be deleted.";
		}

		return new WP_REST_Response(
			[
				'success' => true,
				'message' => $message,
			],
			200
		);
	}
}

// Initialize the DeleteLogs singleton.
DeleteLogs::instance();
