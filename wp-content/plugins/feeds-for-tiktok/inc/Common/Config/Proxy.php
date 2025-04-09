<?php

namespace SmashBalloon\TikTokFeeds\Common\Config;

class Proxy extends \Smashballoon\Customizer\V3\Config\Proxy
{
	/**
	 * Parent menu slug.
	 *
	 * @var string
	 */
	public $parent_menu_slug = SBTT_MENU_SLUG;

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public $menu_slug        = SBTT_MENU_SLUG;

	/**
	 * Menu title.
	 *
	 * @var string
	 */
	public $menu_title       = "TikTok Feeds";

	/**
	 * Page title.
	 *
	 * @var string
	 */
	public $page_title       = "TikTok Feeds";
}
