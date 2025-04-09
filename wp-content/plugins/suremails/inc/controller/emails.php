<?php
/**
 * Emails class
 *
 * Handles the deletion of old email logs.
 *
 * @package SureMails\Inc\Controller
 */

namespace SureMails\Inc\Controller;

use SureMails\Inc\ConnectionManager;
use SureMails\Inc\DB\EmailLog;
use SureMails\Inc\Settings;
use SureMails\Inc\Traits\Instance;
use SureMails\Inc\Utils\LogError;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Emails
 *
 * Handles the deletion of old email logs.
 */
class Emails {

	use Instance;

	/**
	 * Private constructor to enforce Singleton pattern.
	 */
	private function __construct() {
		// Hook into the 'suremails_retry_failed_email' action for retrying failed emails.
		add_action( 'suremails_retry_failed_email', [ $this, 'retry_failed_email' ], 10, 1 );
	}

	/**
	 * Retries sending a failed email.
	 *
	 * @param int|null $log_id   The log ID if available.
	 * @return void
	 */
	public static function retry_failed_email( $log_id = null ) {

		// Ensure log_id is provided.
		if ( $log_id === null ) {
			return;
		}
		$logger = Logger::instance();
		// Retrieve the log entry from the database.
		$log_entry = $logger->get_log( $log_id );

		if ( ! $log_entry ) {
			return;
		}

		if ( is_wp_error( $log_entry ) ) {
			return;
		}

		$meta = $log_entry['meta'];

		// Check if the email has already been sent successfully.
		if ( $log_entry['status'] === Logger::STATUS_SENT ) {
			return;
		}

		// Check if the maximum number of retries has been reached.

		if ( $meta['retry'] >= 1 ) {
			return;
		}

		ConnectionManager::instance()->set_is_retry( true );

		// Set the current log ID using the setter.
		$logger->set_id( $log_id );
		$to         = $log_entry['email_to'];
		$subject    = $log_entry['subject'];
		$message    = $log_entry['body'];
		$headers    = $log_entry['headers'];
		$attachment = $log_entry['attachments'];
		wp_mail( $to, $subject, $message, $headers, $attachment );
	}

	/**
	 * Deletes old email logs based on the configured retention period.
	 *
	 * @return void
	 */
	public function delete_old_email_logs() {

		$options          = Settings::instance()->get_settings();
		$retention_period = $options['delete_email_logs_after'] ?? 'none';

		$date_threshold = null;

		$retention_map = [
			'1_day'    => '-1 day',
			'7_days'   => '-7 days',
			'30_days'  => '-30 days',
			'365_days' => '-1 year',
			'730_days' => '-2 years',
		];

		if ( array_key_exists( $retention_period, $retention_map ) ) {
			$date_threshold = gmdate( 'Y-m-d H:i:s', strtotime( $retention_map[ $retention_period ] ) );
		} elseif ( $retention_period === 'none' ) {
			$date_threshold = null;
		} else {
			$date_threshold = gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
		}

		// Delete logs older than the date threshold.
		if ( $date_threshold ) {
			// Check if EmailLog class exists.
			if ( class_exists( 'SureMails\Inc\DB\EmailLog' ) ) {
				$email_log = EmailLog::instance();

				// Attempt to delete old logs using 'where' condition.
				try {
					$email_log->delete(
						[
							'where' => [
								'created_at <' => $date_threshold,
							],
						]
					);
				} catch ( \Exception $e ) {
					LogError::instance()->log_error( $e->getMessage() );
				}
			}
		}
	}

}
