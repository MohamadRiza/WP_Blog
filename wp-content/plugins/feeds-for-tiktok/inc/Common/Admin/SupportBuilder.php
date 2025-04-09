<?php

namespace SmashBalloon\TikTokFeeds\Common\Admin;

use Smashballoon\Customizer\V3\Support_Builder;
use SmashBalloon\TikTokFeeds\Common\Config\Proxy;
use SmashBalloon\TikTokFeeds\Common\Utils;

/**
 * Support Builder
 */
class SupportBuilder extends Support_Builder
{
	/**
	 * Config Proxy
	 *
	 * @var Proxy
	 */
	protected $config_proxy;

	/**
	 *  Menu Slug
	 *
	 * @var string
	 */
	protected $builder_menu_slug;

	/**
	 * Current Plugin
	 *
	 * @var string
	 */
	protected $current_plugin;

	/**
	 * Add to Menu
	 *
	 * @var bool
	 */
	protected $add_to_menu;

	/**
	 * Menu
	 *
	 * @var array
	 */
	protected $menu;

	/**
	 * Constructor
	 *
	 * @param Proxy $config_proxy Config Proxy.
	 */
	public function __construct(Proxy $config_proxy)
	{
		$this->menu              = [
			'parent_menu_slug' => "sbtt",
			'page_title'       => "Support",
			'menu_title'       => "Support",
			'menu_slug'        => "sbtt-support",
		];
		$this->config_proxy      = $config_proxy;
		$this->builder_menu_slug = SBTT_MENU_SLUG;
		$this->current_plugin    = 'TikTokFeedsPro';
		$this->add_to_menu       = !Utils::sbtt_is_pro() ? true : Utils::is_license_valid();
	}

	/**
	 * Retrieves custom support data.
	 *
	 * @return array The custom support data.
	 */
	public function customSupportData()
	{
		$aboutus_data = [
			'nonce'          => wp_create_nonce('sbtt-admin'),
			'assetsURL'      => SBTT_COMMON_ASSETS,
			'feedsList'      => Utils::get_feeds_list(),
			'supportContent' => $this->getSupportContent(),
			'supportInfo'    => $this->getSupportInfo(),
			'isPro'          => Utils::sbtt_is_pro(),
			'aboutPageUrl'   => admin_url('admin.php?page=sbtt-about'),
			'isSocialWallActive' => Utils::is_sb_plugin_active('social-wall'),
			'socialWallLinks'    => Utils::get_social_wall_links(),
			'adminNoticeContent' => apply_filters('sbtt_admin_notices_filter', 1),
		];

		return $aboutus_data;
	}

	/**
	 * Retrieves the support content.
	 *
	 * @return array The support content.
	 */
	public function getSupportContent()
	{
		$utm_source = 'tiktok-feeds-pro';
		return [
			[
				'heading'     => __('Getting Started', 'feeds-for-tiktok'),
				'description' => __('Some helpful resources to get you started', 'feeds-for-tiktok'),
				'icon'        => 'rocketicon',
				'content'     => [
					[
						'text' => __('Getting Started with TikTok Feeds', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/installing-the-tiktok-feeds-pro-wordpress-plugin/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=getting-started&utm_content=GettingStarted',
					],
					[
						'text' => __('How Do I Show a Feed from Another Account', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/show-a-feed-from-another-tiktok-account/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=another-account&utm_content=ShowFromAnother',
					],
					[
						'text' => __('How to Keep My Account Connected', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/how-to-keep-my-account-connected/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=keep-connected&utm_content=KeepAccountConnected',
					],
				],
				'button'      => [
					'text' => __('More Help Getting started', 'feeds-for-tiktok'),
					'link' => 'https://smashballoon.com/docs/getting-started/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=docs&utm_content=Getting Started',
				],
			],
			[
				'heading'     => __('Docs & Troubleshooting', 'feeds-for-tiktok'),
				'description' => __('Run into an issue? Check out our help docs.', 'feeds-for-tiktok'),
				'icon'        => 'bookopen',
				'content'     => [
					[
						'text' => __('Error Message Reference', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/tiktok-feeds-error-message-reference/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=error-reference&utm_content=ErrorReference',
					],
					[
						'text' => __('My Feed is Not Displaying', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/my-tiktok-feed-is-not-displaying/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=not-displaying&utm_content=FeedNotDisplaying',
					],
					[
						'text' => __('My Feed is Not Updating', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/my-feed-is-not-updating/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=not-updating&utm_content=NotUpdating',
					],
				],
				'button'      => [
					'text' => __('View Documentation', 'feeds-for-tiktok'),
					'link' => 'https://smashballoon.com/docs/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=docs&utm_content=View Documentation',
				],
			],
			[
				'heading'     => __('Additional Resources', 'feeds-for-tiktok'),
				'description' => __('To help you get the most out of the plugin', 'feeds-for-tiktok'),
				'icon'        => 'bookplus',
				'content'     => [
					[
						'text' => __('Video Filtering Guide?', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/how-do-i-filter-the-videos-in-my-tiktok-feed/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=filter-feed&utm_content=VideoFilteringGuide',
					],
					[
						'text' => __('Can I Display Multiple Feeds on One Page?', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/can-i-display-multiple-tiktok-feeds-on-one-page/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=multiple-feeds&utm_content=MultipleFeeds',
					],
					[
						'text' => __('How Do I Embed a Feed Directly In a Template?', 'feeds-for-tiktok'),
						'link' => 'https://smashballoon.com/doc/how-do-i-embed-a-tiktok-feed-directly-in-the-theme-template/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=embed-template&utm_content=EmbedTemplate',
					],
				],
				'button'      => [
					'text' => __('View Blog', 'feeds-for-tiktok'),
					'link' => 'https://smashballoon.com/blog/?tiktok&utm_campaign=' . $utm_source . '&utm_source=support&utm_medium=docs&utm_content=View Blog',
				],
			],
		];
	}

	/**
	 * Retrieves the support info.
	 *
	 * @return string The support info.
	 */
	public function getSupportInfo()
	{
		$output = '';

		$output .= sbtt_get_site_n_server_info();
		$output .= sbtt_get_active_plugins_info();
		$output .= sbtt_get_global_settings_info();
		$output .= sbtt_get_sources_settings_info();

		return $output;
	}
}
