<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Pro\SBY_CPT;
use SmashBalloon\YouTubeFeed\SBY_Settings;
use SmashBalloon\YouTubeFeed\Pro\SBY_Settings_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_Display_Elements_Pro;
use SmashBalloon\YouTubeFeed\SBY_Feed;
use SmashBalloon\YouTubeFeed\Pro\SBY_Feed_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_Parse_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_YT_Details_Query;
use SmashBalloon\YouTubeFeed\Feed_Locator;
use SmashBalloon\YouTubeFeed\SBY_Parse;
use SmashBalloon\YouTubeFeed\SBY_WP_Post;
use SmashBalloon\YouTubeFeed\Helpers\Util;

class AdminAjaxService extends ServiceProvider {

	public function register() {
		add_action( 'wp_ajax_sby_load_more_clicked', [$this, 'sby_get_next_post_set'] );
		add_action( 'wp_ajax_nopriv_sby_load_more_clicked', [$this, 'sby_get_next_post_set'] );
		add_action( 'wp_ajax_sby_live_retrieve', [$this, 'sby_get_live_retrieve'] );
		add_action( 'wp_ajax_nopriv_sby_live_retrieve', [$this, 'sby_get_live_retrieve'] );
		add_action( 'wp_ajax_sby_check_wp_submit', [$this, 'sby_process_wp_posts'] );
		add_action( 'wp_ajax_nopriv_sby_check_wp_submit', [$this, 'sby_process_wp_posts'] );
		add_action( 'wp_ajax_sby_do_locator', [$this, 'sby_do_locator'] );
		add_action( 'wp_ajax_nopriv_sby_do_locator', [$this, 'sby_do_locator'] );
		add_action( 'wp_ajax_sby_add_api_key', [$this, 'sby_api_key'] );
		add_action( 'wp_ajax_sby_other_plugins_modal', [$this, 'sby_other_plugins_modal'] );
		add_action( 'wp_ajax_sby_single_videos_upsell_modal', [$this, 'sby_single_videos_upsell_modal'] );
		add_action( 'wp_ajax_sby_install_other_plugins', [$this, 'sby_install_addon'] );
		add_action( 'wp_ajax_sby_activate_other_plugins', [$this, 'sby_activate_addon'] );
		add_action( 'wp_ajax_sby_manual_access_token', [$this, 'manual_access_token'] );
	}

	/**
	 * Called after the load more button is clicked using admin-ajax.php
	 */
	public function sby_get_next_post_set() {
		if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sby' ) === false ) {
			die( 'invalid feed ID');
		}

		$feed_id = sanitize_text_field( $_POST['feed_id'] );
		$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
		if ( is_array( $atts_raw ) ) {
			array_map( 'sanitize_text_field', $atts_raw );
		} else {
			$atts_raw = array();
		}
		$atts = $atts_raw; // now sanitized

		$offset = isset( $_POST['offset'] ) ? (int)$_POST['offset'] : 0;

		$database_settings = sby_get_database_settings();
		$youtube_feed_settings = sby_is_pro() ? new SBY_Settings_Pro( $atts, $database_settings ) : new SBY_Settings( $atts, $database_settings );

		if ( empty( $database_settings['connected_accounts'] ) && empty( $database_settings['api_key'] ) ) {
			die( 'error no connected account' );
		}

		$youtube_feed_settings->set_feed_type_and_terms();
		$youtube_feed_settings->set_transient_name();
		$transient_name = $youtube_feed_settings->get_transient_name();
		$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
		$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
		$feed_details = array(
			'feed_id' => $feed_id,
			'atts' => $atts,
			'location' => array(
				'post_id' => $post_id,
				'html' => $location
			)
		);

		$this->sby_do_background_tasks( $feed_details );

		$settings = $youtube_feed_settings->get_settings();

		$feed_type_and_terms = $youtube_feed_settings->get_feed_type_and_terms();

		$transient_name = $feed_id;

		$youtube_feed = sby_is_pro() ?  new SBY_Feed_Pro( $transient_name ) : new SBY_Feed( $transient_name );

