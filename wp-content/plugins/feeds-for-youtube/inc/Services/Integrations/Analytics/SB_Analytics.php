<?php

/**
 * SB_Analytics plugin integration
 * Class to impelement filters to return
 * data needed in the SB_Analytics plugin
 */

namespace SmashBalloon\YouTubeFeed\Services\Integrations\Analytics;

use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\SBY_Feed;
use SmashBalloon\YouTubeFeed\Pro\SBY_Feed_Pro;
use SmashBalloon\YouTubeFeed\SBY_Settings;
use SmashBalloon\YouTubeFeed\Pro\SBY_Settings_Pro;
use SmashBalloon\YouTubeFeed\SBY_Parse;

class SB_Analytics
{
	/**
	 * Summary of current_plugin
	 * @var string
	 */
	private static $current_plugin = 'youtube';

	/**
	 * Constructor.
	 *
	 * @since x.x.x
	 */
	public function register()
	{
		$this->load();
	}


	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since x.x.x
	 *
	 * @return bool
	 */
	public function allow_load()
	{
		//return defined( 'SB_ANALYTICS_NAMESPACE' ); -- Need to ask regarding this condition.
		return true;
	}


	/**
	 * Load an integration.
	 *
	 * @since x.x.x
	 */
	public function load()
	{
		if ($this->allow_load()) {
			$this->hooks();
		}
	}


	/**
	 * Hooks.
	 *
	 * @since x.x.x
	 */
	public function hooks()
	{

		//Filter Top Posts
		add_filter(
			'sb_analytics_filter_top_posts',
			[$this, 'filter_top_posts'],
			10,
			3
		);

		//Filter Profile Details
		add_filter(
			'sb_analytics_filter_profile_details',
			[$this, 'filter_profile_details'],
			10,
			3
		);

		//Filter Feed Lists
		add_filter(
			'sb_analytics_filter_feed_list',
			[$this, 'filter_feed_list'],
			10,
			3
		);
	}

	/**
	 * Summary of filter_top_posts
	 *
	 * @param array $posts
	 * @param array $post_ids
	 * @param string $plugin_slug
	 *
	 * @return array
	 */
	public function filter_top_posts($posts, $post_ids, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $posts;
		}

		if (empty($post_ids)) {
			return [];
		}

