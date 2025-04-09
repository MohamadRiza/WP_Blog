<?php
/**
 * SBR_Support_Tool.
 *
 * @since X.X
 */
namespace SmashBalloon\Reviews\Common\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class SBR_Support_Tool  extends ServiceProvider
{
	/**
	 * Plugin Name
	 * @access private
	 *
	 * @var string
	 */
	private static $plugin_name = 'SmashBalloon Reviews';

	/**
	 * Plugin
	 * @access private
	 *
	 * @var string
	 */
	private static $plugin = 'smash_sbr';

	/**
	 * Temp User Name
	 * @access private
	 *
	 * @var string
	 */
	private static $name = 'SmashBalloonSBR';

	/**
	 * Temp Last Name
	 * @access private
	 *
	 * @var string
	 */
	private static $last_name = 'Support';


	/**
	 * Temp Login UserName
	 * @access private
	 *
	 * @var string
	 */
	private static $username = 'SmashBalloon_SBRSupport';

	/**
	 * Cron Job Name
	 * @access public
	 *
	 * @var string
	 */
	public static $cron_event_name = 'smash_sbr_delete_expired_user';

	/**
	 * Temp User Role
	 * @access private
	 *
	 * @var string
	 */
	public static $role = '_support_role';

	public function register()
	{
		$this->init();
	}

	/**
	 * SBR_Support_Tool constructor.
	 *
	 * @since X.X
	 */
	public function init()
	{
		add_action('plugins_loaded', [$this, 'init_temp_login']);

		if (!is_admin()) {
			return;
		}

		$this->ini_ajax_calls();
		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_footer', ['\SmashBalloon\Reviews\Common\Admin\SBR_Support_Tool', 'delete_expired_users']);
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since X.X
	 *
	 * @return void
	 */
	public function ini_ajax_calls()
	{
		add_action('wp_ajax_sbr_create_temp_user', array($this, 'create_temp_user_ajax_call'));
		add_action('wp_ajax_sbr_delete_temp_user', array($this, 'delete_temp_user_ajax_call'));
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since X.X
	 */
	public function delete_temp_user_ajax_call()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		$cap = current_user_can('manage_reviews_feed_options') ? 'manage_reviews_feed_options' : 'manage_options';
		$cap = apply_filters('sbr_settings_pages_capability', $cap);
		if (!current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}

		if (!isset($_POST['userId'])) {
			wp_send_json_error();
		}

		$user_id = sanitize_key($_POST['userId']);
		$return = SBR_Support_Tool::delete_temporary_user($user_id);
		echo wp_json_encode($return);
		wp_die();
	}
	/**
	 * Create New User Ajax Call
	 *
	 * @since X.X
	 */
	public function create_temp_user_ajax_call()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		$cap = current_user_can('manage_reviews_feed_options') ? 'manage_reviews_feed_options' : 'manage_options';
		$cap = apply_filters('sbr_settings_pages_capability', $cap);
		if (!current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}
		$return = SBR_Support_Tool::create_temporary_user();
		echo wp_json_encode($return);
		wp_die();
	}

