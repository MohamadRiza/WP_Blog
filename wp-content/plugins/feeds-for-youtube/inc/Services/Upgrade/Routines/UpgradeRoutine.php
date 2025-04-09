<?php

namespace SmashBalloon\YouTubeFeed\Services\Upgrade\Routines;

use Smashballoon\Stubs\Services\ServiceProvider;

class UpgradeRoutine extends ServiceProvider {
	protected $target_version = 0;
	protected $is_fresh_install = false;

	public function register() {
		if ( $this->will_run() ) {
			$this->run();
			$this->update_db_version();
		}
	}

	protected function will_run() {
		$current_schema = (float) get_option( 'sby_db_version', 0 );

		return $current_schema < (float) $this->target_version;
	}

	protected function update_db_version() {
		update_option('sby_db_version', $this->target_version);
	}

	public function run() {
		//implement your own version
	}

	/**
	 * Set fresh install flag.
	 *
	 * @param $is_fresh_install
	 *
	 * @return void
	 */
	public function set_is_fresh_install($is_fresh_install) {
		$this->is_fresh_install = $is_fresh_install;
	}
}