		if ( $settings['caching_type'] === 'permanent' && empty( $settings['doingModerationMode'] ) ) {
			$youtube_feed->add_report( 'trying to use permanent cache' );
			$youtube_feed->maybe_set_post_data_from_backup();
		} elseif ( $settings['caching_type'] === 'background' ) {
			$youtube_feed->add_report( 'background caching used' );
			if ( $youtube_feed->regular_cache_exists() ) {
				$youtube_feed->add_report( 'setting posts from cache' );
				$youtube_feed->set_post_data_from_cache();
			}

			if ( $youtube_feed->need_posts( $settings['num'], $offset ) && $youtube_feed->can_get_more_posts() ) {
				while ( $youtube_feed->need_posts( $settings['num'], $offset ) && $youtube_feed->can_get_more_posts() ) {
					$youtube_feed->add_remote_posts( $settings, $feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
				}

				if ( $youtube_feed->need_to_start_cron_job() ) {
					$youtube_feed->add_report( 'needed to start cron job' );
					$to_cache = array(
						'atts' => $atts,
						'last_requested' => time(),
					);

					$youtube_feed->set_cron_cache( $to_cache, $youtube_feed_settings->get_cache_time_in_seconds() );

				} else {
					$youtube_feed->add_report( 'updating last requested and adding to cache' );
					$to_cache = array(
						'last_requested' => time(),
					);

					$youtube_feed->set_cron_cache( $to_cache, $youtube_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
				}
			}

		} elseif ( $youtube_feed->regular_cache_exists() ) {
			$youtube_feed->add_report( 'regular cache exists' );
			$youtube_feed->set_post_data_from_cache();

			if ( $youtube_feed->need_posts( $settings['num'], $offset ) && $youtube_feed->can_get_more_posts() ) {
				while ( $youtube_feed->need_posts( $settings['num'], $offset ) && $youtube_feed->can_get_more_posts() ) {
					$youtube_feed->add_remote_posts( $settings, $feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
				}

				$youtube_feed->add_report( 'adding to cache' );
				$youtube_feed->cache_feed_data( $youtube_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
			}


		} else {
			$youtube_feed->add_report( 'no feed cache found' );

			while ( $youtube_feed->need_posts( $settings['num'], $offset ) && $youtube_feed->can_get_more_posts() ) {
				$youtube_feed->add_remote_posts( $settings, $feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
			}

			if ( $youtube_feed->should_use_backup() ) {
				$youtube_feed->add_report( 'trying to use a backup cache' );
				$youtube_feed->maybe_set_post_data_from_backup();
			} else {
				$youtube_feed->add_report( 'transient gone, adding to cache' );
				$youtube_feed->cache_feed_data( $youtube_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
			}
		}

		$settings['feed_avatars'] = array();
		if ( $youtube_feed->need_avatars( $settings ) ) {
			$youtube_feed->set_up_feed_avatars( $youtube_feed_settings->get_connected_accounts_in_feed(), $feed_type_and_terms );
			$settings['feed_avatars'] = $youtube_feed->get_channel_id_avatars();
		}

		$feed_status = array( 'shouldPaginate' => $youtube_feed->should_use_pagination( $settings, $offset ) );

		$feed_status['cacheAll'] = $youtube_feed->do_page_cache_all();

		$return_html = $youtube_feed->get_the_items_html( $settings, $offset, $youtube_feed_settings->get_feed_type_and_terms(), $youtube_feed_settings->get_connected_accounts_in_feed() );

		$post_data = $youtube_feed->get_post_data();
		if ( ($youtube_feed->are_posts_with_no_details() || $youtube_feed->successful_video_api_request_made())
		     && ! empty( $post_data ) ) {
			if ( $settings['storage_process'] === 'page' ) {
				foreach ( $post_data as $post ) {
					$wp_post            = new SBY_WP_Post( $post, $transient_name );
					$sby_video_settings = SBY_CPT::get_sby_cpt_settings();
					$wp_post->update_post( $sby_video_settings['post_status'] );
				}
			} elseif ( $settings['storage_process'] === 'background' ) {
				$feed_status['checkWPPosts'] = true;
				$feed_status['cacheAll']     = true;
			}
		}

		/*if ( $settings['disable_js_image_loading'] || $settings['imageres'] !== 'auto' ) {
			global $sby_posts_manager;
			$post_data = array_slice( $youtube_feed->get_post_data(), $offset, $settings['minnum'] );

			if ( ! $sby_posts_manager->image_resizing_disabled() ) {
				$image_ids = array();
				foreach ( $post_data as $post ) {
					$image_ids[] = SBY_Parse::get_post_id( $post );
				}
				$resized_images = SBY_Feed::get_resized_images_source_set( $image_ids, 0, $feed_id );

				$youtube_feed->set_resized_images( $resized_images );
			}
		}*/

		$return = array(
			'html' => $return_html,
			'feedStatus' => $feed_status,
			'report' => $youtube_feed->get_report(),
			'resizedImages' => array()
			//'resizedImages' => SBY_Feed::get_resized_images_source_set( $youtube_feed->get_image_ids_post_set(), 0, $feed_id )
		);

		//SBY_Feed::update_last_requested( $youtube_feed->get_image_ids_post_set() );

		echo wp_json_encode( $return );

		global $sby_posts_manager;

		$sby_posts_manager->update_successful_ajax_test();

		die();
	}

	public function sby_get_live_retrieve() {
		if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sby' ) === false ) {
			die( 'invalid feed ID');
		}

		$feed_id = sanitize_text_field( $_POST['feed_id'] );
		$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
		$video_id = sanitize_text_field( $_POST['video_id'] );
		if ( is_array( $atts_raw ) ) {
			array_map( 'sanitize_text_field', $atts_raw );
		} else {
			$atts_raw = array();
		}
		$atts = $atts_raw; // now sanitized

		if ( isset( $atts['live'] ) ) {
			unset( $atts['live'] );
		}
		$atts['type'] = 'single';
		$atts['single'] = $video_id;
		$offset = 0;

		$database_settings = sby_get_database_settings();
		$youtube_feed_settings = sby_is_pro() ? new SBY_Settings_Pro( $atts, $database_settings ) : new SBY_Settings( $atts, $database_settings );

		if ( empty( $database_settings['connected_accounts'] ) && empty( $database_settings['api_key'] ) ) {
			die( 'error no connected account' );
		}

		$youtube_feed_settings->set_feed_type_and_terms();
		$youtube_feed_settings->set_transient_name( $feed_id );
		$transient_name = $youtube_feed_settings->get_transient_name();

		if ( $transient_name !== $feed_id ) {
			die( 'id does not match' );
		}

		$settings = $youtube_feed_settings->get_settings();

		$feed_type_and_terms = $youtube_feed_settings->get_feed_type_and_terms();

		$youtube_feed = sby_is_pro() ?  new SBY_Feed_Pro( $transient_name ) : new SBY_Feed( $transient_name );
		$youtube_feed->add_remote_posts( $settings, $feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
		if ( $database_settings['caching_type'] === 'background' ) {
			$to_cache = array(
				'atts' => $atts,
				'last_requested' => time(),
			);
			$youtube_feed->set_cron_cache( $to_cache, $youtube_feed_settings->get_cache_time_in_seconds() );
		} else {
			$youtube_feed->cache_feed_data( $youtube_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}

		$feed_status = array( 'shouldPaginate' => $youtube_feed->should_use_pagination( $settings, $offset ) );

		$feed_status['cacheAll'] = $youtube_feed->do_page_cache_all();

		$return_html = $youtube_feed->get_the_items_html( $settings, $offset, $youtube_feed_settings->get_feed_type_and_terms(), $youtube_feed_settings->get_connected_accounts_in_feed() );
		$post_data = $youtube_feed->get_post_data();
		if ( ($youtube_feed->are_posts_with_no_details() || $youtube_feed->successful_video_api_request_made())
		     && ! empty( $post_data ) ) {
			if ( $settings['storage_process'] === 'page' ) {
				foreach ( $youtube_feed->get_post_data() as $post ) {
					$wp_post            = new SBY_WP_Post( $post, $transient_name );
					$sby_video_settings = SBY_CPT::get_sby_cpt_settings();
					$wp_post->update_post( $sby_video_settings['post_status'] );
				}
			} elseif ( $settings['storage_process'] === 'background' ) {
				$feed_status['checkWPPosts'] = true;
				$feed_status['cacheAll']     = true;
			}
		}

		$return = array(
			'html' => $return_html,
			'feedStatus' => $feed_status,
			'report' => $youtube_feed->get_report(),
			'resizedImages' => array()
			//'resizedImages' => SBY_Feed::get_resized_images_source_set( $youtube_feed->get_image_ids_post_set(), 0, $feed_id )
		);

		//SBY_Feed::update_last_requested( $youtube_feed->get_image_ids_post_set() );

		echo wp_json_encode( $return );

		global $sby_posts_manager;

		$sby_posts_manager->update_successful_ajax_test();

		die();
	}

	/**
	 * Posts that need resized images are processed after being sent to the server
	 * using AJAX
	 *
	 * @return string
	 */
	public function sby_process_wp_posts() {
		if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sby' ) === false ) {
			die( 'invalid feed ID');
		}

		$feed_id = sanitize_text_field( $_POST['feed_id'] );

		$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
		if ( is_array( $atts_raw ) ) {
			array_map( 'sanitize_text_field', $atts_raw );
		} else {
			$atts_raw = array();
		}
		$atts = $atts_raw; // now sanitized
		$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
		$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
		$feed_details = array(
			'feed_id' => $feed_id,
			'atts' => $atts,
			'location' => array(
				'post_id' => $post_id,
				'html' => $location
			)
		);

		$this->sby_do_background_tasks( $feed_details );

		$offset = isset( $_POST['offset'] ) ? (int)$_POST['offset'] : 0;
		$vid_ids = isset( $_POST['posts'] ) && is_array( $_POST['posts'] ) ? $_POST['posts'] : array();

		if ( ! empty( $vid_ids ) ) {
			array_map( 'sanitize_text_field', $vid_ids );
		}

		$cache_all = isset( $_POST['cache_all'] ) ? $_POST['cache_all'] === 'true' : false;

		$info = $this->sby_add_or_update_wp_posts( $vid_ids, $feed_id, $atts, $offset, $cache_all );

		echo wp_json_encode( $info );

		//global $sby_posts_manager;

		//$sby_posts_manager->update_successful_ajax_test();

		die();
	}

	private function sby_add_or_update_wp_posts( $vid_ids, $feed_id, $atts, $offset, $cache_all ) {
		if ( $cache_all ) {
			$database_settings = sby_get_database_settings();
			$youtube_feed_settings = sby_is_pro() ? new SBY_Settings_Pro( $atts, $database_settings ) : new SBY_Settings( $atts, $database_settings );;
			$youtube_feed_settings->set_feed_type_and_terms();
			$youtube_feed_settings->set_transient_name( $feed_id );
			$transient_name = $youtube_feed_settings->get_transient_name();

			$feed_id = $transient_name;
		}

		$database_settings = sby_get_database_settings();
		$sby_settings = sby_is_pro() ? new SBY_Settings_Pro( $atts, $database_settings ) : new SBY_Settings( $atts, $database_settings );;

		$settings = $sby_settings->get_settings();

		$youtube_feed = sby_is_pro() ? new SBY_Feed_Pro( $feed_id ) : new SBY_Feed( $feed_id );
		if ( $youtube_feed->regular_cache_exists() || $feed_id === 'sby_single' ) {
			$youtube_feed->set_post_data_from_cache();

			if ( !$cache_all || $feed_id === 'sby_single'  ) {
				if ( empty( $vid_ids ) || $feed_id !== 'sby_single' ) {
					$posts = array_slice( $youtube_feed->get_post_data(), max( 0, $offset - $settings['num'] ), $settings['num'] );
				} else {
					$posts = $vid_ids;
				}
			} else {
				$posts = $youtube_feed->get_post_data();
			}

			return self::sby_process_post_set_caching( $posts, $feed_id );
		}

		return array();
	}

	public static function sby_process_post_set_caching( $posts, $feed_id ) {

		// if is an array of video ids already, don't need to get them
		if ( isset( $posts[0] ) && SBY_Parse::get_video_id( $posts[0] ) === '' ) {
			$vid_ids = $posts;
		} else {
			$vid_ids = array();
			foreach ( $posts as $post ) {
				$vid_ids[] = SBY_Parse::get_video_id( $post );
				$wp_post = new SBY_WP_Post( $post, $feed_id );
				if ( sby_is_pro() ) {
					$sby_video_settings = SBY_CPT::get_sby_cpt_settings();
					$wp_post->update_post( $sby_video_settings['post_status'] );
				}
			}
		}

		if ( ! sby_is_pro() ) {
			return array();
		}
		
		if ( ! empty( $vid_ids ) ) {
			$details_query = new SBY_YT_Details_Query( array( 'video_ids' => $vid_ids ) );
			$videos_details = $details_query->get_video_details_to_update();

			$updated_details = array();
			foreach ( $videos_details as $video ) {
				$vid_id = SBY_Parse::get_video_id( $video );
				$live_broadcast_type = SBY_Parse_Pro::get_live_broadcast_content( $video );
				$live_streaming_timestamp = SBY_Parse_Pro::get_live_streaming_timestamp( $video );
				$single_updated_details = array(
					"sby_view_count" => SBY_Display_Elements_Pro::escaped_formatted_count_string( SBY_Parse_Pro::get_view_count( $video ), 'views' ),
					"sby_like_count" => SBY_Display_Elements_Pro::escaped_formatted_count_string( SBY_Parse_Pro::get_like_count( $video ), 'likes' ),
					"sby_comment_count" => SBY_Display_Elements_Pro::escaped_formatted_count_string( SBY_Parse_Pro::get_comment_count( $video ), 'comments' ),
					'sby_live_broadcast' => array(
						'broadcast_type' => $live_broadcast_type,
						'live_streaming_string' => SBY_Display_Elements_Pro::escaped_live_streaming_time_string( $video ),
						'live_streaming_date' => SBY_Display_Elements_Pro::format_date( $live_streaming_timestamp, false, true ),
						'live_streaming_timestamp' => $live_streaming_timestamp
					),
					'raw' => array(
						'views' => SBY_Parse_Pro::get_view_count( $video ),
						'likes' => SBY_Parse_Pro::get_like_count( $video ),
						'comments' => SBY_Parse_Pro::get_comment_count( $video )
					)
				);

				$description = SBY_Parse_Pro::get_caption( $video );
				if ( ! empty( $description ) ) {
					$single_updated_details['sby_description'] = sby_esc_html_with_br( $description );
				}
				$post = new SBY_WP_Post( $video, '' );

				$post->update_video_details();

				$updated_details[ $vid_id ] = apply_filters( 'sby_video_details_return', $single_updated_details, $video, $post->get_wp_post_id() );
			}

			return $updated_details;
		}

		return array();
	}

	public function sby_do_locator() {
		if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sbi' ) === false ) {
			die( 'invalid feed ID');
		}

		$feed_id = sanitize_text_field( $_POST['feed_id'] );

		$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
		if ( is_array( $atts_raw ) ) {
			array_map( 'sanitize_text_field', $atts_raw );
		} else {
			$atts_raw = array();
		}
		$atts = $atts_raw; // now sanitized

		$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
		$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
		$feed_details = array(
			'feed_id' => $feed_id,
			'atts' => $atts,
			'location' => array(
				'post_id' => $post_id,
				'html' => $location
			)
		);

		$this->sby_do_background_tasks( $feed_details );

		wp_die( 'locating success' );
	}

	/**
	 * AJAX Add API Key
	 *
	 * @since 2.0
	 */
	public function sby_api_key() {
		check_ajax_referer( 'sby-admin' , 'nonce');

		if ( ! sby_current_user_can( 'manage_youtube_feed_options' ) ) {
			wp_send_json_error();
		}
		// get the settings
		$database_settings = sby_get_database_settings();
		// validate the api key
		$api_key = sanitize_text_field( $_POST['api'] );
		$database_settings['api_key'] = $api_key;
		// update the settings
		update_option( 'sby_settings', $database_settings );

		wp_send_json_success();
	}

	/**
	 * AJAX Add Manual Access Token
	 *
	 * @since 2.0
	 */
	public function manual_access_token() {
		check_ajax_referer( 'sby-admin' , 'nonce');

		if ( ! sby_current_user_can( 'manage_youtube_feed_options' ) ) {
			wp_send_json_error();
		}

		if ( isset( $_POST['sby_access_token'] ) ) {
			$return = sby_attempt_connection();
			wp_send_json_success($return);
		}
	}


	/**
	 * Get other plugin modal
	 *
	 * @since 2.0
	 */
	public function sby_other_plugins_modal() {
		check_ajax_referer( 'sby-admin' , 'nonce');

		if ( ! sby_current_user_can( 'manage_youtube_feed_options' ) ) {
			wp_send_json_error();
		}

		$plugin = isset( $_POST['plugin'] ) ? sanitize_key( $_POST['plugin'] ) : '';
		$sb_other_plugins = sby_get_installed_plugin_info();
		$plugin = isset( $sb_other_plugins[ $plugin ] ) ? $sb_other_plugins[ $plugin ] : false;
		if ( ! $plugin ) {
			wp_send_json_error();
		}

		// Build the content for modals
		$output = '<div class="sby-fb-popup-inside sby-install-plugin-modal">
		<div class="sby-ip-popup-cls"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"></path>
		</svg></div>
		<div class="sby-install-plugin-body sby-fb-fs">
		<div class="sby-install-plugin-header">
		<div class="sb-plugin-image">'. $plugin['svgIcon'] .'</div>
		<div class="sb-plugin-name">
		<h3>'. $plugin['name'] .'<span>Free</span></h3>
		<p><span class="sb-author-logo">
		<svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path fill-rule="evenodd" clip-rule="evenodd" d="M5.72226 4.70098C4.60111 4.19717 3.43332 3.44477 2.34321 3.09454C2.73052 4.01824 3.05742 5.00234 3.3957 5.97507C2.72098 6.48209 1.93286 6.8757 1.17991 7.30453C1.82065 7.93788 2.72809 8.3045 3.45109 8.85558C2.87196 9.57021 1.73414 10.3129 1.45689 10.9606C2.65579 10.8103 4.05285 10.5668 5.16832 10.5174C5.41343 11.7495 5.53984 13.1002 5.88845 14.2288C6.40758 12.7353 6.87695 11.192 7.49488 9.79727C8.44849 10.1917 9.61069 10.6726 10.5416 10.9052C9.88842 9.98881 9.29237 9.01536 8.71356 8.02465C9.57007 7.40396 10.4364 6.79309 11.2617 6.14122C10.0952 6.03375 8.88647 5.96834 7.66107 5.91968C7.46633 4.65567 7.5175 3.14579 7.21791 1.98667C6.76462 2.93671 6.2297 3.80508 5.72226 4.70098ZM6.27621 15.1705C6.12214 15.8299 6.62974 16.1004 6.55318 16.5C6.052 16.3273 5.67498 16.2386 5.00213 16.3338C5.02318 15.8194 5.48587 15.7466 5.3899 15.1151C-1.78016 14.3 -1.79456 1.34382 5.3345 0.546422C14.2483 -0.450627 14.528 14.9414 6.27621 15.1705Z" fill="#FE544F"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M7.21769 1.98657C7.51728 3.1457 7.46611 4.65557 7.66084 5.91955C8.88625 5.96824 10.0949 6.03362 11.2615 6.14113C10.4362 6.79299 9.56984 7.40386 8.71334 8.02454C9.29215 9.01527 9.8882 9.98869 10.5414 10.9051C9.61046 10.6725 8.44827 10.1916 7.49466 9.79716C6.87673 11.1919 6.40736 12.7352 5.88823 14.2287C5.53962 13.1001 5.41321 11.7494 5.16809 10.5173C4.05262 10.5667 2.65558 10.8102 1.45666 10.9605C1.73392 10.3128 2.87174 9.57012 3.45087 8.85547C2.72786 8.30438 1.82043 7.93778 1.17969 7.30443C1.93264 6.8756 2.72074 6.482 3.39547 5.97494C3.05719 5.00224 2.73031 4.01814 2.34299 3.09445C3.43308 3.44467 4.60089 4.19707 5.72204 4.70088C6.22947 3.80499 6.7644 2.93662 7.21769 1.98657Z" fill="white"></path>
		</svg>
		</span>
		<span class="sb-author-name">'. $plugin['author'] .'</span>
		</p></div></div>
		<div class="sby-install-plugin-content">
		<p>'. $plugin['description'] .'</p>';

		$plugin_install_data = array(
			'step' => 'install',
			'action' => 'sby_install_other_plugins',
			'plugin' => $plugin['plugin'],
			'download_plugin' => $plugin['download_plugin'],
		);
		if ( ! $plugin['installed'] ) {
			$output .= sprintf(
				"<button class='sby-install-plugin-btn sbc-btn-orange' id='sby_install_op_plugin' data-plugin-atts='%s'>%s</button></div></div></div>",
				wp_json_encode( $plugin_install_data ),
				__('Install', 'feeds-for-youtube')
			);
		}
		if ( $plugin['installed'] && ! $plugin['activated'] ) {
			$plugin_install_data['step'] = 'activate';
			$plugin_install_data['action'] = 'sby_activate_other_plugins';
			$output .= sprintf(
				"<button class='sby-install-plugin-btn sb-ot-installed sbc-btn-orange' id='sby_install_op_plugin' data-plugin-atts='%s'>%s</button></div></div></div>",
				wp_json_encode( $plugin_install_data ),
				__('Activate', 'feeds-for-youtube')
			);
		}
		if ( $plugin['installed'] && $plugin['activated'] ) {
			$output .= sprintf(
				"<button class='sby-install-plugin-btn sby-btn-orange' id='sby_install_op_plugin' disabled='disabled'>%s</button></div></div></div>",
				__('Plugin installed & activated', 'feeds-for-youtube')
			);
		}

		wp_send_json_success($output, true);
		wp_die();
	}

	/**
	 * Upsell Modal Content for Single Video to Post
	 * @since 2.3
	 */
	public function sby_single_videos_upsell_modal()
	{
		// Run a security check.
		check_ajax_referer('sby-admin', 'nonce');

		// Check for permissions.
		if (!sby_current_user_can('manage_youtube_feed_options')) {
			wp_send_json_error();
		}
		$license_key = Util::get_license_key();
		$upgrade_url = sprintf('https://smashballoon.com/pricing/youtube-feed/?license_key=%s&upgrade=true&utm_campaign=youtube-pro&utm_source=feed-type&utm_medium=youtube-feed&utm_content=upgrade', $license_key);
		$heading = __('Convert YouTube videos to Wordpress Posts with Pro', 'feeds-for-youtube');
		$youtube_utm_campaign = 'youtube-pro';

		if (!\sby_is_pro()) {
			$upgrade_url = 'https://smashballoon.com/pricing/youtube-feed/?utm_campaign=youtube-free&utm_source=single-videos-to-cpt&utm_medium=youtube-feed&utm_content=upgrade';
			$youtube_utm_campaign = 'youtube-free';
		} else {
			$heading = __('Upgrade to Plus to Convert YouTube videos to WordPress Posts', 'feeds-for-youtube');
		}
		
		// Build the content for modals
		$output = '<div data-getext-view="playlist" class="sbc-extensions-popup sbc-popup-inside">
			<div class="sbc-popup-cls">
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"></path>
			</svg>
			</div>
			<div>
			<div class="sbc-extpp-top sbc-fs">
				<div class="sbc-extpp-info">
				<div class="sbc-extpp-head sbc-fs">
					<h2>'. $heading .'</h2>
				</div>
				<div class="sbc-extpp-desc sbc-fs sb-caption">Use YouTube to create a curated list of related videos and display it on your site.</div>
				<!---->
				</div>
				<div class="sbc-extpp-img"><svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_2319_54121)"><g filter="url(#filter0_ddd_2319_54121)"><g clip-path="url(#clip1_2319_54121)"><rect x="186.652" y="68.1201" width="192" height="211" rx="2" transform="rotate(3 186.652 68.1201)" fill="url(#paint0_linear_2319_54121)"/><mask id="path-3-outside-1_2319_54121" maskUnits="userSpaceOnUse" x="185.449" y="68.1201" width="192.941" height="33.017" fill="black"><rect fill="white" x="185.449" y="68.1201" width="192.941" height="33.017"/><path d="M186.652 68.1201L378.389 78.1686L377.238 100.138L185.501 90.09L186.652 68.1201Z"/></mask><path d="M377.274 99.4394L185.538 89.3909L185.464 90.789L377.201 100.838L377.274 99.4394Z" fill="#E6E6EB" mask="url(#path-3-outside-1_2319_54121)"/><rect x="197.27" y="75.6863" width="29" height="8" rx="2" transform="rotate(3 197.27 75.6863)" fill="#9295A6"/><rect x="307.043" y="82.9412" width="16" height="5" rx="1" transform="rotate(3 307.043 82.9412)" fill="#E6E6EB"/><rect x="329.012" y="84.0925" width="16" height="5" rx="1" transform="rotate(3 329.012 84.0925)" fill="#E6E6EB"/><rect x="350.984" y="85.2439" width="16" height="5" rx="1" transform="rotate(3 350.984 85.2439)" fill="#E6E6EB"/><g clip-path="url(#clip2_2319_54121)"><rect x="214.57" y="108.637" width="132.462" height="64" rx="2" transform="rotate(3 214.57 108.637)" fill="#696D80"/><circle cx="333.264" cy="122.534" r="63.0365" transform="rotate(3 333.264 122.534)" fill="#434960"/></g><circle cx="220.318" cy="190.049" r="10" transform="rotate(3 220.318 190.049)" fill="#CED0D9"/><rect x="236.609" y="184.895" width="60" height="5" rx="1" transform="rotate(3 236.609 184.895)" fill="#CED0D9"/><g opacity="0.4"><rect x="209.23" y="210.497" width="132" height="5" rx="1" transform="rotate(3 209.23 210.497)" fill="#CED0D9"/></g><g opacity="0.4"><rect x="208.082" y="232.467" width="132" height="5" rx="1" transform="rotate(3 208.082 232.467)" fill="#CED0D9"/></g><rect x="236.137" y="193.882" width="38" height="5" rx="1" transform="rotate(3 236.137 193.882)" fill="#CED0D9"/><g opacity="0.4"><rect x="208.656" y="221.482" width="123" height="5" rx="1" transform="rotate(3 208.656 221.482)" fill="#CED0D9"/></g><g opacity="0.4"><rect x="207.504" y="243.452" width="123" height="5" rx="1" transform="rotate(3 207.504 243.452)" fill="#CED0D9"/></g></g></g><path d="M111.301 189.253C110.889 188.535 109.972 188.287 109.253 188.699C108.535 189.111 108.287 190.028 108.699 190.747L111.301 189.253ZM163.26 208.707C163.71 208.011 163.51 207.083 162.813 206.633L151.471 199.312C150.775 198.863 149.847 199.063 149.397 199.759C148.948 200.455 149.148 201.383 149.844 201.832L159.926 208.34L153.418 218.422C152.969 219.118 153.169 220.047 153.865 220.496C154.561 220.945 155.49 220.745 155.939 220.049L163.26 208.707ZM108.699 190.747C112.286 196.997 120.225 202.811 129.853 206.475C139.526 210.157 151.149 211.766 162.316 209.36L161.684 206.427C151.185 208.689 140.156 207.187 130.92 203.671C121.638 200.139 114.411 194.673 111.301 189.253L108.699 190.747Z" fill="#9295A6"/><g filter="url(#filter1_ddd_2319_54121)"><g clip-path="url(#clip3_2319_54121)"><rect x="29.2305" y="67.3208" width="157.543" height="113.748" rx="2.12584" transform="rotate(-3 29.2305 67.3208)" fill="white"/><g clip-path="url(#clip4_2319_54121)"><rect width="158.54" height="77.486" transform="translate(28.5312 67.3577) rotate(-3)" fill="#696D80"/><circle cx="171.558" cy="69.0506" r="75.4469" transform="rotate(-3 171.558 69.0506)" fill="#434960"/><path d="M33.0625 144.123L75.5317 141.842" stroke="#EB2121" stroke-width="1.17874"/><line x1="190.984" y1="135.995" x2="75.5576" y2="142.136" stroke="#E6E6EB" stroke-width="1.17874"/></g><circle cx="77.4194" cy="141.061" r="3.55095" transform="rotate(-3 77.4194 141.061)" fill="#EB2121"/><rect x="44.4023" y="157.21" width="67.7775" height="9" rx="1.01955" transform="rotate(-3 44.4023 157.21)" fill="#CED0D9"/><g filter="url(#filter2_di_2319_54121)"><rect x="133.016" y="146.573" width="47.1496" height="18.8598" rx="2.94685" transform="rotate(-3 133.016 146.573)" fill="#EB2121"/><rect opacity="0.4" x="142.152" y="151.996" width="29.4685" height="6.48306" rx="1.17874" transform="rotate(-3 142.152 151.996)" fill="white"/></g></g></g></g><defs><filter id="filter0_ddd_2319_54121" x="164.064" y="61.9036" width="225.871" height="243.849" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="5.32846"/><feGaussianBlur stdDeviation="5.7725"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2319_54121"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="0.888078"/><feGaussianBlur stdDeviation="0.888078"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow_2319_54121" result="effect2_dropShadow_2319_54121"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="2.66423"/><feGaussianBlur stdDeviation="2.66423"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow_2319_54121" result="effect3_dropShadow_2319_54121"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow_2319_54121" result="shape"/></filter><filter id="filter1_ddd_2319_54121" x="15.4125" y="51.6353" width="190.917" height="149.473" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="6.37751"/><feGaussianBlur stdDeviation="6.90897"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2319_54121"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.06292"/><feGaussianBlur stdDeviation="1.06292"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow_2319_54121" result="effect2_dropShadow_2319_54121"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="3.18876"/><feGaussianBlur stdDeviation="3.18876"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow_2319_54121" result="effect3_dropShadow_2319_54121"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow_2319_54121" result="shape"/></filter><filter id="filter2_di_2319_54121" x="131.837" y="142.926" width="50.4278" height="24.2484" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="0.589369"/><feGaussianBlur stdDeviation="0.589369"/><feComposite in2="hardAlpha" operator="out"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.13 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2319_54121"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2319_54121" result="shape"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="-1.17874"/><feGaussianBlur stdDeviation="0.589369"/><feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/><feBlend mode="normal" in2="shape" result="effect2_innerShadow_2319_54121"/></filter><linearGradient id="paint0_linear_2319_54121" x1="282.652" y1="68.1201" x2="282.652" y2="279.12" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="1" stop-color="#F3F4F5"/></linearGradient><clipPath id="clip0_2319_54121"><rect width="396" height="264" fill="white"/></clipPath><clipPath id="clip1_2319_54121"><rect x="186.652" y="68.1201" width="192" height="211" rx="2" transform="rotate(3 186.652 68.1201)" fill="white"/></clipPath><clipPath id="clip2_2319_54121"><rect x="214.57" y="108.637" width="132.462" height="64" rx="2" transform="rotate(3 214.57 108.637)" fill="white"/></clipPath><clipPath id="clip3_2319_54121"><rect x="29.2305" y="67.3208" width="157.543" height="113.748" rx="2.12584" transform="rotate(-3 29.2305 67.3208)" fill="white"/></clipPath><clipPath id="clip4_2319_54121"><rect width="158.54" height="77.486" fill="white" transform="translate(28.5312 67.3577) rotate(-3)"/></clipPath></defs></svg></div>
			</div>
			<div class="sbc-extpp-bottom sbc-fs">
				<div class="ctf-extension-bullets">
				<h4>And get much more!</h4>
				<div class="ctf-extension-bullet-list">
					<div class="ctf-extension-single-bullet">
					<svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="4" height="4" fill="#0096CC"></rect>
					</svg>
					<span class="sb-small-p">Covert videos to WP Posts</span>
					</div>
					<div class="ctf-extension-single-bullet">
					<svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="4" height="4" fill="#0096CC"></rect>
					</svg>
					<span class="sb-small-p">Show subscribers</span>
					</div>
					<div class="ctf-extension-single-bullet">
					<svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="4" height="4" fill="#0096CC"></rect>
					</svg>
					<span class="sb-small-p">Show video details</span>
					</div>
					<div class="ctf-extension-single-bullet">
					<svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="4" height="4" fill="#0096CC"></rect>
					</svg>
					<span class="sb-small-p">Fast and Effective Support</span>
					</div>
					<div class="ctf-extension-single-bullet">
					<svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="4" height="4" fill="#0096CC"></rect>
					</svg>
					<span class="sb-small-p">Always up to date</span>
					</div>
					<div class="ctf-extension-single-bullet">
					<svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="4" height="4" fill="#0096CC"></rect>
					</svg>
					<span class="sb-small-p">30 day money back guarantee</span>
					</div>
				</div>
				</div>
				<div class="sbc-extpp-btns sbc-fs"><a href="'. $upgrade_url .'" target="_blank" class="sbc-extpp-get-btn sbc-btn-orange">
				Upgrade
				</a> <a href="https://smashballoon.com/youtube-feed/?utm_campaign='.$youtube_utm_campaign.'&amp;utm_source=feed-type&amp;utm_medium=youtube-feed&amp;utm_content=learn-more" target="_blank" class="sbc-extpp-get-btn sbc-btn-grey">'. __('Learn More', 'feeds-for-youtube') .'</a>
				</div>
			</div>
			</div>
		</div>';
		wp_send_json_success($output, true);
		wp_die();
	}

	/**
	 * Install Addon or Our Other Plugins
	 *
	 * @since 2.0
	 */
	public function sby_install_addon() {
		require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/PluginSilentUpgrader.php';
		require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/PluginSilentUpgraderSkin.php';
		// Run a security check.
		check_ajax_referer( 'sby-admin', 'nonce' );

		// Check for permissions.
		if ( ! sby_current_user_can( 'manage_youtube_feed_options' ) ) {
			wp_send_json_error();
		}

		$error = esc_html__( 'Could not install addon. Please download from wpforms.com and install manually.', 'feeds-for-youtube' );

		if ( empty( $_POST['plugin'] ) ) {
			wp_send_json_error( $error );
		}

		// Set the current screen to avoid undefined notices.
		set_current_screen( 'youtube-feeds-pro' );

		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'sby-feed-builder',
				),
				admin_url( 'admin.php' )
			)
		);

		$creds = request_filesystem_credentials( $url, '', false, false, null );

		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( $error );
		}

		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( $error );
		}

