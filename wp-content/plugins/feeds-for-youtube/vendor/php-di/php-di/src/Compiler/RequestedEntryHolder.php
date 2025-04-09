<?php


namespace SmashBalloon\YoutubeFeed\Vendor\DI\Compiler;

use SmashBalloon\YoutubeFeed\Vendor\DI\Factory\RequestedEntry;
/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @internal
 */
class RequestedEntryHolder implements RequestedEntry
{
    /**
     * @var string
     */
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function getName() : string
    {
        return $this->name;
    }
}
