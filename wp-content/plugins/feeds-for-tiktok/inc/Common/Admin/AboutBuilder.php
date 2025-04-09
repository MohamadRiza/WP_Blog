<?php

namespace SmashBalloon\TikTokFeeds\Common\Admin;

use Smashballoon\Customizer\V3\About_Builder;
use SmashBalloon\TikTokFeeds\Common\Config\Proxy;
use SmashBalloon\TikTokFeeds\Common\Utils;

/**
 * About Builder
 */
class AboutBuilder extends About_Builder
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
			'page_title'       => "About Us",
			'menu_title'       => "About Us",
			'menu_slug'        => "sbtt-about",
		];
		$this->config_proxy      = $config_proxy;
		$this->builder_menu_slug = SBTT_MENU_SLUG;
		$this->current_plugin    = 'TikTokFeedsPro';
		$this->add_to_menu       = !Utils::sbtt_is_pro() ? true : Utils::is_license_valid();
	}

	/**
	 * Retrieves custom about us data.
	 *
	 * @return array The custom about us data.
	 */
	public function customAboutusData()
	{
		$aboutus_data = [
			'nonce'              => wp_create_nonce('sbtt-admin'),
			'assetsURL'          => SBTT_COMMON_ASSETS,
			'plugins'            => [],
			'isPro'              => Utils::sbtt_is_pro(),
			'recommendedPlugins' => [],
			'aboutPageUrl'       => admin_url('admin.php?page=sbtt-about'),
			'isSocialWallActive' => Utils::is_sb_plugin_active('social-wall'),
			'socialWallLinks'    => Utils::get_social_wall_links(),
			'adminNoticeContent' => apply_filters('sbtt_admin_notices_filter', 1),
		];

		return $aboutus_data;
	}
}
