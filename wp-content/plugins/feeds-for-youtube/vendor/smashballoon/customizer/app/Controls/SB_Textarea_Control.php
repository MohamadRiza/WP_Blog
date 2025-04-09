<?php

/**
 * Customizer Builder
 * TextArea Field Control
 *
 * @since 6.0
 */
namespace Smashballoon\Customizer\Controls;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class SB_Textarea_Control extends \Smashballoon\Customizer\Controls\SB_Controls_Base
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
        return 'textarea';
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
		<div class="sb-control-textarea-ctn sbc-fb-fs">
			<textarea class="sb-control-input-textrea sbc-fb-fs" v-model="<?php 
        echo $controlEditingTypeModel;
        ?>[control.id]" :placeholder="control.placeholder ? control.placeholder : ''" @focusout.prevent.default="changeSettingValue(false,false,false, control.ajaxAction ? control.ajaxAction : false)"></textarea>
		</div>
		<?php 
    }
}
