<?php
/**
 * SB_Analytics plugin integration
 * Class to impelement filters to return
 * data needed in the SB_Analytics plugin
 */

namespace TwitterFeed\Integrations\Analytics;

use TwitterFeed\Builder\CTF_Db;

class SB_Analytics
{
	/**
	 * Summary of current_plugin
	 * @var string
	 */
	private static $current_plugin = 'twitter';


	/**
	 * Summary of __construct
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Summary of init
	 * @return void
	 */
	public function init()
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
	 * @param mixed $posts
	 * @param mixed $post_ids
	 * @param mixed $plugin_slug
	 *
	 * @return mixed
	 */
	public function filter_top_posts($posts, $post_ids, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $posts;
		}

		return CTF_Db::get_posts_by_ids($post_ids);
	}

	/**
	 * Summary of filter_profile_details
	 *
	 * @param mixed $profile_details
	 * @param mixed $feed_id
	 * @param mixed $plugin_slug
	 *
	 * @return mixed
	 */
	public function filter_profile_details($profile_details, $feed_id, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $profile_details;
		}

		$source	= CTF_Db::get_feed_source_info($feed_id);
		if (empty($source) || empty($source['name'])) {
			return [];
		}

		return [
			'pluginSlug' => self::$current_plugin,
			'id'         => $source['name'],
			'profile'    => [
				'label'    => $source['name'],
				'imageSrc' => $source['picture']
			]
		];
	}

	/**
	 * Summary of filter_feed_list
	 *
	 * @param mixed $feeds
	 * @param mixed $plugin_slug
	 *
	 * @return mixed
	 */
	public function filter_feed_list($feeds, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $feeds;
		}

		$results = [];
		$db_fees = CTF_Db::all_feeds_query(); //Get All stored Feeds

		//Transform result feed schema
		foreach ($db_fees as $feed) {
			array_push(
				$results,
				[
					'value' => [
						'feed_id' => $feed['id'],
					],
					'label' => $feed['feed_name'],
				]
			);
		}
		return $results;
	}

}
