<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin\Settings;

use Smashballoon\Customizer\Container;
use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Builder;
use SmashBalloon\YouTubeFeed\SBY_Settings;

class HelpPage extends BaseSettingPage {

	protected $menu_slug = 'support';
	protected $menu_title = 'Support';
	protected $page_title = 'Support';
	protected $has_menu = true;
	protected $template_file = 'settings.index';
	protected $has_assets = true;
	protected $menu_position = 3;
	protected $menu_position_free_version = 3;

	/**
	 * @var Feed_Builder
	 */
	private $feed_builder;

    public function __construct() {
		$this->feed_builder = Container::getInstance()->get(Feed_Builder::class);
	}

	public function register() {
		parent::register();

		add_filter( 'sby_localized_settings', [ $this, 'filter_settings_object' ] );
	}

	public static function get_whitespace( $times ) {
		return str_repeat( '&nbsp;', $times );
	}

	public function filter_settings_object( $settings ) {
		$settings['system_info'] = $this->get_system_info();
		$settings['system_info_n'] = str_replace( '<br />', "\n", $this->get_system_info() );
		$settings['feeds']  = Container::getInstance()->get(Feed_Builder::class)->get_feed_list();

		return $settings;
	}
	
	public function get_system_info() {
		$output = '';

		// Build the output strings
		$output .= $this->get_site_n_server_info();
		$output .=  $this->get_active_plugins_info();
		$output .=  $this->get_global_settings_info();
		$output .=  $this->get_feeds_settings_info();
		$output .=  $this->get_sources_info();
		$output .=  $this->get_cron_report();

		return $output;
	}

