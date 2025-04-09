<?php

/**
 * Plugin Name: Feeds for TikTok (TikTok feed, video, and gallery plugin)
 * Plugin URI: https://smashballoon.com/tiktok-feeds/
 * Description: Add TikTok feeds to your website.
 * Version: 1.1.0
 * Author: Smash Balloon
 * Author URI: https://smashballoon.com/
 * License: GPLv2 or later
 * Text Domain: feeds-for-tiktok
 * Domain Path: /languages
 * Requires at least: 5.2
 * Requires PHP: 7.4
 */

/*
Copyright 2024  Smash Balloon  (email: hey@smashballoon.com)
This program is paid software; you may not redistribute it under any
circumstances without the expressed written consent of the plugin author.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

if (! defined('ABSPATH')) {
	exit;
}

if (! defined('SBTT_PLUGIN_NAME')) {
	define('SBTT_PLUGIN_NAME', 'Feeds for TikTok');
}

if (! defined('SBTTVER')) {
	define('SBTTVER', '1.1.0');
}

if (! defined('SBTT_PLUGIN_FILE')) {
	define('SBTT_PLUGIN_FILE', __FILE__);
}

if (! defined('SBTT_PLUGIN_DIR')) {
	define('SBTT_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (! defined('SBTT_LITE')) {
	define('SBTT_LITE', true);
}

if (! defined('SBTT_PLUGIN_BASENAME')) {
	define('SBTT_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

require_once trailingslashit(SBTT_PLUGIN_DIR) . 'bootstrap.php';
