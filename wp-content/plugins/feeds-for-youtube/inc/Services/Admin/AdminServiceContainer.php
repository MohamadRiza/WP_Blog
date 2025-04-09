<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use SmashBalloon\YouTubeFeed\Vendor\DI\Container;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Customizer\Customizer_Compatibility;
use SmashBalloon\YouTubeFeed\Pro\SBY_API_Connect_Pro;
use SmashBalloon\YouTubeFeed\SBY_API_Connect;
use SmashBalloon\YouTubeFeed\SBY_Settings;
use SmashBalloon\YouTubeFeed\Admin\SBY_Admin_Notice;
use SmashBalloon\YouTubeFeed\Services\Admin\Settings\PagesServiceContainer;

class AdminServiceContainer extends ServiceProvider {
	private $services = [
		MenuService::class,
		Customizer_Compatibility::class,
		AssetsService::class,
		GUIService::class,
		LicenseService::class,
		MiscService::class,
		PagesServiceContainer::class,
		SourcesService::class,
		SBY_Admin_Notice::class,
		ImporterService::class,
		CacheService::class
	];

	public function register() {
		$container = \SmashBalloon\YouTubeFeed\Container::get_instance();
		$container->set(SBY_Settings::class, new SBY_Settings([], sby_get_database_settings()));
		foreach ( $this->services as $service ) {
			$container->get($service)->register();
		}
	}
}