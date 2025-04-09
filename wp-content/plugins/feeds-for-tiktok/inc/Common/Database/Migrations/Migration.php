<?php

/**
 * Interface responsible for defining the methods that must be implemented by a migration.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database\Migrations;

/**
 * Interface Migration
 */
interface Migration
{
	/**
	 * Run the migration.
	 *
	 * @return void
	 */
	public function apply();

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function rollback();
}
