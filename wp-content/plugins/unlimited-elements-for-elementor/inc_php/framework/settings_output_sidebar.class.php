<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UniteSettingsOutputSidebarUC extends UniteCreatorSettingsOutput{

	private $showSapTitle = true;
	private $isAccordion = true;
	private $accordionItemsSpaceBetween = 0;//space between accordion items

	/**
	 * constuct function
	 */
	public function __construct(){

		self::$serial++;

		$this->isSidebar = true;
		$this->isParent = true;
		$this->wrapperID = "unite_settings_sidebar_output_" . self::$serial;
		$this->settingsMainClass = "unite-settings-sidebar";
		$this->showDescAsTips = false;

		$this->setShowSaps(true, self::SAPS_TYPE_ACCORDION);
	}

	/**
	 * draw before settings row
	 */
	protected function drawSettings_before(){

		parent::drawSettings_before();

		?>
		<ul class="unite-list-settings">
		<?php
	}

	/**
	 * draw wrapper end after settings
	 */
	protected function drawSettingsAfter(){

		?>
		</ul>
		<?php

		parent::drawSettingsAfter();
	}

	/**
	 * get options override (add accordion space)
	 */
	protected function getOptions(){

		$arrOptions = parent::getOptions();
		$arrOptions["accordion_sap"] = $this->accordionItemsSpaceBetween;

		return $arrOptions;
	}

	/**
	 * set draw options before draw
	 */
	protected function setDrawOptions(){

		$numSaps = $this->settings->getNumSaps();

		if($numSaps <= 1)
			$this->showSapTitle = false;
	}

	/**
	 * draw responsive picker
	 */
	private function drawResponsivePicker($selectedType){

		$devices = array(
			"desktop" => array(
				"title" => __("Desktop", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 10"><path d="M3.5 10.5h5M6 7.5v3M11.5.5H.5v7h11v-7Z" /></svg>',
			),
			"tablet" => array(
				"title" => __("Tablet", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 12"><path d="M2.5 9.5h5M8.5.5h-7a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1Z" /></svg>',
			),
			"mobile" => array(
				"title" => __("Mobile", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 12"><path d="M2.5 9.5h3M6.5.5h-5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1Z" /></svg>',
			),
		);

		?>
		<select class="unite-responsive-picker">
			<?php foreach($devices as $type => $device): ?>
				<option
					value="<?php echo esc_attr($type); ?>"
					data-content="<?php echo esc_attr('<div class="unite-responsive-picker-item uc-tip" title="' . esc_attr($device["title"]) . '" data-tipsy-gravity="w">' . $device["icon"] . '</div>'); ?>"
					<?php if($type === $selectedType) { echo "selected"; } ?>
				>
					<?php echo esc_html($device["title"]); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * draw settings row
	 */
	protected function drawSettingRow($setting, $mode = ""){

		$addAttr = "";
		$baseClass = "unite-setting-row";

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$type = UniteFunctionsUC::getVal($setting, "type");
		$text = UniteFunctionsUC::getVal($setting, "text");
		$description = UniteFunctionsUC::getVal($setting, "description");

		$toDrawText = true;

		$attribsText = UniteFunctionsUC::getVal($setting, "attrib_text");
		if(empty($attribsText) && empty($text))
			$toDrawText = false;

		$labelBlock = UniteFunctionsUC::getVal($setting, "label_block");
		$labelBlock = UniteFunctionsUC::strToBool($labelBlock);

		if($labelBlock === false)
			$baseClass .= " unite-inline-setting";

		$isResponsive = UniteFunctionsUC::getVal($setting, "is_responsive");
		$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
		$responsiveId = UniteFunctionsUC::getVal($setting, "responsive_id");
		$responsiveType = UniteFunctionsUC::getVal($setting, "responsive_type");

		if($isResponsive === true)
			$addAttr .= " data-responsive-id=\"$responsiveId\" data-responsive-type=\"$responsiveType\"";

		$tabsId = UniteFunctionsUC::getVal($setting, "tabs_id");
		$tabsValue = UniteFunctionsUC::getVal($setting, "tabs_value");

		if (empty($tabsId) === false && empty($tabsValue) === false)
			$addAttr .= " data-tabs-id=\"$tabsId\" data-tabs-value=\"$tabsValue\"";

		$rowClass = $this->drawSettingRow_getRowClass($setting, $baseClass);

		?>
		<li
			id="<?php echo esc_attr($id); ?>_row"
			<?php 
				s_echo($rowClass); ?>
			<?php 
				s_echo($addAttr); ?>
			data-name="<?php echo esc_attr($name); ?>"
			data-type="<?php echo esc_attr($type); ?>"
		>

			<div class="unite-setting-field">

				<?php if($toDrawText === true): ?>
					<div class="unite-setting-text-wrapper">
						<div id="<?php echo esc_attr($id); ?>_text" class='unite-setting-text' <?php 
				s_echo($attribsText); ?>>
							<?php echo esc_html($text); ?>
						</div>
						<?php if($isResponsive === true): ?>
							<?php $this->drawResponsivePicker($responsiveType); ?>
						<?php endif; ?>
					</div>
				<?php endif ?>

				<?php if(!empty($addHtmlBefore)): ?>
					<div class="unite-setting-addhtmlbefore"><?php 
				s_echo($addHtmlBefore); ?></div>
				<?php endif; ?>

				<div class="unite-setting-input">
					<?php $this->drawInputs($setting); ?>
				</div>

			</div>

			<?php if(!empty($description)): ?>
				<div class="unite-setting-helper">
					<?php 
					s_echo($description); 
					?>
				</div>
			<?php endif; ?>

		</li>

		<?php
	}

	/**
	 * draw text row
	 */
	protected function drawTextRow($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$label = UniteFunctionsUC::getVal($setting, "label");
		$text = UniteFunctionsUC::getVal($setting, "text");
		$classAdd = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_CLASSADD);
		$isHeading = UniteFunctionsUC::getVal($setting, "is_heading");
		$isHeading = UniteFunctionsUC::strToBool($isHeading);

		if($isHeading === true)
			$classAdd .= " unite-settings-static-text__heading";

		$rowClass = $this->drawSettingRow_getRowClass($setting);

		?>
		<li id="<?php echo esc_attr($id) ?>_row" <?php 
				s_echo($rowClass); ?>>

			<?php if(empty($label) === false): ?>
				<span class="unite-settings-text-label">
					<?php echo esc_html($label) ?>
				</span>
			<?php endif ?>

			<span class="unite-settings-static-text<?php echo esc_attr($classAdd); ?>">
				<?php echo esc_html($text); ?>
			</span>

		</li>
		<?php
	}

	/**
	 * draw sap before override
	 */
	protected function drawSapBefore($sap, $key){
		
		$tab = UniteFunctionsUC::getVal($sap, "tab");
		$name = UniteFunctionsUC::getVal($sap, "name");
		$text = UniteFunctionsUC::getVal($sap, "text");
		//$classIcon = UniteFunctionsUC::getVal($sap, "icon");
		$classIcon = null; // disable icon for now
		$isHidden = UniteFunctionsUC::getVal($sap, "hidden");
		$isHidden = UniteFunctionsUC::strToBool($isHidden);

		if(empty($tab) === true)
			$tab = UniteSettingsUC::TAB_CONTENT;

		if(empty($name) === true)
			$name = "unnamed_" . UniteFunctionsUC::getRandomString();

		$class = "unite-postbox";

		if(empty($this->addClass) === false)
			$class .= " " . $this->addClass;

		if($this->isAccordion === false)
			$class .= " unite-no-accordion";

		if($isHidden === true)
			$class .= " unite-setting-hidden";

		$id = $this->idPrefix . "ucsap_" . $name;


		?>
		<div
			id="<?php echo esc_attr($id) ?>"
			class="<?php echo esc_attr($class); ?>"
			data-tab="<?php echo esc_attr($tab); ?>"
		>

			<?php if($this->showSapTitle === true): ?>
				<div class="unite-postbox-title">

					<?php if(empty($classIcon) === false): ?>
						<i class="unite-postbox-icon <?php echo esc_attr($classIcon); ?>"></i>
					<?php endif; ?>

					<span><?php echo esc_html($text); ?></span>

					<?php if($this->isAccordion === true): ?>
						<div class="unite-postbox-arrow"></div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

			<div class="unite-postbox-inside">
		<?php
	}

	/**
	 * draw sap after
	 */
	protected function drawSapAfter(){

		?>
			</div>
		</div>
		<?php
	}

	/**
	 * draw hr row
	 */
	protected function drawHrRow($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");

		$rowClass = $this->drawSettingRow_getRowClass($setting);

		?>
		<li id="<?php echo esc_attr($id) ?>_row" <?php 
				s_echo($rowClass) ?>>
			<hr id="<?php echo esc_attr($id) ?>">
		</li>
		<?php
	}

}
