<?php

namespace SmashBalloon\TikTokFeeds\Common\Relay;

class RemoteRequest
{
	/**
	 * The base URL.
	 *
	 * @var string
	 */
	private const BASE_URL = SBTT_API_BASE_URL;

	/**
	 * The API auth token.
	 *
	 * @var string
	 */
	private $api_auth_token = '';

	/**
	 * The endpoint.
	 *
	 * @var string
	 */
	private $endpoint       = '';

	/**
	 * The headers.
	 *
	 * @var array
	 */
	private $headers        = [];

	/**
	 * Constructor.
	 *
	 * @param string $endpoint The endpoint.
	 * @param array  $args    The arguments.
	 * @param string $token  The API auth token.
	 *
	 * @return void
	 */
	public function __construct($endpoint, $args = [], $token = false)
	{
		$this->endpoint       = $endpoint;
		$this->api_auth_token = $token;
		$this->set_header();
	}

	/**
	 * Sets the header.
	 *
	 * @return void
	 */
	public function set_header()
	{
		$headers = [
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];

		if ($this->api_auth_token) {
			$headers['Authorization'] = 'Bearer ' . $this->api_auth_token;
		}

		$this->headers = $headers;
	}

	/**
	 * Gets the method.
	 *
	 * @param string $endpoint The endpoint.
	 * @param array  $params    The arguments.
	 * @param int    $timeout  The timeout.
	 *
	 * @return string
	 */
	public function get($endpoint, $params = [], $timeout = 10)
	{
		$url = self::BASE_URL . $endpoint;

		if (! empty($params)) {
			$url .= '?' . http_build_query($params);
		}

		$args = array(
			'headers' => $this->headers,
			'timeout' => $timeout,
		);

		$response = wp_remote_get($url, $args);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			return $error_message;
		}

		return wp_remote_retrieve_body($response);
	}

	/**
	 * Posts the request.
	 *
	 * @param string $endpoint The endpoint.
	 * @param array  $data     The data.
	 * @param int    $timeout  The timeout.
	 *
	 * @return string|bool
	 */
	public function post($endpoint, $data = [], $timeout = 10)
	{
		$url = self::BASE_URL . $endpoint;

		$args = array(
			'headers' => $this->headers,
			'body'    => json_encode($data),
			'timeout' => $timeout,
		);

		$response = wp_remote_post($url, $args);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			return $error_message;
		}

		return wp_remote_retrieve_body($response);
	}
}
