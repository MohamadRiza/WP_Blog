<?php

namespace SmashBalloon\Reviews\Common\Integrations;

use SmashBalloon\Reviews\Common\Exceptions\RelayResponseException;
use SmashBalloon\Reviews\Common\Helpers\SBR_Error_Handler;
use SmashBalloon\Reviews\Common\Services\SettingsManagerService;

class SBRelay
{
	public const BASE_URL = SBR_RELAY_BASE_URL;
	/**
	 * @var string|null
	 */
	private $access_token;

	/**
	 * A list of endpoints that needs a bigger timeout
	 *
	 * @var array
	 */
	private $slow_endpoints;

	public function __construct(SettingsManagerService $settings)
	{
		$saved_settings = $settings->get_settings();

		if (isset($saved_settings['access_token'])) {
			$this->access_token = $saved_settings['access_token'];
		}

		$this->slow_endpoints = [
			'auth/license',
			'sources/trustpilot',
			'reviews/trustpilot'.
			'sources/wordpress.org',
			'reviews/wordpress.org',
			'sources/yelp',
			'reviews/yelp',
			'sources/google',
			'reviews/google'
		];
	}

	/**
	 * @param string $endpoint
	 * @param array $data
	 * @param string $method
	 * @param bool $require_auth
	 * @return void
	 *
	 * @throws RelayResponseException
	 */
	public function call(string $endpoint, array $data, string $method = 'POST', bool $require_auth = false): array
	{
		$url = $this->format_url($endpoint);

		$headers = [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json'
		];
		if (true === $require_auth) {
			$headers['Authorization'] = 'Bearer ' . $this->access_token;
		}

		switch ($method) {
			case 'GET':
				$callback = 'wp_remote_get';
				break;
			default:
				$callback = 'wp_remote_post';
				break;
		}

		if (isset($data['language'])
			&& (
				empty($data['language'])
				|| $data['language'] === 'default'
				|| $data['language'] === null
			)
		) {
			unset($data['language']);
		}

		$data = $this->apply_new_google_args($endpoint, $data);

		$args = [
			'method' => $method,
			'headers' => $headers,
			'body' => $method === 'POST' ? json_encode($data) : $data
		];

		if (in_array($endpoint, $this->slow_endpoints)) {
			$args['timeout'] = 120;
		}

		$response = $callback($url, $args);

		$body = !is_wp_error($response)
			? json_decode(wp_remote_retrieve_body($response), true)
			: [];

		//Log API Error
		if (false === $body['success'] && !empty($body['data']['id'])) {
			$body['data']['endpoint'] = $url;
			SBR_Error_Handler::log_error($body['data']);
			return !empty($body['data']) ? $body['data'] : $body;
		}

		return $body !== null ? $body : [];
	}

	private function flatten_errors($errors)
	{
		if (is_array($errors)) {
			$mapped_errors = array_column($errors, 0);

			return implode(', ', $mapped_errors);
		}
		return $errors;
	}

	private function format_url($endpoint, $query = []): string
	{
		$query_string = http_build_query($query);
		return self::BASE_URL . stripslashes($endpoint) . '/' . $query_string;
	}

	/**
	 * @return string|null
	 */
	public function getAccessToken(): ?string
	{
		return $this->access_token;
	}

	/**
	 * @param string|null $access_token
	 */
	public function setAccessToken(?string $access_token): void
	{
		$this->access_token = $access_token;
	}

	/**
	 * Summary of apply_new_google_args
	 *
	 * @param mixed $endpoint
	 * @param mixed $data
	 *
	 * @return array
	 */
	public function apply_new_google_args($endpoint, $data)
	{
		if (strpos($endpoint, 'google') !== false) {

			$api_keys = get_option('sbr_apikeys', []);

			if (
				!empty($api_keys['googleApiType'])
				&& !empty($data['place_id'])
				&& $data['place_id'] !== 'XXX'
			) {
				$data['api_type'] = $api_keys['googleApiType']; // Only add google type if we are getting new source or new reviews
			}
		}

		return $data;
	}
}