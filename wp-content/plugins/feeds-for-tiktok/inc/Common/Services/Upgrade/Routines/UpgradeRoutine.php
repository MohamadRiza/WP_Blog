<?php

namespace SmashBalloon\TikTokFeeds\Common\Services\Upgrade\Routines;

use Smashballoon\Stubs\Services\ServiceProvider;

class UpgradeRoutine extends ServiceProvider
{
	/**
	 * The target version for this routine.
	 *
	 * @var int
	 */
	protected $target_version = 0;

	/**
	 * Registers the routine.
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->will_run()) {
			$this->run();
			$this->update_db_version();
		}
	}

	/**
	 * Checks if the routine will run.
	 *
	 * @return bool
	 */
	protected function will_run()
	{
		$current_schema = (float) get_option('sbtt_db_version', 0);

		return $current_schema < (float) $this->target_version;
	}

	/**
	 * Updates the database version.
	 *
	 * @return void
	 */
	protected function update_db_version()
	{
		update_option('sbtt_db_version', $this->target_version);
	}

	/**
	 * Runs the routine.
	 *
	 * @return void
	 */
	public function run()
	{
		// implement your own version.
	}
}
