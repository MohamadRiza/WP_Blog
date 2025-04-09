<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;

class ErrorReportingService extends ServiceProvider {

	public function register() {
		add_action( 'sby_before_feed_end', [$this, 'sby_error_report'], 10, 2 );
	}

	/**
	 * Outputs an organized error report for the front end.
	 * This hooks into the end of the feed before the closing div
	 *
	 * @param object $youtube_feed
	 * @param string $feed_id
	 */
	public function sby_error_report( $youtube_feed, $feed_id ) {
		global $sby_posts_manager;

		$style = current_user_can( 'manage_youtube_feed_options' ) ? ' style="display: block;"' : '';

		$error_messages = $sby_posts_manager->get_frontend_errors();
		if ( ! empty( $error_messages ) ) {?>
			<div id="sby_mod_error"<?php echo $style; ?>>
				<span><?php _e('This error message is only visible to WordPress admins', 'feeds-for-youtube' ); ?></span><br />
				<?php if ( isset( $error_messages['accesstoken'] ) ) :
					echo $error_messages['accesstoken'];

					?>
				<?php else: ?>
					<?php foreach ( $error_messages as $error_message ) {
						echo $error_message;
					} ?>
				<?php endif; ?>
			</div>
			<?php
		}

		$sby_posts_manager->reset_frontend_errors();
	}
}