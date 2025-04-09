<?php

/**
 * Customize Tab
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Customizer\Tabs;

use Smashballoon\Customizer\V3\SB_Sidebar_Tab;
use SmashBalloon\TikTokFeeds\Common\Utils;

class CustomizeTab extends SB_Sidebar_Tab
{
	/**
	 * Get the Sidebar Tab info
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function tab_info()
	{
		return [
			'id'   => 'sb-customize-tab',
			'name' => __('Customize', 'feeds-for-tiktok'),
		];
	}

	/**
	 * Get the Sidebar Tab Section
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function tab_sections()
	{
		return [
			'template_section'     => [
				'heading'  => __('Template', 'feeds-for-tiktok'),
				'icon'     => 'templates',
				'controls' => self::get_templates_controls(),
			],
			'layout_section'       => [
				'heading'   => __('Layout', 'feeds-for-tiktok'),
				'icon'      => 'layout',
				'highlight' => 'posts-layout',
				'controls'  => self::get_layout_controls(),
				'separator' => true,
			],
			'header_section'       => [
				'heading'   => __('Header', 'feeds-for-tiktok'),
				'icon'      => 'header',
				'highlight' => 'header',
				'controls'  => self::get_header_controls(),
			],
			'tiktok_section'       => [
				'heading'   => __('TikToks', 'feeds-for-tiktok'),
				'icon'      => 'reviews',
				'highlight' => 'tiktokfeed',
				'controls'  => self::get_tiktok_controls(),
			],
			'video_player_section' => [
				'heading'   => __('Video Player Experience', 'feeds-for-tiktok'),
				'icon'      => 'video-player',
				'highlight' => 'video-player',
				'controls'  => self::get_video_player_controls(),
			],
			'loadbutton_section'   => [
				'heading'     => __('Load More Button', 'feeds-for-tiktok'),
				'description' => Utils::sbtt_is_pro() ? '' :
						sprintf(
							__('Upgrade to Pro to Load posts asynchronously with Load more button. %1$sLearn More%2$s', 'feeds-for-tiktok'),
							'<a>',
							'</a>'
						),
				'icon'        => 'loadbutton',
				'highlight'   => 'loadmore-button',
				'upsellModal' => 'loadMoreModal',
				'controls'    => self::get_loadbutton_controls(),
			],

		];
	}

	/**
	 * Get Templates Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_templates_controls()
	{
		return [
			[ // Feed Template.
				'type' => 'feedtemplate',
				'id'   => 'feedTemplate',
			],
		];
	}

	/**
	 * Get Layout Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_layout_controls()
	{
		return [
			[// Layout Type.
				'type'    => 'toggleset',
				'id'      => 'layout',
				'options' => [
					[
						'value' => 'grid',
						'icon'  => 'grid',
						'label' => __('Grid', 'feeds-for-tiktok'),
					],
					[
						'value' => 'list',
						'icon'  => 'list',
						'label' => __('List', 'feeds-for-tiktok'),
						'upsellModal' => 'listModal'
					],
					[
						'value' => 'masonry',
						'icon'  => 'masonry',
						'label' => __('Masonry', 'feeds-for-tiktok'),
						'upsellModal' => 'masonryModal'
					],
					[
						'value' => 'carousel',
						'icon'  => 'carousel',
						'label' => __('Carousel', 'feeds-for-tiktok'),
						'upsellModal' => 'carouselModal'
					],
					[
						'value' => 'gallery',
						'icon'  => 'gallery',
						'label' => __('Gallery', 'feeds-for-tiktok'),
						'upsellModal' => 'galleryModal'
					]
				],
			],
			[// Spacing.
				'type'     => 'group',
				'id'       => 'layout_spacing',
				'heading'  => __('Spacing', 'feeds-for-tiktok'),
				'controls' => [
					[
						'type'      => 'slider',
						'id'        => 'verticalSpacing',
						'label'     => __('Vertical', 'feeds-for-tiktok'),
						'labelIcon' => 'verticalspacing',
						'unit'      => 'px',
						'style'     => [ '.sb-post-item-wrap' => 'margin-bottom:{{value}}px;' ],
					],
					[
						'type'      => 'slider',
						'id'        => 'horizontalSpacing',
						'label'     => __('Horizontal', 'feeds-for-tiktok'),
						'condition' => [
							'layout' => [
								'grid',
								'masonry',
								'carousel',
							],
						],
						'labelIcon' => 'horizontalspacing',
						'unit'      => 'px',
					],

				],
			],
			[// Number of Posts.
				'type'     => 'group',
				'id'       => 'number_posts',
				'heading'  => __('Number of posts to display', 'feeds-for-tiktok'),
				'controls' => [
					[
						'type'     => 'list',
						'controls' => [
							[
								'type'        => 'number',
								'id'          => 'numPostDesktop',
								'ajaxAction'  => 'feedFlyPreview',
								'leadingIcon' => 'desktop',
								'min'         => 0,
								'max'         => Utils::sbtt_is_pro() ? 100 : 10,
							],
							[
								'type'        => 'number',
								'id'          => 'numPostTablet',
								'ajaxAction'  => 'feedFlyPreview',
								'leadingIcon' => 'tablet',
								'min'         => 0,
							],
							[
								'type'        => 'number',
								'id'          => 'numPostMobile',
								'ajaxAction'  => 'feedFlyPreview',
								'leadingIcon' => 'mobile',
								'min'         => 0,
							],
						],
					],
				],
			],
			[// Grid Columns.
				'type'      => 'group',
				'id'        => 'grid_columns',
				'heading'   => __('Columns', 'feeds-for-tiktok'),
				'condition' => [
					'layout' => [
						'grid',
					],
				],
				'controls'  => [
					[
						'type'     => 'list',
						'controls' => [
							[
								'type'        => 'number',
								'id'          => 'gridDesktopColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'desktop',
							],
							[
								'type'        => 'number',
								'id'          => 'gridTabletColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'tablet',
							],
							[
								'type'        => 'number',
								'id'          => 'gridMobileColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'mobile',
							],
						],
					],
				],
			],
			[// Masonry Columns.
				'type'      => 'group',
				'id'        => 'masonry_columns',
				'heading'   => __('Columns', 'feeds-for-tiktok'),
				'condition' => [
					'layout' => [
						'masonry',
					],
				],
				'controls'  => [
					[
						'type'     => 'list',
						'controls' => [
							[
								'type'        => 'number',
								'id'          => 'masonryDesktopColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'desktop',
							],
							[
								'type'        => 'number',
								'id'          => 'masonryTabletColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'tablet',
							],
							[
								'type'        => 'number',
								'id'          => 'masonryMobileColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'mobile',
							],
						],
					],
				],
			],
			[// Carousel Columns & Rows.
				'type'      => 'group',
				'id'        => 'carousel_columns_rows',
				'heading'   => __('Columns and Rows', 'feeds-for-tiktok'),
				'condition' => [
					'layout' => [
						'carousel',
					],
				],
				'controls'  => [
					[
						'type'     => 'list',
						'heading'  => __('Columns', 'feeds-for-tiktok'),
						'controls' => [
							[
								'type'        => 'number',
								'id'          => 'carouselDesktopColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'desktop',
							],
							[
								'type'        => 'number',
								'id'          => 'carouselTabletColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'tablet',
							],
							[
								'type'        => 'number',
								'id'          => 'carouselMobileColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'mobile',
							],
						],
					],
				],
			],
			[// Carousel Pagination.
				'type'      => 'group',
				'id'        => 'carousel_pagination',
				'heading'   => __('Pagination', 'feeds-for-tiktok'),
				'condition' => [
					'layout' => [
						'carousel',
					],
				],
				'controls'  => [
					[
						'type'          => 'select',
						'id'            => 'carouselLoopType',
						'layout'        => 'half',
						'strongheading' => false,
						'stacked'       => true,
						'heading'       => __('Loop Type', 'feeds-for-tiktok'),
						'options'       => [
							'rewind'   => __('Rewind', 'feeds-for-tiktok'),
							'infinity' => __('Infinity', 'feeds-for-tiktok'),
						],
					],
					[
						'type'          => 'number',
						'id'            => 'carouselIntervalTime',
						'layout'        => 'half',
						'strongheading' => false,
						'stacked'       => true,
						'heading'       => __('Interval Time', 'feeds-for-tiktok'),
						'trailingText'  => 'ms',
					],
					[
						'type'    => 'checkbox',
						'id'      => 'carouselShowArrows',
						'label'   => __('Show Navigation Arrows', 'feeds-for-tiktok'),
						'stacked' => true,
						'options' => [
							'enabled'  => true,
							'disabled' => false,
						],
					],
					[
						'type'    => 'checkbox',
						'id'      => 'carouselShowPagination',
						'label'   => __('Show Pagination', 'feeds-for-tiktok'),
						'stacked' => true,
						'options' => [
							'enabled'  => true,
							'disabled' => false,
						],
					],
					[
						'type'    => 'checkbox',
						'id'      => 'carouselEnableAutoplay',
						'label'   => __('Enable Autoplay', 'feeds-for-tiktok'),
						'stacked' => true,
						'options' => [
							'enabled'  => true,
							'disabled' => false,
						],
					],
				],
			],
			[ // Gallery Columns.
				'type'      => 'group',
				'id'        => 'gallery_columns',
				'heading'   => __('Columns', 'feeds-for-tiktok'),
				'condition' => [
					'layout' => [
						'gallery',
					],
				],
				'controls'  => [
					[
						'type'     => 'list',
						'controls' => [
							[
								'type'        => 'number',
								'id'          => 'galleryDesktopColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'desktop',
							],
							[
								'type'        => 'number',
								'id'          => 'galleryTabletColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'tablet',
							],
							[
								'type'        => 'number',
								'id'          => 'galleryMobileColumns',
								'min'         => 1,
								'max'         => 6,
								'leadingIcon' => 'mobile',
							],
						],
					],
				],
			],
			[// Content Length.
				'type'     => 'group',
				'id'       => 'content_length',
				'heading'  => __('Content Length', 'feeds-for-tiktok'),
				'controls' => [
					[
						'type'         => 'number',
						'id'           => 'contentLength',
						'trailingText' => __('characters', 'feeds-for-tiktok'),
					],
				],
			],
		];
	}

	/**
	 * Get Header Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_header_controls()
	{
		return [
			[
				'type'    => 'switcher',
				'id'      => 'showHeader',
				'layout'  => 'third',
				'label'   => __('Enable', 'feeds-for-tiktok'),
				'options' => [
					'enabled'  => true,
					'disabled' => false,
				],
			],
			[
				'type'       => 'checkboxsection',
				'id'         => 'header_content_sections',
				'settingId'  => 'headerContent',
				'topLabel'   => __('Name', 'feeds-for-tiktok'),
				'condition'  => [
					'showHeader' => [ true ],
				],
				'includeTop' => true,
				'controls'   => [
					[ // Header Profile Picture.
						'heading'   => __('Profile Picture', 'feeds-for-tiktok'),
						'id'        => 'avatar',
						'highlight' => 'header-avatar',
						'controls'  => [
							[ // Profile Picture size.
								'type'     => 'group',
								'id'       => 'avatarSize',
								'heading'  => __('Size', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'    => 'select',
										'id'      => 'headerAvatar',
										'stacked' => true,
										'options' => [
											'small' => __('Small', 'feeds-for-tiktok'),
											'medium' => __('Medium', 'feeds-for-tiktok'),
											'large' => __('Large', 'feeds-for-tiktok'),
										],
									],
								],
							],
							[ // Profile Picture Spacing.
								'type'     => 'group',
								'id'       => 'avatarSpacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'headerAvatarPadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-logo' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'headerAvatarMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-logo' => 'margin:{{value}};' ],
									],
								],
							],
						],
					],
					[ // Header Name.
						'heading'   => __('Name', 'feeds-for-tiktok'),
						'id'        => 'name',
						'highlight' => 'header-name',
						'controls'  => [
							[ // Name Text.
								'type'     => 'group',
								'id'       => 'name_text_gr',
								'heading'  => __('Text', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'  => 'font',
										'id'    => 'headerNameFont',
										'style' => [ '.sb-feed-header-name' => '{{value}}' ],
									],
								],
							],
							[ // Name Color.
								'type'     => 'group',
								'id'       => 'name_color_gr',
								'heading'  => __('Colors', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'colorpicker',
										'id'            => 'headerNameColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Text', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-name' => 'color:{{value}};' ],
									],
								],
							],
							[ // Name Spacing.
								'type'     => 'group',
								'id'       => 'name_spacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'headerNamePadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-name' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'headerNameMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-name' => 'margin:{{value}};' ],
									],
								],
							],
						],
					],
					[ // Header Username.
						'heading'   => __('Username', 'feeds-for-tiktok'),
						'id'        => 'username',
						'highlight' => 'header-username',
						'controls'  => [
							[ // Username Text.
								'type'     => 'group',
								'id'       => 'username_text_gr',
								'heading'  => __('Text', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'  => 'font',
										'id'    => 'headerUsernameFont',
										'style' => [ '.sb-feed-header-username' => '{{value}}' ],
									],
								],
							],
							[ // Name Color.
								'type'     => 'group',
								'id'       => 'username_color_gr',
								'heading'  => __('Colors', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'colorpicker',
										'id'            => 'headerUsernameColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Text', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-username' => 'color:{{value}};' ],
									],
								],
							],
							[ // Name Spacing.
								'type'     => 'group',
								'id'       => 'username_spacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'headerUsernamePadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-username' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'headerUsernameMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-username' => 'margin:{{value}};' ],
									],
								],
							],
						],
					],
					[ // Header Description.
						'heading'   => __('Description', 'feeds-for-tiktok'),
						'id'        => 'description',
						'highlight' => 'header-description',
						'upsellModal'  => 'proHeaderModal',
						'controls'  => [
							[ // Description Text.
								'type'     => 'group',
								'id'       => 'description_text_gr',
								'heading'  => __('Text', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'  => 'font',
										'id'    => 'headerDescriptionFont',
										'style' => [ '.sb-feed-header-description' => '{{value}}' ],
									],
								],
							],
							[ // Description Color.
								'type'     => 'group',
								'id'       => 'description_color_gr',
								'heading'  => __('Colors', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'colorpicker',
										'id'            => 'headerDescriptionColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Text', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-description' => 'color:{{value}};' ],
									],
								],
							],
							[ // Description Spacing.
								'type'     => 'group',
								'id'       => 'description_spacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'headerDescriptionPadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-description' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'headerDescriptionMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-description' => 'margin:{{value}};' ],
									],
								],
							],
						],
					],
					[ // Header Likes Views Stats.
						'heading'   => __('Stats', 'feeds-for-tiktok'),
						'id'        => 'stats',
						'highlight' => 'header-stats',
						'upsellModal'  => 'proHeaderModal',
						'controls'  => [
							[ // Stats Text.
								'type'     => 'group',
								'id'       => 'stats_text_gr',
								'heading'  => __('Stats', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'  => 'font',
										'id'    => 'headerStatsFont',
										'style' => [ '.sb-feed-header-stats' => '{{value}}' ],
									],
								],
							],
							[ // Stats Description Text.
								'type'     => 'group',
								'id'       => 'stats_desc_text_gr',
								'heading'  => __('Description', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'  => 'font',
										'id'    => 'headerStatsDescriptionFont',
										'style' => [ '.sb-feed-header-stats-description' => '{{value}}' ],
									],
								],
							],
							[ // Stats Color.
								'type'     => 'group',
								'id'       => 'stats_color_gr',
								'heading'  => __('Colors', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'colorpicker',
										'id'            => 'headerStatsColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Stats', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-stats' => 'color:{{value}};' ],
									],
									[
										'type'          => 'colorpicker',
										'id'            => 'headerStatsDescriptionColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Description', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-stats-description' => 'color:{{value}};' ],
									],
								],
							],
							[ // Stats Spacing.
								'type'     => 'group',
								'id'       => 'stats_spacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'headerStatsPadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-stats-info' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'headerStatsMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-stats-info' => 'margin:{{value}};' ],
									],
								],
							],
						],
					],
					[ // Header Button Section.
						'heading'   => __('Button', 'feeds-for-tiktok'),
						'id'        => 'button',
						'highlight' => 'header-button',
						'controls'  => [
							[ // Button Text.
								'type'     => 'group',
								'id'       => 'button_text_gr',
								'heading'  => __('Text', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'text',
										'layout'        => 'third',
										'id'            => 'headerButtonContent',
										'heading'       => __('Content', 'feeds-for-tiktok'),
										'stacked'       => true,
										'strongheading' => false,
										'bottom'        => -2,
									],
									[
										'type'  => 'font',
										'id'    => 'headerButtonFont',
										'style' => [ '.sb-feed-header-btn' => '{{value}}' ],
									],
								],
							],
							[ // Button Color.
								'type'     => 'group',
								'id'       => 'button_color_gr',
								'heading'  => __('Colors', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'colorpicker',
										'id'            => 'headerButtonColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Text', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-btn' => 'color:{{value}};' ],
									],
									[
										'type'          => 'colorpicker',
										'id'            => 'headerButtonBg',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Background', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-btn' => 'background:{{value}};' ],
									],
									[
										'type'          => 'colorpicker',
										'id'            => 'headerButtonHoverColor',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Text/ Hover', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-btn:hover' => 'color:{{value}};' ],
									],
									[
										'type'          => 'colorpicker',
										'id'            => 'headerButtonHoverBg',
										'stacked'       => true,
										'strongheading' => false,
										'layout'        => 'third',
										'heading'       => __('Bg/ Hover', 'feeds-for-tiktok'),
										'style'         => [ '.sb-feed-header-btn:hover' => 'background:{{value}};' ],
									],
								],
							],
							[ // Button Spacing.
								'type'     => 'group',
								'id'       => 'button_spacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'headerButtonPadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-btn' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'headerButtonMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-feed-header-btn' => 'margin:{{value}};' ],
									],

								],
							],
						],
					],
				],
			],

			[ // Header Spacing.
				'type'      => 'group',
				'id'        => 'header_spacing',
				'heading'   => __('Header Spacing', 'feeds-for-tiktok'),
				'condition' => [
					'showHeader' => [ true ],
				],
				'controls'  => [
					[
						'type'         => 'distance',
						'distancetype' => 'padding',
						'id'           => 'headerPadding',
						'condition'    => [
							'showHeader' => [ true ],
						],
						'heading'      => __('Padding', 'feeds-for-tiktok'),
						'style'        => [ '.sb-feed-header' => 'padding:{{value}};' ],
					],
					[
						'type'         => 'distance',
						'distancetype' => 'margin',
						'id'           => 'headerMargin',
						'condition'    => [
							'showHeader' => [ true ],
						],
						'heading'      => __('Margin', 'feeds-for-tiktok'),
						'style'        => [ '.sb-feed-header' => 'margin:{{value}};' ],
					],
				],
			],
		];
	}

	/**
	 * Get TikTok Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_tiktok_controls()
	{
		return [
			[
				'type'      => 'section',
				'id'        => 'posts_style_nested',
				'heading'   => __('TikTok Style', 'feeds-for-tiktok'),
				'icon'      => 'theme',
				'highlight' => 'tiktokfeed',
				'controls'  => self::get_nested_post_style_controls(),
			],
			[
				'type'        => 'section',
				'id'          => 'posts_individual_nested',
				'heading'     => __('Edit Individual Elements', 'feeds-for-tiktok'),
				'description' => __('Hide or Show individual elements of a post or edit their options', 'feeds-for-tiktok'),
				'icon'        => 'reviews',
				'highlight'   => 'tiktokfeed',
				'controls'    => self::get_nested_post_elements_controls(),
			],
		];
	}

	/**
	 * Get Nested TikTok Post Style Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_nested_post_style_controls()
	{
		return [
			[
				'type'    => 'toggleset',
				'id'      => 'postStyle',
				'options' => [
					[

						'value' => 'regular',
						'icon'  => 'regular',
						'label' => __('Regular', 'feeds-for-tiktok'),
					],
					[
						'value' => 'boxed',
						'icon'  => 'post-boxed',
						'label' => __('Boxed', 'feeds-for-tiktok'),
						'upsellModal' => 'cardLayoutModal',
					],
				],
			],
			[
				'type'      => 'group',
				'id'        => 'posts_boxed_background',
				'condition' => [
					'postStyle' => [ 'boxed' ],
				],
				'heading'   => __('Colors', 'feeds-for-tiktok'),
				'controls'  => [
					[
						'type'          => 'colorpicker',
						'id'            => 'boxedBackgroundColor',
						'heading'       => __('Background Color', 'feeds-for-tiktok'),
						'condition'     => [
							'postStyle' => [ 'boxed' ],
						],
						'stacked'       => true,
						'strongheading' => false,
						'layout'        => 'half',
						'style'         => [ '.sb-post-item-wrap' => 'background:{{value}};' ],
					],
				],
			],

			[
				'type'     => 'group',
				'id'       => 'posts_properties',
				'heading'  => __('Properties', 'feeds-for-tiktok'),
				'controls' => [
					[
						'type'      => 'boxshadow',
						'id'        => 'boxedBoxShadow',
						'condition' => [
							'postStyle' => [ 'boxed' ],
						],
						'label'     => __('Box Shadow', 'feeds-for-tiktok'),
						'style'     => [ '.sb-post-item-wrap' => 'box-shadow:{{value}};' ],
					],
					[
						'type'      => 'borderradius',
						'id'        => 'boxedBorderRadius',
						'condition' => [
							'postStyle' => [ 'boxed' ],
						],
						'label'     => __('Corner Radius', 'feeds-for-tiktok'),
						'style'     => [ '.sb-post-item-wrap' => 'border-radius:{{value}};' ],
					],
					[
						'type'  => 'stroke',
						'id'    => 'postStroke',
						'label' => __('Stroke', 'feeds-for-tiktok'),
						'style' => [
							'[data-post-style="boxed"] .sb-post-item-wrap' => 'border:{{value}};',
							'[data-post-style="regular"] .sb-post-item-wrap' => 'border-bottom:{{value}};',
						],
					],
				],
			],

			[// Post Item Padding.
				'type'     => 'group',
				'id'       => 'posts_item_spacing',
				'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
				'controls' => [
					[
						'type'         => 'distance',
						'distancetype' => 'padding',
						'id'           => 'postPadding',
						'heading'      => __('Padding', 'feeds-for-tiktok'),
						'style'        => [ '.sb-post-item-wrap' => 'padding:{{value}};' ],
					],
				],
			],
		];
	}

	/**
	 * Get Post Individual Elements Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_nested_post_elements_controls()
	{
		return [
			[
				'type'       => 'checkboxsection',
				'id'         => 'individual_elements_sections',
				'settingId'  => 'postElements',
				'topLabel'   => __('Name', 'feeds-for-tiktok'),
				'includeTop' => true,
				'controls'   => [
					[// Author Info.
						'heading' => __('Author Info', 'feeds-for-tiktok'),
						'id'      => 'author_info',
						'type'    => 'checkbox',
						'upsellModal' => 'proPostModal',
					],
					[// Video Thumbnail.
						'heading' => __('Video Thumbnail', 'feeds-for-tiktok'),
						'id'      => 'thumbnail',
						'type'    => 'checkbox',
					],
					[// Play Icon.
						'heading' => __('Play Icon', 'feeds-for-tiktok'),
						'id'      => 'playIcon',
						'type'    => 'checkbox',
					],
					[// Views.
						'heading' => __('Views', 'feeds-for-tiktok'),
						'id'      => 'views',
						'type'    => 'checkbox',
						'upsellModal' => 'proPostModal',
					],
					[// Likes.
						'heading' => __('Likes', 'feeds-for-tiktok'),
						'id'      => 'likes',
						'type'    => 'checkbox',
						'upsellModal' => 'proPostModal',
					],
					[// Caption.
						'heading'   => __('Caption', 'feeds-for-tiktok'),
						'id'        => 'caption',
						'highlight' => 'post-caption',
						'upsellModal' => 'proPostModal',
						'controls'  => [
							[// Caption Text Font.
								'type'     => 'group',
								'id'       => 'caption_font',
								'heading'  => __('Font', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'  => 'font',
										'id'    => 'captionFont',
										'style' => [ '.sb-post-item-caption' => '{{value}}' ],
									],
								],
							],
							[// Caption Text Color.
								'type'     => 'group',
								'id'       => 'caption_color',
								'heading'  => __('Color', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'          => 'colorpicker',
										'id'            => 'captionColor',
										'layout'        => 'third',
										'strongheading' => false,
										'heading'       => __('Text', 'feeds-for-tiktok'),
										'style'         => [ '.sb-post-item-caption' => 'color:{{value}};' ],
									],
								],
							],
							[// Caption Text Spacing.
								'type'     => 'group',
								'id'       => 'caption_spacing',
								'heading'  => __('Element Spacing', 'feeds-for-tiktok'),
								'controls' => [
									[
										'type'         => 'distance',
										'distancetype' => 'padding',
										'id'           => 'captionPadding',
										'heading'      => __('Padding', 'feeds-for-tiktok'),
										'style'        => [ '.sb-post-item-caption' => 'padding:{{value}};' ],
									],
									[
										'type'         => 'distance',
										'distancetype' => 'margin',
										'id'           => 'captionMargin',
										'heading'      => __('Margin', 'feeds-for-tiktok'),
										'style'        => [ '.sb-post-item-caption' => 'margin:{{value}};' ],
									],
								],
							],
						],
					],
				],
			],
		];
	}

	/**
	 * Get Video Player Experience Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_video_player_controls()
	{
		return [
			[
				'type'    => 'toggleset',
				'id'      => 'videoPlayer',
				'options' => [
					[
						'value' => 'lightbox',
						'label' => __('In a lightbox', 'feeds-for-tiktok'),
					],
					[
						'value' => 'inline',
						'label' => __('Inline', 'feeds-for-tiktok'),
						'upsellModal' => 'playerExperienceModal'
					],
				],
			],
		];
	}

	/**
	 * Get Load More Button Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_loadbutton_controls()
	{
		return [
			[
				'type'    => 'switcher',
				'id'      => 'showLoadButton',
				'layout'  => 'third',
				'label'   => __('Enable', 'feeds-for-tiktok'),
				'hidePro' => true,
				'options' => [
					'enabled'  => true,
					'disabled' => false,
				],
			],
			[// Load More Text.
				'type'      => 'group',
				'id'        => 'loadmorebutton_text',
				'heading'   => __('Text', 'feeds-for-tiktok'),
				'condition' => [
					'showLoadButton' => [ true ],
				],
				'controls'  => [
					[
						'type'          => 'text',
						'id'            => 'loadButtonText',
						'condition'     => [
							'showLoadButton' => [ true ],
						],
						'upsellModal'   => 'loadMoreModal',
						'heading'       => __('Content', 'feeds-for-tiktok'),
						'strongheading' => false,
						'stacked'       => true,
						'layout'        => 'third',
					],
					[
						'type'        => 'font',
						'id'          => 'loadButtonFont',
						'upsellModal' => 'loadMoreModal',
						'condition'   => [
							'showLoadButton' => [ true ],
						],
						'style'       => [ '.sb-load-button' => '{{value}}' ],
					],
				],
			],
			[// Load More Color.
				'type'      => 'group',
				'id'        => 'loadmorebutton_color',
				'heading'   => __('Color', 'feeds-for-tiktok'),
				'condition' => [
					'showLoadButton' => [ true ],
				],
				'controls'  => [
					[
						'type'          => 'colorpicker',
						'id'            => 'loadButtonColor',
						'upsellModal'   => 'loadMoreModal',
						'condition'     => [
							'showLoadButton' => [ true ],
						],
						'heading'       => __('Text', 'feeds-for-tiktok'),
						'layout'        => 'third',
						'stacked'       => true,
						'strongheading' => false,
						'style'         => [ '.sb-load-button' => 'color:{{value}};' ],
					],
					[
						'type'          => 'colorpicker',
						'id'            => 'loadButtonBg',
						'upsellModal'   => 'loadMoreModal',
						'condition'     => [
							'showLoadButton' => [ true ],
						],
						'heading'       => __('Background', 'feeds-for-tiktok'),
						'layout'        => 'third',
						'stacked'       => true,
						'strongheading' => false,
						'style'         => [ '.sb-load-button' => 'background:{{value}};' ],
					],
					[
						'type'          => 'colorpicker',
						'id'            => 'loadButtonHoverColor',
						'upsellModal'   => 'loadMoreModal',
						'condition'     => [
							'showLoadButton' => [ true ],
						],
						'heading'       => __('Text / Hover', 'feeds-for-tiktok'),
						'layout'        => 'third',
						'stacked'       => true,
						'strongheading' => false,
						'style'         => [ '.sb-load-button:hover' => 'color:{{value}};' ],
					],
					[
						'type'          => 'colorpicker',
						'id'            => 'loadButtonHoverBg',
						'upsellModal'   => 'loadMoreModal',
						'condition'     => [
							'showLoadButton' => [ true ],
						],
						'heading'       => __('Bg / Hover', 'feeds-for-tiktok'),
						'layout'        => 'third',
						'stacked'       => true,
						'strongheading' => false,
						'style'         => [ '.sb-load-button:hover' => 'background:{{value}};' ],
					],
				],
			],
			[// Load More Spacing.
				'type'      => 'group',
				'id'        => 'loadmorebutton_spacing',
				'heading'   => __('Element Spacing', 'feeds-for-tiktok'),
				'condition' => [
					'showLoadButton' => [ true ],
				],
				'controls'  => [
					[
						'type'         => 'distance',
						'distancetype' => 'padding',
						'id'           => 'loadButtonPadding',
						'upsellModal'  => 'loadMoreModal',
						'heading'      => __('Padding', 'feeds-for-tiktok'),
						'condition'    => [
							'showLoadButton' => [ true ],
						],
						'style'        => [ '.sb-load-button' => 'padding:{{value}};' ],
					],
					[
						'type'         => 'distance',
						'distancetype' => 'margin',
						'id'           => 'loadButtonMargin',
						'upsellModal'  => 'loadMoreModal',
						'heading'      => __('Margin', 'feeds-for-tiktok'),
						'condition'    => [
							'showLoadButton' => [ true ],
						],
						'style'        => [ '.sb-load-button-ctn' => 'margin:{{value}};' ],
					],

				],
			],
		];
	}
}
