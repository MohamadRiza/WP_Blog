<?php

/**
 * Service responsible with plugin ajax functionality.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Feed;
use SmashBalloon\TikTokFeeds\Common\FeedCache;
use SmashBalloon\TikTokFeeds\Common\Database\FeedsTable;
use SmashBalloon\TikTokFeeds\Common\Database\SourcesTable;
use SmashBalloon\TikTokFeeds\Common\Database\FeedCacheTable;
use SmashBalloon\TikTokFeeds\Common\Database\PostsTable;
use SmashBalloon\TikTokFeeds\Common\Utils;
use SmashBalloon\TikTokFeeds\Common\Relay\Relay;

/**
 * Class AjaxHandlerService
 */
class AjaxHandlerService extends ServiceProvider
{
	/**
	 * Ajax hooks to register.
	 */
	public function register()
	{
		add_action('wp_ajax_sbtt_builder_update', array( $this, 'builder_update' ));
		add_action('wp_ajax_sbtt_duplicate_feed', array( $this, 'duplicate_feed' ));
		add_action('wp_ajax_sbtt_delete_feeds', array( $this, 'delete_feeds' ));
		add_action('wp_ajax_sbtt_feed_customizer_fly_preview', array( $this, 'feed_customizer_fly_preview' ));

		add_action('wp_ajax_sbtt_delete_source', array( $this, 'delete_source' ));
		add_action('wp_ajax_sbtt_import_feed_settings', array( $this, 'import_feed_settings' ));
		add_action('wp_ajax_sbtt_clear_all_caches', array( $this, 'clear_all_caches' ));
		add_action('wp_ajax_sbtt_reset_posts_images', array($this, 'reset_posts_images'));
	}

	/**
	 * Update the builder
	 */
	public function builder_update()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$feed_id       = isset($_POST['feed_id']) ? absint($_POST['feed_id']) : false;
		$feed_name     = isset($_POST['feed_name']) ? sanitize_text_field($_POST['feed_name']) : '';
		$feed_title    = isset($_POST['feed_title']) ? sanitize_text_field($_POST['feed_title']) : '';
		$feed_type     = isset($_POST['feedType']) ? sanitize_text_field($_POST['feedType']) : 'own_timeline';
		$feed_template = isset($_POST['feedTemplate']) ? sanitize_text_field($_POST['feedTemplate']) : 'default';
		$sources       = isset($_POST['sources']) ? sbtt_sanitize_data($_POST['sources']) : [];
		$styles        = isset($_POST['feed_style']) ? sanitize_text_field(wp_unslash($_POST['feed_style'])) : '';
		$settings      = isset($_POST['settings']) ? sbtt_sanitize_data($_POST['settings']) : [];

		$defaults = sbtt_feed_settings_defaults();
		if (isset($_POST['newInsert']) && ! $feed_id) {
			$settings = array(
				'feedType'     => $feed_type,
				'sources'      => $sources,
				'feedTemplate' => $feed_template,
			);
			$settings = wp_parse_args($settings, $defaults);
			$settings = Utils::get_feed_settings_by_feed_templates($settings);
		}

		$feeds = array(
			'id'            => $feed_id,
			'feed_name'     => $feed_name,
			'feed_title'    => $feed_title,
			'settings'      => $settings,
			'feed_style'    => $styles,
			'last_modified' => date('Y-m-d H:i:s'),
		);

		// Update or insert the feed.
		$feeds_table = new FeedsTable();
		$result     = $feeds_table->update_or_insert($feeds);
		$feed_id    = isset($_POST['newInsert']) ? $result : $feed_id;

		if ($feed_id) {
			$return = [	'feed_id' => $feed_id ];

			if (isset($_POST['get_posts']) && $_POST['get_posts'] == true) {
				$feed = new Feed($settings, $feed_id, new FeedCache($feed_id));
				$feed->init();
				$feed->get_set_cache();
				$posts           = $feed->get_post_set_page();
				$return['posts'] = $posts;
			}

			wp_send_json_success($return);
		} else {
			wp_send_json_error(['feed_id' => false]);
		}

