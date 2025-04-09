<?php

namespace SmashBalloon\TikTokFeeds\Common\Customizer;

use Smashballoon\Customizer\V3\Feed_Builder;
use SmashBalloon\TikTokFeeds\Common\Container;
use SmashBalloon\TikTokFeeds\Common\FeedSettings;
use SmashBalloon\TikTokFeeds\Common\Feed;
use SmashBalloon\TikTokFeeds\Common\FeedCache;
use SmashBalloon\TikTokFeeds\Common\Services\SettingsManagerService;
use SmashBalloon\TikTokFeeds\Common\Utils;
use SmashBalloon\TikTokFeeds\Common\AuthorizationStatusCheck;

/**
 * Feed Builder
 */
class FeedBuilder extends Feed_Builder
{
	/**
	 * Settings Menu Info
	 *
	 * @var array
	 * @since 1.0
	 */
	protected $menu;

	/**
	 *  Customizer Tabs Path
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $tabs_path;

	/**
	 *  Customizer Tabs NameSpace
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $tabs_namespace;

	/**
	 *  MEnu Slug
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $builder_menu_slug;

	/**
	 * Plugin Status
	 *
	 * @var AuthorizationStatusCheck
	 * @since 1.0
	 */
	protected $plugin_status;

	/**
	 * Settings Manager Service
	 *
	 * @var SettingsManagerService
	 * @since 1.0
	 */
	protected $global_settings;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->menu = [
			'parent_menu_slug' => SBTT_MENU_SLUG,
			'page_title'       => "TikTok Feeds",
			'menu_title'       => "All Feeds",
			'menu_slug'        => SBTT_MENU_SLUG,
		];

		$this->tabs_path         = SBTT_CUSTOMIZER_TABS_PATH;
		$this->tabs_namespace    = SBTT_CUSTOMIZER_TABS_NAMESPACE;
		$this->builder_menu_slug = SBTT_MENU_SLUG;

		$this->plugin_status   = new AuthorizationStatusCheck();
		$this->global_settings = Container::get_instance()->get(SettingsManagerService::class);
	}

	/**
	 * Get localization data for the builder
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function customBuilderData()
	{
		$builder_data = [
			'nonce'          => wp_create_nonce('sbtt-admin'),
			'assetsURL'      => SBTT_COMMON_ASSETS,
			'feedsList'      => Utils::get_feeds_list(),
			'feedsCount'     => Utils::get_feeds_count(),
			'sourcesList'    => Utils::get_sources_list(),
			'connectionURLs' => sbtt_get_tiktok_connection_urls(),
			'pluginSettings' => $this->global_settings->get_global_settings(),
			'pluginStatus'   => $this->plugin_status->get_statuses(),
			'feedTypes'      => $this->getFeedtypesList(),
			'isPro'          => Utils::sbtt_is_pro(),
			'isSocialWallActive' => Utils::is_sb_plugin_active('social-wall'),
			'socialWallLinks'    => Utils::get_social_wall_links(),
			'themeSupportsWidgets' => current_theme_supports('widgets'),
			'aboutPageUrl' => admin_url('admin.php?page=sbtt-about'),
			'tieredFeatures' => Utils::get_tiered_features_list(),
			'upsellContent' => Utils::get_upsell_modal_content(),
			'upsellSidebarCards' => Utils::get_sidebar_upsell_cards(),
			'adminNoticeContent' => apply_filters('sbtt_admin_notices_filter', 1),
		];

		$newly_retrieved_source_connection_data = Utils::maybe_source_connection_data();
		if ($newly_retrieved_source_connection_data) {
			$builder_data['newSourceData'] = $newly_retrieved_source_connection_data;
		}

		return $builder_data;
	}

	/**
	 * Get Feed Info by ID
	 * This populates the feed builder with the feed info
	 */
	public function customizerFeedData()
	{
		if (! isset($_GET['feed_id'])) {
			return array();
		}

		$feed_id = absint($_GET['feed_id']);

		$feed_data = new FeedSettings($feed_id);
		$feed_settings = $feed_data->get_feed_settings();

		if (empty($feed_settings)) {
			return array();
		}

		$feed_info = $feed_data->get_feed_info();
		$sources = $feed_data->get_connected_feed_sources();

		$feed = new Feed($feed_settings, $feed_id, new FeedCache($feed_id, 12 * HOUR_IN_SECONDS));
		$feed->init();
		$feed->get_set_cache();
		$posts  = $feed->get_post_set_page();
		$errors = $feed->get_errors();

		if (isset($feed_settings['sortRandomEnabled']) && $feed_settings['sortRandomEnabled'] === true) {
			shuffle($posts);
		}

		return [
			'feed_info'   => $feed_info,
			'settings'    => $feed_settings,
			'posts'       => ! empty($posts) ? $posts : [],
			'errors'      => ! empty($errors) ? $errors : [],
			'sourcesList' => $sources,
		];
	}

	/**
	 * Get Templates
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function getTemplatesList()
	{
		return [
			[
				'type'  => 'default',
				'title' => __('Default', 'feeds-for-tiktok'),
			],
			[
				'type'  => 'cards',
				'title' => __('Cards', 'feeds-for-tiktok'),
				'upsellModal' => 'basicTemplateModal',
			],
			[
				'type'  => 'list',
				'title' => __('List', 'feeds-for-tiktok'),
				'upsellModal' => 'basicTemplateModal',
			],
			[
				'type' => 'latest_video',
				'title' => __('Latest Video', 'feeds-for-tiktok'),
				'upsellModal' => 'basicTemplateModal',
			],
			[
				'type'  => 'carousel',
				'title' => __('Carousel', 'feeds-for-tiktok'),
				'upsellModal' => 'plusTemplateModal',
			],
			[
				'type' => 'showcase_carousel',
				'title' => __('Showcase Carousel', 'feeds-for-tiktok'),
				'upsellModal' => 'plusTemplateModal',
			],
			[
				'type' => 'gallery',
				'title' => __('Gallery', 'feeds-for-tiktok'),
				'upsellModal' => 'plusTemplateModal',
			],
			[
				'type' => 'widget',
				'title' => __('Widget', 'feeds-for-tiktok'),
				'upsellModal' => 'plusTemplateModal',
			]
		];
	}

	/**
	 * Get Feed Types
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function getFeedtypesList()
	{
		return [
			[
				'type'  => 'own_timeline',
				'title' => __('My Timeline', 'feeds-for-tiktok'),
				'info'  => __('Create a feed from your own TikTok posts', 'feeds-for-tiktok'),
			],
		];
	}
}
