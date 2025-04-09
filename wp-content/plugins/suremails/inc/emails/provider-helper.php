<?php
/**
 * Helper
 *
 * @package SureMails\Emails
 * @since 1.2.0
 */

namespace SureMails\Inc\Emails;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper
 * This class will handle all helper functions.
 *
 * @since 1.2.0
 */
class ProviderHelper {

	/**
	 * Get Attachment details
	 *
	 * @param string $attachment Attachment path.
	 * @since 1.2.0
	 * @return array<string,string>|false
	 */
	public static function get_attachment( $attachment ) {
		$file      = false;
		$file_type = '';
		$file_name = '';
		try {
			if ( is_file( $attachment ) && is_readable( $attachment ) ) {
				$file_name = basename( $attachment );
				$file      = file_get_contents( $attachment );
				$mime_type = mime_content_type( $attachment );
				if ( $mime_type !== false ) {
					$file_type = str_replace( ';', '', trim( $mime_type ) );
				}
			}
		} catch ( Exception $e ) {
			$file = false;
		}

		if ( $file === false ) {
			return false;
		}

		return [
			'type' => $file_type,
			'name' => $file_name,
			'blob' => base64_encode( $file ),
		];
	}

	/**
	 * Prepare address param.
	 *
	 * @since 1.2.0
	 *
	 * @param array $address Address array.
	 *
	 * @return array
	 */
	public static function address_format( $address ) {

		$email = $address[0] ?? false;
		$name  = $address[1] ?? false;

		$result = $email;

		if ( ! empty( $name ) ) {
			$result = "{$name} <{$email}>";
		}

		return $result;
	}
}
