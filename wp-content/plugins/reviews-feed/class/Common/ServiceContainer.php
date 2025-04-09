<?php

namespace SmashBalloon\Reviews\Common;

use SmashBalloon\Reviews\Common\Admin\MenuService;
use SmashBalloon\Reviews\Common\Admin\SBR_Admin_Notice;
use SmashBalloon\Reviews\Common\Admin\SBR_Collections_Builder;
use SmashBalloon\Reviews\Common\Admin\SBR_New_User;
use SmashBalloon\Reviews\Common\Admin\SBR_Notifications;
use SmashBalloon\Reviews\Common\Admin\SBR_Plugin_Insltaller;
use SmashBalloon\Reviews\Common\Admin\SBR_Support_Tool;
use SmashBalloon\Reviews\Common\Builder\SBR_Feed_Builder;
use SmashBalloon\Reviews\Common\Integrations\Providers\Google;
use SmashBalloon\Reviews\Common\Integrations\Providers\Yelp;
use SmashBalloon\Reviews\Common\Services\CLIService;
use SmashBalloon\Reviews\Common\Services\FeedCacheUpdateService;
use SmashBalloon\Reviews\Common\Services\SBR_Upgrader;
use SmashBalloon\Reviews\Common\Services\SettingsManagerService;
use SmashBalloon\Reviews\Common\Services\ShortcodeService;
use SmashBalloon\Reviews\Common\Utils\EmailVerification;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\Reviews\Common\Services\Upgrade\RoutineManagerService;
use SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager;
use SmashBalloon\Reviews\Common\Migrations\Reviews_Post;
use SmashBalloon\Reviews\Common\Settings\SBR_Settings_Builder;
use SmashBalloon\Reviews\Common\Admin\SBR_About_Builder;
use SmashBalloon\Reviews\Common\Admin\SBR_Support_Builder;
use SmashBalloon\Reviews\Common\Tooltip_Wizard;

class ServiceContainer extends ServiceProvider
{

    protected $services = [
		Reviews_Post::class,
        MenuService::class,
        RoutineManagerService::class,
        //Customizer Services
        \Smashballoon\Customizer\V2\ServiceContainer::class,
	    FeedCacheUpdateService::class,
	    SettingsManagerService::class,
	    ShortcodeService::class,
        Google::class,
        Yelp::class,
        CLIService::class,
        SBR_Feed_Saver_Manager::class,
        SBR_Feed_Builder::class,
		Clear_Cache::class,
        SBR_Settings_Builder::class,
        SBR_About_Builder::class,
        SBR_Support_Builder::class,
        SBR_Admin_Notice::class,
        SBR_Plugin_Insltaller::class,
        SBR_Blocks::class,
        Tooltip_Wizard::class,
        SBR_Notifications::class,
        SBR_New_User::class,
        SBR_Upgrader::class,
        SBR_Collections_Builder::class,
        SBR_Support_Tool::class,
        Error_Reporter::class
    ];

    public function register(): void
    {
        foreach ($this->services as $service) {
            Container::getInstance()->get($service)->register();
        }
    }
}
