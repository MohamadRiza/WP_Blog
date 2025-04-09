<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Data\DataFactory;
use SmashBalloon\YouTubeFeed\Data\GoogleAPIResponseStruct;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\SBY_API_Connect;
use SmashBalloon\YouTubeFeed\SBY_Settings;

class SourcesService extends ServiceProvider {

	/**
	 * @var SBY_Settings
	 */
	private $settings;
	/**
	 * @var SBY_API_Connect
	 */
	private $connect;
	/**
	 * @var GoogleAPIResponseStruct
	 */
	private $api_verification_data;
	/**
	 * @var DataFactory
	 */
	private $data_factory;

	public function __construct(SBY_Settings $settings, SBY_API_Connect $connect, DataFactory $data_factory) {
		$this->settings = $settings;
		$this->connect = $connect;
		$this->data_factory = $data_factory;

		$this->api_verification_data = $this->data_factory->create(GoogleAPIResponseStruct::class);

	}

	public function register() {
		add_action('wp_ajax_remove_connected_account', [$this, 'ajax_remove_account']);
		add_action('wp_ajax_verify_api_key', [$this, 'ajax_verify_api_key']);

	}
	
	public function ajax_remove_account() {
		Util::ajaxPreflightChecks();

		$account  = trim( sanitize_text_field( $_POST['account_id'] ) );
		$settings = $this->settings->get_settings();
		$connected_accounts = $settings['connected_accounts'];
		if ( ! empty( $account ) && ! empty( $connected_accounts ) && array_key_exists( $account,
				$connected_accounts ) ) {
			unset( $connected_accounts[ $account ] );
			$settings['connected_accounts'] = $connected_accounts;
			$this->settings->update_settings( $settings );
		}
	}

	public function ajax_verify_api_key() {
		Util::ajaxPreflightChecks();

		$settings = $this->settings->get_settings();
		$api_key = sanitize_text_field($_POST['api_key']);

		if ( empty( $api_key ) ) {
			$this->respond_to_api_update($settings, $api_key);
		}

		$this->connect->set_url(null, null, ['username' => 'smashballoon'], $api_key);
		$response = wp_remote_get($this->connect->get_url(), $this->connect->get_args());

		$this->api_verification_data->response = $response['response'];
		$this->api_verification_data->data = json_decode($response['body']);
		$this->api_verification_data->status = $response['response']['code'] === 200;

		$this->respond_to_api_update($settings, $api_key);

		wp_send_json_error();
	}

	private function respond_to_api_update($settings, $api_key) {
		update_option( 'sby_api_key_verification', $this->api_verification_data );

		$settings['api_key'] = $api_key;
		$this->settings->update_settings( $settings );

		wp_send_json_success( $this->api_verification_data );
	}
}