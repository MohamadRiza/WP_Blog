<?php
/**
 * Class SBY_Cron_Updater
 *
 * Finds all regular feed transients saved in the database and updates
 * each cached feed in the background using WP Cron. This is set up with the
 * "sby_cron_updater" function in the if-functions.php file. The "display_instagram"
 * function will trigger a single feed update if no transient is found
 * for the feed
 *
 * @since 1.0/1.0
 */

namespace SmashBalloon\YouTubeFeed;
use SmashBalloon\YouTubeFeed\Services\AdminAjaxService;

class SBY_Cron_Updater
{
	/**
	 * Find and loop through all feed cache transients and update the post and
	 * header caches
	 *
	 * Overwritten in the Pro version
	 *
	 * @since 1.0
	 */
	public static function do_feed_updates() {
		$feed_caches = SBY_Cron_Updater::get_feed_cache_option_names();
		shuffle(  $feed_caches );
		$database_settings = sby_get_database_settings();

		// this is outputted in system info
		$report = array(
			'notes' => array(
				'time_ran' => date( 'Y-m-d H:i:s' ),
				'num_found_transients' => count( $feed_caches )
			)
		);

		foreach ( $feed_caches as $feed_cache ) {

			$feed_id  = str_replace( '_transient_', '', $feed_cache['option_name'] );
			$report[ $feed_id ] = array();

			$transient = get_transient( $feed_id );

			if ( $transient ) {
				$feed_data                  = json_decode( $transient, true );

				// shortcode attributes are saved in order to recreate the feed is needed
				$atts = isset( $feed_data['atts'] ) ? $feed_data['atts'] : array();
				$last_retrieve = isset( $feed_data['last_retrieve'] ) ? (int)$feed_data['last_retrieve'] : 0;
				// the last approximate time the feed was requested to be displayed on a page is recorded
				// in order to stop updating feeds not in use.
				$last_requested = isset( $feed_data['last_requested'] ) ? (int)$feed_data['last_requested'] : false;
				$report[ $feed_id ]['last_retrieve'] = date( 'Y-m-d H:i:s', $last_retrieve );
				if ( $atts !== false ) {

					if ( ! $last_requested || $last_requested > (time() - 60*60*24*30) ) {
						$sby_settings_obj = new SBY_Settings( $atts, $database_settings );

						if ( empty( $database_settings['connected_accounts'] ) && empty( $database_settings['api_key'] ) ) {
							$report[ $feed_id ]['did_update'] = 'no - no connected account';
						} else {
							SBY_Cron_Updater::do_single_feed_cron_update( $sby_settings_obj, $feed_data, $atts );

							$report[ $feed_id ]['did_update'] = 'yes';
						}
					} else {
						$report[ $feed_id ]['did_update'] = 'no - not recently requested';
					}


				} else {
					$report[ $feed_id ]['did_update'] = 'no - missing atts';
				}

			} else {
				$report[ $feed_id ]['did_update'] = 'no - no transient found';
			}

		}

		update_option( 'sby_cron_report', $report, false );
	}

