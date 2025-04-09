<?php

namespace SmashBalloon\TikTokFeeds\Common\Settings\Tabs;

use Smashballoon\Customizer\V3\SB_SettingsPage_Tab;

if (! defined('ABSPATH')) {
	exit;
}

class CodeSnippetsTab extends SB_SettingsPage_Tab
{
	/**
	 * Get the Code Snippets Tab info
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function tab_info()
	{
		return [
			'id'   => 'sb-code-snippets-tab',
			'name' => __('Code Snippets', 'feeds-for-tiktok'),
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
			'wpcode_snippets_section' => [
				'id'    => 'wpcode_snippets_section',
				'title' => __('WPCode Snippets', 'feeds-for-tiktok'),
				'type'  => 'wpcodeintegration',
				'layout' => 'full'
			]
		];
	}
}
