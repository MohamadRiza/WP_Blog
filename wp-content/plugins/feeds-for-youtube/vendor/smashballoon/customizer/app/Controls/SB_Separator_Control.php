<?php

/**
 * Customizer Builder
 * Separator Control
 *
 * @since 6.0
 */
namespace Smashballoon\Customizer\Controls;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class SB_Separator_Control extends \Smashballoon\Customizer\Controls\SB_Controls_Base
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
        return 'separator';
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
			<div class="sb-control-elem-separator sbc-fb-fs" :style="'margin-top:'+ (control.top ? control.top : 0) +'px;margin-bottom:'+ (control.bottom ? control.bottom : 0) +'px;'"></div>
		<?php 
    }
}
