<?php

namespace SmashBalloon\Reviews\Common\Settings\Tabs;

use Smashballoon\Customizer\V2\SB_SettingsPage_Tab;

/**
 * Class General Settings Tab
 *
 * @since 1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SBR_Advanced_Tab extends SB_SettingsPage_Tab {

    /**
     * Get the Settings Tab info
     *
     * @since 1.0
     *
     * @return array
     */
    protected function tab_info(){
        return [
            'id' => 'sb-advanced-tab',
            'name' => __( 'Advanced', 'reviews-feed' )
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
            'optimizeimages_section' => [
                'id'        => 'optimize_images',
                'type'      => 'switcher',
                'heading'   => __('Optimize Images', 'reviews-feed'),
                'info'      => __( /* translators: "It" refers to the optimize images feature */ 'It will create multiple local copies of images in different sizes and use smallest size based on where the image is being displayed', 'reviews-feed'),
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ],
                'ajaxButton'    => [
                    'icon'      => 'reset',
                    'text' => __('Reset', 'reviews-feed'),
                    'action'    => 'sbr_reset_posts',
                    'notification' => [
                        'success' => [
                            'icon' => 'success',
                            'text' => __('Images Cleared', 'reviews-feed')
                        ]
                    ]
                ]
            ],
            /*
            'reseterrorlogs_section' => [
                'heading'       => __('Reset Error Log', 'reviews-feed'),
                'info'          => __('Clear all errors stored in the error log.', 'reviews-feed'),
                'type'      => 'button',
                'ajaxButton' => [
                    'icon'      => 'reset',
                    'text'      => __('Reset', 'reviews-feed'),
                    'action'    => 'sbr_reset_errors',
                    'notification' => [
                        'success' => [
                            'icon' => 'success',
                            'text' => __('Error Log Cleared', 'reviews-feed')
                        ]
                    ]
                ]
            ],
             */
            'usagetracking_section' => [
                'id'        => 'usagetracking',
                'type'      => 'switcher',
                'heading'   => __('Usage Tracking', 'reviews-feed'),
                'info'      => sprintf(
                /* translators: %s: Opening and closing anchor HTML tags */
                __('This helps us prevent plugin and theme conflicts by sending a report in the background once per week about your settings and relevant site stats. It does not send sensitive information like access tokens, email addresses, or user info. This will not affect your site performace as well. %sLearn More%s', 'reviews-feed'),
                    '<a href="https://smashballoon.com/doc/usage-tracking-reviews/" target="_blank" rel="noopener noreferrer">', '</a>' ),
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ],
                'separator' => true
            ],

            'enquejs_section' => [
                'id'        => 'enqueue_js_in_header',
                'type'      => 'switcher',
                'heading'   => __('Enqueue JavaScript in head', 'reviews-feed'),
                'info'      => __('Add the JavaScript file for the plugin in the header instead of the footer', 'reviews-feed'),
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ]
            ],
            /*
            'adminnoticeerror_section' => [
                'id'        => 'admin_error_notices',
                'type'      => 'switcher',
                'heading'   => __('Admin Error Notice', 'reviews-feed'),
                'info'      => __('This will disable or enable the feed error notice that displays in the bottom right corner for admins on the front end of your site.', 'reviews-feed'),
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ]
            ],
            'feedissuereports_section' => [
                'id'        => 'feed_issue_reports',
                'type'      => 'switcher',
                'heading'   => __('Feed Issue Email Reports', 'reviews-feed'),
                'info'      => __('If the feed is down due to a critical issue, we will switch to a cached version and notify you based on these settings. View Documentation', 'reviews-feed'),
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ],
                'separator' => true
            ],
             */

        ];
    }
}