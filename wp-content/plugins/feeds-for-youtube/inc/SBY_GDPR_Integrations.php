<?php
/**
 * Class SBY_GDPR_Integrations
 *
 * Adds GDPR related workarounds for third-party plugins:
 * https://wordpress.org/plugins/cookie-law-info/
 *
 * @since 2.6/5.9
 */

namespace SmashBalloon\YouTubeFeed;
class SBY_GDPR_Integrations {

	/**
	 * Undoing of Cookie Notice's Twitter Feed related code
	 * needs to be done late.
	 */
	public static function init() {
	}

	/**
	 * Whether or not consent plugins that Twitter Feed
	 * is compatible with are active.
	 *
	 * @return bool|string
	 */
	public static function gdpr_plugins_active() {
		if ( function_exists( 'WPConsent' ) ) {
			return 'WPConsent by the WPConsent team';
		}
		if ( defined( 'RCB_ROOT_SLUG' ) ) {
			return 'Real Cookie Banner by devowl.io';
		}
		if ( function_exists( 'gdpr_cookie_is_accepted' ) ) {
			return 'GDPR Cookie Compliance by Moove Agency';
		}
		if ( class_exists( 'Cookie_Notice' ) ) {
			return 'Cookie Notice by dFactory';
		}
		if ( function_exists( 'run_cookie_law_info' ) || class_exists( 'Cookie_Law_Info' ) ) {
			return 'GDPR Cookie Consent by WebToffee';
		}
		if ( class_exists( 'Cookiebot_WP' ) ) {
			return 'Cookiebot by Cybot A/S';
		}
		if ( class_exists( 'COMPLIANZ' ) ) {
			return 'Complianz by Really Simple Plugins';
		}
		if ( function_exists( 'BorlabsCookieHelper' ) || ( defined( 'BORLABS_COOKIE_VERSION' ) && version_compare( BORLABS_COOKIE_VERSION, '3.0', '>=' ) ) ) {
			return 'Borlabs Cookie by Borlabs';
		}
		if ( is_admin() && ! empty( $_GET['page'] ) && $_GET['page'] === 'sby-feed-builder' ) {
			return false;
		}
		return false;
	}

	/**
	 * GDPR features can be added automatically, forced enabled,
	 * or forced disabled.
	 *
	 * @param $settings
	 *
	 * @return bool
	 */
	public static function doing_gdpr( $settings ) {
		$gdpr = isset( $settings['global_settings']['gdpr'] ) ? $settings['global_settings']['gdpr'] : 'auto';
		if ( $gdpr === 'no' ) {
			return false;
		}
		if ( $gdpr === 'yes' ) {
			return true;
		}
		return (SBY_GDPR_Integrations::gdpr_plugins_active() !== false);
	}

	/**
	 * GDPR features are reliant on the image resizing features
	 *
	 * @param bool $retest
	 *
	 * @return bool
	 */
	public static function gdpr_tests_successful( $retest = false ) {
		return true;
	}

	public static function gdpr_tests_error_message() {
		return '';
	}

	/**
	 * @return array|mixed
	 *
	 * @since 2.0
	 */
	public static function statuses() {
		$statuses_option = get_option( 'sby_statuses', array() );

		$return = isset( $statuses_option['gdpr'] ) ? $statuses_option['gdpr'] : array();
		return $return;
	}

}
