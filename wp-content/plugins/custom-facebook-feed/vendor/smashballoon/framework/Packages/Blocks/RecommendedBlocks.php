<?php

/**
 * Description: Recommended blocks for suggesting other Awesome Motive plugins.
 * Version:     1.0
 * Author:      Awesome Motive, Inc.
 * Author URI:  https://awesomemotive.com/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
namespace Smashballoon\Framework\Packages\Blocks;

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
use Plugin_Installer_Skin;
use Plugin_Upgrader;
use WP_Error;
use function Smashballoon\Framework\sb_get_active_plugins;
/**
 * Recommended Blocks class.
 * @internal
 */
class RecommendedBlocks
{
    /**
     * List of the recommended blocks.
     */
    protected $recommended_blocks;
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Each plugin has to be added here. Keep aplhabetical order for easier reading.
        $this->recommended_blocks = ['aioseo' => ['plugin' => 'all-in-one-seo-pack', 'title' => 'Custom SEO', 'keywords' => ['seo']], 'duplicator' => ['plugin' => 'duplicator', 'title' => 'Custom Duplicator', 'keywords' => ['duplicator']], 'edd' => ['plugin' => 'easy-digital-downloads', 'title' => 'Custom Digital Store', 'keywords' => ['digital store', 'payments', 'ecommerce']], 'monster_insights' => ['plugin' => 'google-analytics-for-wordpress', 'title' => 'Google Analytics', 'keywords' => ['google analytics']], 'optin_monster' => ['plugin' => 'optinmonster', 'title' => 'Custom PopUp', 'keywords' => ['popup']], 'rafflepress' => ['plugin' => 'rafflepress', 'title' => 'Custom Giveaway', 'keywords' => ['giveaway', 'contests']], 'sb_facebook_feed' => ['plugin' => 'custom-facebook-feed', 'pro' => 'custom-facebook-feed-pro', 'title' => 'Custom Facebook Feed', 'keywords' => ['Facebook', 'Facebook feed', 'Facebook posts', 'Facebook account', 'Facebook page']], 'sb_instagram_feed' => ['plugin' => 'instagram-feed', 'pro' => 'instagram-feed-pro', 'title' => 'Custom Instagram Feed', 'keywords' => ['Instagram', 'Instagram feed', 'Instagram photos', 'Instagram widget', 'Instagram gallery', 'social feed']], 'sb_reviews_feed' => ['plugin' => 'reviews-feed', 'pro' => 'reviews-feed-pro', 'title' => 'Custom Reviews Feed', 'keywords' => ['Google reviews', 'reviews', 'testimonials', 'yelp', 'Google business']], 'sb_tiktok_feed' => ['plugin' => 'feeds-for-tiktok', 'pro' => 'tiktok-feeds-pro', 'title' => 'Custom TikTok Feeds', 'keywords' => ['TikTok', 'TikTok feed', 'TikTok videos', 'TikTok account', 'TikTok widget']], 'sb_twitter_feed' => ['plugin' => 'custom-twitter-feeds', 'pro' => 'custom-twitter-feeds-pro', 'title' => 'Custom Twitter Feeds', 'keywords' => ['Twitter', 'Twitter feed', 'X feed', 'Twitter widget', 'Custom Twitter Feed']], 'sb_youtube_feed' => ['plugin' => 'feeds-for-youtube', 'pro' => 'youtube-feed-pro', 'title' => 'Custom Youtube Feed', 'keywords' => ['YouTube', 'YouTube feed', 'YouTube widget', 'YouTube channel', 'YouTube gallery']], 'sugar_calendar' => ['plugin' => 'sugar-calendar', 'title' => 'Custom Calendar', 'keywords' => ['event', 'calendar', 'sugar calendar']], 'wp_mail_smtp' => ['plugin' => 'wp-mail-smtp', 'title' => 'Custom emails', 'keywords' => ['mail', 'smtp']], 'wpcode' => ['plugin' => 'insert-headers-and-footers', 'title' => 'Custom code blocks', 'keywords' => ['code', 'css', 'functions', 'snippet']], 'wpforms' => ['plugin' => 'wpforms', 'title' => 'Custom Form', 'keywords' => ['form']], 'wpforms-survey' => ['plugin' => 'wpforms', 'title' => 'Custom Survey', 'keywords' => ['survey']]];
    }
    /**
     * Setup.
     */
    public function setup()
    {
        add_action('init', [$this, 'register_blocks']);
        add_action('wp_ajax_am_faux_install', [$this, 'install_plugin']);
        add_action('admin_enqueue_scripts', [$this, 'install_scripts']);
    }
    /**
     * Register all the blocks we need. The title and the keywords are added from here.
     */
    public function register_blocks()
    {
        $active_plugins = sb_get_active_plugins();
        $checks = [];
        foreach ($active_plugins as $plugin) {
            $check = \strtok($plugin, '/');
            $check = \str_replace('-lite', '', $check);
            $checks[] = $check;
        }
        foreach ($this->recommended_blocks as $block => $info) {
            $json_path = __DIR__ . '/build/' . \strtolower($block) . '/block.json';
            $block_data = $this->parse_json_file($json_path);
            if (empty($block_data)) {
                continue;
            }
            $block_name = isset($block_data['name']) ? $block_data['name'] : 'am/faux-' . $block;
            if (\WP_Block_Type_Registry::get_instance()->is_registered($block_name)) {
                continue;
            }
            $is_free = isset($info['plugin']) && \in_array($info['plugin'], $checks);
            $is_pro = isset($info['pro']) && \in_array($info['pro'], $checks);
            if (!$is_free && !$is_pro) {
                $block_path = __DIR__ . '/build/' . \strtolower($block);
                if (\is_dir($block_path)) {
                    register_block_type(__DIR__ . '/build/' . \strtolower($block), ['title' => !empty($info['title']) ? $info['title'] : '', 'keywords' => !empty($info['keywords']) ? $info['keywords'] : '']);
                }
            }
        }
    }
    /**
     * Function that receives a path to a JSON file and parses it
     *
     * @param string $path
     * 
     * @return array
     */
    public function parse_json_file($path)
    {
        if (!\file_exists($path)) {
            return [];
        }
        $json = \file_get_contents($path);
        $data = \json_decode($json, \true);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            return [];
        }
        return $data;
    }
    /**
     * Enqueue the needed scripts.
     */
    public function install_scripts()
    {
        wp_register_script('faux_js', \false);
        wp_enqueue_script('faux_js');
        wp_localize_script('faux_js', 'fauxData', ['siteUrl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('am_faux_install')]);
    }
    /**
     * Install the plugin.
     */
    public function install_plugin()
    {
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        if (!current_user_can('install_plugins')) {
            $error = new WP_Error('no_permission', 'You do not have permission to install plugins.');
            wp_send_json_error($error);
        }
        if (empty($_REQUEST['nonce']) || !wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'am_faux_install')) {
            $error = new WP_Error('nonce_failure', 'The nonce was not valid.');
            wp_send_json_error($error);
        }
        if (empty($_REQUEST['plugin'])) {
            $error = new WP_Error('missing_file', 'The plugin file was not specified.');
            wp_send_json_error($error);
        }
        $plugin_file = sanitize_text_field($_REQUEST['plugin']);
        $slug = \strtok($plugin_file, '/');
        $plugin_dir = WP_PLUGIN_DIR . '/' . $slug;
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        if (!\is_dir($plugin_dir)) {
            $api = plugins_api('plugin_information', ['slug' => $slug, 'fields' => ['short_description' => \false, 'sections' => \false, 'requires' => \false, 'rating' => \false, 'ratings' => \false, 'downloaded' => \false, 'last_updated' => \false, 'added' => \false, 'tags' => \false, 'compatibility' => \false, 'homepage' => \false, 'donate_link' => \false]]);
            $skin = new Plugin_Installer_Skin(['api' => $api]);
            $upgrader = new Plugin_Upgrader($skin);
            $install = $upgrader->install($api->download_link);
            if ($install !== \true) {
                $error = new WP_Error('failed_install', 'The plugin install failed.');
                wp_send_json_error($error);
            }
        }
        if (\file_exists($plugin_path)) {
            activate_plugin($plugin_path);
            $this->disable_installed_plugins_redirect();
            wp_redirect(get_permalink());
        } else {
            $error = new WP_Error('failed_activation', 'The plugin activation failed.');
            wp_send_json_error($error);
        }
        wp_die();
    }
    /**
     * Disable the redirect to the 3rd party plugin's welcome page.
     *
     * @return void
     */
    public function disable_installed_plugins_redirect()
    {
        // All in one SEO
        update_option('aioseo_activation_redirect', \true);
        // WPForms
        update_option('wpforms_activation_redirect', \true);
        // Seed PROD
        update_option('seedprod_dismiss_setup_wizard', \true);
        // PushEngage
        delete_transient('pushengage_activation_redirect');
        // OptinMonster
        delete_transient('optin_monster_api_activation_redirect');
        update_option('optin_monster_api_activation_redirect_disabled', \true);
        // Duplicator
        add_option('duplicator_redirect_to_welcome');
        // MonsterInsights
        delete_transient('_monsterinsights_activation_redirect');
        // Smash Balloon plugins
        $this->disable_smash_balloon_redirect();
    }
    /**
     * Disable the redirect to Smash Balloon's plugin welcome page after activation.
     *
     * @return void
     */
    public function disable_smash_balloon_redirect()
    {
        $smash_list = ['facebook' => 'cff_plugin_do_activation_redirect', 'instagram' => 'sbi_plugin_do_activation_redirect', 'youtube' => 'sby_plugin_do_activation_redirect', 'twitter' => 'ctf_plugin_do_activation_redirect', 'reviews' => 'sbr_plugin_do_activation_redirect'];
        foreach ($smash_list as $plugin => $option) {
            delete_option($option);
        }
    }
}
