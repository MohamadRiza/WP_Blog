<?php
/**
 * Platform Data
 *
 * @package Platform_Data
 */
namespace CustomFacebookFeed;

use CustomFacebookFeed\Builder\CFF_Db;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Class Platform_Data
 *
 * Handles all data related to the platform.
 *
 * @since 4.2
 *
 * @package CustomFacebookFeed
 */
class Platform_Data
{

	/**
	 * Option key for app statuses.
	 *
	 * @var string
	 */
	const CFF_STATUSES_OPTION_KEY = 'cff_statuses';

	/**
	 * Option key for the revoke platform data.
	 *
	 * @var string
	 */
	const REVOKE_PLATFORM_DATA_OPTION_KEY = 'cff_revoke_platform_data';

	/**
	 * Array key for the app permission status key on `cff_statuses`.
	 *
	 * @var string
	 */
	const APP_PERMISSION_REVOKED_STATUS_KEY = 'app_permission_revoked';

	/**
	 * Array key for the warning email flag for unused feed status key on `cff_statuses`.
	 */
	const UNUSED_FEED_WARNING_EMAIL_SENT_STATUS_KEY = 'unused_feed_warning_email_sent';

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	public function register_hooks()
	{
		add_action('cff_api_connect_response', [$this, 'handle_platform_data_on_api_response'], 10, 2);
		add_action('cff_before_display_facebook', [$this, 'handle_app_permission_error'], 10);
		add_action('cff_app_permission_revoked', [$this, 'handle_app_permission_status'], 10, 1);
		add_action('cff_before_delete_old_data', [$this, 'handle_event_before_delete_old_data'], 10);

		// Ajax Hooks
		add_action('wp_ajax_cff_reset_unused_feed_usage', [$this, 'handle_unused_feed_usage'], 10);
	}

	/**
	 * Handle the platform data on the API response.
	 *
	 * @param array $response The response from the API.
	 * @param string $url The URL of the request.
	 *
	 * @return void
	 */
	public function handle_platform_data_on_api_response($response, $url)
	{
		if (is_wp_error($response)) {
			return;
		}

		if (empty($response['response']) || empty($response['response']['code'])) {
			return;
		}

		if ($response['response']['code'] !== 200) {
			return;
		}

		\cff_main()->cff_error_reporter->remove_error('platform_data_deleted');

		$cff_statuses_option = get_option(self::CFF_STATUSES_OPTION_KEY, []);

		if (empty($cff_statuses_option[self::APP_PERMISSION_REVOKED_STATUS_KEY])) {
			return;
		}
		$cff_revoke_platform_data = get_option(self::REVOKE_PLATFORM_DATA_OPTION_KEY, []);
		$revoked_account_username = isset($cff_revoke_platform_data['connected_account']['name']) ? $cff_revoke_platform_data['connected_account']['name'] : '';

		if (empty($revoked_account_username)) {
			return;
		}
		$api_response_username = json_decode($response['body'])->name;
		if ($revoked_account_username !== $api_response_username) {
			return;
		}

		// Cleanup the revoked platform status and revoke account data.
		$this->cleanup_revoked_account($cff_statuses_option);
		\cff_main()->cff_error_reporter->reset_api_errors();
	}

	/**
	 * Handle the app permission error.
	 *
	 * @return void
	 */
	public function handle_app_permission_error()
	{
		$cff_statuses_option = get_option(self::CFF_STATUSES_OPTION_KEY, []);

		if (empty($cff_statuses_option[self::APP_PERMISSION_REVOKED_STATUS_KEY])) {
			return;
		}

		$cff_revoke_platform_data = get_option(self::REVOKE_PLATFORM_DATA_OPTION_KEY, []);

		$revoke_platform_data_timestamp = isset($cff_revoke_platform_data['revoke_platform_data_timestamp']) ? $cff_revoke_platform_data['revoke_platform_data_timestamp'] : 0;
		$connected_account = isset($cff_revoke_platform_data['connected_account']) ? $cff_revoke_platform_data['connected_account'] : [];

		if (!$revoke_platform_data_timestamp) {
			return;
		}

		$current_timestamp = time();

		// Check if current timestamp is less than revoke platform data timestamp, if so, return.
		if ($current_timestamp < $revoke_platform_data_timestamp) {
			return;
		}

		// Revoke platform data.
		$this->delete_platform_data($connected_account);
		$this->send_platform_data_delete_notification_email();
		// Cleanup the revoked platform status and revoke account data.
		$this->cleanup_revoked_account($cff_statuses_option);
		\cff_main()->cff_error_reporter->reset_api_errors();
		\cff_main()->cff_error_reporter->add_error(
			'platform_data_deleted',
			array(
				__('An account admin has deauthorized the Smash Balloon app used to power the Facebook Feed plugin. The page was not reconnected within the 7 day limit and all Facebook data was automatically deleted on your website due to Facebook data privacy rules.', 'custom-facebook-feed')
			)
		);
	}

