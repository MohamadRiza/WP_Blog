<?php

namespace SmashBalloon\Reviews\Common\Settings\Tabs;

use SmashBalloon\Reviews\Common\Util;
use Smashballoon\Customizer\V2\SB_SettingsPage_Tab;

/**
 * Class Feeds Settings Tab
 *
 * @since 1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SBR_Translation_Tab extends SB_SettingsPage_Tab {

    /**
     * Get the Settings Tab info
     *
     * @since 1.0
     *
     * @return array
     */
    protected function tab_info()
    {
        return [
            'id' => 'sb-translation-tab',
            'name' => __('Language & Translation', 'reviews-feed')
        ];
    }

    /**
    * Get the Settings Tab Section
    *
    * @since 1.0
    *
    * @return array
    */
    protected function tab_sections(){
        return [
            'localization_section' => [
                'id' => 'localization',
                'type' => 'select',
                'heading' => __('Language', 'reviews-feed'),
                'info' => sprintf(
                    __('Change the displayed language for all feeds and its displayed reviews. You can override this for each feed from the individual feed settings.%sLearn more%s <br/>
                    %sNote: Currently only Google support translated reviews.%s', 'reviews-feed'),
                    '<a href="https://smashballoon.com/doc/language-reviews-feed/" target="_blank" rel="noreferrer" >',
                    '</a>',
                    '<span class="sb-notice sb-notice-control sb-notice-default sb-text-tiny" style="padding: 0 12px; margin-top: 7px; width: auto;">',
                    '</span>'
                ),
                'options' => Util::get_translation_languages(),
                'inputLeadingIcon'  => 'translate',
                'separator' => true
            ],
            'translation_section' => [
                'type' => 'translation',
                'id' => 'translations',
                'layout' => 'full',
                'heading' => __('Custom Text/Translate', 'reviews-feed'),
                'description' => __('Enter custom text for the words below, or translate it into the language you would like to use.', 'reviews-feed'),
                'sections' => [
                    [
                        'heading' => __('Dates', 'reviews-feed'),
                        'elements' => [
                            [
                                'id' => 'writeReview',
                                'text' => __('Write a Review', 'reviews-feed'),
                                'description' => __('Used for header “Write a Review” button', 'reviews-feed'),
                            ],
                            [
                                'id' => 'second',
                                'text' => __('second', 'reviews-feed'),
                                'description' => __('Used for “Posted a second ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'seconds',
                                'text' => __('seconds', 'reviews-feed'),
                                'description' => __('Used for “Posted a seconds ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'minute',
                                'text' => __('minute', 'reviews-feed'),
                                'description' => __('Used for “Posted a minute ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'minutes',
                                'text' => __('minutes', 'reviews-feed'),
                                'description' => __('Used for “Posted a minutes ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'hour',
                                'text' => __('hour', 'reviews-feed'),
                                'description' => __('Used for “Posted a hour ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'hours',
                                'text' => __('hours', 'reviews-feed'),
                                'description' => __('Used for “Posted a hours ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'day',
                                'text' => __('day', 'reviews-feed'),
                                'description' => __('Used for “Posted a day ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'days',
                                'text' => __('days', 'reviews-feed'),
                                'description' => __('Used for “Posted a days ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'week',
                                'text' => __('week', 'reviews-feed'),
                                'description' => __('Used for “Posted a week ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'weeks',
                                'text' => __('weeks', 'reviews-feed'),
                                'description' => __('Used for “Posted a weeks ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'month',
                                'text' => __('month', 'reviews-feed'),
                                'description' => __('Used for “Posted a month ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'months',
                                'text' => __('months', 'reviews-feed'),
                                'description' => __('Used for “Posted a months ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'year',
                                'text' => __('year', 'reviews-feed'),
                                'description' => __('Used for “Posted a year ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'years',
                                'text' => __('years', 'reviews-feed'),
                                'description' => __('Used for “Posted a years ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'ago',
                                'text' => __('ago', 'reviews-feed'),
                                'description' => __('Used for “Posted a XXX ago”', 'reviews-feed'),
                            ],
                            [
                                'id' => 'reviewsHeader',
                                'text' => __('Over %s Reviews', 'reviews-feed'),
                                'description' => __('Used for header Reviews number “Over XXX Reviews”, make sure to add %s to be replaced with reviews number.', 'reviews-feed'),
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

}