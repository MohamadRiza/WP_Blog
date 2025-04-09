<?php
/**
 * Customizer Tab
 *
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Customizer\Tabs;

use Smashballoon\Customizer\Tabs\Tab;
use Smashballoon\Customizer\YouTube_License_Tier;

class Settings_Tab extends Tab {
	protected $id = 'settings';
	protected $heading = "";
	protected $license_tier_features;

	public function __construct() {
		$this->heading = __('Settings', 'feeds-for-youtube');
		// init license tier
		$license_tier = new YouTube_License_Tier;
		$this->license_tier_features = $license_tier->tier_features();
	}

	/**
	 * Get Settings Tab Sections
	 *
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return array
	*/
	public function get_sections() {
		$learn_more = 'https://smashballoon.com/pricing/youtube-feed?utm_campaign=youtube-free&utm_source=moderation&utm_medium=learn-more';
		return array(
			'settings_feedtype'           => array(
				'heading'  => __( 'Feed Type', 'feeds-for-youtube' ),
				'icon'     => 'feedtype',
				'controls' => $this->get_settings_feedtype_controls(),
			),      
			'settings_filters'           => array(
				'heading'  => !sby_is_pro() ? __( 'Filters and Moderation', 'feeds-for-youtube' ) . '<span class="sb-breadcrumb-pro-label">PRO</span>' : __( 'Filters and Moderation', 'feeds-for-youtube' ),
				'description' => sprintf( __('Hide one or more videos individually or with the help of Pro features. <a href="%s" target="_blank">Learn More</a>', 'feeds-for-youtube'), $learn_more ),
				'icon'     => 'filters',
				'controls' => $this->get_settings_filters_controls(),
			),
			'empty_sections'              => array(
				'heading'  => '',
				'isHeader' => true,
			),
			'settings_advanced'           => array(
				'heading'  => __( 'Advanced', 'feeds-for-youtube' ),
				'icon'     => 'cog',
				'controls' => $this->get_settings_advanced_controls(),
			),
		);
	}

	/**
	 * Get Settings Tab Feed Type Section
	 * 
	 * @since 2.0
	 * @return array
	*/
	public static function get_settings_feedtype_controls() {
		return [
			[
				'type' 				=> 'customview',
				'viewId'			=> 'feedtype'
			],
			[
				'type'                => 'switcher',
				'id'                  => 'showpast',
				'label'               => __( 'Show Past Live Streams', 'feeds-for-youtube' ),
				'strongHeading'       => 'true',
				'labelStrong'         => 'true',
				'condition'     	  => array( 'type' => array( 'live' ) ),
				'conditionHide'       => true,
				'options'             => array(
					'enabled'  => true,
					'disabled' => false,
				),
			]
		];
	}

	/**
	 * Get Settings Tab Filters Section
	 * 
	 * @since 2.0
	 * @return array
	*/
	private function get_settings_filters_controls() {
		return array(
			array(
				'type'      => 'textarea',
				'id'        => 'includewords',
				'heading'   => __( 'Allowed Words or Hashtags', 'feeds-for-youtube' ),
				'tooltip' 			=> __( 'Allowed Words or Hashtags', 'feeds-for-youtube' ),
				'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() && in_array('video_filtering', $this->license_tier_features) ? null : 'advancedFilters',
				'placeholder' => __( 'Show videos containing these words or hashtags. Separate multiple words with comma, and include “#” for hashtags.', 'feeds-for-youtube' ),
				'ajaxAction'    => 'filtersAndModeration'
			),
			array(
				'type'      => 'textarea',
				'id'        => 'excludewords',
				'heading'   => __( 'Blocked Words or Hashtags', 'feeds-for-youtube' ),
				'placeholder' => __( 'Hide videos containing these words or hashtags. Separate multiple words with comma, and include “#” for hashtags.', 'feeds-for-youtube' ),
				'tooltip' 			=> __( 'Blocked Words or Hashtags', 'feeds-for-youtube' ),
				'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() && in_array('video_filtering', $this->license_tier_features) ? null : 'advancedFilters',
				'separator' => 'bottom',
				'ajaxAction'    => 'filtersAndModeration',
			),
			array(
				'type'      => 'textarea',
				'id'        => 'hidevideos',
				'heading'   => __( 'Hide specific Videos', 'feeds-for-youtube' ),
				'tooltip' 			=> __( 'Hide specific Videos', 'feeds-for-youtube' ),
				'placeholder' => __( 'Enter video IDs. Separate multiple IDs with comma', 'feeds-for-youtube' ),
				'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() && in_array('video_filtering', $this->license_tier_features) ? null : 'advancedFilters',
				'ajaxAction'    => 'feedRefresh',
			),
		);
	}

	/**
	 * Get Settings Tab Advanced Section
	 * @since 2.0
	 * @return array
	*/
	private function get_settings_advanced_controls() {
		return array(
			array(
				'type'          => 'select',
				'id'            => 'storage_process',
				'strongHeading' => 'true',
				'heading'       => __( 'Local Storage Process', 'feeds-for-youtube' ),
				'options'       => array(
					'background' => 'Background',
					'page' => 'Page',
					'none' => 'None',
				),
			),
			array(
				'type'                => 'switcher',
				'id'                  => 'ajax_post_load',
				'label'               => __( 'Load Initial Posts with AJAX', 'feeds-for-youtube' ),
				'strongHeading'       => 'true',
				'labelStrong'         => 'true',
				'options'             => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'                => 'switcher',
				'id'                  => 'eagerload',
				'label'               => __( 'Load iFrames on Page Load', 'feeds-for-youtube' ),
				'strongHeading'       => 'true',
				'labelStrong'         => 'true',
				'options'             => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
		);
	}

	/**
	 * Get Settings TabSources Section
	 * @since 6.0
	 * @return array
	*/
	private function get_settings_sources_controls() {
		return array(
			array(
				'type'   => 'customview',
				'viewId' => 'sources',
			),
		);
	}

}
