<?php


namespace SmashBalloon\Reviews\Vendor\DI;

use SmashBalloon\Reviews\Vendor\Psr\Container\ContainerExceptionInterface;
/**
 * Exception for the Container.
 * @internal
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
