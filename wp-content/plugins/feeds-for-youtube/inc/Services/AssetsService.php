<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\SBY_Settings;

class AssetsService extends ServiceProvider {
	public function register() {
		add_action( 'wp_footer', [$this, 'sby_custom_js'] );
		add_action( 'wp_head', [$this, 'sby_custom_css'] );
		add_action( 'sby_enqueue_scripts', [$this, 'sby_scripts_enqueue'], 10, 1);
		add_action( 'wp_enqueue_scripts', [$this, 'sby_scripts_enqueue'], 10, 1);

	}
	/**
	 * Adds the ajax url and custom JavaScript to the page
	 */
	public function sby_custom_js() {
		global $sby_settings;

		$js = isset( $sby_settings['custom_js'] ) ? trim( $sby_settings['custom_js'] ) : '';

		echo '<!-- YouTube Feeds JS -->';
		echo "\r\n";
		echo '<script type="text/javascript">';
		echo "\r\n";

		if ( ! empty( $js ) ) {
			echo "\r\n";
			echo "if (typeof jQuery !== 'undefined') {  jQuery( document ).ready(function($) {";
			echo "\r\n";
			echo "window.sbyCustomJS = function(){";
			echo "\r\n";
			echo stripslashes($js);
			echo "\r\n";
			echo "}";
			echo "\r\n";
			echo "})};";
		}

		echo "\r\n";
		echo '</script>';
		echo "\r\n";
	}

	public function sby_custom_css() {
		global $sby_settings;

		$css = '';

		$css = isset( $sby_settings['custom_css'] ) ? trim( $sby_settings['custom_css'] ) : '';

		//Show CSS if an Admin (so can see Hide Photos link), if including Custom CSS or if hiding some photos
		if ( current_user_can( 'manage_youtube_feed_options' ) || current_user_can( 'manage_options' ) ||  ! empty( $css ) ) {

			echo '<!-- YouTube Feeds CSS -->';
			echo "\r\n";
			echo '<style type="text/css">';

			if ( ! empty( $css ) ){
				echo "\r\n";
				echo stripslashes($css);
			}

			if ( current_user_can( 'manage_youtube_feed_options' ) || current_user_can( 'manage_options' ) ){
				echo "\r\n";
				echo "#sby_mod_link, #sby_mod_error{ display: block !important; width: 100%; float: left; box-sizing: border-box; }";
			}

			echo "\r\n";
			echo '</style>';
			echo "\r\n";
		}

	}
	/**
	 * Makes the JavaScript file available and enqueues the stylesheet
	 * for the plugin
	 */
	public function sby_scripts_enqueue( $sby_settings ) {
		if ( ! doing_action( "sby_enqueue_scripts" ) && ! is_singular( SBY_CPT ) ) {
			return;
		}

		$database_settings = sby_get_database_settings();
		$global_settings      = ( new SBY_Settings( [], $database_settings ) )->get_settings();

		if ( ! is_array( $sby_settings ) ) {
			$sby_settings = $global_settings;
		}

		//Register the script to make it available
		$assets_url = trailingslashit( SBY_PLUGIN_URL );
		$js_file = $assets_url . 'js/sb-youtube.min.js';

		if ( isset( $_GET['sb_debug'] ) ) {
			$js_file = $assets_url . 'js/sb-youtube-debug.js';
		}

		if(!Util::isProduction()) {
			$js_file = 'http://localhost:9005/sb-youtube.min.js';
		}

		$enqueue_in_head = isset($global_settings['enqueue_js_in_head']) ? $global_settings['enqueue_js_in_head'] : false;
		wp_register_script( 'sby_scripts', $js_file, array('jquery'), SBYVER, !$enqueue_in_head );

		$css_free_file_name = 'sb-youtube-free.min.css';
		$css_pro_file_name = 'sb-youtube.min.css';
		$css_file_name = sby_is_pro() ? $css_pro_file_name : $css_free_file_name;


		if ( !empty( $sby_settings['enqueue_css_in_shortcode'] ) ) {
			wp_register_style( 'sby_styles', trailingslashit( SBY_PLUGIN_URL ) . 'css/' . $css_file_name, array(), SBYVER );
		} else {
			wp_enqueue_style( 'sby_styles', trailingslashit( SBY_PLUGIN_URL ) . 'css/' . $css_file_name, array(), SBYVER );
		}

		$data = array(
			'isAdmin' => is_admin(),
			'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
			'placeholder' => trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png',
			'placeholderNarrow' => trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder-narrow.png',
			'lightboxPlaceholder' => trailingslashit( SBY_PLUGIN_URL ) . 'img/lightbox-placeholder.png',
			'lightboxPlaceholderNarrow' => trailingslashit( SBY_PLUGIN_URL ) . 'img/lightbox-placeholder-narrow.png',
			'autoplay' => $sby_settings['playvideo'] === 'automatically',
			'semiEagerload' => $sby_settings['eagerload'],
			'eagerload' => false,
			'nonce'	=> wp_create_nonce( 'sby_nonce' ),
			'isPro'	=> sby_is_pro(),
			'isCustomizer' => \sby_doing_customizer( $sby_settings )
		);
		//Pass option to JS file
		wp_localize_script('sby_scripts', 'sbyOptions', $data );
		wp_enqueue_style( 'sby_styles' );
		wp_enqueue_script( 'sby_scripts' );
	}
}
