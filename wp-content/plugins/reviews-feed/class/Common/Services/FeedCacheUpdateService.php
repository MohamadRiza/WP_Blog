<?php

namespace SmashBalloon\Reviews\Common\Services;

use SmashBalloon\Reviews\Common\AuthorizationStatusCheck;
use SmashBalloon\Reviews\Common\FeedCacheUpdater;
use Smashballoon\Stubs\Services\ServiceProvider;


class FeedCacheUpdateService extends ServiceProvider {
	public const CACHES_TABLE_NAME = 'sbr_feed_caches';

	public const CRON_JOB_ADDITIONAL_BATCH = 'sbr_cron_additional_batch';

	public const CRON_JOB_NAME = 'sbr_feed_update';

	const RESULTS_PER_PAGE = 20;

	const RESULTS_PER_CRON_UPDATE = 10;

	/**
	 * @var AuthorizationStatusCheck
	 */
	private $auth_check;

	public function register() {
		add_shortcode('reviews-feed-cron-simulator', array( $this, 'init' ) );
		add_action( self::CRON_JOB_NAME, array( $this, 'init' ) );
		add_action( self::CRON_JOB_ADDITIONAL_BATCH, array( $this, 'init_additional_batch' ) );

		add_action( 'sbr_before_shortcode_render', array( $this, 'maybe_check_cron_schedule' ) );
	}

	public function init() {
		$this->auth_check = new AuthorizationStatusCheck();
		if ( $this->should_do_updates() ) {
			$this->auth_check->update_status(
                [ 'last_cron_update' => time() ]
            );
			$this->do_updates();
		}
	}

	public function init_additional_batch() {
		$this->do_updates();
	}

	public function should_do_updates() {
		$statuses = $this->auth_check->get_statuses();
		$time_with_minute_buffer = time() + 60;
		$statuses['last_cron_update'] = 0;
		if ( $statuses['last_cron_update'] <  $time_with_minute_buffer - $statuses['update_frequency'] ) {
			return true;
		}

		return false;
	}

	public function do_updates() {
		$caches = $this->get_caches();

		$updater = new FeedCacheUpdater( $caches );
		$updater->do_updates();

		$num = count( $caches );
		if ( $num === self::RESULTS_PER_CRON_UPDATE ) {
			// Schedule another batch in 5 minutes if needed
			wp_schedule_single_event( time() + 300, self::CRON_JOB_ADDITIONAL_BATCH  );
		}
	}

	public function get_caches() {
		return $this->feed_caches_query( array( 'cron_update' => true ) );
	}

	public function feed_caches_query( $args ) {
		global $wpdb;
		$feed_cache_table_name = $wpdb->prefix . self::CACHES_TABLE_NAME;

		if ( ! isset( $args['cron_update'] ) ) {
			$sql = "
			SELECT * FROM $feed_cache_table_name;";
		} else {
			$sql = $wpdb->prepare(
				"
				SELECT * FROM $feed_cache_table_name
				WHERE cron_update = 'yes'
				AND last_updated < %s
				ORDER BY last_updated ASC
				LIMIT %d;",
				gmdate( 'Y-m-d H:i:s', time() - 12 * HOUR_IN_SECONDS ),
				self::RESULTS_PER_CRON_UPDATE
			);
		}
		return $wpdb->get_results( $sql, ARRAY_A );
	}

	public function maybe_check_cron_schedule() {
		if ( ! sbr_current_user_can( 'manage_reviews_feed_options' ) ) {
			return;
		}

		self::schedule_cron_job();
	}

	public static function schedule_cron_job() {
		if ( ! wp_next_scheduled( self::CRON_JOB_NAME ) ) {
			wp_schedule_event( time(), 'hourly', self::CRON_JOB_NAME );
		}
	}

}
