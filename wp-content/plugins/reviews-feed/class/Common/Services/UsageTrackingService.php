<?php
/**
 * Tracking functions for reporting plugin usage to the Smash Balloon site for users that have opted in
 *
 * @copyright   Copyright (c) 2018, Chris Christoff
 * @since       5.6
 */

// Exit if accessed directly
namespace SmashBalloon\Reviews\Common\Services;

use Smashballoon\Framework\Utilities\UsageTracking;
use SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver;
use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\Util;
use SmashBalloon\Reviews\Pro\FeedCache;
use Smashballoon\Stubs\Services\ServiceProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Usage tracking
 *
 * @access public
 * @since  5.6
 * @return void
 */
class UsageTrackingService extends ServiceProvider {

	/**
	 * @var DB
	 */
	private $DB;

	public function __construct(DB $DB) {
		$this->DB = $DB;
	}
	public function register() {
		add_action( 'init', array( $this, 'schedule_send' ) );
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_filter( 'sb_usage_tracking_data', array( $this, 'filter_usage_tracking_data' ), 10, 2 );
		add_action( 'sbr_usage_tracking_cron', array( $this, 'send_checkin' ) );
	}

	public function get_data() {
		$data = array();

		// Retrieve current theme info
		$theme_data = wp_get_theme();

		$count_b = 1;
		if ( is_multisite() ) {
			if ( function_exists( 'get_blog_count' ) ) {
				$count_b = get_blog_count();
			} else {
				$count_b = 'Not Set';
			}
		}

		$php_version = rtrim( ltrim( sanitize_text_field( phpversion() ) ) );
		$php_version = ! empty( $php_version ) ? substr( $php_version, 0, strpos( $php_version, '.', strpos( $php_version, '.' ) + 1 ) ) : phpversion();

		global $wp_version;
		$data['this_plugin']    = 'sbr';
		$data['php_version']    = $php_version;
		$data['mi_version']     = SBRVER;
		$data['wp_version']     = $wp_version;
		$data['server']         = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';
		$data['multisite']      = is_multisite();
		$data['url']            = home_url();
		$data['themename']      = $theme_data->Name;
		$data['themeversion']   = $theme_data->Version;
		$data['settings']       = array();
		$data['pro']            = (int) Util::sbr_is_pro();
		$data['sites']          = $count_b;
		$data['usagetracking']  = get_option( 'sbr_usage_tracking_config', false );
		$num_users              = function_exists( 'count_users' ) ? count_users() : 'Not Set';
		$data['usercount']      = is_array( $num_users ) ? $num_users['total_users'] : 1;
		$data['timezoneoffset'] = date( 'P' );

		$settings_to_send = array();
		$raw_settings     = get_option( 'sbr_settings', array() );
		$feeds = DB::get_feeds_list();
		$feed_settings = [];

		$settings_to_send           = array_merge($settings_to_send, $feed_settings);
		$con_bus_accounts           = 0;
		$recently_searched_hashtags = 0;
		$access_tokens_tried        = array();


		$sbr_cache = new FeedCache('');

		$settings_to_send['num_found_feed_caches']     = (int)$sbr_cache->get_cache_count();
		$settings_to_send['recently_requested_caches'] = (int)$sbr_cache->get_cache_count(true);

		$settings_to_send['num_found_feeds']                = count($feeds);

		$data['settings'] = $settings_to_send;

		// Retrieve current plugin information
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins         = get_plugins();
		$active_plugins  = get_option( 'active_plugins', array() );
		$plugins_to_send = array();

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}

