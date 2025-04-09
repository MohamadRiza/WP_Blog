<?php


namespace SmashBalloon\YoutubeFeed\Vendor\DI\Definition\Source;

use SmashBalloon\YoutubeFeed\Vendor\DI\Definition\Exception\InvalidDefinition;
use SmashBalloon\YoutubeFeed\Vendor\DI\Definition\ObjectDefinition;
/**
 * Implementation used when autowiring is completely disabled.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
class NoAutowiring implements Autowiring
{
    public function autowire(string $name, ObjectDefinition $definition = null)
    {
        throw new InvalidDefinition(\sprintf('Cannot autowire entry "%s" because autowiring is disabled', $name));
    }
}
