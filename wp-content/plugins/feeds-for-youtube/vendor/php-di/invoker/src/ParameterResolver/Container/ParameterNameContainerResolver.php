<?php


namespace SmashBalloon\YoutubeFeed\Vendor\Invoker\ParameterResolver\Container;

use SmashBalloon\YoutubeFeed\Vendor\Invoker\ParameterResolver\ParameterResolver;
use SmashBalloon\YoutubeFeed\Vendor\Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;
/**
 * Inject entries from a DI container using the parameter names.
 * @internal
 */
class ParameterNameContainerResolver implements ParameterResolver
{
    /** @var ContainerInterface */
    private $container;
    /**
     * @param ContainerInterface $container The container to get entries from.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters) : array
    {
        $parameters = $reflection->getParameters();
        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $parameters = \array_diff_key($parameters, $resolvedParameters);
        }
        foreach ($parameters as $index => $parameter) {
            $name = $parameter->name;
            if ($name && $this->container->has($name)) {
                $resolvedParameters[$index] = $this->container->get($name);
            }
        }
        return $resolvedParameters;
    }
}
