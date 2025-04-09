<?php
/**
 * Customizer Tab
 *
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Customizer\Tabs;


use Smashballoon\Customizer\Tabs\Tab;
use Smashballoon\Customizer\Feed_Builder;
use Smashballoon\Customizer\YouTube_License_Tier;

class Customize_Tab extends Tab {
	protected $feed_id = "";
	protected $id = 'customize';
	protected $heading = "";
	protected $license_tier_features;
	protected $parent_class = "";

	public function __construct() {
		$this->heading = __('Customize', 'feeds-for-youtube');
		$this->feed_id            = isset( $_GET['feed_id'] ) ? sanitize_key( $_GET['feed_id'] ) : '';
		$this->parent_class = "body ";
		// init license tier
		$license_tier = new YouTube_License_Tier;
		$this->license_tier_features = $license_tier->tier_features();
	}

	/**
	 * Get Customize Tab Sections
	 *
	 *
	 * @since 6.0
	 * @access public
	 *
	 * @return array
	*/
	public function get_sections() {
		return array(

			'customize_feedtemplate'   => array(
				'heading'  => __( 'Template', 'feeds-for-youtube' ),
				'icon'     => 'feed_template',
				'controls' => $this->get_customize_feedtemplate_controls(),
			),
			'customize_feedlayout'     => array(
				'heading'  => __( 'Feed Layout', 'feeds-for-youtube' ),
				'icon'     => 'feed_layout',
				'controls' => $this->get_customize_feedlayout_controls(),
			),
			'customize_colorschemes'   => array(
				'heading'  => __( 'Color Scheme', 'feeds-for-youtube' ),
				'icon'     => 'color_scheme',
				'controls' => $this->get_customize_colorscheme_controls(),
			),
			'customize_sections'       => array(
				'heading'  => __( 'Sections', 'feeds-for-youtube' ),
				'isHeader' => true,
			),
			'customize_header'         => array(
				'heading'   => __( 'Header', 'feeds-for-youtube' ),
				'icon'      => 'header',
				'separator' => 'none',
				'controls'  => $this->get_customize_header_controls(),
			),
			'customize_videos'          => array(
				'heading'         => __( 'Videos', 'feeds-for-youtube' ),
				'description'         => __( 'Tweak how a single video looks by default, on hover and when it is opened in a lightbox', 'feeds-for-youtube' ),
				'icon'            => 'videos',
				'controls'        => $this->get_customize_posts_controls(),
				'nested_sections' => $this->get_customize_videos_nested_controls()
			),
			'customize_loadmorebutton' => array(
				'heading'     => __( 'Load More Button', 'feeds-for-youtube' ),
				'icon'        => 'load_more',
				'separator'   => 'none',
				'controls'    => $this->get_customize_loadmorebutton_controls(),
			),
			'customize_subscribebutton'   => array(
				'heading'     => __( 'Subscribe Button', 'feeds-for-youtube' ),
				'icon'        => 'subscribe',
				'separator'   => 'none',
				'controls'    => $this->get_customize_subscribe_button_controls(),
			),
		);
	}

	/**
	 * Get Customize Tab Feed Template Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_feedtemplate_controls() {
		return [
			[
				'type' 				=> 'customview',
				'viewId'			=> 'feedtemplate'
			]
		];
	}

	/**
 * Checks if the extension popup should be shown based on the current license status.
	 * 
	 * @param string $slug
	 * 
	 * @return bool|string
	 * 
	 * @since 2.3.3
	*/
	private static function is_pro_check_extension_popup($slug) {
		return sby_is_pro() && !sby_license_notices_active() ? false : $slug;
	}

	/**
	 * Get Customize Tab Feed Layout Section
	 * @since 6.0
	 * @return array
	*/
	private function get_customize_feedlayout_controls() {
		return array(
			array(
				'type'      => 'toggleset',
				'id'        => 'layout',
				'heading'   => __( 'Layout', 'feeds-for-youtube' ),
				'separator' => 'bottom',
				'options'   => array(
					array(
						'value' => 'grid',
						'icon'  => 'grid',
						'label' => __( 'Grid', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'gallery',
						'icon'  => 'gallery',
						'label' => __( 'Gallery', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'list',
						'icon'  => 'list',
						'label' => __( 'List', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'carousel',
						'icon'  => 'carousel',
						'checkExtension' => sby_is_pro() && !sby_license_notices_active() ? false : 'feedLayout',
						'label' => __( 'Carousel', 'feeds-for-youtube' ),
					),
				),
			),

			//Carousel Settings
			array(
				'type'          => 'heading',
				'heading'       => __( 'Carousel Settings', 'feeds-for-youtube' ),
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
			),
			array(
				'type'          => 'select',
				'id'            => 'carouselrows',
				'layout'        => 'half',
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
				'ajaxAction'    => 'feedFlyPreview',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'heading'       => __( 'Rows', 'feeds-for-youtube' ),
				'options'       => array(
					1 => '1',
					2 => '2',
				),
			),

			array(
				'type'          => 'select',
				'id'            => 'carouselloop',
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Loop Type', 'feeds-for-youtube' ),
				'stacked'       => 'true',
				'options'       => array(
					'rewind'   => __( 'Rewind', 'feeds-for-youtube' ),
					'infinity' => __( 'Infinity', 'feeds-for-youtube' ),
				),
			),
			array(
				'type'          => 'number',
				'id'            => 'carouseltime',
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
				'stacked'       => 'true',
				'layout'        => 'half',
				'fieldSuffix'   => 'ms',
				'heading'       => __( 'Interval Time', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'checkbox',
				'id'            => 'carouselarrows',
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
				'label'         => __( 'Navigation Arrows', 'feeds-for-youtube' ),
				'reverse'       => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'          => 'checkbox',
				'id'            => 'carouselpag',
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
				'label'         => __( 'Pagination Dots', 'feeds-for-youtube' ),
				'reverse'       => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'          => 'checkbox',
				'id'            => 'carouselautoplay',
				'condition'     => array( 'layout' => array( 'carousel' ) ),
				'conditionHide' => true,
				'label'         => __( 'Autoplay', 'feeds-for-youtube' ),
				'reverse'       => 'true',
				'separator'     => 'bottom',
				'stacked'       => 'true',
				'cssClass'		=> 'carousel-autoplay',
				'options'       => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'          => 'heading',
				'heading'       => __( 'Columns', 'feeds-for-youtube' ),
				'condition'     => array( 'layout' => array( 'grid', 'gallery', 'carousel' ) ),
				'conditionHide' => true,
			),
			array(
				'type'          => 'number',
				'id'            => 'cols',
				'icon'          => 'desktop',
				'layout'        => 'half',
				'ajaxAction'    => 'feedFlyPreview',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'condition'     => array( 'layout' => array( 'grid', 'gallery', 'carousel' ) ),
				'conditionHide' => true,
				'heading'       => __( 'Desktop', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'number',
				'id'            => 'colsmobile',
				'icon'          => 'mobile',
				'layout'        => 'half',
				'ajaxAction'    => 'feedFlyPreview',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'condition'     => array( 'layout' => array( 'grid', 'gallery', 'carousel' ) ),
				'conditionHide' => true,
				'heading'       => __( 'Mobile', 'feeds-for-youtube' ),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showheader' => array( true ) ),
				'top'       => 10,
				'bottom'    => 10,
			),
			array(
				'type'          => 'number',
				'id'            => 'num',
				'separator'     => 'bottom',
				'strongHeading' => 'true',
				'heading'       => __( 'Number of Videos to show initially', 'feeds-for-youtube' ),
				'ajaxAction'    => 'feedFlyPreview',
			),
			array(
				'type'          => 'number',
				'id'            => 'itemspacing',
				'separator'     => 'bottom',
				'strongHeading' => 'true',
				'heading'       => __( 'Spacing between videos', 'feeds-for-youtube'),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube .sby_items_wrap .sby_item' => 'padding:{{value}}px !important;' ),
			),

		);
	}

	/**
	 * Get nested controls for customize videos
	 * 
	 * @since 2.1
	 */
	private function get_customize_videos_nested_controls() {
		$nested_controls = array();

		if ( sby_is_pro() && !sby_license_notices_active() ) {
			$nested_controls['video_style'] = array(
				'heading'   => __( 'Video Style', 'feeds-for-youtube' ),
				'icon'      => 'videoStyle',
				'isNested'  => 'true',
				'separator' => 'none',
				'controls'  => $this->get_video_style_nested_controls(),
			);
		}
		
		$nested_controls['individual_elements'] = array(
			'heading'     => __( 'Edit Individual Elements', 'feeds-for-youtube' ),
			'icon'        => 'picture',
			'isNested'    => 'true',
			'separator'   => 'none',
			'controls'    => $this->edit_individual_elements_nested_controls(),
		);
		$nested_controls['hover_state'] = array(
			'heading'     => __( 'Hover State', 'feeds-for-youtube' ),
			'icon'        => 'cursor',
			'isNested'    => 'true',
			'separator'   => 'none',
			'controls'    => $this->get_hover_state_nested_controls(),
		);
		$nested_controls['customize_lightbox'] = array(
			'heading'     => __( 'Video Player Experience', 'feeds-for-youtube' ),
			'description'     => __( 'Tweak and tinker with the experience of when a user plays a video', 'feeds-for-youtube' ),
			'icon'        => 'lightboxExperience',
			'isNested'    => 'true',
			'separator'   => 'none',
			'controls'    => $this->get_nested_lightbox_experience_controls(),
		);

		return $nested_controls;
	}

	/**
	 * Get Customize Tab Color Scheme Section
	 * @since 6.0
	 * @return array
	*/
	private function get_customize_colorscheme_controls() {
		$color_scheme_array = array(
			array(
				'type'      => 'toggleset',
				'id'        => 'colorpalette',
				'separator' => 'bottom',
				'options'   => array(
					array(
						'value' => 'inherit',
						'label' => __( 'Inherit from Theme', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'light',
						'icon'  => 'sun',
						'label' => __( 'Light', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'dark',
						'icon'  => 'moon',
						'label' => __( 'Dark', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'custom',
						'icon'  => 'cog',
						'label' => __( 'Custom', 'feeds-for-youtube' ),
					),
				),
			),

			//Custom Color Palette
			array(
				'type'          => 'heading',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'heading'       => __( 'Custom Palette', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'custombgcolor1',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Background', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id => 'background:{{value}};' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'customtextcolor1',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sby_video_title' => 'color:{{value}};' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'customtextcolor2',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Text 2', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sby_info .sby_meta' => 'color:{{value}};' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'customlinkcolor1',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Link', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sb_youtube_header .sby_header_text .sby_bio, [id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sb_youtube_header .sby_header_text h3, [id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sb_youtube_header .sby_header_text .sby_subscribers' => 'color:{{value}};' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'custombuttoncolor1',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Button 1', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sby_follow_btn a' => 'background:{{value}};' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'custombuttoncolor2',
				'condition'     => array( 'colorpalette' => array( 'custom' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Button 2', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $this->feed_id . ' .sby_footer .sby_load_btn' => 'background:{{value}};' ),
				'stacked'       => 'true',
			),
		);

		$color_overrides = Feed_Builder::get_color_overrides();
		$color_overrides_array = $color_overrides_elements = [];

		foreach ($color_overrides as $cl_override) {
			array_push($color_overrides_array,
				[
					'type' 						=> 'heading',
					'overrideColorCondition' 	=> $cl_override['elements'],
					'heading' 					=> $cl_override['heading'] . '<svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.66656 0L0.726562 0.94L3.7799 4L0.726562 7.06L1.66656 8L5.66656 4L1.66656 0Z" fill="#141B38"/></svg>',
					'enableViewAction' 			=> isset($cl_override['enableViewAction']) ? $cl_override['enableViewAction'] : false
				]
			);

			foreach ($cl_override['controls'] as $cl_override_control) {
				array_push($color_overrides_elements, $cl_override_control['id']);
				array_push($color_overrides_array,
					[
						'type' 						=> 'coloroverride',
						'id' 						=> $cl_override_control['id'],
						'overrideColorCondition' 	=> [$cl_override_control['id']],
						'layout' 					=> 'half',
						'strongHeading'				=> 'false',
						'heading' 					=> $cl_override_control['heading'],
						'pickerType'				=> 'reset',
						'stacked'					=> 'true'
					]
				);
			}
		}
		array_push($color_scheme_array,
			[
				'type' 						=> 'separator',
				'overrideColorCondition' 	=> $color_overrides_elements,
				'conditionHide'				=> true,
				'top' 				=> 20,
				'bottom' 			=> 10,
			],
			[
				'type' 						=> 'heading',
				'overrideColorCondition' 	=> $color_overrides_elements,
				'heading' 					=> __( 'Overrides', 'feeds-for-youtube' ),
				'description'				=> __( 'These colors have been set from the individual element properties and are overriding the global color scheme', 'feeds-for-youtube' ),
			]
		);

		return array_merge( $color_scheme_array, $color_overrides_array );
	}

	/**
	 * Get Customize Tab Header Section
	 * @since 6.0
	 * @return array
	*/
	private function get_customize_header_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'showheader',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showheader' => array( true ) ),
				'top'       => 20,
			),
			array(
				'type'      => 'toggleset',
				'id'        => 'headerstyle',
				'condition' => array( 'showheader' => array( true ) ),
				'conditionHide'	=> true,
				'heading'   => __( 'Header Style', 'feeds-for-youtube' ),
				'options'   => array(
					array(
						'value' => 'standard',
						'label' => __( 'Standard', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'text',
						'label' => __( 'Text', 'feeds-for-youtube' ),
					),
				),
			),
			array(
				'type' 				=> 'separator',
				'condition'			=> ['showheader' => [true]],
				'condition' 		=> 	['showheader' => [true], 'headerstyle' => ['standard'], 'type' => ['channel', 'favorites']],
				'conditionHide'	=> true,
				'top' 				=> 15,
				'bottom' 			=> 15,
			),
			array(
				'type'    => 'switcher',
				'id'      => 'showdescription',
				'label'   => __( 'Channel Descriptions', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'condition' => ['showheader' => [true], 'headerstyle' => ['standard'], 'type' => ['channel', 'favorites']],
				'conditionHide'	=> true,
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'   => 'separator',
				'condition' => ['showheader' => [true], 'headerstyle' => ['standard'], 'type' => ['channel', 'favorites']],
				'conditionHide'	=> true,
				'top'    => 15,
				'bottom' => 15,
			),
			array(
				'type'    => 'switcher',
				'id'      => 'showsubscribers',
				'label'   => __( 'Subscribers', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'condition' => ['showheader' => [true], 'headerstyle' => ['standard'], 'type' => ['channel', 'favorites']],
				'conditionHide'	=> true,
				'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'subscribersCount',
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
				'conditionHide'	=> true,
				'condition'			=> ['showheader' => [true], 'headerstyle' => ['text']],
			),
			array(
				'type' 				=> 'textarea',
				'id' 				=> 'customheadertext',
				'placeholder'		=> __( 'Add Custom bio', 'feeds-for-youtube' ),
				'condition'			=> ['showheader' => [true], 'headerstyle' => ['text']],
				'stacked'			=> 'true',
				'conditionHide'	=> true,
			),
			array(
				'type' 				=> 'select',
				'id' 				=> 'customheadersize',
				'condition'			=> ['showheader' => [true], 'headerstyle' => ['text']],
				'conditionHide'		=> true,
				'layout' 			=> 'half',
				'stacked'			=> 'true',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Size', 'feeds-for-youtube' ),
				'options'			=> [
					'small' => __( 'Small', 'feeds-for-youtube' ),
					'medium' => __( 'Medium', 'feeds-for-youtube' ),
					'large' => __( 'Large', 'feeds-for-youtube' ),
				]
			),
			array(
				'type' 				=> 'colorpicker',
				'id' 				=> 'customheadertextcolor',
				'condition'			=> ['showheader' => [true], 'headerstyle' => ['text']],
				'conditionHide'		=> true,
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'				=> [ $this->parent_class . '[id^=sb_youtube_].sb_youtube .sby-header-type-text' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			),
		);
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 6.0
	 * @return array
	*/
	private function get_customize_posts_controls() {
		return array();
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 6.0
	 * @return array
	*/
	private function get_video_style_nested_controls() {
		return array(
			array(
				'type'   => 'separator',
			),
			array(
				'type'      => 'toggleset',
				'id'        => 'videocardstyle',
				'heading'   => __( 'Card Type', 'feeds-for-youtube' ),
				'separator' => 'bottom',
				'options'   => array(
					array(
						'value' => 'boxed',
						'icon'  => 'boxed',
						'label' => __( 'Boxed', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'regular',
						'icon'  => 'regular',
						'label' => __( 'Regular', 'feeds-for-youtube' ),
					)
				),
			),
			array(
				'type'      => 'toggleset',
				'id'        => 'videocardlayout',
				'heading'   => __( 'Layout', 'feeds-for-youtube' ),
				'separator' => 'bottom',
				'options'   => array(
					array(
						'value' => 'vertical',
						'icon'  => 'regular',
						'label' => __( 'Vertical', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'horizontal',
						'icon'  => 'horizontal',
						'label' => __( 'Horizontal', 'feeds-for-youtube' ),
					)
				),
			),
			array(
				'type'    => 'heading',
				'heading' => __( 'Individual Properties', 'feeds-for-youtube' ),
				'condition'     => array( 'videocardstyle' => array( 'boxed' ) ),
				'conditionHide' => true,
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'boxedbgcolor',
				'condition'     => array( 'videocardstyle' => array( 'boxed' ) ),
				'conditionHide' => true,
				'icon'          => 'background',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Background Color', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube[data-videostyle=boxed] .sby_items_wrap .sby_item .sby_inner_item' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'number',
				'id'            => 'boxborderradius',
				'condition'     => array( 'videocardstyle' => array( 'boxed' ) ),
				'conditionHide' => true,
				'icon'          => 'corner',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Border Radius', 'feeds-for-youtube' ),
				'fieldSuffix'   => 'px',
				'style'         => array( 
					$this->parent_class . '.sb_youtube[data-videostyle=boxed] .sby_items_wrap .sby_item .sby_inner_item' => 'border-radius:{{value}}px!important;',
					$this->parent_class . '.sb_youtube[data-videostyle=boxed] .sby_items_wrap .sby_video_thumbnail' => sprintf('border-radius:%spx %spx 0 0!important;', '{{value}}', '{{value}}' )
				),
				'stacked'       => 'true',
			),
			array(
				'type'   => 'separator',
				'top'    => 20,
				'bottom' => 20,
				'condition'     => array( 'videocardstyle' => array( 'boxed' ) ),
				'conditionHide' => true,
			),
			array(
				'type'          => 'checkbox',
				'id'            => 'enableboxshadow',
				'condition'     => array( 'videocardstyle' => array( 'boxed' ) ),
				'conditionHide' => true,
				'label'         => __( 'Box Shadow', 'feeds-for-youtube' ),
				'reverse'       => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
		);
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 2.0
	 * @return array
	*/
	private function edit_individual_elements_nested_controls() {
		$api_key_activated = Feed_Builder::check_api_key_status();
		$controls = [
			[
				'type' 				=> 'checkboxsection',
				'id'				=> 'include',
				'value'				=> 'icon',
				'checkBoxAction' 	=> true,
				'header' 	 		=> true,
				'label' 			=> __( 'Play Icon', 'feeds-for-youtube' ),
				'separator'			=> 'bottom',
				'options'			=> [
					'enabled'			=> true,
					'disabled'			=> false
				]
			],
		];

		if ( !sby_is_pro() || sby_license_inactive_state() ) {
			$controls[] = [
				'type'          => 'heading',
				'heading'       => __( 'Advanced', 'feeds-for-youtube' ) . '<span class="sb-breadcrumb-pro-label">PRO</span>',
				'description'   => __( 'These properties are available in the PRO version. <a href="#">Learn More</a>', 'feeds-for-youtube' ),
				'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
				'class'			=> 'api-key-required'
			];
		}

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'title',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Video Title', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'			=> true,
				'disabled'			=> false
			],
			'section' 			=> [
				'id' 				=> 'video_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Video Title', 'feeds-for-youtube' ),
				'description' 		=> __( 'The video title that\'s shown at the bottom of each video thumbnails', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::video_styling_title(),
			]
		];

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'duration',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Video Duration', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'			=> true,
				'disabled'			=> false
			]
		];

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'user',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Username', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'			=> true,
				'disabled'			=> false
			],
			'section' 			=> [
				'id' 				=> 'user_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Username', 'feeds-for-youtube' ),
				'description' 		=> __( 'The username that\'s shown at the bottom of each video thumbnails', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::user_styling_title(),
			]
		];

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'countdown',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Live Stream Countdown (when applicable)', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'			=> true,
				'disabled'			=> false
			],
			'section' 			=> [
				'id' 				=> 'countdown_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Live Stream Countdown', 'feeds-for-youtube' ),
				'description' 		=> __( 'The live stream countdown that\'s shown when applicable', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::countdown_styling_title(),
			]
		];

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'date',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Date', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'			=> true,
				'disabled'			=> false
			],
			'section' 			=> [
				'id' 				=> 'date_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Date', 'feeds-for-youtube' ),
				'description' 		=> __( 'The video date that\'s shown below the video username', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::date_styling_title(),
			]
		];

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'description',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Description', 'feeds-for-youtube' ),
			'separator'			=> 'bottom',
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'options'			=> [
				'enabled'			=> true,
				'disabled'			=> false
			],
			'section' 			=> [
				'id' 				=> 'description_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Description', 'feeds-for-youtube' ),
				'description' 		=> __( 'Edit length and size of the video description', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::description_styling_title(),
			]
		];

		if ( ! $api_key_activated && sby_is_pro() ) {
			$controls[] = [
				'type'          => 'heading',
				'heading'       => __( 'API Key Required', 'feeds-for-youtube' ),
				'description'   => __( 'To access or enable these elements you will need to connect an API Key. Learn More', 'feeds-for-youtube' ),
				'class'			=> 'api-key-required'
			];
		}

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'views',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Views Actions', 'feeds-for-youtube' ),
			'separator'			=> 'bottom',
			'disabled' 			=> $api_key_activated ? false : true,
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'section' 			=> [
				'id' 				=> 'views_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Views Actions', 'feeds-for-youtube' ),
				'description' 		=> __( 'Video views that\'s shown below the video thumbnails', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::views_styling_title(),
			]
		];

		$controls[] = [
			'type' 				=> 'checkboxsection',
			'id'				=> 'include',
			'value'				=> 'stats',
			'checkBoxAction' 	=> true,
			'label' 			=> __( 'Likes and Comments Summary', 'feeds-for-youtube' ),
			'separator'			=> 'bottom',
			'disabled' 			=> $api_key_activated ? false : true,
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'options'			=> [
				'enabled'			=> $api_key_activated ? true : false,
				'disabled'			=> false
			],
			'section' 			=> [
				'id' 				=> 'stats_styling_title',
				'separator'			=> 'none',
				'heading' 			=> __( 'Likes and Comments Summary', 'feeds-for-youtube' ),
				'description' 		=> __( 'The likes and comments summary text', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::stats_styling_title(),
			]
		];

		return $controls;
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 6.0
	 * @return array
	*/
	private function get_hover_state_nested_controls() {
		$api_key_activated = Feed_Builder::check_api_key_status();
		
		$controls = [
			[
				'type' 		=> 'checkboxsection',
				'id'		=> 'hoverinclude',
				'value'		=> 'title',
				'checkBoxAction' => true,
				'header' 	 => true,
				'label' 	=> __( 'Video Title', 'feeds-for-youtube' ),
				'separator'			=> 'bottom',
				'options'			=> [
					'enabled'	=> true,
					'disabled'	=> false
				]
			]
		];

		if ( !sby_is_pro() || sby_license_inactive_state() ) {
			$controls[] = [
				'type'          => 'heading',
				'heading'       => __( 'Advanced', 'feeds-for-youtube' ) . '<span class="sb-breadcrumb-pro-label">PRO</span>',
				'description'   => __( 'These properties are available in the PRO version. <a href="#">Learn More</a>', 'feeds-for-youtube' ),
				'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
				'class'			=> 'api-key-required'
			];
		}
		
		$controls[] = [
			'type' 		=> 'checkboxsection',
			'id'		=> 'hoverinclude',
			'value'		=> 'user',
			'checkBoxAction' => true,
			'label' 	=> __( 'Username', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'	=> true,
				'disabled'	=> false
			],
		];
		$controls[] = [
			'type' 		=> 'checkboxsection',
			'id'		=> 'hoverinclude',
			'value'		=> 'countdown',
			'checkBoxAction' => true,
			'label' 	=> __( 'Live Stream Countdown (when applicable)', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'	=> true,
				'disabled'	=> false
			]
		];
		$controls[] = [
			'type' 		=> 'checkboxsection',
			'id'		=> 'hoverinclude',
			'value'		=> 'description',
			'checkBoxAction' => true,
			'label' 	=> __( 'Description', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'	=> true,
				'disabled'	=> false
			]
		];
		$controls[] = [
			'type' 		=> 'checkboxsection',
			'id'		=> 'hoverinclude',
			'value'		=> 'date',
			'checkBoxAction' => true,
			'label' 	=> __( 'Date', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'	=> true,
				'disabled'	=> false
			]
		];

		if ( ! $api_key_activated && sby_is_pro() ) {
			$controls[] = [
				'type'          => 'heading',
				'heading'       => __( 'API Key Required', 'feeds-for-youtube' ),
				'description'   => __( 'To access or enable these elements you will need to connect an API Key. Learn More', 'feeds-for-youtube' ),
				'class'			=> 'api-key-required'
			];
		}

		$controls[] = [
			'type' 		=> 'checkboxsection',
			'id'		=> 'hoverinclude',
			'value'		=> 'views',
			'checkBoxAction' => true,
			'disabled' 			=> $api_key_activated ? false : true,
			'label' 	=> __( 'Views', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'	=> true,
				'disabled'	=> false
			]
		]; 
		$controls[] = [
			'type' 		=> 'checkboxsection',
			'id'		=> 'hoverinclude',
			'value'		=> 'stats',
			'checkBoxAction' => true,
			'disabled' 			=> $api_key_activated ? false : true,
			'label' 	=> __( 'Likes and Comments Summary', 'feeds-for-youtube' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'videoElements',
			'separator'			=> 'bottom',
			'options'			=> [
				'enabled'	=> true,
				'disabled'	=> false
			]
		];

		return $controls;
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 6.0
	 * @return array
	*/
	private function get_nested_hover_state_controls() {
		return array(
			array(
				'type'          => 'colorpicker',
				'id'            => 'hovercolor',
				'icon'          => 'background',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Background', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sbi_link' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'hovertextcolor',
				'icon'          => 'text',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sbi_photo_wrap .sbi_username > a, .sbi_photo_wrap .sbi_caption,.sbi_photo_wrap .sbi_instagram_link,.sbi_photo_wrap .sbi_hover_bottom,.sbi_photo_wrap .sbi_location,.sbi_photo_wrap .sbi_meta,.sbi_photo_wrap .sbi_comments' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'    => 'heading',
				'heading' => __( 'Information to display', 'feeds-for-youtube' ),
			),
			array(
				'type'    => 'checkboxlist',
				'id'      => 'hoverdisplay',
				'options' => array(
					array(
						'value' => 'username',
						'label' => __( 'Username', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'date',
						'label' => __( 'Date', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'instagram',
						'label' => __( 'Instagram Icon', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'caption',
						'label' => __( 'Caption', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'likes',
						'label' => __( 'Like/Comment Icons<br/>(Business account only)', 'feeds-for-youtube' ),
					),
				),
				'reverse' => 'true',
			),
		);
	}



	/**
	 * Get Customize Tab Load More Button Section
	 * @since 6.0
	 * @return array
	*/
	private function get_customize_loadmorebutton_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'showbutton',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showbutton' => array( true ) ),
				'top'       => 20,
				'bottom'    => 5,
			),
			array(
				'type'          => 'text',
				'id'            => 'buttontext',
				'condition'     => array( 'showbutton' => array( true ) ),

				'strongHeading' => 'true',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showbutton' => array( true ) ),
				'top'       => 15,
				'bottom'    => 15,
			),
			array(
				'type'      => 'heading',
				'heading'   => __( 'Color', 'feeds-for-youtube' ),
				'condition' => array( 'showbutton' => array( true ) ),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'buttoncolor',
				'condition'     => array( 'showbutton' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'background',
				'strongHeading' => 'false',
				'heading'       => __( 'Background', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sby_load_btn' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'buttonhovercolor',
				'condition'     => array( 'showbutton' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'cursor',
				'strongHeading' => 'false',
				'heading'       => __( 'Hover State', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sby_load_btn:hover' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'buttontextcolor',
				'condition'     => array( 'showbutton' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'text',
				'strongHeading' => 'false',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sby_load_btn' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
		);
	}

	/**
	 * Get Customize Tab Follow Button Section
	 * @since 6.0
	 * @return array
	*/
	private function get_customize_subscribe_button_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'showsubscribe',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showsubscribe' => array( true ) ),
				'top'       => 20,
				'bottom'    => 5,
			),
			array(
				'type'          => 'text',
				'id'            => 'subscribetext',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showsubscribe' => array( true ) ),
				'top'       => 15,
				'bottom'    => 15,
			),
			array(
				'type'      => 'heading',
				'heading'   => __( 'Color', 'feeds-for-youtube' ),
				'condition' => array( 'showsubscribe' => array( true ) ),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'subscribecolor',
				'condition'     => array( 'showsubscribe' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'background',
				'strongHeading' => 'false',
				'heading'       => __( 'Background', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sby_follow_btn a' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'subscribehovercolor',
				'condition'     => array( 'showsubscribe' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'cursor',
				'strongHeading' => 'false',
				'heading'       => __( 'Hover State', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sby_follow_btn a:hover' => 'box-shadow:inset 0 0 10px 20px {{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'subscribetextcolor',
				'condition'     => array( 'showsubscribe' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'text',
				'strongHeading' => 'false',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
				'style'         => array( $this->parent_class . '.sby_follow_btn a' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
		);
	}


	/**
	 * Get Customize Tab LightBox Section
	 * @since 6.0
	 * @return array
	*/
	private function get_nested_lightbox_experience_controls() {
		$controls = array(
			array(
				'type'    => 'select',
				'id'      => 'playerratio',
				'heading'   => __( 'Player Size Ratio', 'feeds-for-youtube' ),
				'strongHeading' => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'9:16' => '9:16',
					'3:4' => '3:4',
				),
				'default' => '9:16'
			),
			array(
				'type'      => 'separator',
				'top'       => 15,
				'bottom'    => 10,
			),
			array(
				'type'    => 'select',
				'id'      => 'playvideo',
				'heading'   => __( 'When does video play?', 'feeds-for-youtube' ),
				'strongHeading' => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'onclick' => 'Play when clicked',
					'automatically' => 'Automatically',
				),
				'default' => 'automatically'
			),
		);

		$controls[] = array(
			'type'      => 'separator',
			'top'       => 15,
			'bottom'    => 0,
		);

		$controls[] = array(
			'type'    => 'switcher',
			'id'      => 'enablecomments',
			'label'   => __( 'Comments', 'feeds-for-youtube' ),
			'description' => __( '', 'feeds-for-youtube' ),
			'labelStrong' => 'true',
			'descriptionPosition' => 'bottom',
			'checkExtensionPopup' => self::is_pro_check_extension_popup('comments'),
			'stacked' => 'true',
			'options' => array(
				'enabled'  => true,
				'disabled' => false,
			),
			'class' => 'enable-comments'
		);

		$controls[] = array(
			'type'                => 'number',
			'id'                  => 'numcomments',
			'condition'           => array(
				'enablecomments' => array( true ),
			),
			'conditionHide'       => true,
			'strongHeading'       => false,
			'stacked'             => 'true',
			'placeholder'         => '20',
			'child'               => true,
			'fieldSuffixAction'   => 'clearCommentCache',
			'checkExtensionDimmed'	=> self::is_pro_check_extension_popup('comments'),
			'checkExtensionPopup' => self::is_pro_check_extension_popup('comments'),
			'fieldSuffix'         => 'Clear Cache',
			'heading'             => __( 'No. of Comments', 'feeds-for-youtube' ),
			'description'         => __( 'Clearing cache will remove all the saved comments in the database', 'feeds-for-youtube' ),
			'buttonIcon'        => 'update',
			'descriptionPosition' => 'bottom',
			'class' => 'sb-input-button-with-icon'
		);

		$controls[] = array(
			'type'      => 'separator',
			'top'       => 15,
			'bottom'    => 0,
		);

		$controls[] = array(
			'type'    => 'switcher',
			'id'      => 'enablesubscriberlink',
			'label'   => __( 'Subscribe Link', 'feeds-for-youtube' ),
			'description' => __( 'Shows a subscribe link below the video', 'feeds-for-youtube' ),
			'strongHeading' => 'true',
			'descriptionPosition' => 'bottom',
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'subscribeLink',
			'stacked' => 'true',
			'options' => array(
				'enabled'  => true,
				'disabled' => false,
			),
			'class' => 'enable-subscriber-link'
		);

		$controls[] = array(
			'type'          => 'colorpicker',
			'id'            => 'subscribebtnprimarycolor',
			'layout'        => 'half',
			'strongHeading' => 'false',
			'heading'       => __( 'Channel Name', 'feeds-for-youtube' ),
			'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube .sby-video-header-info h5, [id^=sb_youtube_].sb_youtube .sby-channel-info-bar .sby-channel-name, .sby_lb-dataContainer .sby-lb-channel-header .sby-lb-channel-name-with-subs span:first-child' => 'color:{{value}};' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'subscribeLink',
			'stacked'       => 'true',
		);
		$controls[] = array(
			'type'          => 'colorpicker',
			'id'            => 'subscribebtnsecondarycolor',
			'layout'        => 'half',
			'strongHeading' => 'false',
			'heading'       => __( 'Subscriber Count', 'feeds-for-youtube' ),
			'style'         => array( $this->parent_class . '[id^=sb_youtube_].sb_youtube .sby-channel-info-bar .sby-channel-subscriber-count, [id^=sb_youtube_].sb_youtube .sby-video-header-info .sby-video-header-meta, .sby_lb-dataContainer .sby-lb-channel-header .sby-lb-channel-name-with-subs span:nth-child(2)' => 'color:{{value}};', '[id^=sb_youtube_].sb_youtube .sby-video-header-meta span:last-child::after' => 'background:{{value}}' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'subscribeLink',
			'stacked'       => 'true',
		);

		$controls[] = array(
			'type'          => 'colorpicker',
			'id'            => 'subscribelinkcolorbg',
			'layout'        => 'half',
			'strongHeading' => 'false',
			'heading'       => __( 'Button Background', 'feeds-for-youtube' ),
			'style'         => array( $this->parent_class . '.sbc-channel-subscribe-btn button' => 'background:{{value}};', '.sby-player-info .sby-channel-info-bar .sby-channel-subscribe-btn a' => 'background:{{value}};' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'subscribeLink',
			'stacked'       => 'true',
		);

		$controls[] = array(
			'type'          => 'colorpicker',
			'id'            => 'subscribebtntextcolor',
			'layout'        => 'half',
			'strongHeading' => 'false',
			'heading'       => __( 'Button Text', 'feeds-for-youtube' ),
			'style'         => array( $this->parent_class . '.sbc-channel-subscribe-btn button' => 'color:{{value}};', '.sby-player-info .sby-channel-info-bar .sby-channel-subscribe-btn a' => 'color:{{value}};' ),
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() ? false : 'subscribeLink',
			'stacked'       => 'true',
		);

		$controls[] = array(
			'type'      => 'separator',
			'top'       => 15,
			'bottom'    => 0,
		);

		$controls[] = array(
			'type' 		=> 'checkboxsection',
			'id'		=> 'include',
			'value'		=> 'cta',
			'label' 	=> __( 'Call to Action', 'feeds-for-youtube' ),
			'class'		=> 'sbc_hide_toggle',
			'icon'        => 'calltoaction',
			'checkExtensionPopup' => sby_is_pro() && !sby_license_notices_active() && in_array('call_to_actions', $this->license_tier_features) ? false : 'call_to_action',
			'section' 	=> [
				'id' 				=> 'lightbox_call_to_action',
				'separator'			=> 'none',
				'heading' 			=> __( 'Call to Action', 'feeds-for-youtube' ),
				'description' 		=> __( 'Add a call to action when a user pauses a video or if the video finishes', 'feeds-for-youtube' ),
				'controls'			=> Styling_Tab::call_to_action( $this->feed_id ),
			]
		);

		return $controls;
	}

	/**
	 * Text Size Options
	 *
	 *
	 * @since 6.0
	 * @access public
	 *
	 * @return array
	 */
	private function get_text_size_options() {
		return array(
			'inherit' => __( 'Inherit', 'feeds-for-youtube' ),
			'10'      => '10px',
			'11'      => '11px',
			'12'      => '12px',
			'13'      => '13px',
			'14'      => '14px',
			'15'      => '15px',
			'16'      => '16px',
			'18'      => '18px',
			'20'      => '20px',
			'24'      => '24px',
			'28'      => '28px',
			'32'      => '32px',
			'36'      => '36px',
			'42'      => '42px',
			'48'      => '48px',
			'54'      => '54px',
			'60'      => '60px',
		);
	}
}