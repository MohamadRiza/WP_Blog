<?php

namespace Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

/** @internal */
interface PlatformInterface
{
    /**
     * Register the platform hooks.
     *
     * @return void
     */
    public function register();
    /**
     * Filter the hosting platform.
     *
     * @param string $platform
     *
     * @return string
     */
    public function filter_sb_hosting_platform($platform);
}
