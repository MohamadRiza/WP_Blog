<?php
/**
 * Connection Handler Factory
 *
 * @since 0.0.1
 * @package suremails
 */

namespace SureMails\Inc\Emails\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Factory class to create appropriate connection handler based on type.
 */
class ConnectionHandlerFactory {
	/**
	 * Create appropriate connection handler based on type.
	 *
	 * @param array $connection_data Connection data.
	 * @since 0.0.1
	 * @return ConnectionHandler|null
	 */
	public static function create( array $connection_data ) {
		$handler_class = 'SureMails\\Inc\\Emails\\Providers\\' . strtoupper( $connection_data['type'] ) . '\\' . ucfirst( strtolower( $connection_data['type'] ) ) . 'Handler';

		if ( class_exists( $handler_class ) ) {
			$handler = new $handler_class( $connection_data );

			// Ensure the handler implements ConnectionHandler.
			if ( $handler instanceof ConnectionHandler ) {
				return $handler;
			}
		}

		return null;
	}
}
