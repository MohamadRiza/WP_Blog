<?php

namespace SmashBalloon\YouTubeFeed\Customizer;

use Smashballoon\Customizer\Feed_Builder;

class DB extends \Smashballoon\Customizer\DB {
	protected $feeds_table = 'sby_feeds';
	protected $sources_table = 'sby_sources';

	/**
	 * Query the feeds table
	 * Porcess to define the name of the feed when adding new
	 *
	 * @param array $args
	 *
	 * @return array|bool
	 *
	 * @since 2.0
	 */
	public static function feeds_query_name( $feedname ) {
		global $wpdb;
		$feeds_table_name = $wpdb->prefix . 'sby_feeds';
		$sql = $wpdb->prepare(
			"SELECT * FROM $feeds_table_name
			WHERE feed_name LIKE %s;",
			$wpdb->esc_like($feedname) . '%'
		);
		$count = sizeof($wpdb->get_results( $sql, ARRAY_A ));
		return ($count == 0) ? $feedname : $feedname .' ('. ($count+1) .')';
	}

	/**
	 * Query to Duplicate a Single Feed
	 *
	 * @param array $args
	 *
	 * @return array|bool
	 *
	 * @since 2.0
	 */
	public static function duplicate_feed_query( $feed_id ){
		global $wpdb;
		$feeds_table_name = $wpdb->prefix . 'sby_feeds';
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $feeds_table_name (feed_name, settings, author, status)
				SELECT CONCAT(feed_name, ' (copy)'), settings, author, status
				FROM $feeds_table_name
				WHERE id = %d; ", $feed_id
			)
		);

		$builder = Feed_Builder::instance();
		echo sby_json_encode($builder->get_feed_list());
		wp_die();
	}

	/**
	 * Query to Remove Feeds from Database
	 *
	 * @param array $args
	 *
	 * @return array|bool
	 *
	 * @since 2.0
	 */
	public static function delete_feeds_query( $feed_ids_array ) {
		global $wpdb;
		$feeds_table_name = $wpdb->prefix . 'sby_feeds';
		$feed_caches_table_name = $wpdb->prefix . 'sby_feed_caches';
		$feed_ids_array = array_map('intval', $feed_ids_array);
		$feed_ids_array = implode(',', $feed_ids_array);
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $feeds_table_name WHERE id IN ($feed_ids_array)"
			)
		);
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $feed_caches_table_name WHERE feed_id IN ($feed_ids_array)"
			)
		);

		$builder = Feed_Builder::instance();
		echo sby_json_encode($builder->get_feed_list());
		wp_die();
	}

}