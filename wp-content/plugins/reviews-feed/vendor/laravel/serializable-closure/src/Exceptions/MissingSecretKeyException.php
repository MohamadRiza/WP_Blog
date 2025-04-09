<?php

namespace SmashBalloon\Reviews\Vendor\Laravel\SerializableClosure\Exceptions;

use Exception;
/** @internal */
class MissingSecretKeyException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = 'No serializable closure secret key has been specified.')
    {
        parent::__construct($message);
    }
}
