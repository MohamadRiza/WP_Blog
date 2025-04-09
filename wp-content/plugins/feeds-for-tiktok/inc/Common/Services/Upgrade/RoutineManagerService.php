<?php

namespace SmashBalloon\TikTokFeeds\Common\Services\Upgrade;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Services\Upgrade\Routines\RegisterWebsiteRoutine;
use SmashBalloon\TikTokFeeds\Common\Services\Upgrade\Routines\RefreshTokenRoutine;
use SmashBalloon\TikTokFeeds\Common\Services\Upgrade\Routines\FeedUpdateRoutine;

class RoutineManagerService extends ServiceProvider
{
	/**
	 * A list of upgrade routines to be run.
	 * Keep this list in order of oldest to newest.
	 *
	 * @var array
	 */
	private $routines = [
		RegisterWebsiteRoutine::class,
		RefreshTokenRoutine::class,
		FeedUpdateRoutine::class,
	];

	/**
	 * Register the upgrade routines.
	 *
	 * @return void
	 */
	public function register()
	{
		foreach ($this->routines as $routine) {
			$routine = new $routine();
			$routine->register();
		}
	}
}
