<?php

namespace Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

/** @internal */
class Flywheel implements \Smashballoon\Framework\Utilities\PlatformTracking\Platforms\PlatformInterface
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        add_filter('sb_hosting_platform', [$this, 'filter_sb_hosting_platform']);
    }
    /**
     * @inheritDoc
     */
    public function filter_sb_hosting_platform($platform)
    {
        if (\defined('FLYWHEEL_CONFIG_DIR')) {
            $platform = 'flywheel';
        }
        return $platform;
    }
}
