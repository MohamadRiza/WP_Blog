<?php


namespace SmashBalloon\YoutubeFeed\Vendor\DI\Definition;

use SmashBalloon\YoutubeFeed\Vendor\Psr\Container\ContainerInterface;
/**
 * Describes a definition that can resolve itself.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
interface SelfResolvingDefinition
{
    /**
     * Resolve the definition and return the resulting value.
     *
     * @return mixed
     */
    public function resolve(ContainerInterface $container);
    /**
     * Check if a definition can be resolved.
     */
    public function isResolvable(ContainerInterface $container) : bool;
}
