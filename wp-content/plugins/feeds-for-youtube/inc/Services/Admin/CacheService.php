<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;

class CacheService extends ServiceProvider {

	public function register() {
		add_action( 'wp_ajax_sby_clear_cache', [ $this, 'ajax_clear_cache' ] );
	}

	public function ajax_clear_cache() {
		Util::ajaxPreflightChecks();
		// clear object cache
		sby_clear_object_cache();
		// clear transients
		sby_clear_cache();
		wp_clear_scheduled_hook( 'sby_feed_update' );
		wp_send_json_success();
	}

}