<?php
/**
 * Class BusinessDataCache
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;

class BusinessDataCache {

	public const CACHE_KEY = 'sbr_business_cache';

	private $business_cache;

	public function __construct() {
		$raw_cache = get_option( self::CACHE_KEY, '{}' );

		$this->business_cache = json_decode( $raw_cache, true );
	}

	public function get_data( $provider, $business_id ) {
		if ( ! empty( $this->business_cache[ $provider ][ $business_id ] ) ) {
			return $this->business_cache[ $provider ][ $business_id ];
		}

		return array();
	}

	public function update_single_datum( $provider, $business_id, $data_key, $data_value ) {
		if ( empty( $this->business_cache[ $provider ][ $business_id ] ) ) {
			return false;
		}
		$this->business_cache[ $provider ][ $business_id ][ $data_key ] = $data_value;

		return update_option( self::CACHE_KEY, wp_json_encode( $this->business_cache ), false );
	}

	public function update_data( $provider, $business_id, $data ) {
		$this->business_cache[ $provider ][ $business_id ] = $data;

		return update_option( self::CACHE_KEY, wp_json_encode( $this->business_cache ), false );
	}

}
