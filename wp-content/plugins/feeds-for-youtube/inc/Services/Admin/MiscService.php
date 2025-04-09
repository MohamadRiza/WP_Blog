<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\Pro\SBY_API_Connect_Pro;
use SmashBalloon\YouTubeFeed\Services\AdminAjaxService;

class MiscService extends ServiceProvider {
	public function register() {
		add_action( 'wp_ajax_sby_ca_after_remove_clicked', [$this, 'sby_delete_connected_account'] );
		add_action( 'wp_ajax_sby_process_access_token', [$this, 'sby_process_access_token'] );
		add_action( 'wp_ajax_sby_delete_wp_posts', [$this, 'sby_delete_wp_posts'] );
		add_action( 'wp_ajax_sbspf_account_search', [$this, 'sbspf_account_search'] );
		add_action('admin_init', [$this, 'sby_register_option']);
		add_action( 'wp_ajax_sby_do_import_batch', [$this, 'sby_do_import_batch'] );
		add_action( 'sby_settings_after_configure_save', [$this, 'sby_reset_cron'], 10, 1 );

	}

	public function sby_delete_connected_account() {
		if ( ! isset( $_POST['sbspf_nonce'] ) || ! isset( $_POST['account_id']) ) return;
		$nonce = $_POST['sbspf_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'sbspf_nonce' ) ) {
			die ( 'You did not do this the right way!' );
		}

		global $sby_settings;

		$account_id = sanitize_text_field( $_POST['account_id'] );
		$to_save = array();

		foreach ( $sby_settings['connected_accounts'] as $connected_account ) {
			if ( (string)$connected_account['channel_id'] !== (string)$account_id ) {
				$to_save[ $connected_account['channel_id'] ] = $connected_account;
			}
		}

		$sby_settings['connected_accounts'] = $to_save;
		update_option( 'sby_settings', $sby_settings );

		echo wp_json_encode( array( 'success' => true ) );

		die();
	}
	public function sby_process_access_token() {
		if ( ! isset( $_POST['sbspf_nonce'] ) || ! isset( $_POST['sby_access_token'] ) ) return;
		$nonce = $_POST['sbspf_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'sbspf_nonce' ) ) {
			die ( 'You did not do this the right way!' );
		}

		$account = sby_attempt_connection();

		if ( $account ) {
			global $sby_settings;

			$options = $sby_settings;
			$username = $account['username'] ? $account['username'] : $account['channel_id'];
			if ( isset( $account['local_avatar'] ) && $account['local_avatar'] && isset( $options['favorlocal'] ) && $options['favorlocal' ] === 'on' ) {
				$upload = wp_upload_dir();
				$resized_url = trailingslashit( $upload['baseurl'] ) . trailingslashit( SBY_UPLOADS_NAME );
				$profile_picture = '<img class="sbspf_ca_avatar" src="'.$resized_url . $account['username'].'.jpg" />'; //Could add placeholder avatar image
			} else {
				$profile_picture = $account['profile_picture'] ? '<img class="sbspf_ca_avatar" src="'.$account['profile_picture'].'" />' : ''; //Could add placeholder avatar image
			}

			$text_domain = SBY_TEXT_DOMAIN;
			$slug = SBY_SLUG;
			ob_start();
			include_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/templates/single-connected-account.php';
			if ( sby_notice_not_dismissed() ) {
				include_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/templates/modal.php';
				echo '<span class="sby_account_just_added"></span>';
			}

			$html = ob_get_contents();
			ob_get_clean();

			$return = array(
				'account_id' => $account['channel_id'],
				'html' => $html
			);
		} else {
			$return = array(
				'error' => __( 'Could not connect your account. Please check to make sure this is a valid access token for the Smash Balloon YouTube App.'),
				'html' => ''
			);
		}

		echo wp_json_encode( $return );

		die();
	}
	public function sby_delete_wp_posts() {
		if ( ! isset( $_POST['sbspf_nonce'] ) ) return;
		$nonce = $_POST['sbspf_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'sbspf_nonce' ) ) {
			die ( 'You did not do this the right way!' );
		}

		sby_clear_wp_posts();

		sby_clear_cache();

		echo '{}';

		die();
	}
	public function sbspf_account_search() {
		if ( ! isset( $_POST['sbspf_nonce'] ) || ! isset( $_POST['term']) ) return;
		$nonce = $_POST['sbspf_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'sbspf_nonce' ) ) {
			die ( 'You did not do this the right way!' );
		}

		global $sby_settings;

		$term = sanitize_text_field( $_POST['term'] );
		$params = array(
			'q' => $term,
			'type' => 'channel'
		);

		$connected_account_for_term = array();
		foreach ( $sby_settings['connected_accounts'] as $connected_account ) {
			$connected_account_for_term = $connected_account;
		}
		if ( $connected_account_for_term['expires'] < time() + 5 ) {
			$new_token_data = SBY_API_Connect::refresh_token( '', $connected_account_for_term['refresh_token'], '' );

			if ( isset( $new_token_data['access_token'] ) ) {
				$connected_account_for_term['access_token'] = $new_token_data['access_token'];
				$connected_accounts_for_feed[ $term ]['access_token'] = $new_token_data['access_token'];
				$connected_account_for_term['expires'] = $new_token_data['expires_in'] + time();
				$connected_accounts_for_feed[ $term ]['expires'] = $new_token_data['expires_in'] + time();

				sby_update_or_connect_account( $connected_account_for_term );

			}
		}

		$search = new SBY_API_Connect( $connected_account_for_term, 'search', $params );

		$search->connect();


		echo wp_json_encode( $search->get_data() );

		die();
	}
	public function sby_register_option() {
		// creates our settings in the options table
		register_setting('sby_license', 'sby_license_key', 'sby_sanitize_license' );
	}

