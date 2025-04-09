<?php
/**
 * Summary of namespace SmashBalloon\Reviews\Common\Utils
 */
namespace SmashBalloon\Reviews\Common\Utils;

use Smashballoon\Stubs\Services\ServiceProvider;

/**
 * Summary of EmailVerification
 */
class EmailVerification	extends ServiceProvider
{

	/**
	 * Email Verification Data /
	 * @var string
	 */
	public static $email_opt_name = 'sbr_email_verification';

	/**
	 * Get Email Verification Options
	 *
	 * @return array
	 */
	public static function get_email_verification_settings()
	{
		return get_option(self::$email_opt_name, []);
	}

	/**
	 * Summary of catch_email_verification
	 *
	 * @return bool
	 */
	public static function catch_email_verification()
	{
		if (is_admin()) {
			$nonce = !empty($_GET['con_nonce'])
				? sanitize_key($_GET['con_nonce'])
				: '';

			if (!wp_verify_nonce($nonce, 'sbr_con')
				|| empty($_GET['sbr_email_token'])
				|| empty($_GET['verified_email'])
			) {
				return false;
			}
			update_option(
				self::$email_opt_name,
				[
					'email'	=> sanitize_email($_GET['verified_email']),
					'token'	=> sanitize_text_field($_GET['sbr_email_token'])
				]
			);
		}
	}

	/**
	 * Build Email Verification URL
	 *
	 * @return string
	 */
	public static function build_email_verification_url($current_page = false)
	{
		$settings = get_option('sbr_settings', []);
		$args = [
			'state'					=> $current_page !== false ? $current_page : admin_url('admin.php?page=sbr-settings'),
			'wordpress_user'		=> self::get_current_email(),
			'con_nonce' 			=> wp_create_nonce('sbr_con'),
			'site_token'			=> !empty($settings['access_token']) ? $settings['access_token'] : null
		];
		return add_query_arg($args, SBR_CONNECT_SITE_URL);
	}

	/**
	 * Get Current User Email
	 *
	 * @return string
	 */
	public static function get_current_email()
	{
		if (!is_user_logged_in()) {
			return get_option('admin_email', '');
		}
		$current_user = wp_get_current_user();
		return $current_user->user_email;
	}

	/**
	 * Check if it's verified
	 *
	 * @return boolean
	 */
	public static function check_verified()
	{
		$data = self::get_email_verification_settings();
		return !empty($data['email']) && !empty($data['token']);
	}

}