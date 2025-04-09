<?php
/**
 * Customizer Builder
 * Toggle Buttons
 *
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Builder\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SB_Togglebutton_Control extends SB_Controls_Base {

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
		return 'togglebutton';
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
		<div class="sb-control-togglebutton-ctn sby-yt-fs">
			<div class="sb-control-togglebutton-elm sby-yt-fs sb-tr-1" v-for="toggle in control.options" :data-active="<?php echo $controlEditingTypeModel; ?>[control.id] == toggle.value" v-show="toggle.condition != undefined ? checkControlCondition(toggle.condition) : true"  @click.prevent.default="changeSettingValue(control.id,toggle.value, true)" >
				{{toggle.label}}
			</div>
		</div>

		<?php
	}

}
