<?php
/**
 * Class CFF_Error_Reporter
 *
 * Set as a global object to record and report errors
 *
 * @since
 */

namespace CustomFacebookFeed;
use CustomFacebookFeed\CFF_Education;
use CustomFacebookFeed\Builder\CFF_Source;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Error_Reporter
{
	/**
	 * @var array
	 */
	var $errors;

	/**
	 * @var array
	 */
	var $frontend_error;

	/**
	 * @var string
	 */
	var $reporter_key;

	/**
	 * @var array
	 */
	var $display_error;


	/**
	 * CFF_Error_Reporter constructor.
	 */
	public function __construct() {
		$this->reporter_key = 'cff_error_reporter';
		$this->errors = get_option( $this->reporter_key, [] );
		if ( ! isset( $this->errors['connection'] ) ) {
			$this->errors = array(
				'connection' 			=> [],
				'resizing' 				=> [],
				'database_create' 		=> [],
				'upload_dir' 			=> [],
				'accounts' 				=> [],
				'error_log' 			=> [],
				'action_log' 			=> [],
				'revoked' 			=> []
			);
		}


		$this->display_error = [];
		$this->frontend_error = '';

		add_action('cff_feed_issue_email', [$this, 'maybe_trigger_report_email_send']);
		add_action('wp_ajax_cff_dismiss_critical_notice', [$this, 'dismiss_critical_notice']);
		add_action('wp_footer', [$this, 'critical_error_notice'], 300);
		add_action('cff_admin_notices', [$this, 'admin_error_notices']);
		add_action('cff_admin_notices', [$this, 'platform_data_deleted_notice']);
		add_action('cff_admin_notices', [$this, 'group_deprecation_notice']);
		add_action('cff_admin_notices', [$this, 'platform_unused_feed_notice']);
	}

	/**
	 * @return array
	 *
	 * @since 2.0/4.0
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * @param $type
	 * @param $message_array
	 *
	 * @since 2.0/4.0
	 */
	public function add_error($type, $args, $connected_account_term = false)
	{
		$connected_account = false;

		$log_item = date('m-d H:i:s') . ' - ';
		if ($connected_account_term !== false) {
			if (!is_array($connected_account_term)) {
				$connected_account = CFF_Source::get_single_source_info($connected_account_term);
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
					'accesstoken' => $args['accesstoken'],
					'post_id' => $args['post_id'],
					'critical' => true,
					'type' => $type,
					'errorno' => $args['errorno']
				);

			}
		}

		//Connection Error API & WP REMOTE CALL
		if ($type === 'api' || $type === 'wp_remote_get') {
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
					/**
					 * Fires when an app permission related error is encountered
					 *
					 * @param array $connected_account The connected account that encountered the error
					 *
					 * @since
					 */
					do_action('cff_app_permission_revoked', $connected_account);
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

		if ($type === 'image_editor' || $type === 'storage') {
			$this->errors['resizing'] = $args;
			$log_item .= is_array($args) ? wp_json_encode($args) : $args;
		}

		if ($type === 'database_create') {
			$this->errors['database_create'] = $args;
			$log_item .= $args;
		}

		if ($type === 'upload_dir') {
			$this->errors['upload_dir'] = $args;
			$log_item .= $args;
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
	 * @since 2.19
	 */
	public function add_connected_account_error( $connected_account, $error_type, $details ) {
		$account_id = $connected_account['id'];
		$this->errors['accounts'][ $account_id ][ $error_type ] = $details;

		if ( $error_type === 'api' || $error_type === 'accesstoken' ) {
			$this->errors['accounts'][ $account_id ][ $error_type ]['clear_time'] = time() + 60 * 3;
		}

		if ( isset( $details['error']['code'] )
			&& (int)$details['error']['code'] === 18 ) {
			$this->errors['accounts'][ $account_id ][ $error_type ]['clear_time'] = time() + 60 * 15;
		}

		\CustomFacebookFeed\Builder\CFF_Source::add_error( $account_id, $details );
	}

	/**
	 * @return mixed
	 *
	 * @since 2.19
	 */
	public function get_error_log() {
		return $this->errors['error_log'];
	}



	/**
	 * Creates an array of information for easy display of API errors
	 *
	 * @param $response
	 * @param array $connected_account
	 *
	 * @return array
	 *
	 * @since 2.19
	 */
	public function generate_error_message( $response, $connected_account = array( 'username' => '' ) ) {
		$error_message_return = array(
			'public_message' 		=> '',
			'admin_message' 		=> '',
			'frontend_directions' 	=> '',
			'backend_directions' 	=> '',
			'post_id' 				=> get_the_ID(),
			'errorno'				=> '',
			'time' 					=> time()
		);

		if( isset( $response['error']['code'] ) ){
			$error_code 							= (int)$response['error']['code'];
			if ( $error_code === 104 ) {
				$error_code = 999;
				$url        = 'https://smashballoon.com/doc/error-999-access-token-could-not-be-decrypted/';

				$response['error']['message'] = __( 'Your access token could not be decrypted on this website. Reconnect this account or go to our website to learn how to prevent this.', 'custom-facebook-feed' );
			} else {
				$url = 'https://smashballoon.com/doc/facebook-api-errors/';
			}

			$api_error_number_message 				= sprintf( __( 'API Error %s:', 'custom-facebook-feed' ), $error_code );
			$error_message_return['public_message'] = __( 'Error connecting to the Facebook API.', 'custom-facebook-feed' ) . ' ' . $api_error_number_message;
			$ppca_error								= ( strpos($response['error']['message'], 'Public Content Access') !== false ) ? true : false;

			$error_message_return['admin_message'] 	= ( $ppca_error)
				? '<B>PPCA Error:</b> Due to Facebook API changes it is no longer possible to display a feed from a Facebook Page you are not an admin of. Please use the button below for more information on how to fix this.'
				: '<strong>' . $api_error_number_message . '</strong><br>' . $response['error']['message'];

			$error_message_return['frontend_directions'] = ( $ppca_error )
				? '<p class="cff-error-directions"><a href="https://smashballoon.com/facebook-api-changes-september-4-2020/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a></p>'
				: '<p class="cff-error-directions"><a href="' . $url . '?facebook&utm_campaign=facebook-pro&utm_source=error-message&utm_medium=frontend#'. absint( $error_code ) .'" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a></p>';

			$error_message_return['backend_directions'] = ( $ppca_error )
				? '<a class="cff-notice-btn cff-btn-blue" href="https://smashballoon.com/facebook-api-changes-september-4-2020/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>'
				: '<a class="cff-notice-btn cff-btn-blue" href="' . $url . '?facebook&utm_campaign=facebook-pro&utm_source=error-message&utm_medium=frontend#'. absint( $error_code ) .'" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>';

			$error_message_return['errorno'] = $error_code;

		}else{
			$error_message_return['error_message'] = __( 'An unknown error has occurred.', 'custom-facebook-feed' );
			$error_message_return['admin_message'] = json_encode( $response );
		}

		return $error_message_return;

	}




	/**
	 * Certain API errors are considered critical and will trigger
	 * the various notifications to users to correct them.
	 *
	 * @param $details
	 *
	 * @return bool
	 *
	 * @since 2.7/5.10
	 */
	public function is_critical_error( $details ) {
		$error_code = (int)$details['error']['code'];

		$critical_codes = array(
			10,
			100,
			200,
			190,
			104,
			999
		);

		return in_array( $error_code, $critical_codes, true );
	}

	/**
	 * @param $type
	 *
	 * @since X.X.X
	 */
	public function remove_error($type, $connected_account = false)
	{
		$update = false;
		if (!empty($this->errors[$type])) {
			$this->errors[$type] = array();
			$this->add_action_log('Cleared ' . $type . ' error.');
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

	public function remove_all_errors() {
		delete_option( $this->reporter_key );
	}

	public function reset_api_errors() {
		$this->errors['connection'] = array();
		$this->errors['accounts'] = array();
		update_option( $this->reporter_key, $this->errors, false );
	}

	/**
	 * @param $type
	 * @param $message
	 *
	 * @since 2.0/5.0
	 */
	public function add_frontend_error( $message, $directions ) {
		$this->frontend_error = $message . $directions;
	}

	public function remove_frontend_error() {
		$this->frontend_error = '';
	}

	/**
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public function get_frontend_error() {
		return $this->frontend_error;
	}


	public function get_critical_errors()
	{
		if (!$this->are_critical_errors()) {
			return '';
		}

		$accounts_revoked_string = '';
		$accounts_revoked = '';

		if ($this->was_app_permission_related_error()) {
			$accounts_revoked = $this->get_app_permission_related_error_ids();
			if (count($accounts_revoked) > 1) {
				$accounts_revoked = implode(', ', $accounts_revoked);
			} else {
				$accounts_revoked = $accounts_revoked[0];
			}
			$accounts_revoked_string = sprintf(__('Facebook Feed related data for the account(s) %s was removed due to permission for the Smash Balloon App on Facebook being revoked. <br><br> To prevent the automated data deletion for the account, please reconnect your account within 7 days.', 'custom-facebook-feed'), $accounts_revoked);
		}

		$error_message = $directions = false;
		if (isset($this->errors['connection']['critical'])) {
			$errors = $this->get_errors();
			$error_message = '';
			$error = $errors['connection'];
			if ($errors['connection']['error_id'] === 190) {
				$error_message .= '<strong>' . __('Action Required Within 7 Days', 'custom-facebook-feed') . '</strong><br>';
				$error_message .= __('An account admin has deauthorized the Smash Balloon app used to power the Facebook Feed plugin.', 'custom-facebook-feed');
				$error_message .= ' ' . sprintf(__('If the Facebook source is not reconnected within 7 days then all Facebook data will be automatically deleted on your website for this account (ID: %s) due to Facebook data privacy rules.', 'custom-facebook-feed'), $accounts_revoked);
				$error_message .= __('<br><br>To prevent the automated data deletion for the account, please reconnect your account within 7 days.', 'custom-facebook-feed');
				$error_message .= '<br><br><a href="https://smashballoon.com/doc/action-required-within-7-days/?facebook&utm_campaign=facebook-pro&utm_source=permissionerror&utm_medium=notice&utm_content=More Information" target="_blank" rel="noopener">' . __('More Information', 'custom-facebook-feed') . '</a>';
				$directions = '';
			} else {
				$error_message_array = $error['error_message'];
				$error_message = $error_message_array['admin_message'];
				if (!empty($accounts_revoked_string)) {
					$error_message .= $accounts_revoked_string . '<br><br>';
				}

				$directions = '<p class="cff-error-directions">';
				$directions .= $error_message_array['backend_directions'];
				if (!empty($error_message_array['post_id'])) {
					$directions .= '<button data-url="' . get_the_permalink($error_message_array['post_id']) . '" class="cff-clear-errors-visit-page cff-space-left cff-btn cff-notice-btn cff-btn-grey">' . __('View Feed and Retry', 'custom-facebook-feed') . '</button>';
				}
				$directions .= '</p>';
			}
		} else {

		}
		return [
			'error_message' => $error_message,
			'directions' => $directions
		];
	}

	public function are_critical_errors()
	{
		$are_errors = false;
		$errors = $this->get_errors();
		if (
			(isset($errors['connection']['critical']) && $errors['connection']['critical'] === true) ||
			CFF_Source::should_show_group_deprecation()
		) {
			return true;
		} else {
			$connected_accounts = CFF_Utils::cff_get_connected_accounts();
			foreach ($connected_accounts as $connected_account) {
				$connected_account = (array) $connected_account;

				if (isset($connected_account['account_id']) && isset($this->errors['accounts'][$connected_account['account_id']]['api'])) {
					if (isset($this->errors['accounts'][$connected_account['account_id']]['api']['error'])) {
						return $this->is_critical_error($this->errors['accounts'][$connected_account['account_id']]['api']);
					}
				}
			}
		}
		return $are_errors;
	}

	/**
	 * Stores a time stamped string of information about
	 * actions that might lead to correcting an error
	 *
	 * @param string $log_item
	 *
	 * @since 2.19
	 */
	public function add_action_log( $log_item ) {
		$current_log = $this->errors['action_log'];

		if ( is_array( $current_log ) && count( $current_log ) >= 10 ) {
			reset( $current_log );
			unset( $current_log[ key( $current_log ) ] );
		}
		$current_log[] = date( 'm-d H:i:s' ) . ' - ' . $log_item;

		$this->errors['action_log'] = $current_log;
		update_option( $this->reporter_key, $this->errors, false );
	}

	/**
	 * @return mixed
	 *
	 * @since 2.19
	 */
	public function get_action_log() {
		return $this->errors['action_log'];
	}


	/**
	 * Load the critical notice for logged in users.
	 */
	public function critical_error_notice() {
		// Don't do anything for guests.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Only show this to users who are not tracked.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( ! $this->are_critical_errors() ) {
			return;
		}


		// Don't show if already dismissed.
		if ( get_option( 'cff_dismiss_critical_notice', false ) ) {
			return;
		}

		/** TODO: Match real option */
		$options = get_option('cff_settings' );
		if ( isset( $options['disable_admin_notice'] ) && $options['disable_admin_notice'] === 'on' ) {
			return;
		}

		?>
		<div class="cff-critical-notice cff-critical-notice-hide">
			<div class="cff-critical-notice-icon">
				<img src="<?php echo CFF_PLUGIN_URL . 'admin/assets/img/cff-icon.png'; ?>" width="45" alt="Custom Facebook Feed icon" />
			</div>
			<div class="cff-critical-notice-text">
				<h3><?php esc_html_e( 'Facebook Feed Critical Issue', 'custom-facebook-feed' ); ?></h3>
				<p>
					<?php
					$doc_url = admin_url() . 'admin.php?page=cff-settings';
					// Translators: %s is the link to the article where more details about critical are listed.
					printf( esc_html__( 'An issue is preventing your Custom Facebook Feeds from updating. %1$sResolve this issue%2$s.', 'custom-facebook-feed' ), '<a href="' . esc_url( $doc_url ) . '" target="_blank">', '</a>' );
					?>
				</p>
			</div>
			<div class="cff-critical-notice-close">&times;</div>
		</div>
		<style type="text/css">
			.cff-critical-notice {
                position: fixed;
                bottom: 20px;
                right: 15px;
                font-family: Arial, Helvetica, "Trebuchet MS", sans-serif;
                background: #fff;
                box-shadow: 0 0 10px 0 #dedede;
                padding: 10px 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 325px;
                max-width: calc( 100% - 30px );
                border-radius: 6px;
                transition: bottom 700ms ease;
                z-index: 10000;
			}

			.cff-critical-notice h3 {
				font-size: 13px;
				color: #222;
				font-weight: 700;
				margin: 0 0 4px;
				padding: 0;
				line-height: 1;
				border: none;
			}

			.cff-critical-notice p {
				font-size: 12px;
				color: #7f7f7f;
				font-weight: 400;
				margin: 0;
				padding: 0;
				line-height: 1.2;
				border: none;
			}

			.cff-critical-notice p a {
				color: #7f7f7f;
				font-size: 12px;
				line-height: 1.2;
				margin: 0;
				padding: 0;
				text-decoration: underline;
				font-weight: 400;
			}

			.cff-critical-notice p a:hover {
				color: #666;
			}

			.cff-critical-notice-icon img {
				height: auto;
				display: block;
				margin: 0;
			}

			.cff-critical-notice-icon {
				padding: 0;
				border-radius: 4px;
				flex-grow: 0;
				flex-shrink: 0;
				margin-right: 12px;
				overflow: hidden;
			}

			.cff-critical-notice-close {
				padding: 10px;
				margin: -12px -9px 0 0;
				border: none;
				box-shadow: none;
				border-radius: 0;
				color: #7f7f7f;
				background: transparent;
				line-height: 1;
				align-self: flex-start;
				cursor: pointer;
				font-weight: 400;
			}
			.cff-critical-notice-close:hover,
			.cff-critical-notice-close:focus{
				color: #111;
			}

			.cff-critical-notice.cff-critical-notice-hide {
				bottom: -200px;
			}
		</style>
		<?php

		if ( ! wp_script_is( 'jquery', 'queue' ) ) {
			wp_enqueue_script( 'jquery' );
		}
		?>
		<script>
            if ( 'undefined' !== typeof jQuery ) {
                jQuery( document ).ready( function ( $ ) {
                    /* Don't show the notice if we don't have a way to hide it (no js, no jQuery). */
                    $( document.querySelector( '.cff-critical-notice' ) ).removeClass( 'cff-critical-notice-hide' );
                    $( document.querySelector( '.cff-critical-notice-close' ) ).on( 'click', function ( e ) {
                        e.preventDefault();
                        $( this ).closest( '.cff-critical-notice' ).addClass( 'cff-critical-notice-hide' );
                        $.ajax( {
                            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                            method: 'POST',
                            data: {
                                action: 'cff_dismiss_critical_notice',
                                nonce: '<?php echo esc_js( wp_create_nonce( 'cff-critical-notice' ) ); ?>',
                            }
                        } );
                    } );
                } );
            }
		</script>
		<?php
	}

	/**
	 * Ajax handler to hide the critical notice.
	 */
	public function dismiss_critical_notice() {

		check_ajax_referer( 'cff-critical-notice', 'nonce' );
		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		update_option( 'cff_dismiss_critical_notice', 1, false );

		wp_die();

	}

	public function send_report_email() {
		$options = get_option( 'cff_style_settings', array() );

		$to_string = ! empty( $options['email_notification_addresses'] ) ? str_replace( ' ', '', $options['email_notification_addresses'] ) : get_option( 'admin_email', '' );

		$to_array_raw = explode( ',', $to_string );
		$to_array = array();

		foreach ( $to_array_raw as $email ) {
			if ( is_email( $email ) ) {
				$to_array[] = $email;
			}
		}

		if ( empty( $to_array ) ) {
			return false;
		}
		$from_name = esc_html( wp_specialchars_decode( get_bloginfo( 'name' ) ) );
		$email_from = $from_name . ' <' . get_option( 'admin_email', $to_array[0] ) . '>';
		$header_from  = "From: " . $email_from;

		$headers = array( 'Content-Type: text/html; charset=utf-8', $header_from );

		$header_image = CFF_PLUGIN_URL . 'admin/assets/img/balloon-120.png';
		$title = __( 'Custom Facebook Feed Report for ' . home_url() );
		$link = admin_url( 'admin.php?page=cff-settings');
		//&tab=customize-advanced
		$footer_link = admin_url('admin.php?page=cff-style&tab=misc&flag=emails');
		$bold = __( 'There\'s an Issue with a Facebook Feed on Your Website', 'custom-facebook-feed' );
		$details = '<p>' . __( 'A Custom Facebook Feed on your website is currently unable to connect to Facebook to retrieve new posts. Don\'t worry, your feed is still being displayed using a cached version, but is no longer able to display new posts.', 'custom-facebook-feed' ) . '</p>';
		$details .= '<p>' . sprintf( __( 'This is caused by an issue with your Facebook account connecting to the Facebook API. For information on the exact issue and directions on how to resolve it, please visit the %sCustom Facebook Feed settings page%s on your website.', 'custom-facebook-feed' ), '<a href="' . esc_url( $link ) . '">', '</a>' ). '</p>';
		$message_content = '<h6 style="padding:0;word-wrap:normal;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-weight:bold;line-height:130%;font-size: 16px;color:#444444;text-align:inherit;margin:0 0 20px 0;Margin:0 0 20px 0;">' . $bold . '</h6>' . $details;
		$educator = new CFF_Education();
		$dyk_message = $educator->dyk_display();
		ob_start();
		include_once CFF_PLUGIN_DIR . 'email.php';
		$email_body = ob_get_contents();
		ob_get_clean();
		$sent = wp_mail( $to_array, $title, $email_body, $headers );

		return $sent;
	}

	/**
	 * Should clear platform data
	 *
	 * @param $details
	 *
	 * @return bool
	 *
	 * @since 2.7/5.10
	 */
	public function is_app_permission_related($details)
	{
		$error_code = (int) $details['error']['code'];
		$critical_codes = array(
			190, // access token or permissions
		);
		return in_array($error_code, $critical_codes, true) && strpos($details['error']['message'], 'user has not authorized application') !== false;
	}

	public function maybe_trigger_report_email_send() {
		if ( ! $this->are_critical_errors() ) {
			return;
		}
		/** TODO: Match real option */
		$options = get_option('cff_settings' );

		if ( isset( $options['enable_email_report'] ) && empty( $options['enable_email_report'] ) ) {
			return;
		}

		$this->send_report_email();
	}

	public function admin_error_notices() {

		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'cff-settings' )) ) {
			$errors = $this->get_errors();
			if ( ! empty( $errors ) && (! empty( $errors['database_create'] ) || ! empty( $errors['upload_dir'] )) ) : ?>
			<div class="cff-admin-notices cff-critical-error-notice">
	            <?php if ( ! empty( $errors['database_create'] ) ) echo '<p>' . $errors['database_create'] . '</p>'; ?>
				<?php if ( ! empty( $errors['upload_dir'] ) ) echo '<p>' . $errors['upload_dir'] . '</p>'; ?>
				<p><?php _e( sprintf( 'Visit our %s page for help', '<a href="https://smashballoon.com/custom-facebook-feed/faq/" class="cff-notice-btn cff-btn-grey" target="_blank">FAQ</a>' ), 'custom-facebook-feed' ); ?></p>
            </div>

		<?php endif;
			$errors = $this->get_critical_errors();
			if ( $this->are_critical_errors() && is_array( $errors ) && $errors['error_message'] !== false && $errors['directions'] !== false  ) :
				?>
				<div class="cff-admin-notices cff-critical-error-notice">
					<span class="sb-notice-icon sb-error-icon">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="#D72C2C"/>
						</svg>
					</span>
					<div class="cff-notice-body">
						<h3 class="sb-notice-title">
							<?php echo esc_html__( 'Custom Facebook Feed is encountering an error and your feeds may not be updating due to the following reasons:', 'custom-facebook-feed') ; ?>
						</h3>

						<p><?php echo $errors['error_message']; ?></p>

						<div class="license-action-btns">
							<?php echo $errors['directions']; ?>
						</div>
					</div>
				</div>
			<?php
			endif;

			/*
			$errors = $this->get_critical_errors();
			if ( $this->are_critical_errors() && ! empty( $errors ) ) :
				if ( isset( $errors['wp_remote_get'] ) ) {
					$error = $errors['wp_remote_get'];
					$error_message = $error['admin_message'];
					$button = $error['backend_directions'];
					$post_id = $error['post_id'];
					$directions = '<p class="cff-error-directions">';
					$directions .= $button;
					$directions .= '<button data-url="'.get_the_permalink( $post_id ).'" class="cff-clear-errors-visit-page cff-space-left button button-secondary">' . __( 'View Feed and Retry', 'custom-facebook-feed' )  . '</button>';
					$directions .=	'</p>';
				} elseif ( isset( $errors['api'] ) ) {
					$error = $errors['api'];
					$error_message = $error['admin_message'];
					$button = $error['backend_directions'];
					$post_id = $error['post_id'];
					$directions = '<p class="cff-error-directions">';
					$directions .= $button;
					$directions .= '<button data-url="'.get_the_permalink( $post_id ).'" class="cff-clear-errors-visit-page cff-space-left button button-secondary">' . __( 'View Feed and Retry', 'custom-facebook-feed' )  . '</button>';
					$directions .=	'</p>';
				} else {
					$error = $errors['accesstoken'];

					$tokens = array();
					$post_id = false;
					foreach ( $error as $token ) {
						$tokens[] = $token['accesstoken'];
						$post_id = $token['post_id'];
					}
					$error_message = sprintf( __( 'The access token %s is invalid or has expired.', 'custom-facebook-feed' ), implode( ', ', $tokens ) );
					$directions = '<p class="cff-error-directions">';
					$directions .= '<button class="button button-primary cff-reconnect">' . __( 'Reconnect Your Account', 'custom-facebook-feed' )  . '</button>';
					$directions .= '<button data-url="'.get_the_permalink( $post_id ).'" class="cff-clear-errors-visit-page cff-space-left button button-secondary">' . __( 'View Feed and Retry', 'custom-facebook-feed' )  . '</button>';
					$directions .=	'</p>';
				}
				?>
				<div class="notice notice-warning is-dismissible cff-admin-notice">
					<p><strong><?php echo esc_html__( 'Custom Facebook Feed is encountering an error and your feeds may not be updating due to the following reasons:', 'custom-facebook-feed') ; ?></strong></p>

					<?php echo $error_message; ?>

					<?php echo $directions; ?>
				</div>
			<?php endif;
			*/

		}

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

	public function get_app_permission_related_error_ids()
	{
		return $this->errors['revoked'];
	}


	public function platform_data_deleted_notice()
	{
		$errors = $this->get_errors();
		if (!empty($errors) && (!empty($errors['platform_data_deleted']))) {
			?>
						<div class="cff-admin-notices cff-critical-error-notice">
							<span class="sb-notice-icon sb-error-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="#D72C2C"/>
								</svg>
							</span>
							<div class="cff-notice-body">
								<h3 class="sb-notice-title">
									<?php echo esc_html__('All Facebook Data has Been Removed:', 'custom-facebook-feed'); ?>
								</h3>
								<p><?php echo $errors['platform_data_deleted']; ?></p>
								<p><?php echo esc_html__('To fix your feeds, reconnect all accounts that were in use on the Settings page.', 'custom-facebook-feed'); ?></p>

							</div>
						</div>
					<?php
		}

	}

	public function platform_unused_feed_notice()
	{
		$errors = $this->get_errors();
		if (!empty($errors) && (!empty($errors['unused_feed']))) {
			?>
						<div class="cff-admin-notices cff-critical-error-notice">
							<span class="sb-notice-icon sb-error-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="#D72C2C"/>
								</svg>
							</span>
							<div class="cff-notice-body">
								<h3 class="sb-notice-title">
									<?php echo esc_html__('Action Required Within 7 Days:', 'custom-facebook-feed'); ?>
								</h3>

								<p><?php echo $errors['unused_feed']; ?></p>
								<p><?php echo esc_html__('Or you can simply press the "Fix Usage" button to fix this issuee.', 'custom-facebook-feed'); ?></p>
								<div class="license-action-btns">
									<button class="sbi-reset-unused-feed-usage sbi-space-left sbi-btn sbi-notice-btn sbi-btn-blue"><?php echo __('Fix Usage', 'custom-facebook-feed'); ?></button>
								</div>
							</div>
						</div>
					<?php
		}
	}
	/**
	 * Should Add deprecation error for Groups
	 *
	 * @param $group_id
	 *
	 * @since X.X.X
	 */
	public function add_group_deprecation_error($group_id)
	{
		$group_deprecation_error = [
			'group_ids' => []
		];
		if (isset($this->errors['group_deprecation'])) {
			$group_deprecation_error['group_ids'] = $this->errors['group_deprecation']['group_ids'];
		}
		if (!in_array($group_id, $group_deprecation_error['group_ids'])) {
			$group_deprecation_error['dimissed'] = false;
			array_push($group_deprecation_error['group_ids'], $group_id);
		}
		$this->errors['group_deprecation'] = $group_deprecation_error;

		update_option($this->reporter_key, $this->errors, false);
	}

	/**
	 * Dismiss Group Notice
	 *
	 * @param $group_id
	 *
	 * @since X.X.X
	 */
	public function dismiss_group_deprecation_error()
	{
		if (isset($this->errors['group_deprecation'])) {
			$this->errors['group_deprecation']['dismissed'] = true;
			update_option($this->reporter_key, $this->errors, false);
		}
	}
	public function group_deprecation_notice()
	{
		$errors = $this->get_errors();
		if (
			!empty($errors) && !empty($errors['group_deprecation']) &&
			(!isset($errors['group_deprecation']['dismissed']) || $errors['group_deprecation']['dismissed'] !== true)
		) {
			$close_href = add_query_arg(array('cff_dismiss_notice' => 'group_deprecation'));
		?>
			<div class="cff-admin-notices cff-critical-error-notice">
				<span class="sb-notice-icon sb-error-icon">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="#D72C2C"/>
					</svg>
				</span>
				<div class="cff-notice-body">
					<h3 class="sb-notice-title sb-noticegroup-title">
						<?php echo esc_html__('Group feeds will no longer update as of April 22, 2024 :', 'custom-facebook-feed') ; ?>
					</h3>
					<p>
						<?php
							echo
							__('You have one or more feeds that will no longer update after April 22, 2024. This is caused by a change in Facebook\'s API, which we use to get new data for feed updates.', 'custom-facebook-feed') ;
						?>
					</p>
					<br/>

					<p class="cff-error-directions">
						<a
							class="cff-notice-btn cff-btn-blue" target="_blank" rel="noopener"
							href="https://smashballoon.com/doc/facebook-api-changes-affecting-groups-april-2024">
							<?php echo esc_html__('Learn More', 'custom-facebook-feed') ; ?>
						</a>
						<a class="cff-notice-btn" href="<?php echo esc_attr($close_href); ?>" rel="noopener"><?php echo esc_html__('Dismiss', 'custom-facebook-feed') ; ?></a>
					</p>


				</div>
			</div>
		<?php
		}

	}
}
