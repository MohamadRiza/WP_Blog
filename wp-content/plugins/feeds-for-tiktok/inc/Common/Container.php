<?php

namespace SmashBalloon\TikTokFeeds\Common;

use Smashballoon\Stubs\Traits\Singleton;

class Container
{
	use Singleton;

	/**
	 * Services
	 *
	 * @var mixed[]
	 */
	protected $services = [];

	/**
	 * Returns an instance of the Container class.
	 *
	 * @return self The instance of the Container class.
	 */
	public static function get_instance()
	{
		self::$instance = self::getInstance();

		$db_manager_class = \SmashBalloon\TikTokFeeds\Common\Database\DBManager::class;
		self::$instance->set('DBManager', new $db_manager_class());

		$proxy_class = \SmashBalloon\TikTokFeeds\Common\Config\Proxy::class;
		self::$instance->set('Proxy', new $proxy_class());

		return self::$instance;
	}

	/**
	 * Set a service instance in the container.
	 *
	 * @param string $key The key to store the service under.
	 * @param mixed  $value The service instance.
	 */
	public function set($key, $value)
	{
		$this->services[ $key ] = $value;
	}

	/**
	 * Get a service instance from the container.
	 *
	 * @param string $key The key to retrieve the service from.
	 * @return mixed|null The service instance or null if it doesn't exist.
	 */
	public function get($key)
	{
		return $this->services[ $key ] ?? null;
	}
}
