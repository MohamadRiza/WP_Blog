<?php

/**
 * Service responsible with plugin deactivation functionality.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Services;

use SmashBalloon\TikTokFeeds\Common\Container;

/**
 * Plugin Deactivation Service class.
 */
class DeactivationService
{
	/**
	 * Register.
	 *
	 * @return void
	 */
	public function register()
	{
		register_deactivation_hook(SBTT_PLUGIN_FILE, [ $this, 'deactivate' ]);
	}

	/**
	 * Deactivate.
	 *
	 * @return void
	 */
	public function deactivate()
	{
		wp_clear_scheduled_hook('sbtt_refresh_token_routine');
		wp_clear_scheduled_hook('sbtt_feed_update_routine');
		wp_clear_scheduled_hook('sbtt_usage_tracking_cron');
	}
}