		/*
		 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		 */
		require_once SBY_PLUGIN_DIR . 'inc/class-install-skin.php';

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

		// Create the plugin upgrader with our custom skin.
		$installer = new \SmashBalloon\YouTubeFeed\PluginSilentUpgrader( new \SmashBalloon\YouTubeFeed\SBY_Install_Skin() );

		// Error check.
		if ( ! method_exists( $installer, 'install' ) || empty( $_POST['plugin'] ) ) {
			wp_send_json_error( $error );
		}

		$installer->install( $_POST['plugin'] ); // phpcs:ignore

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin_basename = $installer->plugin_info();

		if ( $plugin_basename ) {

			$type = 'addon';
			if ( ! empty( $_POST['type'] ) ) {
				$type = sanitize_key( $_POST['type'] );
			}

			// Activate the plugin silently.
			$activated = activate_plugin( $plugin_basename );

			if ( ! is_wp_error( $activated ) ) {
				wp_send_json_success(
					array(
						'msg'          => 'plugin' === $type ? esc_html__( 'Plugin installed & activated.', 'feeds-for-youtube' ) : esc_html__( 'Addon installed & activated.', 'feeds-for-youtube' ),
						'is_activated' => true,
						'basename'     => $plugin_basename,
					)
				);
			} else {
				wp_send_json_success(
					array(
						'msg'          => 'plugin' === $type ? esc_html__( 'Plugin installed.', 'feeds-for-youtube' ) : esc_html__( 'Addon installed.', 'feeds-for-youtube' ),
						'is_activated' => false,
						'basename'     => $plugin_basename,
					)
				);
			}
		}

