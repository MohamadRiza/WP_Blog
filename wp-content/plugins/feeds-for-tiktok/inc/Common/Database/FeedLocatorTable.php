<?php

/**
 * Table responsible for storing feed locators.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database;

class FeedLocatorTable extends Table
{
	/**
	 * Custom table name.
	 *
	 * @var string
	 */
	protected const TABLE_NAME = SBTT_FEED_LOCATOR;

	/**
	 * Table version.
	 *
	 * @var int
	 */
	protected const VERSION = 1;

	/**
	 * Create custom table.
	 *
	 * @return bool True if table exists if created.
	 */
	public function create_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            feed_id varchar(50) NOT NULL DEFAULT '',
            post_id bigint(20) unsigned NOT NULL,
            html_location varchar(50) NOT NULL DEFAULT 'unknown',
            shortcode_atts longtext NOT NULL,
            last_update datetime,
            PRIMARY KEY  (id),
            INDEX feed_id (feed_id),
            INDEX post_id (post_id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		return $this->table_exists();
	}
}
