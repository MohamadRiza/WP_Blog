<?php


namespace SmashBalloon\Reviews\Vendor\DI\Definition\Source;

use SmashBalloon\Reviews\Vendor\DI\Definition\Exception\InvalidDefinition;
use SmashBalloon\Reviews\Vendor\DI\Definition\ObjectDefinition;
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