	public function sby_do_import_batch() {

		Util::ajaxPreflightChecks();

		$next_page = isset( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : '';
		$channel = isset( $_POST['channel'] ) ? sanitize_text_field( $_POST['channel'] ) : '';
		$needed = isset( $_POST['needed'] ) ? sanitize_text_field( $_POST['needed'] ) : 1;
		$playlist = isset( $_POST['playlist'] ) ? sanitize_text_field( $_POST['playlist'] ) : '';

		if ( empty( $channel ) && empty( $playlist ) ) {
			die();
		}

		$params = array(
			'num' => (int) $needed
		);

		if ( ! empty( $next_page ) ) {
			$params['nextPageToken'] = $next_page;
		}

		if ( strpos( $channel, 'UC' ) === 0 ) {
			$params['channel_id'] = $channel;
		} else {
			$params['channel_name'] = $channel;
		}

		if ( empty( $playlist ) ) {
			$connection = new SBY_API_Connect_Pro( sby_get_first_connected_account(), 'channels', $params );
			$connection->connect();
			$channel_data = $connection->get_data();

			$playlist = isset( $channel_data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ) ? $channel_data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] : false;
		}

		$return = array();
		if ( $playlist ) {
			$return['playlist'] = $playlist;
			$params['playlist_id'] = $playlist;
			$playlist_connection = new SBY_API_Connect_Pro( sby_get_first_connected_account(), 'playlistItems', $params );
			$playlist_connection->connect();
			$data = $playlist_connection->get_data();

			$posts = isset( $data['items'] ) ? $data['items'] : array();

			AdminAjaxService::sby_process_post_set_caching( $posts, 'sby_importer' );

			$return['num_retrieved'] = count( $posts );

			$return['next'] = $playlist_connection->get_next_page();

			echo wp_json_encode( $return );
		}

		die();
	}
	public function sby_reset_cron( $settings ) {
		$sby_caching_type = isset( $settings['caching_type'] ) ? $settings['caching_type'] : '';
		$sby_cache_cron_interval = isset( $settings['cache_cron_interval'] ) ? $settings['cache_cron_interval'] : '';
		$sby_cache_cron_time = isset( $settings['cache_cron_time'] ) ? $settings['cache_cron_time'] : '';
		$sby_cache_cron_am_pm = isset( $settings['cache_cron_am_pm'] ) ? $settings['cache_cron_am_pm'] : '';

		if ( $sby_caching_type === 'background' ) {
			delete_option( 'sby_cron_report' );
			SBY_Cron_Updater::start_cron_job( $sby_cache_cron_interval, $sby_cache_cron_time, $sby_cache_cron_am_pm );
		}
	}

}