<?php

namespace SmashBalloon\YouTubeFeed\Helpers;

use SmashBalloon\YouTubeFeed\Pro\SBY_API_Connect_Pro;
use SmashBalloon\YouTubeFeed\SBY_API_Connect;

class Util {
	public static function isPro() {
		return defined( 'SBY_PRO' ) && SBY_PRO === true;
	}

	public static function isProduction() {
		return empty($_ENV['SBY_DEVELOPMENT']) || $_ENV['SBY_DEVELOPMENT'] !== 'true';
	}

	public static function ajaxPreflightChecks() {
		check_ajax_referer( 'sby-admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(); // This auto-dies.
		}
	}

	public static function sby_capability_check() {
		$cap = current_user_can( 'manage_youtube_feed_options' ) ? 'manage_youtube_feed_options' : 'manage_options';
		$cap = apply_filters( 'sby_settings_pages_capability', $cap );
		return $cap;
	}

	public static function isCurrentScreenAllowed() {
		$allowed_screens = array( 
			'dashboard', 
			'toplevel_page_sby-feed-builder',
			'youtube-feed_page_youtube-feed-setup',
			'youtube-feed_page_youtube-feed-settings',
			'youtube-feed_page_youtube-feed-single-videos',
			'youtube-feed_page_youtube-feed-support',
			'youtube-feed_page_youtube-feed-about',
		);
		$allowed_screens = apply_filters( 'sby_settings_pages_allowed_screens', $allowed_screens );
		$current_screen = get_current_screen();
		$current_screen = $current_screen->id;
		$is_allowed = in_array( $current_screen, $allowed_screens );
		return $is_allowed;
	}

	public static function get_license_key() {
		$license_key = get_option( 'sby_license_key' );
		$license_key = apply_filters( 'sby_license_key', $license_key );
		return $license_key;
	}

	public static function get_license_data() {
		if ( get_option( 'sby_license_data' ) ) {
			// Get license data from the db and convert the object to an array
			return (array) get_option( 'sby_license_data' );
		}

		$sby_license_data = self::sby_check_license( self::get_license_key() );

		return $sby_license_data;
	}

	public static function is_license_expired() {
		// Get license data
		$sby_license_data = (array) Util::get_license_data();
		//If expires param isn't set yet then set it to be a date to avoid PHP notice
		$sby_license_expires_date = isset( $sby_license_data['expires'] ) ? $sby_license_data['expires'] : '2036-12-31 23:59:59';
		if ( $sby_license_expires_date == 'lifetime' ) {
			$sby_license_expires_date = '2036-12-31 23:59:59';
		}
		$sby_todays_date = date('Y-m-d');
		$sby_interval = round( abs( strtotime( $sby_todays_date ) - strtotime( $sby_license_expires_date ) ) / 86400 );
		//Is license expired?
		if( $sby_interval == 0 || strtotime( $sby_license_expires_date ) < strtotime( $sby_todays_date ) ) {
			// If we haven't checked the API again one last time before displaying the expired notice then check it to make sure the license hasn't been renewed
			if ( get_option( 'sby_check_license_api_when_expires' ) !== 'false' ) {
				$sby_license_expired = self::sby_check_license( self::get_license_key(), true );
			} else {
				$sby_license_expired = true;
			}
		} else {
			$sby_license_expired = false;
			//License is not expired so change the check_api setting to be true so the next time it expires it checks again
			update_option( 'sby_check_license_api_when_expires', 'true' );
			update_option( 'sby_check_license_api_post_grace_period', 'true' );
		}

		$sby_license_expires_date_arr = str_split($sby_license_expires_date);
		// If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
		if( $sby_license_expires_date_arr[0] == '1' ) $sby_license_expired = false;

		// If there's no expired date then don't show the expired notification
		if( empty($sby_license_expires_date) || !isset($sby_license_expires_date) ) {
			$sby_license_expired = false;
		}

		// Is license missing - ie. on very first check
		if ( isset( $sby_license_data['error'] ) ) {
			if ( $sby_license_data['error'] == 'missing' ) {
				$sby_license_expired = false;
			}
		}

		return $sby_license_expired;
	}

	public static function is_license_grace_period_ended( $post_grace_period = false ) {
		// Get license data
		$sby_license_data = (array) Util::get_license_data();
		//If expires param isn't set yet then set it to be a date to avoid PHP notice
		$sby_license_expires_date = isset( $sby_license_data['expires'] ) ? $sby_license_data['expires'] : '2036-12-31 23:59:59';
		if ( $sby_license_expires_date == 'lifetime' ) {
			$sby_license_expires_date = '2036-12-31 23:59:59';
		}

		$sby_todays_date = date('Y-m-d');
		$sby_grace_period_date = strtotime( $sby_license_expires_date . '+14 days');
		$sby_grace_period_interval = round( abs( strtotime( $sby_todays_date ) - $sby_grace_period_date ) / 86400 );

		if ( $post_grace_period && strtotime( $sby_todays_date ) > $sby_grace_period_date ) {
			return true;
		}

		if ( $sby_grace_period_interval == 0 || $sby_grace_period_date < strtotime( $sby_todays_date ) ) {
			return true;
		}

		return;
	}

