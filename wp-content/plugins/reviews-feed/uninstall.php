<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}


$settings = get_option( 'sbr_settings', array() );

$preserve_settings = ! empty( $settings['preserve_settings'] ) && $settings['preserve_settings'];

if ( ! $preserve_settings ) {

	// wp_options
	$wp_options_keys = array(
		'sbr_apikeys',
		'sbr_apikeys_limit',
		'sbr_business_cache',
		'sbr_db_version',
		'sbr_settings',
		'sbr_statuses',
	);
	foreach ( $wp_options_keys as $key ) {
		delete_option( $key );
	}

	// user roles
	global $wp_roles;
	$wp_roles->remove_cap( 'administrator', 'manage_reviews_feed_options' );

	// cron events
	$cron_keys = array(
		'sbr_cron_additional_batch',
		'sbr_feed_update',
	);
	foreach ( $cron_keys as $key ) {
		wp_clear_scheduled_hook( $key );
	}

	// custom tables
	global $wpdb;

	$table_keys = array(
		'sbr_feeds',
		'sbr_feed_caches',
		'sbr_feed_locator',
		'sbr_posts',
		'sbr_reviews_posts',
		'sbr_sources',
	);
	foreach ( $table_keys as $key ) {
		$table_name = $wpdb->prefix . $key;
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}

	// custom image files
	$upload = wp_upload_dir();
	$folder = trailingslashit( $upload['basedir'] ) . trailingslashit( 'sbr-feed-images' );
	$image_files = glob( $folder . '*' ); // get all file names
	foreach ( $image_files as $file ) { // iterate files
		if ( is_file( $file ) ) {
			unlink( $file );
		}
	}

	global $wp_filesystem;
	$wp_filesystem->delete( $folder, true );
}
