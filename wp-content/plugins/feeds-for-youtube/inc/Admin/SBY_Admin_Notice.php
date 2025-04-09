<?php
/**
 * SBY Admin Notice.
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Admin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SBY_Admin_Notice {
    
    public function register() {
        add_action( 'sby_admin_header_notices', array( $this, 'header_notices' ) );
        add_action( 'wp_ajax_sby_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );
    }

    /**
     * Header Notices
     *
     * @since 2.0
     */
    public function header_notices() {
        if ( sby_is_pro() ) {
            return;
        }

        $lite_notice_dismissed = get_transient( 'youtube_feed_dismiss_lite' );
        if ( $lite_notice_dismissed ) {
            return;
        }

        $output = '';

        $upgrade_url = 'https://smashballoon.com/youtube-feed/demo/?utm_campaign='. sby_utm_campaign() .'&utm_source=lite-upgrade-bar';
        $output .= '<div id="sbc-notice-bar" class="sbc-header-notice">';
        $output .= sprintf(
            '<span class="sbc-notice-bar-message">%s <a href="%s" target="_blank" rel="noopener noreferrer">%s</a></span>',
            __('You\'re using YouTube Feeds Lite. To unlock more features consider', 'feeds-for-youtube'),
            $upgrade_url,
            __('upgrading to Pro', 'feeds-for-youtube')
        );

        $output .= sprintf(
            '<button type="button" class="sbc-dismiss" id="sbc-dismiss-header-notice" title="%s" data-page="overview">%s</button>',
            __('Dismiss this message', 'feeds-for-youtube'),
            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8327 5.34175L14.6577 4.16675L9.99935 8.82508L5.34102 4.16675L4.16602 5.34175L8.82435 10.0001L4.16602 14.6584L5.34102 15.8334L9.99935 11.1751L14.6577 15.8334L15.8327 14.6584L11.1744 10.0001L15.8327 5.34175Z" fill="white"/></svg>'
        );

        $output .= '</div>';

        echo $output;
    }


    /**
     * Dismiss Upgrade Notice
     *
     * @since 2.0
     */
    public function dismiss_upgrade_notice() {
        // Run a security check.
		check_ajax_referer( 'sby-admin' , 'nonce');

		if ( ! sby_current_user_can( 'manage_youtube_feed_options' ) ) {
			wp_send_json_error();
		}
        // set the transient so it will hide for next 7 days
        set_transient( 'youtube_feed_dismiss_lite', 'dismiss', 2 * WEEK_IN_SECONDS );
        
        wp_send_json_success();
        wp_die();
    }

}