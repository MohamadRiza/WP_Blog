<?php
namespace CustomFacebookFeed\Integrations;


use CustomFacebookFeed\CFF_Cache;
use CustomFacebookFeed\CFF_Group_Posts;
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Class CFF_Graph_Data
 * Build API cal URLs, Logic to get JSON from cache, backup or API
 * Error Handling
 *
 * @since 4.X
 */
class CFF_Graph_Data
{


	/**
	 * Feed ID
	 *
	 * @var int
	 */
	private $feed_id;

	/**
	 * Page ID
	 *
	 * @var string
	 */
	private $page_id;


	/**
	 * Page IDs used for Multifeed
	 *
	 * @var string
	 */
	private $page_ids;

	/**
	 * Feed Setting
	 *
	 * @var array
	 */
	private $feed_settings;

	/**
	 * Feed Type
	 *
	 * @var string
	 */
	private $feed_type;

	/**
	 * Graph API URL
	 *
	 * @var string
	 */
	private $graph_url;

	/**
	 * Is Multifeed
	 *
	 * @var boolean
	 */
	private $is_multifeed;

	/**
	 * Is Event Feed
	 *
	 * @var boolean
	 */
	private $is_event;

	/**
	 * Is Group Source
	 *
	 * @var boolean
	 */
	private $is_group;

	/**
	 * Is Album Work Around
	 *
	 * @var boolean
	 */
	private $is_album_workaround;

	/**
	 * Cache Seconds
	 *
	 * @var int
	 */
	private $cache_seconds;

	/**
	 * Shortcode Atts
	 *
	 * @var array
	 */
	private $shortcode_atts;


	/**
	 * Post Limit
	 *
	 * @var int
	 */
	private $post_limit;

	/**
	 * Locale
	 *
	 * @var string
	 */
	private $locale;

	/**
	 * Transient Name
	 *
	 * @var string
	 */
	private $transient_name;

	/**
	 * Legacy Feeds
	 *
	 * @var boolean
	 */
	private $is_legacy;

	/**
	 * Access Token
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Next Set Of Posts URL
	 *
	 * @var array
	 */
	private $next_urls_arr_safe;


	/**
	 * Feed Cache
	 *
	 * @var CFF_Cache
	 */
	private $feed_cache;

	/**
	 * Feed Page
	 *
	 * @var int
	 */
	private $feed_page;

	/**
	 * Post JSON Data
	 *
	 * @var object
	 */
	private $posts_json;

	/**
	 * Cache Type
	 *
	 * @var string
	 */
	private $cache_type;

	/**
	 * Cache Page Type
	 *
	 * @var string
	 */
	private $cache_type_page;



	/**
	 * Is Customizer
	 *
	 * @var boolean
	 */
	private $is_customizer;

	/**
	 * Is Customizer
	 *
	 * @var SB_Facebook_Data_Encryption
	 */
	private $encryption;

	/**
	 * Previeous Page Data
	 *
	 * @var array
	 */
	private $prev_page_data;

	/**
	 * CFF_Graph_Data constructor.
	 *
	 * @param int $page_id Source Page/Group ID
	 * @param int $feed_id the Feed ID
	 * @param array $feed_settings list of Feed Settings
	 * @since 5.0
	 */
	public function __construct($page_id, $page_ids, $feed_id, $feed_settings, $data_att_html, $next_urls_arr_safe, $is_customizer = false, $prev_page_data = [])
	{

		$this->page_id = $page_id;
		$this->feed_id = $feed_id;
		$this->feed_settings = $feed_settings;
		$this->is_customizer = $is_customizer;
		$this->next_urls_arr_safe = $next_urls_arr_safe;
		$this->feed_type = $this->feed_settings['feedtype'];
		$this->is_group = $this->feed_settings['pagetype'] === 'group';
		$this->encryption = new SB_Facebook_Data_Encryption();
		$this->page_ids = $page_ids;
		$this->prev_page_data = $prev_page_data;
		$this->locale = !empty($this->feed_settings['locale']) ? $this->feed_settings['locale'] : get_option('cff_locale', 'en_US');
		$this->shortcode_atts = is_array($data_att_html) ? $data_att_html : json_decode(str_replace('&quot;', '"', (isset($data_att_html) ? $data_att_html : '') ), true);
		$this->post_limit = $this->get_post_limit();

		$this->transient_name = $this->get_transient_name();
		$this->generate_feed_id_and_legacy();
		$this->cache_seconds = $this->get_cache_seconds();
		$this->access_token = $this->get_page_access_token();

		$misc_args = [
			'token' => $this->access_token,
			'limit' => $this->post_limit,
			'locale' => $this->locale,
			'data_att_html' => $this->shortcode_atts
		];

		$this->graph_url = CFF_Graph_Url::get_url($this->feed_type, $this->page_id, $this->feed_settings, $misc_args);
		$this->feed_page = isset($this->shortcode_atts['feedPage']) ? $this->shortcode_atts['feedPage'] : 1;
		$this->feed_cache = new CFF_Cache($this->feed_id, $this->feed_page, $this->cache_seconds, $this->is_legacy);
		$this->feed_cache->retrieve_and_set();
		$this->set_cache_type();
	}



