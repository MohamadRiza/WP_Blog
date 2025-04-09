<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;


use Smashballoon\Stubs\Services\ServiceProvider;

class AssetsService extends ServiceProvider {

	public function register() {
		add_action( 'admin_enqueue_scripts', [$this, 'sby_admin_style'] );
		add_action( 'admin_enqueue_scripts', [$this, 'sby_admin_scripts'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_vue_assets'] );
	}

	public function enqueue_vue_assets() {
		if ( ! sby_is_admin_page() ) {
			return;
		}
		wp_enqueue_script(
			'feed-builder-vue',
			'https://cdn.jsdelivr.net/npm/vue@2.6.12',
			null,
			'2.6.12',
			true
		);
	}
	public function sby_admin_style() {
		wp_enqueue_style( SBY_SLUG . '_admin_notices_css', SBY_PLUGIN_URL . 'css/sby-notices.css', array(), SBYVER );
		wp_enqueue_style( SBY_SLUG . '_admin_css', SBY_PLUGIN_URL . 'css/admin.css', array(), SBYVER );
		if ( ! sby_is_admin_page() ) {
			return;
		}
		wp_enqueue_style( 'sb_font_awesome',
			'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	public function sby_admin_scripts() {
		// We need to enqueue this globally
		wp_enqueue_script( SBY_SLUG . '_sby_admin_js', SBY_PLUGIN_URL . 'js/sby-admin.js', array(), SBYVER );

		// wp_enqueue_script( SBY_SLUG . '_admin_js', SBY_PLUGIN_URL . 'js/admin.js', array(), SBYVER );
		wp_localize_script( SBY_SLUG . '_sby_admin_js', 'sby_admin', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'sby-admin' )
			)
		);		
		if ( ! sby_is_admin_page() ) {
			return;
		}
		wp_enqueue_script( 'wp-color-picker' );
	}
}