		wp_send_json_error( $error );
	}

	/**
	 * Activate our other plugins
	 *
	 * @since 2.0
	 */
	public function sby_activate_addon() {
		require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/PluginSilentUpgrader.php';
		require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/PluginSilentUpgraderSkin.php';
		require_once SBY_PLUGIN_DIR . 'inc/class-install-skin.php';
		// Run a security check.
		check_ajax_referer( 'sby-admin', 'nonce' );

		// Check for permissions.
		if ( ! sby_current_user_can( 'manage_youtube_feed_options' ) ) {
			wp_send_json_error();
		}

		if ( isset( $_POST['plugin'] ) ) {
			$type = 'addon';
			if ( ! empty( $_POST['type'] ) ) {
				$type = sanitize_key( $_POST['type'] );
			}
			$activate = activate_plugins( $_POST['plugin'] );
			if ( ! is_wp_error( $activate ) ) {
				if ( 'plugin' === $type ) {
					wp_send_json_success( esc_html__( 'Plugin activated.', 'feeds-for-youtube' ) );
				} else {
					wp_send_json_success( esc_html__( 'Addon activated.', 'feeds-for-youtube' ) );
				}
			}
		}

		wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'feeds-for-youtube' ) );
	}

	private function sby_do_background_tasks( $feed_details ) {
		$locator = new Feed_Locator( $feed_details );
		$locator->add_or_update_entry();
		if ( $locator::should_clear_old_locations() ) {
			$locator::delete_old_locations();
		}
	}
}
