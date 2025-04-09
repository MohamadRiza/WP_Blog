<?php

namespace SmashBalloon\Reviews\Vendor\Laravel\SerializableClosure\Contracts;

/** @internal */
interface Serializable
{
    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke();
    /**
     * Gets the closure that got serialized/unserialized.
     *
     * @return \Closure
     */
    public function getClosure();
}
