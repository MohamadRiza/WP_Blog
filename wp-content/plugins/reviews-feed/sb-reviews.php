<?php
/*
Plugin Name: Reviews Feed
Plugin URI: https://smashballoon.com/reviews-feed
Description: Reviews Feeds allows you to display completely customizable Reviews feeds from many different providers.
Version: 2.1.0
Author: Smash Balloon
Author URI: https://smashballoon.com/
Text Domain: reviews-feed
Domain Path: /languages
*/

/*
Copyright 2024  Smash Balloon LLC (email : hey@smashballoon.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!defined('SBRVER')) {
	define('SBRVER', '2.1.0');
}

if (!defined('SBR_PLUGIN_DIR')) {
	define('SBR_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('SBR_LITE')) {
	define('SBR_LITE', true);
}

if (!defined('SBR_PLUGIN_BASENAME')) {
	define('SBR_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

require_once trailingslashit(SBR_PLUGIN_DIR) . 'bootstrap.php';
