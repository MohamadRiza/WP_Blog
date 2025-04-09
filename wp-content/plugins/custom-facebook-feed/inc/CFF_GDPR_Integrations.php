<?php
/**
 * Class CFF_GDPR_Integrations
 *
 * Adds GDPR related workarounds for third-party plugins:
 * https://wordpress.org/plugins/cookie-law-info/
 *
 * @since 2.6/3.17
 */
namespace CustomFacebookFeed;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


class CFF_GDPR_Integrations {

	/**
	 * Nothing currently for CFF
	 *
	 * @since 2.6/3.17
	 */
	public static function init() {
		add_filter( 'wt_cli_third_party_scripts', array( 'CFF_GDPR_Integrations', 'undo_script_blocking' ), 11 );
	}

	/**
	 * Prevents changes made to how JavaScript file is added to
	 * pages.
	 *
	 * @param array $blocking
	 *
	 * @return array
	 *
	 * @since 2.6/3.17
	 */
	public static function undo_script_blocking( $blocking ) {
		$options = get_option( 'cff_style_settings', array() );
		if ( ! CFF_GDPR_Integrations::doing_gdpr( $options ) ) {
			return $blocking;
		}
		remove_filter( 'wt_cli_third_party_scripts', 'wt_cli_facebook_feed_script' );

		return $blocking;
	}

	/**
	 * Whether or not consent plugins that Custom Facebook Feed
	 * is compatible with are active.
	 *
	 * @return bool|string
	 *
	 * @since 2.6/3.17
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
		if ( defined( 'CKY_APP_ASSETS_URL' ) ) {
			return 'CookieYes | GDPR Cookie Consent by CookieYes';
		}
		if ( class_exists( 'Cookiebot_WP' ) ) {
			return 'Cookiebot by Cybot A/S';
		}
		if ( class_exists( 'COMPLIANZ' ) ) {
			return 'Complianz by Really Simple Plugins';
		}
		if (function_exists('BorlabsCookieHelper')  || ( defined('BORLABS_COOKIE_VERSION') && version_compare(BORLABS_COOKIE_VERSION, '3.0', '>=') )) {
			return 'Borlabs Cookie by Borlabs';
		}
		if ( is_admin() && ! empty( $_GET['page'] ) && $_GET['page'] === 'cff-feed-builder' ) {
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
	 *
	 * @since 2.6/3.17
	 */
	public static function doing_gdpr( $settings ) {
		$gdpr = isset( $settings['gdpr'] ) ? $settings['gdpr'] : 'auto';
		if ( $gdpr === 'no' ) {
			return false;
		}
		if ( $gdpr === 'yes' ) {
			return true;
		}
		return (CFF_GDPR_Integrations::gdpr_plugins_active() !== false);
	}

	


	/**
	 * No tests needed in free version
	 *
	 * @param bool $retest
	 *
	 * @return bool
	 *
	 * @since 1.7/1.12
	 */
	public static function gdpr_tests_successful( $retest = false ) {
		return true;
	}

	/**
	 * No tests needed in free version
	 *
	 * @return array
	 *
	 * @since 1.7/1.12
	 */
	public static function gdpr_tests_error_message() {
		return array();
	}

	

}