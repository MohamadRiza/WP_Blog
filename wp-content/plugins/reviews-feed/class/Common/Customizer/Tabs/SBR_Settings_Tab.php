<?php

namespace SmashBalloon\Reviews\Common\Customizer\Tabs;

use Smashballoon\Customizer\V2\SB_Sidebar_Tab;
use SmashBalloon\Reviews\Common\Util;
/**
 * Class Settings Tab
 *
 * @since 1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SBR_Settings_Tab extends SB_Sidebar_Tab {

    /**
     * Get the Sidebar Tab info
     *
     * @since 1.0
     *
     * @return array
     */
    protected function tab_info(){
        return [
            'id' => 'sb-settings-tab',
            'name' => __('Settings', 'reviews-feed')
        ];
    }

    /**
     * Get the Sidebar Tab Section
     *
     * @since 1.0
     *
     * @return array
     */
    protected function tab_sections(){
        return [
            'sources_section' => [
                'heading'     => __('Sources', 'reviews-feed'),
                'icon'        => 'sourcesadd',
                'controls'    => self::get_sources_controls()
            ],
            'sort_section' => [
                'heading'     => __('Sort', 'reviews-feed'),
                'icon'        => 'sorting',
                'controls'    => self::get_sort_controls()
            ],
            'filters_section' => [
                'heading'     => __('Filters', 'reviews-feed'),
                'icon'        => 'filter',
                'description' => Util::sbr_is_pro() ? '' :
                sprintf(
                /* translators: %s: Opening and closing anchor HTML tags */
                __('Upgrade to Pro to apply filters to your reviews feed. %sLearn More%s', 'reviews-feed'),
                    '<a>',
                    '</a>'
                ),
                'upsellModal' => 'filtersModal',
                'controls'    => self::get_filters_controls()
            ],
            'moderation_section' => [
                'heading'     => __('Moderation', 'reviews-feed'),
                'icon'        => 'eye',
                'controls'    => Util::sbr_is_pro() ? self::get_moderation_controls() : self::get_moderation_free_controls(),
                'description' => Util::sbr_is_pro() ? '' :
                sprintf(
                /* translators: %s: Opening and closing anchor HTML tags */

                __('Upgrade to Pro to moderate your feed. %sLearn More%s', 'reviews-feed'),
                    '<a>',
                    '</a>'
                ),
                'upsellModal' => 'moderationModal',
                'separator'   => true
            ],
            /*
            'advanced_section' => [
                'heading'     => __('Advanced', 'reviews-feed'),
                'icon'        => 'cog',
                'controls'    => self::get_advanced_controls()
            ],
            */
            'language_section' => [
                'heading' => __('Language', 'reviews-feed'),
                'description' => __('Change language of what posts are displayed in', 'reviews-feed'),
                'icon' => 'translate',
                'controls' => self::get_language_controls(),
                'separator' => true
            ],
        ];
    }

    /**
     * Get the Sources Section Controls
     *
     * @since 1.0
     *
     * @return array
     */
    protected static function get_sources_controls(){
        return [
            [//Feed Sources
                'type'      => 'feedsources',
                'ajaxAction'    => 'feedFlyPreview',
                'id'        => 'sources'
            ]
        ];
    }
    /**
     * Get the Sort Section Controls
     *
     * @since 1.0
     *
     * @return array
     */
    protected static function get_sort_controls(){
        return [
            [
                'type' => 'separator',
                'top' => 10,
                'bottom' => 20,
            ],
            [
                'type'      => 'switcher',
                'id'        => 'sortByDateEnabled',
                'labelStrong'   => true,
                'layout' => 'third',
                'ajaxAction' => 'feedFlyPreview',
                'condition' => [
                    'sortRandomEnabled' => [false]
                ],
                'label'     => __('By Date', 'reviews-feed'),
                'stacked' => true,
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ]
            ],
            [
                'type'      => 'select',
                'id'        => 'sortByDate',
                'child'     => true,
                'ajaxAction' => 'feedFlyPreview',
                'condition' => [
                    'sortByDateEnabled' => [ true ],
                    'sortRandomEnabled'   => [ false ]
                ],
                'stacked' => true,
                'options' => [
                    'latest' => __( 'Latest Reviews First', 'reviews-feed' ),
                    'oldest' => __( 'Oldest Reviews First', 'reviews-feed' )
                ]
            ],
            [
                'type' => 'separator',
                'top' => 20,
                'condition' => [
                    'sortRandomEnabled' => [false]
                ],
                'bottom' => 20,
            ],
            [
                'type'      => 'switcher',
                'id'        => 'sortByRatingEnabled',
                'labelStrong'   => true,
                'ajaxAction' => 'feedFlyPreview',
                'condition' => [
                    'sortRandomEnabled' => [false]
                ],
                'layout' => 'third',
                'label'     => __('By Rating', 'reviews-feed'),
                'stacked' => true,
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ]
            ],
            [
                'type'      => 'select',
                'id'        => 'sortByRating',
                'ajaxAction' => 'feedFlyPreview',
                'child' => true,
                'condition' => [
                    'sortByRatingEnabled' => [ true ],
                    'sortRandomEnabled'   => [ false ]
                ],
                'stacked' => true,
                'options' => [
                    'highest' => __( 'Highest Rated First', 'reviews-feed' ),
                    'lowest' => __( 'Lowest Rated First', 'reviews-feed' )
                ]
            ],
            [
                'type' => 'separator',
                'top' => 20,
                'condition' => [
                    'sortRandomEnabled' => [false]
                ],
                'bottom' => 20,
            ],

            [
                'type'          => 'switcher',
                'id'            => 'sortRandomEnabled',
                'labelStrong'   => true,
                'ajaxAction'    => 'feedFlyPreview',
                'layout'        => 'third',
                'label'         => __('Randomize', 'reviews-feed'),
                'labelDescription' => __('This will disable “By date” and “By rating” and randomly choose among the filtered reviews', 'reviews-feed'),
                'stacked'       => true,
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ]
            ],
        ];
    }
    /**
     * Get the Filters Section Controls
     *
     * @since 1.0
     *
     * @return array
     */
    protected static function get_filters_controls(){
        return [
            [
                'type'          => 'checkboxsection',
                'id'            => 'filter_reviews_sections',
                'settingId'     => 'includedStarFilters',
                'ajaxAction'    => 'feedFlyPreview',
                'includeTop'    => false,
                'controls'   => [
                    [
                        'id'       => 5,
                        'icon'     => '5stars',
                        'upsellModal' => 'filtersModal',
                        'disableProLabel' => true
                    ],
                    [
                        'id'       => 4,
                        'icon'     => '4stars',
                        'upsellModal' => 'filtersModal',
                        'disableProLabel' => true
                    ],
                    [
                        'id'       => 3,
                        'icon'     => '3stars',
                        'upsellModal' => 'filtersModal',
                        'disableProLabel' => true
                    ],
                    [
                        'id'       => 2,
                        'icon'     => '2stars',
                        'upsellModal' => 'filtersModal',
                        'disableProLabel' => true
                    ],
                    [
                        'id'       => 1,
                        'icon'     => '1stars',
                        'upsellModal' => 'filtersModal',
                        'disableProLabel' => true
                    ]
                ]
            ],
            [//Filter By Words
                'type'      => 'group',
                'id'        => 'filter_bywords',
                'heading'   => __('By Words', 'reviews-feed'),
                'controls'  => [
                    [
                        'type'          => 'textarea',
                        'id'            => 'includeWords',
                        'ajaxAction' => 'feedFlyPreview',
                        'upsellModal' => 'filtersModal',
                        'rows' => 5,
                        'heading'       => __('Only show reviews containing', 'reviews-feed'),
                        'tooltip'       => __('Only show reviews containing', 'reviews-feed'),
                        'placeholder'   => __('Add words here to only show posts containing these words', 'reviews-feed'),
                    ],
                    [
                        'type'          => 'textarea',
                        'id'            => 'excludeWords',
                        'ajaxAction'    => 'feedFlyPreview',
                        'upsellModal' => 'filtersModal',
                        'rows'          => 5,
                        'heading'       => __('Do not show reviews containing', 'reviews-feed'),
                        'tooltip'       => __('Only show reviews containing', 'reviews-feed'),
                        'placeholder'   => __('Add words here to hide any posts containing these words', 'reviews-feed'),
                    ],
                ]
            ],
            /*
            [//Filter By Content
                'type'      => 'group',
                'id'        => 'filter_bycontent',
                'heading'   => __('By Content', 'reviews-feed'),
                'controls'  => [
                    [
                        'type'      => 'checkbox',
                        'id'        => 'filterByImage',
                        'label'   => __('Images', 'reviews-feed'),
                        'options'   => [
                            'enabled' => true,
                            'disabled' => false
                        ]
                    ],
                    [
                        'type'      => 'checkbox',
                        'id'        => 'filterByVideos',
                        'label'   => __('Videos', 'reviews-feed'),
                        'options'   => [
                            'enabled' => true,
                            'disabled' => false
                        ]
                    ],
                ]
            ],
            */
            [//Filter By Length
                'type'      => 'group',
                'id'        => 'filter_bycharcount',
                'heading'   => __('By Character Count', 'reviews-feed'),
                'controls'  => [
                    [
                        'type'          => 'number',
                        'id'            => 'filterCharCountMin',
                        'ajaxAction'    => 'feedFlyPreview',
                        'min'           => 0,
                        'layout'        => 'third',
                        'strongheading' => false,
                        'stacked'       => true,
                        'heading'       => __('Minimum', 'reviews-feed'),
                        'trailingText'  => __('characters', 'reviews-feed'),
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'filterCharCountMax',
                        'ajaxAction'    => 'feedFlyPreview',
                        'min'           => 0,
                        'layout'        => 'third',
                        'strongheading' => false,
                        'stacked'       => true,
                        'heading'       => __('Maxiumum', 'reviews-feed'),
                        'trailingText'  => __('characters', 'reviews-feed'),
                        'placeholder'  => __('Unset', 'reviews-feed'),
                    ],
                ]
            ],
        ];
    }
    /**
     * Get the Moderation Section Controls
     *
     * @since 1.0
     *
     * @return array
     */
    protected static function get_moderation_controls(){
        return [
            [
                'type' => 'separator',
                'top' => 10,
                'bottom' => 20,
            ],
            [
                'type' => 'switcher',
                'id' => 'moderationEnabled',
                'labelStrong' => true,
                'layout' => 'third',
                'ajaxAction' => 'moderationModeStart',
                'label' => __('Enable', 'reviews-feed'),
                'stacked' => true,
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ]
            ],
            [
                'type'      => 'toggleset',
                'id'        => 'moderationType',
                'strongLabel' => true,
                'condition' => [
                    'moderationEnabled' => [ true ]
                ],
                'options'   => [
                    [
                        'value' => 'allow',
                        'label' => __( 'Allow List', 'reviews-feed' ),
                        'description' => __( 'Hides post by default so you can select the ones you want to show', 'reviews-feed' )
                    ],
                    [
                        'value' => 'block',
                        'label' => __( 'Block List', 'reviews-feed' ),
                        'description' => __( 'Show all posts by default so you can select the ones you want to hide', 'reviews-feed' )
                    ]
                ]
            ],
            [
                'type' => 'savemoderation',
                'condition' => [
                    'moderationEnabled' => [ true ]
                ],
            ],
            [
                'type' => 'notice',
                'condition' => [
                    'moderationEnabled' => [ true ]
                ],
                'noticeIcon' => 'notice',
                'noticeHeading' => __( 'You can only view and filter through the last 100 reviews', 'reviews-feed' ),
                'noticeText' => __( 'Due to platform API limitations, we cannot store and provide you with a complete repository of your reviews.', 'reviews-feed' ),
            ],
        ];
    }

    protected static function get_moderation_free_controls(){
        return [
            [
                'type'      => 'toggleset',
                'id'        => 'moderationType',
                'strongLabel' => true,
                'options'   => [
                    [
                        'value' => 'allow',
                        'label' => __( 'Allow List', 'reviews-feed' ),
                        'upsellModal' => 'moderationModal',
                        'description' => __( 'Hides post by default so you can select the ones you want to show', 'reviews-feed' )
                    ],
                    [
                        'value' => 'block',
                        'label' => __( 'Block List', 'reviews-feed' ),
                        'upsellModal' => 'moderationModal',
                        'description' => __( 'Show all posts by default so you can select the ones you want to hide', 'reviews-feed' )
                    ]
                ]
            ],
            [
                'type' => 'savemoderation',
                'upsellModal' => 'moderationModal',
            ]
        ];

    }

    /**
     * Get the Advanced Section Controls
     *
     * @since 1.0
     *
     * @return array
     */
    protected static function get_advanced_controls(){
        return [
            [
                'type'                 => 'heading',
                'heading'             => __('Advanced', 'reviews-feed'),
            ]
        ];
    }

    /**
     * Get the Language Section Controls
     *
     * @since 1.0
     *
     * @return array
     */
    protected static function get_language_controls()
    {
        return [ //Languages & Translation
            [
                'type' => 'list',
                'fullspace' => true,
                'controls' => [
                    [
                        'type' => 'select',
                        'id' => 'localization',
                        'inputLeadingIcon'  => 'translate',
                        'ajaxAction'    => 'feedFlyPreview',
                        'options' => Util::get_translation_languages(true)
                    ],
                    [
                        'type' => 'button',
                        'ajaxAction'    => 'changeSettingValue',
                        'data'  => [
                            'name' => 'localization',
                            'value' => 'default',
                            'feedFlyPreview' => true
                        ],
                        'buttonText' => __('Reset', 'reviews-feed'),
                    ],

                ]
            ],
            [
                'type' => 'notice',
                'noticeIcon' => 'notice',
                'noticeHeading' => __('Limited Support', 'reviews-feed'),
                'noticeText' => __('Only Google supports different languages. Reviews from other providers will still show up in English or the language they were submitted in.', 'reviews-feed'),
            ],
        ];

    }

}