	/**
	 * Init Login
	 *
	 * @since X.X
	 */
	public function init_temp_login()
	{

		$attr = SBR_Support_Tool::$plugin . '_token';
		if (empty($_GET[$attr])) {
			return;
		}


		$token = sanitize_key($_GET[$attr]);  // Input var okay.
		$temp_user = SBR_Support_Tool::get_temporary_user_by_token($token);
		if (!$temp_user) {
			wp_die(esc_attr__("You Cannot connect user", 'instgaram-feed'));
		}

		$user_id = $temp_user->ID;
		$should_login = (is_user_logged_in() && $user_id !== get_current_user_id()) || !is_user_logged_in();

		if ($should_login) {

			if ($user_id !== get_current_user_id()) {
				wp_logout();
			}

			$user_login = $temp_user->user_login;

			wp_set_current_user($user_id, $user_login);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $user_login, $temp_user);
			$redirect_page = 'admin.php?page=' . SBR_Support_Tool::$plugin . '_tool';
			wp_safe_redirect(admin_url($redirect_page));
			exit();
		}

	}

	/**
	 * Create New User.
	 *
	 * @return array
	 *
	 * @since X.X
	 */
	public static function create_temporary_user()
	{
		if (!current_user_can('create_users')) {
			return [
				'success' => false,
				'message' => __('You don\'t have enough permission to create users'),
			];
		}
		$domain = str_replace([
			'http://', 'https://', 'http://www.', 'https://www.', 'www.'
		], '', site_url());

		$email = SBR_Support_Tool::$username . '@' . $domain;
		$temp_user_args = [
			'user_email' => $email,
			'user_pass' => SBR_Support_Tool::generate_temp_password(),
			'first_name' => SBR_Support_Tool::$name,
			'last_name' => SBR_Support_Tool::$last_name,
			'user_login' => SBR_Support_Tool::$username,
			'role' => SBR_Support_Tool::$plugin . SBR_Support_Tool::$role
		];

		$temp_user_id = \wp_insert_user($temp_user_args);
		$result = [];

		if (is_wp_error($temp_user_id)) {
			$result = [
				'success' => false,
				'message' => __('Cannot create user')
			];
		} else {
			$creation_time = \current_time('timestamp');
			$expires = strtotime('+15 days', $creation_time);
			$token = str_replace(['=', '&', '"', "'"], '', \sbr_encrypt_decrypt('encrypt', SBR_Support_Tool::generate_temp_password(35)));

			update_user_meta($temp_user_id, SBR_Support_Tool::$plugin . '_user', $temp_user_id);
			update_user_meta($temp_user_id, SBR_Support_Tool::$plugin . '_token', $token);
			update_user_meta($temp_user_id, SBR_Support_Tool::$plugin . '_create_time', $creation_time);
			update_user_meta($temp_user_id, SBR_Support_Tool::$plugin . '_expires', $expires);

			$result = [
				'success' => true,
				'message' => __('Temporary user created successfully'),
				'user' => SBR_Support_Tool::get_user_meta_data($temp_user_id)
			];
		}
		return $result;
	}

	/**
	 * Delete Temp User.
	 *
	 * @param $user_id User ID to delete
	 *
	 * @return array
	 *
	 * @since X.X
	 */
	public static function delete_temporary_user($user_id)
	{
		require_once(ABSPATH . 'wp-admin/includes/user.php');

		if (!current_user_can('delete_users')) {
			return [
				'success' => false,
				'message' => __('You don\'t have enough permission to delete users'),
			];
		}
		if (!wp_delete_user($user_id)) {
			return [
				'success' => false,
				'message' => __('Cannot delete this user'),
			];
		}

		return [
			'success' => true,
			'message' => __('User Deleted'),
		];
	}

	/**
	 * Get User Meta
	 *
	 * @param $user_id User ID to Get
	 *
	 * @return array/boolean
	 *
	 * @since X.X
	 */
	public static function get_user_meta_data($user_id)
	{
		$user = get_user_meta($user_id, SBR_Support_Tool::$plugin . '_user');
		if (!$user) {
			return false;
		}
		$token = get_user_meta($user_id, SBR_Support_Tool::$plugin . '_token');
		$creation_time = get_user_meta($user_id, SBR_Support_Tool::$plugin . '_create_time');
		$expires = get_user_meta($user_id, SBR_Support_Tool::$plugin . '_expires');

		$url = SBR_Support_Tool::$plugin . '_token=' . $token[0];
		return [
			'id' => $user_id,
			'token' => $token[0],
			'creation_time' => $creation_time[0],
			'expires' => $expires[0],
			'expires_date' => SBR_Support_Tool::get_expires_days($expires[0]),
			'url' => admin_url('/?' . $url)
		];
	}

	/**
	 * Get UDays before Expiring Token
	 *
	 * @param $expires timestamp
	 *
	 * @since X.X
	 */
	public static function get_expires_days($expires)
	{
		return ceil(($expires - time()) / 60 / 60 / 24);
	}

	/**
	 * Get User By Token.
	 *
	 * @param $token Token to connect with
	 *
	 * @since X.X
	 */
	public static function get_temporary_user_by_token($token = '')
	{
		if (empty($token)) {
			return false;
		}

		$args = [
			'fields' => 'all',
			'meta_query' => [
				[
					'key' => SBR_Support_Tool::$plugin . '_token',
					'value' => sanitize_text_field($token),
					'compare' => '=',
				]
			]
		];

		$users = new \WP_User_Query($args);
		$users_result = $users->get_results();

		if (empty($users_result)) {
			return false;
		}

		return $users_result[0];
	}

	/**
	 * Check Temporary User Created
	 *
	 * @since X.X
	 */
	public static function check_temporary_user_exists()
	{
		$args = [
			'fields' => 'all',
			'meta_query' => [
				[
					'key' => SBR_Support_Tool::$plugin . '_token',
					'value' => null,
					'compare' => '!=',
				]
			]
		];
		$users = new \WP_User_Query($args);
		$users_result = $users->get_results();
		if (empty($users_result)) {
			return null;
		}
		return SBR_Support_Tool::get_user_meta_data($users_result[0]->ID);
	}

	/**
	 * Check & Delete Expired Users
	 *
	 * @since X.X
	 *
	 */
	public static function delete_expired_users()
	{
		$existing_user = SBR_Support_Tool::check_temporary_user_exists();
		if ($existing_user === null) {
			return false;
		}
		$is_expired = intval($existing_user['expires']) - \current_time('timestamp') <= 0;
		if (!$is_expired) {
			return false;
		}
		require_once(ABSPATH . 'wp-admin/includes/user.php');
		\wp_delete_user($existing_user['id']);
	}

	/**
	 * Delete Temp User
	 *
	 * @since X.X
	 *
	 */
	public static function delete_temp_user()
	{
		$existing_user = SBR_Support_Tool::check_temporary_user_exists();
		if ($existing_user === null) {
			return false;
		}
		require_once(ABSPATH . 'wp-admin/includes/user.php');
		\wp_delete_user($existing_user['id']);
	}


	/**
	 * Register Menu.
	 *
	 * @since X.X
	 */
	public function register_menu()
	{
		$role_id = SBR_Support_Tool::$plugin . SBR_Support_Tool::$role;
		$cap = $role_id;
		$cap = apply_filters('sbr_settings_pages_capability', $cap);

		$support_tool_page = add_submenu_page(
			'sbr',
			__('Support API tool', 'reviews-feed'),
			__('Support API tool', 'reviews-feed'),
			$cap,
			SBR_Support_Tool::$plugin . '_tool',
			array($this, 'render'),
			5
		);
		#add_action('load-' . $support_tool_page, array( $this, 'support_page_enqueue_assets'));
	}


	/**
	 * Generate Temp User Password
	 *
	 * @param $length Length of password
	 *
	 * @since X.X
	 *
	 * @return string
	 */
	public static function generate_temp_password($length = 20)
	{
		return wp_generate_password($length, true, true);
	}


	/**
	 * Render the Api Tools Page
	 *
	 * @since X.X
	 *
	 * @return string
	 */
	public function render()
	{
		include_once SBR_PLUGIN_DIR . 'assets/admin/views/support/support-tools.php';
	}

	/**
	 * Available Endpoints
	 *
	 * @since 6.3
	 *
	 * @return array
	 */
	public function available_endpoints()
	{
		return array(
			'source' => 'Source',
			'reviews' => 'Reviews'
		);
	}


	/**
	 * Render Validate Settings & return data
	 *
	 * @since X.X
	 *
	 * @return string
	 */
	public function validate_and_sanitize_support_settings($raw_post)
	{
		if (empty($raw_post['sb_reviews_support_source'])) {
			return array();
		}

		$encryption = new \SmashBalloon\Reviews\Common\Helpers\Data_Encryption();
		$data_response_text = __('Cannot get info', 'reviews-feed');

		$source_id = sanitize_key($raw_post['sb_reviews_support_source']);
		$endpoint = sanitize_key($raw_post['sb_reviews_support_endpoint']);
		$db = new \SmashBalloon\Reviews\Common\Customizer\Db();
		$sources = $db->get_single_source(
			[
				'id' => $source_id,
				'provider' => 'facebook'
			]
		);

		if (isset($sources[0])) {
			$access_token = $encryption->maybe_decrypt($sources[0]['access_token']);
			if ($endpoint === 'source') {
				$data_response = \SmashBalloon\Reviews\Pro\Integrations\Providers\Facebook::get_source_header($source_id, $access_token, false);
			} elseif ($endpoint === 'reviews') {
				$data_response = \SmashBalloon\Reviews\Pro\Integrations\Providers\Facebook::get_reviews_list($source_id, $access_token);
			}
			$sanitized_results = wp_json_encode($data_response, true);
			$data_response_text = str_replace($access_token, '{access_token}', $sanitized_results);
		}

		echo '<h3>Results</h3>';
		echo '<pre>';
		var_dump($data_response_text, json_decode($data_response_text, true));
		echo '</pre>';
		echo '<hr>';
	}






	public function create_api_url($url, $settings)
	{

	}


}
