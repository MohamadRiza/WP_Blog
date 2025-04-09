<?php

namespace SmashBalloon\YouTubeFeed;

use Smashballoon\Customizer\Config;
use Smashballoon\Customizer\DB;
use Smashballoon\Customizer\PreviewProvider;
use Smashballoon\Customizer\ProxyProvider;
use Smashballoon\Stubs\Traits\Singleton;
use SmashBalloon\YouTubeFeed\Customizer\ShortcodePreviewProvider;

class Container {
	use Singleton;

	/**
	 * @return \SmashBalloon\YouTubeFeed\Vendor\DI\Container
	 */
	public static function get_instance() {
		if(null === self::$instance) {
			self::$instance = ( new \SmashBalloon\YouTubeFeed\Vendor\DI\ContainerBuilder() )->build();

			self::$instance->set(Config::class, new \SmashBalloon\YouTubeFeed\Customizer\Config());
			self::$instance->set(DB::class, new \SmashBalloon\YouTubeFeed\Customizer\DB());
			self::$instance->set(ProxyProvider::class, new \SmashBalloon\YouTubeFeed\Customizer\ProxyProvider());
			self::$instance->set( PreviewProvider::class, new ShortcodePreviewProvider());
			self::$instance->set(SBY_Settings::class, new SBY_Settings([], sby_get_database_settings()));

		}
		return self::$instance;
	}
}