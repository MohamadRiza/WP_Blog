<?php

namespace SmashBalloon\TikTokFeeds\Common\Services;

use SmashBalloon\TikTokFeeds\Common\Container;

/**
 * Plugin Activation Service class.
 */
class ActivationService
{
	/**
	 * Register.
	 *
	 * @return void
	 */
	public function register()
	{
		register_activation_hook(SBTT_PLUGIN_FILE, [ $this, 'activate' ]);
		add_action('activated_plugin', [ $this, 'onPluginActivation' ]);
	}

	/**
	 * Setup db tables.
	 *
	 * @return void
	 */
	public function activate()
	{
		Container::get_instance()->get('DBManager')->create_or_update_db_tables();
		$this->createUploadFolder();
		$this->addFirstInstall();
	}

	/**
	 * Create upload folder.
	 *
	 * @return void
	 */
	public function createUploadFolder()
	{
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$upload_dir = trailingslashit($upload_dir) . SBTT_UPLOAD_FOLDER_NAME;

		if (! file_exists($upload_dir)) {
			wp_mkdir_p($upload_dir);
		}
	}

	/**
	 * On plugin activation.
	 *
	 * @param string $plugin Plugin path.
	 * @return void
	 */
	public function onPluginActivation($plugin)
	{
		if (! in_array(basename($plugin), array( 'feeds-for-tiktok.php', 'tiktok-feeds-pro.php' ))) {
			return;
		}

		$plugin_to_deactivate = 'feeds-for-tiktok/feeds-for-tiktok.php';
		if (strpos($plugin, $plugin_to_deactivate) !== false) {
			$plugin_to_deactivate = 'tiktok-feeds-pro/tiktok-feeds-pro.php';
		}

		$active_plugins = $this->getActivePlugins();
		foreach ($active_plugins as $plugin) {
			if ($plugin === $plugin_to_deactivate) {
				deactivate_plugins($plugin);
				return;
			}
		}
	}

	/**
	 * Get active plugins.
	 *
	 * @return array
	 */
	private function getActivePlugins()
	{
		if (is_multisite()) {
			$active_plugins = array_keys((array)get_site_option('active_sitewide_plugins', array()));
		} else {
			$active_plugins = (array)get_option('active_plugins', array());
		}

		return $active_plugins;
	}

	/**
	 * Add a 'first_install' to sbtt_options table.
	 *
	 * @return void
	 */
	private function addFirstInstall()
	{
		$sbtt_statuses = get_option('sbtt_statuses', array());
		if (!isset($sbtt_statuses['first_install'])) {
			$sbtt_statuses['first_install'] = time();
			update_option('sbtt_statuses', $sbtt_statuses);
		}

		$sbtt_rating_notice = get_option('sbtt_rating_notice', false);
		$sbtt_rating_notice_waiting = get_transient('tiktok_feed_rating_notice_waiting');
		if ($sbtt_rating_notice_waiting === false && $sbtt_rating_notice === false) {
			$time = 2 * WEEK_IN_SECONDS;
			set_transient('tiktok_feed_rating_notice_waiting', 'waiting', $time);
			update_option('sbtt_rating_notice', 'pending', false);
		}
	}
}
