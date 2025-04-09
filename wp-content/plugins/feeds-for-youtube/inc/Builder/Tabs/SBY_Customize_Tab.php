<?php
/**
 * Customizer Tab
 *
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Builder\Tabs;

use SmashBalloon\YouTubeFeed\Builder\SBY_Feed_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class SBY_Customize_Tab {


	/**
	 * Get Customize Tab Sections
	 *
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return array
	*/
	public static function get_sections() {
		return array(

			'customize_feedtemplate'   => array(
				'heading'  => __( 'Feed Template', 'feeds-for-youtube' ),
				'icon'     => 'feed_template',
				'controls' => self::get_customize_feedlayout_controls(),
			),
			'customize_feedlayout'     => array(
				'heading'  => __( 'Feed Layout', 'feeds-for-youtube' ),
				'icon'     => 'feed_layout',
				'controls' => self::get_customize_feedlayout_controls(),
			),
			'customize_colorschemes'   => array(
				'heading'  => __( 'Color Scheme', 'feeds-for-youtube' ),
				'icon'     => 'color_scheme',
				'controls' => self::get_customize_colorscheme_controls(),
			),
			'customize_sections'       => array(
				'heading'  => __( 'Sections', 'feeds-for-youtube' ),
				'isHeader' => true,
			),
			'customize_header'         => array(
				'heading'   => __( 'Header', 'feeds-for-youtube' ),
				'icon'      => 'header',
				'separator' => 'none',
				'controls'  => self::get_customize_header_controls(),
			),
			'customize_posts'          => array(
				'heading'         => __( 'Videos', 'feeds-for-youtube' ),
				'icon'            => 'videos',
				'controls'        => self::get_customize_posts_controls(),
				'nested_sections' => array(
					'images_videos'        => array(
						'heading'   => __( 'Images and Videos', 'feeds-for-youtube' ),
						'icon'      => 'picture',
						'isNested'  => 'true',
						'separator' => 'none',
						'controls'  => self::get_nested_images_videos_controls(),
					),
					'caption'              => array(
						'heading'     => __( 'Caption', 'feeds-for-youtube' ),
						'description' => __( 'Customize caption text for your posts<br/><br/>', 'feeds-for-youtube' ),
						'icon'        => 'caption',
						'isNested'    => 'true',
						'separator'   => 'none',
						'controls'    => self::get_nested_caption_controls(),
					),
					'like_comment_summary' => array(
						'heading'     => __( 'Like and Comment Summary', 'feeds-for-youtube' ),
						'description' => __( 'The like and comment icons below each post<br/><br/>', 'feeds-for-youtube' ),
						'icon'        => 'heart',
						'isNested'    => 'true',
						'separator'   => 'none',
						'controls'    => self::get_nested_like_comment_summary_controls(),
					),
					'hover_state'          => array(
						'heading'     => __( 'Hover State', 'feeds-for-youtube' ),
						'description' => __( 'What\'s displayed when hovering over a post<br/><br/>', 'feeds-for-youtube' ),
						'icon'        => 'cursor',
						'isNested'    => 'true',
						'separator'   => 'none',
						'controls'    => self::get_nested_hover_state_controls(),
					),
				),
			),
			'customize_loadmorebutton' => array(
				'heading'     => __( 'Load More Button', 'feeds-for-youtube' ),
				'description' => '<br/>',
				'icon'        => 'load_more',
				'separator'   => 'none',
				'controls'    => self::get_customize_loadmorebutton_controls(),
			),
			'customize_subscribebutton'   => array(
				'heading'     => __( 'Subscribe Button', 'feeds-for-youtube' ),
				'description' => '<br/>',
				'icon'        => 'subscribe',
				'separator'   => 'none',
				'controls'    => self::get_customize_followbutton_controls(),
			),
		);
	}



	/**
	 * Get Customize Tab Feed Layout Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_feedlayout_controls() {
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
				'stacked'       => 'true',
				'options'       => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),

			array(
				'type'          => 'number',
				'id'            => 'numberofvideos',
				'fieldSuffix'   => 'px',
				'separator'     => 'bottom',
				'strongHeading' => 'true',
				'heading'       => __( 'Number of Videos to show initially', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'number',
				'id'            => 'videospacing',
				'fieldSuffix'   => 'px',
				'separator'     => 'bottom',
				'strongHeading' => 'true',
				'heading'       => __( 'Spacing between videos', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'number',
				'id'            => 'num',
				'icon'          => 'desktop',
				'layout'        => 'half',
				'ajaxAction'    => 'feedFlyPreview',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'heading'       => __( 'Desktop', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'number',
				'id'            => 'nummobile',
				'icon'          => 'mobile',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'heading'       => __( 'Mobile', 'feeds-for-youtube' ),
			),

			array(
				'type'   => 'separator',
				'top'    => 10,
				'bottom' => 10,
			),
			array(
				'type'          => 'heading',
				'heading'       => __( 'Columns', 'feeds-for-youtube' ),
				'conditionHide' => true,
			),
			array(
				'type'          => 'select',
				'id'            => 'cols',
				'conditionHide' => true,
				'icon'          => 'desktop',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Desktop', 'feeds-for-youtube' ),
				'stacked'       => 'true',
				'options'       => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',

				),
			),

			array(
				'type'          => 'select',
				'id'            => 'colstablet',
				'conditionHide' => true,
				'icon'          => 'tablet',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Tablet', 'feeds-for-youtube' ),
				'stacked'       => 'true',
				'options'       => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				),
			),
			array(
				'type'          => 'select',
				'id'            => 'colsmobile',
				'conditionHide' => true,
				'icon'          => 'mobile',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Mobile', 'feeds-for-youtube' ),
				'stacked'       => 'true',
				'options'       => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			),

		);
	}

	/**
	 * Get Customize Tab Color Scheme Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_colorscheme_controls() {
		$feed_id            = isset( $_GET['feed_id'] ) ? sanitize_key( $_GET['feed_id'] ) : '';
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
				'style'         => array( '.sbi_header_palette_custom_' . $feed_id . ',#sb_instagram.sbi_palette_custom_' . $feed_id . ',#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer,#sbi_lightbox .sbi_lightbox_tooltip,#sbi_lightbox .sbi_share_close' => 'background:{{value}}!important;' ),
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
				'style'         => array( '#sb_instagram.sbi_palette_custom_' . $feed_id . ' .sbi_caption,#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer .sbi_lb-details .sbi_lb-caption,#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer .sbi_lb-number,#sbi_lightbox.sbi_lb-comments-enabled .sbi_lb-commentBox p' => 'color:{{value}}!important;' ),
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
				'style'         => array( '.sbi_header_palette_custom_' . $feed_id . ' .sbi_bio,#sb_instagram.sbi_palette_custom_' . $feed_id . ' .sbi_meta' => 'color:{{value}}!important;' ),
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
				'style'         => array( '.sbi_header_palette_custom_' . $feed_id . ' a,#sb_instagram.sbi_palette_custom_' . $feed_id . ' .sbi_expand a,#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer .sbi_lb-details a,#sbi_lightbox.sbi_lb-comments-enabled .sbi_lb-commentBox .sbi_lb-commenter' => 'color:{{value}};' ),
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
				'style'         => array( '#sb_instagram.sbi_palette_custom_' . $feed_id . ' #sbi_load .sbi_load_btn' => 'background:{{value}}!important;' ),
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
				'style'         => array( '#sb_instagram.sbi_palette_custom_' . $feed_id . ' #sbi_load .sbi_follow_btn a' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
		);

		$color_overrides = array();

		$color_overrides_array = array();
		return array_merge( $color_scheme_array, $color_overrides_array );
	}

	/**
	 * Get Customize Tab Header Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_header_controls() {
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
				'top'       => 10,
				'bottom'    => 10,
			),
			array(
				'type'      => 'toggleset',
				'id'        => 'headerstyle',
				'condition' => array( 'showheader' => array( true ) ),
				'heading'   => __( 'Header Style', 'feeds-for-youtube' ),
				'options'   => array(
					array(
						'value' => 'standard',
						'label' => __( 'Standard', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'boxed',
						'label' => __( 'Boxed', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'centered',
						'label' => __( 'Centered', 'feeds-for-youtube' ),
					),
				),
			),
			array(
				'type'          => 'select',
				'id'            => 'headersize',
				'condition'     => array( 'showheader' => array( true ) ),

				'strongHeading' => 'true',
				'separator'     => 'both',
				'heading'       => __( 'Header Size', 'feeds-for-youtube' ),
				'options'       => array(
					'small'  => __( 'Small', 'feeds-for-youtube' ),
					'medium' => __( 'Medium', 'feeds-for-youtube' ),
					'large'  => __( 'Large', 'feeds-for-youtube' ),
				),
			),

			array(
				'type'          => 'imagechooser',
				'id'            => 'customavatar',
				'condition'     => array( 'showheader' => array( true ) ),

				'strongHeading' => 'true',
				'separator'     => 'bottom',
				'heading'       => __( 'Use Custom Avatar', 'feeds-for-youtube' ),
				'tooltip'       => __( 'Upload your own custom image to use for the avatar. This is automatically retrieved from Instagram for Business accounts, but is not available for Personal accounts.', 'feeds-for-youtube' ),
				'placeholder'   => __( 'No Image Added', 'feeds-for-youtube' ),
			),

			array(
				'type'      => 'heading',
				'heading'   => __( 'Text', 'feeds-for-youtube' ),
				'condition' => array( 'showheader' => array( true ) ),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'headercolor',
				'condition'     => array( 'showheader' => array( true ) ),

				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_header_text > *, .sbi_bio_info > *' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'headerprimarycolor',
				'condition'     => array(
					'showheader'  => array( true ),
					'headerstyle' => 'boxed',
				),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Primary Color', 'feeds-for-youtube' ),
				'style'         => array(
					'.sbi_header_style_boxed .sbi_bio_info > *' => 'color:{{value}}!important;',
					'.sbi_header_style_boxed' => 'background:{{value}}!important;',
				),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'headersecondarycolor',
				'condition'     => array(
					'showheader'  => array( true ),
					'headerstyle' => 'boxed',
				),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Secondary Color', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_header_style_boxed .sbi_header_bar' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showheader' => array( true ) ),
				'top'       => 10,
				'bottom'    => 10,
			),
			array(
				'type'        => 'switcher',
				'id'          => 'showfollowers',
				'condition'   => array( 'showheader' => array( true ) ),

				'label'       => __( 'Show number of followers', 'feeds-for-youtube' ),
				'stacked'     => 'true',
				'labelStrong' => 'true',
				'options'     => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showheader' => array( true ) ),
				'top'       => 10,
				'bottom'    => 10,
			),
			array(
				'type'        => 'switcher',
				'id'          => 'showbio',
				'condition'   => array( 'showheader' => array( true ) ),

				'label'       => __( 'Show Bio Text', 'feeds-for-youtube' ),
				'tooltip'     => __( 'Use your own custom bio text in the feed header. This is automatically retrieved from Instagram for Business accounts, but it not available for Personal accounts.
', 'feeds-for-youtube' ),
				'stacked'     => 'true',
				'labelStrong' => 'true',
				'options'     => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'        => 'textarea',
				'id'          => 'custombio',
				'placeholder' => __( 'Add Custom bio', 'feeds-for-youtube' ),
				'condition'   => array(
					'showheader' => array( true ),
					'showbio'    => array( true ),
				),

				'child'       => 'true',
				'stacked'     => 'true',
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showheader' => array( true ) ),
				'top'       => 10,
				'bottom'    => 10,
			),
			array(
				'type'        => 'switcher',
				'id'          => 'headeroutside',
				'condition'   => array( 'showheader' => array( true ) ),

				'label'       => __( 'Show outside scrollable area', 'feeds-for-youtube' ),
				'stacked'     => 'true',
				'labelStrong' => 'true',
				'options'     => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showheader' => array( true ) ),
				'top'       => 10,
				'bottom'    => 10,
			),
			array(
				'type'        => 'switcher',
				'id'          => 'stories',
				'condition'   => array( 'showheader' => array( true ) ),
				'switcherTop' => true,

				'heading'     => __( 'Include Stories', 'feeds-for-youtube' ),
				'description' => __( 'You can view active stories by clicking the profile picture in the header. Instagram Business accounts only.<br/><br/>', 'feeds-for-youtube' ),
				'tooltip'     =>
					'<div class="sbi-story-tltp-ctn"><strong>' . __( 'Add Instagram Stories', 'feeds-for-youtube' ) . '</strong>' .
					'<p>' . __( 'Show your active stories from Instagram on your website.', 'feeds-for-youtube' ) . '</p>' .
					'<p class="sbi-story-note"><strong>' . __( 'Note: ', 'feeds-for-youtube' ) . '</strong>' .
					'<span>' . __( 'You need to have a business account with an active story.', 'feeds-for-youtube' ) . '</span></p>' .
					'<div class="sbi-story-tooltip-img"><img src="'.esc_url(SBY_BUILDER_URL . 'assets/img/stories-tooltip.png' ).'" alt="stories tooltip"></div></div>' ,

				'stacked'     => 'true',
				'labelStrong' => 'true',
				'layout'      => 'half',
				'reverse'     => 'true',
				'options'     => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'                => 'number',
				'id'                  => 'storiestime',
				'condition'           => array(
					'showheader' => array( true ),
					'stories'    => array( true ),
				),
				'conditionHide'       => true,
				'strongHeading'       => false,
				'stacked'             => 'true',
				'placeholder'         => '500',
				'child'               => true,

				'fieldSuffix'         => 'milliseconds',
				'heading'             => __( 'Change Interval', 'feeds-for-youtube' ),
				'description'         => __( 'This is the time a story displays for, before displaying the next one. Videos always change when the video is finished.', 'feeds-for-youtube' ),
				'descriptionPosition' => 'bottom',
			),

		);
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_posts_controls() {
		return array();
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_nested_images_videos_controls() {
		return array(
			array(
				'type'   => 'separator',
				'top'    => 20,
				'bottom' => 20,
			),
			array(
				'type'          => 'select',
				'id'            => 'imageres',
				'strongHeading' => 'true',
				'conditionHide' => true,
				'stacked'       => 'true',
				'heading'       => __( 'Resolution', 'feeds-for-youtube' ),
				'description'   => __( 'By default we auto-detect image width and fetch a optimal resolution.', 'feeds-for-youtube' ),
				'options'       => array(
					'auto'   => __( 'Auto-detect (recommended)', 'feeds-for-youtube' ),
					'thumb'  => __( 'Thumbnail (150x150)', 'feeds-for-youtube' ),
					'medium' => __( 'Medium (320x320)', 'feeds-for-youtube' ),
					'full'   => __( 'Full size (640x640)', 'feeds-for-youtube' ),
				),
			),
		);
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_nested_caption_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'showcaption',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'condition' => array( 'layout' => array( 'grid', 'carousel', 'masonry' )),
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'top'       => 15,
				'bottom'    => 15,
				'condition' => array(
							'showcaption' => array( true ),
							'layout' => array( 'grid', 'carousel', 'masonry' )
				),

			),
			array(
				'type'        => 'number',
				'id'          => 'captionlength',
				'condition'   => array(
							'showcaption' => array( true ),
							'layout' => array( 'grid', 'carousel', 'masonry' )
				),

				'stacked'     => 'true',
				'fieldSuffix' => 'characters',
				'heading'     => __( 'Maximum Text Length', 'feeds-for-youtube' ),
				'description' => __( 'Caption will truncate after reaching the length', 'feeds-for-youtube' ),
			),
			array(
				'type'      => 'separator',
				'top'       => 25,
				'bottom'    => 15,
				'condition' => array(
							'showcaption' => array( true ),
							'layout' => array( 'grid', 'carousel', 'masonry' )
				),
			),
			array(
				'type'      => 'heading',
				'condition' => array(
							'showcaption' => array( true ),
							'layout' => array( 'grid', 'carousel', 'masonry' )
				),

				'heading'   => __( 'Text', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'select',
				'id'            => 'captionsize',
				'condition'     => array(
							'showcaption' => array( true ),
							'layout' => array( 'grid', 'carousel', 'masonry' )
				),
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Size', 'feeds-for-youtube' ),
				'stacked'       => 'true',
				'style'         => array( '.sbi_caption_wrap .sbi_caption' => 'font-size:{{value}}px!important;' ),
				'options'       => SBY_Builder_Customizer_Tab::get_text_size_options(),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'captioncolor',
				'condition'     => array(
					'showcaption' => array( true ),
					'layout' => array( 'grid', 'carousel', 'masonry' )
				),

				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_caption_wrap .sbi_caption' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),

		);
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_nested_like_comment_summary_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'showlikes',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'condition'     => array( 'layout' => array( 'grid', 'carousel', 'masonry' ) ),
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'      => 'separator',
				'top'       => 15,
				'bottom'    => 15,
				'condition' => array(
					'showlikes' => array( true ),
					'layout' => array( 'grid', 'carousel', 'masonry' )
				),

			),
			array(
				'type'      => 'heading',
				'condition' => array(
					'showlikes' => array( true ),
					'layout' => array( 'grid', 'carousel', 'masonry' )
				),
				'heading'   => __( 'Icon', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'select',
				'id'            => 'likessize',
				'condition'     =>  array(
					'showlikes' => array( true ),
					'layout' => array( 'grid', 'carousel', 'masonry' )
				),

				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Size', 'feeds-for-youtube' ),
				'stacked'       => 'true',
				'style'         => array( '.sbi_likes, .sbi_comments, .sbi_likes svg, .sbi_comments svg' => 'font-size:{{value}}px!important;' ),
				'options'       => SBY_Builder_Customizer_Tab::get_text_size_options(),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'likescolor',
				'condition'     =>  array(
					'showlikes' => array( true ),
					'layout' => array( 'grid', 'carousel', 'masonry' )
				),
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_likes, .sbi_comments' => 'color:{{value}};' ),
				'stacked'       => 'true',
			),
		);
	}

	/**
	 * Get Customize Tab Posts Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_nested_hover_state_controls() {
		return array(
			array(
				'type'          => 'colorpicker',
				'id'            => 'hovercolor',
				'icon'          => 'background',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Background', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_link' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'hovertextcolor',
				'icon'          => 'text',
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_photo_wrap .sbi_username > a, .sbi_photo_wrap .sbi_caption,.sbi_photo_wrap .sbi_instagram_link,.sbi_photo_wrap .sbi_hover_bottom,.sbi_photo_wrap .sbi_location,.sbi_photo_wrap .sbi_meta,.sbi_photo_wrap .sbi_comments' => 'color:{{value}}!important;' ),
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
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_loadmorebutton_controls() {
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
				'style'         => array( '.sbi_load_btn' => 'background:{{value}}!important;' ),
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
				'style'         => array( '.sbi_load_btn:hover' => 'background:{{value}}!important;' ),
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
				'style'         => array( '.sbi_load_btn' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showbutton' => array( true ) ),
				'top'       => 15,
				'bottom'    => 15,
			),
			array(
				'type'        => 'switcher',
				'id'          => 'autoscroll',
				'condition'   => array( 'showbutton' => array( true ) ),
				'switcherTop' => true,

				'heading'     => __( 'Infinite Scroll', 'feeds-for-youtube' ),
				'description' => __( 'This will load more posts automatically when the users reach the end of the feed', 'feeds-for-youtube' ),
				'stacked'     => 'true',
				'labelStrong' => 'true',
				'layout'      => 'half',
				'reverse'     => 'true',
				'options'     => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'          => 'number',
				'id'            => 'autoscrolldistance',
				'condition'     => array(
					'showbutton' => array( true ),
					'autoscroll' => array( 'true' ),
				),
				'conditionHide' => true,
				'strongHeading' => false,
				'stacked'       => 'true',
				'layout'        => 'half',
				'placeholder'   => '200',
				'child'         => true,
				'fieldSuffix'   => 'px',
				'heading'       => __( 'Trigger Distance', 'feeds-for-youtube' ),
			),

		);
	}

	/**
	 * Get Customize Tab Follow Button Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_followbutton_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'showfollow',
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
				'condition' => array( 'showfollow' => array( true ) ),
				'top'       => 20,
				'bottom'    => 5,
			),
			array(
				'type'          => 'text',
				'id'            => 'followtext',
				'condition'     => array( 'showfollow' => array( true ) ),

				'strongHeading' => 'true',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'showfollow' => array( true ) ),
				'top'       => 15,
				'bottom'    => 15,
			),
			array(
				'type'      => 'heading',
				'heading'   => __( 'Color', 'feeds-for-youtube' ),
				'condition' => array( 'showfollow' => array( true ) ),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'followcolor',
				'condition'     => array( 'showfollow' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'background',
				'strongHeading' => 'false',
				'heading'       => __( 'Background', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_follow_btn a' => 'background:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'followhovercolor',
				'condition'     => array( 'showfollow' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'cursor',
				'strongHeading' => 'false',
				'heading'       => __( 'Hover State', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_follow_btn a:hover' => 'box-shadow:inset 0 0 10px 20px {{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'followtextcolor',
				'condition'     => array( 'showbutton' => array( true ) ),
				'layout'        => 'half',
				'icon'          => 'text',
				'strongHeading' => 'false',
				'heading'       => __( 'Text', 'feeds-for-youtube' ),
				'style'         => array( '.sbi_follow_btn a' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
		);
	}


	/**
	 * Get Customize Tab LightBox Section
	 * @since 2.0
	 * @return array
	*/
	public static function get_customize_lightbox_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'disablelightbox',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'options' => array(
					'enabled'  => false,
					'disabled' => true,
				),
			),
			array(
				'type'      => 'separator',
				'condition' => array( 'disablelightbox' => array( false ) ),
				'top'       => 20,
				'bottom'    => 5,
			),
			array(
				'type'        => 'switcher',
				'id'          => 'lightboxcomments',
				'condition'   => array( 'disablelightbox' => array( false ) ),
				'switcherTop' => true,

				'heading'     => __( 'Comments', 'feeds-for-youtube' ),
				'tooltip'     => __( 'Display comments for your posts inside the lightbox. Comments are only available for User feeds from Business accounts.', 'feeds-for-youtube' ),
				'stacked'     => 'true',
				'labelStrong' => 'true',
				'layout'      => 'half',
				'reverse'     => 'true',
				'options'     => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'                => 'number',
				'id'                  => 'numcomments',
				'condition'           => array(
					'disablelightbox'  => array( false ),
					'lightboxcomments' => array( true ),
				),
				'conditionHide'       => true,
				'strongHeading'       => false,
				'stacked'             => 'true',
				'placeholder'         => '20',
				'child'               => true,
				'fieldSuffixAction'   => 'clearCommentCache',
				'fieldSuffix'         => 'Clear Cache',
				'heading'             => __( 'No. of Comments', 'feeds-for-youtube' ),
				'description'         => __( 'Clearing cache will remove all the saved comments in the database', 'feeds-for-youtube' ),
				'descriptionPosition' => 'bottom',
			),
		);
	}
}
