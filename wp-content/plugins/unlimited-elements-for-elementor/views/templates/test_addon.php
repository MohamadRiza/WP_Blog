<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;


if($this->showHeader)
	$this->putHeaderHtml();
else
	require HelperUC::getPathTemplate("header_missing");

$slot1AddHtml = "";
if($this->isTestData1 == false)
	$slot1AddHtml = "style='display:none'";


$styleShow = "";
$styleHide = "style='display:none'";

$urlBack = HelperUC::getViewUrl_Addons($addonType);
if(!empty($objAddonType->addonView_urlBack))
	$urlBack = $objAddonType->addonView_urlBack;

	$textEditThis = esc_html__("Edit This ", "unlimited-elements-for-elementor"). $this->textSingle;

	$textBackTo = esc_html__("Back To ", "unlimited-elements-for-elementor"). $this->textPlural .esc_html__(" List", "unlimited-elements-for-elementor");

	$urlTestAddonNew = HelperUC::getViewUrl("testaddon", "id={$addonID}");

?>

<div id="uc_testaddon_wrapper" class="uc-testaddon-wrapper">
 
<?php if($this->showToolbar):?>

<div class="uc-testaddon-panel">

		<a href="<?php echo esc_url($urlEditAddon)?>" class="unite-button-secondary" ><?php echo esc_attr($textEditThis)?></a>
		<a class="unite-button-secondary uc-button-cat-sap" href="<?php echo esc_url($urlBack)?>"><?php echo esc_html($textBackTo);?></a>

		<a id="uc_button_preview" href="javascript:void(0)" class="unite-button-secondary" <?php 
				s_echo( $isPreviewMode?$styleHide:$styleShow ); ?>><?php esc_html_e("To Preview", "unlimited-elements-for-elementor")?></a>
		<a id="uc_button_close_preview" href="javascript:void(0)" class="unite-button-secondary" <?php 
				s_echo( $isPreviewMode?$styleShow:$styleHide ); ?>><?php esc_html_e("Hide Preview", "unlimited-elements-for-elementor")?></a>

		<a id="uc_button_preview_tab" href="javascript:void(0)" class="unite-button-secondary uc-button-cat-sap"><?php esc_html_e("Preview New Tab", "unlimited-elements-for-elementor")?></a>

		<a id="uc_button_testaddon_new" href="<?php echo esc_url($urlTestAddonNew); ?>" class="unite-button-secondary uc-button-cat-sap"><?php esc_html_e("Test Widget New", "unlimited-elements-for-elementor")?></a>

		<span id="uc_testaddon_slot1" class="uc-testaddon-slot" <?php 
				s_echo( $slot1AddHtml ); ?>>
			<a id="uc_testaddon_button_restore" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Restore Data", "unlimited-elements-for-elementor")?></a>
			<span id="uc_testaddon_loader_restore" class="loader-text" style="display:none"><?php esc_html_e("loading...", "unlimited-elements-for-elementor")?></span>
			<a id="uc_testaddon_button_delete" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Delete Data", "unlimited-elements-for-elementor")?></a>
			<span id="uc_testaddon_loader_delete" class="loader-text" style="display:none"><?php esc_html_e("deleting...", "unlimited-elements-for-elementor")?></span>
		</span>

		<a id="uc_testaddon_button_save" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Save Data", "unlimited-elements-for-elementor")?></a>
		<span id="uc_testaddon_loader_save" class="loader-text" style="display:none"><?php esc_html_e("saving...", "unlimited-elements-for-elementor")?></span>

		<a id="uc_testaddon_button_clear" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Clear", "unlimited-elements-for-elementor")?></a>

</div>

<?php endif; ?>

<form name="form_test_addon">

<?php

	//put helper editor if needed

	if($isNeedHelperEditor)
		UniteProviderFunctionsUC::putInitHelperHtmlEditor();

    $addonConfig->putHtmlFrame();
?>

</form>

</div>

<script type="text/javascript">

	jQuery(document).ready(function(){

		var objTestAddonView = new UniteCreatorTestAddon();
		objTestAddonView.init();

		<?php if($isPreviewMode == true):?>
		jQuery("#uc_button_preview").trigger("click");
		<?php endif?>

	});


</script>
