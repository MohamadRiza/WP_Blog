<?php

namespace CustomFacebookFeed\Integrations\Divi;

use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\Integrations\CFF_Integration;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Divi Handler.
 *
 * @since 4.3
 */
class CFF_Divi_Handler
{
    /**
     * Constructor.
     *
     * @since 4.3
     */
    public function __construct()
    {
        $this->load();
    }


    /**
     * Indicate if current integration is allowed to load.
     *
     * @since 4.3
     *
     * @return bool
     */
    public function allow_load()
    {
        if (function_exists('et_divi_builder_init_plugin')) {
            return true;
        }

        $allow_themes = ['Divi'];
        $theme_name   = get_template();

        return in_array($theme_name, $allow_themes, true);
    }


    /**
     * Load an integration.
     *
     * @since 4.3
     */
    public function load()
    {
        if ($this->allow_load()) {
            $this->hooks();
        }
    }


    /**
     * Hooks.
     *
     * @since 4.3
     */
    public function hooks()
    {

        add_action('et_builder_ready', [ $this, 'register_module' ]);

        if (wp_doing_ajax()) {
            add_action('wp_ajax_sb_facebookfeed_divi_preview', [ $this, 'preview' ]);
        }

        if ($this->is_divi_builder()) {
            add_action('wp_enqueue_scripts', [ $this, 'builder_scripts' ]);
        }
    }

    /**
     * Load scripts.
     *
     * @since 4.3
     */
    public function builder_scripts()
    {

        wp_enqueue_script(
            'sbfacebook-divi',
            // The unminified version is not supported by the browser.
            CFF_PLUGIN_URL . 'admin/assets/js/divi-handler.min.js',
            [ 'react', 'react-dom', 'jquery' ],
            CFFVER,
            true
        );

        wp_enqueue_script(
            'cff-builders-handler',
            // The unminified version is not supported by the browser.
            CFF_PLUGIN_URL . 'admin/assets/js/builders-preview-handler.js',
            ['jquery'],
            CFFVER,
            true
        );

        wp_localize_script(
            'sbfacebook-divi',
            'sb_divi_builder',
            [
                'ajax_handler'		=> admin_url('admin-ajax.php'),
                'nonce'             => wp_create_nonce('cff-admin'),
                'feed_splash' 		=> htmlspecialchars(CFF_Integration::get_widget_cta('button'))
            ]
        );
        wp_register_script(
            'cffscripts',
            CFF_PLUGIN_URL . 'assets/js/cff-scripts.min.js',
            array('jquery'),
            CFFVER,
            true
        );
        wp_localize_script(
            'cffscripts',
            'cffOptions',
            [
                'placeholder' => CFF_PLUGIN_URL . 'assets/img/placeholder.png',
            ]
        );
    }


    /**
     * Register module.
     *
     * @since 4.3
     */
    public function register_module()
    {

        if (! class_exists('ET_Builder_Module')) {
            return;
        }

        new SBFacebookFeed();
    }


    /**
     * Ajax handler for the Feed preview.
     *
     * @since 4.3
     */
    public function preview()
    {

        //check_ajax_referer('cff-admin', 'nonce');

        $cap = current_user_can('manage_custom_facebook_feed_options') ? 'manage_custom_facebook_feed_options' : 'manage_options';
        $cap = apply_filters('cff_settings_pages_capability', $cap);
        if (! current_user_can($cap)) {
            wp_send_json_error(); // This auto-dies.
        }

        $feed_id    = absint(filter_input(INPUT_POST, 'feed_id', FILTER_SANITIZE_NUMBER_INT));

        wp_send_json_success(
            do_shortcode(
                sprintf(
                    '[custom-facebook-feed feed="%1$s" disable-js-loading="true"]',
                    absint($feed_id)
                )
            )
        );
    }

    /**
     * Determine if a current page is opened in the Divi Builder.
     *
     * @since 4.3
     *
     * @return bool
     */
    private function is_divi_builder()
    {
        return ! empty($_GET['et_fb']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }
}
