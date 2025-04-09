<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin\Settings;

use Smashballoon\Customizer\Container;
use Smashballoon\Customizer\Feed_Builder;
use Smashballoon\Customizer\Feed_Saver;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\Pro\SBY_CPT;
use SmashBalloon\YouTubeFeed\SBY_Settings;

class SettingsPage extends BaseSettingPage {
	protected $has_assets = true;
	protected $has_menu = true;
	protected $template_file = 'settings.index';

	/**
	 * @var SBY_Settings
	 */
	private $settings;

	/**
	 * @var Feed_Saver
	 */
	private $feed_saver;

	public function __construct( Feed_Saver $feed_saver, SBY_Settings $settings) {
		$this->page_title = __( 'Settings', 'feeds-for-youtube' );
		$this->menu_title = __( 'Settings', 'feeds-for-youtube' );
		$this->menu_slug  = 'settings';
		$this->menu_position  = 1;
		$this->menu_position_free_version  = 1;

		$this->settings   = $settings;
		$this->feed_saver = $feed_saver;
	}

	public function register() {
		parent::register();

		add_action( 'wp_ajax_sby_update_settings', [ $this, 'handle_settings_update' ] );
		add_filter( 'sby_localized_settings', [ $this, 'filter_settings_object' ] );
	}

	public function handle_settings_update() {
		check_ajax_referer( 'sby-admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$sanitization_skip = [
			'custom_js'
		];

		unset( $_POST['nonce'] );

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			unset($_POST['custom_css']);
			unset($_POST['custom_js']);
		}

		$update_array = $_POST;

		$this->handle_single_video_settings( $update_array );

		foreach ( $_POST as $item => $value ) {
			if ( ! in_array( $item, $sanitization_skip ) ) {
				$update_array[ $item ] = sanitize_text_field( $value );
			}
		}

		wp_clear_scheduled_hook('sby_feed_update');

		if ( $this->settings->update_settings( $update_array ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	private function handle_single_video_settings( &$settings ) {
		$prefix      = "single_video_info_";
		$includes    = [];
		$post_status = "draft";

		foreach ( $settings as $key => $setting ) {
			if ( false !== strpos( $key, $prefix ) ) {
				$setting_name = str_replace( $prefix, "", $key );
				if ( ( $setting_name !== 'post_status' ) ) {
					if ( $setting === "true" ) {
						$includes[] = $setting_name;
					}
				} else {
					$post_status = $setting;
				}
				unset( $settings[ $key ] );
			}
		}

		update_option( SBY_CPT . '_settings', [
			"include"     => $includes,
			"post_status" => $post_status
		] );
	}

	private function get_next_cron_schedule() {
		$timestamp = wp_next_scheduled('sby_feed_update');
		$date = new \DateTime();
		$date->setTimestamp($timestamp);
		$date->setTimezone(wp_timezone());
		$date_string = $date->format('h:i');
		$settings = $this->settings->get_settings();
		$interval = !empty($settings['cache_cron_interval']) ? $settings['cache_cron_interval'] : '1hour';
		$am_pm = !empty($settings['cache_cron_am_pm']) ? $settings['cache_cron_am_pm'] : 'AM';

		switch($interval) {
			case '30mins':
				$interval_string = __('every 30 minutes', 'feeds-for-youtube');
				break;
			case '12hours':
				$interval_string = 'every 12 hours';
				break;
			case '24hours':
				$interval_string = 'every 24 hours';
				break;
			default:
				$interval_string = __('every hour', 'feeds-for-youtube');
		}

		return sprintf(__('<strong>Next check: %s %s (%s)</strong> - Note: Clicking "Clear All Caches" will reset this schedule.', 'feeds-for-youtube'), $date_string, strtoupper($am_pm), $interval_string);
	}

	public function filter_settings_object( $settings ) {
		$settings['settings'] = $this->settings->get_settings();
		$settings['sources']  = $this->feed_saver->get_source_list();
		$settings['sbyIsPro'] = \sby_is_pro() ? true : false;
		$settings['user_can_unfiltered_html'] = \current_user_can( 'unfiltered_html' ) ? true : false;
		$settings['feeds']  = Container::getInstance()->get(Feed_Builder::class)->get_feed_list();
		$settings['next_cron'] = $this->get_next_cron_schedule();
		$settings['connect_site_parameters'] = sby_builder_pro()->oauth_connet_parameters();

		return $settings;
	}
}