<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\HTTP_Request;
use SmashBalloon\YouTubeFeed\Response;
use SmashBalloon\YouTubeFeed\Services\LicenseNotification;

class LicenseService extends ServiceProvider {

	private $license_service;
	
	public function __construct() {
		$this->license_service = new LicenseNotification();
	}

	public function register() {
		/* Display a license expired notice */
		add_action('sby_admin_notices', [$this, 'sby_renew_license_notice']);
		add_action('sby_admin_header_notices', [$this, 'sby_admin_header_license_notice']);

		// Add extra localize script items
		add_filter('sby_localized_settings', [$this, 'localized_license_settings']);
		add_action('wp_ajax_sby_check_connection', [$this, 'test_connection']);
		add_action('wp_ajax_sby_license_activation', [$this, 'ajax_activate_license']);
		add_action('wp_ajax_sby_license_deactivation', [$this, 'ajax_deactivate_license']);
		add_action( 'wp_ajax_sby_check_license', [ $this, 'check_license' ] );
		add_action( 'wp_ajax_sby_dismiss_license_notice', [ $this, 'dismiss_license_notice' ] );
	}

	public function localized_license_settings($settings) {
		$license_key = $this->get_license_key();

		$settings['licenseStatus'] = $this->get_license_status();
		$settings['licenseData']   = $this->get_license_data();
		$settings['licenseKey']    = $license_key;
		$settings['upgradeUrl']    = sprintf( 'https://smashballoon.com/youtube-feed/pricing/?edd_license_key=%s&upgrade=true&utm_campaign=youtube-pro&utm_source=settings&utm_medium=upgrade-license', $this->get_license_key() );
		return $settings;
	}

	public function test_connection() {
		check_ajax_referer( 'sby-admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $this->get_license_key(),
			'item_name'  => urlencode( SBY_PLUGIN_EDD_NAME ), // the name of our product in EDD
		);
		$url            = add_query_arg( $api_params, SBY_STORE_URL );
		$args           = array(
			'timeout'   => 60,
		);
		// Make the remote API request
		$request = HTTP_Request::request( 'GET', $url, $args );

		if ( HTTP_Request::is_error( $request ) ) {
			$response = new Response(
				false,
				array(
					'hasError' => true,
				)
			);
			$response->send();
		}

		$response = new Response(
			true,
			array(
				'hasError' => false,
			)
		);

		$response->send();
	}

