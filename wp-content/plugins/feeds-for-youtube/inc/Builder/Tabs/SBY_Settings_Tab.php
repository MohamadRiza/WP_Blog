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


class SBY_Settings_Tab {


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
			'settings_feedtype'           => array(
				'heading'  => __( 'Sources', 'feeds-for-youtube' ),
				'icon'     => 'source',
				'controls' => self::get_settings_sources_controls(),
			),
			'settings_filters_moderation' => array(
				'heading'   => __( 'Filters and Moderation', 'feeds-for-youtube' ),
				'icon'      => 'filter',
				'separator' => 'none',
				'controls'  => self::get_settings_filters_moderation_controls(),
			),
			'settings_sort'               => array(
				'heading'  => __( 'Sort', 'feeds-for-youtube' ),
				'icon'     => 'sort',
				'controls' => self::get_settings_sort_controls(),
			),
			'settings_shoppable_feed'     => array(
				'heading'   => __( 'Shoppable Feed', 'feeds-for-youtube' ),
				'icon'      => 'shop',
				'separator' => 'none',
				'controls'  => self::get_settings_shoppable_feed_controls(),
			),
			'empty_sections'              => array(
				'heading'  => '',
				'isHeader' => true,
			),
			'settings_advanced'           => array(
				'heading'  => __( 'Advanced', 'feeds-for-youtube' ),
				'icon'     => 'cog',
				'controls' => self::get_settings_advanced_controls(),
			),
		);
	}

	/**
	 * Get Settings Tab Filters & Moderation Section
	 * @since 4.0
	 * @return array
	*/
	public static function get_settings_filters_moderation_controls() {
		return array(
			array(
				'type'            => 'customview',
				'viewId'          => 'moderationmode',
				'switcher'        => array(
					'id'          => 'enablemoderationmode',
					'label'       => __( 'Enable', 'feeds-for-youtube' ),
					'reverse'     => 'true',
					'stacked'     => 'true',
					'labelStrong' => true,
					'options'     => array(
						'enabled'  => true,
						'disabled' => false,
					),
				),
				'moderationTypes' => array(
					'allow' => array(
						'label'       => __( 'Allow List', 'feeds-for-youtube' ),
						'description' => __( 'Hides post by default so you can select the ones you want to show', 'feeds-for-youtube' ),
					),
					'block' => array(
						'label'       => __( 'Block List', 'feeds-for-youtube' ),
						'description' => __( 'Show all posts by default so you can select the ones you want to hide', 'feeds-for-youtube' ),
					),
				),
			),
			array(
				'type'              => 'separator',
				'top'               => 10,
				'bottom'            => 10,
				'checkViewDisabled' => 'moderationMode',
			),
			array(
				'type'              => 'heading',
				'strongHeading'     => 'true',
				'heading'           => __( 'Filters', 'feeds-for-youtube' ),
				'checkViewDisabled' => 'moderationMode',
			),
			array(
				'type'              => 'textarea',
				'id'                => 'includewords',
				'heading'           => __( 'Only show posts containing', 'feeds-for-youtube' ),
				'tooltip'           => __( 'Only show posts which contain certain words or hashtags in the caption. For example, adding "sheep, cow, dog" will show any photos which contain either the word sheep, cow, or dog. You can separate multiple words or hashtags using commas.', 'feeds-for-youtube' ),
				'placeholder'       => __( 'Add words here to only show posts containing these words', 'feeds-for-youtube' ),
				'checkViewDisabled' => 'moderationMode',
			),

			array(
				'type'              => 'textarea',
				'id'                => 'excludewords',
				'disabledInput'     => true,
				'heading'           => __( 'Do not show posts containing', 'feeds-for-youtube' ),
				'tooltip'           => __( 'Remove any posts containing these text strings, separating multiple strings using commas.', 'feeds-for-youtube' ),
				'placeholder'       => __( 'Add words here to hide any posts containing these words', 'feeds-for-youtube' ),
				'checkViewDisabled' => 'moderationMode',
			),

			array(
				'type'              => 'heading',
				'strongHeading'     => 'true',
				'stacked'           => 'true',
				'heading'           => __( 'Show specific types of posts', 'feeds-for-youtube' ),
				'checkViewDisabled' => 'moderationMode',
			),

			array(
				'type'              => 'checkbox',
				'id'                => 'photosposts',
				'label'             => __( 'Photos', 'feeds-for-youtube' ),
				'reverse'           => 'true',
				'stacked'           => 'true',
				'checkViewDisabled' => 'moderationMode',
				'ajaxAction'        => 'feedFlyPreview',
				'options'           => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),

			array(
				'type'              => 'checkbox',
				'id'                => 'videosposts',
				'label'             => __( 'Feed Videos', 'feeds-for-youtube' ),
				'reverse'           => 'true',
				'stacked'           => 'true',
				'checkViewDisabled' => 'moderationMode',
				'ajaxAction'        => 'feedFlyPreview',
				'options'           => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'              => 'checkbox',
				'id'                => 'igtvposts',
				'label'             => __( 'IGTV Videos', 'feeds-for-youtube' ),
				'reverse'           => 'true',
				'stacked'           => 'true',
				'checkViewDisabled' => 'moderationMode',
				'ajaxAction'        => 'feedFlyPreview',
				'options'           => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),

			array(
				'type'              => 'separator',
				'top'               => 26,
				'bottom'            => 15,
				'checkViewDisabled' => 'moderationMode',
			),

			array(
				'type'              => 'number',
				'id'                => 'offset',
				'strongHeading'     => 'true',
				'stacked'           => 'true',
				'placeholder'       => '0',
				'fieldSuffix'       => 'posts',
				'heading'           => __( 'Post Offset', 'feeds-for-youtube' ),
				'description'       => __( 'This will skip the specified number of posts from displaying in the feed', 'feeds-for-youtube' ),
				'checkViewDisabled' => 'moderationMode',
			),

		);
	}


	/**
	 * Get Settings Tab Sort Section
	 * @since 4.0
	 * @return array
	*/
	public static function get_settings_sort_controls() {
		return array(
			array(
				'type'          => 'toggleset',
				'id'            => 'sortby',
				'heading'       => __( 'Sort Posts by', 'feeds-for-youtube' ),
				'strongHeading' => 'true',
				'ajaxAction'    => 'feedFlyPreview',
				'options'       => array(
					array(
						'value' => 'none',
						'label' => __( 'Newest', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'likes',
						'label' => __( 'Likes', 'feeds-for-youtube' ),
					),
					array(
						'value' => 'random',
						'label' => __( 'Random', 'feeds-for-youtube' ),
					),
				),
			),
		);
	}


	/**
	 * Get Settings Tab Shoppable Feed Section
	 * @since 4.0
	 * @return array
	*/
	public static function get_settings_shoppable_feed_controls() {
		return array(
			array(
				'type'    => 'switcher',
				'id'      => 'shoppablefeed',
				'label'   => __( 'Enable', 'feeds-for-youtube' ),
				'reverse' => 'true',
				'stacked' => 'true',
				'options' => array(
					'enabled'  => true,
					'disabled' => false,
				),
			),
			array(
				'type'          => 'customview',
				'condition'     => array( 'shoppablefeed' => array( false ) ),
				'conditionHide' => true,
				'viewId'        => 'shoppabledisabled',
			),
			array(
				'type'          => 'customview',
				'condition'     => array( 'shoppablefeed' => array( true ) ),
				'conditionHide' => true,
				'viewId'        => 'shoppableenabled',
			),
			array(
				'type'          => 'customview',
				'condition'     => array( 'shoppablefeed' => array( true ) ),
				'conditionHide' => true,
				'viewId'        => 'shoppableselectedpost',
			),

		);
	}


	/**
	 * Get Settings Tab Advanced Section
	 * @since 4.0
	 * @return array
	*/
	public static function get_settings_advanced_controls() {
		return array(
			array(
				'type'          => 'number',
				'id'            => 'maxrequests',
				'strongHeading' => 'true',
				'heading'       => __( 'Max Concurrent API Requests', 'feeds-for-youtube' ),
				'description'   => __( 'Change the number of maximum concurrent API requests. Not recommended unless directed by the support team.', 'feeds-for-youtube' ),
			),
			array(
				'type'                => 'switcher',
				'id'                  => 'customtemplates',
				'label'               => __( 'Custom Templates', 'feeds-for-youtube' ),
				'description'         => sprintf( __( 'The default HTML for the feed can be replaced with custom templates added to your theme\'s folder. Enable this setting to use these templates. Custom templates are not used in the feed editor. %1$sLearn More%2$s', 'feeds-for-youtube' ), '<a href="https://smashballoon.com/guide-to-creating-custom-templates/?utm_source=plugin-pro&utm_campaign=sbi&utm_medium=customizer" target="_blank">', '</a>' ),
				'descriptionPosition' => 'bottom',
				'reverse'             => 'true',
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
	 * @since 2.0
	 * @return array
	*/
	public static function get_settings_sources_controls() {
		return array(
			array(
				'type'   => 'customview',
				'viewId' => 'sources',
			),
		);
	}

}