	/**
	 * Generates Set the Feed ID and if Is Legacy
	 *
	 * @since 5.0
	 */
	public function generate_feed_id_and_legacy()
	{
		if (!empty($this->shortcode_atts['feed'])) {
			$this->feed_id = intval($this->shortcode_atts['feed']);
			$this->is_legacy = false;
		} else {
			$this->feed_id = $this->transient_name;
			$this->is_legacy = true;
		}

		$this->feed_id = $this->is_customizer ? '*' . $this->feed_id : $this->feed_id;
	}

	/**
	 * Get Page Ids
	 *
	 * @since 5.0
	 */
	public function get_page_ids()
	{
		return [$this->page_id];
	}

	/**
	 * Get Post JSON
	 *
	 * @since 5.0
	 */
	public function get_posts_json()
	{
		return $this->posts_json;
	}

	/**
	 * Set Post JSON
	 *
	 * @var object $posts_json
	 *
	 * @since 5.0
	 */
	public function set_posts_json($posts_json)
	{
		$this->posts_json = $posts_json;
	}

	/**
	 * Set The Graph URL
	 *
	 * @since 5.0
	 */
	public function set_graph_url($graph_url)
	{
		$this->graph_url = $graph_url;
	}

	/**
	 * Get Page Access Token
	 *
	 * @since 5.0
	 */
	public function get_page_access_token()
	{
		$access_token = $this->feed_settings['accesstoken'];
		return $this->encryption->maybe_decrypt($access_token);
	}


	/**
	 * Get Post Limit
	 *
	 * @since 5.0
	 */
	public function get_post_limit()
	{
		$post_limit = $this->feed_settings['limit'];
		$show_posts = isset($this->feed_settings['minnum']) ? $this->feed_settings['minnum'] : $this->feed_settings['num'];
		$show_posts = empty($show_posts) || $show_posts === 0 ? 25 : intval($show_posts);
		if (!isset($post_limit) || empty($post_limit)) {
			switch ($show_posts) {
				case $show_posts >= 50:
					$post_limit = $show_posts + 7;
					break;
				case $show_posts < 50:
					$post_limit = $show_posts + 5;
					break;
				case $show_posts < 25:
					$post_limit = $show_posts + 4;
					break;
				case $show_posts < 10:
					$post_limit = $show_posts + 3;
					break;
				case $show_posts < 6:
					$post_limit = $show_posts + 2;
					break;
				case $show_posts < 2:
					$post_limit = $show_posts + 1;
					break;
			}

			$post_limit = $post_limit > 100 ? 100 : $post_limit;
			$post_limit = $show_posts === '0' || $show_posts === 0 ? 1 : $post_limit;
		}
		$post_limit = $this->feed_settings['timelinepag'] === 'paging' ? $show_posts : $post_limit;
		$post_limit = $this->feed_settings['gridpag'] === 'cursor' ? $show_posts : $post_limit;
		if (isset($this->feed_settings['limit']) || $this->feed_settings['limit'] === '') {
			$post_limit = intval($show_posts);
		}
		return $post_limit;
	}


	/**
	 * Get Feed Transient Name
	 *
	 * @since 5.0
	 */
	public function get_transient_name()
	{
		return $this->build_default_transient_name();
	}

	/**
	 * Build Default Transient Name
	 *
	 * @since 5.0
	 */
	public function build_default_transient_name()
	{
		$page_id_caching = ($this->feed_settings['playlist']) ? $this->feed_settings['playlist'] : $this->page_id;
		$trans_items_arr = array(
			'page_id' => $page_id_caching,
			'post_limit' => substr($this->post_limit, 0, 3),
			'show_posts_by' => substr($this->feed_settings['showpostsby'], 0, 2),
			'locale' => $this->locale
		);
		$trans_arr_item_count = 1;


		$albums_only = $this->check_onlyone_feed_type('album');
		$photos_only = $this->check_onlyone_feed_type('photo');
		$videos_only = $this->check_onlyone_feed_type('video');
		$reviews_only = $this->check_onlyone_feed_type('review');
		$trans_items_arr['albums_only'] = intval($albums_only);
		$trans_items_arr['photos_only'] = intval($photos_only);
		$trans_items_arr['videos_only'] = intval($videos_only);
		$trans_items_arr['reviews'] = intval($reviews_only);
		if ($albums_only) {
			$trans_items_arr['albums_source'] = $this->feed_settings['albumsource'];
		}

		$arr_item_max_length = floor(28 / $trans_arr_item_count);
		$arr_item_max_length_half = floor($arr_item_max_length / 2);
		$transient_name = 'cff_';
		foreach ($trans_items_arr as $key => $value) {
			if ($value !== false) {
				if ($key === 'page_id' || $key === 'featured_post' || $key === 'from' || $key === 'until') {
					$transient_name .= substr($value, 0, $arr_item_max_length_half) . substr($value, $arr_item_max_length_half * -1);
				}
				if ($key === 'locale') {
					$transient_name .= substr($value, 0, 2);
				}
				if ($key === 'post_limit' || $key === 'show_posts_by') {
					$transient_name .= substr($value, 0, 3);
				}
			}
		}
		$transient_name = substr($transient_name, 0, 45);
		return $transient_name;
	}





