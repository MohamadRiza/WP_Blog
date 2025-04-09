<?php

/**
 * YouTube Feed Saver Manager
 *
 * @since 2.0
 */

namespace SmashBalloon\YouTubeFeed\Builder;

use SmashBalloon\YouTubeFeed\SBY_Cache;
use SmashBalloon\YouTubeFeed\Services\ShortcodeService;
use SmashBalloon\YouTubeFeed\Customizer\DB;
use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\SBY_Parse;
use SmashBalloon\YouTubeFeed\Helpers\Util;

class SBY_Feed_Saver_Manager
{
	/**
	 * AJAX hooks for various feed data related functionality
	 *
	 * @since 2.0
	 */
	public static function register()
	{
		add_action('wp_ajax_sby_feed_saver_manager_builder_update', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'builder_update' ));
		add_action('wp_ajax_sby_feed_saver_manager_get_feed_list_page', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'get_feed_list_page' ));
		add_action('wp_ajax_sby_feed_saver_manager_fly_preview', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'feed_customizer_fly_preview' ));
		add_action('wp_ajax_sby_feed_handle_saver_manager_fly_preview', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'feed_customizer_feed_handle_fly_preview' ));
		add_action('wp_ajax_sby_feed_saver_manager_clear_single_feed_cache', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'clear_single_feed_cache' ));
		add_action('wp_ajax_sby_feed_saver_manager_duplicate_feed', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'duplicate_feed' ));
		add_action('wp_ajax_sby_feed_saver_manager_delete_feeds', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'delete_feed' ));
		add_action('wp_ajax_sby_dismiss_onboarding', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'after_dismiss_onboarding' ));
		add_action('wp_ajax_sby_feed_refresh', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'feed_refresh' ));
		add_action('wp_ajax_sby_feed_saver_clear_comments_cache', array( 'SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Saver_Manager', 'clear_comments_cache' ));
	}

	/**
	 * Used in an AJAX call to update settings for a particular feed.
	 * Can also be used to create a new feed if no feed_id sent in
	 * $_POST data.
	 *
	 * @since 2.0
	 */
	public static function builder_update()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		$settings_data = $_POST;

		$feed_id     = false;
		$is_new_feed = isset($settings_data['new_insert']) ? true : false;
		if (! empty($settings_data['feed_id'])) {
			$feed_id = sanitize_text_field($settings_data['feed_id']);
			unset($settings_data['feed_id']);
		} elseif (isset($settings_data['feed_id'])) {
			unset($settings_data['feed_id']);
		}
		unset($settings_data['action']);
		unset($settings_data['nonce']);

		if (! isset($settings_data['feed_name'])) {
			$settings_data['feed_name'] = '';
		}

		// If all video elements are disabled then store it as an empty array
		if (empty($settings_data['settings']['include'])) {
			$settings_data['settings']['include'] = array();
		}
		if (empty($settings_data['settings']['hoverinclude'])) {
			$settings_data['settings']['hoverinclude'] = array();
		}
		$update_feed = isset($settings_data['update_feed']) ? true : false;
		unset($settings_data['update_feed']);

		if ($is_new_feed) {
			$settings_data = SBY_Feed_Templates_Settings::get_feed_settings_by_feed_templates($settings_data);
		}

		$selected_feed_model = isset($settings_data['selectedFeedModel']) ? self::filter_feed_model_data($settings_data['selectedFeedModel'], $settings_data['feedtype']) : '';

		// Check if New
		if (isset($settings_data['new_insert']) && $settings_data['new_insert'] === 'true' && isset($settings_data['feedtype'])) {
			$feedtype = sanitize_text_field($settings_data['feedtype']);
			$feed_type_data = self::create_single_feed_type_data($feedtype, $selected_feed_model);
			$settings_data = array_merge($feed_type_data, $settings_data);

			$settings_data['feed_name'] =  self::create_feed_name($settings_data['feedtype'], $selected_feed_model);
		}

		unset($settings_data['feedtype']);
		unset($settings_data['selectedFeedModel']);
		unset($settings_data['new_insert']);
		unset($settings_data['sourcename']);
		$feed_name = '';
		if ($update_feed) {
			$feed_name                            = $settings_data['feed_name'];
			$settings_data                        = $settings_data['settings'];
		}

		unset($settings_data['sources']);

		$feed_saver = new SBY_Feed_Saver($feed_id);
		$feed_saver->set_feed_name($feed_name);
		$feed_saver->set_data($settings_data);

		$return = array(
			'success' => false,
			'feed_id' => false,
		);

		if ($feed_saver->update_or_insert()) {
			$return = array(
				'success' => true,
				'feed_id' => $feed_saver->get_feed_id(),
			);
			if ($is_new_feed) {
				echo wp_json_encode($return);
				wp_die();
			} else {
				$feed_cache = new SBY_Cache($feed_id);
				$feed_cache->clear('all');
				$feed_cache->clear('posts');
				echo wp_json_encode($return);
				wp_die();
			}
		}
		echo wp_json_encode($return);

		wp_die();
	}

	/**
	 * Get a list of feeds with a limit and offset like a page
	 *
	 * @since 2.0
	 */
	public static function get_feed_list_page()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		$args = array( 'page' => (int)$_POST['page'] );
		$feeds_data = sby_builder_pro()->get_feed_list($args);

		echo wp_json_encode($feeds_data);

		wp_die();
	}

	/**
	 * Used in an AJAX call to delete a feed cache from the Database
	 * $_POST data.
	 *
	 * @since 2.0
	 */
	public static function clear_single_feed_cache()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		$feed_id = sanitize_key($_POST['feedID']);
		// clear object cache
		sby_clear_object_cache();
		// clear transients
		sby_clear_cache();
		self::feed_customizer_fly_preview();
		wp_die();
	}

	/**
	 * Used To check if it's customizer Screens
	 * Returns Feed info or false!
	 *
	 * @param bool $include_comments
	 *
	 * @return array|bool
	 *
	 * @since 2.0
	 */
	public static function maybe_feed_customizer_data($include_comments = false)
	{
		if (isset($_GET['feed_id'])) {
			$feed_id      = sanitize_key($_GET['feed_id']);
			$feed_saver   = new SBY_Feed_Saver($feed_id);
			$settings     = $feed_saver->get_feed_settings();
			$feed_db_data = $feed_saver->get_feed_db_data();

			if ($settings !== false) {
				$return = array(
					'feed_info'  => $feed_db_data,
					'headerData' => $feed_db_data,
					'settings'   => $settings,
					'posts'      => array(),
				);
				if (intval($feed_id) > 0) {
					$instagram_feed_settings = new \SB_Instagram_Settings_Pro(
						array(
							'feed'       => $feed_id,
							'customizer' => true,
						),
						sbi_defaults()
					);
				} else {
					$instagram_feed_settings = new \SB_Instagram_Settings_Pro($settings, sbi_defaults());
				}

				$instagram_feed_settings->set_feed_type_and_terms();
				$instagram_feed_settings->set_transient_name();
				$transient_name = $instagram_feed_settings->get_transient_name();
				$settings       = $instagram_feed_settings->get_settings();

				$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
				if ($feed_id === 'legacy' && $transient_name === 'sbi_false') {
					$transient_name = 'sbi_legacy';
				}
				$instagram_feed = new \SB_Instagram_Feed_Pro($transient_name);

				$instagram_feed->set_cache($instagram_feed_settings->get_cache_time_in_seconds(), $settings);

				if ($instagram_feed->regular_cache_exists()) {
					$instagram_feed->set_post_data_from_cache();

					if ($instagram_feed->need_posts($settings['num']) && $instagram_feed->can_get_more_posts()) {
						while ($instagram_feed->need_posts($settings['num']) && $instagram_feed->can_get_more_posts()) {
							$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
						}

						$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
					}
				} else {
					while ($instagram_feed->need_posts($settings['num']) && $instagram_feed->can_get_more_posts()) {
						$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
					}

					if ($instagram_feed->out_of_next_pages() || $instagram_feed->should_look_for_db_only_posts($settings, $feed_type_and_terms)) {
						$instagram_feed->add_db_only_posts($transient_name, $settings, $feed_type_and_terms);
					}

					if (! $instagram_feed->should_use_backup()) {
						$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
					} elseif ($instagram_feed->should_cache_error()) {
						$cache_time = min($instagram_feed_settings->get_cache_time_in_seconds(), 15 * 60);
						$instagram_feed->cache_feed_data($cache_time, false);
					}
				}
				$return['posts'] = $instagram_feed->get_post_data();

				$header_data = array();

				$instagram_feed->set_remote_header_data($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
				$header_data = $instagram_feed->get_header_data();
				if ($settings['stories'] && ! empty($header_data)) {
					$instagram_feed->set_remote_stories_data($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
				}
				$instagram_feed->cache_header_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
				if (! empty($header_data) && \SB_Instagram_Connected_Account::local_avatar_exists($header_data['username'])) {
					$header_data['local_avatar_url'] = \SB_Instagram_Connected_Account::get_local_avatar_url($header_data['username']);
					$header_data['local_avatar']     = \SB_Instagram_Connected_Account::get_local_avatar_url($header_data['username']);
				} else {
					$header_data['local_avatar'] = false;
				}
				$header_data['local_avatar'] = false;
				$return['header']            = $header_data;
				$return['headerData']        = $header_data;

				return $return;
			}
		}
		return false;
	}

	/**
	 * Get single feed type create data
	 *
	 * @since 2.0
	 */
	public static function create_single_feed_type_data($feedtype, $model)
	{
		$feed_data = array();
		$feed_data['usecustomsearch'] = '';
		$feed_data['showpast'] = '';
		$feed_data['customsearch'] = '';
		$feed_data['api_key'] = '';
		$feed_data['type'] = $feedtype;
		$feed_data['channel'] = $model['channel'];
		$feed_data['playlist'] = $model['playlist'];
		$feed_data['favorites'] = $model['favorites'];
		$feed_data['search'] = $model['search'];
		$feed_data['live'] = $model['live'];
		$feed_data['single'] = $model['single'];

		$feed_data['caching_type'] = 'page';
		$feed_data['caching_time'] = 1;
		$feed_data['caching_time_unit'] = 'hours';
		$feed_data['cache_cron_interval'] = '30mins';
		$feed_data['cache_cron_time'] = 0;
		$feed_data['cache_cron_am_pm'] = 'am';

		return $feed_data;
	}

	/**
	 * Used to retrieve Feed Posts for preview screen
	 * Returns Feed info or false!
	 *
	 * @since 2.0
	 */
	public static function feed_customizer_fly_preview()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['feedID']) && isset($_POST['previewSettings'])) {
			$feed_id          = $_POST['feedID'];
			$preview_settings = SBY_Parse::parse_quoted_string_as_boolean($_POST['previewSettings']);
			$feed_name        = $_POST['feedName'];

			$preview_settings = isset($_POST['isFeedTemplatesPopup']) ? SBY_Feed_Templates_Settings::get_feed_settings_by_feed_templates($preview_settings) + $preview_settings : $preview_settings;

			$feed_saver = new SBY_Feed_Saver($feed_id);
			$feed_saver->set_feed_name($feed_name);
			$feed_saver->set_data($preview_settings);

			if (isset($_POST['clearCache']) && $_POST['clearCache'] == true) {
				sby_clear_cache();
			}

			$atts                = Feed_Builder::add_customizer_att(
				array(
					'feed'       => $feed_id,
					'customizer' => true,
				)
			);

			//Fixes Carousel preview issue.
			$only_preview = self::carousel_column_preview($_POST['previewScreen'],$preview_settings);

			$shortcode = new ShortcodeService();
			$feed_output = $shortcode->sby_youtube_feed($atts, $only_preview);
			$return['feed_html'] = $feed_output['feedInitOutput'];
			;
			$return['customizerDataSettings'] = $preview_settings;

			echo json_encode($return);
		}
		wp_die();
	}

	/**
	 * Used to feed handle for preview screen
	 * Returns Feed info or false!
	 *
	 * @since 2.0
	 */
	public static function feed_customizer_feed_handle_fly_preview()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['feedID']) && isset($_POST['previewSettings'])) {
			$feed_id          = $_POST['feedID'];
			$preview_settings = SBY_Parse::parse_quoted_string_as_boolean($_POST['previewSettings']);
			$feed_name        = $_POST['feedName'];
			$feed_type        = $_POST['feedType'];

			$preview_settings = isset($_POST['isFeedTemplatesPopup']) ? SBY_Feed_Templates_Settings::get_feed_settings_by_feed_templates($preview_settings) + $preview_settings : $preview_settings;
			$preview_settings = self::filter_feed_model_data($preview_settings, $feed_type);

			$feed_saver = new SBY_Feed_Saver($feed_id);
			$feed_saver->set_feed_name($feed_name);
			$feed_saver->set_data($preview_settings);

			if (isset($_POST['clearCache']) && $_POST['clearCache'] == true) {
				sby_clear_cache();
			}

			$atts                = Feed_Builder::add_customizer_att(
				array(
					'feed'       => $feed_id,
					'customizer' => true,
				)
			);

			$shortcode = new ShortcodeService();
			$feed_output = $shortcode->sby_youtube_feed($atts, $preview_settings);
			$return['feed_html'] = $feed_output['feedInitOutput'];
			;
			$return['customizerDataSettings'] = $preview_settings;

			echo json_encode($return);
		}
		wp_die();
	}

	/**
	 * Used in an AJAX call to duplicate a Feed
	 * $_POST data.
	 *
	 * @since 2.0
	 */
	public static function duplicate_feed()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		if (! empty($_POST['feed_id'])) {
			DB::duplicate_feed_query($_POST['feed_id']);
		}
	}

	/**
	 * Used in an AJAX call to delete feeds from the Database
	 * $_POST data.
	 *
	 * @since 2.0
	 */
	public static function delete_feed()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		if (! empty($_POST['feeds_ids']) && is_array($_POST['feeds_ids'])) {
			DB::delete_feeds_query($_POST['feeds_ids']);
		}
	}

	/**
	 * Create Feed Name
	 * This will create the feed name when creating new Feeds
	 *
	 * @since 2.0
	 */
	public static function create_feed_name($selected_feed, $selected_feed_models)
	{
		$feed_name = 'YouTube Feed';
		if ($selected_feed) {
			$feed_name .= ' - ' . ucfirst($selected_feed);
		}

		return DB::feeds_query_name($feed_name);
	}

	/**
	 * Used to dismiss onboarding using AJAX
	 *
	 * @since 2.0
	 */
	public static function after_dismiss_onboarding()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		$type = 'newuser';
		if (isset($_POST['was_active'])) {
			$type = sanitize_text_field($_POST['was_active']);
		}

		Feed_Builder::update_onboarding_meta('dismissed', $type);

		wp_die();
	}

	/**
	 * Used in an AJAX call to delete a feed cache from the Database
	 * $_POST data.
	 *
	 * @since 2.0
	 */
	public static function feed_refresh()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		$feed_id = sanitize_key($_POST['feedID']);

		sby_clear_cache();
		self::feed_customizer_fly_preview();
		wp_die();
	}

	/**
	 * Filter feed model data
	 *
	 * @since 2.2
	 */
	public static function filter_feed_model_data($feed_data, $feedtype)
	{
		if ($feedtype == 'channel') {
			$channel = $feed_data['channel'];
		} elseif ($feedtype == 'favorites') {
			$channel = $feed_data['favorites'];
		} elseif ($feedtype == 'live') {
			$channel = $feed_data['live'];
		}

		$channel_id = '';
		$channel_url = '';
		if (strpos($channel, '@') !== false && ( strpos($channel, 'https://') !== false || strpos($channel, 'http://') !== false )) {
			$channel_url = $channel;
		} elseif (strpos($channel, '@') !== false) {
			$channel_url = 'https://www.youtube.com/' . $channel;
		}
		if ($channel_url) {
			$saved_channel_id = Util::get_saved_channel_id($channel);
			if (! $saved_channel_id) {
				$channel_id = Util::get_channel_id_by_api_request($channel_url);
			} else {
				$channel_id = $saved_channel_id;
			}
		} else {
			$channel_id = $channel;
		}

		if ($feedtype == 'channel') {
			$feed_data['channel'] = $channel_id;
		} elseif ($feedtype == 'favorites') {
			$feed_data['favorites'] = $channel_id;
		} elseif ($feedtype == 'live') {
			$feed_data['live'] = $channel_id;
		}

		return $feed_data;
	}

	/**
	 * Determines what table and sanitization should be used
	 * when handling feed setting data.
	 *
	 * TODO: Add settings that need something other than sanitize_text_field
	 *
	 * @param string $key
	 *
	 * @return array
	 *
	 * @since 2.0
	 */
	public static function get_data_type($key)
	{
		switch ($key) {
			case 'sources':
				$return = array(
					'table'        => 'feed_settings',
					'sanitization' => 'sanitize_text_field',
				);
				break;
			case 'feed_title':
				$return = array(
					'table'        => 'feeds',
					'sanitization' => 'sanitize_text_field',
				);
				break;
			case 'feed_name':
				$return = array(
					'table'        => 'feeds',
					'sanitization' => 'sanitize_text_field',
				);
				break;
			case 'status':
				$return = array(
					'table'        => 'feeds',
					'sanitization' => 'sanitize_text_field',
				);
				break;
			case 'author':
				$return = array(
					'table'        => 'feeds',
					'sanitization' => 'int',
				);
				break;
			default:
				$return = array(
					'table'        => 'feed_settings',
					'sanitization' => 'sanitize_text_field',
				);
				break;
		}

		return $return;
	}

	/**
	 * Check if boolean
	 * for a value
	 *
	 * @param string     $type
	 * @param int|string $value
	 *
	 * @return int|string
	 *
	 * @since 2.0
	 */
	public static function is_boolean($value)
	{
		return ( $value === 'true' || $value === 'false' || is_bool($value) ) ? true : false;
	}

	public static function cast_boolean($value)
	{
		if ($value === 'true' || $value === true || $value === 'on') {
			return true;
		}
		return false;
	}

	/**
	 * Uses the appropriate sanitization function and returns the result
	 * for a value
	 *
	 * @param string     $type
	 * @param int|string $value
	 *
	 * @return int|string
	 *
	 * @since 2.0
	 */
	public static function sanitize($type, $value)
	{
		switch ($type) {
			case 'int':
				$return = intval($value);
				break;
			case 'boolean':
				$return = self::cast_boolean($value);
				break;
			default:
				$return = sanitize_text_field(wp_unslash($value));
				break;
		}

		return $return;
	}

	/**
	 * Clear comments cache AJAX call
	 *
	 * @return void
	 *
	 * @since 2.3.3
	 */
	public static function clear_comments_cache()
	{
		check_ajax_referer('sby-admin', 'nonce');

		if (! current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}

		delete_transient('sby_comment_cache');
		echo 'success';
		wp_die();
	}

	/**
	 * Carousel Column Preview.
	 *
	 * @param string $preview_screen
	 * @param array $preview_settings
	 * @return array
	 *
	 * @since 2.3.4
	 */
	public static function carousel_column_preview($preview_screen, $preview_settings) {
		// Return if preview_settings does not exist.
		if(empty($preview_settings)) {
			return $preview_settings;
		}

		$layout = !empty($preview_settings['layout']) ? $preview_settings['layout'] : '';

		// As we do not use iframe owl slider still thinks that it's in desktop mode ( on mobile view inside preview ). So we just intercepted the desktop value with the mobile one to simulate the desired behavior.
		if( !empty( $preview_screen ) && 'mobile' === $preview_screen && 'carousel' === $layout ) {
			$preview_settings['cols'] = $preview_settings['colsmobile'];
		}

		return $preview_settings;
	}
}
