<?php

namespace SmashBalloon\TikTokFeeds\Common;

class AuthorizationStatusCheck
{
	/**
	 * Statuses
	 *
	 * @var array
	 */
	private $statuses;

	/**
	 * License tiers
	 *
	 * @var array
	 */
	private $license_tiers = [
		"TikTok Feeds Pro Basic"                    => 'basic',
		"TikTok Feeds Pro Plus"                     => 'plus',
		"TikTok Feeds Pro Elite"                    => 'pro',
		"All Access Bundle - All Plugins Unlimited" => 'pro',
	];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$statuses       = get_option('sbtt_statuses', array());
		$this->statuses = is_array($statuses) ? $statuses : array();
	}

	/**
	 * Get statuses
	 *
	 * @return array
	 */
	public function get_statuses()
	{
		$statuses = array(
			'license_info'     => isset($this->statuses['license_info']) ? $this->statuses['license_info'] : array(),
			'license_tier'     => $this->get_license_tier(),
			'update_frequency' => $this->get_update_frequency(),
			'last_cron_update' => ! empty($this->statuses['last_cron_update']) ? $this->statuses['last_cron_update'] : 0,
		);

		return $statuses;
	}

	/**
	 * Get the license tier.
	 *
	 * @return string
	 */
	public function get_license_tier()
	{
		$license_tier = isset($this->statuses['license_tier']) ? $this->statuses['license_tier'] : 'free';

		if (isset($this->statuses['license_info']) && isset($this->statuses['license_info']['item_name'])) {
			$item_name = isset($this->statuses['license_info']['item_name']) ? $this->statuses['license_info']['item_name'] : false;

			$license_tier = $item_name && isset($this->license_tiers[$item_name]) ? $this->license_tiers[$item_name] : 'free';
		}

		return $license_tier;
	}

	/**
	 * How often, in seconds, feeds can be updated based on license tier
	 *
	 * @return float|int
	 */
	public function get_update_frequency()
	{
		$frequency = 24 * HOUR_IN_SECONDS;
		$tier = $this->get_license_tier();

		switch ($tier) {
			case 'basic':
				$frequency = 12 * HOUR_IN_SECONDS;
				break;
			case 'plus':
				$frequency = 6 * HOUR_IN_SECONDS;
				break;
			case 'pro':
				$frequency = 3 * HOUR_IN_SECONDS;
				break;
			default:
				$frequency = 24 * HOUR_IN_SECONDS;
				break;
		}

		return $frequency;
	}

	/**
	 * Update statuses
	 *
	 * @param array $status Statuses.
	 */
	public function update_statuses($status)
	{
		$this->statuses = array_merge($this->statuses, $status);
		update_option('sbtt_statuses', $this->statuses);

		return $this->statuses;
	}
}
