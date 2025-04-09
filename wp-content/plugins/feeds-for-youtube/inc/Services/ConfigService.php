<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;

class ConfigService extends ServiceProvider {
	public function register() {
		add_filter('sb_customizer_sources_table', function($table) {
			global $wpdb;
			return $wpdb->prefix . 'sby_sources';
		});
	}
}