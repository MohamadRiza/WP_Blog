<?php

namespace Smashballoon\Customizer\V3;

use Smashballoon\Stubs\Services\ServiceProvider;
/**
 * Class Customizer
 * Class to create
 *
 * @since 1.0
 */
if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Feed_Builder extends ServiceProvider
{
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = \Smashballoon\Customizer\V3\Container::getInstance()->get(self::class);
            return self::$instance;
        }
        return self::$instance;
    }
    public function __construct()
    {
        $this->config_proxy = apply_filters('sb_customizer_config_proxy', $this->config_proxy);
        $this->tabs_path = apply_filters('sb_customizer_tabs_path', $this->tabs_path);
        $this->tabs_namespace = apply_filters('sb_customizer_tabs_namespace', $this->tabs_namespace);
        $this->builder_menu_slug = apply_filters('sb_customizer_builder_menu_slug', $this->builder_menu_slug);
    }
    /**
     * Entry point
     *
     * @return void
     *
     * @since 1.0
     */
    public function register()
    {
        if (is_admin()) {
            add_action('admin_menu', [$this, 'register_menu']);
        }
    }
    /**
     * Register Menu
     *
     *
     * @since 1.0
     */
    public function register_menu()
    {
        $feed_builder = add_submenu_page($this->menu['parent_menu_slug'], __("All Feeds", "feeds-for-tiktok"), __("All Feeds", "feeds-for-tiktok"), 'manage_options', $this->menu['menu_slug'], [$this, 'feed_cutomizer_output'], 0);
        add_action('load-' . $feed_builder, [$this, 'builder_enqueue_admin_scripts']);
    }
    /**
     * Enqueue Builder CSS & Script.
     *
     * Loads only for builder pages
     *
     * @since 1.0
     */
    public function builder_enqueue_admin_scripts()
    {
        $builder_data = ['ajaxHandler' => admin_url('admin-ajax.php'), 'adminPostURL' => admin_url('post.php'), 'widgetsPageURL' => admin_url('widgets.php'), 'builderUrl' => admin_url('admin.php?page=' . $this->builder_menu_slug), 'wordpressPageLists' => \Smashballoon\Customizer\V3\SB_Utils::get_wp_pages(), 'iconsList' => \Smashballoon\Customizer\V3\SB_Utils::get_icons(), 'reactScreen' => 'customizer', 'templatesList' => $this->getTemplatesList()];
        //Only for Customizer
        if (isset($_GET['feed_id'])) {
            $builder_data = array_merge($builder_data, ['isFeedEditor' => \true, 'feedEditor' => ['defaultTab' => 'sb-customize-tab'], 'customizerData' => $this->customizer_builder_data(), 'feedData' => $this->customizerFeedData()]);
            $this->enqueue_date_i18n();
        }
        $customizer_js_file = SBTT_CUSTOMIZER_ASSETS . '/build/static/js/main.js';
        if (!\Smashballoon\Customizer\V3\SB_Utils::is_production()) {
            $customizer_js_file = "http://localhost:3000/static/js/main.js";
        } else {
            wp_enqueue_style('sb-customizer-style', SBTT_CUSTOMIZER_ASSETS . '/build/static/css/main.css', \false, \false);
        }
        $builder_data = array_merge($builder_data, $this->customBuilderData());
        //Data comming from the Actual plugin
        wp_enqueue_script('sb-customizer-app', $customizer_js_file, array('wp-i18n', 'jquery'), \false, \true);
        wp_localize_script('sb-customizer-app', 'sb_customizer', $builder_data);
        wp_enqueue_media();
        wp_set_script_translations('sb-customizer-app', 'feeds-for-tiktok', SBTT_PLUGIN_DIR . 'languages/');
    }
    public function customBuilderData()
    {
        return [];
    }
    /**
     * Build Builder Data
     * Will create an array that contains all the Customizer sidebar Data
     *
     * @since 1.0
     * @return array
     */
    public function customizer_builder_data()
    {
        $customizer_builder_data = [];
        /* Require Directly Tab Classes files */
        foreach (scandir($this->tabs_path) as $filename) {
            $path = $this->tabs_path . '/' . $filename;
            if (is_file($path)) {
                require $path;
                $tab_name = $this->tabs_namespace . str_replace('.php', '', $filename);
                if (class_exists($tab_name) && is_subclass_of($tab_name, \Smashballoon\Customizer\V3\SB_Sidebar_Tab::class)) {
                    $tab_class = new $tab_name();
                    $customizer_builder_data[] = $tab_class->get_tab();
                }
            }
        }
        return $customizer_builder_data;
    }
    /**
     * Enqueue Date i18n
     *
     * @return void
     *
     * @since 1.0
     */
    public function enqueue_date_i18n()
    {
        global $wp_locale;
        wp_enqueue_script("sb-date_i18n", SBTT_COMMON_ASSETS . 'sb-customizer/assets/js/date_i18n.js', null, \false, \true);
        $monthNames = array_map(array(&$wp_locale, 'get_month'), range(1, 12));
        $monthNamesShort = array_map(array(&$wp_locale, 'get_month_abbrev'), $monthNames);
        $dayNames = array_map(array(&$wp_locale, 'get_weekday'), range(0, 6));
        $dayNamesShort = array_map(array(&$wp_locale, 'get_weekday_abbrev'), $dayNames);
        wp_localize_script("sb-date_i18n", "DATE_I18N", array("month_names" => $monthNames, "month_names_short" => $monthNamesShort, "day_names" => $dayNames, "day_names_short" => $dayNamesShort));
    }
    /**
     * Get Feed Info
     * Settings
     *
     * @return array
     *
     * @since 1.0
     */
    public function customizerFeedData()
    {
        return ['feed_info' => [], 'settings' => [], 'posts' => []];
    }
    /**
     * Get Feed List
     *
     * @return array
     *
     * @since 1.0
     */
    public static function get_feeds_list()
    {
        return [];
    }
    /**
     * Get Sources
     *
     * @return array
     *
     * @since 1.0
     */
    public function get_sources_list()
    {
        return [];
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
        return [];
    }
    /**
     * Feed Customizer Output
     *
     * @return HTML
     *
     * @since 1.0
     */
    public function feed_cutomizer_output()
    {
        ?>
            <div id="sb-app" class="sb-fs"></div>
        <?php 
    }
}