	/**
	 * Get Site and Server Info
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public function get_site_n_server_info() {
		$allow_url_fopen = ini_get( 'allow_url_fopen' ) ? 'Yes' : 'No';
		$php_curl        = is_callable( 'curl_init' ) ? 'Yes' : 'No';
		$php_json_decode = function_exists( 'json_decode' ) ? 'Yes' : 'No';
		$php_ssl         = in_array( 'https', stream_get_wrappers(), true ) ? 'Yes' : 'No';
		$settings = sby_get_database_settings();
		$api_verification_status = get_option('sby_api_key_verification', null);
		$output  = '## SITE/SERVER INFO: ##<br />';
		$output .= 'Plugin Version:' . self::get_whitespace( 11 ) . esc_html( SBY_PLUGIN_NAME ) . '<br />';
		$output .= 'Site URL:' . self::get_whitespace( 17 ) . esc_html( site_url() ) . '<br />';
		$output .= 'Home URL:' . self::get_whitespace( 17 ) . esc_html( home_url() ) . '<br />';
		$output .= 'WordPress Version:' . self::get_whitespace( 8 ) . esc_html( get_bloginfo( 'version' ) ) . '<br />';
		$output .= 'PHP Version:' . self::get_whitespace( 14 ) . esc_html( PHP_VERSION ) . '<br />';
		$output .= 'Web Server Info:' . self::get_whitespace( 10 ) . esc_html( $_SERVER['SERVER_SOFTWARE'] ) . '<br />';
		$output .= 'PHP allow_url_fopen:' . self::get_whitespace( 6 ) . esc_html( $allow_url_fopen ) . '<br />';
		$output .= 'PHP cURL:' . self::get_whitespace( 17 ) . esc_html( $php_curl ) . '<br />';
		$output .= 'JSON:' . self::get_whitespace( 21 ) . esc_html( $php_json_decode ) . '<br />';
		$output .= 'SSL Stream:' . self::get_whitespace( 15 ) . esc_html( $php_ssl ) . '<br />';
		$output .= 'API Key:' . self::get_whitespace( 18 ) . esc_html( isset($settings['api_key']) ? $settings['api_key'] : 'Empty' ) . '<br />';
		$output .= 'API Status:' . self::get_whitespace( 15 ) . esc_html( !empty($api_verification_status) ? ($api_verification_status->status == true ? "Successful" : "Failed") : 'Unknown' ) . '<br />';
		$output .= '<br />';

		return $output;
	}

	/**
	 * Get Active Plugins
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public function get_active_plugins_info() {
		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins' );
		$output         = '## ACTIVE PLUGINS: ## <br />';

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( in_array( $plugin_path, $active_plugins, true ) ) {
				$output .= esc_html( $plugin['Name'] ) . ': ' . esc_html( $plugin['Version'] ) . '<br />';
			}
		}

		$output .= '<br />';

		return $output;
	}

	/**
	 * Get Global Settings
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public function get_global_settings_info() {
		$output             = '## GLOBAL SETTINGS: ## <br />';
		$sby_license_key    = get_option( 'sby_license_key' );
		$sby_license_data   = get_option( 'sby_license_data' );
		$sby_license_status = get_option( 'sby_license_status' );
		$sby_settings       = get_option( 'settings', array() );

		$usage_tracking = get_option(
			'sby_usage_tracking',
			array(
				'last_send' => 0,
				'enabled'   => \sby_is_pro_version(),
			)
		);

		$output .= 'License key: ';
		if ( $sby_license_key ) {
			$output .= esc_html( $sby_license_key );
		} else {
			$output .= ' Not added';

		}
		$output .= '<br />';
		$output .= 'License status: ';
		if ( $sby_license_status ) {
			$output .= $sby_license_status;
		} else {
			$output .= ' Inactive';
		}
		$output .= '<br />';
		$output .= 'Preserve settings if plugin is removed: ';
		$output .= isset( $sby_settings['preserve_settings'] ) && ( $sby_settings['preserve_settings'] ) ? 'Yes' : 'No';
		$output .= '<br />';
		$output .= 'Connected Accounts: ';
		$output .= 'Placeholder!';

		$output .= '<br />';
		$output .= 'Caching: ';
		if ( wp_next_scheduled( 'sby_feed_update' ) ) {
			$time_format = get_option( 'time_format' );
			if ( ! $time_format ) {
				$time_format = 'g:i a';
			}
			//
			$schedule = wp_get_schedule( 'sby_feed_update' );
			if ( $schedule === '30mins' ) {
				$schedule = __( 'every 30 minutes', 'feeds-for-youtube' );
			}
			if ( $schedule === 'twicedaily' ) {
				$schedule = __( 'every 12 hours', 'feeds-for-youtube' );
			}
			$sby_next_cron_event = wp_next_scheduled( 'sby_feed_update' );
			$output              = __( 'Next check', 'feeds-for-youtube' ) . ': ' . gmdate( $time_format, $sby_next_cron_event + sby_get_utc_offset() ) . ' (' . $schedule . ')';

		} else {
			$output .= 'Nothing currently scheduled';
		}
		$output .= '<br />';
		$output .= 'GDPR: ';
		$output .= isset( $sby_settings['gdpr'] ) ? $sby_settings['gdpr'] : ' Not setup';
		$output .= '<br />';
		$output .= 'Custom CSS: ';
		$output .= isset( $sby_settings['custom_css'] ) && ! empty( $sby_settings['custom_css'] ) ? $sby_settings['custom_css'] : 'Empty';
		$output .= '<br />';
		$output .= 'Custom JS: ';
		$output .= isset( $sby_settings['custom_js'] ) && ! empty( $sby_settings['custom_js'] ) ? $sby_settings['custom_js'] : 'Empty';
		$output .= '<br />';
		$output .= 'Usage Tracking: ';
		$output .= isset( $usage_tracking['enabled'] ) && $usage_tracking['enabled'] === true ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		$output .= 'AJAX theme loading fix: ';
		$output .= isset( $sby_settings['ajax_theme'] ) && $sby_settings['ajax_theme'] ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		$output .= 'AJAX Initial: ';
		$output .= isset( $sby_settings['sb_ajax_initial'] ) && $sby_settings['sb_ajax_initial'] === true ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		$output .= 'Enqueue in Head: ';
		$output .= isset( $sby_settings['enqueue_js_in_head'] ) && $sby_settings['enqueue_js_in_head'] === true ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		$output .= 'Disable WP Posts: ';
		$output .= !empty($sby_settings['disable_wp_posts']) ? 'True' : 'False';;
		$output .= '<br />';
		$output .= 'Enqueue in Shortcode: ';
		$output .= isset( $sby_settings['enqueue_css_in_shortcode'] ) && $sby_settings['enqueue_css_in_shortcode'] === true ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		$output .= 'Enable JS Image: ';
		$output .= isset( $sby_settings['disable_js_image_loading'] ) && $sby_settings['disable_js_image_loading'] === false ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		$output .= 'Admin Error Notice: ';
		$output .= isset( $sby_settings['disable_admin_notice'] ) && $sby_settings['disable_admin_notice'] === false ? 'Enabled' : 'Disabled';
		$output .= '<br />';
		return $output;
	}

	/**
	 * Get Feeds Settings
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public function get_feeds_settings_info() {
		$output = '## FEEDS: ## <br />';

		$feeds_list  = $this->feed_builder->get_feed_list();

		$i = 0;
		foreach ( $feeds_list as $feed ) {
			$settings = json_decode($feed['settings'], true);
			$type = ! empty( $settings['type'] ) ? $settings['type'] : 'channel';
			if ( $i >= 25 ) {
				break;
			}
			$output .= $feed['feed_name'];
			$output .= '<br />';
			if ( isset( $feed['location_summary'] ) && count( $feed['location_summary'] ) > 0 ) {
				$first_feed = $feed['location_summary'][0];
				if ( ! empty( $first_feed['link'] ) ) {
					$output .= esc_html( $first_feed['link'] ) . '?sb_debug';
					$output .= '<br />';
				}
			}

			if ( $i < ( count( $feeds_list ) - 1 ) ) {
				$output .= '<br />';
			}
			$i++;
		}
		$output .= '<br />';

		return $output;
	}

	/**
	 * Get Feeds Settings
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public function get_sources_info() {
		$output = '## Sources: ## <br />';

		foreach ( [] as $source ) {

			$output .= $source['account_id'];
			$output .= '<br />';
			$output .= 'Type: ' . esc_html( $source['account_type'] );
			$output .= '<br />';
			$output .= 'Username: ' . esc_html( $source['username'] );
			$output .= '<br />';
			$output .= 'Error: ' . esc_html( $source['error'] );
			$output .= '<br />';
			$output .= '<br />';
			$output .= '<br />';

		}
		$output .= '<br />';

		return $output;
	}

	/**
	 * Get Reports
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public function get_cron_report() {
		$output      = '## Cron Cache Report: ## <br />';
		$cron_report = get_option( 'sby_cron_report', array() );
		if ( ! empty( $cron_report ) ) {
			$output .= 'Time Ran: ' . esc_html( $cron_report['notes']['time_ran'] );
			$output .= '<br />';
			$output .= 'Found Feeds: ' . esc_html( $cron_report['notes']['num_found_transients'] );
			$output .= '<br />';
			$output .= '<br />';

			foreach ( $cron_report as $key => $value ) {
				if ( $key !== 'notes' ) {
					$output .= esc_html( $key ) . ':';
					$output .= '<br />';
					if ( ! empty( $value['last_retrieve'] ) ) {
						$output .= 'Last Retrieve: ' . esc_html( $value['last_retrieve'] );
						$output .= '<br />';
					}
					$output .= 'Did Update: ' . esc_html( $value['did_update'] );
					$output .= '<br />';
					$output .= '<br />';
				}
			}
		} else {
			$output .= '<br />';
		}

		$cron = _get_cron_array();
		foreach ( $cron as $key => $data ) {
			$is_target = false;
			foreach ( $data as $key2 => $val ) {
				if ( strpos( $key2, 'sby' ) !== false || strpos( $key2, 'sb_youtube' ) !== false ) {
					$is_target = true;
					$output   .= esc_html( $key2 );
					$output   .= '<br />';
				}
			}
			if ( $is_target ) {
				$output .= esc_html( date( 'Y-m-d H:i:s', $key ) );
				$output .= '<br />';
				$output .= esc_html( 'Next Scheduled: ' . round( ( (int) $key - time() ) / 60 ) . ' minutes' );
				$output .= '<br />';
				$output .= '<br />';
			}
		}

		return $output;
	}

}