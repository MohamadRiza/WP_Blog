<?php

// composer autoload
require_once __DIR__ . '/vendor/autoload.php';

if (!defined('SBY_DBVERSION')) {
    define('SBY_DBVERSION', '1.4');
}

if (!defined('SBY_UPLOADS_NAME')) {
// Upload folder name for local image files for posts
    define('SBY_UPLOADS_NAME', 'sby-local-media');
}


if (!defined('SBY_ITEMS')) {
    // Name of the database table that contains instagram posts
    define('SBY_ITEMS', 'sby_items');
}

if (!defined('SBY_ITEMS_FEEDS')) {
    // Name of the database table that contains feed ids and the ids of posts
    define('SBY_ITEMS_FEEDS', 'sby_items_feeds');
}
if (!defined('SBY_CPT')) {
    // Name of the database table that contains feed ids and the ids of posts
    define('SBY_CPT', 'sby_videos');
}

if (!defined('SBY_PLUGIN_DIR')) {
    // Plugin Folder Path.
    define('SBY_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('SBY_PLUGIN_URL')) {
    // Plugin Folder URL.
    define('SBY_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('SBY_MINIMUM_WALL_VERSION')) {
    define('SBY_MINIMUM_WALL_VERSION', '1.0');
}
if (!defined('SBY_FEED_LOCATOR')) {
    define('SBY_FEED_LOCATOR', 'sby_feed_locator');
}

if (!defined('CUSTOMIZER_ABSPATH')) {
    define('CUSTOMIZER_ABSPATH', __DIR__ . '/vendor/smashballoon/customizer/');
}

if (!defined('CUSTOMIZER_PLUGIN_URL')) {
    define('CUSTOMIZER_PLUGIN_URL', plugin_dir_url(__DIR__ . '/vendor/smashballoon/customizer/bootstrap.php'));
}

//Load .env variables
if (class_exists('Dotenv\Dotenv') && method_exists('Dotenv\Dotenv', 'createImmutable')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}