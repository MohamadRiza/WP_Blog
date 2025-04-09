<?php
/**
 * Summary of namespace SmashBalloon\Reviews\Common\Helpers
 */
namespace SmashBalloon\Reviews\Common\Helpers;

/**
 * Summary of SBR_Error_Handler
 */
class SBR_Error_Handler
{
	/**
	 * Errors Options
	 *
	 * @var string
	 */
	private static $errors_opt = 'sbr_errors';

	/**
	 * Get All Errors
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public static function get_errors()
	{
		return get_option(self::$errors_opt, []);
	}

	/**
	 * Clear All Logs
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public static function clear_all_errors()
	{
		update_option(self::$errors_opt, []);
	}

	/**
	 * Update Reviews Feed Errors
	 *
	 * @param array $error Error to update
	 *
	 * @return boolean
	 *
	 * @since 1.0
	 */
	public static function update_errors($errors, $type = 'merge')
	{
		$current_errors = self::get_errors();
		$updated_errors = $type === 'merge'
			? array_merge($current_errors, $errors)
			: $errors;

		$updated_errors = self::truncate_errors($updated_errors);

		return update_option(self::$errors_opt, $updated_errors);
	}

	/**
	 * Check if Error Exists
	 *
	 * @param array $error
	 *
	 * @return int|boolean
	 *
	 * @since 1.0
	 */
	public static function check_error($error)
	{
		$current_errors = self::get_errors();
		$exists = 'not_defined';
		foreach ($current_errors as $key => $error_elm) {
			if (
				$error_elm['type'] === $error['type'] &&
				$error_elm['id'] === $error['id'] &&
				(
					(
						!empty($error_elm['provider']) &&
						$error_elm['provider'] === $error['provider']
					) || empty($error_elm['provider'])
				)
			) {
				$exists = $key;
				break;
			}
		}
		return $exists;
	}

	/**
	 * Log API Error
	 *
	 * @param array $erros Error to add
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public static function log_error($error)
	{
		$current_errors = self::get_errors();
		$error_index = self::check_error($error);

		if ($error_index !== 'not_defined') {
			$current_errors[$error_index] = $error;
		} else {
			array_push(
				$current_errors,
				$error
			);
		}

		self::update_errors($current_errors, 'no_merge');
	}

	/**
	 * Log Only the Latest 20 error
	 *
	 * @param array $erros Error
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public static function truncate_errors($errors)
	{
		return array_slice($errors, -20);
	}

	/**
	 * Clear All Logs Ajax
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public static function clear_all_error_ajax()
	{
		check_ajax_referer('sbr-admin', 'nonce');
		if (!sbr_current_user_can('manage_reviews_feed_options')) {
			wp_send_json_error();
		}
		self::clear_all_errors();
		echo sbr_json_encode(
			[
				'success' => true
			]
		);
		wp_die();
	}
}