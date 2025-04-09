<?php

namespace SmashBalloon\Reviews\Common\Exceptions;

class BaseException extends \Exception
{
    public function formatted(): string
    {
        return sprintf('Error Code: %s - %s in %s:%s', $this->getCode(), $this->getMessage(), $this->getFile(),
            $this->getLine());
    }
}