<?php

namespace SmashBalloon\YouTubeFeed\Services\Upgrade;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Container;
use SmashBalloon\YouTubeFeed\Services\Upgrade\Routines\UpgradeRoutine;
use SmashBalloon\YouTubeFeed\Services\Upgrade\Routines\V2Routine;
use SmashBalloon\YouTubeFeed\Services\Upgrade\Routines\OnboardingWizardRoutine;

class RoutineManagerService extends ServiceProvider {
	/**
	 * a list of upgrade routines to be executed,
	 * keep the correct order, newer is always at the end of the list.
	 * @var UpgradeRoutine[]
	 */
	private $routines = [
		V2Routine::class,
		OnboardingWizardRoutine::class
	];

	public function is_fresh_install() {
		$db_version = get_option( 'sby_db_version', false );
		return false === $db_version;
	}

	public function register() {
		$container = Container::get_instance();
		$is_fresh_install = $this->is_fresh_install();

		foreach ($this->routines as $routine) {
			$routine = $container->get($routine);
			$routine->set_is_fresh_install($is_fresh_install);
			$routine->register();
		}
	}
}