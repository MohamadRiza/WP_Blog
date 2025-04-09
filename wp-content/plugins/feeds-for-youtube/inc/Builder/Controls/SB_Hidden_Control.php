<?php
/**
 * Customizer Builder
 * Hidden Field Control
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Builder\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SB_Hidden_Control extends SB_Controls_Base {

	/**
	 * Get control type.
	 *
	 * Getting the Control Type
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return string
	*/
	public function get_type() {
		return 'hidden';
	}

	/**
	 * Output Control
	 *
	 *
	 * @since 2.0
	 * @access public
	*/
	public function get_control_output( $controlEditingTypeModel ) {
		?>
		<div class="sb-control-input-ctn sby-yt-fs">
			<input type="hidden" v-model="<?php echo $controlEditingTypeModel; ?>[control.id]">
		</div>
		<?php
	}

}