	public function ajax_deactivate_license() {
		check_ajax_referer( 'sby-admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$response = $this->sby_deactivate_license();

		if ( $response === true ) {
			wp_send_json_success( [
				'licenseStatus' => $this->get_license_status(),
				'licenseData'   => $this->get_license_data()
			] );
		}

		wp_send_json_error();
	}

	public function ajax_activate_license() {
		check_ajax_referer( 'sby-admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$license_key = sanitize_text_field($_POST['license_key']);

		$response = $this->sby_activate_license($license_key);

		if ( $response === true ) {
			wp_send_json_success( [
				'licenseStatus' => $this->get_license_status(),
				'licenseData'   => $this->get_license_data()
			] );
		}

		wp_send_json_error();
	}

	public function sby_activate_license($license_key) {
		// retrieve the license from the database
		$sby_license = trim( $license_key );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $sby_license,
			'item_name'  => urlencode( SBY_PLUGIN_EDD_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, SBY_STORE_URL ),
			array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$sby_license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 
			isset( $sby_license_data->success ) && ( $sby_license_data->success == false ) ||
			isset( $sby_license_data->error ) && ( $sby_license_data->error == 'missing' ) ||
			isset( $sby_license_data->license ) && (
				$sby_license_data->license == 'invalid_item_id' ||
				$sby_license_data->license == 'invalid' ||
				$sby_license_data->license == 'expired'
			)
		) {
			return false;
		}

		// only store the license key
		update_option( 'sby_license_key', $license_key );
		//store the license data in an option
		update_option( 'sby_license_data', $sby_license_data );
		// $license_data->license will be either "valid" or "invalid"
		update_option( 'sby_license_status', $sby_license_data->license );
		// make license check_api true so next time it expires it checks again
		update_option( 'sby_check_license_api_when_expires', 'true' );
		update_option( 'sby_check_license_api_post_grace_period', 'true' );

		return true;
	}

	public function sby_deactivate_license() {

		// retrieve the license from the database
		$sby_license= trim( get_option( 'sby_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license'   => $sby_license,
			'item_name' => urlencode( SBY_PLUGIN_EDD_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, SBY_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$sby_license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $sby_license_data->license == 'deactivated' || $sby_license_data->license == 'failed' ) {
			delete_option( 'sby_license_data' );
			delete_option( 'sby_license_status' );
		}

		return true;
	}

	/**
	 * Check license key
	 * 
	 * @since 2.0.2
	 */
	public function sby_check_license( $sby_license, $check_license_status = false ) {
		// data to send in our API request
		$sby_api_params = array(
			'edd_action'=> 'check_license',
			'license'   => $sby_license,
			'item_name' => urlencode( SBY_PLUGIN_NAME ) // the name of our product in EDD
		);
		$api_url = add_query_arg( $sby_api_params, SBY_STORE_URL );
		$args = array(
			'timeout' => 60,
			'sslverify' => false
		);
		// Call the custom API.
		$request = wp_remote_get( $api_url, $args );
		if ( is_wp_error( $request ) ) {
			return;
		}
		// decode the license data
		$sby_license_data = json_decode( wp_remote_retrieve_body( $request ) );

		if ( $check_license_status ) {
			//Check whether it's active
			if( $sby_license_data['license'] !== 'expired' && ( strtotime( $sby_license_data['expires'] ) > strtotime( $sby_todays_date ) ) ){
				$sby_license_status = false;
			} else {
				$sby_license_status = true;
				//Set a flag so it doesn't check the API again until the next time it expires
				update_option( 'sby_check_license_api_when_expires', 'false' );
			}

			return $sby_license_status;
		}

		//Store license data in db
		update_option( 'sby_license_data', $sby_license_data );

		return $sby_license_data;
	}

	public function sby_renew_license_notice() {
		if ( !current_user_can( Util::sby_capability_check() ) ) {
			return;
		}
		// We will display the license notice only on specified allowed screens
		if ( ! Util::isCurrentScreenAllowed()  ) {
			return;
		}
		// Check that the license exists and the user hasn't already clicked to ignore the message
		if ( empty( Util::get_license_key() ) ) {
			return;
		}
		// If license not expired then return;
		if ( !Util::is_license_expired() ) {
			return;
		}
		// Grace period ended?
		if ( Util::is_license_grace_period_ended() ) {
			return;
		}
		// So, license has expired and grace period active
		// Lets display the error notice
		echo $this->get_expired_license_notice_content();
	}

	public function old_sby_renew_license_notice() {

		//Show this notice on every page apart from the YouTube Feed settings pages
		isset($_GET['page'])? $sby_check_page = $_GET['page'] : $sby_check_page = '';
		( $sby_check_page == 'youtube-feed' || $sby_check_page == 'sby_single_settings' || $sby_check_page == 'youtube-feed_license' ) ? $sby_notice_dismissible = false : $sby_notice_dismissible = true;

		//If the user is re-checking the license key then use the API below to recheck it
		( isset( $_GET['sbychecklicense'] ) ) ? $sby_check_license = true : $sby_check_license = false;

		$sby_license = trim( get_option( 'sby_license_key' ) );

		global $current_user;
		$user_id = $current_user->ID;

		// Use this to show notice again
		// delete_user_meta($user_id, 'sby_ignore_notice');

		/* Check that the license exists and the user hasn't already clicked to ignore the message */
		if( empty($sby_license) || !isset($sby_license) || ( ( get_user_meta( $user_id,
						'sby_ignore_notice' ) && $sby_notice_dismissible ) && ! $sby_check_license ) ) {
			return;
		}

		//Is there already license data in the db?
		if( get_option( 'sby_license_data' ) && !$sby_check_license ){
			//Yes
			//Get license data from the db and convert the object to an array
			$sby_license_data = (array) get_option( 'sby_license_data' );
		} else {
			//No
			// data to send in our API request
			$sby_api_params = array(
				'edd_action'=> 'check_license',
				'license'   => $sby_license,
				'item_name' => urlencode( SBY_PLUGIN_EDD_NAME ) // the name of our product in EDD
			);

			// Call the custom API.
			$sby_response = wp_remote_get( add_query_arg( $sby_api_params, SBY_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

			// decode the license data
			$sby_license_data = (array) json_decode( wp_remote_retrieve_body( $sby_response ) );

			//Store license data in db
			update_option( 'sby_license_data', $sby_license_data );
		}

		//Number of days until license expires
		$sby_license_expires_date = isset( $sby_license_data['expires'] ) ? $sby_license_data['expires'] : $sby_license_expires_date = '2036-12-31 23:59:59'; //If expires param isn't set yet then set it to be a date to avoid PHP notice
		if( $sby_license_expires_date == 'lifetime' ) $sby_license_expires_date = '2036-12-31 23:59:59';
		$sby_todays_date = date('Y-m-d');
		$sby_interval = round(abs(strtotime($sby_todays_date . ' -1 day')-strtotime($sby_license_expires_date))/86400); //-1 day to make sure auto-renewal has run before showing expired

		//Is license expired?
		if( $sby_interval == 0 || strtotime($sby_license_expires_date) < strtotime($sby_todays_date) ){

			//If we haven't checked the API again one last time before displaying the expired notice then check it to make sure the license hasn't been renewed
			if( get_option( 'sby_check_license_api_when_expires' ) == FALSE || get_option( 'sby_check_license_api_when_expires' ) == 'true' ){

				// Check the API
				$sby_api_params = array(
					'edd_action'=> 'check_license',
					'license'   => $sby_license,
					'item_name' => urlencode( SBY_PLUGIN_EDD_NAME ) // the name of our product in EDD
				);
				$sby_response = wp_remote_get( add_query_arg( $sby_api_params, SBY_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );
				$sby_license_data = (array) json_decode( wp_remote_retrieve_body( $sby_response ) );

				//Check whether it's active
				if( $sby_license_data['license'] !== 'expired' && ( strtotime( $sby_license_data['expires'] ) > strtotime($sby_todays_date) ) ){
					$sby_license_expired = false;
				} else {
					$sby_license_expired = true;
					//Set a flag so it doesn't check the API again until the next time it expires
					update_option( 'sby_check_license_api_when_expires', 'false' );
				}

				//Store license data in db
				update_option( 'sby_license_data', $sby_license_data );

			} else {
				//Display the expired notice
				$sby_license_expired = true;
			}

		} else {
			$sby_license_expired = false;

			//License is not expired so change the check_api setting to be true so the next time it expires it checks again
			update_option( 'sby_check_license_api_when_expires', 'true' );
		}

		//If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
		if( $sby_license_expires_date[0] == '1' ) $sby_license_expired = false;

		//If there's no expired date then don't show the expired notification
		if( empty($sby_license_expires_date) || !isset($sby_license_expires_date) ) $sby_license_expired = false;

		//Is license missing - ie. on very first check
		if( isset($sby_license_data['error']) ){
			if( $sby_license_data['error'] == 'missing' ) $sby_license_expired = false;
		}


		//Is the license expired?
		if( $sby_license_expired || $sby_check_license ) {

			global $sby_download_id;

			$sby_expired_box_classes = "sby-license-expired";

			$sby_expired_box_msg = '<svg style="width:16px;height:16px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-exclamation-triangle fa-w-18 fa-2x"><path fill="currentColor" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z" class=""></path></svg>';
			$sby_expired_box_msg .= "<b>Important: Your YouTube Feeds Pro license key has expired.</b><br /><span>You are no longer receiving updates that protect you against upcoming YouTube platform changes.</span>";

			//Create the re-check link using the existing query string in the URL
			$sby_url = '?' . $_SERVER["QUERY_STRING"];
			//Determine the separator
			( !empty($sby_url) && $sby_url != '' ) ? $separator = '&' : $separator = '';
			//Add the param to check license if it doesn't already exist in URL
			if( strpos($sby_url, 'sbychecklicense') === false ) $sby_url .= $separator . "sbychecklicense=true";

			//Create the notice message
			$sby_expired_box_msg .= " &nbsp;<a href='https://smashballoon.com/checkout/?edd_license_key=".$sby_license."&download_id=".$sby_download_id."&utm_source=plugin-pro&utm_campaign=youtube-pro&utm_medium=expired-notice-dashboard' target='_blank' class='button button-primary'>Renew License</a><a href='javascript:void(0);' id='sby-why-renew-show' onclick='sbyShowReasons()' class='button button-secondary'>Why renew?</a><a href='javascript:void(0);' id='sby-why-renew-hide' onclick='sbyHideReasons()' class='button button-secondary' style='display: none;'>Hide text</a> <a href='".$sby_url."' class='button button-secondary'>Re-check License</a></p>
            <div id='sby-why-renew' style='display: none;'>
                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='shield-check' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-shield-check fa-w-16 fa-2x' data-ce-key='470'><path fill='currentColor' d='M466.5 83.7l-192-80a48.15 48.15 0 0 0-36.9 0l-192 80C27.7 91.1 16 108.6 16 128c0 198.5 114.5 335.7 221.5 380.3 11.8 4.9 25.1 4.9 36.9 0C360.1 472.6 496 349.3 496 128c0-19.4-11.7-36.9-29.5-44.3zm-47.2 114.2l-184 184c-6.2 6.2-16.4 6.2-22.6 0l-104-104c-6.2-6.2-6.2-16.4 0-22.6l22.6-22.6c6.2-6.2 16.4-6.2 22.6 0l70.1 70.1 150.1-150.1c6.2-6.2 16.4-6.2 22.6 0l22.6 22.6c6.3 6.3 6.3 16.4 0 22.6z' class='' data-ce-key='471'></path></svg>Protected Against All Upcoming YouTube Platform Updates and API Changes</h4>
                <p>You currently don't need to worry about your YouTube feeds breaking due to constant changes in the YouTube platform. You are currently protected by access to continual plugin updates, giving you peace of mind that the software will always be up to date.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fab' data-icon='wordpress' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-wordpress fa-w-16 fa-2x'><path fill='currentColor' d='M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8.9 0 1.8.1 2.8.2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7.3 13.7.3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z' class=''></path></svg>WordPress Compatability Updates</h4>
                <p>With WordPress updates being released continually, we make sure the plugin is always compatible with the latest version so you can update WordPress without needing to worry.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='far' data-icon='life-ring' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-life-ring fa-w-16 fa-2x' data-ce-key='500'><path fill='currentColor' d='M256 504c136.967 0 248-111.033 248-248S392.967 8 256 8 8 119.033 8 256s111.033 248 248 248zm-103.398-76.72l53.411-53.411c31.806 13.506 68.128 13.522 99.974 0l53.411 53.411c-63.217 38.319-143.579 38.319-206.796 0zM336 256c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80zm91.28 103.398l-53.411-53.411c13.505-31.806 13.522-68.128 0-99.974l53.411-53.411c38.319 63.217 38.319 143.579 0 206.796zM359.397 84.72l-53.411 53.411c-31.806-13.505-68.128-13.522-99.973 0L152.602 84.72c63.217-38.319 143.579-38.319 206.795 0zM84.72 152.602l53.411 53.411c-13.506 31.806-13.522 68.128 0 99.974L84.72 359.398c-38.319-63.217-38.319-143.579 0-206.796z' class='' data-ce-key='501'></path></svg>Expert Technical Support</h4>
                <p>Without a valid license key you will no longer be able to receive updates or support for the YouTube Feeds plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='unlock' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' class='svg-inline--fa fa-unlock fa-w-14 fa-2x' data-ce-key='477'><path fill='currentColor' d='M400 256H152V152.9c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v16c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-16C376 68 307.5-.3 223.5 0 139.5.3 72 69.5 72 153.5V256H48c-26.5 0-48 21.5-48 48v160c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V304c0-26.5-21.5-48-48-48z' class='' data-ce-key='478'></path></svg>All Pro YouTube Feeds Features</h4>
                <p>Live Streaming API, Custom End-of-Video Actions, YouTube Search API, Carousel Sliders, Combine Feeds, Convert Videos to WP Posts, Favorites, Video Filtering, Smart Video Player Loading, and more!</p>
            </div>";

			if( $sby_check_license && !$sby_license_expired ){
				$sby_expired_box_classes = "sby-license-expired sby-license-valid";
				$sby_expired_box_msg = "Thanks ".$sby_license_data["customer_name"].", your YouTube Feeds Pro license key is valid.";
			}

			_e("<div class='".$sby_expired_box_classes."'>");
			if( $sby_notice_dismissible ){
				_e("<a style='float:right; color: #dd3d36; text-decoration: none;' href='" .esc_url( add_query_arg( 'sby_nag_ignore', '0' ) ). "'>Dismiss</a>");
			}
			_e("<p>".$sby_expired_box_msg."
        </div>
        <script type='text/javascript'>
        function sbyShowReasons() {
            document.getElementById('sby-why-renew').style.display = 'block';
            document.getElementById('sby-why-renew-show').style.display = 'none';
            document.getElementById('sby-why-renew-hide').style.display = 'inline-block';
        }
        function sbyHideReasons() {
            document.getElementById('sby-why-renew').style.display = 'none';
            document.getElementById('sby-why-renew-show').style.display = 'inline-block';
            document.getElementById('sby-why-renew-hide').style.display = 'none';
        }
        </script>
        <style>.sby-license-expired{clear:both;width:96%;margin:10px 0 20px 0;background:#f7e6e6;padding:15px 1.5%;border:1px solid #ba7b7b;color:#592626;border-left:4px solid #dc3232;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px}.sby-license-valid{border:1px solid #5baf2a;background:#e2f2d5}.sby-license-expired p{padding:0;margin:0 5px 0 0}.sby-license-expired b{display:inline-block;padding:0;font-size:15px}#sby-why-renew{clear:both;width:100%}#sby-why-renew h4{clear:both;margin:15px 0 0 0;font-size:15px}#sby-why-renew p{margin:0;clear:both}.sby-license-expired .button-primary{margin:0 4px 0 5px}.sby-license-expired .button-secondary{margin:0 0 0 3px;background:rgba(255,255,255,.3);border:1px solid rgba(0,0,0,.15);display:inline-block}.sby-license-expired svg{width:16px;height:16px;position:relative;top:2px;margin:0 8px 0 0}.sby-license-expired svg path{fill:#cc2727}@media all and (max-width:600px){.sby-license-expired b{display:inline}.sby-license-expired span{display:block}.sby-license-expired .button{margin:10px 5px 0 0}}</style>
        ");
		}
	}

	/**
	 * Admin header notices
	 * 
	 * @since 2.0.2
	 */
	public function sby_admin_header_license_notice () {
		if ( !sby_is_pro() ) {
			return;
		}
		if ( !current_user_can( Util::sby_capability_check() ) ) {
			return;
		}
		// We will display the license notice only on specified allowed screens
		if ( ! Util::isCurrentScreenAllowed()  ) {
			return;
		}
		// Check that the license exists and the user hasn't already clicked to ignore the message
		if ( empty( Util::get_license_key() ) ) {
			echo $this->get_post_grace_period_header_notice( $inactive = 'sby-license-inactive-state' );
			return;
		}
		// If license not expired then return;
		$license_expired = Util::is_license_expired();
		if ( !$license_expired ) {
			return;
		}
		// Grace period ended?
		if ( Util::is_license_grace_period_ended( true ) ) {
			if ( get_option( 'sby_check_license_api_post_grace_period' ) !== 'false' ) {
				$license_expired = Util::sby_check_license( Util::get_license_key(), true, true );
			}
			if ( $license_expired ) {
				echo $this->get_post_grace_period_header_notice();
			}
		}
		return;
	}

	/**
	 * Get post grace period header notice content
	 * 
	 * @since 2.0
	 */
	public function get_post_grace_period_header_notice( $license_status = 'expired' ) {
		$notice_text = 'Your YouTube Feeds Pro License has expired. Renew to keep using PRO features.';
		if ( $license_status == 'sby-license-inactive-state' ) {
			$notice_text = 'Your license key is inactive. Please add license key to enable PRO features.';
		}
		return '<div id="sby-license-expired-agp" class="sby-license-expired-agp sby-le-flow-1 '. $license_status .'">
			<span class="sby-license-expired-agp-message">'. $notice_text .' <span @click.prevent.default="activateView(\'licenseLearnMore\')">Learn More</span></span>
			<button type="button" id="sby-dismiss-header-notice" title="Dismiss this message" data-page="overview" class="sby-dismiss">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M15.8327 5.34175L14.6577 4.16675L9.99935 8.82508L5.34102 4.16675L4.16602 5.34175L8.82435 10.0001L4.16602 14.6584L5.34102 15.8334L9.99935 11.1751L14.6577 15.8334L15.8327 14.6584L11.1744 10.0001L15.8327 5.34175Z" fill="white"></path>
				</svg>
			</button>
		</div>';
	}

	private function get_license_key() {
		return Util::get_license_key();
	}

	private function recheck_license_status() {
		if ( empty( $this->get_license_key() ) ) {
			return [];
		}

		$license_last_check = get_option( 'sby_license_last_check_timestamp' );
		$date               = time() - ( DAY_IN_SECONDS * 90 );

		if ( $date < $license_last_check ) {
			return $this->get_license_data();
		}

		$sby_license = $this->get_license_key();

		$this->sby_activate_license($sby_license);

		update_option( 'sby_license_last_check_timestamp', time() );

		return $this->get_license_data();
	}

	public function get_license_status() {
		return get_option('sby_license_status', 'inactive');
	}

	public function get_license_data() {
		return $this->get_license_error_message( get_option( 'sby_license_data' ) );
	}

	public function get_license_error_message( $license_data ) {
		global $sby_download_id;

		$license_key = $this->get_license_key();

		$upgrade_url    = sprintf( 'https://smashballoon.com/youtube-feed/pricing/?edd_license_key=%s&upgrade=true&utm_campaign=youtube-pro&utm_source=settings&utm_medium=upgrade-license', $license_key );
		$renew_url      = sprintf( 'https://smashballoon.com/checkout/?edd_license_key=%s&download_id=%s&utm_campaign=youtube-pro&utm_source=settings&utm_medium=upgrade-license&utm_content=renew-license', $license_key, $sby_download_id );
		$learn_more_url = 'https://smashballoon.com/doc/my-license-key-wont-activate/?utm_campaign=youtube-pro&utm_source=settings&utm_medium=license&utm_content=learn-more';

		// Check if the license key reached max site installations
		if (  !empty($license_data->error) && 'no_activations_left' === $license_data->error ) {
			$license_data->errorMsg = sprintf(
				'%s (%s/%s). %s <a href="%s" target="_blank">%s</a> %s <a href="%s" target="_blank">%s</a>',
				__( 'You have reached the maximum number of sites available in your plan', 'feeds-for-youtube' ),
				$license_data->site_count,
				$license_data->max_sites,
				__( 'Learn more about it', 'feeds-for-youtube' ),
				$learn_more_url,
				'here',
				__( 'or upgrade your plan.', 'feeds-for-youtube' ),
				$upgrade_url,
				__( 'Upgrade', 'feeds-for-youtube' )
			);
		}
		// Check if the license key has expired
		if (
			( !empty( $license_data->license ) && 'expired' === $license_data->license ) ||
			( !empty( $license_data->error ) && 'expired' === $license_data->error )
		) {
			$license_data->error          = true;
			$expired_date                 = new \DateTime( $license_data->expires );
			$expired_date                 = $expired_date->format( 'F d, Y' );
			$license_data->errorMsg = sprintf(
				'%s %s. %s <a href="%s" target="_blank">%s</a>',
				__( 'The license expired on ', 'feeds-for-youtube' ),
				$expired_date,
				__( 'Please renew it and try again.', 'feeds-for-youtube' ),
				$renew_url,
				__( 'Renew', 'feeds-for-youtube' )
			);
		}
		return $license_data;
	}

	/**
	 * Get content for expired license notice
	 *
	 * @since 2.0
	 *
	 * @return string $output
	 */
	public function get_expired_license_notice_content() {
		global $current_user;
		$current_screen = get_current_screen();

		$output = '<div class="sb-license-notice">
				<h4>Your license key has expired</h4>
				<p>You are no longer receiving updates that protect you against upcoming YouTube changes. There’s a <strong>14 day</strong> grace period before access to some Pro features in the plugin will be limited.</p>
				<div class="sb-notice-buttons">
					<a href="'. $this->license_service->get_renew_url() .'" class="sb-btn sb-btn-blue" target="_blank">Renew License</a>
					<a href="#" class="sb-btn" @click.prevent.default="activateView(\'whyRenewLicense\')">Why Renew?</a>
					<a href="" class="sb-btn" @click.prevent.default="reCheckLicenseKey()" v-html="recheckBtnText()" :class="recheckLicenseStatus">Re-check License Key</a>
				</div>
				<svg class="sb-notice-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="#D72C2C"/></svg>
			</div>';

		if ( ! empty( $current_screen->base ) && $current_screen->base == 'dashboard' ) {
			$output .= '<button id="sb-dismiss-notice">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.66683 1.27325L8.72683 0.333252L5.00016 4.05992L1.2735 0.333252L0.333496 1.27325L4.06016 4.99992L0.333496 8.72659L1.2735 9.66659L5.00016 5.93992L8.72683 9.66659L9.66683 8.72659L5.94016 4.99992L9.66683 1.27325Z" fill="white"/>
                        </svg>
                    </button>';
		}

		return $output;
	}

	/**
	 * Get content for successfully renewed license notice
	 *
	 * @since 2.0
	 *
	 * @return string $output
	 */
	public function get_renewed_license_notice_content() {
		$output = '<span class="sb-notice-icon sb-error-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill="#59AB46"/>
                </svg>
            </span>
            <div class="sb-notice-body">
                <h3 class="sb-notice-title">Thanks! Your license key is valid.</h3>
                <p>You can safely dismiss this modal.</p>
                <div class="license-action-btns">
                    <a target="_blank" class="sby-license-btn sby-btn-blue sby-notice-btn" id="sby-hide-notice">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.66683 1.27325L8.72683 0.333252L5.00016 4.05992L1.2735 0.333252L0.333496 1.27325L4.06016 4.99992L0.333496 8.72659L1.2735 9.66659L5.00016 5.93992L8.72683 9.66659L9.66683 8.72659L5.94016 4.99992L9.66683 1.27325Z" fill="white"/>
                        </svg>
                        Dismiss
                    </a>
                </div>
            </div>';

		return $output;
	}

	/**
	 * Get modal content that will trigger by "Why Renew" button
	 *
	 * @since 2.0
	 *
	 * @return string $output
	 */
	public function get_modal_content() {
		$output = '<div class="sby-sb-modal license-details-modal">
            <div class="sby-modal-content">
                <button type="button" class="cancel-btn sby-btn" id="sby-sb-close-modal">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.2084 2.14275L12.8572 0.791504L7.50008 6.14859L2.143 0.791504L0.791748 2.14275L6.14883 7.49984L0.791748 12.8569L2.143 14.2082L7.50008 8.85109L12.8572 14.2082L14.2084 12.8569L8.85133 7.49984L14.2084 2.14275Z" fill="#141B38">
                        </path>
                    </svg>
                </button>
                <div class="sby-sb-modal-body">
                    <h2 class="sb-modal-title">Why Renew?</h2>
                    <p class="sb-modal-description">See below for why it\'s so important to keep an active plugin license.</p>
                    <div class="sb-why-renew-list-parent">
                        <div class="sb-why-renew-list">
                            <div class="sb-icon">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 1.33325L4 6.66659V14.6666C4 22.0666 9.12 28.9866 16 30.6666C22.88 28.9866 28 22.0666 28 14.6666V6.66659L16 1.33325Z" fill="#59AB46"/>
                                    <path d="M10.3433 16.5525L14.1145 20.3237L21.657 12.7813" stroke="white" stroke-width="2.66667"/>
                                </svg>
                            </div>
                            <div class="sb-list-item">
                                <h4>Protected Against All Upcoming YouTube Platform Updates and API Changes</h4>
                                <p>Don\'t worry about your YouTubes feeds breaking due to constant changes in the YouTube platform. Stay protected with access to continual plugin updates, giving you peace of mind that the software will always be up to date.</p>
                            </div>
                        </div>
                        <div class="sb-why-renew-list">
                            <div class="sb-icon">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.9998 2.66675C8.63984 2.66675 2.6665 8.64008 2.6665 16.0001C2.6665 23.3601 8.63984 29.3334 15.9998 29.3334C23.3598 29.3334 29.3332 23.3601 29.3332 16.0001C29.3332 8.64008 23.3598 2.66675 15.9998 2.66675ZM25.9465 12.1601L22.2398 13.6934C21.9059 12.7949 21.3814 11.9793 20.7025 11.3027C20.0235 10.626 19.2061 10.1043 18.3065 9.77341L19.8398 6.06675C22.6398 7.13341 24.8665 9.36008 25.9465 12.1601ZM15.9998 20.0001C13.7865 20.0001 11.9998 18.2134 11.9998 16.0001C11.9998 13.7867 13.7865 12.0001 15.9998 12.0001C18.2132 12.0001 19.9998 13.7867 19.9998 16.0001C19.9998 18.2134 18.2132 20.0001 15.9998 20.0001ZM12.1732 6.05341L13.7332 9.76008C12.8229 10.0918 11.9959 10.6179 11.3097 11.3018C10.6235 11.9857 10.0946 12.811 9.75984 13.7201L6.05317 12.1734C6.58782 10.7816 7.40887 9.51764 8.46313 8.46338C9.5174 7.40911 10.7814 6.58806 12.1732 6.05341ZM6.05317 19.8267L9.75984 18.2934C10.0923 19.2002 10.619 20.0233 11.303 20.705C11.9871 21.3868 12.812 21.9107 13.7198 22.2401L12.1598 25.9467C10.771 25.4097 9.51009 24.5876 8.45831 23.5335C7.40653 22.4795 6.58722 21.2167 6.05317 19.8267ZM19.8398 25.9467L18.3065 22.2401C19.2103 21.9052 20.0304 21.3775 20.7097 20.6936C21.3889 20.0098 21.9111 19.1862 22.2398 18.2801L25.9465 19.8401C25.4101 21.2272 24.5898 22.4869 23.5382 23.5385C22.4866 24.59 21.2269 25.4103 19.8398 25.9467Z" fill="#59AB46"/>
                                </svg>
                            </div>
                            <div class="sb-list-item">
                                <h4>Expert Technical Support</h4>
                                <p>Without a valid license key you will no longer be able to receive updates or support for the YouTube Feeds plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>
                            </div>
                        </div>
                        <div class="sb-why-renew-list">
                            <div class="sb-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16 0C7.16343 0 0 7.16342 0 16C0 24.8365 7.16343 32 16 32C24.8366 32 32 24.8365 32 16C32 7.16342 24.8366 0 16 0ZM16 0.96C18.0308 0.96 20.0004 1.35753 21.8539 2.14152C22.7449 2.51837 23.6044 2.98488 24.4084 3.528C25.205 4.06617 25.9541 4.68427 26.6349 5.3651C27.3157 6.04593 27.9338 6.79507 28.472 7.59163C29.0152 8.39563 29.4816 9.25507 29.8585 10.146C30.6425 11.9996 31.04 13.9692 31.04 16C31.04 18.0308 30.6425 20.0003 29.8585 21.8539C29.4816 22.7449 29.0152 23.6043 28.472 24.4083C27.9338 25.2049 27.3157 25.954 26.6349 26.6349C25.9541 27.3157 25.205 27.9338 24.4084 28.472C23.6044 29.0151 22.7449 29.4816 21.8539 29.8584C20.0004 30.6425 18.0308 31.04 16 31.04C13.9692 31.04 11.9996 30.6425 10.1461 29.8584C9.25508 29.4816 8.39564 29.0151 7.59164 28.472C6.79508 27.9338 6.04594 27.3157 5.36511 26.6349C4.68428 25.954 4.06618 25.2049 3.528 24.4083C2.98488 23.6043 2.51837 22.7449 2.14152 21.8539C1.35754 20.0003 0.960001 18.0308 0.960001 16C0.960001 13.9692 1.35754 11.9996 2.14152 10.146C2.51837 9.25507 2.98488 8.39563 3.528 7.59163C4.06618 6.79507 4.68428 6.04593 5.36511 5.3651C6.04594 4.68427 6.79508 4.06617 7.59164 3.528C8.39564 2.98488 9.25508 2.51837 10.1461 2.14152C11.9996 1.35753 13.9692 0.96 16 0.96Z" fill="#0068A0"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M27.7008 9.60322C27.7581 10.0278 27.7904 10.4834 27.7904 10.9742C27.7904 12.3266 27.537 13.8476 26.7762 15.7497L22.7039 27.5239C26.6679 25.2129 29.3337 20.9184 29.3337 15.9996C29.3337 13.6814 28.7413 11.5022 27.7008 9.60322ZM16.2346 17.1658L12.2335 28.7901C13.4284 29.1417 14.6917 29.3334 16.0004 29.3334C17.553 29.3334 19.0425 29.0654 20.4283 28.5774C20.3926 28.5204 20.3598 28.4598 20.3326 28.3937L16.2346 17.1658ZM25.0015 15.3271C25.0015 13.6788 24.4094 12.5379 23.9023 11.6501C23.2264 10.5513 22.5925 9.62166 22.5925 8.52289C22.5925 7.29745 23.5219 6.15659 24.8316 6.15659C24.8908 6.15659 24.9468 6.16369 25.0042 6.16734C22.632 3.9938 19.4715 2.66675 16.0004 2.66675C11.342 2.66675 7.24413 5.05691 4.85997 8.6762C5.17303 8.68614 5.46799 8.69238 5.71807 8.69238C7.11237 8.69238 9.27175 8.52289 9.27175 8.52289C9.99012 8.48079 10.075 9.53674 9.357 9.62166C9.357 9.62166 8.63445 9.70628 7.83113 9.74833L12.6863 24.1908L15.6046 15.4399L13.5274 9.74833C12.809 9.70628 12.129 9.62166 12.129 9.62166C11.4102 9.57922 11.4944 8.48079 12.2135 8.52289C12.2135 8.52289 14.415 8.69238 15.725 8.69238C17.1193 8.69238 19.279 8.52289 19.279 8.52289C19.9978 8.48079 20.0824 9.53674 19.364 9.62166C19.364 9.62166 18.6407 9.70628 17.8381 9.74833L22.6566 24.0807L24.0321 19.7223C24.6432 17.8175 25.0015 16.468 25.0015 15.3271ZM2.66699 15.9996C2.66699 21.2769 5.73372 25.838 10.1819 27.999L3.82154 10.5734C3.08171 12.2315 2.66699 14.0665 2.66699 15.9996Z" fill="#0068A0"/>
                            </svg>
                            </div>
                            <div class="sb-list-item">
                                <h4>WordPress Compatibility Updates</h4>
                                <p>With WordPress updates being released continually, we make sure the plugin is always compatible with the latest version so you can update WordPress without needing to worry.</p>
                            </div>
                        </div>
                        <div class="sb-why-renew-list">
                            <div class="sb-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.1583 8.40195C12.8183 7.39434 10.3809 5.88954 8.10558 5.18909C8.91398 7.03648 9.59628 9.00467 10.3023 10.9501C8.89406 11.9642 7.2491 12.7514 5.67754 13.6091C7.0149 14.8758 8.9089 15.609 10.418 16.7112C9.20919 18.1404 6.83433 19.6258 6.25565 20.9211C8.758 20.6207 11.6739 20.1336 14.0021 20.0348C14.5137 22.4989 14.7776 25.2005 15.5052 27.4577C16.5887 24.4706 17.5684 21.384 18.8581 18.5945C20.8485 19.3834 23.2742 20.3453 25.2172 20.8103C23.8539 18.9776 22.6098 17.0307 21.4018 15.0493C23.1895 13.8079 24.9976 12.5862 26.7202 11.2824C24.2854 11.0675 21.7627 10.9367 19.205 10.8394C18.7985 8.31133 18.9053 5.29159 18.28 2.97334C17.3339 4.87343 16.2174 6.61017 15.1583 8.40195ZM16.3145 29.3411C15.993 30.6598 17.0524 31.2007 16.8926 32C15.8465 31.6546 15.0596 31.4771 13.6553 31.6676C13.6992 30.6387 14.6649 30.4932 14.4646 29.2303C-0.500692 27.5999 -0.530751 1.68764 14.349 0.0928438C32.9539 -1.90125 33.5377 28.8829 16.3145 29.3411Z" fill="#FE544F"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.2802 2.97314C18.9055 5.2914 18.7987 8.31114 19.2052 10.8391C21.7629 10.9365 24.2856 11.0672 26.7204 11.2823C24.9978 12.586 23.1896 13.8077 21.4019 15.0491C22.61 17.0305 23.8541 18.9774 25.2174 20.8101C23.2744 20.3451 20.8487 19.3832 18.8583 18.5943C17.5686 21.3838 16.5889 24.4704 15.5054 27.4575C14.7778 25.2003 14.5139 22.4987 14.0023 20.0346C11.6741 20.1334 8.7582 20.6205 6.25584 20.9209C6.83452 19.6256 9.20937 18.1402 10.4181 16.7109C8.90907 15.6088 7.01509 14.8756 5.67773 13.6089C7.24929 12.7512 8.89422 11.964 10.3025 10.9499C9.59646 9.00448 8.91419 7.03628 8.10578 5.18889C10.381 5.88935 12.8185 7.39414 15.1585 8.40176C16.2176 6.60997 17.3341 4.87324 18.2802 2.97314Z" fill="white"/>
                            </svg>
                            </div>
                            <div class="sb-list-item">
                                <h4>All Pro YouTube Feeds Features</h4>
                                <p>Video Statistics, Live Streams, Search Feeds, PlayLists, Call to Action Settings, Carousel Layouts, Video filtering and more!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

		return $output;
	}

	/**
	 * SBY Check License
	 *
	 * @since 2.0
	 */
	public function check_license() {
		$sby_license = trim( get_option( 'sby_license_key' ) );

		// Check the API
		$sby_api_params = array(
			'edd_action'=> 'check_license',
			'license'   => $sby_license,
			'item_name' => urlencode( SBY_PLUGIN_NAME ) // the name of our product in EDD
		);
		$sby_response = wp_remote_get( add_query_arg( $sby_api_params, SBY_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );
		$sby_license_data = (array) json_decode( wp_remote_retrieve_body( $sby_response ) );
		// Update the updated license data
		update_option( 'sby_license_data', $sby_license_data );

		$sby_todays_date = date('Y-m-d');
		// Check whether it's active
		if( $sby_license_data['license'] !== 'expired' && ( strtotime( $sby_license_data['expires'] ) > strtotime($sby_todays_date) ) ) {
			// if the license is active then lets remove the ignore check for dashboard so next time it will show the expired notice in dashboard screen
			update_user_meta( get_current_user_id(), 'sby_ignore_dashboard_license_notice', false );
			wp_send_json_success( array(
				'msg' => 'License Active',
				'content' => $this->get_renewed_license_notice_content()
			) );

		} else {
			$content = $this->get_expired_license_notice_content();
			$content = str_replace( 'Your YouTube Feeds Pro license key has expired', 'We rechecked but your license key is still expired', $content );
			wp_send_json_success( array(
				'msg' => 'License Not Renewed',
				'content' => $content
			) );
		}
	}

	/**
	 * SBY Dismiss Notice
	 *
	 * @since 2.0
	 */
	public function dismiss_license_notice() {
		global $current_user;
		$user_id = $current_user->ID;
		update_user_meta( $user_id, 'sby_ignore_dashboard_license_notice', true );
	}
}