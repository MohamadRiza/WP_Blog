<?php

/**
 * Service responsible with plugin global settings functionality.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Utils;

/**
 * Class SettingsManagerService
 */
class SettingsManagerService extends ServiceProvider
{
	/**
	 * Options name for the global settings.
	 *
	 * @var string
	 */
	private $settings_options = 'sbtt_global_settings';

	/**
	 * Register the service.
	 */
	public function register()
	{
		add_action('wp_ajax_sbtt_update_global_settings', array( $this, 'ajax_update_global_settings' ));
	}

	/**
	 * Update the global settings.
	 */
	public function ajax_update_global_settings()
	{
		check_ajax_referer('sbtt-admin', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error();
		}

		unset($_POST['action'], $_POST['nonce']);

		$settings = isset($_POST['settings']) ? sbtt_sanitize_data($_POST['settings']) : [];

		$this->update_global_settings($settings);

		wp_send_json_success();
	}

	/**
	 * Update the global settings.
	 *
	 * @param array $settings The settings to update.
	 */
	public function update_global_settings($settings)
	{
		if (! is_array($settings) || empty($settings)) {
			return;
		}

		$current_settings = $this->get_global_settings();
		$updated_settings = wp_parse_args($settings, $current_settings);

		update_option($this->settings_options, $updated_settings);
	}

	/**
	 * Get the global settings.
	 *
	 * @return array
	 */
	public function get_global_settings()
	{
		$defaults = $this->get_global_settings_defaults();
		$settings = get_option($this->settings_options, []);
		$settings = wp_parse_args($settings, $defaults);

		return $settings;
	}

	/**
	 * Get the global settings defaults.
	 *
	 * @return array
	 */
	private function get_global_settings_defaults()
	{
		return [
			'optimize_images'     => true,
			'usagetracking'       => Utils::sbtt_is_pro() ? true : false,
			'admin_error_notices' => true,
			'feed_issue_reports'  => true,
		];
	}
}
