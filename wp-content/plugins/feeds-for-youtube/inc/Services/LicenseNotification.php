<?php

namespace SmashBalloon\YouTubeFeed\Services;

use SmashBalloon\YouTubeFeed\Services\Admin\LicenseService;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use Smashballoon\Customizer\Feed_Builder;
use Smashballoon\Customizer\DB;

class LicenseNotification extends ServiceProvider {

	protected $db;

	public function __construct() {
		$this->db = new DB();
	}

	public function register() {
		add_action( 'wp_footer', [$this, 'sby_frontend_license_error'], 300 );
		add_action( 'wp_ajax_sby_hide_frontend_license_error', [$this, 'hide_frontend_license_error'], 10 );
		add_action( 'wp_ajax_sby_recheck_connection', array( $this, 'sby_recheck_connection' ) );
	}

	/**
	 * Hide the frontend license error message for a day
	 * 
	 * @since 2.0.3
	 */
	public function hide_frontend_license_error() {
		check_ajax_referer( 'sby_nonce' , 'nonce');

		set_transient( 'sby_license_error_notice', true, DAY_IN_SECONDS );

		wp_die();
	}

    public function sby_frontend_license_error() {
        // Don't do anything for guests.
        if ( ! is_user_logged_in() ) {
            return;
        }
        if ( ! sby_is_pro() ) {
            return;
		}
		if ( !current_user_can( Util::sby_capability_check() ) ) {
			return;
		}
		// Check that the license exists and the user hasn't already clicked to ignore the message
		if ( empty( Util::get_license_key() ) ) {
			$this->sby_frontend_license_error_content( 'inactive' );
			return;
		}
		// If license not expired then return;
		if ( !Util::is_license_expired() ) {
			return;
		}
		if ( Util::is_license_grace_period_ended( true ) ) {
			$this->sby_frontend_license_error_content();
		}
		return;
    }

    /**
     * Output frontend license error HTML content
     * 
     * @since 2.0.2
     */
	public function sby_frontend_license_error_content( $license_state = 'expired' ) {
            $icons = sby_builder_pro()->builder_svg_icons(); 

			$feeds_count = $this->db->feeds_count();
			if ( $feeds_count <= 0 ) {
				return;
			}

			$should_display_license_error_notice = get_transient( 'sby_license_error_notice' );
			if ( $should_display_license_error_notice ) {
				return;
			}
        ?>
            <div id="sby-fr-ce-license-error" class="sby-critical-error sby-frontend-license-notice sby-ce-license-<?php echo $license_state; ?>">
                <div class="sby-fln-header">
                    <span class="sb-left">
                        <?php echo $icons['eye2']; ?>
                        <span class="sb-text">Only Visible to WordPress Admins</span>
                    </span>
                    <span id="sby-frce-hide-license-error" class="sb-close"><?php echo $icons['times']; ?></span>
                </div>
                <div class="sby-fln-body">
                    <?php echo $icons['instagram']; ?>
                    <div class="sby-fln-expired-text">
                        <p>
                            <?php 
                                printf( 
                                    __( 'Your YouTube Feeds Pro license key %s', 'feeds-for-youtube' ), 
                                    $license_state == 'expired' ? 'has ' . $license_state : 'is ' . $license_state
                                ); 
                            ?>
                            <a href="<?php echo $this->get_renew_url( $license_state ); ?>">Resolve Now <?php echo $icons['chevronRight']; ?></a>
                        </p>
                    </div>
                </div>
            </div>
        <?php
	}

	/**
	 * SBY Re-Check License
	 *
	 * @since 2.2.0
	 */
	public function sby_recheck_connection() {
		// Do the form validation
		$license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
		if ( empty( $license_key ) ) {
			wp_send_json_error();
		}

		// make the remote license check API call
		$sby_license_data = Util::sby_check_license( $license_key );

		// update options data
		$license_changed = Util::update_recheck_license_data( $sby_license_data );

		// send AJAX response back
		wp_send_json_success(
			array(
				'license'        => $sby_license_data->license,
				'licenseChanged' => $license_changed,
			)
		);
	}

	/**
	 * SBY Get Renew License URL
	 *
	 * @since 2.0
	 *
	 * @return string $url
	 */
	public function get_renew_url( $license_state = 'expired' ) {
		global $sby_download_id;
		if ( $license_state == 'inactive' ) {
			return admin_url('admin.php?page=youtube-feed-settings');
		}
		$license_key = get_option( 'sby_license_key' ) ? get_option( 'sby_license_key' ) : null;

		$url = sprintf(
			'https://smashballoon.com/checkout/?edd_license_key=%s&download_id=%s&utm_campaign=youtube-pro&utm_source=expired-notice&utm_medium=renew-license',
			$license_key,
			$sby_download_id
		);

		return $url;
	}

}