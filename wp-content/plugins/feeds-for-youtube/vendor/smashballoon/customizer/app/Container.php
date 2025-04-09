<?php

namespace Smashballoon\Customizer;

/** @internal */
class Container
{
    public static $container;
    /**
     * @return \DI\Container
     */
    public static function getInstance()
    {
        if (self::$container === null) {
            self::$container = (new \SmashBalloon\YoutubeFeed\Vendor\DI\ContainerBuilder())->build();
        }
        return self::$container;
    }
}
