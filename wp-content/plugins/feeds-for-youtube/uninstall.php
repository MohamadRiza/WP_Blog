<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

//If the user is preserving the settings then don't delete them
$options = get_option( 'sby_settings' );
$sby_preserve_settings = isset( $options[ 'preserve_settings' ] ) ? $options[ 'preserve_settings' ] : false;

// allow the user to preserve their settings in case they are upgrading
if ( ! $sby_preserve_settings ) {

	// clear cron jobs
	wp_clear_scheduled_hook( 'sby_cron_job' );
	wp_clear_scheduled_hook( 'sby_feed_update' );

	// clean up options from the database
	delete_option( 'sby_license_key' );
	delete_option( 'sby_license_data' );
	delete_option( 'sby_license_status' );
	delete_option( 'sby_settings' );
	delete_option( 'sby_cron_report' );
	delete_option( 'sby_errors' );
	delete_option( 'sby_ajax_status' );
	delete_option( 'sby_db_version' );
	delete_option( 'sby_statuses' );
	delete_option( 'sby_channel_ids' );
	delete_option( 'sby_check_license_api_when_expires' );
	delete_option( 'sby_check_license_api_post_grace_period' );

	delete_option( 'sby_rating_notice' );
	delete_option( 'sby_notifications' );
	delete_option( 'sby_newuser_notifications' );
	delete_option( 'sby_usage_tracking_config' );
	// delete role
	global $wp_roles;
	$wp_roles->remove_cap( 'administrator', 'manage_youtube_feed_options' );

	// delete all custom post type data
	global $wpdb;

	$youtube_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'sby_videos';" );

	$id_string = implode( ', ', $youtube_ids );
	if ( ! empty( $id_string ) ) {
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id IN ($id_string);" );
		$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = 'sby_videos';" );
	}

	// delete the onboarding data
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE `%sby_onboarding%`") );

	// delete transients and backup data
	$table_name = $wpdb->prefix . "options";
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\!sby\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_sby\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_sby\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_&sby\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_&sby\_%')
        " );
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
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE '%~sby%'
        " );

	// Delete sby_onboarding data from usermeta table
	$usermeta_table_name = $wpdb->prefix . "usermeta";
	$wpdb->query( "
        DELETE
        FROM $usermeta_table_name
        WHERE `meta_key` LIKE '%sby_onboarding%'
        " );

	//delete image resizing related things
	$posts_table_name       = $wpdb->prefix . 'sby_items';
	$feeds_posts_table_name = $wpdb->prefix . 'sby_items_feeds';
	$locator_table_name = $wpdb->prefix . 'sby_feed_locator';
	$feed_caches_table_name = $wpdb->prefix . 'sby_feed_caches';
	$feeds_table_name = $wpdb->prefix . 'sby_feeds';

	$upload                 = wp_upload_dir();
	$wpdb->query( "DROP TABLE IF EXISTS $posts_table_name" );
	$wpdb->query( "DROP TABLE IF EXISTS $feeds_posts_table_name" );
	$wpdb->query( "DROP TABLE IF EXISTS $locator_table_name" );
	$wpdb->query( "DROP TABLE IF EXISTS $feed_caches_table_name" );
	$wpdb->query( "DROP TABLE IF EXISTS $feeds_table_name" );

	$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( 'sby-local-media' ) . '*' ); // get all file names
	foreach ( $image_files as $file ) { // iterate files
		if ( is_file( $file ) ) {
			unlink( $file );
		} // delete file
	}

	global $wp_filesystem;

	$wp_filesystem->delete( trailingslashit( $upload['basedir'] ) . trailingslashit( 'sby-local-media' ) , true );

	$pto = get_post_type_object( 'sby_videos' );

	$admin_caps = array(
		'edit_sby_videos',
		'read_sby_videos',
		'delete_sby_videos',
		'edit_sby_videos',
		'edit_others_sby_videos',
		'publish_sby_videos',
		'read_private_sby_videos',
		'read',
		'delete_sby_videos',
		'delete_private_sby_videos',
		'delete_published_sby_videos',
		'delete_others_sby_videos',
		'edit_private_sby_videos',
		'edit_published_sby_videos',
	);
	$author_caps = array(
		'edit_sby_videos',
		'read_sby_videos',
		'delete_sby_videos',
		'edit_sby_videos',
		'publish_sby_videos',
		'read',
		'delete_sby_videos',
		'delete_published_sby_videos',
		'edit_published_sby_videos',
	);

	if ( ! empty( $pto ) ) {
		foreach ( array( 'administrator', 'editor' ) as $role_id ) {
			foreach ( $admin_caps as $cap ) {
				$wp_roles->remove_cap( $role_id, $cap );
			}
		}
		foreach ( $author_caps as $cap ) {
			$wp_roles->remove_cap( 'author', $cap );
		}
	}

	$admin_caps = array(
		'edit_sby_video',
		'read_sby_video',
		'delete_sby_video',
		'edit_sby_video',
		'edit_others_sby_video',
		'publish_sby_video',
		'read_private_sby_video',
		'read',
		'delete_sby_video',
		'delete_private_sby_video',
		'delete_published_sby_video',
		'delete_others_sby_video',
		'edit_private_sby_video',
		'edit_published_sby_video',
	);
	$author_caps = array(
		'edit_sby_video',
		'read_sby_video',
		'delete_sby_video',
		'edit_sby_video',
		'publish_sby_video',
		'read',
		'delete_sby_video',
		'delete_published_sby_video',
		'edit_published_sby_video',
	);

	if ( ! empty( $pto ) ) {
		foreach ( array( 'administrator', 'editor' ) as $role_id ) {
			foreach ( $admin_caps as $cap ) {
				$wp_roles->remove_cap( $role_id, $cap );
			}
		}
		foreach ( $author_caps as $cap ) {
			$wp_roles->remove_cap( 'author', $cap );
		}
	}
}


