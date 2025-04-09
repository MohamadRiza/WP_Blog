<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('SBR_DBVERSION')) {
	define('SBR_DBVERSION', '1.5');
}

if (!defined('SBR_MENU_SLUG')) {
	define('SBR_MENU_SLUG', 'sbr');
}

if (!defined('SBR_SLUG')) {
	define('SBR_SLUG', 'sbr');
}

if (!defined('SBR_PLUGIN_URL')) {
	define('SBR_PLUGIN_URL', plugin_dir_url(__FILE__));
}


if (!defined('SBR_REST_DOMAIN')) {
	define('SBR_REST_DOMAIN', 'SBR/v1');
}

// Common Library Assets URL
if (!defined('SB_COMMON_ASSETS')) {
	define('SB_COMMON_ASSETS', plugin_dir_url(__FILE__) . 'vendor/smashballoon/customizer/sb-common/');
}

// Common Library Assets URL
if (!defined('SB_COMMON_ASSETS_DIR')) {
	define('SB_COMMON_ASSETS_DIR', __DIR__ . '/vendor/smashballoon/customizer/sb-common/');
}

// Customizer Assets URL
if (!defined('SB_CUSTOMIZER_ASSETS')) {
	define('SB_CUSTOMIZER_ASSETS', plugin_dir_url(__FILE__) . 'vendor/smashballoon/customizer/sb-common/sb-customizer');
}

if (!defined('SB_CUSTOMIZER_COMMON_ASSETS')) {
	define('SB_CUSTOMIZER_COMMON_ASSETS', plugin_dir_url(__FILE__) . 'vendor/smashballoon/customizer/sb-common/assets/');
}

//Customizer Tabs Path
if (!defined('SBR_CUSTOMIZER_TABS_PATH')) {
	define('SBR_CUSTOMIZER_TABS_PATH', __DIR__ . '/class/Common/Customizer/Tabs/');
}

//Customizer Tabs Name Space
if (!defined('SBR_CUSTOMIZER_TABS_NAMESPACE')) {
	define('SBR_CUSTOMIZER_TABS_NAMESPACE',  'SmashBalloon\Reviews\Common\Customizer\Tabs\\');
}

//Settings Page Tabs Path
if (!defined('SBR_SETTINGSPAGE_TABS_PATH')) {
	define('SBR_SETTINGSPAGE_TABS_PATH', __DIR__ . '/class/Common/Settings/Tabs/');
}

//Settings Page Tabs Name Space
if (!defined('SBR_SETTINGSPAGE_TABS_NAMESPACE')) {
	define('SBR_SETTINGSPAGE_TABS_NAMESPACE',  'SmashBalloon\Reviews\Common\Settings\Tabs\\');
}


//Relay Backend Name Space
if (!defined('SBR_RELAY_BASE_URL')) {
 	define('SBR_RELAY_BASE_URL', 'https://reviews.smashballoon.com/api/v1.0/');
}

if (!defined('SBR_CONNECT_SITE_URL')) {
	define('SBR_CONNECT_SITE_URL', 'https://connect.smashballoon.com/auth/rv/');
}


if (!defined('SBR_CRON_UPDATE_CACHE_TIME')) {
	define('SBR_CRON_UPDATE_CACHE_TIME', 60 * 60 * 24 * 60);
}

//Feed Locator
if ( !defined('SBR_FEED_LOCATOR')) {
	define('SBR_FEED_LOCATOR', 'sbr_feed_locator');
}

//Feed Table
if (!defined('SBR_FEEDS_TABLE')) {
	define('SBR_FEEDS_TABLE', 'sbr_feeds');
}

//Feed Sources
if (!defined('SBR_SOURCES_TABLE')) {
	define('SBR_SOURCES_TABLE', 'sbr_sources');
}

//Feed Caches
if (!defined('SBR_FEED_CACHES_TABLE')) {
	define('SBR_FEED_CACHES_TABLE', 'sbr_feed_caches');
}

//Reviews Post Table
if (!defined('POSTS_TABLE_NAME')) {
	define('POSTS_TABLE_NAME', 'sbr_reviews_posts');
}

//Feed Posts
if (!defined('SBR_POSTS_TABLE')) {
	define('SBR_POSTS_TABLE', 'sbr_reviews_posts');
}

//Menu Slug
if (!defined('SBR_CUSTOMIZER_MENU_SLUG')) {
	define('SBR_CUSTOMIZER_MENU_SLUG', 'sbr');
}

// Identify plugin is in production mode
if (!defined('SBR_PRODUCTION')) {
	define('SBR_PRODUCTION', true);
}
if (!defined('SBR_REFRESH_THRESHOLD_OFFSET')) {
	define('SBR_REFRESH_THRESHOLD_OFFSET', 40 * 86400);
}
if (!defined('SBR_MINIMUM_INTERVAL')) {
	define('SBR_MINIMUM_INTERVAL', 600);
}
if ( ! defined( 'SBR_STORE_URL' ) ) {
	define( 'SBR_STORE_URL', 'https://smashballoon.com/' );
}

if (!defined('SBR_FB_CONNECT_URL')){
    define('SBR_FB_CONNECT_URL', 'https://connect.smashballoon.com/auth/fb/');
}

require_once trailingslashit(SBR_PLUGIN_DIR) . 'vendor/autoload.php';
require_once trailingslashit(SBR_PLUGIN_DIR) . 'class/sbr-functions.php';



//Customizer container config
$customizerContainer = \Smashballoon\Customizer\V2\Container::getInstance();
$customizerContainer->set(\Smashballoon\Customizer\V2\Config\Proxy::class, new \SmashBalloon\Reviews\Common\Builder\Config\Proxy());
$serviceContainerClass = SmashBalloon\Reviews\Common\Util::sbr_is_pro() ? \SmashBalloon\Reviews\Pro\ServiceContainer::class : \SmashBalloon\Reviews\Common\ServiceContainer::class;
$commonServiceContainer = \SmashBalloon\Reviews\Common\Container::get_instance()->get($serviceContainerClass)->register();
