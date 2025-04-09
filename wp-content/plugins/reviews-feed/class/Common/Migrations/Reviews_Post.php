<?php

namespace SmashBalloon\Reviews\Common\Migrations;

use Smashballoon\Stubs\Services\ServiceProvider;

/**
 * Migration for Reviews Post table creation.
 */
class Reviews_Post extends ServiceProvider
{
	public function register()
	{
		$this->create_table();
	}

	private function create_table()
	{
	}
}
