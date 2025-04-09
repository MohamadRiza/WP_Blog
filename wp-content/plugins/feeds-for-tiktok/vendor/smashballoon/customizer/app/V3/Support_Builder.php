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
class Support_Builder extends ServiceProvider
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
        $this->current_plugin = apply_filters('sb_current_plugin', $this->current_plugin);
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
        $add_to_menu = isset($this->add_to_menu) ? $this->add_to_menu : \true;
        if (is_admin() && $add_to_menu) {
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
        $support_builder = add_submenu_page($this->menu['parent_menu_slug'], __('Support', 'feeds-for-tiktok'), __('Support', 'feeds-for-tiktok'), 'manage_options', $this->menu['menu_slug'], [$this, 'support_page_output'], 3);
        add_action('load-' . $support_builder, [$this, 'support_enqueue_admin_scripts']);
    }
    /**
     * Enqueue Builder CSS & Script.
     *
     * Loads only for builder pages
     *
     * @since 1.0
     */
    public function support_enqueue_admin_scripts()
    {
        $support_data = ['ajaxHandler' => admin_url('admin-ajax.php'), 'adminPostURL' => admin_url('post.php'), 'iconsList' => \Smashballoon\Customizer\V3\SB_Utils::get_icons(), 'reactScreen' => 'support'];
        $support_js_file = SBTT_CUSTOMIZER_ASSETS . '/build/static/js/main.js';
        if (!\Smashballoon\Customizer\V3\SB_Utils::is_production()) {
            $support_js_file = "http://localhost:3000/static/js/main.js";
        } else {
            wp_enqueue_style('sb-customizer-style', SBTT_CUSTOMIZER_ASSETS . '/build/static/css/main.css', \false, \false);
        }
        $support_data = array_merge($support_data, $this->customSupportData());
        //Data comming from the Actual plugin
        wp_enqueue_script('sb-customizer-app', $support_js_file, array('wp-i18n', 'jquery'), \false, \true);
        wp_localize_script('sb-customizer-app', 'sb_customizer', $support_data);
        wp_enqueue_media();
        wp_set_script_translations('sb-customizer-app', 'feeds-for-tiktok', SBTT_PLUGIN_DIR . 'languages/');
    }
    public function customSupportData()
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
    public function support_page_output()
    {
        ?>
        <div id="sb-app" class="sb-fs"></div>
    <?php 
    }
}
