<?php

namespace SmashBalloon\YouTubeFeed\Customizer;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;

class Customizer_Compatibility extends ServiceProvider {

	public function register() {
		add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
	}

	public function register_scripts() {
		$asset_url = trailingslashit( SBY_PLUGIN_URL ) . 'js/customizer.min.js';

		if ( isset( $_GET['sb_debug'] ) ) {
			$asset_url = trailingslashit( SBY_PLUGIN_URL ) . 'js/customizer-debug.js';
		}

		if(!Util::isProduction()) {
			$asset_url = 'http://localhost:9005/customizer.min.js';
		}
		
		// only enqueue the below scripts on allowed pages; YouTube plugin All Feeds page
		if ( !$this->is_allowed_screens() ) {
			return;
		}
		wp_enqueue_script( 'sby_builder_extension', $asset_url, [], SBYVER );
	}

	/**
	 * Check for allowed screens
	 * 
	 * @since 2.0
	 */
	public function is_allowed_screens() {
		global $current_user;
		$current_screen = get_current_screen();
		$allowed_screens = array(
			'toplevel_page_sby-feed-builder',
		);
		if ( in_array( $current_screen->base, $allowed_screens )  ) {
			return true;
		}
		return;
	}
}