		return self::get_posts_by_ids($post_ids);
	}

	/**
	 * Filters and modifies profile details based on the current plugin.
	 *
	 * @param array  $profile_details
	 * @param int    $feed_id
	 * @param string $plugin_slug
	 * 
	 * @since x.x.x
	 * 
	 * @return array
	 */
	function filter_profile_details($profile_details, $feed_id, $plugin_slug)
	{

		if ($plugin_slug !== self::$current_plugin) {
			return $profile_details;
		}

		$info = self::get_feed_source_info($feed_id);

		if (empty($info)) {
			return [];
		}

		return $info;
	}

	/**
	 * Summary of filter_feed_list
	 *
	 * @param array $feeds
	 * @param string $plugin_slug
	 *
	 * @since x.x.x
	 * 
	 * @return array
	 */
	public function filter_feed_list($feeds, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $feeds;
		}

        return $this->get_all_feeds();
	}

	/**
	 * Get posts by id.
	 *
	 * @param array $post_ids Posts id.
	 * 
	 * @since x.x.x
	 *
	 * @return array
	 */
	public function get_posts_by_ids($post_ids)
	{
		if (empty($post_ids)) {
			return [];
		}

		$records = [];

		$feeds = Feed_Builder::instance()->get_feed_list();

		foreach ($feeds as $feed) {

			$feed_id = $feed['id'];

			if (empty($feed_id)) {
				continue;
			}

			$atts                  = ['feed' => $feed_id];
			$database_settings     = sby_get_database_settings();

			$youtube_feed_settings =
				sby_is_pro()
				? new SBY_Settings_Pro($atts, $database_settings, false)
				: new SBY_Settings($atts, $database_settings, false);

			$youtube_feed_settings->set_feed_type_and_terms();
			$youtube_feed_settings->set_transient_name();
			$transient_name = $youtube_feed_settings->get_transient_name();

			$youtube_feed =
				sby_is_pro()
				? new SBY_Feed_Pro($transient_name)
				: new SBY_Feed($transient_name);
			$youtube_feed->set_post_data_from_cache();

			$posts = $youtube_feed->get_post_data();

			foreach ($posts as $post) {

				$youtube_id = '';

				if (!empty($post['snippet']['liveBroadcastContent'])) {
					$youtube_id = !empty($post['id']) ? $post['id'] : '';
				} else {
					$youtube_id = !empty($post['snippet']['resourceId']['videoId']) ? $post['snippet']['resourceId']['videoId'] : '';
				}

				if (in_array($youtube_id, $post_ids, true)) {

					$records[$youtube_id] = [
						'plugin'           => [
							'slug' => self::$current_plugin,
						],
						'feed_id'          => $feed_id,
						'feed_post_id'     => $youtube_id,
						'text'             => !empty($post['snippet']['title']) ? $post['snippet']['title'] : '',
						'imageSrc'         => !empty($post['snippet']['thumbnails']['medium']) ? $post['snippet']['thumbnails']['medium']['url'] : '',
						'updated_time_ago' => !empty($post['snippet']['publishedAt']) ? sprintf('%s ago', human_time_diff(strtotime($post['snippet']['publishedAt']), time())) : '',
						'profile'          => [
							'label' => !empty($post['snippet']['channelTitle']) ? $post['snippet']['channelTitle'] : '',
							'url'   => '',
							'id'    => !empty($post['snippet']['channelId']) ? $post['snippet']['channelId'] : '',
						],
					];
				}
			}
		}

		return $records;
	}

	/**
	 * Get all feeds by using the classes from the YouTube plugin.
	 *
	 * @since x.x.x
	 * 
	 * @return array
	 */
	public function get_all_feeds()
	{
		$records = [];

		$feeds = Feed_Builder::instance()->get_feed_list();

		if (empty($feeds)) {
			return $records;
		}

		foreach ($feeds as $feed) {
			$feed_id = $feed['id'];

			$records[] = [
				'value'  => [
					'feed_id'    => !empty($feed_id) ? $feed_id : '',

				],
				'label'  => ! empty($feed['feed_title']) ? $feed['feed_title'] : $feed['feed_name'],
			];
		}

		return $records;
	}


	/**
	 * Retrieves source information for a specific feed.
	 *
	 * @param int $feed_id 
	 * 
	 * @since x.x.x
	 * 
	 * @return array
	 */
	public function get_feed_source_info($feed_id)
	{

		if (empty($feed_id)) {
			return [];
		}

		$header_data = self::get_header_data($feed_id);

		if (empty($header_data)) {
			return [];
		}

		$channel_title = SBY_Parse::get_channel_title( $header_data );
		$avatar        = SBY_Parse::get_avatar( $header_data, [] );
		$source_id     = null;

		if ( ! empty( $header_data['items'][0]['id'] ) ) {
			$source_id = $header_data['items'][0]['id'];
		}

		return [
			'id'         => $source_id,
			'pluginSlug' => self::$current_plugin,
			'profile'    => [
				'label'    => !empty($channel_title) ? $channel_title : '',
				'imageSrc' => !empty($avatar) ? $avatar : '',
			],
		];
	}

	/**
	 * Get header data.
	 *
	 * @param int $feed_id.
	 * 
	 * @since x.x.x
	 *
	 * @return array
	 */
	public static function get_header_data($feed_id)
	{
		$atts              = ['feed' => $feed_id];
		$database_settings = sby_get_database_settings();
		$youtube_feed_settings = sby_is_pro() ? new SBY_Settings_Pro($atts, $database_settings) : new SBY_Settings($atts, $database_settings);

		if (empty($database_settings['connected_accounts']) && empty($database_settings['api_key'])) {
			return [];
		}

		$youtube_feed_settings->set_feed_type_and_terms();
		$youtube_feed_settings->set_transient_name();
		$transient_name = $youtube_feed_settings->get_transient_name();

		$youtube_feed = sby_is_pro()
			? new SBY_Feed_Pro($transient_name)
			: new SBY_Feed($transient_name);

		$youtube_feed->set_header_data_from_cache();
		$header_data = $youtube_feed->get_header_data();

		if ($header_data) {
			return $header_data;
		}

		$youtube_feed->set_remote_header_data(
			$youtube_feed_settings,
			$youtube_feed_settings->get_feed_type_and_terms(),
			$youtube_feed_settings->get_connected_accounts_in_feed()
		);

		$header_data = $youtube_feed->get_header_data();
	}
}
