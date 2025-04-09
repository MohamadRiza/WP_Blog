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

class SBR_General_Tab extends SB_SettingsPage_Tab {

    /**
     * Get the Settings Tab info
     *
     * @since 1.0
     *
     * @return array
     */
    protected function tab_info(){
        return [
            'id' => 'sb-general-tab',
            'name' => __( 'General', 'reviews-feed' )
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
            'licensekey_section' => [
                'type' => 'licensekey',
                'heading' => __('License Key', 'reviews-feed'),
                'isProSetting' => true,
                'description' => __('Your license key provides access to updates and support', 'reviews-feed'),
                'separator' => true
            ],

            'licensekeyfree_section' => [
                'type' => 'licensekeyfree',
                'heading' => __('License Key', 'reviews-feed'),
                'isFreeSetting' => true,
                'description' => __('Your license key provides access to updates and support', 'reviews-feed'),
                'separator' => true
            ],

            'sources_section' => [
                'heading' => __('Manage Sources', 'reviews-feed'),
                'inputDescription' => __('Add or remove connected accounts', 'reviews-feed'),
                'type' => 'sources',
            ],
            'apikeys_section' => [
                'heading' => __('Manage API Keys', 'reviews-feed'),
                'type' => 'apikeys',
                'inputDescription' => __('Update or modify an API key', 'reviews-feed'),
                'separator' => true
            ],

            'preservesettings_section' => [
                'id'        => 'preserve_settings',
                'type'      => 'switcher',
                'heading'   => __('Preserve settings if plugin is removed', 'reviews-feed'),
                'info'      => __('This will make sure that all of your feeds and settings are still saved even if the plugin is uninstalled', 'reviews-feed'),
                'options' => [
                    'enabled' => true,
                    'disabled' => false
                ],
                'separator' => true
            ],

            'importfeed_section' => [
                'heading' => __('Import Feed Settings', 'reviews-feed'),
                'type' => 'importfeed',
                'info'      => __('You will need a JSON file previously exported from the Reviews Feed Plugin','reviews-feed')
            ],
            'exportfeed_section' => [
                'heading' => __('Export Feed Settings', 'reviews-feed'),
                'type' => 'exportfeed',
                'info'      => __('Export settings for one or more of your feeds','reviews-feed'),
                'separator' => true
            ],

            'importcollection_section' => [
                'heading' => __('Import Collection', 'reviews-feed'),
                'type' => 'importcollection',
                'info'      => __('You will need a JSON file previously exported from the collections Reviews Feed Plugin','reviews-feed')
            ],
            'exportcollection_section' => [
                'heading' => __('Export Collection', 'reviews-feed'),
                'type' => 'exportcollection',
                'info'      => __('Export collection reviews','reviews-feed'),
                'separator' => true
            ],
        ];
    }
}