	/**
	 * Handle the app permission status.
	 *
	 * @param array $connected_account The connected account data.
	 *
	 * @return void
	 */
	public function handle_app_permission_status($connected_account)
	{
		$cff_statuses_option = get_option(self::CFF_STATUSES_OPTION_KEY, []);
		if (isset($cff_statuses_option['app_permission_revoked']) && true === $cff_statuses_option['app_permission_revoked']) {
			return;
		}

		$this->update_app_permission_revoked_status($cff_statuses_option, true);

		// Calculate the grace period for revoking platform data.
		$current_timestamp = time();
		$revoke_platform_data_timestamp = strtotime('+7 days', $current_timestamp);

		update_option(
			self::REVOKE_PLATFORM_DATA_OPTION_KEY,
			[
				'revoke_platform_data_timestamp' => $revoke_platform_data_timestamp,
				'connected_account' => $connected_account,
			]
		);
		$this->send_revoke_notification_email();
	}

	/**
	 * Delete any data associated with the Facebook API and the
	 * connected account being deleted.
	 *
	 * @param $to_delete_connected_account
	 *
	 * @return void
	 */
	protected function delete_platform_data($to_delete_connected_account)
	{
		$all_connected_accounts = get_connected_accounts_list();
		$to_update = [];
		foreach ($all_connected_accounts as $connected_account) {
			if ((int) $connected_account['id'] !== (int) $to_delete_connected_account['id']) {
				$to_update[$connected_account['id']] = $connected_account;
			}
		}
		update_connected_accounts($to_update);
		CFF_Db::delete_source_by_id($to_delete_connected_account['id']);
		$manager = new SB_Facebook_Data_Manager();
		$manager->delete_caches();
	}

	/**
	 * Update the app permission revoked status.
	 *
	 * @param array $cff_statuses_option The option value.
	 * @param bool $is_revoked The revoke status.
	 *
	 * @return void
	 */
	protected function update_app_permission_revoked_status($cff_statuses_option, $is_revoked)
	{
		if ($is_revoked) {
			$cff_statuses_option[self::APP_PERMISSION_REVOKED_STATUS_KEY] = true;
		} else {
			unset($cff_statuses_option[self::APP_PERMISSION_REVOKED_STATUS_KEY]);
		}
		update_option(self::CFF_STATUSES_OPTION_KEY, $cff_statuses_option);
	}

	/**
	 * Handles events before the deletion of old data.
	 *
	 * @param array $statuses
	 *
	 * @return void
	 */
	public function handle_event_before_delete_old_data($statuses)
	{
		$cff_statuses_option = get_option(self::CFF_STATUSES_OPTION_KEY, []);
		if (!empty($cff_statuses_option[self::UNUSED_FEED_WARNING_EMAIL_SENT_STATUS_KEY])) {
			return;
		}

		if ($statuses['last_used'] < cff_get_current_time() - (14 * DAY_IN_SECONDS)) {
			\cff_main()->cff_error_reporter->add_error(
				'unused_feed',
				array(
					__('Your Facebook feed has been not viewed in the last 14 days. Due to Facebook data privacy rules, all data for this feed will be deleted in 7 days time. To avoid automated data deletion, simply view the Facebook feed on your website within the next 7 days.', 'custom-facebook-feed')
				)
			);
			$this->send_unused_feed_usage_notification_email();
			// Setting the flag to true so that the warning email is not sent again.
			$cff_statuses_option[self::UNUSED_FEED_WARNING_EMAIL_SENT_STATUS_KEY] = true;
			update_option(self::CFF_STATUSES_OPTION_KEY, $cff_statuses_option);
		}
	}

	/**
	 * Handles the reset of unused feed data for deletion.
	 *
	 * @return void
	 */
	public function handle_unused_feed_usage()
	{
		//Security Checks
		check_ajax_referer('cff_nonce', 'cff_nonce');
		$cap = current_user_can('manage_custom_facebook_feed_options') ? 'manage_custom_facebook_feed_options' : 'manage_options';
		if (!current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}

		\cff_main()->cff_error_reporter->remove_error('unused_feed');

		//##############
		$manager = new SB_Facebook_Data_Manager();
		$manager->update_last_used();
		$cff_statuses_option = get_option(self::CFF_STATUSES_OPTION_KEY, []);
		// Unset the flag to allow the warning email to be sent again.
		unset($cff_statuses_option[self::UNUSED_FEED_WARNING_EMAIL_SENT_STATUS_KEY]);
		update_option(self::CFF_STATUSES_OPTION_KEY, $cff_statuses_option);

		wp_send_json_success(
			[
				'message' => '<div style="margin-top: 10px;">' . esc_html__('Success! Your Facebook Feeds will continue to work normally.', 'custom-facebook-feed') . '</div>'
			]
		);
	}

