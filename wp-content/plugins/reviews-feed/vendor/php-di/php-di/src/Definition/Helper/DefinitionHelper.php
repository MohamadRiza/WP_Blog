<?php


namespace SmashBalloon\Reviews\Vendor\DI\Definition\Helper;

use SmashBalloon\Reviews\Vendor\DI\Definition\Definition;
/**
 * Helps defining container entries.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
interface DefinitionHelper
{
    /**
     * @param string $entryName Container entry name
     */
    public function getDefinition(string $entryName) : Definition;
}
