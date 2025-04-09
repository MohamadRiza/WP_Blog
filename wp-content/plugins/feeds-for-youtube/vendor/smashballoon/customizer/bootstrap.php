<?php

namespace SmashBalloon\YoutubeFeed\Vendor;

use Smashballoon\Customizer\Container;
use Smashballoon\Customizer\Tabs\Manager;
\define('SBCVER', 2.0);
if (!\defined('CUSTOMIZER_ABSPATH')) {
    \define('CUSTOMIZER_ABSPATH', \trailingslashit(__DIR__));
}
if (!\defined('CUSTOMIZER_PLUGIN_URL')) {
    \define('CUSTOMIZER_PLUGIN_URL', \plugin_dir_url(__FILE__));
}
//initialize container
$container = Container::getInstance();
//Setting tabs manager singleton
$container->set(Manager::class, Manager::getInstance());
//Load .env variables
if (\class_exists('SmashBalloon\\YoutubeFeed\\Vendor\\Dotenv\\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    if (\is_file(__DIR__ . '/.env')) {
        $dotenv->safeLoad();
    }
}
