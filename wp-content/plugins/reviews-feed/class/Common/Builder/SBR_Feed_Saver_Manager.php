<?php

/**
 * Reviews Feed Saver Manager
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Builder;

use SmashBalloon\Reviews\Common\BusinessDataCache;
use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\Error_Reporter;
use SmashBalloon\Reviews\Common\Integrations\Providers\Google;
use SmashBalloon\Reviews\Common\Integrations\Providers\Yelp;
use SmashBalloon\Reviews\Common\Integrations\SBRelay;
use SmashBalloon\Reviews\Common\PostAggregator;
use SmashBalloon\Reviews\Common\Services\SettingsManagerService;
use SmashBalloon\Reviews\Common\Traits\SBR_Feed_Templates_Settings;
use SmashBalloon\Reviews\Common\Util;
use SmashBalloon\Reviews\Common\FeedCache;
use SmashBalloon\Reviews\Common\Helpers\Data_Encryption;

class SBR_Feed_Saver_Manager
{
	use SBR_Feed_Templates_Settings;

	/**
	 * AJAX hooks for various feed data related functionality
	 *
	 * @since 1.0
	 */
	public static function register()
	{
		add_action('wp_ajax_sbr_feed_saver_manager_builder_update', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'builder_update'));
		add_action('wp_ajax_sbr_feed_saver_manager_duplicate_feed', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'duplicate_feed'));
		add_action('wp_ajax_sbr_feed_saver_manager_delete_feeds', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'delete_feed'));
		add_action('wp_ajax_sbr_feed_saver_manager_fly_preview', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'feed_customizer_fly_preview'));
		add_action('wp_ajax_sbr_feed_saver_manager_start_moderation_mode', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'start_moderation_mode'));


		add_action('wp_ajax_sbr_feed_saver_manager_add_source', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'add_source'));
		add_action('wp_ajax_sbr_feed_saver_manager_add_facebook_souce', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'add_facebook_souce'));
		add_action('wp_ajax_sbr_feed_saver_manager_connect_manual_facebook', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'add_manual_facebook_souce'));
		add_action('wp_ajax_sbr_feed_saver_manager_delete_source', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'delete_souce'));
		add_action('wp_ajax_sbr_feed_saver_manager_update_api_key', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'update_api_key'));
		add_action('wp_ajax_sbr_import_feed_settings', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'import_feed_settings'));
		add_action('wp_ajax_sbr_clear_all_caches', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'clear_all_caches'));

		add_action('wp_ajax_sbr_feed_saver_manager_get_feed_list_page', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'get_feed_list_page'));
		add_action('wp_ajax_sbr_feed_saver_manager_create_new_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'create_new_collection'));
		add_action('wp_ajax_sbr_feed_saver_manager_addupdate_review_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'create_update_collection_review'));
		add_action('wp_ajax_sbr_feed_saver_manager_update_collection_name', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'update_collection_name'));
		add_action('wp_ajax_sbr_feed_saver_manager_get_source_posts', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'get_source_posts'));
		add_action('wp_ajax_sbr_feed_saver_manager_delete_review_from_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'delete_review_from_collection'));
		add_action('wp_ajax_sbr_feed_saver_manager_add_multiple_reviews_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'add_multiple_reviews_collection'));
		add_action('wp_ajax_sbr_feed_saver_manager_advanced_search_reviews', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'advanced_search_reviews'));
		add_action('wp_ajax_sbr_feed_saver_manager_duplicate_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'duplicate_collection'));
		add_action('wp_ajax_sbr_feed_saver_manager_load_more_sources', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'load_more_sources'));

		add_action('wp_ajax_sbr_feed_saver_manager_export_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'export_collection'));
		add_action('wp_ajax_sbr_import_full_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'import_full_collection'));
		add_action('wp_ajax_sbr_import_reviews_collection', array('SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager', 'import_reviews_collection'));


		add_action('wp_ajax_sbr_clear_error_logs', array('SmashBalloon\Reviews\Common\Helpers\SBR_Error_Handler', 'clear_all_error_ajax'));


	}

	/**
	 * Used in an AJAX call to update settings for a particular feed.
	 * Can also be used to create a new feed if no feed_id sent in
	 * $_POST data.
	 *
	 * @since 1.0
	 */
	public static function builder_update()
	{

		check_ajax_referer('sbr-admin', 'nonce');

		if (! sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		$settings_data = $_POST;

		$feed_id = false;
		$is_new_feed = isset($settings_data['new_insert']) ? true : false;
		if (!empty($settings_data['feed_id'])) {
			$feed_id = sanitize_text_field($settings_data['feed_id']);
			unset($settings_data['feed_id']);
		} elseif (isset($settings_data['feed_id'])) {
			unset($settings_data['feed_id']);
		}
		unset($settings_data['action']);
		unset($settings_data['nonce']);

		if (!isset($settings_data['feed_name'])) {
			$settings_data['feed_name'] = '';
		}

		$update_feed = isset($settings_data['update_feed']) ? true : false;
		unset($settings_data['update_feed']);

		$settings_data = self::sanitize_settings($settings_data);

		//Check if New
		if ($is_new_feed) {
			$settings_data['sources'] = json_decode(stripslashes($settings_data['sources']));
			$is_collection = false;
			foreach ($settings_data['sources'] as $s_source) {
				if (!is_array($s_source) && strpos($s_source, 'collection') !== false) {
					$is_collection = true;
				}
			}
			if ($is_collection) {
				$settings_data['filterCharCountMin'] = 0;
			}

			$new_name = self::create_feed_name(stripslashes($settings_data['feed_name']));
			$settings_data['feed_name'] = !empty($new_name) ? $new_name : 'Reviews Feed';
			$settings_data = SBR_Feed_Saver_Manager::get_feed_settings_by_feed_templates($settings_data);
		}

		unset($settings_data['new_insert']);
		$feed_name = '';
		$feed_style = '';
		if ($update_feed) {
			$feed_name = stripslashes($settings_data['feed_name']);
			$feed_style = stripslashes($settings_data['feed_style']);
			$settings_data = json_decode(stripslashes($settings_data['settings']), true);
		}


		$feed_saver = new SBR_Feed_Saver($feed_id);
		$feed_saver->set_feed_name($feed_name);
		$feed_saver->set_feed_style($feed_style);
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
				$feed_cache = new FeedCache($feed_id);
				if (isset($_POST['get_posts']) && $_POST['get_posts'] == true) {
					$feed = Util::sbr_is_pro() ? new \SmashBalloon\Reviews\Pro\Feed($settings_data, $feed_id, $feed_cache) : new \SmashBalloon\Reviews\Common\Feed($settings_data, $feed_id, $feed_cache);
					$feed->init();
					$feed->get_set_cache();
					$posts = $feed->get_post_set_page();
					$return['posts'] = $posts;
				}
			}
		}
		echo wp_json_encode($return);
		wp_die();
	}


	/**
	 * Create Feed Name
	 * This will create the feed name when creating new Feeds
	 *
	 * @since 1.0
	 */
	public static function create_feed_name($feed_name)
	{
		return DB::feeds_query_name($feed_name);
	}

	/**
	 * Used in an AJAX call to delete feeds from the Database
	 * $_POST data.
	 *
	 * @since 1.0
	 */
	public static function delete_feed()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		$feeds_id = json_decode(stripslashes($_POST['feeds_ids']));
		if (! empty($feeds_id) && is_array($feeds_id)) {
			$feeds_id = array_map('absint', $feeds_id);
			DB::delete_feeds_query($feeds_id);
		}
	}

	/**
	 * Used in an AJAX call to duplicate a Feed
	 * $_POST data.
	 *
	 * @since 1.0
	 */
	public static function duplicate_feed()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (! empty($_POST['feed_id'])) {
			$feed_id = absint($_POST['feed_id']);
			DB::duplicate_feed_query($feed_id);
		}
	}

	/**
	 * Used to retrieve Feed Posts for preview screen
	 * Returns Feed info or false!
	 *
	 *
	 *
	 * @since 1.0
	 */
	public static function feed_customizer_fly_preview()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		if (isset($_POST['feedID']) && isset($_POST['previewSettings'])) {
			$return = [
				'posts' => []
			];
			$preview_settings = json_decode(stripslashes($_POST['previewSettings']), true);
			$preview_settings = self::sanitize_settings($preview_settings);

			$feed_id = absint($_POST['feedID']);
			$feed_cache = new FeedCache($feed_id, 30000);
			$feed_cache->clear('posts');
			$feed_cache->clear('header');
			$feed = Util::sbr_is_pro() ? new \SmashBalloon\Reviews\Pro\Feed($preview_settings, $feed_id, $feed_cache) : new \SmashBalloon\Reviews\Common\Feed($preview_settings, $feed_id, $feed_cache);
			$feed->init();

			$feed->get_set_cache();
			$posts            = $feed->get_post_set_page();

			$feed_id          = absint($_POST['feedID']);
			$feed_name        = sanitize_text_field(wp_unslash($_POST['feedName']));

			if (isset($posts['data'])) {
				$posts = $posts['data'];
			}

			if (isset($preview_settings['sortRandomEnabled']) && $preview_settings['sortRandomEnabled'] === true) {
				shuffle($posts);
			}

			// Array of Included Stars $preview_settings['includedStarFilters']


			$feed_saver = new SBR_Feed_Saver($feed_id);
			$feed_saver->set_feed_name($feed_name);
			$feed_saver->set_data($preview_settings);
			// Update feed settings depending on feed templates
			if (isset($_POST['isFeedTemplatesPopup'])) {
				$preview_settings = SBR_Feed_Saver_Manager::get_feed_settings_by_feed_templates($preview_settings);
				$return['settings'] = $preview_settings;
			}
			$return['posts'] = $posts;
			$return['sourcesList'] = SBR_Sources::get_sources_list([
				'id' => !empty($preview_settings['sources']) && isset($preview_settings['sources']) ? $preview_settings['sources'] : [],
			]);

			echo sbr_json_encode(
				$return
			);
		}
		wp_die();
	}

	/**
	 * Used to Start the Moderation Mode & retrieve Reviews
	 *
	 *
	 *
	 * @since 1.0
	 */
	public static function start_moderation_mode()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		if (isset($_POST['feedID']) && isset($_POST['previewSettings'])) {
			$preview_settings = json_decode(stripslashes($_POST['previewSettings']), true);
			$preview_settings = self::sanitize_settings($preview_settings);
			$feed_id = absint($_POST['feedID']);

			$preview_settings['numPostDesktop'] = 100;
			$preview_settings['numPostTablet'] = 100;
			$preview_settings['numPostMobile'] = 100;

			$feed = Util::sbr_is_pro() ? new \SmashBalloon\Reviews\Pro\Feed($preview_settings, $feed_id, new FeedCache($feed_id, 30000)) : new \SmashBalloon\Reviews\Common\Feed($preview_settings, $feed_id, new FeedCache($feed_id, 30000));
			$feed->init();
			$posts = $feed->get_posts_for_moderation();

			echo sbr_json_encode(
				[
					'posts' => $posts
				]
			);
		}
		wp_die();
	}


	/**
	* Used to Add Sources
	*
	*
	*
	* @since 1.0
	*/
	public static function add_source()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		$return = [];

		if (isset($_POST['provider']) && isset($_POST['providerIdUrl'])) {
			if (isset($_POST['apiKey']) && !empty($_POST['apiKey']) && $_POST['apiKey'] !== null) {
				$data = array(
					'provider' => sanitize_text_field($_POST['provider']),
					'providerIdUrl' => sanitize_text_field($_POST['providerIdUrl']),
					'apiKey' => sanitize_text_field($_POST['apiKey']),
				);
				$return = self::process_source_apikey($data);
			}
			$return['freeRetrieverData'] = Util::get_free_retriever_data();
			echo sbr_json_encode(
				$return
			);
		}
		wp_die();
	}

	/**
	* Used to Add Facebook Sources
	*
	*
	*
	* @since 1.0
	*/
	public static function add_facebook_souce()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['selectedFacebookPages']) && !empty($_POST['selectedFacebookPages'])) {
			$selected_facebook_pages = json_decode(stripslashes($_POST['selectedFacebookPages']), true);
			$encryption = new Data_Encryption();
			$reporter = new Error_Reporter();
			$reporter->remove_error('platform_data_deleted');
			$reporter->reset_api_errors();

			foreach ($selected_facebook_pages as $page) {
				$page['access_token'] = $encryption->maybe_encrypt($page['access_token']);
				SBR_Sources::update_or_insert($page);
			}
		}

		echo sbr_json_encode(
			[
				'sourcesCount' => SBR_Sources::get_sources_count(),
				'sourcesList' => SBR_Sources::get_sources_list()
			]
		);
		wp_die();
	}

	/**
	* Used to Add Manual Facebook Source
	*
	*
	*
	* @since 1.0
	*/
	public static function add_manual_facebook_souce()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (
			isset($_POST['pageType']) && ! empty($_POST['pageType']) &&
			isset($_POST['pageId']) && ! empty($_POST['pageId']) &&
			isset($_POST['pageToken']) && ! empty($_POST['pageToken'])
		) {
			$encryption = new Data_Encryption();
			$access_token = $_POST['pageToken'];
			\SmashBalloon\Reviews\Pro\Integrations\Providers\Facebook::connect_manual_source($_POST['pageId'], $access_token);
		}
		wp_die();
	}

	/**
	* Used to Add Sources
	*
	*
	*
	* @since 1.0
	*/
	public static function delete_souce()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['sourceID'], $_POST['sourceAccountID'], $_POST['sourceProvider'])) {
			$source_id = sanitize_text_field($_POST['sourceID']);
			$source_account_id = sanitize_text_field($_POST['sourceAccountID']);
			$provider = sanitize_text_field($_POST['sourceProvider']);

			SBR_Sources::delete_source($source_id);

			if (isset($_POST['isCollection']) && $_POST['isCollection']) {
				PostAggregator::delete_reviews_by_provide_id($source_account_id);
			} else {
				$relay = new SBRelay(new SettingsManagerService());
				$relay_args = [
					'place_id' => $source_account_id
				];
				switch ($provider) {
					case 'google':
						$google = new Google($relay);
						$info = $google->removeSource($relay_args);
						break;
					case 'yelp':
						$yelp = new Yelp($relay);
						$info = $yelp->removeSource($relay_args);
						break;
					case 'tripadvisor':
						$tripadvisor = new \SmashBalloon\Reviews\Pro\Integrations\Providers\TripAdvisor($relay);
						$info = $tripadvisor->removeSource($relay_args);
						break;
					case 'trustpilot':
						$trustpilot = new \SmashBalloon\Reviews\Pro\Integrations\Providers\TrustPilot($relay);
						$info = $trustpilot->removeSource($relay_args);
						break;
					case 'wordpress.org':
						$WordpressOrg = new \SmashBalloon\Reviews\Pro\Integrations\Providers\WordpressOrg($relay);
						$info = $WordpressOrg->removeSource($relay_args);
						break;
				}
			}
		}
		echo sbr_json_encode([
			'sourcesCount' => SBR_Sources::get_sources_count(),
			'sourcesList' => SBR_Sources::get_sources_list(),
			'freeRetrieverData'     => Util::get_free_retriever_data()
		]);
		wp_die();
	}



	/**
	 * import feed Settings
	 *
	 *
	 * @since 1.0
	 */
	public static function import_feed_settings()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}


		$filename = $_FILES['feedFile']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ('json' !== $ext) {
			wp_send_json_error(['message' => __('JSON file needed. Your file is not in the correct format.', 'reviews-feed')]);
		}

		$imported_settings = file_get_contents($_FILES['feedFile']['tmp_name']);
		if (empty($imported_settings)) {
			wp_send_json_error(["message" => __("Don't have file, Please try again", "reviews-feed")]);
		}

		$feed_return = SBR_Feed_Saver_Manager::import_feed($imported_settings);
		wp_send_json($feed_return, 200);

		wp_die();
	}

	public static function check_api_limit($provider)
	{
		if (in_array($provider, ['trustpilot', 'wordpress.org'])) {
			return false;
		}
		$apikeys_limit = get_option('sbr_apikeys_limit', []);
		return is_array($apikeys_limit) && in_array($provider, $apikeys_limit);
	}


	public static function check_provider_apikey($provider)
	{
		$apikeys = get_option('sbr_apikeys', []);
		return is_array($apikeys) && isset($apikeys[$provider]) && !empty($apikeys[$provider]);
	}

	public static function limit_provider_api_calls($provider, $provider_id)
	{
		$retriever = Util::sbr_is_pro()
			? new \SmashBalloon\Reviews\Pro\Utils\FreeRetriever()
			: new \SmashBalloon\Reviews\Common\Utils\FreeRetriever();
		return !$retriever->check_api_call($provider, $provider_id);
	}


	public static function update_api_limit($provider, $action)
	{
		$apikeys_limit = get_option('sbr_apikeys_limit', []);
		if ($action === 'add') {
			if (!in_array($provider, $apikeys_limit, true)) {
				array_push($apikeys_limit, $provider);
			}
		}
		if ($action === 'delete') {
			if (in_array($provider, $apikeys_limit)) {
				$apikeys_limit = array_diff($apikeys_limit, [$provider]);
			}
		}
		update_option('sbr_apikeys_limit', $apikeys_limit);
	}

	/**
	 * Add Or Update API Key
	 * for a value
	 *
	 * @param string $provider
	 * @param string $apikey
	 *
	 *
	 * @since 1.0
	 */
	public static function process_source_apikey($data)
	{
		$key_name = sanitize_key($data['provider']) . '_api_key';
		$provider = isset($data['provider']) ? $data['provider'] : false;
		$api_keys = get_option('sbr_apikeys', []);
		$relay_args = [];
		$return = [];

		if ($provider !== false) {
			$providers_bulk = sbr_get_bulk_providers();
			//If there is no providerIdUrl set, then the call is just for checking the API KEY
			switch ($provider) {
				case 'tripadvisor':
					$relay_args['place_id'] = isset($data['providerIdUrl']) ? self::get_place_id_tripadvisor($data['providerIdUrl']) : 'XXX';
					break;
				case 'trustpilot':
					$relay_args['place_id'] = $data['providerIdUrl'];
					break;
				case 'wordpress.org':
						$wordpressorg_args = self::get_place_id_wordpressorg($data['providerIdUrl']);
						$relay_args['place_id'] = $wordpressorg_args['type'] . '/' . $wordpressorg_args['slug'];
						$relay_args['type'] = $wordpressorg_args['type'];
						$relay_args['slug'] = $wordpressorg_args['slug'];
					break;
				default:
					$relay_args['place_id'] = isset($data['providerIdUrl']) ? self::get_place_id($data['providerIdUrl']) : 'XXX';
					$relay_args['check_api_key'] = true;
					break;
			}

			//Check if we need to add the API Key
			if (isset($data['apiKey']) && !empty($data['apiKey']) && !is_null($data['apiKey']) && $data['apiKey'] !== 'null') {
				$relay_args['api_key'] = $data['apiKey'];
			} elseif (isset($api_keys[$provider]) && !empty($api_keys[$provider]) && $api_keys[$provider] !== null) {
				$relay_args['api_key'] = $api_keys[$provider];
			}

			if (!isset($relay_args['api_key']) || empty($relay_args['api_key']) || $relay_args['api_key'] === null || $relay_args['api_key'] === 'null') {
				unset($relay_args['api_key']);
			}

			//This Means that the users tries to get a new source
			//else tries to add API Key to be checked
			if (SBR_Feed_Saver_Manager::check_api_limit($provider) && $relay_args['place_id'] !== 'XXX' && (empty($relay_args['api_key']) || is_null($relay_args['api_key']))) {
				return [
					'apiKeyLimits' => get_option('sbr_apikeys_limit', []),
					'pluginNotices' => Util::get_plugin_notices(),
					'error' => 'sourceLimitExceeded'
				];
			}
			$relay = new SBRelay(new SettingsManagerService());
			$info = false;
			switch ($provider) {
				case 'google':
					$google = new Google($relay);
					$settings = wp_parse_args(get_option('sbr_settings', []), sbr_plugin_settings_defaults());
					$relay_args['language'] = $settings['localization'];
					$info = $google->getSourcesInfo($relay_args);
					break;
				case 'yelp':
					$yelp = new Yelp($relay);
					$info = $yelp->getSourcesInfo($relay_args);
					break;
				case 'tripadvisor':
					$tripadvisor = new \SmashBalloon\Reviews\Pro\Integrations\Providers\TripAdvisor($relay);
					$info = $tripadvisor->getSourcesInfo($relay_args);
					break;
				case 'trustpilot':
					$trustpilot = new \SmashBalloon\Reviews\Pro\Integrations\Providers\TrustPilot($relay);
					$info = $trustpilot->getSourcesInfo($relay_args);
					break;
				case 'wordpress.org':
					$wordpressorg = new \SmashBalloon\Reviews\Pro\Integrations\Providers\WordpressOrg($relay);
					$info = $wordpressorg->getSourcesInfo($relay_args);
					break;
			}
			if ($info !== false) {
				$info = json_decode($info, true);
				//Checks if there is an error
				if (!empty($info['error'])
					|| (isset($info['success']) && false === $info['success'])
				) {
					if (!empty($info['error'])
						&& $info['error'] === 'sourceLimitExceeded'
					) {
						SBR_Feed_Saver_Manager::update_api_limit($provider, 'add');
					}
					$info['apiKeyLimits'] = get_option('sbr_apikeys_limit', []);
					$info['pluginNotices'] = Util::get_plugin_notices();
					return $info;
				}
				/**
				 * This check for valid API key
				 * When valid the API response may return the source info
				 * OR
				 * Return an error which if it's invalid location
				 */
				$checkValidKey = (
						!empty($info['info']['error']) && $info['info']['error'] !== 'invalidKey'
					)
					|| !empty($info['info']['id'])
					|| !empty($info['info']['successId']);


				if (isset($data['apiKey']) && $data['apiKey'] !== null && $data['apiKey'] !== 'null' && $checkValidKey) {
					self::update_provider_apikey($provider, $data['apiKey']);

					self::check_google_api_type($provider, $info);

					$return['apikey'] = 'valid';
					SBR_Feed_Saver_Manager::update_api_limit($provider, 'delete');
					$info['apiKeyLimits'] = get_option('sbr_apikeys_limit', []);
				}

				if (isset($info['info']['error'])) {
					if ($info['info']['error'] === 'invalidKey') {
						$return['apikey'] = 'invalid';
					}
					if ($info['info']['error'] === 'invalidLocation') {
						$return['placeId'] = 'invalid';
					}
				}
				if (isset($info['info']['id'])) {
					$info['info']['provider'] = $provider;

					$info['info']['name'] = htmlspecialchars_decode(html_entity_decode($info['info']['name'], ENT_QUOTES | ENT_HTML5), ENT_QUOTES | ENT_HTML5);

					if (isset($info['info']['reviews'])) {
						$reviews_list = $info['info']['reviews'];
						unset($info['info']['reviews']);
						self::cache_single_posts_from_set($reviews_list, $info['info']);
					}
					SBR_Sources::update_or_insert($info['info']);
					$return['message'] = 'addedSource';
					$return['newAddedSource'] = $info['info'];
					SBR_Feed_Saver_Manager::update_api_limit($provider, 'delete');

					if (
						Util::sbr_is_pro()
						&& in_array($provider, $providers_bulk)
						&& !empty($data['apiKey'])
						&& $data['apiKey'] !== "null"
					) {
						$bulk_reviews = new \SmashBalloon\Reviews\Pro\Services\BulkUpdate\Bulk_Reviews_Update();
						$bulk_reviews->schedule_task([
							'source_account_id' => $info['info']['id'],
							'source_provider'	=> $provider
						]);
						if ($bulk_reviews->check_account_option($info['info']['id'])) {
							$return['bulkStarted']      = true;
						}
					}
				}
				$return['apiKeys']      = get_option('sbr_apikeys', []);
				$return['sourcesList']  = SBR_Sources::get_sources_list();
			}
		}

		$return['apiKeyLimits'] = get_option('sbr_apikeys_limit', []);
		$return['pluginNotices'] = Util::get_plugin_notices();
		$return['sourcesCount'] = SBR_Sources::get_sources_count();

		return $return;
	}

	public static function update_api_key()
	{
		if (! sbr_current_user_can('manage_reviews_feed_options') || ! check_ajax_referer('sbr-admin', 'nonce', false)) {
			wp_send_json_error([
				"error" => "Unauthorized Access",
				"message" => "You are not allowed to perform this action."
			]);
		}
		$return = [];
		if (isset($_POST['provider']) && isset($_POST['apiKey'])) {
			$provider = sanitize_text_field($_POST['provider']);
			$apiKey = sanitize_text_field($_POST['apiKey']);

			//Delete API Key from Plugin
			if (!empty($_POST['removeApiKey'])
				&& $_POST['removeApiKey'] === "true"
			) {
				self::update_provider_apikey($provider, null);
				$return = [
					'apiKeys' => get_option('sbr_apikeys', []),
					'apikey'  => 'deleted'
				];
			} else {
				$data = array(
					'provider' => $provider,
					'apiKey' => $apiKey
				);
				$return = self::process_source_apikey($data);
			}
		}
		$return['freeRetrieverData'] = Util::get_free_retriever_data();
		echo sbr_json_encode(
			$return
		);
		wp_die();
	}

	/**
	 * Summary of check_google_api_type
	 *
	 * @param mixed $provider
	 * @param mixed $info
	 *
	 * @return void
	 */
	public static function check_google_api_type($provider, $info)
	{
		if (
			$provider === 'google'
			&& !empty($info['info']['api_type'])
		) {
			self::update_provider_apikey('googleApiType', $info['info']['api_type']);
		}
	}

	/**
	* Add Or Update API Key
	* for a value
	*
	* @param string $provider
	* @param string $apikey
	*
	*
	* @since 1.0
	*/
	public static function update_provider_apikey($provider, $apikey)
	{
		$api_keys = get_option('sbr_apikeys', []);
		if ($apikey !== null && $apikey !== 'null') {
			$api_keys = !is_array($api_keys) ? [] : $api_keys;
			$api_keys[$provider] = $apikey;
		}
		if (empty($apikey) && isset($api_keys[$provider])) {
			unset($api_keys[$provider]);
			if ($provider === 'google') {
				unset($api_keys['googleApiType']);
			}
		}

		update_option('sbr_apikeys', $api_keys);
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
	 * @since 1.0
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
	 * @param int|string $value
	 *
	 * @return  boolean
	 *
	 * @since 1.0
	 */
	public static function is_boolean($value)
	{
		return ($value === 'true' || $value === 'false' || is_bool($value)) ? true : false;
	}

	/**
	 * Get Place or Page ID from URL
	 *
	 * @param string $place_url
	 *
	 * @return string
	 *
	 * @since 1.0
	*/
	public static function get_place_id($place_url)
	{
		$res = explode('/', $place_url);
		return end($res);
	}

	public static function get_place_id_tripadvisor($place_url)
	{
		// probably a place ID
		if ((bool) filter_var($place_url, FILTER_VALIDATE_URL) === false) {
			return $place_url;
		}

		$broken_up = explode('/', $place_url);

		foreach ($broken_up as $url_piece) {
			if (strpos($url_piece, '.html') !== false) {
				preg_match('/-d+\d{0,10}-/i', $url_piece, $matches, PREG_OFFSET_CAPTURE);
				if (! empty($matches[0])) {
					return str_replace(array('-', 'd'), '', $matches[0][0]);
				}
			}
		}
		return $place_url;
	}

	//WordPress Org Theme/Plugin Source
	public static function get_place_id_wordpressorg($place_url)
	{
		$broken_up = explode(
			'/',
			parse_url(
				trim($place_url, '/'),
				PHP_URL_PATH
			)
		);

		$slug = $broken_up[count($broken_up) - 1];
		$type = strpos($place_url, 'theme') !== false ? 'theme' : 'plugin';
		return [
			'type' => $type,
			'slug' => $slug
		];
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
	 * @param string $type
	 * @param int|string $value
	 *
	 * @return int|string
	 *
	 * @since 1.0
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
				$return = sanitize_text_field($value);
				break;
		}

		return $return;
	}

	public static function import_feed($json)
	{
		$feed_json = json_decode($json, true);
		$return = ['success' => false];

		if (empty($feed_json['sourcesList'])) {
			$return['message'] = __('No feed source is included. Cannot upload feed.', 'reviews-feed');
			return $return;
		}

		$settings_data = $feed_json['settings'];

		$feed_saver = new SBR_Feed_Saver(false);
		$feed_saver->set_data($settings_data);

		if ($feed_saver->update_or_insert()) {
			$return = array(
				'success' => true,
				'message' => __('Feed settings imported successfully.', 'reviews-feed'),
				'feed_id' => $feed_saver->get_feed_id(),
			);
			return $return;
		} else {
			$return['message'] = __('Could not import feed. Please try again', 'reviews-feed');
		}
		return $return;
	}


	/**
	 * Clear All Feeds Caches
	 *
	 * @return int|string
	 *
	 * @since 1.0
	 */
	public static function clear_all_caches()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		self::clear_plugin_cache();

		echo wp_json_encode([
			'success' => true
		]);

		wp_die();
	}

	/**
	 * Clear All Plugin Caches
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public static function clear_plugin_cache()
	{
		global $wpdb;

		$cache_table_name = $wpdb->prefix . 'sbr_feed_caches';

		$sql = "
		UPDATE $cache_table_name
		SET cache_value = ''
		WHERE cache_key NOT IN ('posts_backup', 'header_backup');";
		$wpdb->query($sql);

		$posts_table_name = $wpdb->prefix . 'sbr_reviews_posts';

		$sql_posts = "
		UPDATE $posts_table_name
		SET images_done = 0";
		$wpdb->query($sql_posts);

		$table_name = $wpdb->prefix . "options";
		$wpdb->query("
			DELETE
			FROM $table_name
			WHERE `option_name` LIKE ('%\_transient\_sbr\_%')
			");
		$wpdb->query("
			DELETE
			FROM $table_name
			WHERE `option_name` LIKE ('%\_transient\_sbr\_ej\_%')
			");
		$wpdb->query("
			DELETE
			FROM $table_name
			WHERE `option_name` LIKE ('%\_transient\_sbr\_tle\_%')
			");
		$wpdb->query("
			DELETE
			FROM $table_name
			WHERE `option_name` LIKE ('%\_transient\_sbr\_album\_%')
			");
		$wpdb->query("
			DELETE
			FROM $table_name
			WHERE `option_name` LIKE ('%\_transient\_timeout\_sbr\_%')
			");

		//Clear cache of major caching plugins
		if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
			$GLOBALS['wp_fastest_cache']->deleteCache();
		}
		//WP Super Cache
		if (function_exists('wp_cache_clear_cache')) {
			wp_cache_clear_cache();
		}
		//W3 Total Cache
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

	}

	public static function sanitize_settings($raw_settings)
	{
		$sanitized_values = array();
		foreach ($raw_settings as $key => $value) {
			if (is_bool($value)) {
				// already a safe value
				$sanitized_values[$key] = $value;
			} elseif (is_int($value)) {
				// already a safe value
				$sanitized_values[$key] = $value;
			} elseif (is_array($value)) {
				if (empty($value)) {
					$sanitized_values[$key] = array();
				} else {
					foreach ($value as $key2 => $item) {
						if (is_bool($item)) {
							// already a safe value
							$sanitized_values[$key][$key2] = $item;
						} elseif (is_int($item)) {
							// already a safe value
							$sanitized_values[$key][$key2] = $item;
						} else {
							$sanitized_values[$key][$key2] = sanitize_text_field($item);
						}
					}
				}
			} else {
				$sanitized_values[$key] = sanitize_text_field($value);
			}
		}

		return $sanitized_values;
	}


	/**
	 * Get a list of feeds with a limit and offset like a page
	 *
	 * @since 1..1
	 */
	public static function get_feed_list_page()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		$args = array('page' => (int)sanitize_text_field(wp_unslash($_POST['page'])));
		$feeds_data = DB::get_feeds_list($args);

		echo wp_json_encode($feeds_data);
		wp_die();
	}

	 /**
	 * Cache Single Posts form Posts List
	 *
	 * @since 1..1
	 */
	public static function cache_single_posts_from_set($posts, $provider)
	{
		$settings = wp_parse_args(get_option('sbr_settings', []), sbr_plugin_settings_defaults());
		$lang = $settings['localization'];
		$providers_no_media = sbr_get_no_media_providers();
		$providers_lang = sbr_get_lang_providers();


		foreach ($posts as $single_review) {
			$single_review['source'] = $provider;
			$single_post_cache = Util::sbr_is_pro() ?
								new \SmashBalloon\Reviews\Pro\SinglePostCache($single_review, new \SmashBalloon\Reviews\Pro\MediaFinder($single_review['source'])) :
								new \SmashBalloon\Reviews\Common\SinglePostCache($single_review);

			$single_post_cache->set_provider_id($provider['id']);

			if (in_array($provider['provider'], $providers_lang, true)) {
				$single_post_cache->set_lang($lang);
			}

			$single_post_cache->check_api_media();

			if (! $single_post_cache->db_record_exists()) {
				$single_post_cache->resize_avatar(150);
				if (in_array($provider['provider'], $providers_no_media, true)) {
					$single_post_cache->set_storage_data('images_done', 1);
				}
				$single_post_cache->store();
			} else {
				$single_post_cache->update_single();
			}
		}
	}




	/**
	 * Create Or Update Collection Review
	 *
	 * @since 1.3
	 */
	public static function create_new_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');

		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['collection_name']) && !empty($_POST['collection_name'])) {
			$id = 'collection-' . time() . wp_rand();
			$collection_name = sanitize_text_field($_POST['collection_name']);
			$collection_info = [
				'id'    => $id,
				'account_id'    => $id,
				'name'          => $collection_name,
				'provider'      => 'collection',
				'rating'        =>  0,
				'total_rating'  => 0,
			];
			SBR_Sources::update_or_insert($collection_info);
			$collection_info['info'] = $collection_info;
			echo sbr_json_encode(
				[
					'newCollection' => $collection_info,
					'sourcesCount' => SBR_Sources::get_sources_count(),
					'sourcesList'   => SBR_Sources::get_sources_list()
				]
			);
		}

		wp_die();
	}

	/**
	 * Create Or Update Collection Review
	 *
	 * @since 1.3
	 */
	public static function create_update_collection_review()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		$review = json_decode(stripslashes($_POST['reviewContent']), true);

		if (!isset($review['provider_id']) || empty($review['provider_id'])) {
			wp_send_json_error(
				[
					"message" => __("Please make sure the collection exists", "reviews-feed")
				]
			);
		}
		$is_duplicate 	= isset($_POST['is_duplicate']) && $_POST['is_duplicate'];
		$is_new 		= (isset($review['new']) && $review['new'] === true) || $is_duplicate;
		$provider_id = sanitize_text_field($review['provider_id']);
		$page_number = absint($_POST['page_number']);
		$review_id 	= $is_new ? $provider_id . time() . wp_rand() : sanitize_text_field($review['review_id']);
		$time = isset($review['time']) ? sanitize_text_field($review['time']) : '';
		$sanitized_review = [
			'time' 			=> empty($time) ? time() : (($is_new && !$is_duplicate) || !is_numeric($time) ? strtotime($time) : $time),
			'rating' 		=> absint($review['rating']),
			'provider_id' 	=> $provider_id,
			'review_id'		=> $review_id,
			'text' 			=> sanitize_text_field($review['text']),
			'title' 		=> sanitize_text_field($review['title']),
			'reviewer'		=> [
				'name' 			=> sanitize_text_field($review['reviewer']['first_name']) . ' ' . sanitize_text_field($review['reviewer']['last_name']),
				'first_name' 	=> sanitize_text_field($review['reviewer']['first_name']),
				'last_name' 	=> sanitize_text_field($review['reviewer']['last_name']),
				'avatar' 		=> sanitize_text_field($review['reviewer']['avatar'])
			],
			'provider'		=> [
				'name' 	=> sanitize_text_field($review['provider']['name'])
			],
			'source'		=> [
				'id' 	=> $provider_id,
				'url' 	=> sanitize_text_field($review['source']['url'])
			]
		];

		if (isset($review['media']) && is_array($review['media'])) {
			$sanitized_review['media'] = $review['media'];
		}
		//JSON Data
		$sanitized_review['json_data'] = $sanitized_review;
		$single_post_cache = new \SmashBalloon\Reviews\Pro\SinglePostCache($sanitized_review, new \SmashBalloon\Reviews\Pro\MediaFinder($sanitized_review['source']));
		$single_post_cache->set_provider_id($provider_id);


		if ($is_new) {
			$single_post_cache->store();
		} else {
			$single_post_cache->update_single();
		}

		if (isset($sanitized_review['media'])) {
			$aggregator = new PostAggregator();
			$single_post_cache->resize_images(array(640, 150));
			$single_post_data = $single_post_cache->get_post_data();
			$single_post_storage_data = $single_post_cache->get_storage_data();
			$single_post_data = $aggregator->add_local_image_urls($single_post_data, $single_post_storage_data);
			$single_post_cache->update(
				[
					array('images_done', 1, '%d'),
					array('json_data', wp_json_encode($single_post_data), '%s')
				]
			);
		}



		SBR_Sources::update_collection_ratings($provider_id);
		echo sbr_json_encode(
			[
				'postsList'   => PostAggregator::get_source_posts_list($provider_id, $page_number)
			]
		);

		wp_die();
	}

	/**
	 * Update Collection Name
	 *
	 * @since 1.3
	 */
	public static function update_collection_name()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (
				isset($_POST['id'], $_POST['collection_name'], $_POST['provider_id'], $_POST['provider'])
				&& !empty($_POST['collection_name'])
				&& !empty($_POST['provider_id'])
				&& !empty($_POST['provider'])
				&& !empty($_POST['id'])
		) {
			$id = sanitize_text_field($_POST['id']);
			$provider = sanitize_text_field($_POST['provider']);
			$provider_id = sanitize_text_field($_POST['provider_id']);
			$collection_name = sanitize_text_field($_POST['collection_name']);

			$collection_info_search = [
				'id'    		=> $provider_id,
				'provider' 		=> $provider
			];
			$db = new DB();
			$results = $db->get_single_source($collection_info_search);
			if (sizeof($results) === 0) {
				wp_send_json_error(
					[
						"message" => __("Please make sure the collection exists", "reviews-feed")
					]
				);
			}
			$collection_db = $results[0];
			$collection_db['name'] = $collection_name;
			unset($collection_db['info']);
			$collection_db['last_updated'] = date('Y-m-d H:i:s');
			$collection_db['id'] = $provider_id;
			$collection_db['info'] = wp_json_encode($collection_db);
			SBR_Sources::update($collection_db);

			echo sbr_json_encode(
				[
					'newCollectionData' => $collection_db,
					'sourcesCount' => SBR_Sources::get_sources_count(),
					'sourcesList'   => SBR_Sources::get_sources_list()
				]
			);
		}
		wp_die();
	}


	/**
	 * Update Source Provider Posts List
	 *
	 * @since 1.4
	 */
	public static function get_source_posts()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (
			isset($_POST['provider_id'], $_POST['provider'], $_POST['page_number'])
			&& !empty($_POST['provider_id'])
			&& !empty($_POST['provider'])
			&& !empty($_POST['page_number'])
		) {
			$provider = sanitize_text_field($_POST['provider']);
			$provider_id = sanitize_text_field($_POST['provider_id']);
			$page_number = absint($_POST['page_number']);
			echo sbr_json_encode(
				[
					'postsList'   => PostAggregator::get_source_posts_list($provider_id, $page_number)
				]
			);
		}
		wp_die();
	}


	/**
	 * Delete Review from a Collection
	 *
	 * @since 1.4
	 */
	public static function delete_review_from_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		if (
			isset($_POST['review_id'], $_POST['provider_id'], $_POST['provider'], $_POST['page_number'])
			&& !empty($_POST['review_id'])
			&& !empty($_POST['provider_id'])
			&& !empty($_POST['provider'])
			&& !empty($_POST['page_number'])
		) {
			$provider = sanitize_text_field($_POST['provider']);
			$review_id = sanitize_text_field($_POST['review_id']);
			$provider_id = sanitize_text_field($_POST['provider_id']);
			$page_number = absint($_POST['page_number']);

			PostAggregator::delete_review($provider_id, $review_id, $provider);
			SBR_Sources::update_collection_ratings($provider_id);

			echo sbr_json_encode(
				[
					'postsList'   => PostAggregator::get_source_posts_list($provider_id, $page_number)
				]
			);
		}
		wp_die();
	}

	/**
	 * Add Multiple Selected Reviews to a Collection
	 *
	 * @since 1.4
	 */
	public static function add_multiple_reviews_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		if (
			isset($_POST['provider_id'], $_POST['selected_reviews'])
			&& !empty($_POST['provider_id'])
			&& !empty($_POST['selected_reviews'])
		) {
			$provider_id = sanitize_text_field($_POST['provider_id']);
			$selected_reviews = json_decode(stripslashes($_POST['selected_reviews']));
			PostAggregator::insert_multiple_reviews($provider_id, $selected_reviews);

			echo sbr_json_encode(
				[
					'postsList'   => PostAggregator::get_source_posts_list($provider_id, 1)
				]
			);
			wp_die();
		}
	}

	/**
	 * Advanced Reviews Search
	 *
	 * @since 1.4
	 */
	public static function advanced_search_reviews()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		if (
			isset($_POST['provider_id'], $_POST['search_text'])
			&& !empty($_POST['provider_id'])
			&& !empty($_POST['search_text'])
		) {
			$provider_id = sanitize_text_field($_POST['provider_id']);
			$search_text = sanitize_text_field($_POST['search_text']);

			echo sbr_json_encode(
				[
					'postsList'   => PostAggregator::search_source_posts_list($provider_id, $search_text)
				]
			);
			wp_die();
		}
	}


	/**
	 * Duplicate Collection
	 *
	 * @since 1.4
	 */
	public static function duplicate_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		if (!empty($_POST['collection_id'])) {
			$collection_id = sanitize_text_field($_POST['collection_id']);

			$db = new DB();
			$args = [
				'id' => $collection_id,
				'provider' => 'collection'
			];
			$results = $db->get_single_source($args);
			if (!isset($results[0])) {
				wp_send_json_error(
					[
						'message' => __('Cannot duplicate Collection, something went wrong!', 'reviews-feed')
					]
				);
			}
			$collection = $results[0];
			$collection_info = json_decode($collection['info'], true);


			$id = 'collection-' . time() . wp_rand();
			$collection_insert = [
				'id'   			=> $id,
				'account_id'    => $id,
				'name'          => $collection['name'] . ' (copy)',
				'provider'      => 'collection',
				'rating'        => $collection_info['rating'],
				'total_rating'  => $collection_info['total_rating']
			];


			SBR_Sources::update_or_insert($collection_insert);

			$collection_reviews = PostAggregator::get_source_all_posts($collection_id);

			foreach ($collection_reviews as $s_review) {
				$review_json = json_decode($s_review['json_data'], true);
				$review_id 	= $id . time() . wp_rand();

				$sanitized_review = [
					'time' 			=> absint($review_json['time']),
					'rating' 		=> absint($review_json['rating']),
					'provider_id' 	=> $id,
					'review_id'		=> $review_id,
					'text' 			=> sanitize_text_field($review_json['text']),
					'title' 		=> sanitize_text_field($review_json['title']),
					'reviewer'		=> [
						'name' 			=> sanitize_text_field($review_json['reviewer']['name']),
						'first_name' 	=> isset($review_json['reviewer']['first_name']) ? sanitize_text_field($review_json['reviewer']['first_name']) : '',
						'last_name' 	=> isset($review_json['reviewer']['last_name']) ? sanitize_text_field($review_json['reviewer']['last_name']) : '',
						'avatar' 		=> sanitize_text_field($review_json['reviewer']['avatar'])
					],
					'provider'		=> [
						'name' 	=> sanitize_text_field($review_json['provider']['name'])
					],
					'source'		=> [
						'id' 	=> $id,
						'url' 	=> sanitize_text_field($review_json['source']['url'])
					]
				];

				if (isset($review['media']) && is_array($review_json['media'])) {
					$sanitized_review['media'] = $review_json['media'];
				}
				//JSON Data
				$sanitized_review['json_data'] = $sanitized_review;
				$single_post_cache = new \SmashBalloon\Reviews\Pro\SinglePostCache($sanitized_review, new \SmashBalloon\Reviews\Pro\MediaFinder($sanitized_review['source']));
				$single_post_cache->set_provider_id($id);
				$single_post_cache->store();
				if (isset($sanitized_review['media'])) {
					$aggregator = new PostAggregator();
					$single_post_cache->resize_images(array(640, 150));
					$single_post_data = $single_post_cache->get_post_data();
					$single_post_storage_data = $single_post_cache->get_storage_data();
					$single_post_data = $aggregator->add_local_image_urls($single_post_data, $single_post_storage_data);
					$single_post_cache->update(
						[
							array('images_done', 1, '%d'),
							array('json_data', wp_json_encode($single_post_data), '%s')
						]
					);
				}
			}
			echo sbr_json_encode(
				[
					'sourcesCount' => SBR_Sources::get_sources_count(),
					'sourcesList' => SBR_Sources::get_sources_list()
				]
			);
			wp_die();
		}
	}

	/**
	 * Load More Sources
	 *
	 * @since 1.4
	 */
	public static function load_more_sources()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['sources_page']) && !empty($_POST['sources_page'])) {
			echo sbr_json_encode(
				[
					'sourcesCount' => SBR_Sources::get_sources_count(),
					'sourcesList' => SBR_Sources::get_sources_list(
						[
							'page' => absint($_POST['sources_page'])
						]
					)
				]
			);
		}
		wp_die();
	}

	/**
	 * Export Single Collection Reviews
	 *
	 * @since 1.4
	 */
	public static function export_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (!empty($_POST['collection_id'])) {
			$collection_id = sanitize_text_field($_POST['collection_id']);
			$collection_data = DB::get_collections_reviews($collection_id);
			//Will Return Collection Data + The Reviews List
			echo sbr_json_encode(
				[
					'collection' => $collection_data
				]
			);
		}
		wp_die();
	}


	/**
	 * IMport Full Collection Data
	 *
	 *
	 * @since 1.0
	 */
	public static function import_full_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		$collection_json = self::parse_collection_json();
		$info = json_decode($collection_json['info'], true);

		if (!empty($info)) {
			$collection_info = [
				'id'   			=> sanitize_text_field($collection_json['account_id']),
				'account_id'    => sanitize_text_field($collection_json['account_id']),
				'name'          => sanitize_text_field($collection_json['name']),
				'provider'      => 'collection',
				'rating'        =>  absint($info['rating']),
				'total_rating'  =>  absint($info['total_rating']),
			];
			SBR_Sources::update_or_insert($collection_info);

			foreach ($collection_json['reviewsList'] as $single_review) {
				unset($single_review['id']);
				$single_review = json_decode($single_review['json_data'], true);
				if (!empty($single_review)) {
					$review_store = Util::parse_single_review($single_review, $collection_json['account_id'], $single_review['review_id']);
					$review_store['json_data'] = $review_store;

					$single_post_cache = new \SmashBalloon\Reviews\Pro\SinglePostCache($review_store, new \SmashBalloon\Reviews\Pro\MediaFinder($review_store['source']));
					$single_post_cache->set_provider_id($collection_json['account_id']);
					$single_post_cache->store();
				}
			}
		}

		echo sbr_json_encode(
			[
				'message' => __("Collection imported successfully", "reviews-feed")
			]
		);
		wp_die();
	}


	/**
	 * Import Only reviews to Collection
	 *
	 *
	 * @since 1.0
	 */
	public static function import_reviews_collection()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (!empty($_POST['collection_id'])) {
			$collection_json = self::parse_collection_json();
			$collection_id = sanitize_text_field($_POST['collection_id']);
			foreach ($collection_json['reviewsList'] as $single_review) {
				unset($single_review['id']);
				$single_review = json_decode($single_review['json_data'], true);
				if (!empty($single_review)) {
					$single_review['review_id'] = $collection_id . time() . wp_rand();
					$single_review['retimeview_id'] = time();

					$review_store = Util::parse_single_review($single_review, $collection_id, $single_review['review_id']);
					$review_store['json_data'] = $review_store;

					$single_post_cache = new \SmashBalloon\Reviews\Pro\SinglePostCache($review_store, new \SmashBalloon\Reviews\Pro\MediaFinder($review_store['source']));
					$single_post_cache->set_provider_id($collection_id);
					$single_post_cache->store();
					if (isset($sanitized_review['media'])) {
						$aggregator = new PostAggregator();
						$single_post_cache->resize_images(array(640, 150));
						$single_post_data = $single_post_cache->get_post_data();
						$single_post_storage_data = $single_post_cache->get_storage_data();
						$single_post_data = $aggregator->add_local_image_urls($single_post_data, $single_post_storage_data);
						$single_post_cache->update(
							[
								array('images_done', 1, '%d'),
								array('json_data', wp_json_encode($single_post_data), '%s')
							]
						);
					}
				}
			}

			SBR_Sources::update_collection_ratings($collection_id);
			echo sbr_json_encode(
				[
					'postsList'   => PostAggregator::get_source_posts_list($collection_id, 1)
				]
			);
		}

		wp_die();
	}


	/**
	 * Parse Collections JSON file
	 *
	 *
	 * @since 1.0
	 */
	public static function parse_collection_json()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}

		if (empty($_FILES['feedFile'])) {
			wp_send_json_error(
				[
					'message' => __('JSON file needed. Your file is not in the correct format.', 'reviews-feed')
				]
			);
		}
		$filename = $_FILES['feedFile']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ('json' !== $ext) {
			wp_send_json_error(
				[
					'message' => __('JSON file needed. Your file is not in the correct format.', 'reviews-feed')
				]
			);
		}

		$imported_file = file_get_contents($_FILES['feedFile']['tmp_name']);
		if (empty($imported_file)) {
			wp_send_json_error(
				[
					"message" => __("JSON file needed. Your file is not in the correct format.", "reviews-feed")
				]
			);
		}
		$collection_json = json_decode($imported_file, true);

		if (empty($collection_json['reviewsList'])) {
			wp_send_json_error(
				[
					"message" => __("Something went wrong. This doesn't look like a collections import file.", "reviews-feed")
				]
			);
		}
		return $collection_json;
	}
}
