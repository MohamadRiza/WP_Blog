<?php

namespace SmashBalloon\TikTokFeeds\Common\Integrations;

use SmashBalloon\TikTokFeeds\Common\Database\FeedsTable;
use SmashBalloon\TikTokFeeds\Common\Database\SourcesTable;

class FeedAnalytics
{
	/**
	 * The slug of the current plugin.
	 *
	 * @var string
	 */
	protected static $current_plugin = 'tiktok';

	/**
	 * Load the necessary filters for analytics.
	 *
	 * @return void
	 */
	public function register()
	{
		add_filter('sb_analytics_filter_profile_details', [$this, 'filterProfileDetails'], 10, 3);
		add_filter('sb_analytics_filter_feed_list', [$this, 'filterFeedList'], 10, 2);
	}

	/**
	 * Filter the profile details based on the provided profile information.
	 *
	 * @param array      $profile_details The original profile details.
	 * @param int|string $feed_id The ID of the feed.
	 * @param string     $plugin_slug The slug of the current plugin.
	 * @return array The filtered profile details.
	 */
	public function filterProfileDetails($profile_details, $feed_id, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $profile_details;
		}

		$feeds_table = new FeedsTable();
		$feed_data = $feeds_table->get_feed($feed_id);

		$feed_settings = !empty($feed_data['settings']) ? json_decode($feed_data['settings'], true) : [];

		if (!empty($feed_settings['sources'])) {
			$open_id = is_array($feed_settings['sources']) ? $feed_settings['sources'][0] : $feed_settings['sources'];

			$source_table = new SourcesTable();
			$source = $source_table->get_source($open_id);

			if ($source) {
				$source_info = json_decode($source['info'], true);
				$profile_details = [
					'id'         => $source_info['open_id'],
					'pluginSlug' => self::$current_plugin,
					'profile'    => [
						'label'    => $source_info['display_name'] ?? $source_info['username'],
						'imageSrc' => $source_info['local_avatar_url'] ?? $source_info['avatar_url']
					]
				];
			}
		}

		return $profile_details;
	}

	/**
	 * Filter the feed list based on the provided plugin slug.
	 *
	 * @param array  $feeds The original feeds array.
	 * @param string $plugin_slug The slug of the current plugin.
	 * @return array The filtered feeds array.
	 */
	public function filterFeedList($feeds, $plugin_slug)
	{
		if ($plugin_slug !== self::$current_plugin) {
			return $feeds;
		}

		$feeds_table = new FeedsTable();
		$feeds  = $feeds_table->get_feeds();

		if (! $feeds) {
			return [];
		}

		$results = [];
		foreach ($feeds as $feed) {
			$results[] = [
				'value' => [
					'feed_id' => $feed['id'],
				],
				'label' => $feed['feed_name'],
			];
		}

		return $results;
	}
}
