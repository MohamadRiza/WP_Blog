<?php

namespace SmashBalloon\TikTokFeeds\Common\Settings\Tabs;

use Smashballoon\Customizer\V3\SB_SettingsPage_Tab;

if (! defined('ABSPATH')) {
	exit;
}

class AdvancedTab extends SB_SettingsPage_Tab
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
			'id'   => 'sb-advanced-tab',
			'name' => __('Advanced', 'feeds-for-tiktok'),
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
			'optimizeimages_section' => [
				'id'         => 'optimize_images',
				'type'       => 'switcher',
				'heading'    => __('Optimize Images', 'feeds-for-tiktok'),
				'info'       => __('It will create multiple local copies of images in different sizes and use smallest size based on where the image is being displayed', 'feeds-for-tiktok'),
				'options'    => [
					'enabled'  => true,
					'disabled' => false,
				],
				'ajaxButton' => [
					'icon'         => 'reset',
					'text'         => __('Reset', 'feeds-for-tiktok'),
					'action'       => 'sbtt_reset_posts_images',
					'notification' => [
						'success' => [
							'icon' => 'success',
							'text' => __('Images Cleared', 'feeds-for-tiktok'),
						],
					],
				],
			],

			'usagetracking_section'  => [
				'id'        => 'usagetracking',
				'type'      => 'switcher',
				'heading'   => __('Usage Tracking', 'feeds-for-tiktok'),
				'info'      => sprintf(
					__('This helps us prevent plugin and theme conflicts by sending a report in the background once per week about your settings and relevant site stats. It does not send sensitive information like access tokens, email addresses, or user info. This will not affect your site performace as well. %1$sLearn More%2$s', 'feeds-for-tiktok'),
					'<a href="https://smashballoon.com/doc/usage-tracking-tiktok-feeds/" target="_blank" rel="noopener">',
					'</a>'
				),
				'options'   => [
					'enabled'  => true,
					'disabled' => false,
				],
				'separator' => true,
			],

		];
	}
}
