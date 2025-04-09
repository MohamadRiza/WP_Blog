<?php

namespace SmashBalloon\TikTokFeeds\Common\Services\Upgrade\Routines;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Relay\Relay;

class RegisterWebsiteRoutine extends ServiceProvider
{
	/**
	 * The target version for this routine.
	 *
	 * @var int
	 */
	protected $target_version = 0;

	/**
	 * Registers the routine.
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->will_run() || $this->force_run()) {
			$this->run();
		}
	}

	/**
	 * Checks if the routine will run.
	 *
	 * @return bool
	 */
	protected function will_run()
	{
		$global_settings       = get_option('sbtt_global_settings', []);

		if (isset($global_settings['api_site_error']) && $global_settings['api_site_error'] !== '') {
			return false;
		}

		return ! isset($global_settings['api_site_access_token']) || $global_settings['api_site_access_token'] === '';
	}

	/**
	 * Checks if the routine should be forced to run.
	 *
	 * @return bool
	 */
	protected function force_run()
	{
		if (isset($_GET['sbtt_force_register']) && $_GET['sbtt_force_register'] === 'true') {
			return true;
		}

		return false;
	}

	/**
	 * Runs the routine.
	 *
	 * @return void
	 */
	public function run()
	{
		$global_settings       = get_option('sbtt_global_settings', []);

		$args = [
			'url'         => get_home_url(),
			'license_key' => isset($global_settings['license_key']) && ! empty($global_settings['license_key']) ? trim($global_settings['license_key']) : '',
		];

		$relay    = new Relay();
		$response = $relay->call('auth/register', $args);

		if (isset($response['data']['error']) && $response['data']['error']) {
			$global_settings['api_site_access_token'] = '';
			$global_settings['api_site_error']        = $response['data']['error'];

			update_option('sbtt_global_settings', $global_settings);
			return;
		}

		if (isset($response['data']) && $response['data'] && isset($response['data']['token'])) {
			$global_settings['api_site_access_token'] = $response['data']['token'];
			$global_settings['api_site_error']        = '';

			update_option('sbtt_global_settings', $global_settings);
		}
	}
}
