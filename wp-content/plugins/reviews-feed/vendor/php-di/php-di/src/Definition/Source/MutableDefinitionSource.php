<?php


namespace SmashBalloon\Reviews\Vendor\DI\Definition\Source;

use SmashBalloon\Reviews\Vendor\DI\Definition\Definition;
/**
 * Describes a definition source to which we can add new definitions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
interface MutableDefinitionSource extends DefinitionSource
{
    public function addDefinition(Definition $definition);
}
