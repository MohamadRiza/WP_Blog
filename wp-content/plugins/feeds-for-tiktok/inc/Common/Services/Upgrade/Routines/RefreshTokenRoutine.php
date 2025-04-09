<?php

namespace SmashBalloon\TikTokFeeds\Common\Services\Upgrade\Routines;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Relay\Relay;
use SmashBalloon\TikTokFeeds\Common\Database\SourcesTable;

class RefreshTokenRoutine extends ServiceProvider
{
	/**
	 * The cron interval to use for the refresh token update routine.
	 *
	 * @var string
	 */
	private $cron_interval = 'daily';

	/**
	 * Represents a routine for updating refresh tokens.
	 */
	public function register()
	{
		if (! wp_next_scheduled('sbtt_refresh_token_routine')) {
			wp_schedule_event(time(), $this->cron_interval, 'sbtt_refresh_token_routine');
		}
		add_action('sbtt_refresh_token_routine', [ $this, 'refresh_token' ]);
	}

	/**
	 * Refreshes the refresh tokens.
	 *
	 * @return void
	 */
	public function refresh_token()
	{
		$sources = $this->get_sources();

		if (! $sources) {
			return;
		}

		foreach ($sources as $source) {
			$this->call_refresh_token_endpoint($source);
		}
	}

	/**
	 * Gets the sources.
	 *
	 * @return array
	 */
	public function get_sources()
	{
		$source_table = new SourcesTable();
		$sources      = $source_table->get_sources();
		return $sources;
	}

	/**
	 * Calls the refresh token endpoint.
	 *
	 * @param array $source The source to update.
	 *
	 * @return void
	 */
	public function call_refresh_token_endpoint($source)
	{
		if (! isset($source['open_id']) || ! $source['open_id']) {
			return;
		}

		$args = [
			'open_id'       => isset($source['open_id']) ? $source['open_id'] : '',
			'refresh_token' => isset($source['refresh_token']) ? $source['refresh_token'] : '',
			'website'       => get_home_url(),
		];

		$relay    = new Relay();
		$response = $relay->call('refresh/token', $args);

		if (isset($response['data']) && $response['data'] && isset($response['data']['access_token'])) {
			$this->update_source($source, $response['data']);
		}
	}

	/**
	 * Updates the source.
	 *
	 * @param array $source The source to update.
	 * @param array $data The data to update the source with.
	 *
	 * @return void
	 */
	public function update_source($source, $data)
	{
		$source['access_token']    = isset($data['access_token']) ? sanitize_text_field(wp_unslash($data['access_token'])) : '';
		$source['refresh_token']   = isset($data['refresh_token']) ? sanitize_text_field(wp_unslash($data['refresh_token'])) : '';
		$source['expires']         = isset($data['expires_in']) ? date('Y-m-d H:i:s', time() + $data['expires_in']) : '';
		$source['refresh_expires'] = isset($data['refresh_expires_in']) ? date('Y-m-d H:i:s', time() + $data['refresh_expires_in']) : '';
		$source['open_id']         = isset($data['open_id']) ? sanitize_text_field(wp_unslash($data['open_id'])) : '';
		$source['scope']           = isset($data['scope']) ? sanitize_text_field(wp_unslash($data['scope'])) : '';

		// Update or insert the source.
		$source_table = new SourcesTable();
		$source_table->update_or_insert($source);
	}
}
