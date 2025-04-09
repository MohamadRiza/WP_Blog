<?php


namespace SmashBalloon\YoutubeFeed\Vendor\DI\Definition\Source;

use SmashBalloon\YoutubeFeed\Vendor\DI\Definition\Exception\InvalidDefinition;
use SmashBalloon\YoutubeFeed\Vendor\DI\Definition\ObjectDefinition;
/**
 * Source of definitions for entries of the container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
interface Autowiring
{
    /**
     * Autowire the given definition.
     *
     * @throws InvalidDefinition An invalid definition was found.
     * @return ObjectDefinition|null
     */
    public function autowire(string $name, ObjectDefinition $definition = null);
}
