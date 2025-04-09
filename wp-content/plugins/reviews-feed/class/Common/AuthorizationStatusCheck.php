<?php
/**
 * Class AuthorizationStatusCheck
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;

class AuthorizationStatusCheck {

	/**
	 * @var array
	 */
	private $statuses;

	public function __construct() {
        $this->statuses = get_option( 'sbr_statuses', array() );
	}

	/**
	 * An associative array with statuses of various authorization related things
	 *
	 * @return array
	 */
	public function get_statuses() {
		$return = array(
			'license_info' => isset( $this->statuses['license_info'] ) ? $this->statuses['license_info'] : [],
            'license_tier' => $this->get_license_tier(),
			'provider_source_limit_reached' => $this->get_provider_source_limit_reached(),
			'tier_allowed_providers' => $this->get_tier_allowed_providers(),
			'update_frequency' => $this->get_update_frequency(),
			'last_cron_update' => ! empty( $this->statuses['last_cron_update'] ) ? $this->statuses['last_cron_update'] : 0,
			'tiers_info' => $this->get_tiers_info()

		);

		return $return;
	}


	/**
	 * The current license tier, 0 for a free tier
	 *
	 * @return int
	 */
	public function get_license_tier() {
		$tier = isset( $this->statuses['license_tier'] ) ? $this->statuses['license_tier'] : 0;
        if (isset($this->statuses['license_info']) && isset($this->statuses['license_info']['item_name']) && $this->statuses['license_info']['item_name'] === 'All Access Bundle - All Plugins Unlimited'){
            $tier = 3;
        }
        return intval( $tier );
	}

	/**
	 * Returns a list of providers that have had the maximum number of non API key supported
	 * sources connected and also do not have an API key saved in settings
	 *
	 * @return array
	 */
	public function get_provider_source_limit_reached() {
		if ( ! empty( $this->statuses['provider_limit_reached'] ) && is_array( $this->statuses['provider_limit_reached'] ) ) {
			$providers_with_keys = $this->get_providers_with_api_keys();
			$return = array();
			foreach ( $this->statuses['provider_limit_reached'] as $provider_limit_reached ) {
				if ( ! in_array( $provider_limit_reached, $providers_with_keys ) ) {
					$return[] = $provider_limit_reached;
				}
			}
			return $return;
		}

		return array();
	}

	/**
	 * Get Tiers Info
	 *
	 * @return []
	 */
	public function get_tiers_info()
	{
		return [
			'google' => 1,
			'yelp' => 1,
			'facebook' => 2,
			'trustpilot' => 2,
			'tripadvisor' => 3,
			'wordpress.org' => 3
		];
	}


	/**
	 * Tier allowed providers
	 *
	 * @return string[]
	 */
	public function get_tier_allowed_providers() {
		$allowed = array(
			'google',
			'yelp'
        );

        if( Util::sbr_is_pro() ){
            if ( $this->get_license_tier() >= 2 ) {
                $allowed[] = 'facebook';
                $allowed[] = 'trustpilot';
            }
            if ( $this->get_license_tier() === 3 ) {
                $allowed[] = 'tripadvisor';
                $allowed[] = 'wordpress.org';
            }
        }
		return $allowed;
	}

	/**
	 * How often, in seconds, feeds can be updated based on license tier
	 *
	 * @return float|int
	 */
	public function get_update_frequency() {
		$frequency = 24 * HOUR_IN_SECONDS;
		if ( $this->get_license_tier() === 3 ) {
			$frequency = 12 * HOUR_IN_SECONDS;
		}

		return $frequency;
	}

	/**
	 * Providers that have an API key saved in settings
	 *
	 * @return array
	 */
	public function get_providers_with_api_keys() {
		// look through options for providers with API key supplied
		return array();
	}

    public function update_status( $status )
    {
        $current_status = get_option('sbr_statuses', array());
        $updated_status = array_merge($current_status, $status);

        return update_option( 'sbr_statuses' , $updated_status);
    }
}
