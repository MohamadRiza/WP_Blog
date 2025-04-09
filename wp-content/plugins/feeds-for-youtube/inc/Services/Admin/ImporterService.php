<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use Smashballoon\Customizer\Feed_Saver_Manager;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;

class ImporterService extends ServiceProvider {
	/**
	 * @var Feed_Saver_Manager
	 */
	private $saver_manager;

	public function __construct(Feed_Saver_Manager $saver_manager) {
		$this->saver_manager = $saver_manager;
	}

	public function register() {
		add_action('wp_ajax_sby_do_feed_import', [$this, 'ajax_handle_file_import']);
	}

	public function ajax_handle_file_import() {
		Util::ajaxPreflightChecks();

		$filename = $_FILES['feedFile']['name'];
		$ext      = pathinfo( $filename, PATHINFO_EXTENSION );

		if ( 'json' !== $ext ) {
			wp_send_json_error(['message' => __('Unsupported file type.', 'feeds-for-youtube'), 'success' => false]);
		}

		$imported_settings = file_get_contents( $_FILES['feedFile']['tmp_name'] );

		if ( empty($imported_settings) ) {
			wp_send_json_error(['message' => __('Could not parse file contents.', 'feeds-for-youtube'), 'success' => false]);
		}

		$decoded_settings = json_decode($imported_settings, true);

		$result = $this->saver_manager->import_feed($imported_settings, $decoded_settings['feedName']);

		wp_send_json_success($result);
	}
}