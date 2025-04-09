<?php

namespace Smashballoon\Customizer\V2;

use Smashballoon\Stubs\Services\ServiceProvider;
/** @internal */
class ServiceContainer extends ServiceProvider
{
    /**
     * @var ServiceProvider[]
     */
    public $services = [\Smashballoon\Customizer\V2\CustomizerBootstrapService::class];
    public function register()
    {
        $container = \Smashballoon\Customizer\V2\Container::getInstance();
        foreach ($this->services as $service) {
            $container->get($service)->register();
        }
    }
}
