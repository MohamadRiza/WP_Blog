<?php

namespace SmashBalloon\Reviews\Common;

class TemplateRenderer {

    /**
     * Base file path of the templates
     *
     * @since 1.0
     */
    public const BASE_PATH = SBR_PLUGIN_DIR . 'templates/';

    /**
     * Render template
     *
     * @param string $file
     * @param array $data
     *
     * @since 1.0
     */
    public static function render( $file, $data = array() ) {
        $file = str_replace( '.', '/', $file );
        $file = self::BASE_PATH . $file . '.php';

        if ( is_file( $file ) ) {
            if ( ! empty( $data ) ) {
                extract( $data );
            }
            include_once $file;
        }
    }
}
