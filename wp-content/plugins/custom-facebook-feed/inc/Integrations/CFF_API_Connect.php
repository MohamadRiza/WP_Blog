<?php

namespace CustomFacebookFeed\Integrations;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Class CFF_API_Connect
 * Connect to the Facebook Graph and make API Calls
 *
 * @since 4.X
 */
class CFF_API_Connect
{
	/**
	 * API url call
	 *
	 * @var string
	 */
	private $url;

	/**
	 * API url response
	 *
	 * @var object
	 */
	private $response;

	/**
	 * API call params
	 *
	 * @var array
	 */
	private $params;

	/**
	 * CFF_API_Connect constructor.
	 *
	 * @param mixed|array|string $connected_account_or_url either the connected account.
	 *  data for this request or the complete url for the request.
	 * @param string $endpoint (optional) is optional only if the complete url is provided.
	 * otherwise is they key for the endpoint needed for the request (ex. "header").
	 * @param array $params (optional) used with the connected account and endpoint to add.
	 *  additional query parameters to the url if needed.
	 * @since 5.0
	 */
	public function __construct($connected_account_or_url, $endpoint = '', $params = array())
	{
		if (is_array($connected_account_or_url) && isset($connected_account_or_url['access_token'])) {
			$this->set_url($connected_account_or_url, $endpoint, $params);
		} elseif (!is_array($connected_account_or_url) && strpos($connected_account_or_url, 'https') !== false) {
			$this->url = $connected_account_or_url;
		} else {
			$this->url = '';
		}
		$this->params = $params;
	}

	/**
	 * If url needs to be generated from the connected account, endpoint,
	 * and params, this function is used to do so.
	 *
	 * @param string $url (API URL).
	 */
	public function set_url_from_args($url)
	{
		$this->url = $url;
	}

	/**
	 * GET API URL
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public function get_url()
	{
		return $this->url;
	}

	/**
	 * If the server is unable to connect to the url, returns true
	 *
	 * @return bool
	 *
	 * @since 5.0
	 */
	public function is_wp_error()
	{
		return is_wp_error($this->response);
	}

	/**
	 * Connect to the Facebook API and record the response
	 *
	 * @since 5.0
	 */
	public function connect()
	{
		if (empty($this->url)) {
			$this->response = array();
			return;
		}
		$args = array(
			'timeout' => 20
		);
		$response = wp_safe_remote_get($this->url, $args);
		$body = json_decode(wp_remote_retrieve_body($response), true);
		$this->response = $response;
		if (is_wp_error($body) || isset($body['error'])) {
			$this->log_fb_error();
		}


	}

	/**
	 * Returns the response data from Facebook
	 *
	 * @return array|object
	 *
	 * @since 5.0
	 */
	public function get_data($only_body = false)
	{
		if ($this->is_wp_error()) {
			return array();
		}
		if (!empty($this->response['body'])) {
			$body = json_decode($this->response['body']);
			return $body;
		} else {
			return $this->response;
		}
	}


	/**
	 * Returns the response data from Facebook
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public function get_json_data()
	{
		return wp_json_encode($this->get_data(), true);
	}

	/**
	 * Returns the response from Facebook
	 *
	 * @return array|object
	 *
	 * @since 5.0
	 */
	public function get_response()
	{
		return $this->response;
	}


	/**
	 * Log Error
	 *
	 * @since 5.0
	 */
	public function log_fb_error()
	{
		delete_option('cff_dismiss_critical_notice');
		$access_token_refresh_errors = array(10, 4, 200);
		$response = json_decode($this->response['body'], true);
		$page_id = isset($this->params['page_id']) ? $this->params['page_id'] : false;
		$api_error_code = $response['error']['code'];
		$ppca_error = false;
		if (strpos($response['error']['message'], 'Public Content Access') !== false) {
			$ppca_error = true;
		}

		if (in_array((int) $api_error_code, $access_token_refresh_errors, true) && !$ppca_error) {
			$pieces = explode('access_token=', $this->url);
			$accesstoken_parts = isset($pieces[1]) ? explode('&', $pieces[1]) : 'none';
			$accesstoken = $accesstoken_parts[0];

			$error = array(
				'accesstoken' => $accesstoken,
				'post_id' => get_the_ID(),
				'errorno' => $api_error_code
			);
			\cff_main()->cff_error_reporter->add_error('accesstoken', $error, $page_id);
		} else {
			\cff_main()->cff_error_reporter->add_error('api', $response, $page_id);
		}
	}
}