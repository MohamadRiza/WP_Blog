<?php

namespace SmashBalloon\YouTubeFeed\Customizer;

use SmashBalloon\YouTubeFeed\Pro\SBY_Settings_Pro;
use SmashBalloon\YouTubeFeed\SBY_Settings;

class ProxyProvider extends \Smashballoon\Customizer\ProxyProvider {
	public function get_settings_class() {
		if(!sby_is_pro_version()) {
			return new SBY_Settings([], sby_get_database_settings());
		}
		return new SBY_Settings_Pro([], sby_get_database_settings());
	}

	public function get_db_settings() {
		return sby_get_database_settings();
	}
}