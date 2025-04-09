<?php

namespace SmashBalloon\TikTokFeeds\Common\Services;

use SmashBalloon\TikTokFeeds\Common\Helpers\PluginSilentUpgrader;
use SmashBalloon\TikTokFeeds\Common\Helpers\InstallSkin;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Util;

class PluginInstallerService extends ServiceProvider
{
	/**
	 * Registers the PluginInstallerService.
	 *
	 * This method initializes the PluginInstallerService.
	 */
	public function register()
	{
		$this->init();
	}

	/**
	 * Initializes the PluginInstallerService.
	 * This method adds the necessary actions for plugin installation, activation, and deactivation.
	 * It should be called within the WordPress admin area.
	 */
	public function init()
	{
		if (! is_admin()) {
			return;
		}

		add_action('wp_ajax_sbtt_install_plugin', [ $this, 'install_plugin' ]);
		add_action('wp_ajax_sbtt_activate_plugin', [ $this, 'activate_plugin' ]);
		add_action('wp_ajax_sbtt_deactivate_plugin', [ $this, 'deactivate_plugin' ]);
	}

	/**
	 * Install Plugin.
	 *
	 * @since 1.0.0
	 */
	public function install_plugin()
	{

		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$error = esc_html__('Could not install addon. Please download from smashballoon.com and install manually.', 'feeds-for-tiktok');

		if (empty($_POST['plugin'])) {
			wp_send_json_error($error);
		}

		// Set the current screen to avoid undefined notices.
		set_current_screen('sbtt-about');

		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'sbtt-about',
				),
				admin_url('admin.php')
			)
		);

		$creds = request_filesystem_credentials($url, '', false, false, null);

		// Check for file system permissions.
		if (false === $creds) {
			wp_send_json_error($error);
		}

		if (! WP_Filesystem($creds)) {
			wp_send_json_error($error);
		}

		/*
		 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		 */

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action('upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20);

		// Create the plugin upgrader with our custom skin.
		$installer = new PluginSilentUpgrader(new InstallSkin());
		// Error check.
		if (! method_exists($installer, 'install') || empty($_POST['plugin'])) {
			wp_send_json_error($error);
		}

        $installer->install(sanitize_text_field(wp_unslash($_POST['plugin']))); // phpcs:ignore

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin_basename = $installer->plugin_info();

		if ($plugin_basename) {
			// Activate the plugin silently.
			$activated = activate_plugin($plugin_basename);

			// TODO:: fill in plugins and recommendedPlugins.
			wp_send_json_success(
				[
					'plugins'            => array(),
					'recommendedPlugins' => array(),
					'message'            => ! is_wp_error($activated) ? __('Plugin Installed & Activated.', 'feeds-for-tiktok') : __('Plugin Installed.', 'feeds-for-tiktok'),
				]
			);
		}

		wp_send_json_error($error);
	}

	/**
	 * Activate Plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate_plugin()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['plugin'])) {
			$plugin   = sanitize_text_field(wp_unslash($_POST['plugin']));
			$activate = activate_plugins($plugin);

			// TODO:: fill in plugins and recommendedPlugins.
			wp_send_json_success(
				[
					'plugins'            => [],
					'recommendedPlugins' => [],
				]
			);
		}

		wp_send_json_error(
			__('Could not Activate the Plugin. Please Activate from the Plugins page.', 'feeds-for-tiktok')
		);
	}

	/**
	 * Deactivate Plugin.
	 *
	 * @since 1.0.0
	 */
	public function deactivate_plugin()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		if (isset($_POST['plugin'])) {
			$plugin     = sanitize_text_field(wp_unslash($_POST['plugin']));
			$deactivate = deactivate_plugins($plugin);

			// TODO:: fill in plugins and recommendedPlugins.
			wp_send_json_success(
				[
					'plugins'            => [],
					'recommendedPlugins' => [],
				]
			);
		}

		wp_send_json_error(
			__('Could not deactivate the Plugin. Please deactivate from the Plugins page.', 'feeds-for-tiktok')
		);
	}
}
