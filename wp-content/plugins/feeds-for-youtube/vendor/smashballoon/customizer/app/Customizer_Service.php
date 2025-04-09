<?php

namespace Smashballoon\Customizer;

use Smashballoon\Stubs\Services\ServiceProvider;
/** @internal */
class Customizer_Service extends ServiceProvider
{
    /**
     * @var Builder_Customizer
     */
    private $builder_customizer;
    public function __construct(\Smashballoon\Customizer\Builder_Customizer $builder_customizer)
    {
        $this->builder_customizer = $builder_customizer;
    }
    public function register()
    {
        include_once __DIR__ . '/../bootstrap.php';
        $this->builder_customizer->register();
    }
}
