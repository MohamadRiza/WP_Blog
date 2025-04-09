<?php

/**
 * Source table migration.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database\Migrations;

use SmashBalloon\TikTokFeeds\Common\Database\Migrations\Migration;

/**
 * Source table migration.
 */
class SourcesScopeFieldUpdate implements Migration
{
	/**
	 * Run the migration.
	 *
	 * @return void
	 */
	public function apply()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . SBTT_SOURCES_TABLE;

		// scope field had a varchar(50) update to varchar(255).
		$sql = "ALTER TABLE $table_name MODIFY COLUMN scope VARCHAR(255) NOT NULL";

		$wpdb->query($sql);
	}

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function rollback()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . SBTT_SOURCES_TABLE;

		$sql = "ALTER TABLE $table_name MODIFY COLUMN scope VARCHAR(50) NOT NULL";

		$wpdb->query($sql);
	}
}
