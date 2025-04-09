<?php

namespace SmashBalloon\YouTubeFeed\Services\Upgrade\Routines;

use Smashballoon\Customizer\DB;
use Smashballoon\Customizer\Feed_Saver;
use Smashballoon\Customizer\Feed_Locator;

class V2Routine extends UpgradeRoutine {
	protected $target_version = 2.0;

	/**
	 * @var Feed_Saver
	 */
	private $feed_saver;

	/**
	 * @var Feed_Locator
	 */
	private $feed_locator;

	/**
	 * @var DB
	 */
	private $db;

	public function __construct(Feed_Saver $feed_saver, DB $DB, Feed_Locator $feed_locator) {
		$this->feed_saver = $feed_saver;
		$this->feed_locator = $feed_locator;
		$this->db = $DB;
	}

	public function run() {
		$this->create_tables();
		$this->migrate_legacy_feeds();
		$this->set_rating_notice_and_first_install_flags();
	}

	private function create_tables() {
		$this->feed_locator->create_table();
		$this->db->create_tables(true, true);
	}

	private function migrate_legacy_feeds() {
		$statuses_option = get_option( 'sby_statuses', array() );
		$args = array(
			'html_location' => array( 'header', 'footer', 'sidebar', 'content', 'unknown' ),
			'group_by' => 'shortcode_atts',
			'page' => 1
		);
		$feeds_data = $this->feed_locator->legacy_feed_locator_query($args);
		$legacy_count = count($feeds_data);
		$statuses_option['support_legacy_shortcode'] = false;

		if($legacy_count > 0) {
			if($legacy_count > 1) {
				$statuses_option['legacy_onboarding'] = array(
					'active' => true,
					'type'=> 'multiple'
				);
				$statuses_option['support_legacy_shortcode'] = true;
			} else {
				$statuses_option['legacy_onboarding'] = array(
					'active' => true,
					'type'=> 'single'
				);

				$shortcode_atts = ! empty($feeds_data[0] ) && $feeds_data[0]['shortcode_atts'] != '[""]' ? json_decode( $feeds_data[0]['shortcode_atts'], true ) : [];
				$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

				$statuses_option['support_legacy_shortcode'] = true;

				$shortcode_atts['from_update'] = true;

				$this->feed_saver->set_data( $shortcode_atts );
				$this->feed_saver->set_feed_name( "Legacy feed" );

				$new_feed_id = $this->feed_saver->update_or_insert();

				$args = array(
					'new_feed_id' => $new_feed_id,
					'legacy_feed_id' => $feeds_data[0]['feed_id'],
				);

				$this->feed_locator->update_legacy_to_builder( $args );
			}
		}

		update_option( 'sby_statuses', $statuses_option, true );
	}

	private function set_rating_notice_and_first_install_flags() {
		$sby_statuses_option = get_option( 'sby_statuses', array() );

		if ( ! isset( $sby_statuses_option['first_install'] ) ) {

			$options_set = get_option( 'sby_settings', false );

			if ( $options_set ) {
				$sby_statuses_option['first_install'] = 'from_update';
			} else {
				$sby_statuses_option['first_install'] = time();
			}

			$sby_rating_notice_option = get_option( 'sby_rating_notice', false );

			if ( $sby_rating_notice_option === 'dismissed' ) {
				$sby_statuses_option['rating_notice_dismissed'] = time();
			}

			$sby_rating_notice_waiting = get_transient( 'feeds_for_youtube_rating_notice_waiting' );

			if ( $sby_rating_notice_waiting === false
			     && $sby_rating_notice_option === false ) {
				$time = 2 * WEEK_IN_SECONDS;
				set_transient( 'feeds_for_youtube_rating_notice_waiting', 'waiting', $time );
				update_option( 'sby_rating_notice', 'pending', false );
			}

			update_option( 'sby_statuses', $sby_statuses_option, false );

		}
	}
}
