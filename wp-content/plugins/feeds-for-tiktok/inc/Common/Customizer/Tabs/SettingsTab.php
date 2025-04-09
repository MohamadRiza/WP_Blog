<?php

/**
 * Settings Tab
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Customizer\Tabs;

use Smashballoon\Customizer\V3\SB_Sidebar_Tab;
use SmashBalloon\TikTokFeeds\Common\Utils;
use SmashBalloon\TikTokFeeds\Common\AuthorizationStatusCheck;

class SettingsTab extends SB_Sidebar_Tab
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
			'id'   => 'sb-settings-tab',
			'name' => __('Settings', 'feeds-for-tiktok'),
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
			'sources_section' => [
				'heading'  => __('Sources', 'feeds-for-tiktok'),
				'icon'     => 'sourcesadd',
				'controls' => self::get_sources_controls(),
			],
			'sort_section'    => [
				'heading'  => __('Sort', 'feeds-for-tiktok'),
				'icon'     => 'sorting',
				'controls' => self::get_sort_controls(),
				'id'       => 'sort',
			],
			'filters_section' => [
				'heading'     => __('Filters', 'feeds-for-tiktok'),
				'icon'        => 'filter',
				'description' => Utils::sbtt_is_pro() ? '' :
				sprintf(
					__('Upgrade to Pro to apply filters to your TikTok Feeds. %1$sLearn More%2$s', 'feeds-for-tiktok'),
					'<a>',
					'</a>'
				),
				'upsellModal' => 'filtersModal',
				'controls'    => self::get_filters_controls(),
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
	protected static function get_sources_controls()
	{
		return [
			[// Feed Sources.
				'type'       => 'feedsources',
				'ajaxAction' => 'feedFlyPreview',
				'id'         => 'sources',
			],
		];
	}

	/**
	 * Get the Sort Section Controls
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected static function get_sort_controls()
	{
		$plugin_status = new AuthorizationStatusCheck();
		$statuses = $plugin_status->get_statuses();

		$license_tier = isset($statuses['license_tier']) ? $statuses['license_tier'] : 'free';
		$sort_options = [
			'latest' => __('Recent First', 'feeds-for-tiktok'),
			'oldest' => __('Oldest First', 'feeds-for-tiktok'),
		];

		if ($license_tier === 'pro') {
			$sort_options['views'] = __('Most Views First', 'feeds-for-tiktok');
			$sort_options['likes'] = __('Most Likes First', 'feeds-for-tiktok');
		}

		return [
			[
				'type'   => 'separator',
				'top'    => 10,
				'bottom' => 20,
			],
			[
				'type'       => 'select',
				'id'         => 'sortFeedsBy',
				'heading'    => __('Sort By', 'feeds-for-tiktok'),
				'ajaxAction' => 'feedFlyPreview',
				'condition'  => [
					'sortRandomEnabled' => [ false ],
				],
				'stacked'    => true,
				'options'    => $sort_options,
				'upsellModal' => 'sortModal',
			],
			[
				'type'      => 'separator',
				'top'       => 20,
				'condition' => [
					'sortRandomEnabled' => [ false ],
				],
				'bottom'    => 20,
			],

			[
				'type'             => 'switcher',
				'id'               => 'sortRandomEnabled',
				'labelStrong'      => true,
				'ajaxAction'       => 'feedFlyPreview',
				'layout'           => 'third',
				'label'            => __('Randomize', 'feeds-for-tiktok'),
				'labelDescription' => __('This will disable “By date” and “By views/likes” and randomly choose among the filtered feeds', 'feeds-for-tiktok'),
				'stacked'          => true,
				'options'          => [
					'enabled'  => true,
					'disabled' => false,
				],
				'upsellModal' => 'randomSortModal',
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
	protected static function get_filters_controls()
	{
		return [
			[// Filter By Words.
				'type'     => 'group',
				'id'       => 'filter_bywords',
				'heading'  => __('By Words', 'feeds-for-tiktok'),
				'controls' => [
					[
						'type'        => 'textarea',
						'id'          => 'includeWords',
						'ajaxAction'  => 'feedFlyPreview',
						'upsellModal' => 'filtersModal',
						'rows'        => 5,
						'heading'     => __('Only show posts containing', 'feeds-for-tiktok'),
						'tooltip'     => __('Only show posts containing', 'feeds-for-tiktok'),
						'placeholder' => __('Add words here to only show posts containing these words', 'feeds-for-tiktok'),
					],
					[
						'type'        => 'textarea',
						'id'          => 'excludeWords',
						'ajaxAction'  => 'feedFlyPreview',
						'upsellModal' => 'filtersModal',
						'rows'        => 5,
						'heading'     => __('Do not show posts containing', 'feeds-for-tiktok'),
						'tooltip'     => __('Only show posts containing', 'feeds-for-tiktok'),
						'placeholder' => __('Add words here to hide any posts containing these words', 'feeds-for-tiktok'),
					],
				],
			],
		];
	}
}