	/**
	 * Update a single feed cache based on settings. Local image storing and
	 * resizing is done in the background here as well unless this is the initial
	 * time the feed is created and no cached data exists yet.
	 *
	 * Overwritten in the Pro version
	 *
	 * @param object $sby_settings_obj object created by the sby_settings class
	 * @param array $feed_data post, header, shortcode settings, and other info
	 *  associated with the feed that is saved in the cache
	 * @param array $atts shortcode settings
	 * @param bool $include_resize whether or not to resize images during the update since
	 *  images can also be resized with an ajax call when the feed is viewed on the frontend
	 *
	 * @since 1.0
	 */
	public static function do_single_feed_cron_update( $sby_settings_obj, $feed_data, $atts, $include_resize = true ) {
		$sby_settings_obj->set_feed_type_and_terms();
		$sby_settings_obj->set_transient_name();
		$transient_name = $sby_settings_obj->get_transient_name();
		$settings = $sby_settings_obj->get_settings();
		$feed_type_and_terms = $sby_settings_obj->get_feed_type_and_terms();

		$youtube_feed = new SBY_Feed( $transient_name );

		while ( $youtube_feed->need_posts( $settings['num'] ) && $youtube_feed->can_get_more_posts() ) {
			$youtube_feed->add_remote_posts( $settings, $feed_type_and_terms, $sby_settings_obj->get_connected_accounts_in_feed() );
		}

		$to_cache = array(
			'atts' => $atts,
			'last_requested' => isset($feed_data['last_requested']) ? $feed_data['last_requested'] : '',
			'last_retrieve' => time()
		);

		$youtube_feed->set_cron_cache( $to_cache, $sby_settings_obj->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );

		if ( $youtube_feed->need_header( $settings, $feed_type_and_terms ) ) {
			$youtube_feed->set_remote_header_data( $settings, $feed_type_and_terms, $sby_settings_obj->get_connected_accounts_in_feed() );

			$youtube_feed->cache_header_data( $sby_settings_obj->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}

		$post_data = $youtube_feed->get_post_data();
		$post_data = array_slice( $post_data, 0, $settings['num'] );

		AdminAjaxService::sby_process_post_set_caching( $post_data, $transient_name );

	}

	/**
	 * Retrieve option name column values for all feed cache transients
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public static function get_feed_cache_option_names() {
		global $wpdb;
		$feed_caches = array();

		$results = $wpdb->get_results( "
		SELECT option_name
        FROM $wpdb->options
        WHERE `option_name` LIKE ('%\_transient\_sby\_%')
        AND `option_name` NOT LIKE ('%\_transient\_sby\_header%');", ARRAY_A );

		if ( isset( $results[0] ) ) {
			$feed_caches = $results;
		}

		return $feed_caches;
	}

	/**
	 * Start cron jobs based on user's settings for cron cache update frequency.
	 * This is triggered when settings are saved on the "Configure" tab.
	 *
	 * @param string $sby_cache_cron_interval arbitrary name from one of the
	 *  settings on the "Configure" tab
	 * @param string $sby_cache_cron_time hour of the day (1 = 1:00)
	 * @param string $sby_cache_cron_am_pm am or pm (time of day)
	 *
	 * @since 1.0
	 */
	public static function start_cron_job( $sby_cache_cron_interval, $sby_cache_cron_time, $sby_cache_cron_am_pm ) {
		wp_clear_scheduled_hook( 'sby_feed_update' );

		if ( $sby_cache_cron_interval === '12hours' || $sby_cache_cron_interval === '24hours' ) {
			$relative_time_now = time() + sby_get_utc_offset();
			$base_day = strtotime( date( 'Y-m-d', $relative_time_now ) );
			$add_time = $sby_cache_cron_am_pm === 'pm' ? (int)$sby_cache_cron_time + 12 : (int)$sby_cache_cron_time;
			$utc_start_time = $base_day + (($add_time * 60 * 60) - sby_get_utc_offset());

			if ( $utc_start_time < time() ) {
				if ( $sby_cache_cron_interval === '12hours' ) {
					$utc_start_time += 60*60*12;
				} else {
					$utc_start_time += 60*60*24;
				}
			}

			if ( $sby_cache_cron_interval === '12hours' ) {
				wp_schedule_event( $utc_start_time, 'twicedaily', 'sby_feed_update' );
			} else {
				wp_schedule_event( $utc_start_time, 'daily', 'sby_feed_update' );
			}

		} else {
			if ( $sby_cache_cron_interval === '30mins' ) {
				wp_schedule_event( time(), 'sby30mins', 'sby_feed_update' );
			} else {
				wp_schedule_event( time(), 'hourly', 'sby_feed_update' );
			}
		}

	}
}
