<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;

class ActivationService extends ServiceProvider {

	public function register() {
		add_action( 'activated_plugin', [ $this, 'on_plugin_activation' ] );
	}

	public function on_plugin_activation( $plugin ) {
		if ( ! in_array( basename( $plugin ), array( 'youtube-feed.php', 'youtube-feed-pro.php' ) ) ) {
			return;
		}

		$plugin_to_deactivate = 'youtube-feed.php';
		if ( basename( $plugin ) === $plugin_to_deactivate ) {
			$plugin_to_deactivate = 'youtube-feed-pro.php';
		}

		foreach ( $this->get_active_plugins() as $basename ) {
			if ( false !== strpos( $basename, $plugin_to_deactivate ) ) {
				deactivate_plugins( $basename );

				return;
			}
		}
	}

	private function get_active_plugins() {
		if ( is_multisite() ) {
			$active_plugins = array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) );
		} else {
			$active_plugins = (array) get_option( 'active_plugins', array() );
		}

		return $active_plugins;
	}

}