<?php
/**
 * Reviews Feed Saver
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Admin;

use Smashballoon\Customizer\V2\About_Builder;
use SmashBalloon\Reviews\Common\Builder\Config\Proxy;
use SmashBalloon\Reviews\Common\Util;
class SBR_About_Builder extends About_Builder
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
        $this->menu = [
            'parent_menu_slug' => "sbr",
            'page_title' => "About us",
            'menu_title' => "About us",
            'menu_slug' => "sbr-about",
        ];
        $this->config_proxy = $config_proxy;
        $this->builder_menu_slug = SBR_CUSTOMIZER_MENU_SLUG;
        $this->current_plugin = 'ReviewsPro';
        $this->add_to_menu = !Util::sbr_is_pro() ? true : check_license_valid();
    }

    public function custom_aboutus_data()
    {
        $aboutus_data = [
            'nonce' => wp_create_nonce('sbr-admin'),
            'assetsURL' => SB_COMMON_ASSETS,
            'plugins' => Util::get_plugins_info(),
            'isPro' => Util::sbr_is_pro(),
            'recommendedPlugins' => Util::get_smashballoon_recommended_plugins_info(),
            'adminNoticeContent' => apply_filters('sbr_admin_notices_filter', 1),
            'aboutPageUrl' => admin_url('admin.php?page=sbr-about'),
            'collectionsPageUrl' => admin_url('admin.php?page=sbr-collections'),
            'builderUrl'           => admin_url( 'admin.php?page=sbr'),
            'freeRetrieverData'     => Util::get_free_retriever_data()
        ];

        return $aboutus_data;
    }



}