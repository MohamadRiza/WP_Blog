<?php
/**
 * CFF_Support_Tool.
 *
 * @since 6.4
 */
namespace CustomFacebookFeed\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class CFF_Support_Tool
{

	static $plugin_name = 'SmashBalloon Facebook';
	static $plugin = 'smash_cff';

	/**
	 * Temp User Name
	 * @access private
	 *
	 * @var string
	 */
	static $name = 'SmashBalloonCFF';

	/**
	 * Temp Last Name
	 * @access private
	 *
	 * @var string
	 */
	static $last_name = 'Support';


	/**
	 * Temp Login UserName
	 * @access private
	 *
	 * @var string
	 */
	static $username = 'SmashBalloon_CFFSupport';

	/**
	 * Cron Job Name
	 * @access public
	 *
	 * @var string
	 */
	static $cron_event_name = 'smash_cff_delete_expired_user';

	/**
	 * Temp User Role
	 * @access private
	 *
	 * @var string
	 */
	static $role = '_support_role';

	public function __construct()
	{
		$this->init();
	}

	/**
	 * CFF_Support_Tool constructor.
	 *
	 * @since 6.3
	 */
	public function init()
	{
		$this->init_temp_login();

		if (!is_admin()) {
			return;
		}

		$this->ini_ajax_calls();
		add_action('admin_menu', array($this, 'register_menu'));
		add_action('admin_footer', ['\CustomFacebookFeed\Admin\CFF_Support_Tool', 'delete_expired_users']);
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 *
	 * @return void
	 */
	public function ini_ajax_calls()
	{
		add_action('wp_ajax_cff_create_temp_user', array($this, 'create_temp_user_ajax_call'));
		add_action('wp_ajax_cff_delete_temp_user', array($this, 'delete_temp_user_ajax_call'));
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 */
	public function delete_temp_user_ajax_call()
	{
		check_ajax_referer('cff-admin', 'nonce');
		$cap = current_user_can('manage_custom_facebook_feed_options') ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters('cff_settings_pages_capability', $cap);
		if (!current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}

		if (!isset($_POST['userId'])) {
			wp_send_json_error();
		}

		$user_id = sanitize_key($_POST['userId']);
		$return = CFF_Support_Tool::delete_temporary_user($user_id);
		echo wp_json_encode($return);
		wp_die();
	}
	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 */
	public function create_temp_user_ajax_call()
	{
		check_ajax_referer('cff-admin', 'nonce');
		$cap = current_user_can('manage_custom_facebook_feed_options') ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters('cff_settings_pages_capability', $cap);
		if (!current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}
		$return = CFF_Support_Tool::create_temporary_user();
		echo wp_json_encode($return);
		wp_die();
	}

	/**
	 * Init Login
	 *
	 * @since 6.3
	 */
	public function init_temp_login()
	{

		$attr = CFF_Support_Tool::$plugin . '_token';
		if (empty($_GET[$attr])) {
			return;
		}


		$token = sanitize_key($_GET[$attr]);  // Input var okay.
		$temp_user = CFF_Support_Tool::get_temporary_user_by_token($token);
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
			$redirect_page = 'admin.php?page=' . CFF_Support_Tool::$plugin . '_tool';
			wp_safe_redirect(admin_url($redirect_page));
			exit();
		}

	}

	/**
	 * Create New User.
	 *
	 * @return array
	 *
	 * @since 6.3
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

		$email = CFF_Support_Tool::$username . '@' . $domain;
		$temp_user_args = [
			'user_email' => $email,
			'user_pass' => CFF_Support_Tool::generate_temp_password(),
			'first_name' => CFF_Support_Tool::$name,
			'last_name' => CFF_Support_Tool::$last_name,
			'user_login' => CFF_Support_Tool::$username,
			'role' => CFF_Support_Tool::$plugin . CFF_Support_Tool::$role
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
			$token = str_replace(['=', '&', '"', "'"], '', \cff_encrypt_decrypt('encrypt', CFF_Support_Tool::generate_temp_password(35)));

			update_user_meta($temp_user_id, CFF_Support_Tool::$plugin . '_user', $temp_user_id);
			update_user_meta($temp_user_id, CFF_Support_Tool::$plugin . '_token', $token);
			update_user_meta($temp_user_id, CFF_Support_Tool::$plugin . '_create_time', $creation_time);
			update_user_meta($temp_user_id, CFF_Support_Tool::$plugin . '_expires', $expires);

			$result = [
				'success' => true,
				'message' => __('Temporary user created successfully'),
				'user' => CFF_Support_Tool::get_user_meta_data($temp_user_id)
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
	 * @since 6.3
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
	 * @since 6.3
	 */
	public static function get_user_meta_data($user_id)
	{
		$user = get_user_meta($user_id, CFF_Support_Tool::$plugin . '_user');
		if (!$user) {
			return false;
		}
		$token = get_user_meta($user_id, CFF_Support_Tool::$plugin . '_token');
		$creation_time = get_user_meta($user_id, CFF_Support_Tool::$plugin . '_create_time');
		$expires = get_user_meta($user_id, CFF_Support_Tool::$plugin . '_expires');

		$url = CFF_Support_Tool::$plugin . '_token=' . $token[0];
		return [
			'id' => $user_id,
			'token' => $token[0],
			'creation_time' => $creation_time[0],
			'expires' => $expires[0],
			'expires_date' => CFF_Support_Tool::get_expires_days($expires[0]),
			'url' => admin_url('/?' . $url)
		];
	}

	/**
	 * Get UDays before Expiring Token
	 *
	 * @param $expires timestamp
	 *
	 * @since 6.3
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
	 * @since 6.3
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
					'key' => CFF_Support_Tool::$plugin . '_token',
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
	 * @since 6.3
	 */
	public static function check_temporary_user_exists()
	{
		$args = [
			'fields' => 'all',
			'meta_query' => [
				[
					'key' => CFF_Support_Tool::$plugin . '_token',
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
		return CFF_Support_Tool::get_user_meta_data($users_result[0]->ID);
	}

	/**
	 * Check & Delete Expired Users
	 *
	 * @since 6.3
	 *
	 */
	public static function delete_expired_users()
	{
		$existing_user = CFF_Support_Tool::check_temporary_user_exists();
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
	 * @since 6.3
	 *
	 */
	public static function delete_temp_user()
	{
		$existing_user = CFF_Support_Tool::check_temporary_user_exists();
		if ($existing_user === null) {
			return false;
		}
		require_once(ABSPATH . 'wp-admin/includes/user.php');
		\wp_delete_user($existing_user['id']);
	}


	/**
	 * Register Menu.
	 *
	 * @since 6.0
	 */
	public function register_menu()
	{
		$role_id = CFF_Support_Tool::$plugin . CFF_Support_Tool::$role;
		$cap = $role_id;
		$cap = apply_filters('cff_settings_pages_capability', $cap);

		$support_tool_page = add_submenu_page(
			'cff-top',
			__('Support API tool', 'custom-facebook-feed'),
			__('Support API tool', 'custom-facebook-feed'),
			$cap,
			CFF_Support_Tool::$plugin . '_tool',
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
	 * @since 6.3
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
	 * @since 6.3
	 *
	 * @return string
	 */
	public function render()
	{
		include_once CFF_PLUGIN_DIR . 'admin/views/support/support-tools.php';
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
			'timeline' => 'Timeline'
		);
	}

	/**
	 * Show Posts By
	 *
	 * @since 6.3
	 *
	 * @return array
	 */
	public function available_timeline_showby()
	{
		return array(
			'others' => __('Page owner + Visitors', 'custom-facebook-feed'),
			'me' => __('Page Owner', 'custom-facebook-feed'),
			'onlyothers' => __('Visitors', 'custom-facebook-feed'),
		);
	}

	public function validate_and_sanitize_support_settings($raw_post)
	{

		if (empty($raw_post['sb_facebook_support_source'])) {
			return array();
		}

		$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();
		$data_response = 'Cannot get info';

		$source_id = sanitize_key($raw_post['sb_facebook_support_source']);
		$endpoint = sanitize_key($raw_post['sb_facebook_support_endpoint']);
		$limit = absint($raw_post['sb_facebook_support_limit']);
		$showpostsby = sanitize_text_field(wp_unslash($raw_post['sb_facebook_support_showby']));


		$source_info = \CustomFacebookFeed\Builder\CFF_Source::get_single_source_info($source_id);

		if ($source_info === false) {
			return false;
		}
		$token 	= $encryption->maybe_decrypt($source_info['access_token']);
		$limit 	= !empty($limit) ? $limit : 3;
		$locale = get_option('cff_locale', 'en_US');

		if (in_array($endpoint, ['timeline'])) {
			$is_group = $source_info['account_type'] === 'group';
			$graph_query = 'posts';
			if ($showpostsby === 'others' || $is_group) {
				$graph_query = 'feed';
			}
			if ($showpostsby === 'onlyothers' && !$is_group) {
				$graph_query = 'visitor_posts';
			}

			$url = 'https://graph.facebook.com/v4.0/'. $source_info['account_id'].'/'. $graph_query .'?fields=id,updated_time,from{picture,id,name,link},message,message_tags,story,story_tags,picture,full_picture,status_type,created_time,backdated_time,attachments{title,description,media_type,unshimmed_url,target{id},multi_share_end_card,media{source,image},subattachments},shares,call_to_action,privacy&access_token='.$token.'&limit='.$limit.'&locale='.$locale;
			$data_response = wp_remote_get($url);

		}



		$sanitized_results = sanitize_text_field(wp_unslash(wp_remote_retrieve_body($data_response)));
		$sanitized_results_token_removed = str_replace($token, '{access_token}', $sanitized_results);
		echo '<h3>Results</h3>';
		echo '<pre>';
		var_dump($sanitized_results_token_removed, json_decode($sanitized_results_token_removed, true));
		echo '</pre>';
		echo '<hr>';
	}





	public function create_api_url($url, $settings)
	{

	}


}


?>