		wp_die();
	}

	/**
	 * Used in an AJAX call to duplicate feeds in the Database
	 * $_POST data.
	 *
	 * @since 1.0
	 */
	public function duplicate_feed()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$feed_id = isset($_POST['feed_id']) ? absint($_POST['feed_id']) : false;
		if ($feed_id) {
			$feeds_table = new FeedsTable();
			$feeds_table->duplicate_feed($feed_id);

			wp_send_json_success(['feeds' => Utils::get_feeds_list()]);
		}

		wp_die();
	}

	/**
	 * Used in an AJAX call to delete feeds from the Database
	 * $_POST data.
	 *
	 * @since 1.0
	 */
	public function delete_feeds()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$feeds_id = isset($_POST['feeds_ids']) ? sbtt_sanitize_data($_POST['feeds_ids']) : false;
		if (! empty($feeds_id) && is_array($feeds_id)) {
			$feeds_id    = array_map('absint', $feeds_id);
			$feeds_table = new FeedsTable();
			$feeds_table->delete_feeds($feeds_id);

			wp_send_json_success(['feeds' => Utils::get_feeds_list()]);
		}

		wp_die();
	}

	/**
	 * Used in an AJAX call to preview the feed in the customizer
	 * Returns Feed info or false!
	 *
	 * @since 1.0
	 */
	public function feed_customizer_fly_preview()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		if (! isset($_POST['feedID']) && ! isset($_POST['previewSettings'])) {
			wp_send_json_error();
		}

		$feed_id          = isset($_POST['feedID']) ? absint($_POST['feedID']) : false;
		$feed_name        = isset($_POST['feedName']) ? sanitize_text_field(wp_unslash($_POST['feedName'])) : '';
		$preview_settings = isset($_POST['previewSettings']) ? sbtt_sanitize_data($_POST['previewSettings']) : [];

		// Get updated feed info based on settings.
		$feed_cache = new FeedCache($feed_id, 12 * HOUR_IN_SECONDS);
		$feed_cache->clear('posts');

		$feed = new Feed($preview_settings, $feed_id, $feed_cache);
		$feed->init();
		$feed->get_set_cache();

		$posts  = $feed->get_post_set_page();
		$errors = $feed->get_errors();

		if (isset($preview_settings['sortRandomEnabled']) && $preview_settings['sortRandomEnabled'] === true) {
			shuffle($posts);
		}

		$return = [
			'posts'  => $posts,
			'errors' => $errors,
		];

		// Update feed settings depending on feed templates.
		if (isset($_POST['isFeedTemplatesPopup'])) {
			$preview_settings   = Utils::get_feed_settings_by_feed_templates($preview_settings);
			$return['settings'] = $preview_settings;
		}

		wp_send_json_success($return);

		wp_die();
	}

	/**
	 * Ajax handler to delete a source.
	 *
	 * @return void
	 */
	public function delete_source()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		if (! isset($_POST['sourceID'])) {
			wp_send_json_error();
		}

		if (isset($_POST['sourceID'], $_POST['sourceOpenID'])) {
			$source_id    = sanitize_text_field($_POST['sourceID']);
			$open_id      = sanitize_text_field($_POST['sourceOpenID']);
			$source_table = new SourcesTable();
			$source_table->delete_source($source_id);

			// TODO:: test this
			$args = [
				'open_id' => $open_id,
			];

			$relay    = new Relay();
			$response = $relay->call('source/remove', $args);

			$sources = $source_table->get_sources();

			wp_send_json_success(['sourcesList' => $sources ? $sources : []]);
		}

		wp_die();
	}

	/**
	 * Ajax handler to import feed settings.
	 *
	 * @return void
	 */
	public function import_feed_settings()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$filename = isset($_FILES['feedFile']['name']) ? sanitize_file_name($_FILES['feedFile']['name']) : '';
		$ext      = pathinfo($filename, PATHINFO_EXTENSION);
		if ('json' !== $ext) {
			wp_send_json_error([ 'message' => __('Supports only JSON file', 'feeds-for-tiktok') ]);
		}

		$tmp_name = isset($_FILES['feedFile']['tmp_name']) ? sanitize_text_field($_FILES['feedFile']['tmp_name']) : '';
		if (! is_uploaded_file($tmp_name)) {
			wp_send_json_error([ "message" => __("Don't have file, Please try again", "feeds-for-tiktok") ]);
		}

		$imported_settings = file_exists($tmp_name) ? file_get_contents($tmp_name) : '';
		if (empty($imported_settings)) {
			wp_send_json_error([ "message" => __("Don't have file, Please try again", "feeds-for-tiktok") ]);
		}

		$imported_settings = json_decode($imported_settings, true);
		if (empty($imported_settings['sourcesList'])) {
			wp_send_json_error([ "message" => __("No feed source is included. Cannot upload feed.", "feeds-for-tiktok") ]);
		}

		$feed_id    = false;
		$feed_name  = isset($imported_settings['feed_name']) ? sanitize_text_field($imported_settings['feed_name']) : '';
		$feed_title = isset($imported_settings['feed_title']) ? sanitize_text_field($imported_settings['feed_title']) : '';
		$feed_style = isset($imported_settings['feed_style']) ? sanitize_text_field(wp_unslash($imported_settings['feed_style'])) : '';
		$settings   = isset($imported_settings['settings']) ? sbtt_sanitize_data($imported_settings['settings']) : [];

		$feeds = array(
			'id'            => false,
			'feed_name'     => $feed_name,
			'feed_title'    => $feed_title,
			'settings'      => $settings,
			'feed_style'    => $feed_style,
			'last_modified' => date('Y-m-d H:i:s'),
		);

		// Update or insert the feed.
		$feeds_table = new FeedsTable();
		$feed_id     = $feeds_table->update_or_insert($feeds);

		if ($feed_id) {
			wp_send_json_success([
				"message" => __("Feed settings imported successfully", "feeds-for-tiktok"),
				"feed_id" => $feed_id
			]);
		} else {
			wp_send_json_error([ "message" => __("Could not import feed, Please try again", "feeds-for-tiktok") ]);
		}

		wp_die();
	}

	/**
	 * Ajax handler to clear all caches.
	 *
	 * @return void
	 */
	public function clear_all_caches()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		// Clear cache from cache table.
		$cache_table = new FeedCacheTable();
		$cache_table->clear_feed_cache();

		$posts_table = new PostsTable();
		$posts_table->delete_all_posts();

		// Clear transients.
		global $wpdb;
		$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_sbtt_%'");

		global $wpdb;
		$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_sbtt_%'");

		// Clear cache of major caching plugins.
		if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
			$GLOBALS['wp_fastest_cache']->deleteCache();
		}

		// WP Super Cache.
		if (function_exists('wp_cache_clear_cache')) {
			wp_cache_clear_cache();
		}

		// W3 Total Cache.
		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
		}

		if (function_exists('sg_cachepress_purge_cache')) {
			sg_cachepress_purge_cache();
		}

		if (class_exists('W3_Plugin_TotalCacheAdmin')) {
			$plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
			$plugin_totalcacheadmin->flush_all();
		}

		if (function_exists('rocket_clean_domain')) {
			rocket_clean_domain();
		}

		if (has_action('litespeed_purge_all')) {
			do_action('litespeed_purge_all');
		}

		$global_settings = get_option('sbtt_global_settings', []);
		$global_settings['api_site_access_token'] = '';
		$global_settings['api_site_error']        = '';
		update_option('sbtt_global_settings', $global_settings);
		delete_option('sbtt_resize_images_data');

		wp_send_json_success();

		wp_die();
	}

	/**
	 * Ajax handler to reset posts and images.
	 *
	 * @return void
	 */
	public function reset_posts_images()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$feed_cache_table = new FeedCacheTable();
		$feed_cache_table->clear_feed_cache();

		$upload = wp_upload_dir();
		$upload_dir = trailingslashit($upload['basedir']) . SBTT_UPLOAD_FOLDER_NAME;

		if (file_exists($upload_dir)) {
			$files = glob($upload_dir . '/*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
		}

		$posts_table = new PostsTable();
		$posts_table->reset_images_done();

		wp_send_json_success();

		wp_die();
	}
}
