<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UniteCreatorTestAddonNewView{

	/**
	 * constructor
	 */
	public function __construct(){

		$this->putHtml();
	}

	/**
	 * put html
	 */
	private function putHtml(){

		$addonID = UniteFunctionsUC::getGetVar("id", "", UniteFunctionsUC::SANITIZE_ID);

		$addon = new UniteCreatorAddon();
		$addon->initByID($addonID);

		$addonTitle = $addon->getTitle();
		$isTestData1 = $addon->isTestDataExists(1);

		$addonEditUrl = HelperUC::getViewUrl_EditAddon($addon->getId());
		$addonsListUrl = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR);

		?>

		<h1><?php esc_html_e("Widget Preview", "unlimited-elements-for-elementor"); ?> - <?php echo esc_html($addonTitle); ?></h1>

		<div class="uc-preview-addon-actions">
			<div class="uc-preview-addon-actions-primary">

				<button
					id="uc_testaddon_button_save"
					class="unite-button-secondary"
					data-text-default="<?php esc_attr_e("Save", "unlimited-elements-for-elementor"); ?>"
					data-text-loading="<?php esc_attr_e("Saving...", "unlimited-elements-for-elementor"); ?>"
				>
					<?php esc_html_e("Save", "unlimited-elements-for-elementor"); ?>
				</button>
				<button
					id="uc_testaddon_button_restore"
					class="unite-button-secondary"
					<?php echo ($isTestData1 === false ? 'style="display:none"' : ""); ?>
					data-text-default="<?php esc_attr_e("Restore", "unlimited-elements-for-elementor"); ?>"
					data-text-loading="<?php esc_attr_e("Restoring...", "unlimited-elements-for-elementor"); ?>"
				>
					<?php esc_html_e("Restore", "unlimited-elements-for-elementor"); ?>
				</button>
				<button
					id="uc_testaddon_button_delete"
					class="unite-button-secondary"
					<?php echo ($isTestData1 === false ? 'style="display:none"' : ""); ?>
					data-text-default="<?php esc_attr_e("Delete", "unlimited-elements-for-elementor"); ?>"
					data-text-loading="<?php esc_attr_e("Deleting...", "unlimited-elements-for-elementor"); ?>"
				>
					<?php esc_html_e("Delete", "unlimited-elements-for-elementor"); ?>
				</button>

				<span>|</span>

				<button id="uc_testaddon_button_clear" class="unite-button-secondary">
					<?php esc_html_e("Clear", "unlimited-elements-for-elementor"); ?>
				</button>
				<button id="uc_testaddon_button_check" class="unite-button-secondary">
					<?php esc_html_e("Check", "unlimited-elements-for-elementor"); ?>
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

		<script>
			jQuery(document).ready(function () {
				var objView = new UniteCreatorTestAddonNew();
				objView.init();
			});
		</script>

		<?php
	}

}

new UniteCreatorTestAddonNewView();
