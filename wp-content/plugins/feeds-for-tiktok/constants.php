<?php

/**
 * Define all the constants in the plugin for easier management from one place.
 *
 * @package tiktok-feeds
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! defined('SBTT_PLUGIN_URL')) {
	define('SBTT_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (! defined('SBTT_DBVERSION')) {
	define('SBTT_DBVERSION', '1.0');
}

if (! defined('SBTT_MENU_SLUG')) {
	define('SBTT_MENU_SLUG', 'sbtt');
}

if (! defined('SBTT_SLUG')) {
	define('SBTT_SLUG', 'sbtt');
}

if (! defined('SBTT_CONNECT_URL')) {
	define('SBTT_CONNECT_URL', 'https://connect.smashballoon.com/auth/tt/');
}

if (! defined('SBTT_API_BASE_URL')) {
	define('SBTT_API_BASE_URL', 'https://tiktok.smashballoon.com/api/v1.0/');
}

// Identify plugin is in production mode.
if (! defined('SBTT_PRODUCTION')) {
	define('SBTT_PRODUCTION', true);
}

/**
 * Database tables constants.
 */
if (! defined('SBTT_FEED_LOCATOR')) {
	define('SBTT_FEED_LOCATOR', 'sbtt_feed_locator');
}

if (! defined('SBTT_FEEDS_TABLE')) {
	define('SBTT_FEEDS_TABLE', 'sbtt_feeds');
}

if (! defined('SBTT_SOURCES_TABLE')) {
	define('SBTT_SOURCES_TABLE', 'sbtt_sources');
}

if (! defined('SBTT_FEED_CACHES_TABLE')) {
	define('SBTT_FEED_CACHES_TABLE', 'sbtt_feed_caches');
}

if (! defined('SBTT_TIKTOK_POSTS_TABLE')) {
	define('SBTT_TIKTOK_POSTS_TABLE', 'sbtt_tiktok_posts');
}

if (! defined('SBTT_UPLOAD_FOLDER_NAME')) {
	define('SBTT_UPLOAD_FOLDER_NAME', 'sb-tiktok-feeds-images');
}

// Common Library Assets URL.
if (! defined('SBTT_COMMON_ASSETS')) {
	define('SBTT_COMMON_ASSETS', plugin_dir_url(__FILE__) . 'vendor/smashballoon/customizer/sb-common/');
}

// Common Library Assets URL.
if (! defined('SBTT_COMMON_ASSETS_DIR')) {
	define('SBTT_COMMON_ASSETS_DIR', __DIR__ . '/vendor/smashballoon/customizer/sb-common/');
}

// Customizer Assets URL.
if (! defined('SBTT_CUSTOMIZER_ASSETS')) {
	define('SBTT_CUSTOMIZER_ASSETS', plugin_dir_url(__FILE__) . 'vendor/smashballoon/customizer/sb-common/sb-customizer');
}

if (! defined('SBTT_CUSTOMIZER_COMMON_ASSETS')) {
	define('SBTT_CUSTOMIZER_COMMON_ASSETS', plugin_dir_url(__FILE__) . 'vendor/smashballoon/customizer/sb-common/assets/');
}

// Customizer Tabs Path.
if (! defined('SBTT_CUSTOMIZER_TABS_PATH')) {
	define('SBTT_CUSTOMIZER_TABS_PATH', __DIR__ . '/inc/Common/Customizer/Tabs/');
}

// Customizer Tabs Name Space.
if (! defined('SBTT_CUSTOMIZER_TABS_NAMESPACE')) {
	define('SBTT_CUSTOMIZER_TABS_NAMESPACE', 'SmashBalloon\TikTokFeeds\Common\Customizer\Tabs\\');
}

// Settings Page Tabs Path.
if (! defined('SBTT_SETTINGSPAGE_TABS_PATH')) {
	define('SBTT_SETTINGSPAGE_TABS_PATH', __DIR__ . '/inc/Common/Settings/Tabs/');
}

// Settings Page Tabs Name Space.
if (! defined('SBTT_SETTINGSPAGE_TABS_NAMESPACE')) {
	define('SBTT_SETTINGSPAGE_TABS_NAMESPACE', 'SmashBalloon\TikTokFeeds\Common\Settings\Tabs\\');
}
