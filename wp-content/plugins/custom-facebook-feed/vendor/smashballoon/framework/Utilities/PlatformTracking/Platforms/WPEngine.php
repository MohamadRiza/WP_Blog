<?php

namespace Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

/** @internal */
class WPEngine implements \Smashballoon\Framework\Utilities\PlatformTracking\Platforms\PlatformInterface
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
        if (\method_exists('WpeCommon', 'get_wpe_auth_cookie_value') && !empty(\WpeCommon::get_wpe_auth_cookie_value())) {
            $platform = 'wpengine';
        }
        return $platform;
    }
}