	/**
	 * Remote check for license status
	 * 
	 * @since 2.0.2
	 */
	public static function sby_check_license( $sby_license, $check_license_status = false, $license_api_second_check = false ) {
		//Set a flag so it doesn't check the API again until the next time it expires
		if ( $license_api_second_check ) {
			update_option( 'sby_check_license_api_post_grace_period', 'false' );
		} else {
			update_option( 'sby_check_license_api_when_expires', 'false' );
		}

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
		$sby_license_data_arr = (array) $sby_license_data;
		//Store license data in db
		update_option( 'sby_license_data', $sby_license_data );
		update_option( 'sby_license_status', $sby_license_data->license );
		$sby_todays_date = date('Y-m-d');
		if ( $check_license_status ) {
			//Check whether it's active
			if( $sby_license_data_arr['license'] !== 'expired' && ( strtotime( $sby_license_data_arr['expires'] ) > strtotime( $sby_todays_date ) ) ){
				$sby_license_status = false;
			} else {
				$sby_license_status = true;
			}

			return $sby_license_status;
		}

		return $sby_license_data;
	}

	
	/**
	 * Update License Data
	 *
	 */
	public static function update_recheck_license_data( $license_data ) {
		$license_changed = false;
		// compare the old stored license status with new license status
		if ( get_option( 'sby_license_status' ) !== $license_data->license ) {
			$license_changed = true;
			// make license check_api true so next time it expires it checks again
			update_option( 'sby_check_license_api_when_expires', 'true' );
			update_option( 'sby_check_license_api_post_grace_period', 'true' );
		}
		update_option( 'sby_license_status', $license_data->license );

		return $license_changed;
	}

	/**
	 * Check if licese expired/inactive notices needs to show
	 * 
	 * @since 2.0.2
	 */
	public static function expiredLicenseWithGracePeriodEnded() {
		return !empty( self::get_license_key() ) && 
				self::is_license_expired() && 
				self::is_license_grace_period_ended( true );
	}

	/**
	 * Make API request to get channel ID from YouTube handle
	 */
	public static function get_channel_id_by_api_request( $url ) {
		$response = new \stdClass();
		$path = parse_url($url, PHP_URL_PATH);
		$basename = basename($path); // This will give you the username part

		if( !empty($basename) ) {
				$username = strpos($basename, '@') === 0 ? substr($basename, 1) : $basename;
			
				$params = array(
					'channel_handle' => $username
				);

				$connected_account = sby_get_first_connected_account();
				$sby_api_connect = new SBY_API_Connect($connected_account, 'channels', $params );
				$sby_api_connect->connect();

				$data = $sby_api_connect->get_data();


				if ( ! $sby_api_connect->is_youtube_error() ) {
					$channelId = !empty($data['items'][0]['id']) ? $data['items'][0]['id'] : '';
					$response->channel_id = $channelId;
			}
		}

		if( empty($response->channel_id)) {
			$api_register_url = SBY_API_URL . 'auth/register?url=' . get_home_url();
			$api_url = SBY_API_URL . 'youtube/handle?channel_url=' . $url;

			// Get Authorization Token
			$request = wp_remote_post( $api_register_url );
			if ( is_wp_error( $request ) ) {
				return;
			}
			$response = json_decode( wp_remote_retrieve_body( $request ) );
			if ( $response->success && empty( $response->token ) || ! $response->success && empty( $response->data->token ) ) {
				error_log('returning due to empty token');
				return;
			}
			if ( $response->success ) {
				$api_token = $response->token;
			} else {
				$api_token = $response->data->token;
			}

			// Get Channel ID
			$request = wp_remote_get( $api_url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_token,
				),
			));

			if ( is_wp_error( $request ) ) {
				return;
			}

			$response = json_decode( wp_remote_retrieve_body( $request ) );

		}

		self::cache_saved_channel_id( $url, $response );

		return $response->channel_id;
	}

	/**
	 * Cache saved channel ID to database
	 * 
	 * @return void
	 */
	public static function cache_saved_channel_id( $url, $response ) {
		$channel_ids = get_option( 'sby_saved_channel_ids' );
		$channel_ids = json_decode( $channel_ids, true );

		if ( empty( $channel_ids ) ) {
			$channel_ids = array();
		}

		// find the channel @handle from the channel string
		$regex_pattern = '/@(\w+)/';
		if ( preg_match($regex_pattern, $url, $matches ) ) {
			$channel_handle = $matches[1];
		}
		if ( ! $channel_handle ) {
			return;
		}

		$channel_ids[ '@' . strtolower($channel_handle) ] = $response->channel_id;
		update_option( 'sby_saved_channel_ids', json_encode( $channel_ids ) );
	}

	/**
	 * Get channel ID from saved channel IDs cached in the database
	 * 
	 * @return null|string
	 */
	public static function get_saved_channel_id( $channel ) {
		$channel_ids = get_option( 'sby_saved_channel_ids' );
		$channel_ids = json_decode( $channel_ids, true );
		if ( empty( $channel_ids ) ) {
			return;
		}

		$channel_id = '';

		// find the channel @handle from the channel string
		$regex_pattern = '/@(\w+)/';
		if ( preg_match($regex_pattern, $channel, $matches ) ) {
			$channel_handle = $matches[1];
		}
		if ( ! $channel_handle ) {
			return;
		}

		if ( isset( $channel_ids[ '@' . strtolower($channel_handle) ] ) ) {
			$channel_id = $channel_ids[ '@' . strtolower($channel_handle) ];
		}

		return $channel_id;
	}

	/**
	 * @return string
	 *
	 * @since 2.1.1
	 */
	public static function sby_get_resized_uploads_url() {
		$upload = wp_upload_dir();

		$base_url = $upload['baseurl'];
		$home_url = home_url();

		if ( strpos( $home_url, 'https:' ) !== false ) {
			str_replace( 'http:', 'https:', $base_url );
		}

		$resize_url = apply_filters( 'sby_resize_url', trailingslashit( $base_url ) . trailingslashit( SBY_UPLOADS_NAME ) );

		return $resize_url;
	}

}
