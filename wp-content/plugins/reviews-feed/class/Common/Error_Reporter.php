<?php
/**
 * Class CFF_Error_Reporter
 *
 * Set as a global object to record and report errors
 *
 * @since X.X
 */

namespace SmashBalloon\Reviews\Common;

use SmashBalloon\Reviews\Common\Builder\SBR_Sources;
use  SmashBalloon\Reviews\Common\Customizer\DB;
use Smashballoon\Stubs\Services\ServiceProvider;

//Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class Error_Reporter extends ServiceProvider
{

	/**
	 * @var array
	 */
	public $errors;

	/**
	 * @var array
	 */
	public $frontend_error;

	/**
	 * @var string
	 */
	public $reporter_key;

	/**
	 * @var array
	 */
	public $display_error;


	/**
	 * CFF_Error_Reporter constructor.
	 */
	public function __construct()
	{
		$this->reporter_key = 'sbr_error_reporter';
		$this->errors = get_option($this->reporter_key, []);
		if (!isset($this->errors['connection'])) {
			$this->errors = array(
				'connection' 			=> [],
				'resizing' 				=> [],
				'database_create' 		=> [],
				'upload_dir' 			=> [],
				'accounts' 				=> [],
				'error_log' 			=> [],
				'action_log' 			=> [],
				'revoked'         		=> []
			);
		}


		$this->display_error = [];
		$this->frontend_error = '';
	}

	public function register()
	{
		add_action('sbr_feed_issue_email', [$this, 'maybe_trigger_report_email_send']);
		add_action('wp_ajax_sbr_dismiss_critical_notice', [$this, 'dismiss_critical_notice']);
		add_action('wp_footer', [$this, 'critical_error_notice'], 300);
	}

	/**
	 * @return array
	 *
	 * @since X.X
	 */
	public function get_errors()
	{
		return $this->errors;
	}

	/**
	 * @param $type
	 * @param $message_array
	 *
	 * @since X>X
	 */
	public function add_error($type, $args, $connected_account_term = false)
	{
		$connected_account = false;
		$log_item = date('m-d H:i:s') . ' - ';

		if ($connected_account_term !== false) {
			if (!is_array($connected_account_term)) {
				$connected_account = SBR_Sources::get_single_source_info(
					[
						'id'	=> $connected_account_term,
						'provider' => 'facebook'
					]
				);
			} else {
				$connected_account = $connected_account_term;
			}
			$this->add_connected_account_error($connected_account, $type, $args);
		}

		//Access Token Error
		if ($type === 'accesstoken') {
			$accesstoken_error_exists = false;
			if (isset($this->errors['accounts'])) {
				foreach ($this->errors['accounts'] as $account) {
					if ($args['accesstoken'] === $account['accesstoken']) {
						$accesstoken_error_exists = true;
					}
				}
			}
			if (!$accesstoken_error_exists && isset($this->errors['accounts'])) {
				$this->errors['accounts'][$connected_account['id']][] = array(
					'accesstoken' 	=> $args['accesstoken'],
					'post_id' 		=> $args['post_id'],
					'critical' 		=> true,
					'type'			=> $type,
					'errorno' 		=> $args['errorno']
				);
			}
		}

		//Connection Error API & WP REMOTE CALL
		if ($type === 'api'  || $type === 'wp_remote_get') {
			$connection_details = array(
				'error_id' => ''
			);
			$connection_details['critical'] = false;

			if (isset($args['error']['code'])) {
				$connection_details['error_id'] = $args['error']['code'];
				if ($this->is_critical_error($args)) {
					$connection_details['critical'] = true;
				}

				if ($this->is_app_permission_related($args)) {
					if (!isset($this->errors['revoked']) || (!is_array($this->errors['revoked']))) {
						$this->errors['revoked'] = array();
					}
					if (isset($connected_account['account_id']) && !in_array($connected_account['account_id'], $this->errors['revoked'], true)) {
						$this->errors['revoked'][] = $connected_account['account_id'];
					}
					do_action('sbr_app_permission_revoked', $connected_account);
				}
			} elseif (isset($args['response']) && is_wp_error($args['response'])) {
				foreach ($args['response']->errors as $key => $item) {
					$connection_details['error_id'] = $key;
				}
				$connection_details['critical'] = true;
			}

			$connection_details['error_message'] = $this->generate_error_message($args, $connected_account);
			$log_item .= $connection_details['error_message']['admin_message'];
			$this->errors['connection'] = $connection_details;
		}

		if ($type === 'platform_data_deleted') {
			$this->errors['platform_data_deleted'] = $args[0];
			$log_item .= is_array($args) ? wp_json_encode($args) : $args;
		}

		$current_log = $this->errors['error_log'];
		if (is_array($current_log) && count($current_log) >= 10) {
			reset($current_log);
			unset($current_log[key($current_log)]);
		}
		$current_log[] = $log_item;
		$this->errors['error_log'] = $current_log;
		update_option($this->reporter_key, $this->errors, false);
	}

	/**
	 * Stores information about an encountered error related to a connected account
	 *
	 * @param $connected_account array
	 * @param $error_type string
	 * @param $details mixed/array/string
	 *
	 * @since X.X.X
	 */
	public function add_connected_account_error($connected_account, $error_type, $details)
	{
		$account_id = $connected_account['id'];
		$this->errors['accounts'][$account_id][$error_type] = $details;

		if ($error_type === 'api' || $error_type === 'accesstoken') {
			$this->errors['accounts'][$account_id][$error_type]['clear_time'] = time() + 60 * 3;
		}

		if (isset($details['error']['code']) && (int)$details['error']['code'] === 18) {
			$this->errors['accounts'][$account_id][$error_type]['clear_time'] = time() + 60 * 15;
		}
		//\CustomFacebookFeed\Builder\CFF_Source::add_error($account_id, $details);
	}

	/**
	 * @return mixed
	 *
	 * @since X.X.X
	 */
	public function get_error_log()
	{
		return $this->errors['error_log'];
	}

	/**
	 * Certain API errors are considered critical and will trigger
	 * the various notifications to users to correct them.
	 *
	 * @param $details
	 *
	 * @return bool
	 *
	 * @since X.X
	 */
	public function is_critical_error($details)
	{
		$error_code = (int)$details['error']['code'];
		$critical_codes = array(
			10,
			100,
			200,
			190,
			104,
			999
		);
		return in_array($error_code, $critical_codes, true);
	}

	/**
	 * Removes Single Error
	 *
	 * @param $type
	 *
	 * @since X.X.X
	 */
	public function remove_error($type, $connected_account = false)
	{
		$update = false;
		if (!empty($this->errors[$type])) {
			$this->errors[$type] = array();
			$this->add_action_log('Cleared ' . $type .' error.');
			$update = true;
		}

		if (!empty($this->errors['revoked'])) {
			if (!is_array($this->errors['revoked'])) {
				$this->errors['revoked'] = array();
			}
			if (isset($connected_account['account_id']) && ($key = array_search($connected_account['account_id'], $this->errors['revoked'])) !== false) {
				unset($this->errors['revoked'][$key]);
			}
		}

		if ($update) {
			update_option($this->reporter_key, $this->errors, false);
		}
	}

	/**
	 * Resets and Removes All Errors
	 *
	 * @since X.X.X
	 */
	public function remove_all_errors()
	{
		delete_option($this->reporter_key);
	}

	/**
	 * Resets and Removes All API Errors
	 *
	 * @since X.X.X
	 */
	public function reset_api_errors()
	{
		$this->errors['connection'] = array();
		$this->errors['accounts'] = array();
		update_option($this->reporter_key, $this->errors, false);
	}

	/**
	 * Stores a time stamped string of information about
	 * actions that might lead to correcting an error
	 *
	 * @param string $log_item
	 *
	 * @since X.X.X
	 */
	public function add_action_log($log_item)
	{
		$current_log = $this->errors['action_log'];

		if (is_array($current_log) && count($current_log) >= 10) {
			reset($current_log);
			unset($current_log[key($current_log)]);
		}
		$current_log[] = date('m-d H:i:s') . ' - ' . $log_item;

		$this->errors['action_log'] = $current_log;
		update_option($this->reporter_key, $this->errors, false);
	}

	/**
	 * Get Action Logs
	 *
	 * @return mixed
	 *
	 * @since X.X.X
	 */
	public function get_action_log()
	{
		return $this->errors['action_log'];
	}

	/**
	 * Should clear platform data
	 *
	 * @param $details
	 *
	 * @return bool
	 *
	 * @since X.X
	 */
	public function is_app_permission_related($details)
	{
		$error_code = (int)$details['error']['code'];
		$critical_codes = array(
			190, // access token or permissions
		);
		return in_array($error_code, $critical_codes, true) && strpos($details['error']['message'], 'user has not authorized application') !== false;
	}

	public function maybe_trigger_report_email_send()
	{
		if (!$this->are_critical_errors()) {
			return;
		}
		/** TODO: Match real option */
		$options = get_option('sbr_settings');

		if (isset($options['enable_email_report']) && empty($options['enable_email_report'])) {
			return;
		}
		$this->send_report_email();
	}

	/**
	 * Whether or not there was a platform data clearing error
	 *
	 * @return bool
	 */
	public function was_app_permission_related_error()
	{
		return !empty($this->errors['revoked']);
	}

	/**
	 * App Permission Errors
	 *
	 * @return array
	 */
	public function get_app_permission_related_error_ids()
	{
		return $this->errors['revoked'];
	}

	/**
	 * Are Critical Errors
	 *
	 * @return bool
	 */
	public function are_critical_errors()
	{
		$are_errors = false;
		$errors = $this->get_errors();
		if (isset($errors['connection']['critical']) && $errors['connection']['critical'] === true) {
			return true;
		} else {
			$connected_accounts = DB::get_facebook_sources();

			foreach ($connected_accounts as $connected_account) {
				$connected_account = (array)$connected_account;

				if (isset($connected_account['account_id']) && isset($this->errors['accounts'][$connected_account['account_id']]['api'])) {
					if (isset($this->errors['accounts'][$connected_account['account_id']]['api']['error'])) {
						return $this->is_critical_error($this->errors['accounts'][$connected_account['account_id'] ]['api']);
					}
				}
			}
		}
		return $are_errors;
	}

	/**
	 * Creates an array of information for easy display of API errors
	 *
	 * @param $response
	 * @param array $connected_account
	 *
	 * @return array
	 *
	 * @since X.X.X
	 */
	public function generate_error_message($response, $connected_account = array('username' => ''))
	{
		$error_message_return = array(
			'public_message' 		=> '',
			'admin_message' 		=> '',
			'frontend_directions' 	=> '',
			'backend_directions' 	=> '',
			'post_id' 				=> get_the_ID(),
			'errorno'				=> '',
			'time' 					=> time()
		);

		if (isset($response['error']['code'])) {
			$error_code = (int)$response['error']['code'];
			if ($error_code === 104) {
				$error_code = 999;
				$url        = 'https://smashballoon.com/doc/error-999-access-token-could-not-be-decrypted/';

				$response['error']['message'] = __('Your access token could not be decrypted on this website. Reconnect this account or go to our website to learn how to prevent this.', 'reviews-feed');
			} else {
				$url = 'https://smashballoon.com/doc/facebook-api-errors/';
			}
			$api_error_number_message 				= sprintf(__('API Error %s:', 'reviews-feed'), $error_code);
			$error_message_return['public_message'] = __('Error connecting to the Facebook API.', 'reviews-feed') . ' ' . $api_error_number_message;
			$ppca_error								= (strpos($response['error']['message'], 'Public Content Access') !== false) ? true : false;

			$error_message_return['admin_message'] 	= ($ppca_error)
				? '<B>PPCA Error:</b> Due to Facebook API changes it is no longer possible to display a feed from a Facebook Page you are not an admin of. Please use the button below for more information on how to fix this.'
				: '<strong>' . $api_error_number_message . '</strong><br>' . $response['error']['message'];

			$error_message_return['frontend_directions'] = ($ppca_error)
				? '<p class="sbr-error-directions"><a href="https://smashballoon.com/facebook-api-changes-september-4-2020/" target="_blank" rel="noopener">' . __('Directions on How to Resolve This Issue', 'reviews-feed')  . '</a></p>'
				: '<p class="sbr-error-directions"><a href="' . $url . '?facebook&utm_campaign=facebook-pro&utm_source=error-message&utm_medium=frontend#' . absint($error_code) . '" target="_blank" rel="noopener">' . __('Directions on How to Resolve This Issue', 'reviews-feed')  . '</a></p>';

			$error_message_return['backend_directions'] = ($ppca_error)
				? '<a class="sbr-notice-btn sbr-btn-blue" href="https://smashballoon.com/facebook-api-changes-september-4-2020/" target="_blank" rel="noopener">' . __('Directions on How to Resolve This Issue', 'reviews-feed')  . '</a>'
				: '<a class="sbr-notice-btn sbr-btn-blue" href="' . $url . '?facebook&utm_campaign=facebook-pro&utm_source=error-message&utm_medium=frontend#' . absint($error_code) . '" target="_blank" rel="noopener">' . __('Directions on How to Resolve This Issue', 'reviews-feed')  . '</a>';

			$error_message_return['errorno'] = $error_code;
		} else {
			$error_message_return['error_message'] = __('An unknown error has occurred.', 'reviews-feed');
			$error_message_return['admin_message'] = wp_json_encode($response);
		}
		return $error_message_return;
	}


	/**
	 * Send Email Repport
	 *
	 *
	 * @since X.X.X
	 */
	public function send_report_email()
	{
		$options = get_option('sbr_settings', array());

		$to_string = ! empty($options['email_notification_addresses']) ? str_replace(' ', '', $options['email_notification_addresses']) : get_option('admin_email', '');

		$to_array_raw = explode(',', $to_string);
		$to_array = array();

		foreach ($to_array_raw as $email) {
			if (is_email($email)) {
				$to_array[] = $email;
			}
		}

		if (empty($to_array)) {
			return false;
		}

		$headers = array('Content-Type: text/html; charset=utf-8');

		$header_image = SBR_PLUGIN_URL . '/assets/images/balloon-120.png';

		$title = __('Reviews Feed Report for ' . home_url());
		$link = admin_url('admin.php?page=sbr-settings');
		//&tab=customize-advanced
		$footer_link = admin_url('admin.php?page=sbr-style&tab=misc&flag=emails');
		$bold = __('There\'s an Issue with a Reviews Feed on Your Website', 'reviews-feed');
		$details = '<p>' . __('A Custom Reviews Feed on your website is currently unable to connect to Facebook to retrieve new posts. Don\'t worry, your feed is still being displayed using a cached version, but is no longer able to display new posts.', 'reviews-feed') . '</p>';
		$details .= '<p>' . sprintf(__('This is caused by an issue with your Facebook account connecting to the Facebook API. For information on the exact issue and directions on how to resolve it, please visit the %sCustom Facebook Feed settings page%s on your website.', 'reviews-feed'), '<a href="' . esc_url($link) . '">', '</a>'). '</p>';
		$message_content = '<h6 style="padding:0;word-wrap:normal;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-weight:bold;line-height:130%;font-size: 16px;color:#444444;text-align:inherit;margin:0 0 20px 0;Margin:0 0 20px 0;">' . $bold . '</h6>' . $details;

		$educator = new SBR_Education();
		$dyk_message = $educator->dyk_display();

		ob_start();
		include_once SBR_PLUGIN_URL . '/templates/email.php';
		$email_body = ob_get_contents();
		ob_get_clean();
		$sent = wp_mail($to_array, $title, $email_body, $headers);
		return $sent;
	}


	/**
	 * Returns Array of Notices
	 *
	 * @return array
	 *
	 * @since X.X.X
	 */
	public function get_facebook_platform_notices()
	{
		$errors = $this->get_errors();
		$notices = [];
		if (!empty($errors) && (!empty($errors['platform_data_deleted']))) {
			$notices[] = [
				'type' => 'error',
				'heading' => __('All Facebook Data has Been Remove.', 'reviews-feed'),
				'description' => __('An account admin has deauthorized the Smash Balloon app used to power the Facebook Feed plugin. The page was not reconnected within the 7 day limit and all Facebook data was automatically deleted on your website due to Facebook data privacy rules.<br/><br/>Due to API limitations your feeds will not show new reviews until you enter an API key on the settings page.', 'reviews-feed'),
			];
		}

		if (!empty($errors) && (!empty($errors['unused_feed']))) {
			$notices[] = [
				'type' => 'error',
				'heading' => __('Action Required Within 7 Days:', 'reviews-feed'),
				'description' => __('Your Facebook Reviews feed has been not viewed in the last 14 days. Due to Facebook data privacy rules, all data for this feed will be deleted in 7 days time. To avoid automated data deletion, simply view the Facebook feed on your website within the next 7 days.', 'reviews-feed'),
			];
		}

		if (!empty($errors) && isset($errors['connection']['error_id']) && isset($errors['connection']['error_message']['admin_message']) && $errors['connection']['error_id'] === 190 && strpos($errors['connection']['error_message']['admin_message'], 'user has not authorized application') !== false) {
			$accounts_revoked = '';
			if ($this->was_app_permission_related_error()) {
				$accounts_revoked = $this->get_app_permission_related_error_ids();
				if (count($accounts_revoked) > 1) {
					$accounts_revoked = implode(', ', $accounts_revoked);
				} else {
					$accounts_revoked = $accounts_revoked[0];
				}
			}

			$notices[] = [
				'type' => 'error',
				'heading' => __('Action Required Within 7 Days', 'reviews-feed'),
				'description' => [
					__('An account admin has deauthorized the Smash Balloon app used to power the Facebook Feed plugin.', 'reviews-feed'),
					sprintf(
						__('Facebook Feed related data for the account(s) %s was removed due to permission for the Smash Balloon App on Facebook being revoked.', 'reviews-feed'),
						$accounts_revoked
					),
					__('To prevent the automated data deletion for the account, please reconnect your account within 7 days.', 'reviews-feed'),
					'<a href="https://smashballoon.com/doc/action-required-within-7-days/?facebook&utm_campaign=facebook-pro&utm_source=permissionerror&utm_medium=notice&utm_content=More Information" target="_blank" rel="noopener">' . __('More Information', 'reviews-feed') . '</a>',
				]
			];
		}

		return $notices;
	}

	/**
	 * Load the critical notice for logged in users.
	 */
	public function critical_error_notice()
	{

	}

}