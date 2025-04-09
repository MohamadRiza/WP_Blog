<?php

namespace SmashBalloon\TikTokFeeds\Common\Settings\Tabs;

use Smashballoon\Customizer\V3\SB_SettingsPage_Tab;

if (! defined('ABSPATH')) {
	exit;
}

class GeneralTab extends SB_SettingsPage_Tab
{
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
			'id'   => 'sb-general-tab',
			'name' => __('General', 'feeds-for-tiktok'),
		];
	}

	/**
	 * Get the Settings Tab Section
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function tab_sections()
	{
		return [
			'licensekey_section'       => [
				'type'         => 'licensekey',
				'heading'      => __('License Key', 'feeds-for-tiktok'),
				'isProSetting' => true,
				'description'  => __('Your license key provides access to updates and support', 'feeds-for-tiktok'),
				'separator'    => true,
			],

			'licensekeyfree_section'   => [
				'type'          => 'licensekeyfree',
				'heading'       => __('License Key', 'feeds-for-tiktok'),
				'isFreeSetting' => true,
				'description'   => __('Your license key provides access to updates and support', 'feeds-for-tiktok'),
				'separator'     => true,
			],

			'sources_section'          => [
				'heading'          => __('Manage Sources', 'feeds-for-tiktok'),
				'inputDescription' => __('Add or remove connected TikTok accounts', 'feeds-for-tiktok'),
				'type'             => 'sources',
				'separator'        => true,
			],

			'preservesettings_section' => [
				'id'        => 'preserve_settings',
				'type'      => 'switcher',
				'heading'   => __('Preserve settings if plugin is removed', 'feeds-for-tiktok'),
				'info'      => __('This will make sure that all of your feeds and settings are still saved even if the plugin is uninstalled', 'feeds-for-tiktok'),
				'options'   => [
					'enabled'  => true,
					'disabled' => false,
				],
				'separator' => true,
			],

			'importfeed_section'       => [
				'heading' => __('Import Feed Settings', 'feeds-for-tiktok'),
				'type'    => 'importfeed',
				'info'    => __('You will need a JSON file previously exported from the TikTok Feeds Plugin', 'feeds-for-tiktok'),
			],
			'exportfeed_section'       => [
				'heading'   => __('Export Feed Settings', 'feeds-for-tiktok'),
				'type'      => 'exportfeed',
				'info'      => __('Export settings for one or more of your feeds', 'feeds-for-tiktok'),
				'separator' => true,
			],
		];
	}
}
