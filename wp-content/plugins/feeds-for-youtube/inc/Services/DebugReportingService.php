<?php

namespace SmashBalloon\YouTubeFeed\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Pro\SBY_Settings_Pro;
use SmashBalloon\YouTubeFeed\SBY_GDPR_Integrations;

class DebugReportingService extends ServiceProvider {
	public function register() {
		add_action( 'sby_before_feed_end', [ $this, 'sby_debug_report' ], 11, 2 );
	}

	public function sby_debug_report( $youtube_feed, $feed_id ) {

		if ( ! isset( $_GET['sb_debug'] ) ) {
			return;
		}

		?>
		<p>Status</p>
		<ul>
			<li>Time: <?php echo date( "Y-m-d H:i:s", time() ); ?></li>
			<?php foreach ( $youtube_feed->get_report() as $item ) : ?>
				<li><?php echo esc_html( $item ); ?></li>
			<?php endforeach; ?>

		</ul>

		<?php
		$feed = $youtube_feed->get_feed_id();
		$atts = array();
		if ( ! empty( $feed ) ) {
			$atts = array( 'feed' => $feed );
		}

		$settings_obj = sby_is_pro_version() ? new SBY_Settings_Pro( $atts, sby_get_database_settings() ) : new SBY_Settings( $atts, sby_get_database_settings() );

		$settings = $settings_obj->get_settings();
		$public_settings_keys = sby_is_pro_version() ? SBY_Settings_Pro::get_public_db_settings_keys() : SBY_Settings::get_public_db_settings_keys();

		?>
		<p>Settings</p>
		<ul>
			<?php foreach ( $public_settings_keys as $key ) : if ( isset( $settings[ $key ] ) ) : ?>
				<li>
					<small><?php echo esc_html( $key ); ?>:</small>
					<?php if ( ! is_array( $settings[ $key ] ) ) :
						echo esc_html( $settings[ $key ] );
					else : ?>
						<ul>
							<?php foreach ( $settings[ $key ] as $sub_key => $value ) {
								echo '<li><small>' . esc_html( $sub_key ). ':</small> '. esc_html( $value ) . '</li>';
							} ?>
						</ul>
					<?php endif; ?>
				</li>

			<?php endif; endforeach; ?>
		</ul>
		<p>GDPR</p>
		<ul>
			<?php
			$statuses = SBY_GDPR_Integrations::statuses();
			foreach ( $statuses as $status_key => $value) : ?>
				<li>
					<small><?php echo esc_html( $status_key ); ?>:</small>
					<?php if ( $value == 1 ) { echo 'success'; } else {  echo 'failed'; } ?>
				</li>

			<?php endforeach; ?>
			<li>
				<small>Enabled:</small>
				<?php echo SBY_GDPR_Integrations::doing_gdpr( $settings ); ?>
			</li>
		</ul>
		<?php
	}

}
