<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;


use Smashballoon\Stubs\Services\ServiceProvider;

class GUIService extends ServiceProvider {
	public function register() {
		add_action( 'wp_ajax_sby_dismiss_api_key_notice', [$this, 'sby_dismiss_api_key_notice'] );
		add_action( 'wp_ajax_sby_dismiss_at_warning_notice', [$this, 'sby_dismiss_at_warning_notice'] );
		add_action( 'wp_ajax_sby_dismiss_connect_warning_button', [$this, 'sby_dismiss_connect_warning_notice'] );
		add_action( 'admin_footer', [$this, 'sby_access_token_warning_modal'], 1 );
		add_action('admin_init', [$this, 'sby_nag_ignore']);
		add_action( 'admin_print_scripts', [$this, 'sby_admin_hide_unrelated_notices'] );
	}

	public function sby_dismiss_api_key_notice() {

		update_user_meta( get_current_user_id(), 'sby_api_key_notice', 'dismissed' );

		die();
	}
	public function sby_dismiss_at_warning_notice() {

		update_user_meta( get_current_user_id(), 'sby_at_warning_notice', time() );

		die();
	}
	public function sby_dismiss_connect_warning_notice() {

		update_user_meta( get_current_user_id(), 'sby_connect_warning_notice', time() );

		die();
	}
	public function sby_access_token_warning_modal() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === SBY_SLUG && isset( $_GET['sby_access_token'] ) && sby_notice_not_dismissed() ) {
			$text_domain = SBY_TEXT_DOMAIN;
			include_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/templates/modal.php';
			echo '<span class="sby_account_just_added"></span>';
		}

	}
	public function sby_nag_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		if ( isset($_GET['sby_nag_ignore']) && '0' == $_GET['sby_nag_ignore'] ) {
			add_user_meta($user_id, 'sby_ignore_notice', 'true', true);
		}
	}
	/**
	 * Remove non-WPForms notices from WPForms pages.
	 *
	 * @since 1.3.9
	 */
	public function sby_admin_hide_unrelated_notices() {

		// Bail if we're not on a Sby screen or page.
		if ( ! sby_is_admin_page() ) {
			return;
		}

		// Extra banned classes and callbacks from third-party plugins.
		$blacklist = array(
			'classes'   => array(),
			'callbacks' => array(
				'sbydb_admin_notice', // 'Database for Sby' plugin.
			),
		);

		global $wp_filter;

		foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
			if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
				continue;
			}
			foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( $arr['function'] instanceof \Closure ) {
						unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
						continue;
					}
					$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
					if (
						! empty( $class ) &&
						strpos( $class, 'sby' ) !== false &&
						! in_array( $class, $blacklist['classes'], true )
					) {
						continue;
					}
					if (
						! empty( $name ) && (
							strpos( $name, 'sby' ) === false ||
							in_array( $class, $blacklist['classes'], true ) ||
							in_array( $name, $blacklist['callbacks'], true )
						)
					) {
						unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}
	}
}