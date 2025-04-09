<?php

namespace SmashBalloon\YouTubeFeed\Services\Integrations\Divi;

use SmashBalloon\YouTubeFeed\Services\Integrations\SBY_Integration;
use SmashBalloon\YouTubeFeed\Helpers\Util;

/**
 * Class Divi Handler.
 *
 * @since 2.3
 */
class SBY_Divi_Handler
{
	/**
	 * Constructor.
	 *
	 * @since 2.3
	 */
	public function register()
	{
		$this->load();
	}


	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 2.3
	 *
	 * @return bool
	 */
	public function allow_load()
	{
		if (function_exists('et_divi_builder_init_plugin')) {
			return true;
		}
		$allow_themes = [ 'Divi' ];
		$theme_name = get_template();

		return in_array($theme_name, $allow_themes, true);
	}


	/**
	 * Load an integration.
	 *
	 * @since 2.3
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
	 * @since 2.3
	 */
	public function hooks()
	{
		add_action('et_builder_ready', [ $this, 'register_module' ]);

		if (wp_doing_ajax()) {
			add_action('wp_ajax_sb_youtubefeed_divi_preview', [ $this, 'preview' ]);
		}

		if ($this->is_divi_builder()) {
			add_action('wp_enqueue_scripts', [ $this, 'builder_scripts' ]);
		}
	}

	/**
	 * Load scripts.
	 *
	 * @since 2.3
	 */
	public function builder_scripts()
	{
		
		$css_free_file_name = 'sb-youtube-free.min.css';
		$css_pro_file_name = 'sb-youtube.min.css';
		$css_file_name = sby_is_pro() ? $css_pro_file_name : $css_free_file_name;

		wp_enqueue_style('sby_styles', trailingslashit(SBY_PLUGIN_URL) . 'css/' . $css_file_name, array(), SBYVER);

		$data = array(
		'isAdmin' => is_admin(),
		'adminAjaxUrl' => admin_url('admin-ajax.php'),
		'placeholder' => trailingslashit(SBY_PLUGIN_URL) . 'img/placeholder.png',
		'placeholderNarrow' => trailingslashit(SBY_PLUGIN_URL) . 'img/placeholder-narrow.png',
		'lightboxPlaceholder' => trailingslashit(SBY_PLUGIN_URL) . 'img/lightbox-placeholder.png',
		'lightboxPlaceholderNarrow' => trailingslashit(SBY_PLUGIN_URL) . 'img/lightbox-placeholder-narrow.png',
		'autoplay' => false,
		'semiEagerload' => false,
		'eagerload' => false,
		'nonce'    => wp_create_nonce('sby_nonce'),
		'isPro'    => sby_is_pro(),
		'resized_url' => Util::sby_get_resized_uploads_url(),
		'isCustomizer' => false
		);

		wp_enqueue_script(
			'sbyscripts',
			SBY_PLUGIN_URL . 'js/sb-youtube.min.js',
			array('jquery'),
			SBYVER,
			true
		);
		wp_localize_script('sbyscripts', 'sbyOptions', $data);

		wp_enqueue_script(
			'sbyoutube-divi',
			// The unminified version is not supported by the browser.
			SBY_PLUGIN_URL . 'js/divi-handler.min.js',
			['react', 'react-dom', 'jquery'],
			SBYVER,
			true
		);

		wp_enqueue_script(
			'sby-divi-handler',
			// The unminified version is not supported by the browser.
			SBY_PLUGIN_URL . 'js/divi-preview-handler.js',
			['jquery'],
			SBYVER,
			true
		);

		wp_localize_script(
			'sbyoutube-divi',
			'sb_divi_builder',
			[
			'ajax_handler'        => admin_url('admin-ajax.php'),
			'nonce'             => wp_create_nonce('sby-admin'),
			'feed_splash'         => htmlspecialchars(SBY_Integration::get_widget_cta('button'), ENT_QUOTES | ENT_HTML5)
			]
		);
	}

	/**
	 * Register module.
	 *
	 * @since 2.3
	 */
	public function register_module()
	{
		if (!class_exists('ET_Builder_Module')) {
			return;
		}

		new SB_YouTube_Feed();
	}


	/**
	 * Ajax handler for the Feed preview.
	 *
	 * @since 2.3
	 */
	public function preview()
	{
		check_ajax_referer('sby-admin', 'nonce');

		$cap = current_user_can('manage_youtube_feed_options') ? 'manage_youtube_feed_options' : 'manage_options';
		$cap = apply_filters('sby_settings_pages_capability', $cap);
		if (! current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}

		$feed_id = absint(filter_input(INPUT_POST, 'feed_id', FILTER_SANITIZE_NUMBER_INT));

		wp_send_json_success(
			do_shortcode(
				sprintf(
					'[youtube-feed feed="%1$s"]',
					absint($feed_id)
				)
			)
		);
	}

	/**
	 * Determine if a current page is opened in the Divi Builder.
	 *
	 * @since 2.3
	 *
	 * @return bool
	 */
	private function is_divi_builder()
	{
		return !empty($_GET['et_fb']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}