	/**
	 * Check if Only One Feed Type
	 * Function to check if Only => Album/Link/Video/Photo...
	 *
	 * @since 5.0
	 */
	public function check_onlyone_feed_type($type)
	{
		$types_array = ['link', 'video', 'photo', 'album', 'status', 'review', 'event'];
		unset($types_array[array_search($type, $types_array)]);
		$feed_types = $this->feed_settings['type'];
		$is_onlyone = CFF_Utils::stripos($type, 'event') !== false;
		foreach ($types_array as $s_type) {
			if (CFF_Utils::stripos($feed_types, $s_type) !== false) {
				$is_onlyone = false;
			}
		}
		return $is_onlyone;
	}

	/**
	 * Get Feed Cache Seconds
	 *
	 * @since 5.0
	 */
	public function get_cache_seconds()
	{
		$cache_type = $this->feed_settings['cachetype'];

		if ($cache_type === 'background') {
			return 7 * DAY_IN_SECONDS;
		}

		$cache_unit = 60;
		$cache_time = (intval($this->feed_settings['cachetime']) < 1) ? 1 : $this->feed_settings['cachetime'];

		switch ($this->feed_settings['cacheunit']) {
			case ('hour' || 'hours' || 0):
				$cache_unit = 60 * 60;
				break;
			case ('day' || 'days'):
				$cache_unit = 60 * 60 * 24;
				break;
		}

		return intval($cache_unit) * intval($cache_time);
	}


	/**
	 * Get Feed Data
	 *
	 * @since 5.0
	 */
	public function set_cache_type()
	{
		$cache_type = 'posts';
		if (strpos($this->transient_name, 'cff_header_') !== false) {
			$cache_type = 'header';
		}
		$cache_type_page = $cache_type;
		if ($cache_type === 'posts' && $this->feed_page > 1) {
			$cache_type_page = 'posts_' . $this->feed_page;
		}
		$this->cache_type = $cache_type;
		$this->cache_type_page = $cache_type_page;
	}

	/**
	 * Get Posts JSON By Feed Type
	 *
	 * @since 5.0
	 */
	public function get_posts_json_byfeed_type()
	{
		if ($this->is_group && !CFF_Graph_Data::should_make_group_call()) {
			\cff_main()->cff_error_reporter->add_group_deprecation_error($this->page_id);
			return [];
		}
		$posts_json = $this->get_remote_data();
		return $posts_json;
	}


	/**
	 * Get Group Posts
	 *
	 * @since 5.0
	 */
	public function get_group_posts()
	{
		$groups_post = new CFF_Group_Posts($this->page_id, $this->feed_settings, $this->graph_url, $this->shortcode_atts, false);
		$latest_record_date = (isset($this->next_urls_arr_safe['latest_record_date'])) ? $this->next_urls_arr_safe['latest_record_date'] : false;
		$groups_post_result = $groups_post->init_group_posts($this->posts_json, $latest_record_date, $this->post_limit);
		return $groups_post_result['posts_json'];
	}

	/**
	 * Get Group Posts
	 *
	 * @since 5.0
	 */
	public static function should_make_group_call()
	{
		return time() < strtotime('2024-4-22');
	}

	/**
	 * Get Feed Data
	 *
	 * @since 5.0
	 */
	public function get_feed_data()
	{
		if ($this->feed_cache->is_expired($this->cache_type) || $this->is_customizer) {
			$posts_json = $this->get_posts_json_byfeed_type();
			$this->process_posts_json_response($posts_json);
		} else {
			$this->process_cached_posts_json();
		}
		if ($this->is_group) {
			$posts_json = $this->get_group_posts();
			$this->set_posts_json($posts_json);
		}
		return $this->get_posts_json();
	}

