<?php

/**
 * Service responsible with plugin uninstall functionality.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Services;

use SmashBalloon\TikTokFeeds\Common\Container;

/**
 * Plugin Uninstall Service class.
 */
class UninstallService
{
	/**
	 * Register.
	 *
	 * @return void
	 */
	public function register()
	{
		register_uninstall_hook(SBTT_PLUGIN_FILE, [ self::class, 'uninstall' ]);
	}

	/**
	 * Remove plugin database data. Drop tables when the plugin is deleted from WordPress Admin Plugins page.
	 *
	 * @return void
	 */
	public static function uninstall()
	{
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$is_pro = defined('SBTT_PRO') && SBTT_PRO === true;

		$installed_plugins = get_plugins();

		$free_plugin_is_installed = isset($installed_plugins['feeds-for-tiktok/feeds-for-tiktok.php']);
		$pro_plugin_is_installed = isset($installed_plugins['tiktok-feeds-pro/tiktok-feeds-pro.php']);

		if (($is_pro && $free_plugin_is_installed) || (!$is_pro && $pro_plugin_is_installed)) {
			// Do nothing.
			return;
		}

		$global_settings = get_option('sbtt_global_settings');

		if (isset($global_settings['preserve_settings']) && $global_settings['preserve_settings']) {
			return;
		}

		self::delete_db_tables();

		self::delete_options();

		self::delete_cron_jobs();

		self::delete_upload_folder();
	}

	/**
	 * Remove plugin database data.
	 *
	 * @return void
	 */
	public static function delete_db_tables()
	{
		Container::get_instance()->get('DBManager')->drop_db_tables();
	}

	/**
	 * Remove plugin options.
	 *
	 * @return void
	 */
	public static function delete_options()
	{
		$options_to_delete = [
			'sbtt_global_settings',
			'sbtt_statuses',
			'sbtt_db_version',
			'sbtt_usage_tracking_config',
			'sbtt_newuser_notifications',
			'sbtt_notifications',
			'sbtt_resize_images_data'
		];

		foreach ($options_to_delete as $option) {
			delete_option($option);
		}
	}

	/**
	 * Remove plugin cron jobs.
	 *
	 * @return void
	 */
	public static function delete_cron_jobs()
	{
		$cron_jobs = [
			'sbtt_refresh_token_routine',
			'sbtt_feed_update_routine',
			'sbtt_usage_tracking_cron',
		];

		foreach ($cron_jobs as $cron_job) {
			wp_clear_scheduled_hook($cron_job);
		}
	}

	/**
	 * Remove plugin upload folder.
	 *
	 * @return void
	 */
	public static function delete_upload_folder()
	{
		$upload     = wp_upload_dir();
		$upload_dir = trailingslashit($upload['basedir']) . SBTT_UPLOAD_FOLDER_NAME;

		if (file_exists($upload_dir)) {
			$files = glob($upload_dir . '/*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}

			global $wp_filesystem;
			$wp_filesystem->delete($upload_dir, true);
		}
	}
}
