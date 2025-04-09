<?php

/**
 * Handles creation and destruction of the custom database tables.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database;

class DBManager
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * Create or update the custom database tables.
	 *
	 * @return void
	 */
	public function create_or_update_db_tables()
	{
		( new FeedCacheTable() )->create_or_update_db_table();
		( new FeedLocatorTable() )->create_or_update_db_table();
		( new FeedsTable() )->create_or_update_db_table();
		( new PostsTable() )->create_or_update_db_table();
		( new SourcesTable() )->create_or_update_db_table();
	}

	/**
	 * Drop the custom database tables.
	 *
	 * @return void
	 */
	public function drop_db_tables()
	{
		( new FeedCacheTable() )->drop_table();
		( new FeedLocatorTable() )->drop_table();
		( new FeedsTable() )->drop_table();
		( new PostsTable() )->drop_table();
		( new SourcesTable() )->drop_table();
	}
}