			$plugins_to_send[] = $plugin['Name'];
		}

		$data['active_plugins'] = $plugins_to_send;
		$data['locale']         = get_locale();

		return $data;
	}

	public function send_checkin( $override = false ) {

		$home_url = trailingslashit( home_url() );
		if ( strpos( $home_url, 'smashballoon.com' ) !== false ) {
			return false;
		}

		if ( ! $this->tracking_allowed() && ! $override ) {
			return false;
		}

		return UsageTracking::send_usage_update($this->get_data(), 'sbr');
	}

	private function tracking_allowed() {
		$settings = sbr_get_database_settings();

		return ( isset( $settings['usagetracking'] ) && true === $settings['usagetracking'] );
	}

	public function schedule_send() {
		if ( ! wp_next_scheduled( 'sbr_usage_tracking_cron' ) ) {
			$tracking           = array();
			$tracking['day']    = rand( 0, 6 );
			$tracking['hour']   = rand( 0, 23 );
			$tracking['minute'] = rand( 0, 59 );
			$tracking['second'] = rand( 0, 59 );
			$tracking['offset'] = ( $tracking['day'] * DAY_IN_SECONDS ) +
									( $tracking['hour'] * HOUR_IN_SECONDS ) +
									( $tracking['minute'] * MINUTE_IN_SECONDS ) +
									$tracking['second'];
			$last_sunday        = strtotime( 'next sunday' ) - ( 7 * DAY_IN_SECONDS );
			if ( ( $last_sunday + $tracking['offset'] ) > time() + 6 * HOUR_IN_SECONDS ) {
				$tracking['initsend'] = $last_sunday + $tracking['offset'];
			} else {
				$tracking['initsend'] = strtotime( 'next sunday' ) + $tracking['offset'];
			}

			wp_schedule_event( $tracking['initsend'], 'weekly', 'sbr_usage_tracking_cron' );
			update_option( 'sbr_usage_tracking_config', $tracking );
		}
	}

	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'reviews-feed' ),
		);
		return $schedules;
	}

	/**
	 * Filter the usage tracking data
	 *
	 * @param array $data
	 * @param string $plugin_slug
	 *
	 * @handles sb_usage_tracking_data
	 *
	 * @return array|mixed
	 */
	public function filter_usage_tracking_data( $data, $plugin_slug ) {
		if ( 'sbr' !== $plugin_slug ) {
			return $data;
		}

		if ( ! is_array( $data ) ) {
			return $data;
		}

		if ( ! isset( $data['settings'] ) ) {
			$data['settings'] = [];
		}

		$tracked_boolean_settings = explode( ',',
			'contentLength,carouselIntervalTime,carouselShowArrows,carouselShowPagination,carouselEnableAutoplay,showHeader,
			headerContent,headerPadding,headerMargin,headerHeadingContent,headingFont,headingColor,headerHeadingPadding,
			headerHeadingMargin,headerButtonLinkTo,headerButtonIcon,headerButtonExternalLink,headerButtonFont,headerButtonColor,
			headerButtonBg,headerButtonHoverColor,headerButtonHoverBg,headerButtonPadding,headerButtonMargin,headerAvReviewFont,
			headerAvSubtextReviewFont,headerAvReviewIconColor,headerAvReviewColor,headerAvReviewSubtextColor,headerAvReviewMargin,
			headerAvReviewPadding,boxedBackgroundColor,boxedBoxShadow,boxedBorderRadius,postStroke,postPadding,postElements,ratingIconSize,
			ratingIconColor,ratingIconPadding,ratingIconMargin,paragraphFont,paragraphColor,paragraphPadding,paragraphMargin,authorContent,
			authorPadding,authorMargin,authorNameFont,authorNameColor,authorNamePadding,authorNameMargin,dateFont,dateColor,dateFormat,
			dateCustomFormat,dateBeforeText,dateAfterText,datePadding,dateMargin,authorImageBorderRadius,authorImageMargin,showLoadButton,
			loadButtonText,loadButtonFont,loadButtonColor,loadButtonHoverColor,loadButtonBg,loadButtonHoverBg,loadButtonPadding,loadButtonMargin,
			includedStarFilters,includeWords,excludeWords,filterByImage,filterByVideos,sortByDateEnabled,sortByRatingEnabled,sortByRating,
			sortRandomEnabled,colorScheme,moderationEnabled,moderationType,moderationAllowList,moderationBlockList,localization'
		);

		$tracked_string_settings = explode( ',',
			'feedTemplate,layout,verticalSpacing,horizontalSpacing,contentAlignment,numPostDesktop,numPostTablet,
			numPostMobile,gridDesktopColumns,gridTabletColumns,gridMobileColumns,masonryDesktopColumns,masonryTabletColumns,
			masonryMobileColumns,carouselDesktopColumns,carouselTabletColumns,carouselMobileColumns,carouselDesktopRows,
			carouselTabletRows,carouselMobileRows,carouselLoopType,sortByDate,filterCharCountMin,filterCharCountMax'
		);

		$feeds = DB::get_feeds_list();
		$settings_defaults = sbr_settings_defaults();

		// Track settings of the first feed
		if ( ! empty( $feeds ) ) {
			$feed = $feeds[0];
			$feed_settings = ( new SBR_Feed_Saver( $feed['id'] ) )->get_feed_settings();

			if(!is_array($feed_settings)) {
				return $data;
			}

			$booleans = UsageTracking::tracked_settings_to_booleans($tracked_boolean_settings, $settings_defaults, $feed_settings);
			$strings = UsageTracking::tracked_settings_to_strings($tracked_string_settings, $feed_settings);

			if ( is_array( $booleans ) && is_array( $strings ) ) {
				$data['settings'] = array_merge( $data['settings'], $booleans, $strings );
			}
		}

		return $data;
	}
}
