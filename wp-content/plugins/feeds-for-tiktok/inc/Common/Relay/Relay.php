<?php

namespace SmashBalloon\TikTokFeeds\Common\Relay;

use SmashBalloon\TikTokFeeds\Common\Container;
use SmashBalloon\TikTokFeeds\Common\Services\SettingsManagerService;
use SmashBalloon\TikTokFeeds\Common\Relay\RemoteRequest;
use SmashBalloon\TikTokFeeds\Common\Database\SourcesTable;

class Relay
{
	/**
	 * The API auth token.
	 *
	 * @var string
	 */
	private $api_auth_token = false;

	/**
	 * The endpoints.
	 *
	 * @var array
	 */
	private $endpoints = [
		// POST.
		'auth/register'       => 'POST',
		'auth/license'        => 'POST',
		'auth/encrypt-tokens' => 'POST',
		'refresh/token'       => 'POST',
		'revoke/access'       => 'POST',
		'source/remove'       => 'POST',

		// GET.
		'health'              => 'GET',
		'user/info'           => 'GET',
		'list/videos'         => 'GET',
		'query/videos'        => 'GET',
	];

	/**
	 * The slow endpoints.
	 *
	 * @var array
	 */
	private $slow_endpoints = [
		'user/info',
		'list/videos',
		'auth/license',
	];

	/**
	 * The timeout.
	 *
	 * @var int
	 */
	private $timeout = 10;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$global_settings = Container::get_instance()->get(SettingsManagerService::class)->get_global_settings();

		if (isset($global_settings['api_site_access_token']) && $global_settings['api_site_access_token'] !== '') {
			$this->api_auth_token = $global_settings['api_site_access_token'];
		}
	}

	/**
	 * Calls the endpoint.
	 *
	 * @param string $endpoint The endpoint.
	 * @param array  $args    The arguments.
	 *
	 * @return array|bool
	 */
	public function call($endpoint, $args = [])
	{
		if (! $this->is_valid_endpoint($endpoint)) {
			return false;
		}

		if ($this->requires_token($endpoint) && ! $this->api_auth_token) {
			$registered = $this->register_website();
			if (! $registered && ! $this->api_auth_token) {
				return false;
			}
		}

		$method = $this->get_method($endpoint);
		$this->timeout  = $this->get_endpoint_timeout($endpoint);

		$remote_request = new RemoteRequest($endpoint, $args, $this->api_auth_token);
		$response       = $remote_request->$method($endpoint, $args, $this->timeout);
		$response 	 = json_decode($response, true);

		// If the response is an error, try to refresh the token and try again.
		if (isset($response['data']['error']) && $response['data']['error'] === 'access_token_invalid') {
			$refreshed_token = $this->refresh_token($args);

			if ($refreshed_token) {
				$args['access_token'] = $refreshed_token;

				$remote_request = new RemoteRequest($endpoint, $args, $this->api_auth_token);
				$response       = $remote_request->$method($endpoint, $args);
				$response = json_decode($response, true);
			}
		}

		// TODO:: Add error notices and invalid source errors.
		if (isset($response['data']['error']) && isset($response['data']['message'])) {
			$error_message = sbtt_get_error_message_and_directions($response['data']['message']);

			$error = [
				'success' => false,
				'data' => [
					'error'   => isset($error_message['message']) ? $error_message['message'] : '',
					'directions' => isset($error_message['directions']) ? $error_message['directions'] : '',
				]
			];

			return $error;
		}

		return $response;
	}

	/**
	 * Checks if the endpoint is valid.
	 *
	 * @param string $endpoint The endpoint.
	 *
	 * @return bool
	 */
	private function is_valid_endpoint($endpoint)
	{
		return array_key_exists($endpoint, $this->endpoints);
	}

	/**
	 * Checks if the endpoint requires a token.
	 *
	 * @param string $endpoint The endpoint.
	 *
	 * @return bool
	 */
	private function requires_token($endpoint)
	{
		return $endpoint !== 'auth/register' && $endpoint !== 'auth/encrypt-tokens' && $endpoint !== 'health';
	}

	/**
	 * Gets the method.
	 *
	 * @param string $endpoint The endpoint.
	 *
	 * @return string
	 */
	private function get_method($endpoint)
	{
		return $this->endpoints[ $endpoint ];
	}

	/**
	 * Gets the endpoint timeout.
	 *
	 * @param string $endpoint The endpoint.
	 *
	 * @return int
	 */
	private function get_endpoint_timeout($endpoint)
	{
		return in_array($endpoint, $this->slow_endpoints) ? 120 : $this->timeout;
	}

	/**
	 * Refreshes the token.
	 *
	 * @param array $source_args The source arguments.
	 *
	 * @return string|bool
	 */
	private function refresh_token($source_args)
	{
		$open_id = isset($source_args['open_id']) ? $source_args['open_id'] : '';

		if (! $open_id || ! $this->api_auth_token) {
			return false;
		}

		$source_table = new SourcesTable();
		$source = $source_table->get_source($open_id);

		if (! $source || ! isset($source['refresh_token']) || ! $source['refresh_token']) {
			return false;
		}

		$args = [
			'open_id'       => $open_id,
			'website'       => get_home_url(),
			'refresh_token' => $source['refresh_token'],
		];

		$remote_request = new RemoteRequest('refresh/token', $args, $this->api_auth_token);
		$response       = $remote_request->post('refresh/token', $args);

		if (is_wp_error($response)) {
			return false;
		}

		$response = json_decode($response, true);

		if (isset($response['data']) && $response['data'] && isset($response['data']['access_token'])) {
			$this->update_source($source, $response['data']);

			return $response['data']['access_token'];
		}

		return false;
	}

	/**
	 * Updates the source.
	 *
	 * @param array $source The source.
	 * @param array $data   The data.
	 *
	 * @return void
	 */
	private function update_source($source, $data)
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

	/**
	 * Registers the website.
	 *
	 * @return bool
	 */
	private function register_website()
	{
		$global_settings       = get_option('sbtt_global_settings', []);

		$args = [
			'url'         => get_home_url(),
			'license_key' => isset($global_settings['license_key']) && ! empty($global_settings['license_key']) ? trim($global_settings['license_key']) : '',
		];

		$remote_request = new RemoteRequest('auth/register', $args);
		$response       = $remote_request->post('auth/register', $args);

		if (is_wp_error($response)) {
			return false;
		}

		$response = json_decode($response, true);

		if (isset($response['data']['error']) && $response['data']['error']) {
			$global_settings['api_site_access_token'] = '';
			$global_settings['api_site_error']        = $response['data']['error'];

			update_option('sbtt_global_settings', $global_settings);
			return false;
		}

		if (isset($response['data']) && $response['data'] && isset($response['data']['token'])) {
			$global_settings['api_site_access_token'] = $response['data']['token'];
			$global_settings['api_site_error']        = '';

			update_option('sbtt_global_settings', $global_settings);

			$this->api_auth_token = $response['data']['token'];
			return true;
		}

		return false;
	}
}
