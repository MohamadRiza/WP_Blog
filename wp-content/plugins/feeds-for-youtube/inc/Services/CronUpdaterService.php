<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Pro\SBY_Cron_Updater_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_Settings_Pro;
use SmashBalloon\YouTubeFeed\SBY_Settings;
use SmashBalloon\YouTubeFeed\SBY_Cron_Updater;

class CronUpdaterService extends ServiceProvider {

	public function register() {
		add_action( 'init', [ $this, 'maybe_run_cron' ] );
		add_action( 'sby_feed_update', [ $this, 'sby_cron_updater' ] );
	}

	public function maybe_run_cron() {
		$database_settings = sby_get_database_settings();
		$settings = ( new SBY_Settings( [], $database_settings ) )->get_settings();
		$next_run = wp_next_scheduled('sby_feed_update');

		if ( $next_run === false || time() > $next_run ) {
			SBY_Cron_Updater::start_cron_job( $settings['cache_cron_interval'], $settings['cache_cron_time'],
				$settings['cache_cron_am_pm'] );
		}
	}

	/**
	 * Triggered by a cron event to update feeds
	 */
	public function sby_cron_updater() {
		if ( \sby_is_pro() ) {
			SBY_Cron_Updater_Pro::do_feed_updates();
			SBY_Cron_Updater_Pro::do_comment_delete();
			
		} else {
			SBY_Cron_Updater::do_feed_updates();
		}
	}
}