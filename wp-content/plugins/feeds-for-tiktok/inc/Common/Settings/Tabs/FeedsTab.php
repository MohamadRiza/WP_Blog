<?php

namespace SmashBalloon\TikTokFeeds\Common\Settings\Tabs;

use Smashballoon\Customizer\V3\SB_SettingsPage_Tab;

if (! defined('ABSPATH')) {
	exit;
}

class FeedsTab extends SB_SettingsPage_Tab
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
			'id'   => 'sb-feeds-tab',
			'name' => __('Feeds', 'feeds-for-tiktok'),
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
			'caching_section' => [
				'type'    => 'caching',
				'heading' => __('Caching', 'feeds-for-tiktok'),
			],
			'gdpr_section'    => [
				'id'        => 'gdpr',
				'type'      => 'select',
				'heading'   => __('GDPR', 'feeds-for-tiktok'),
				'info'      => sprintf(
					__('We will automatically enable GDPR compliance if we detect a supported privacy consent plugin. %1$sLearn more%2$s.', 'feeds-for-tiktok'),
					'<a href="https://smashballoon.com/gdpr-compliant/?tiktok&utm_campaign=tiktok-free&utm_source=settings&utm_medium=gdpr-link" target="_blank" rel="noopener">',
					'</a>'
				),
				'options'   => [
					'auto' => __('Automatic', 'feeds-for-tiktok'),
					'yes'  => __('Yes', 'feeds-for-tiktok'),
					'no'   => __('No', 'feeds-for-tiktok'),
				],
				'separator' => true,
			],
		];
	}
}
