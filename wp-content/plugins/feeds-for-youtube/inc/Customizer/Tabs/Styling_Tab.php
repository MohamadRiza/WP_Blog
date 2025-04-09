<?php
/**
 * Styling Tab
 * Contains different controls for the individual Elements
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Customizer\Tabs;

if(!defined('ABSPATH'))	exit;

use SmashBalloon\YouTubeFeed\SBY_Display_Elements;

class Styling_Tab{

	/**
	 *
	 * @since 2.0
	 * @return array
	*/
	public static function empty_style(){
		return [];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function playicon_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'playiconcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_video_title' => 'color:{{value}};' ),
				'stacked'			=> 'true'
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function video_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'videotitlecolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_video_title' => 'color:{{value}} !important;' ),
				'stacked'			=> 'true'
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function user_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'videouserecolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_meta span.sby_username_wrap' => 'color:{{value}} !important;' ),
				'stacked'			=> 'true'
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function views_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'videoviewsecolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_meta span.sby_view_count_wrap' => 'color:{{value}} !important;' ),
				'stacked'			=> 'true'
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function countdown_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'videocountdowncolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_ls_message_wrap .sby_ls_message' => 'background-color:{{value}} !important;' ),
				'stacked'			=> 'true'
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function stats_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'videostatscolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_info .sby_stats' => 'color:{{value}}!important;' ),
				'stacked'			=> 'true'
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function date_styling_title(){
		$full_date = SBY_Display_Elements::full_date( strtotime( 'July 25th, 5:30 pm' ), array( 'dateformat' => '0', 'customdate' => '' ), $include_time = true );
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Format', 'feeds-for-youtube' ),
			],
			[
				'type'    => 'select',
				'id'      => 'dateformat',
				'stacked'       => 'true',
				'options'       => array(
					'0' => sprintf( __( 'WordPress Default (%s)', 'feeds-for-youtube' ), $full_date ),
					'1' => __( 'July 25th, 5:30 pm', 'feeds-for-youtube'),
					'2' => __( 'July 25th', 'feeds-for-youtube'),
					'3' => __( 'Mon July 25th', 'feeds-for-youtube'),
					'4' => __( 'Monday July 25th', 'feeds-for-youtube'),
					'5' => __( 'Mon Jul 25th, 2020', 'feeds-for-youtube'),
					'6' => __( 'Monday July 25th, 2020 - 5:30 pm', 'feeds-for-youtube'),
					'7' => __( '07.25.20', 'feeds-for-youtube'),
					'8' => __( '07.25.20 - 17:30', 'feeds-for-youtube'),
					'9' => __( '07/25/20', 'feeds-for-youtube'),
					'10' => __( '25.07.20', 'feeds-for-youtube'),
					'11' => __( '25/07/20', 'feeds-for-youtube'),
					'12' => __( '25th July 2020, 17:30', 'feeds-for-youtube'),
					'custom' => __( 'Custom', 'feeds-for-youtube'),
				),
				'default' => 'automatically',
				'ajaxAction'    => 'feedFlyPreview',
			],
			[
				'type'          => 'text',
				'id'            => 'customdate',
				'condition'     => array( 'dateformat' => array( 'custom' ) ),
				'conditionHide' => true,
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'placeholder'	=> 'Enter custom format (F j, Y g:i a)',
				'ajaxAction'    => 'feedFlyPreview',
			],
			[
				'type'          => 'checkbox',
				'id'            => 'userelative',
				'label'         => __( 'Use relative time (for example: 5 hours ago) when the video is less than 2 days old', 'feeds-for-youtube' ),
				'reverse'       => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'enabled'  => true,
					'disabled' => false,
				)
			]
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function description_styling_title(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type'          => 'number',
				'id'            => 'descriptionlength',
				'stacked'       => 'true',
				'fieldSuffix'   => 'characters',
				'heading'       => __( 'Maximum Text Length', 'feeds-for-youtube' ),
				'description'   => __( 'Description will truncate after reaching the length', 'feeds-for-youtube' ),
			],
			[
				'type'      => 'heading',
				'heading'   => __( 'Text', 'feeds-for-youtube' ),
			],
			[
				'type'    => 'select',
				'id'      => 'descriptiontextsize',
				'heading'   => __( 'Description Text Size', 'feeds-for-youtube' ),
				'layout'        => 'half',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'options'       => array(
					'12px' => '12px',
					'13px' => '13px',
					'14px' => '14px',
					'15px' => '15px',
					'16px' => '16px',
					'18px' => '18px',
					'20px' => '20px',
				),
				'default' => '13px',
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_caption_wrap .sby_caption' => 'font-size:{{value}} !important;' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'videodescriptioncolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube .sby_caption_wrap.sby_item_caption_wrap .sby_caption' => 'color:{{value}}!important;' ),
				'stacked'			=> 'true'
			],
		];
	}

	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 2.0
	 * @return array
	*/
	public static function call_to_action( $feed_id ){
		return array(
			array(
				'type' 				=> 'separator',
				'top' 				=> 0,
				'bottom' 			=> 10,
			),
			array(
				'type'    => 'select',
				'id'      => 'cta',
				'heading'   => __( 'Type', 'feeds-for-youtube' ),
				'description'   => __( 'What the user sees when a video pauses or ends', 'feeds-for-youtube' ),
				'strongHeading' => 'true',
				'stacked'       => 'true',
				'options'       => array(
					'related' => 'Related Videos',
					'link' => 'Custom Link',
					'default' => 'YouTube Default',
				),
				'default' => 'related'
			),
			array(
				'type'      => 'separator',
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
				'top'       => 20,
				'bottom'    => 5,
			),
			array(
				'type'          => 'heading',
				'heading'       => __( 'Settings', 'feeds-for-youtube' ),
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
			),
			array(
				'type'          => 'text',
				'id'            => 'linktext',
				'layout'        => 'half',
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'heading'       => __( 'Button Text', 'feeds-for-youtube' ),
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'linkcolor',
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Button Background', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $feed_id . ' .sby_video_title' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'          => 'colorpicker',
				'id'            => 'linktextcolor',
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'heading'       => __( 'Button Text Color', 'feeds-for-youtube' ),
				'style'         => array( '[id^=sb_youtube_].sb_youtube.sby_palette_custom_' . $feed_id . ' .sby_video_title' => 'color:{{value}}!important;' ),
				'stacked'       => 'true',
			),
			array(
				'type'    => 'select',
				'id'      => 'linkopentype',
				'heading'   => __( 'Open link in', 'feeds-for-youtube' ),
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
				'layout'        => 'half',
				'strongHeading' => 'false',
				'stacked'       => 'true',
				'options'       => array(
					'same' => 'Same Window',
					'newwindow' => 'New Window',
				),
				'default' => 'same'
			),
			array(
				'type' 				=> 'textarea',
				'id' 				=> 'linkurl',
				'heading'			=> __( 'Default Link', 'feeds-for-youtube' ),
				'placeholder'		=> __( 'https://', 'feeds-for-youtube' ),
				'condition'     => array( 'cta' => array( 'link' ) ),
				'conditionHide' => true,
			)
		);
	}
}