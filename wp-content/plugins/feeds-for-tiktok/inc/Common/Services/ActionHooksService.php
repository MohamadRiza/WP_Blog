<?php

namespace SmashBalloon\TikTokFeeds\Common\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Utils;

class ActionHooksService extends ServiceProvider
{
	/**
	 * Registers the action hooks for the plugin.
	 */
	public function register()
	{
		add_action('init', array($this, 'load_textdomain' ));
		add_action('admin_enqueue_scripts', array($this, 'dequeue_styles'), 11);

		add_action('sbtt_enqueue_scripts', array( $this, 'register_scripts' ));
		add_action('wp_enqueue_scripts', array( $this, 'register_scripts' ));
		add_action('wp_enqueue_scripts', array( $this, 'set_script_translations' ), 11);

		add_action('wpcode_loaded', array($this, 'register_username'));
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_textdomain()
	{
		load_plugin_textdomain('feeds-for-tiktok', false, dirname(SBTT_PLUGIN_BASENAME) . '/languages');
	}

	/**
	 * Dequeue styles.
	 *
	 * @return void
	 */
	public function dequeue_styles()
	{
		$current_screen = get_current_screen();

		if (! $current_screen || ! isset($current_screen->id)) {
			return;
		}

		if (strpos($current_screen->id, 'sbtt') !== false) {
			wp_dequeue_style('cff_custom_wp_admin_css');
			wp_deregister_style('cff_custom_wp_admin_css');

			wp_dequeue_style('feed-global-style');
			wp_deregister_style('feed-global-style');

			wp_dequeue_style('sb_instagram_admin_css');
			wp_deregister_style('sb_instagram_admin_css');

			wp_dequeue_style('ctf_admin_styles');
			wp_deregister_style('ctf_admin_styles');
		}
	}

	/**
	 * Register the plugin's scripts and styles.
	 *
	 * @param bool $enqueue Whether to enqueue the scripts and styles.
	 *
	 * @return void
	 */
	public function register_scripts($enqueue = false)
	{
		$feed_js_file = SBTT_CUSTOMIZER_ASSETS . '/build/static/js/tikTokFeed.js';

		if (! Utils::isProduction()) {
			$feed_js_file = "http://localhost:3000/static/js/tikTokFeed.js";
		} else {
			wp_register_style(
				'sbtt-tiktok-feed',
				SBTT_CUSTOMIZER_ASSETS . '/build/static/css/tikTokFeed.css',
				false,
				false
			);
		}

		wp_register_script('sbtt-tiktok-feed', $feed_js_file, array( 'wp-i18n', 'jquery' ), SBTTVER, true);

		$data = array(
			'ajaxHandler' => admin_url('admin-ajax.php'),
			'nonce'       => wp_create_nonce('sbtt-frontend'),
			'isPro'		  => Utils::sbtt_is_pro()
		);

		wp_localize_script('sbtt-tiktok-feed', 'sbtt_feed_options', $data);

		if ($enqueue) {
			wp_enqueue_script('sbtt-tiktok-feed');
			wp_enqueue_style('sbtt-tiktok-feed');
		}
	}

	/**
	 * Set script translations.
	 *
	 * @return void
	 */
	public function set_script_translations()
	{
		wp_set_script_translations('sbtt-tiktok-feed', 'feeds-for-tiktok', SBTT_PLUGIN_DIR . 'languages/');
	}

	/**
	 * Register the username for the WPCode snippets.
	 *
	 * @return void
	 */
	public function register_username()
	{
		if (!function_exists('wpcode_register_library_username')) {
			return;
		}

		wpcode_register_library_username('smashballoon', 'Smash Balloon');
	}
}
