<?php

namespace SmashBalloon\YouTubeFeed;

class SBY_Posts_Manager
{
	var $options_prefix;

	/**
	 * @var int
	 */
	var $limit;

	/**
	 * @var array
	 */
	var $errors;

	var $ajax_status;

	/**
	 * @var array
	 */
	var $frontend_errors;

	/**
	 * @var bool
	 */
	var $resizing_tables_exist;

	/**
	 * SBY_Posts_Manager constructor.
	 */
	public function __construct( $options_prefix, $errors, $ajax_status ) {
		$this->options_prefix = $options_prefix;
		$this->errors = $errors;
		$this->ajax_status = $ajax_status;
		$this->frontend_errors = array();
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_ajax_status() {
		return $this->ajax_status;
	}

	/**
	 * @param $to_update
	 *
	 * @since 1.0
	 */
	public function update_ajax_status( $to_update ) {
		foreach ( $to_update as $key => $value ) {
			$this->ajax_status[ $key ] = $value;
		}

		update_option( $this->options_prefix . '_ajax_status', $this->ajax_status );
	}

	/**
	 * When the plugin is first installed and used, an AJAX call to admin-ajax.php
	 * is made to verify that it's available
	 *
	 * @param bool $force_check
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function maybe_start_ajax_test( $force_check = false ) {
		if ( ! $this->ajax_status['tested'] || $force_check ) {
			set_transient( $this->options_prefix . '_doing_ajax_test', 'yes', 60*60 );
			$this->update_ajax_status( array( 'tested' => true ) );
			return true;
		}

		return false;
	}

	/**
	 * Called if a successful Admin ajax request is made
	 *
	 * @since 1.0
	 */
	public function update_successful_ajax_test() {
		$this->update_ajax_status( array( 'successful' => true ) );
	}

	/**
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function should_add_ajax_test_notice() {
		return ($this->ajax_status['tested'] && ! $this->ajax_status['successful'] && get_transient( $this->options_prefix . '_doing_ajax_test' ) !== 'yes');
	}

	/**
	 * The plugin has a limit on how many post records can be stored and
	 * images resized to avoid overloading servers. This function deletes the post that
	 * has the longest time passed since it was retrieved.
	 *
	 * @since 1.0
	 */
	public function delete_least_used_image() {

	}

	/**
	 * Calculates how many records are in the database and whether or not it exceeds the limit
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function max_total_records_reached() {
		global $wpdb;
		$table_name = $wpdb->prefix . SBY_ITEMS;

		$num_records = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

		if ( !isset( $this->limit ) && (int)$num_records > SBY_MAX_RECORDS ) {
			$this->limit = (int)$num_records - SBY_MAX_RECORDS;
		}

		return ((int)$num_records > SBY_MAX_RECORDS);
	}

	/**
	 * The plugin caps how many new images are created in a 15 minute window to
	 * avoid overloading servers
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function max_resizing_per_time_period_reached() {
		global $wpdb;
		$table_name = $wpdb->prefix . SBY_ITEMS;

		$fifteen_minutes_ago = date( 'Y-m-d H:i:s', time() - 15 * 60 );

		$num_new_records = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE created_on > '$fifteen_minutes_ago'" );

		return ((int)$num_new_records > 100);
	}

	/**
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function image_resizing_disabled() {
		global $sby_settings;

		$disable_resizing = isset( $sby_settings['disable_resize'] ) ? $sby_settings['disable_resize'] === 'on' || $sby_settings['disable_resize'] === true : false;

		if ( ! $disable_resizing ) {
			$disable_resizing = isset( $this->resizing_tables_exist ) ? ! $this->resizing_tables_exist : ! $this->does_resizing_tables_exist();
		}

		return $disable_resizing;
	}

	/**
	 * Used to skip image resizing if the tables were never successfully
	 * created
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function does_resizing_tables_exist() {
		return true;
	}

	/**
	 * Resets the custom tables and deletes all image files
	 *
	 * @since 1.0
	 */
	public function delete_all_sby_posts() {
		$upload = wp_upload_dir();

		global $wpdb;

		$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( SBY_UPLOADS_NAME ) . '*'  ); // get all file names
		foreach ( $image_files as $file ) { // iterate files
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}

		$options = get_option( $this->options_prefix . '_settings', array() );
		$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

		foreach ( $connected_accounts as $account_id => $data ) {

			if ( isset( $data['local_avatar'] ) ) {
				$connected_accounts[ $account_id ]['local_avatar'] = false;
			}

		}

		$options['connected_accounts'] = $connected_accounts;

		update_option( $this->options_prefix . '_settings', $options );

		$table_name = $wpdb->prefix . "options";

		$wpdb->query( "
			        DELETE
			        FROM $table_name
			        WHERE `option_name` LIKE ('%\_transient\_\$sby\_%')
			        " );
		$wpdb->query( "
			        DELETE
			        FROM $table_name
			        WHERE `option_name` LIKE ('%\_transient\_timeout\_\$sby\_%')
			        " );

		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . SBY_UPLOADS_NAME;
		if ( ! file_exists( $upload_dir ) ) {
			$created = wp_mkdir_p( $upload_dir );
			if ( $created ) {
				$this->remove_error( 'upload_dir' );
			} else {
				$this->add_error( 'upload_dir', array( __( 'There was an error creating the folder for storing resized images.', 'feeds-for-youtube' ), $upload_dir ) );
			}
		} else {
			$this->remove_error( 'upload_dir' );
		}

	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * @param $type
	 * @param $message_array
	 *
	 * @since 1.0
	 */
	public function add_error( $type, $message_array ) {
		$message_array[] = "Error timestamp: " . strtotime( 'now' );
		$this->errors[ $type ] = $message_array;

		update_option( 'sby_errors', $this->errors, false );
	}

	/**
	 * @param $type
	 *
	 * @since 1.0
	 */
	public function remove_error( $type ) {
		if ( isset( $this->errors[ $type ] ) ) {
			unset( $this->errors[ $type ] );

			update_option( $this->options_prefix . '_errors', $this->errors, false );
		}
	}

	public function remove_all_errors() {
		delete_option( $this->options_prefix . '_errors' );
	}

	/**
	 * @param $type
	 * @param $message
	 *
	 * @since 1.0
	 */
	public function add_frontend_error( $type, $message ) {
		$this->frontend_errors[ $type ] = $message;
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_frontend_errors() {
		return $this->frontend_errors;
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function reset_frontend_errors() {
		return $this->frontend_errors = array();
	}

	/**
	 * @since 1.0
	 */
	public function add_api_request_delay( $time_in_seconds = 300, $account_id = false ) {
		if ( $account_id ) {
			set_transient( SBY_USE_BACKUP_PREFIX . $this->options_prefix . '_'  . $account_id, '1', $time_in_seconds );
		} else {
			set_transient( SBY_USE_BACKUP_PREFIX . $this->options_prefix . '_delay_requests', '1', $time_in_seconds );
		}
	}

	/**
	 * @since 1.0
	 */
	public function are_current_api_request_delays( $account_id = false ) {
		$is_delay = (get_transient( SBY_USE_BACKUP_PREFIX . $this->options_prefix . '_delay_requests' ) !== false);

		return $is_delay;
	}
}