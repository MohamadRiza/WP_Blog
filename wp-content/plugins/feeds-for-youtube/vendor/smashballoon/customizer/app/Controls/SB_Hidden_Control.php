<?php

/**
 * Customizer Builder
 * Hidden Field Control
 *
 * @since 6.0
 */
namespace Smashballoon\Customizer\Controls;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class SB_Hidden_Control extends \Smashballoon\Customizer\Controls\SB_Controls_Base
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
        return 'hidden';
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
			<input type="hidden" v-model="<?php 
        echo $controlEditingTypeModel;
        ?>[control.id]">
		</div>
		<?php 
    }
}
