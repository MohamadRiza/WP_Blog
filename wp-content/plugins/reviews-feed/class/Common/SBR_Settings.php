<?php
namespace SmashBalloon\Reviews\Common;

use Smashballoon\Customizer\V2\Feed_Saver;

class SBR_Settings {

    /**
     * @var array
     */
    protected $atts;

    /**
     * @var array
     */
    protected $db;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $feed_type_and_terms;

    /**
     * @var array
     */
    protected $connected_accounts;

    /**
     * @var array
     */
    protected $connected_accounts_in_feed;

    /**
     * @var string
     */
    protected $transient_name;

    /**
	 * SBR_Settings constructor.
	 *
	 * Overwritten in the Pro version.
	 *
	 * @param array $atts shortcode settings
	 * @param array $db settings from the wp_options table
	 */
	public function __construct( $atts, $db, $preview_settings = false ) {
		$atts = is_array( $atts ) ? $atts : array();

		if ( ! empty( $atts['feed'] ) ) {
			$this->settings = self::get_settings_by_feed_id( $atts['feed'], $preview_settings );

			if ( ! empty( $this->settings ) ) {
				$this->settings['customizer'] = isset($atts['customizer']) && $atts['customizer'] == true ? true : false;
				$this->settings['feed'] = intval( $atts['feed'] );

				$this->settings['localization'] = Util::get_api_call_language($this->settings);
			}
		}

		// convert string 'false' and 'true' to booleans
		foreach ( $atts as $key => $value ) {
			if ( $value === 'false' ) {
				$atts[ $key ] = false;
			} elseif ( $value === 'true' ) {
				$atts[ $key ] = true;
			}
		}

		$this->atts = $atts;
		$this->db   = $db;

		$this->connected_accounts = isset( $db['connected_accounts'] ) ? $db['connected_accounts'] : array();
    }


    /**
     * Get Settings By Feed ID
     *
     * @since 1.0
     */
    public static function get_settings_by_feed_id($feed_id, $preview_settings = false, $single_review = false){
        global $wpdb;

        if (is_array($preview_settings)) {
            return $preview_settings;
        }

		if( $single_review && intval($feed_id) < 1)
		{
			return sbr_settings_defaults();
		}

        if (intval($feed_id) < 1) {
            return false;
        }
        $container = Container::get_instance();
        $feed_saver = $container->get( Feed_Saver::class );
        $feed_saver->set_feed_id( $feed_id );

        return $feed_saver->get_feed_settings();
    }

    /**
     * @return array
     *
     * @since 1.0
     */
    public function get_settings(){
        return $this->settings;
    }

    /**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_connected_accounts() {
		return $this->connected_accounts;
	}

	/**
	 * @return array|bool
	 *
	 * @since 1.0
	 */
	public function get_connected_accounts_in_feed() {
		if ( isset( $this->connected_accounts_in_feed ) ) {
			return $this->connected_accounts_in_feed;
		} else {
			return false;
		}
	}

	/**
	 * @return bool|string
	 *
	 * @since 1.0
	 */
	public function get_transient_name() {
		if ( isset( $this->transient_name ) ) {
			return $this->transient_name;
		} else {
			return false;
		}
	}

    /**
	 * @return float|int
	 *
	 * @since 1.0
	 */
	public function get_cache_time_in_seconds() {
		if ( $this->db['caching_type'] === 'background' ) {
			return SBR_CRON_UPDATE_CACHE_TIME;
		} else {
			//If the caching time doesn't exist in the database then set it to be 1 hour
			$cache_time = isset( $this->settings['cache_time'] ) ? (int)$this->settings['cache_time'] : 1;
			$cache_time_unit = isset( $this->settings['cache_time_unit'] ) ? $this->settings['cache_time_unit'] : 'hours';

			//Calculate the cache time in seconds
			if ( $cache_time_unit == 'minutes' ) $cache_time_unit = 60;
			if ( $cache_time_unit == 'hours' ) $cache_time_unit = 60*60;
			if ( $cache_time_unit == 'days' ) $cache_time_unit = 60*60*24;

			$cache_time = max( 900, $cache_time * $cache_time_unit );

			return $cache_time;
		}
	}

	public function update_settings($update_array = []) {
		if(!is_array($update_array)) {
			return false;
		}

		$updated = array_merge($this->settings, array_map(function ($value) {
			return $this->convert_value($value);
		}, $update_array));

		return update_option('sbr_settings', $updated);
	}

	private function convert_value($value) {
		switch($value) {
			case 'true':
				return true;
			case 'false':
				return false;
			default:
				return $value;
		}
	}

}