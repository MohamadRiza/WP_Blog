<?php
/**
 * Reviews Collections PAge
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Admin;

use Smashballoon\Customizer\V2\Collections_Builder;
use SmashBalloon\Reviews\Common\Builder\Config\Proxy;
use SmashBalloon\Reviews\Common\Builder\SBR_Sources;
use SmashBalloon\Reviews\Common\Util;


class SBR_Collections_Builder extends Collections_Builder
{

	protected $config_proxy;

	/**
	 *  MEnu Slug
	 * @since 1.0
	 */
	protected $builder_menu_slug;
	protected $current_plugin;
	protected $add_to_menu;

	protected $menu;

	public function __construct(Proxy $config_proxy)
	{
		$menu_title = __('Collections', 'reviews-feed') . ( !Util::sbr_is_pro() ? '<span class="sb-men-pro">PRO</span>' : '');
		$this->menu = [
			'parent_menu_slug' => "sbr",
			'page_title' => "Collections",
			'menu_title' => $menu_title,
			'menu_slug' => "sbr-collections",
		];
		$this->config_proxy = $config_proxy;
		$this->builder_menu_slug = SBR_CUSTOMIZER_MENU_SLUG;
		$this->current_plugin = 'ReviewsPro';
		$this->add_to_menu = !Util::sbr_is_pro() ? true : check_license_valid();
	}

	public function custom_collections_data()
	{

		$collections_data = [
			'nonce' => wp_create_nonce('sbr-admin'),
			'assetsURL' => SB_COMMON_ASSETS,
			'plugins' => Util::get_plugins_info(),
			'providers' => Util::get_providers(),
			'isPro' => Util::sbr_is_pro(),
			'recommendedPlugins' => Util::get_smashballoon_recommended_plugins_info(),
			'adminNoticeContent' => apply_filters('sbr_admin_notices_filter', 1),
			'collectionsPageUrl' => admin_url('admin.php?page=sbr-collections'),
			'sourcesList' => SBR_Sources::get_sources_list(),
			'sourcesCount' => SBR_Sources::get_sources_count(),
			'builderUrl'           => admin_url( 'admin.php?page=sbr'),
			'adminHomeURL'           => admin_url( 'admin.php'),
		];
		if (Util::sbr_is_pro()) {
			$FormsManager = new \SmashBalloon\Reviews\Pro\Integrations\Forms\FormsManager();
			$collections_data['formsManagerData'] = $FormsManager->get_form_plugins_data();
		}
		return $collections_data;
	}



}