	/**
	 * Process Posts JSON Response
	 *
	 * @since 5.0
	 */
	public function process_posts_json_response($posts_json)
	{
		$fb_data_json = !is_array($posts_json) ? json_decode($posts_json) : [];
		$fb_data = [];
		if (isset($fb_data_json->data)) {
			$fb_data = $fb_data_json->data;
		} else {
			$fb_data = !is_array($posts_json) ? json_decode($posts_json) : [];
		}
		$posts_json = [
			'api_url' => $this->graph_url,
			'shortcode_options' => $this->shortcode_atts,
			'data' => $fb_data
		];

		if (isset($fb_data_json->paging)) {
			$posts_json['paging'] = $fb_data_json->paging;
		}
		$posts_json = wp_json_encode($posts_json, true);
		$this->set_posts_json($posts_json);
		$this->process_backup_data($fb_data);
	}

	/**
	 * Process Posts JSON Response
	 *
	 * @since 5.0
	 */
	public function process_cached_posts_json()
	{
		$posts_json = $this->feed_cache->get($this->cache_type_page);
		if ($posts_json !== null && strpos($posts_json, '"error":{"message":') !== false && false !== get_transient('!cff_backup_' . $this->transient_name)) {
			$posts_json = $this->feed_cache->get($this->cache_type . '_backup');
		}
		if ($posts_json === false) {
			$posts_json = $this->get_remote_data();
		}
		$this->set_posts_json($posts_json);
	}


	/**
	 * Connect to API & get Remote data
	 *
	 * @since 5.0
	 */
	public function get_remote_data()
	{
		$api_connect = new CFF_API_Connect($this->graph_url, '', [
			'page_id' => $this->page_id
		]);
		$api_connect->connect();
		$response = $api_connect->get_json_data();
		do_action('cff_api_connect_response', $response, $this->graph_url);
		return $response;
	}

	/**
	 * Process API Error
	 *
	 * @since 5.0
	 */
	public function process_api_error($fb_data, $cache_type)
	{
		$posts_json = [];
		if (false !== $this->feed_cache->get($cache_type . '_backup')) {
			$posts_json = $this->feed_cache->get($cache_type . '_backup');

			$error_message = isset($fb_data->error->message) ? $fb_data->error->message : '';
			$error_type = isset($fb_data->error->type) ? $fb_data->error->type : '';
			$error_json = [
				'cached_error' => [
					'message' => $error_message,
					'type' => $error_type
				]
			];
			if (!empty($posts_json)) {
				array_push(
					$error_json,
					json_decode($posts_json, true)
				);
			}
			$posts_json = wp_json_encode($error_json);
		}
		return $posts_json;
	}


	/**
	 * Process API Erro
	 *
	 * @since 5.0
	 */
	public function process_backup_data($fb_data)
	{
		if (!empty($fb_data)) {
			if (isset($fb_data->error)) {
				$posts_json = $this->process_api_error($fb_data, $this->cache_type);
				$this->set_posts_json($posts_json);
			} else {
				if ($this->cache_type === 'posts') {
					$this->feed_cache->after_new_posts_retrieved();
				}
			}
			$this->feed_cache->update_or_insert($this->cache_type, $this->posts_json);
		}
	}
	/**
	 * Triggered when clicking the Load More Button
	 * to Get new Feed Posts
	 *
	 * @since 5.0
	 */
	public function load_more_feed_data()
	{

		if (is_null($this->next_urls_arr_safe) || empty($this->next_urls_arr_safe)) {
			return '';
		}
		$more_posts = isset($this->next_urls_arr_safe[$this->page_id]) ? true : false;
		$next_url_safe = isset($this->next_urls_arr_safe[$this->page_id]) ? $this->next_urls_arr_safe[$this->page_id] : '';
		if (!$more_posts) {
			return 'no_more_posts';
		}
		$feed_token = $this->access_token;
		if (is_array($feed_token)) {
			if (isset($feed_token[$this->page_id])) {
				$feed_token = $feed_token[$this->page_id];
			} else {
				$feed_token = reset($feed_token);
			}
		}

		$api_url = str_replace("x_cff_hide_token_x", $feed_token, $next_url_safe);
		$url_bits = parse_url($api_url, PHP_URL_QUERY);
		parse_str($url_bits, $url_bits_arr);
		if (isset($url_bits_arr['until'])) {
			$unique_string = $url_bits_arr['until'];
		} elseif (isset($url_bits_arr['after'])) {
			$unique_string = $url_bits_arr['after'];
			if (strlen($unique_string) > 15) {
				$unique_string = substr($unique_string, -15);
			}
		} elseif (isset($url_bits_arr['offset'])) {
			$unique_string = $url_bits_arr['offset'];
		} else {
			$unique_string = '';
		}
		$this->set_graph_url($api_url);
		return $this->get_feed_data();
	}
}