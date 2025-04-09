<?php


namespace SmashBalloon\YoutubeFeed\Vendor\DI;

use SmashBalloon\YoutubeFeed\Vendor\Psr\Container\NotFoundExceptionInterface;
/**
 * Exception thrown when a class or a value is not found in the container.
 * @internal
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
