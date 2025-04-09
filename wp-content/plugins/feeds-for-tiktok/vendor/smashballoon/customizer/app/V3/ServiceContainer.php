<?php

namespace Smashballoon\Customizer\V3;

use Smashballoon\Stubs\Services\ServiceProvider;
class ServiceContainer extends ServiceProvider
{
    /**
     * @var ServiceProvider[]
     */
    public $services = [\Smashballoon\Customizer\V3\CustomizerBootstrapService::class];
    /**
     * Constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        $container = \Smashballoon\Customizer\V3\Container::getInstance();
        foreach ($this->services as $service) {
            $container->set($service, new $service());
        }
    }
    public function register()
    {
        $container = \Smashballoon\Customizer\V3\Container::getInstance();
        foreach ($this->services as $service) {
            $serviceInstance = $container->get($service);
            if ($serviceInstance !== null) {
                $serviceInstance->register();
            }
        }
    }
}
