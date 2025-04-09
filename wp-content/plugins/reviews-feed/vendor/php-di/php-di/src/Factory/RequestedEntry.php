<?php


namespace SmashBalloon\Reviews\Vendor\DI\Factory;

/**
 * Represents the container entry that was requested.
 *
 * Implementations of this interface can be injected in factory parameters in order
 * to know what was the name of the requested entry.
 *
 * @api
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
interface RequestedEntry
{
    /**
     * Returns the name of the entry that was requested by the container.
     */
    public function getName() : string;
}
