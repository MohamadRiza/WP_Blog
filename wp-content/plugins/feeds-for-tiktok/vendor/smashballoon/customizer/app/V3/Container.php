<?php

namespace Smashballoon\Customizer\V3;

class Container
{
    public static $container;
    /**
     * @var mixed[]
     */
    protected $services = [];
    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$container === null) {
            self::$container = new self();
        }
        return self::$container;
    }
    /**
     * Set a service instance in the container.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->services[$key] = $value;
    }
    /**
     * Get a service instance from the container.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->services[$key] ?? null;
    }
}
