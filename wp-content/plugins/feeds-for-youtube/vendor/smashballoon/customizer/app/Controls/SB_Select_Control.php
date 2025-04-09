<?php

/**
 * Customizer Builder
 * Select Field Control
 *
 * @since 6.0
 */
namespace Smashballoon\Customizer\Controls;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class SB_Select_Control extends \Smashballoon\Customizer\Controls\SB_Controls_Base
{
    /**
     * Get control type.
     *
     * Getting the Control Type
     *
     * @since 6.0
     * @access public
     *
     * @return string
     */
    public function get_type()
    {
        return 'select';
    }
    /**
     * Output Control
     *
     *
     * @since 6.0
     * @access public
     */
    public function get_control_output($controlEditingTypeModel)
    {
        ?>
		<div class="sb-control-input-ctn sbc-fb-fs">
			<select class="sb-control-input sbc-fb-fs" v-model="<?php 
        echo $controlEditingTypeModel;
        ?>[control.id]" @change.prevent.default="changeSettingValue(control.id,false,false, control.ajaxAction ? control.ajaxAction : false)">
				<option v-for="(opName, opValue) in control.options" :value="opValue">{{opName}}</option>
			</select>
		</div>
		<?php 
    }
}
