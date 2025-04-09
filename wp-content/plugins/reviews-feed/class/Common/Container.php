<?php

namespace SmashBalloon\Reviews\Common;

use Smashballoon\Customizer\V2\Config;
use Smashballoon\Customizer\V2\DB;
use Smashballoon\Customizer\V2\PreviewProvider;
use Smashballoon\Customizer\V2\ProxyProvider;
use SmashBalloon\Reviews\Common\Customizer\ShortcodePreviewProvider;
use Smashballoon\Stubs\Traits\Singleton;


class Container {
    use Singleton;


    /**
     * @return \DI\Container
     */
    public static function get_instance() {
        if(null === self::$instance) {
            self::$instance = ( new \SmashBalloon\Reviews\Vendor\DI\ContainerBuilder() )->build();

            self::$instance->set(Config::class , new \SmashBalloon\Reviews\Common\Customizer\Config());
            self::$instance->set(DB::class , new \SmashBalloon\Reviews\Common\Customizer\DB());
            self::$instance->set(ProxyProvider::class , new \SmashBalloon\Reviews\Common\Customizer\ProxyProvider());
            self::$instance->set(PreviewProvider::class , new ShortcodePreviewProvider());
            self::$instance->set(SBR_Settings::class , new SBR_Settings([], sbr_get_database_settings()));

        }
        return self::$instance;
    }
}