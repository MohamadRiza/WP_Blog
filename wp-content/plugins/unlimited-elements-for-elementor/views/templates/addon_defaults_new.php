<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$addonTitle = $addon->getTitle();
$addonEditUrl = HelperUC::getViewUrl_EditAddon($addon->getId());
$addonsListUrl = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR);

?>

<div id="uc_addondefaults_wrapper" class="uc-addondefaults-wrapper">

	<h1><?php esc_html_e("Widget Defaults", "unlimited-elements-for-elementor"); ?> - <?php echo esc_html($addonTitle); ?></h1>

	<div class="uc-preview-addon-actions">
		<div class="uc-preview-addon-actions-primary">
			<button
				id="uc_addondefaults_button_save"
				class="unite-button-primary"
				data-text-default="<?php esc_attr_e("Save Defaults", "unlimited-elements-for-elementor"); ?>"
				data-text-loading="<?php esc_attr_e("Saving...", "unlimited-elements-for-elementor"); ?>"
			>
				<?php esc_html_e("Save Defaults", "unlimited-elements-for-elementor"); ?>
			</button>
		</div>
		<div class="uc-preview-addon-actions-secondary">
			<a class="unite-button-secondary" href="<?php echo esc_url($addonEditUrl); ?>">
				<?php esc_html_e("Edit Widget", "unlimited-elements-for-elementor"); ?>
			</a>
			<a class="unite-button-secondary" href="<?php echo esc_url($addonsListUrl); ?>">
				<?php esc_html_e("Back to Widgets", "unlimited-elements-for-elementor"); ?>
			</a>
		</div>
	</div>

	<?php require HelperUC::getPathTemplate("addon_preview"); ?>

</div>

<script>
	jQuery(document).ready(function () {
		var objView = new UniteCreatorAddonDefaultsAdmin();
		objView.init();
	});
</script>