	/**
	 * Cleanup revoked account data.
	 *
	 * @param array $cff_statuses_option
	 *
	 * @return void
	 */
	public function cleanup_revoked_account($cff_statuses_option)
	{
		$this->update_app_permission_revoked_status($cff_statuses_option, false);
		delete_option(self::REVOKE_PLATFORM_DATA_OPTION_KEY);
	}

	/**
	 * Sends a notification email to the admin when the app permission is revoked.
	 *
	 * @return void
	 */
	protected function send_revoke_notification_email()
	{
		$link = admin_url('admin.php?page=cff-settings');

		$title = __('There has been a problem with your Facebook Feed.', 'custom-facebook-feed');
		$bold = __('Action Required Within 7 Days', 'custom-facebook-feed');
		$site_url = sprintf('<a href="%s">%s<a/>', esc_url(home_url()), __('your website', 'custom-facebook-feed'));
		$details = '<p>' . sprintf(__('An account admin has deauthorized the Smash Balloon app used to power the Facebook Feed plugin on %s. If the Facebook source is not reconnected within 7 days then all Facebook data will be automatically deleted on your website due to Facebook data privacy rules.', 'custom-facebook-feed'), $site_url) . '</p>';
		$settings_page = sprintf('<a href="%s">%s</a>', esc_url($link), esc_html__('Settings Page', 'custom-facebook-feed'));
		$details .= '<p>' . sprintf(__('To prevent the automated deletion of data for the source, please reconnect your source for the Facebook Feed plugin %s within 7 days.', 'custom-facebook-feed'), $settings_page) . '</p>';
		$details .= '<p><a href="https://smashballoon.com/doc/action-required-within-7-days/?facebook&utm_campaign=facebook-pro&utm_source=permissionerror&utm_medium=email&utm_content=More Information" target="_blank" rel="noopener">' . __('More Information', 'custom-facebook-feed') . '</a></p>';

		Email_Notification::send($title, $bold, $details);
	}

	/**
	 * Sends a notification email to the admin when the feed has not been used for a while.
	 *
	 * @return void
	 */
	protected function send_unused_feed_usage_notification_email()
	{
		$title = __('There has been a problem with your Facebook Feed.', 'custom-facebook-feed');
		$bold = __('Action Required Within 7 Days', 'custom-facebook-feed');
		$site_url = sprintf('<a href="%s">%s<a/>', esc_url(home_url()), __('your website', 'custom-facebook-feed'));
		$details = '<p>' . sprintf(__('An Facebook feed on %s has been not viewed in the last 14 days. Due to Facebook data privacy rules, all data for this feed will be deleted in 7 days time.', 'custom-facebook-feed'), $site_url) . '</p>';
		$details .= '<p>' . __('To avoid automated data deletion, simply view the Facebook feed on your website within the next 7 days.', 'custom-facebook-feed') . '</p>';
		Email_Notification::send($title, $bold, $details);
	}

	/**
	 * Sends a notification email to the admin when the platform data has been deleted.
	 *
	 * @return void
	 */
	protected function send_platform_data_delete_notification_email()
	{
		$link = admin_url('admin.php?page=cff-settings');

		$title = __('All Facebook Data has Been Removed', 'custom-facebook-feed');
		$bold = __('An account admin has deauthorized the Smash Balloon app used to power the Facebook Feed plugin.', 'custom-facebook-feed');
		$site_url = sprintf('<a href="%s">%s<a/>', esc_url(home_url()), __('your website', 'custom-facebook-feed'));
		$details = '<p>' . sprintf(__('The page was not reconnected within the 7 day limit and all Facebook data was automatically deleted on %s due to Facebook data privacy rules.', 'custom-facebook-feed'), $site_url) . '</p>';
		$settings_page = sprintf('<a href="%s">%s</a>', esc_url($link), esc_html__('Settings Page', 'custom-facebook-feed'));
		$details .= '<p>' . sprintf(__('To fix your feeds, reconnect all accounts that were in use on the Settings page.', 'custom-facebook-feed'), $settings_page) . '</p>';

		Email_Notification::send($title, $bold, $details);
	}

	/**
	 * Sends a notification email to the admin when the platform data has been deleted.
	 *
	 * @return void
	 */
	protected function platform_data_deleted_notice()
	{

	}

}
?>