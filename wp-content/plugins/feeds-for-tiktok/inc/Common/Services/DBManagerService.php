<?php

/**
 * Class responsible for database management.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Services;

use SmashBalloon\TikTokFeeds\Common\Container;

/**
 * DBManagerService class.
 */
class DBManagerService
{
	/**
	 * Register.
	 *
	 * @return void
	 */
	public function register()
	{
		add_action('wp_loaded', [ $this, 'setup_db_tables' ]);
	}

	/**
	 * Setup db tables.
	 *
	 * @return void
	 */
	public function setup_db_tables()
	{
		Container::get_instance()->get('DBManager')->create_or_update_db_tables();
	}

	/**
	 * Drop db tables.
	 *
	 * @return void
	 */
	public function drop_db_tables()
	{
		Container::get_instance()->get('DBManager')->drop_db_tables();
	}
}
