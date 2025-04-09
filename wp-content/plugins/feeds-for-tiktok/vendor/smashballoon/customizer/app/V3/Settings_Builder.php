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
class Settings_Builder extends ServiceProvider
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
        $this->menu = apply_filters('sb_customizer_config_proxy', $this->menu);
        $this->settingspage_tabs_path = apply_filters('sb_customizer_tabs_path', $this->settingspage_tabs_path);
        $this->settingspage_tabs_namespace = apply_filters('sb_customizer_tabs_namespace', $this->settingspage_tabs_namespace);
        $this->tabs_order = apply_filters('sb_settings_tabs_order', $this->tabs_order);
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
        $settings_builder = add_submenu_page($this->menu['parent_menu_slug'], __('Settings', 'feeds-for-tiktok'), __('Settings', 'feeds-for-tiktok'), 'manage_options', $this->menu['menu_slug'], [$this, 'settings_page_output'], 2);
        add_action('load-' . $settings_builder, [$this, 'settings_enqueue_admin_scripts']);
    }
    /**
     * Enqueue Builder CSS & Script.
     *
     * Loads only for builder pages
     *
     * @since 1.0
     */
    public function settings_enqueue_admin_scripts()
    {
        $settings_data = ['ajaxHandler' => admin_url('admin-ajax.php'), 'adminPostURL' => admin_url('post.php'), 'widgetsPageURL' => admin_url('widgets.php'), 'iconsList' => \Smashballoon\Customizer\V3\SB_Utils::get_icons(), 'reactScreen' => 'settings', 'settingsData' => $this->settingspage_builder_data()];
        $settings_js_file = SBTT_CUSTOMIZER_ASSETS . '/build/static/js/main.js';
        if (!\Smashballoon\Customizer\V3\SB_Utils::is_production()) {
            $settings_js_file = "http://localhost:3000/static/js/main.js";
        } else {
            wp_enqueue_style('sb-customizer-style', SBTT_CUSTOMIZER_ASSETS . '/build/static/css/main.css', \false, \false);
        }
        $settings_data = array_merge($settings_data, $this->customSettingsData());
        //Data comming from the Actual plugin
        wp_enqueue_script('sb-customizer-app', $settings_js_file, array('wp-i18n', 'jquery'), \false, \true);
        wp_localize_script('sb-customizer-app', 'sb_customizer', $settings_data);
        wp_enqueue_media();
        wp_set_script_translations('sb-customizer-app', 'feeds-for-tiktok', SBTT_PLUGIN_DIR . 'languages/');
    }
    /**
     * Build Settings Page Data
     * Will create an array that contains all the Settings Page
     *
     * @since 1.0
     * @return array
     */
    public function settingspage_builder_data()
    {
        $settings_page_data = [];
        $index = $tab_index = 0;
        /* Require Directly Tab Classes files */
        foreach (scandir($this->settingspage_tabs_path) as $filename) {
            $path = $this->settingspage_tabs_path . '/' . $filename;
            if (is_file($path)) {
                require $path;
                $tab_name = $this->settingspage_tabs_namespace . str_replace('.php', '', $filename);
                if (class_exists($tab_name) && is_subclass_of($tab_name, \Smashballoon\Customizer\V3\SB_SettingsPage_Tab::class)) {
                    $tab_class = new $tab_name();
                    $tab_content = $tab_class->get_tab();
                    $tab_index = ($this->tabs_order !== null) ? array_search($tab_content['id'], $this->tabs_order) : $index;
                    $settings_page_data[$tab_index] = $tab_content;
                    $index++;
                }
            }
        }
        return $settings_page_data;
    }
    public function customSettingsData()
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
    public function settings_page_output()
    {
        ?>
        <div id="sb-app" class="sb-fs"></div>
    <?php 
    }
}
