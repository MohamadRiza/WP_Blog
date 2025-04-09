<?php

/**
 * Customizer Builder
 * Toggle Set Control
 *
 * @since 6.0
 */
namespace Smashballoon\Customizer\Controls;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class SB_Toggleset_Control extends \Smashballoon\Customizer\Controls\SB_Controls_Base
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
        return 'toggleset';
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
		<div class="sb-control-toggle-set-ctn sbc-fb-fs">
			<div 
				class="sb-control-toggle-elm sbc-fb-fs sb-tr-2" 
				v-for="toggle in control.options" 
				:data-active="<?php 
        echo $controlEditingTypeModel;
        ?>[control.id] == toggle.value" 
				@click.prevent.default="changeSettingValue(control.id,toggle.value, toggle.checkExtension != undefined ? checkExtensionActive(toggle.checkExtension) : true, control.ajaxAction != undefined ? control.ajaxAction : false)" 
				v-show="toggle.condition != undefined ? checkControlCondition(toggle.condition) : true" 
				:data-disabled="toggle.checkExtension != undefined && (sbyIsPro && sbyLicenseNoticeActive || !sbyIsPro) ? !checkExtensionActive(toggle.checkExtension) : false"
			>
				<div 
					class="sb-control-toggle-extension-cover" 
					v-show="shouldShowTogglesetCover(toggle)" 
					@click.prevent.default="togglesetExtPopup(toggle)"
				></div>
				<div class="sb-control-toggle-deco sb-tr-1"></div>
				<div class="sb-control-toggle-icon" v-if="toggle.icon" v-html="svgIcons[toggle.icon]"></div>
				<div class="sb-control-label">{{toggle.label}}</div>
			</div>
		</div>
		<?php 
    }
}
