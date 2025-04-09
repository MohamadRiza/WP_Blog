<?php
/**
 * SBY Tooltip Wizard
 *
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Builder;

class Tooltip_Wizard {

	/**
	 * Register component
	 *
	 * @since 2.0
	 */
	public function register() {
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
		add_action( 'admin_footer', [ $this, 'output' ] );
	}


	/**
	 * Enqueue assets.
	 *
	 * @since 2.0
	 */
	public function enqueues() {

		wp_enqueue_style(
			'sby_tooltipster',
			SBY_PLUGIN_URL . 'css/tooltipster.css',
			null,
			SBYVER
		);

		wp_enqueue_script(
			'tooltipster',
			SBY_PLUGIN_URL . 'js/jquery.tooltipster.min.js',
			[ 'jquery' ],
			SBYVER,
			true
		);

		wp_enqueue_script(
			'sby-admin-tooltip-wizard',
			SBY_PLUGIN_URL . 'js/tooltip-wizard.js',
			[ 'jquery' ],
			SBYVER
		);

		$wp_localize_data = [];
		if( $this->check_gutenberg_wizard() ){
			$wp_localize_data['sby_wizard_gutenberg'] = true;
		}

		wp_localize_script(
			'sby-admin-tooltip-wizard',
			'sby_admin_tooltip_wizard',
			$wp_localize_data
		);
	}

	/**
	 * Output HTML.
	 *
	 * @since 2.0
	 */
	public function output() {
		if( $this->check_gutenberg_wizard() ){
			$this->gutenberg_tooltip_output();
		}

	}

	/**
	 * Gutenberg Tooltip Output HTML.
	 *
	 * @since 2.0
	 */
	public function check_gutenberg_wizard() {
		global $pagenow;
		return  (	( $pagenow == 'post.php' ) || (get_post_type() == 'page') )
				&& ! empty( $_GET['sby_wizard'] );
	}


	/**
	 * Gutenberg Tooltip Output HTML.
	 *
	 * @since 2.0
	 */
	public function gutenberg_tooltip_output() {
		?>
		<div id="sby-gutenberg-tooltip-content">
			<div class="sby-tlp-wizard-cls sby-tlp-wizard-close"></div>
			<div class="sby-tlp-wizard-content">
				<strong class="sby-tooltip-wizard-head"><?php echo __('Add a Block','feeds-for-youtube') ?></strong>
				<p class="sby-tooltip-wizard-txt"><?php echo __('Click the plus button, search for Feeds for YouTube','feeds-for-youtube'); ?>
                    <br/><?php echo __('Feed, and click the block to embed it.','feeds-for-youtube') ?> <a href="https://smashballoon.com/doc/wordpress-5-block-page-editor-gutenberg/?youtube" rel="noopener" target="_blank" rel="nofollow noopener"><?php echo __('Learn More','feeds-for-youtube') ?></a></p>
				<div class="sby-tooltip-wizard-actions">
					<button class="sby-tlp-wizard-close"><?php echo __('Done','feeds-for-youtube') ?></button>
				</div>
			</div>
		</div>
		<?php
	}
}
