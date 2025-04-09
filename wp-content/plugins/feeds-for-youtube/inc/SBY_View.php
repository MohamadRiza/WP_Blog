<?php
/**
 * Class SBY_View
 *
 * This class loads view page template files on the admin dashboard area.
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SBY_View {

	/**
	 * Base file path of the templates
	 *
	 * @since 2.0
	 */
	const BASE_PATH = SBY_PLUGIN_DIR . 'admin/templates/';

	public function __construct() {
	}

	/**
	 * Render template
	 *
	 * @param string $file
	 * @param array $data
	 *
	 * @since 2.0
	 */
	public static function render( $file, $data = array() ) {
		$file = str_replace( '.', '/', $file );
		$file = self::BASE_PATH . $file . '.php';

		if ( file_exists( $file ) ) {
			if ( ! empty( $data ) ) {
				extract( $data );
			}
			include_once $file;
		}
	}
}
