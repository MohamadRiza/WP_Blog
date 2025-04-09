<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin\Settings;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Container;

class PagesServiceContainer extends ServiceProvider {
	private $services = [
		SettingsPage::class,
		SingleVideoPage::class,
		HelpPage::class,
		AboutPage::class,
		SetupPage::class,
	];

	public function register() {
		$container = Container::get_instance();
		foreach ( $this->services as $service ) {
			$container->get($service)->register();
		}
	}
}