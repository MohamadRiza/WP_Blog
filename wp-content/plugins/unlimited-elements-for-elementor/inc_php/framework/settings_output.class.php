<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UniteSettingsOutputUCWork extends HtmlOutputBaseUC{

	protected static $arrIDs = array();

	protected $arrSettings = array();
	protected $settings;
	protected $formID;

	protected static $serial = 0;

	protected $showDescAsTips = false;
	protected $wrapperID = "";
	protected $idPrefix;
	protected $addCss = "";
	protected $settingsMainClass = "";
	protected $isParent = false;    //variable that this class is parent
	protected $isSidebar = false;

	const INPUT_CLASS_NORMAL = "unite-input-regular";
	const INPUT_CLASS_NUMBER = "unite-input-number";
	const INPUT_CLASS_ALIAS = "unite-input-alias";
	const INPUT_CLASS_LONG = "unite-input-long";
	const INPUT_CLASS_SMALL = "unite-input-small";

	//saps related variables

	protected $showSaps = false;
	protected $sapsType = null;
	protected $activeSap = 0;

	private $subSettingsDialogs = array();

	const SAPS_TYPE_INLINE = "saps_type_inline";  //inline sapts type
	const SAPS_TYPE_CUSTOM = "saps_type_custom";  //custom saps tyle
	const SAPS_TYPE_ACCORDION = "saps_type_accordion";


	/**
	 *
	 * init the output settings
	 */
	public function init(UniteSettingsUC $settings){

		if($this->isParent == false)
			UniteFunctionsUC::throwError("The output class must be parent of some other class.");

		$this->settings = new UniteSettingsUC();
		$this->settings = $settings;

		$this->idPrefix = $settings->getIDPrefix();

	}


	/**
	 * validate that the output class is inited with settings
	 */
	protected function validateInited(){
		if(empty($this->settings))
			UniteFunctionsUC::throwError("The output class not inited. Please call init() function with some settings class");
	}


	/**
	 * set add css. work with placeholder
	 * [wrapperid]
	 */
	public function setAddCss($css){

		$replace = "#".$this->wrapperID;
		$this->addCss = str_replace("[wrapperid]", $replace, $css);
	}

	/**
	 *
	 * set show descriptions as tips true / false
	 */
	public function setShowDescAsTips($show){
		$this->showDescAsTips = $show;
	}


	/**
	 *
	 * show saps true / false
	 */
	public function setShowSaps($show = true, $type = null){

		if($type === null)
			$type = self::SAPS_TYPE_INLINE;

		$this->showSaps = $show;


		switch($type){
			case self::SAPS_TYPE_CUSTOM:
			case self::SAPS_TYPE_INLINE:
			case self::SAPS_TYPE_ACCORDION:
			break;
			default:
				UniteFunctionsUC::throwError("Wrong saps type: $type ");
			break;
		}

		$this->sapsType = $type;

	}


	/**
	 * get default value add html
	 */
	protected function getDefaultAddHtml($setting, $implodeArray = false){

		$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");

		if(is_array($defaultValue) || is_object($defaultValue))
			$defaultValue = json_encode($defaultValue);

		$value = UniteFunctionsUC::getVal($setting, "value"); 

		if(is_array($value) || is_object($value)){
			if($implodeArray === true)
				$value = implode(",", $value);
			else
				$value = json_encode($value);
		}

		echo ' data-default="' . esc_attr($defaultValue) . '" data-initval="' . esc_attr($value) . '" ';

		$this->getGroupSelectorAddAttr($setting);
		$this->getSelectorsAddAttr($setting);

		$addParams = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDPARAMS);

		if(!empty($addParams))
			echo " " . esc_attr($addParams);
	}



	/**
	 * prepare draw setting text
	 */
	protected function drawSettingRow_getText($setting){

		//modify text:
		$text = UniteFunctionsUC::getVal($setting, "text", "");

		if(empty($text))
			return("");

		// prevent line break (convert spaces to nbsp)
		$text = str_replace(" ","&nbsp;",$text);

		/*
		switch($setting["type"]){
			case UniteSettingsUC::TYPE_CHECKBOX:
				$text = "<label for='".$setting["id"]."' style='cursor:pointer;'>$text</label>";
				break;
		}
		*/

		return($text);
	}


	/**
	 *
	 * get text style
	 */
	protected function drawSettingRow_getTextStyle($setting){

		//set text style:
		$textStyle = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_TEXTSTYLE);

		if($textStyle != "")
			$textStyle = "style='".$textStyle."'";

		return($textStyle);
	}


	/**
	 * get row style
	 */
	protected function drawSettingRow_getRowHiddenClass($setting){

		$type = UniteFunctionsUC::getVal($setting, "type");
		$isHidden = UniteFunctionsUC::getVal($setting, "hidden");
		$isHidden = UniteFunctionsUC::strToBool($isHidden);

		if($this->showSaps === true && $this->sapsType === self::SAPS_TYPE_INLINE){
			$sap = UniteFunctionsUC::getVal($setting, "sap");
			$sap = (int)$sap;

			if($sap !== $this->activeSap)
				$isHidden = true;
		}

		$class = "";

		if($isHidden === true){
			if($type === UniteSettingsUC::TYPE_HIDDEN)
				$class = "unite-hidden"; // just hide
			else
				$class = "unite-setting-hidden"; // exclude from values/selectors
		}

		return $class;
	}


	/**
	 *
	 * get row class
	 */
	protected function drawSettingRow_getRowClass($setting, $basClass = ""){

		//set text class:
		$class = $basClass;

		if(isset($setting["disabled"])){
			if(!empty($class))
				$class .= " ";

			$class .= "setting-disabled";
		}

		//add saps class
		if($this->showSaps && $this->sapsType == self::SAPS_TYPE_INLINE){

			$sap = UniteFunctionsUC::getVal($setting, "sap");
			$sap = (int)$sap;
			$sapClass = "unite-sap-element unite-sap-".$sap;

			if(!empty($class))
				$class .= " ";

			$class .= $sapClass;
		}

		$showin = UniteFunctionsUC::getVal($setting, "showin");
		if(!empty($showin)){
			if(!empty($class))
				$class .= " ";

			$class .= "uc-showin-{$showin}";
		}

		$classHidden = $this->drawSettingRow_getRowHiddenClass($setting);
		if(!empty($classHidden)){

			if(!empty($class))
				$class .= " ";

			$class .= $classHidden;
		}

		if(!empty($class))
			$class = "class='{$class}'";


		return($class);
	}




	/**
	* draw after body additional settings accesories
	*/
	public function drawAfterBody(){
		$arrTypes = $this->settings->getArrTypes();
		foreach($arrTypes as $type){
			switch($type){
				case self::TYPE_COLOR:
					?>
						<div id='divPickerWrapper' style='position:absolute;display:none;'><div id='divColorPicker'></div></div>
					<?php
				break;
			}
		}
	}


	/**
	 *
	 * do some operation before drawing the settings.
	 */
	protected function prepareToDraw(){

		$this->settings->setSettingsStateByControls();
		$this->settings->setPairedSettings();

	}


	/**
	 * get setting class attribute
	 */
	protected function getInputClassAttr($setting, $defaultClass="", $addClassParam="", $wrapClass = true){

		$class = UniteFunctionsUC::getVal($setting, "class", $defaultClass);
		$classAdd = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_CLASSADD);

		switch($class){
			case "alias":
				$class = self::INPUT_CLASS_ALIAS;
			break;
			case "long":
				$class = self::INPUT_CLASS_LONG;
			break;
			case "normal":
				$class = self::INPUT_CLASS_NORMAL;
			break;
			case "number":
				$class = self::INPUT_CLASS_NUMBER;
			break;
			case "small":
				$class = self::INPUT_CLASS_SMALL;
			break;
			case "nothing":
				$class = "";
			break;
		}

		if(!empty($classAdd)){
			if(!empty($class))
				$class .= " ";
			$class .= $classAdd;
		}

		if(!empty($addClassParam)){
			if(!empty($class))
				$class .= " ";
			$class .= $addClassParam;
		}

		$isTransparent = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_MODE_TRANSPARENT);
		if(!empty($isTransparent)){
			if(!empty($class))
				$class .= " ";
			$class .= "unite-setting-transparent";
		}

		if(!empty($class) && $wrapClass == true)
			$class = "class='$class'";

		return($class);
	}

	/**
	 * draw link input
	 */
	protected function drawLinkInput($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$value = UniteFunctionsUC::getVal($setting, "value");

		if(is_string($value) === true)
			$value = array("url" => $value);

		$class = $this->getInputClassAttr($setting, "", "unite-setting-link");
		$urlValue = UniteFunctionsUC::getVal($value, "url");

		$externalId = "$id-external";
		$externalChecked = UniteFunctionsUC::getVal($value, "is_external");
		$externalChecked = $externalChecked === "on";

		$nofollowId = "$id-nofollow";
		$nofollowChecked = UniteFunctionsUC::getVal($value, "nofollow");
		$nofollowChecked = $nofollowChecked === "on";

		$attributesId = "$id-attributes";
		$attributesValue = UniteFunctionsUC::getVal($value, "custom_attributes");

		?>
		<div class="unite-setting-link-wrapper">

			<div class="unite-setting-link-field">
				<input
					id="<?php echo esc_attr($id); ?>"
					type="text"
					name="<?php echo esc_attr($name); ?>"
					value="<?php echo esc_attr($urlValue); ?>"
					placeholder="<?php esc_attr_e("Search or enter URL", "unlimited-elements-for-elementor"); ?>"
					data-settingtype="link"
					<?php 
					s_echo($class); ?>
					<?php $this->getDefaultAddHtml($setting); ?>
				/>
				<button
					class="unite-setting-link-toggle unite-setting-button uc-tip"
					type="button"
					title="<?php esc_attr_e("Options", "unlimited-elements-for-elementor"); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12">
						<path d="M11.5 6.9V5.1l-1.668-.185a3.989 3.989 0 0 0-.355-.857l1.049-1.311-1.273-1.273-1.311 1.049a3.989 3.989 0 0 0-.857-.355L6.9.5H5.1l-.185 1.668a3.989 3.989 0 0 0-.857.355L2.747 1.474 1.474 2.747l1.049 1.311a3.989 3.989 0 0 0-.355.857L.5 5.1v1.8l1.668.185a3.989 3.989 0 0 0 .355.857L1.474 9.253l1.273 1.272 1.311-1.048a3.989 3.989 0 0 0 .857.355L5.1 11.5h1.8l.185-1.668a3.989 3.989 0 0 0 .857-.355l1.311 1.048 1.273-1.272-1.049-1.311a3.989 3.989 0 0 0 .355-.857Z" />
						<circle cx="6" cy="6" r="1.5" />
					</svg>
				</button>
				<div class="unite-setting-link-autocomplete">
					<div class="unite-setting-link-autocomplete-loader">
						<?php esc_html_e("Loading...", "unlimited-elements-for-elementor"); ?>
					</div>
					<div class="unite-setting-link-autocomplete-items"></div>
				</div>
			</div>

			<div class="unite-setting-link-options unite-settings-exclude">
				<div class="unite-setting-link-option">
					<div class="unite-setting-link-checkbox">
						<input
							id="<?php echo esc_attr($externalId); ?>"
							class="unite-setting-link-external"
							type="checkbox"
							<?php 
							echo ($externalChecked ? "checked" : ""); 
							?>
						/>
						<label for="<?php echo esc_attr($externalId); ?>">
							<?php esc_html_e("Open in new window", "unlimited-elements-for-elementor"); ?>
						</label>
					</div>
					<div class="unite-setting-link-checkbox">
						<input
							id="<?php echo esc_attr($nofollowId); ?>"
							class="unite-setting-link-nofollow"
							type="checkbox"
							<?php 
							echo ($nofollowChecked ? "checked" : ""); 
							?>
						/>
						<label for="<?php echo esc_attr($nofollowId); ?>">
							<?php esc_html_e("Add nofollow", "unlimited-elements-for-elementor"); ?>
						</label>
					</div>
				</div>
				<div class="unite-setting-link-option">
					<div class="unite-setting-text-wrapper">
						<div class="unite-setting-text">
							<?php esc_html_e("Custom attributes", "unlimited-elements-for-elementor"); ?>
						</div>
					</div>
					<input
						id="<?php echo esc_attr($attributesId); ?>"
						class="unite-setting-link-attributes"
						type="text"
						value="<?php echo esc_attr($attributesValue); ?>"
						placeholder="<?php echo esc_attr("key|value"); ?>"
					/>
					<div class="unite-setting-helper">
						<?php esc_html_e("Set custom attributes for the link element. Separate attribute keys from values using the | (pipe) character. Separate key-value pairs with a comma.", "unlimited-elements-for-elementor"); ?>
					</div>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * modify image setting
	 */
	protected function modifyImageSetting($setting){

		$urlBase = UniteFunctionsUC::getVal($setting, "url_base", null);

		$keys = array("value", "default_value");

		foreach($keys as $key){
			$value = UniteFunctionsUC::getVal($setting, $key);
			$value = trim($value);

			if(empty($value) === false && is_numeric($value) === false)
				$value = HelperUC::URLtoFull($value, $urlBase);

			$setting[$key] = $value;
		}

		return $setting;
	}

	/**
	 * draw image input
	 */
	protected function drawImageInput($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$value = UniteFunctionsUC::getVal($setting, "value");
		$title = UniteFunctionsUC::getVal($setting, "text");
		$source = UniteFunctionsUC::getVal($setting, "source");
		$error = UniteFunctionsUC::getVal($setting, "error");

		if(is_array($value) === false){
			$value = array(
				"id" => is_numeric($value) === true ? $value : null,
				"url" => is_numeric($value) === false ? $value : null,
			);
		}

		$imageId = UniteFunctionsUC::getVal($value, "id");
		$imageUrl = UniteFunctionsUC::getVal($value, "url");
		$imageSize = UniteFunctionsUC::getVal($value, "size", "full");

		if(empty($imageId) === false)
			$imageUrl = UniteProviderFunctionsUC::getImageUrlFromImageID($imageId, $imageSize);
		else
			$imageUrl = HelperUC::URLtoFull($imageUrl);

		$setting["value"] = $value; // for initval

		$previewStyle = "";

		if(empty($imageUrl) === false)
			$previewStyle .= "background-image:url('$imageUrl');";

		if(empty($previewStyle) === false)
			$previewStyle = "style=\"$previewStyle\"";

		$urlName = UniteFunctionsUC::getVal($setting, "url_name");
		// translators: %s is a string
		$urlTitle = sprintf(__("%s URL", "unlimited-elements-for-elementor"), $title);
		$urlValue = $imageUrl;

		$sizes = UniteFunctionsWPUC::getArrThumbSizes();
		$sizeName = UniteFunctionsUC::getVal($setting, "size_name");
		// translators: %s is a string
		$sizeTitle = sprintf(__("%s Size", "unlimited-elements-for-elementor"), $title);
		$sizeValue = $imageSize;

		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-setting-image unite-setting-input-object unite-settings-exclude"
			data-settingtype="image"
			data-name="<?php echo esc_attr($name); ?>"
			<?php 
			$this->getDefaultAddHtml($setting);
			if(empty($source) === false) {
				echo ' data-source="' . esc_attr($source) . '"';
			}
			if(empty($imageId) === false) {
				echo ' data-image-id="' . esc_attr($imageId) . '"';
			}
			?>
		>

			<div class="unite-setting-image-preview" <?php 
				s_echo($previewStyle); ?>>
				<div class="unite-setting-image-placeholder">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
						<path d="M4.281 10.313V0h1.75v10.313h-1.75ZM0 6.03v-1.75h10.313v1.75H0Z" />
					</svg>
				</div>
				<div class="unite-setting-image-actions">
					<button class="unite-setting-image-choose unite-setting-button" type="button">
						<?php esc_html_e("Choose Image", "unlimited-elements-for-elementor"); ?>
					</button>
					<button class="unite-setting-image-clear unite-setting-button" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 13 13">
							<path d="M1 3h11M5 3V1h3v2M10.5 5l-.401 5.607A1.5 1.5 0 0 1 8.603 12H4.394a1.5 1.5 0 0 1-1.496-1.394L2.5 5" />
						</svg>
					</button>
				</div>
			</div>

			<?php if(empty($error) === false): ?>
				<div class="unite-setting-image-error unite-setting-error">
					<?php echo esc_html($error); ?>
				</div>
			<?php endif; ?>

			<div class="unite-setting-image-section unite-inline-setting">
				<div class="unite-setting-field">
					<div class="unite-setting-text-wrapper">
						<div class="unite-setting-text">
							<?php echo esc_html($urlTitle); ?>
						</div>
					</div>
					<div class="unite-setting-input">
						<input
							class="unite-setting-image-url"
							type="text"
							name="<?php echo esc_attr($urlName); ?>"
							value="<?php echo esc_attr($urlValue); ?>"
							placeholder="<?php esc_attr_e("Image URL", "unlimited-elements-for-elementor"); ?>"
						/>
					</div>
				</div>
			</div>

			<div class="unite-setting-image-section unite-inline-setting">
				<div class="unite-setting-field">
					<div class="unite-setting-text-wrapper">
						<div class="unite-setting-text">
							<?php echo esc_html($sizeTitle); ?>
						</div>
					</div>
					<div class="unite-setting-input">
						<select
							class="unite-setting-image-size"
							name="<?php echo esc_attr($sizeName); ?>"
						>
							<?php foreach($sizes as $size => $label): ?>
								<option
									value="<?php echo esc_attr($size); ?>"
									<?php echo ($size === $sizeValue ? "selected" : ""); ?>
								>
									<?php echo esc_html($label); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * draw mp3 input
	 */
	protected function drawMp3Input($setting){

		$previewStyle = "display:none";

		$setting = $this->modifyImageSetting($setting);

		$value = UniteFunctionsUC::getVal($setting, "value");

		$class = $this->getInputClassAttr($setting, "", "unite-setting-mp3-input unite-input-image");

		//add source param
		$source = UniteFunctionsUC::getVal($setting, "source");

		?>
			<div class="unite-setting-mp3">
				<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" <?php 
				s_echo( $class )?> value="<?php echo esc_attr($value)?>" 
				<?php
					$this->getDefaultAddHtml($setting);
					if(!empty($source)) {
						echo ' data-source="' . esc_attr($source) . '"';
					}
				?> />
				<a href="javascript:void(0)" class="unite-button-secondary unite-button-choose"><?php esc_html_e("Choose", "unlimited-elements-for-elementor")?></a>
			</div>
		<?php
	}

	/**
	 * draw icon picker input
	 */
	protected function drawIconPickerInput($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$value = UniteFunctionsUC::getVal($setting, "value");
		$iconsType = UniteFunctionsUC::getVal($setting, "icons_type");
		$enableSvg = UniteFunctionsUC::getVal($setting, "enable_svg");
		$enableSvg = UniteFunctionsUC::strToBool($enableSvg);

		if(empty($iconsType) === true || $iconsType === "fa"){
			$setting["value"] = UniteFontManagerUC::fa_convertIcon($value);
			$value = $setting["value"];
		}
		$class = $this->getInputClassAttr($setting, "", "unite-iconpicker-input");
		?>
		<div class="unite-iconpicker">
			<div class="unite-setting-buttons-group">
				<div class="unite-iconpicker-button unite-setting-button uc-tip" title="<?php esc_attr_e("None", "unlimited-elements-for-elementor"); ?>" data-action="none">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12">
						<path d="m2.111 9.889 7.778-7.778" />
						<path d="M6 11.5a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11Z" />
					</svg>
				</div>
				<div class="unite-iconpicker-button unite-setting-button uc-tip" title="<?php esc_attr_e("Upload SVG", "unlimited-elements-for-elementor"); ?>" data-action="upload">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12">
						<path d="M2.5.5H2A1.5 1.5 0 0 0 .5 2v8A1.5 1.5 0 0 0 2 11.5h8a1.5 1.5 0 0 0 1.5-1.5V2A1.5 1.5 0 0 0 10 .5h-.5M.5 8.5h11" />
						<path d="M3.5 3 6 .5 8.5 3M6 6.5v-6" />
					</svg>
					<img class="unite-iconpicker-uploaded-icon" src="" alt="" />
				</div>
				<div class="unite-iconpicker-button unite-setting-button uc-tip" title="<?php esc_attr_e("Icon Library", "unlimited-elements-for-elementor"); ?>" data-action="library">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 11">
						<path d="m5.5 2.5-1-2h-4v9a1 1 0 0 0 1 1h9a1 1 0 0 0 1-1v-7h-6Z" />
					</svg>
					<div class="unite-iconpicker-library-icon"></div>
				</div>
			</div>
			<div class="unite-iconpicker-error unite-setting-error"></div>
			<input
				id="<?php echo esc_attr($id); ?>"
				type="hidden"
				name="<?php echo esc_attr($name); ?>"
				value="<?php echo esc_attr($value); ?>"
				<?php 
				s_echo($class); ?>
				<?php 
					$this->getDefaultAddHtml($setting);
					if($iconsType) {
						echo ' data-icons_type="' . esc_attr($iconsType) . '"';
					}
				?>
			/>
		</div>
		<?php
	}


	/**
	 * draw addon picker input
	 */
	protected function drawAddonPickerInput($setting){

		$value = UniteFunctionsUC::getVal($setting, "value");
		$value = trim($value);

		$addonType = UniteFunctionsUC::getVal($setting, "addontype");
		$objAddonType = UniteCreatorAddonType::getAddonTypeObject($addonType);
		if(empty($addonType))
			$addonType = GlobalsUC::ADDON_TYPE_REGULAR_ADDON;

		$isSVG = $objAddonType->isSVG;

		$addClass = "";
		if($isSVG == true)
			$addClass = " unite-addonpicker-icon-svg";

		$addClass .= " uc-addon-type-".$addonType;

		$styleButton = "";
		$title = "";

		//get all the addon data
		if(!empty($value)){
			try{

				$objAddon = new UniteCreatorAddon();
				$objAddon->initByMixed($value, $addonType);

				$urlPreview = $objAddon->getPreviewImageUrl();
				if($urlPreview)
					$styleButton = "background-image:url('{$urlPreview}')";

				$title = $objAddon->getTitle(true);

			}catch(Exception $e){
				$value = "";
			}

		}

		if(!empty($styleButton))
			$styleButton = "style=\"{$styleButton}\"";

		if(empty($value))
			$addClass .= " unite-empty-content";

		$textSelect = __("Select ", "unlimited-elements-for-elementor").$objAddonType->textSingle;

		$showTitle = true;
		if(isset($setting["noaddontitle"]))
			$showTitle = false;

		//add data holder
		$addDataHolder = UniteFunctionsUC::getVal($setting, "add_data_holder");
		$addDataHolder = UniteFunctionsUC::strToBool($addDataHolder);

		$addClearButton = UniteFunctionsUC::getVal($setting, "add_clear_button");
		$addClearButton = UniteFunctionsUC::strToBool($addClearButton);

		$addConfigureButton = UniteFunctionsUC::getVal($setting, "add_configure_button");
		$addConfigureButton = UniteFunctionsUC::strToBool($addConfigureButton);

		$configureButtonAction = UniteFunctionsUC::getVal($setting, "configure_button_action");


		?>
				<div class="unite-settings-addonpicker-wrapper <?php echo esc_attr($addClass)?>">
				<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" class="unite-setting-addonpicker" value="<?php echo esc_attr($value)?>" 
				<?php 
					$this->getDefaultAddHtml($setting);
					echo ' data-addontype="' . esc_attr($addonType) . '" style="display:none"';
				?> />

				<?php if($addDataHolder == true):?>
				<input type="hidden" id="<?php echo esc_attr($setting["id"])?>_data" name="<?php echo esc_attr($setting["name"])?>_data" value="" />
						<?php endif?>

						<div class="unite-addonpicker-button" <?php 
							s_echo($styleButton)?>>
							<div class="unite-addonpicker-empty-container">
								<?php echo esc_html($textSelect)?>
							</div>
						</div>

						<?php if($showTitle == true):?>
						<div class="unite-addonpicker-title"><?php echo esc_html($title)?></div>
					<?php endif?>

					<?php if($addClearButton == true):?>
					<a href="javascript:void(0)" class="unite-button-secondary uc-action-button" data-action="clear" ><?php esc_html_e("Clear", "unlimited-elements-for-elementor")?></a>
					<?php endif?>

					<?php if($addConfigureButton == true):?>
					<a href="javascript:void(0)" class="unite-button-secondary uc-action-button" data-action="configure" data-configureaction="<?php echo esc_attr($configureButtonAction)?>" ><?php esc_html_e("Configure", "unlimited-elements-for-elementor")?></a>
					<?php endif?>

			</div>
		<?php

	}

	private function ___________SELECTORS________________(){}

	/**
	 * get group selector add attributes
	 */
	private function getGroupSelectorAddAttr($setting){

		$selectorName = UniteFunctionsUC::getVal($setting, "group_selector");

		if(empty($selectorName))
			return null;

		$selectorName = is_array($selectorName) === false ? [$selectorName] : $selectorName;
		$selectorName = UniteFunctionsUC::jsonEncodeForHtmlData($selectorName);

		echo ' data-group-selector="' . esc_attr($selectorName) . '"';
	}

	/**
	 * get selectors add attributes
	 */
	private function getSelectorsAddAttr($setting){

		$selector = UniteFunctionsUC::getVal($setting, "selector");
		$selector1 = UniteFunctionsUC::getVal($setting, "selector1");

		if(empty($selector) && empty($selector1))
			return null;

		$arrData = array();

		foreach($setting as $key => $value){
			if(strpos($key, "selector") !== false)
				$arrData[$key] = $value;
		}

		if(empty($arrData))
			return null;

		$strAttr = UniteFunctionsUC::jsonEncodeForHtmlData($arrData);
		echo ' data-selectors="' . esc_attr($strAttr) . '"';
	}


	/**
	 * special inputs
	 */
	private function a______SPECIAL_INPUTS_____(){}


	/**
	 * draw gallery setting
	 */
	protected function drawGallerySetting($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-setting-gallery unite-setting-input-object"
			data-settingtype="gallery"
			data-name="<?php echo esc_attr($name); ?>"
			<?php 
			$this->getDefaultAddHtml($setting);
			?>
		>
			<div
				class="unite-setting-gallery-header"
				data-text-none="<?php esc_attr_e("No images selected", "unlimited-elements-for-elementor"); ?>"
				data-text-one="<?php 
				// translators: %d is a number
				esc_attr_e("%d image selected", "unlimited-elements-for-elementor"); 
				?>"
				data-text-default="<?php 
				// translators: %d is a number
				esc_attr_e("%d images selected", "unlimited-elements-for-elementor"); 
				?>"
			>
				<?php esc_html_e("No images selected", "unlimited-elements-for-elementor"); ?>
			</div>
			<div class="unite-setting-gallery-content">
				<div class="unite-setting-gallery-empty">
					<button class="unite-button-primary unite-setting-gallery-add">
						<?php esc_html_e("Add Images", "unlimited-elements-for-elementor"); ?>
					</button>
				</div>
				<div class="unite-setting-gallery-items">
					<div class="unite-setting-gallery-item">
						<div class="unite-setting-gallery-image unite-setting-gallery-add">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
								<path d="M4.281 10.313V0h1.75v10.313h-1.75ZM0 6.03v-1.75h10.313v1.75H0Z" />
							</svg>
						</div>
					</div>
				</div>
				<div class="unite-setting-gallery-actions">
					<button
						class="unite-setting-gallery-action unite-setting-gallery-clear"
						data-text="<?php esc_attr_e("Are you sure you want to clear the gallery?", "unlimited-elements-for-elementor"); ?>"
					>
						<?php esc_html_e("Clear Gallery", "unlimited-elements-for-elementor"); ?>
					</button>
					<button class="unite-setting-gallery-action unite-setting-gallery-edit">
						<?php esc_html_e("Edit Gallery", "unlimited-elements-for-elementor"); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * draw icon picker input:
	 * @param $setting 
	 */
	protected function drawMapPickerInput($setting){

		$value = UniteFunctionsUC::getVal($setting, "value");

		$dialogTitle = esc_html__("Select Map","unlimited-elements-for-elementor");

		$filepathPickerObject = GlobalsUC::$pathViewsObjects."mappicker_view.class.php";
		require_once $filepathPickerObject;

		$objPicker = new UniteCreatorMappickerView();
		$objPicker->setData($value);

		$strMapData = UniteFunctionsUC::jsonEncodeForHtmlData($value, "mapdata");

		?>
				<div id="<?php echo esc_attr($setting["id"])?>" data-settingtype="map" <?php 
				s_echo($strMapData)?> class="unite-settings-mappicker unite-setting-input-object" data-name="<?php echo esc_attr($setting["name"])?>" data-dialogtitle="<?php echo esc_attr($dialogTitle)?>" >
					 <?php $objPicker->putPickerInputHtml()?>
			</div>
		<?php
	}


	/**
	 * draw icon picker input:
	 * @param $setting
	 */
	protected function drawPostPickerInput($setting){
		dmp("drawPostPickerInput: function for override");
		exit();
	}


	/**
	 * draw module picker input:
	 * @param $setting
	 */
	protected function drawModulePickerInput($setting){
		dmp("drawModulePickerInput: function for override");
		exit();
	}


	/**
	 * draw color picker
	 * @param $setting
	 */
	protected function drawColorPickerInput($setting){

		$disabled = "";
		if(isset($setting["disabled"])){
			$color = "";
			$disabled = 'disabled="disabled"';
		}

		$pickerType = GlobalsUC::$colorPickerType;

		$bgcolor = $setting["value"];
		$bgcolor = str_replace("0x","#",$bgcolor);

		$style = "";
		if($pickerType == "farbtastic"){


			// set the forent color (by black and white value)
			$rgb = UniteFunctionsUC::html2rgb($bgcolor);
			$bw = UniteFunctionsUC::yiq($rgb[0],$rgb[1],$rgb[2]);

			$color = "#000000";
			if($bw<128)
				$color = "#ffffff";

			$style="style='background-color:$bgcolor;color:$color'";
		}

		$class = $this->getInputClassAttr($setting, "", "unite-color-picker");

		?>
			<div class="unite-color-picker-wrapper">

				<input type="text" <?php 
				s_echo($class)?> id="<?php echo esc_attr($setting["id"])?>" <?php 
				s_echo($style)?> name="<?php echo esc_attr($setting["name"])?>" value="<?php echo esc_attr($bgcolor)?>" <?php 
				s_echo($disabled)?> 
				<?php 
				$this->getDefaultAddHtml($setting);
				?>></input>

			</div>
		<?php
	}


	/**
	 * draw the editor by provider
	 */
	protected function drawEditorInput($setting){

		dmp("provider settings output - function to override");
		exit();
	}

	/**
	 * draw fonts panel - function for override
	 */
	protected function drawFontsPanel($setting){

		dmp("draw fonts panel - function for override");
		exit();
	}

	/**
	 * draw fonts panel - function for override
	 */
	protected function drawItemsPanel($setting){

		dmp("draw items panel - function for override");
		exit();
	}

	/**
	 * draw buttons group setting
	 */
	protected function drawButtonsGroupSetting($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$items = UniteFunctionsUC::getVal($setting, "items");
		$deselectable = UniteFunctionsUC::getVal($setting, "deselectable");
		$deselectable = UniteFunctionsUC::strToBool($deselectable);

		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-setting-buttons-group unite-setting-input-object unite-settings-exclude"
			data-settingtype="buttons_group"
			data-name="<?php echo esc_attr($name); ?>"
			data-deselectable="<?php echo esc_attr($deselectable); ?>"
			<?php $this->getDefaultAddHtml($setting); ?>
		>
			<?php foreach($items as $itemValue => $item): ?>
				<div
					class="unite-setting-button uc-tip"
					title="<?php echo esc_attr($item["title"]); ?>"
					data-value="<?php echo esc_attr($itemValue); ?>"
				>
					<?php 
					s_echo($item["icon"]); 
					?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * draw tabs setting
	 */
	protected function drawTabsSetting($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$items = UniteFunctionsUC::getVal($setting, "items");

		$counter = 0;

		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-setting-tabs unite-setting-input-object unite-settings-exclude"
			data-settingtype="tabs"
			data-name="<?php echo esc_attr($name); ?>"
			<?php $this->getDefaultAddHtml($setting); ?>
		>
			<?php foreach($items as $itemText => $itemValue): ?>

				<?php $itemId = $id . "_" . ++$counter; ?>

				<div class="unite-setting-tabs-item">
					<input
						id="<?php echo esc_attr($itemId); ?>"
						class="unite-setting-tabs-item-input"
						type="radio"
						name="<?php echo esc_attr($name); ?>"
						value="<?php echo esc_attr($itemValue); ?>"
					/>
					<label
						class="unite-setting-tabs-item-label"
						for="<?php echo esc_attr($itemId); ?>"
					>
						<?php echo esc_html($itemText); ?>
					</label>
				</div>

			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * draw group selector setting
	 */
	protected function drawGroupSelectorSetting($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$selectorReplace = UniteFunctionsUC::getVal($setting, "selector_replace");
		$selectorReplace = json_encode($selectorReplace);

		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-setting-group-selector unite-setting-input-object"
			data-settingtype="group_selector"
			data-name="<?php echo esc_attr($name); ?>"
			data-replace="<?php echo esc_attr($selectorReplace); ?>"
			<?php $this->getDefaultAddHtml($setting); ?>
		></div>
		<?php
	}


	/**
	 * draw setting input by type
	 */
	protected function drawInputs($setting){

		switch($setting["type"]){
			case UniteSettingsUC::TYPE_TEXT:
				$this->drawTextInput($setting);
			break;
			case UniteSettingsUC::TYPE_COLOR:
				$this->drawColorPickerInput($setting);
			break;
			case UniteSettingsUC::TYPE_SELECT:
				$this->drawSelectInput($setting);
			break;
			case UniteSettingsUC::TYPE_MULTISELECT:
				$this->drawMultiSelectInput($setting);
			break;
			case UniteSettingsUC::TYPE_CHECKBOX:
				$this->drawCheckboxInput($setting);
			break;
			case UniteSettingsUC::TYPE_RADIO:
				$this->drawRadioInput($setting);
			break;
			case UniteSettingsUC::TYPE_TEXTAREA:
				$this->drawTextAreaInput($setting);
			break;
			case UniteSettingsUC::TYPE_LINK:
				$this->drawLinkInput($setting);
			break;
			case UniteSettingsUC::TYPE_IMAGE:
				$this->drawImageInput($setting);
			break;
			case UniteSettingsUC::TYPE_MP3:
				$this->drawMp3Input($setting);
			break;
			case UniteSettingsUC::TYPE_ICON:
				$this->drawIconPickerInput($setting);
			break;
			case UniteSettingsUC::TYPE_ADDON:
				$this->drawAddonPickerInput($setting);
			break;
			case UniteSettingsUC::TYPE_MAP:
				$this->drawMapPickerInput($setting);
			break;
			case UniteSettingsUC::TYPE_POST:
				$this->drawPostPickerInput($setting);
			break;
			case UniteSettingsUC::TYPE_EDITOR:
				$this->drawEditorInput($setting);
			break;
			case UniteCreatorSettings::TYPE_FONT_PANEL:
				$this->drawFontsPanel($setting);
			break;
			case UniteCreatorSettings::TYPE_ITEMS:
				$this->drawItemsPanel($setting);
			break;
			case UniteCreatorSettings::TYPE_BUTTON:
				$this->drawButtonInput($setting);
			break;
			case UniteCreatorSettings::TYPE_RANGE:
				$this->drawRangeSliderInput($setting);
			break;
			case UniteCreatorSettings::TYPE_HIDDEN:
				$this->drawHiddenInput($setting);
			break;
			case UniteCreatorSettings::TYPE_REPEATER:
				$this->drawRepeaterInput($setting);
			break;
			case UniteCreatorSettings::TYPE_TYPOGRAPHY:
			case UniteCreatorSettings::TYPE_TEXTSHADOW:
			case UniteCreatorSettings::TYPE_TEXTSTROKE:
			case UniteCreatorSettings::TYPE_BOXSHADOW:
			case UniteCreatorSettings::TYPE_CSS_FILTERS:
				$this->drawSubSettings($setting);
			break;
			case UniteSettingsUC::TYPE_SWITCHER:
				$this->drawSwitcherSetting($setting);
			break;
			case UniteCreatorSettings::TYPE_DIMENTIONS:
				$this->drawDimentionsSetting($setting);
			break;
			case UniteCreatorSettings::TYPE_GALLERY:
				$this->drawGallerySetting($setting);
			break;
			case UniteCreatorSettings::TYPE_BUTTONS_GROUP:
				$this->drawButtonsGroupSetting($setting);
			break;
			case UniteCreatorSettings::TYPE_TABS:
				$this->drawTabsSetting($setting);
			break;
			case UniteCreatorSettings::TYPE_GROUP_SELECTOR:
				$this->drawGroupSelectorSetting($setting);
			break;
			case UniteSettingsUC::TYPE_CUSTOM:
				if(method_exists($this,"drawCustomInputs") == false){
					UniteFunctionsUC::throwError("Method don't exists: drawCustomInputs, please override the class");
				}
				$this->drawCustomInputs($setting);
			break;
			default:
				throw new Exception("drawInputs error: wrong setting type - ".esc_html($setting["type"]));
			break;
		}

	}


	/**
	 * draw range slider input
	 */
	protected function drawRangeSliderInput($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
		$value = UniteFunctionsUC::getVal($setting, "value", $defaultValue);
		$min = UniteFunctionsUC::getVal($setting, "min", 0);
		$max = UniteFunctionsUC::getVal($setting, "max", 0);
		$step = UniteFunctionsUC::getVal($setting, "step", 1);
		$units = UniteFunctionsUC::getVal($setting, "units");
		$unit = empty($units) === false ? reset($units) : "px";
		$showSlider = UniteFunctionsUC::getVal($setting, "show_slider", true);
		$showSlider = UniteFunctionsUC::strToBool($showSlider);

		$setting["default_value"] = array("size" => $defaultValue, "unit" => $unit);
		$setting["value"] = array("size" => $value, "unit" => $unit);

		$wrapperClass = "";

		if($showSlider === true)
			$wrapperClass .= " with-slider";

		if(empty($units) === false)
			$wrapperClass .= " with-units";

		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-setting-range unite-setting-input-object unite-settings-exclude <?php echo esc_attr($wrapperClass); ?>"
			data-settingtype="range"
			data-name="<?php echo esc_attr($name); ?>"
			<?php $this->getDefaultAddHtml($setting); ?>
		>

			<?php if($showSlider === true): ?>
				<div
					class="unite-setting-range-slider"
					data-value="<?php echo esc_attr($value); ?>"
					data-min="<?php echo esc_attr($min); ?>"
					data-max="<?php echo esc_attr($max); ?>"
					data-step="<?php echo esc_attr($step); ?>"
				></div>
			<?php endif; ?>

			<input
				class="unite-setting-range-input"
				type="number"
				value="<?php echo esc_attr($value); ?>"
			/>

			<?php if(empty($units) === false): ?>
				<div class="unite-setting-range-units">
					<?php $this->drawUnitsPicker($units, $unit); ?>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}


	/**
	 * draw repeater input
	 */
	protected function drawRepeaterInput($setting){

		$itemsValues = UniteFunctionsUC::getVal($setting, "items_values");
		$strData = UniteFunctionsUC::jsonEncodeForHtmlData($itemsValues, "itemvalues");
		$addItemText = UniteFunctionsUC::getVal($setting, "add_button_text");

		if(empty($addItemText))
			$addItemText = esc_html__("Add Item", "unlimited-elements-for-elementor");

		//get empty text
		$emptyText = UniteFunctionsUC::getVal($setting, "empty_text");

		if(empty($emptyText))
			$emptyText = esc_html__("No items found.", "unlimited-elements-for-elementor");

		$objSettingsItems = UniteFunctionsUC::getVal($setting, "settings_items");

		UniteFunctionsUC::validateNotEmpty($objSettingsItems, "settings items");

		$emptyTextAddHtml = "";

		if(!empty($value))
			$emptyTextAddHtml = "style='display:none'";

		if($this->isSidebar === true){
			$output = new UniteSettingsOutputSidebarUC();
			$output->setShowSaps(false);
		}else
			$output = new UniteSettingsOutputWideUC();

		$output->init($objSettingsItems);

		//get item title
		$itemTitle = UniteFunctionsUC::getVal($setting, "item_title");

		if(empty($itemTitle))
			$itemTitle = esc_html__("Item", "unlimited-elements-for-elementor");

		$itemTitle = htmlspecialchars($itemTitle);

		//delete button text
		$deleteButtonText = UniteFunctionsUC::getVal($setting, "delete_button_text");

		if(empty($deleteButtonText))
			$deleteButtonText = esc_html__("Delete", "unlimited-elements-for-elementor");

		$duplicateButtonText = UniteFunctionsUC::getVal($setting, "duplicate_button_text");

		if(empty($duplicateButtonText))
			$duplicateButtonText = esc_html__("Duplicate", "unlimited-elements-for-elementor");

		$deleteButtonText = htmlspecialchars($deleteButtonText);
		$duplicateButtonText = htmlspecialchars($duplicateButtonText);

		?>
		<div
			id="<?php echo esc_attr($setting["id"]); ?>"
			class="unite-setting-repeater unite-setting-input-object"
			data-settingtype="repeater"
			data-name="<?php echo esc_attr($setting["name"]); ?>"
			data-item-title="<?php echo esc_attr($itemTitle); ?>"
			data-text-delete="<?php echo esc_attr($deleteButtonText); ?>"
			data-text-duplicate="<?php echo esc_attr($duplicateButtonText); ?>"
			<?php 
				s_echo($strData); ?>
		>
			<div class="unite-repeater-template unite-hidden">
				<?php $output->draw("settings_item_repeater", false); ?>
			</div>
			<div class="unite-repeater-empty" <?php 
				s_echo($emptyTextAddHtml); ?>>
				<?php echo esc_html($emptyText); ?>
			</div>
			<div class="unite-repeater-items"></div>
			<div class="unite-repeater-actions">
				<button class="unite-button-primary unite-repeater-add" type="button">
					<?php 
				s_echo($addItemText); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * special inputs
	 */
	private function a______REGULAR_INPUTS______(){}

	/**
	 * draw units picker
	 */
	protected function drawUnitsPicker($units, $selectedUnit = null){

		$defaultUnit = reset($units);
		$selectedUnit = $selectedUnit ?: $defaultUnit;

		?>
		<select
			class="unite-units-picker"
			<?php echo count($units) === 1 ? "disabled" : ""; ?>
		>
			<?php foreach($units as $unit): ?>
				<option
					value="<?php echo esc_attr($unit); ?>"
					data-content="<?php echo esc_attr('<div class="unite-units-picker-item">' . $unit . '</div>'); ?>"
					<?php echo ($unit === $selectedUnit ? "selected" : ""); ?>
				>
					<?php echo esc_html($unit); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * draw text input
	 */
	protected function drawTextInput($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$value = UniteFunctionsUC::getVal($setting, "value");
		$placeholder = UniteFunctionsUC::getVal($setting, "placeholder");
		$style = UniteFunctionsUC::getVal($setting, "style");
		$disabled = UniteFunctionsUC::getVal($setting, "disabled");
		$readonly = UniteFunctionsUC::getVal($setting, "readonly");
		$unit = UniteFunctionsUC::getVal($setting, "unit");
		$units = UniteFunctionsUC::getVal($setting, "units");
		$step = UniteFunctionsUC::getVal($setting, "step");
		$typeNumber = UniteFunctionsUC::getVal($setting, "type_number");
		$typeNumber = UniteFunctionsUC::strToBool($typeNumber);
		$typePassword = UniteFunctionsUC::getVal($setting, "ispassword");
		$typePassword = UniteFunctionsUC::strToBool($typePassword);

		if(is_array($value) === true)
			$value = json_encode($value);

		if(empty($style) === false)
			$style = "style='" . esc_attr($style) . "'";

		$wrapperClass = "";
		$defaultClass = self::INPUT_CLASS_NORMAL;

		if(empty($unit) === false && empty($units) === true)
			$units = array($unit);

		if(empty($units) === false){
			$wrapperClass .= " with-units";
			$defaultClass = self::INPUT_CLASS_NUMBER;
			$typeNumber = true;
		}

		$type = "text";

		if($typeNumber === true)
			$type = "number";
		elseif($typePassword === true)
			$type = "password";

		$class = $this->getInputClassAttr($setting, $defaultClass);

		?>
		<div class="unite-input-wrapper <?php echo esc_attr($wrapperClass); ?>">

			<input
				<?php 
				s_echo($class); ?>
				id="<?php echo esc_attr($id); ?>"
				type="<?php echo esc_attr($type); ?>"
				name="<?php echo esc_attr($name); ?>"
				value="<?php echo esc_attr($value); ?>"
				<?php 
				s_echo($style); ?>
				<?php 
				$this->getDefaultAddHtml($setting);
				if(empty($placeholder) === false) {
					echo " placeholder=\"" . esc_attr($placeholder) . "\"";
				}
	
				if(empty($disabled) === false) {
					echo " disabled";
				}
	
				if(empty($readonly) === false) {
					echo " readonly";
				}
				if($typeNumber === true
					&& empty($step) === false
					&& is_numeric($step) === true
				) {
					echo " step=\"" . esc_attr($step) . "\"";
				}
				?>
			/>

			<?php if(empty($units) === false): ?>
				<div class="unite-input-units">
					<?php $this->drawUnitsPicker($units, $unit); ?>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * draw hidden input
	 */
	protected function drawHiddenInput($setting){

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$value = UniteFunctionsUC::getVal($setting, "value");

		?>
		<input
			id="<?php echo esc_attr($id); ?>"
			type="hidden"
			name="<?php echo esc_attr($name); ?>"
			value="<?php echo esc_attr($value); ?>"
			<?php $this->getDefaultAddHtml($setting) ?>
		/>
		<?php
	}

	/**
	 * draw button input
	 */
	protected function drawButtonInput($setting){

		$name = $setting["name"];
		$id = $setting["id"];
		$value = $setting["value"];
		$href = "#";
		$gotoView = UniteFunctionsUC::getVal($setting, "gotoview");

		if(!empty($gotoView))
			$href = HelperUC::getViewUrl($gotoView);

		$url = UniteFunctionsUC::getVal($setting, "url");

		if(!empty($url))
			$href = $url;

		$isNewWindow = UniteFunctionsUC::getVal($setting, "newwindow");
		?>
		<a id="<?php echo esc_attr($id)?>" href="<?php echo esc_url($href)?>" name="<?php echo esc_attr($name)?>" <?php 
		echo ( $isNewWindow ? 'target="_blank"' : '' );
		?> class="unite-button-secondary"><?php echo esc_html($value)?></a>
		<?php

	}


	/**
	 * draw text area input
	 */
	protected function drawTextAreaInput($setting){

		$disabled = "";
		if (isset($setting["disabled"]))
			$disabled = 'disabled="disabled"';

		$style = "";
		if(isset($setting["style"]))
			$style = "style='".$setting["style"]."'";

		$rows = UniteFunctionsUC::getVal($setting, "rows");
		if(!empty($rows))
			$rows = "rows='$rows'";

		$cols = UniteFunctionsUC::getVal($setting, "cols");
		if(!empty($cols))
			$cols = "cols='$cols'";

		$class = $this->getInputClassAttr($setting);

		$value = $setting["value"];
		$value = htmlspecialchars($value);

		?>
			<textarea id="<?php echo esc_attr($setting["id"])?>" <?php 
			s_echo($class);
			?> name="<?php echo esc_attr($setting["name"])?>" <?php 
			s_echo($style)?> <?php 
			s_echo($disabled)?> <?php 
			s_echo($rows)?> <?php 
			s_echo($cols)?> 
			<?php $this->getDefaultAddHtml($setting); ?> ><?php 
				s_echo($value)?></textarea>
		<?php
		if(!empty($cols))
			echo "<br>";	//break line on big textareas.
	}

	/**
	 * draw radio input
	 */
	protected function drawRadioInput($setting){
				
		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$items = UniteFunctionsUC::getVal($setting, "items");
		$value = UniteFunctionsUC::getVal($setting, "value");
		$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
		$disabled = UniteFunctionsUC::getVal($setting, "disabled");
		$disabled = UniteFunctionsUC::strToBool($disabled);

		$classAttr = $this->getInputClassAttr($setting, "", "unite-radio-item-input");
		$counter = 0;

		?>
		<div
			id="<?php echo esc_attr($id) ?>"
			class="unite-radio-wrapper"
			<?php $this->getDefaultAddHtml($setting); ?>
		>
			<?php foreach($items as $itemText => $itemValue): ?>

				<?php

				$itemId = $id . "_" . ++$counter;
				$itemAttr = $classAttr;

				if($disabled === true)
					$itemAttr .= " disabled";

				if($itemValue == $defaultValue)
					$itemAttr .= ' data-defaultchecked="true"';

				if($itemValue == $value){
					$itemAttr .= ' data-initchecked="true"';
					$itemAttr .= " checked='checked'";
				}
				
				?>

				<input
					id="<?php echo esc_attr($itemId); ?>"
					type="radio"
					name="<?php echo esc_attr($name); ?>"
					value="<?php echo esc_attr($itemValue); ?>"
					<?php 
				s_echo($itemAttr); ?>
				/>
				<label
					class="unite-radio-item-label"
					for="<?php echo esc_attr($itemId); ?>"
				>
					<?php echo esc_html($itemText); ?>
				</label>

			<?php endforeach; ?>
		</div>
		<?php
	}


	/**
	 * draw checkbox
	 */
	protected function drawCheckboxInput($setting){

		$checked = "";

		$value = UniteFunctionsUC::getVal($setting, "value");
		$value = UniteFunctionsUC::strToBool($value);

		if($value == true)
			$checked = 'checked="checked"';

			$textNear = UniteFunctionsUC::getVal($setting, "text_near");

		$settingID = $setting["id"];

		if(!empty($textNear)){
			$textNearAddHtml = "";
			if($this->showDescAsTips == true){
				$description = UniteFunctionsUC::getVal($setting, "description");
				$description = htmlspecialchars($description);
				$textNearAddHtml = " title='$description' class='uc-tip'";
			}

			$textNear = "<label for=\"{$settingID}\"{$textNearAddHtml}>$textNear</label>";
		}

		$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
		$defaultValue = UniteFunctionsUC::strToBool($defaultValue);

		$addHtml = "";
		if($defaultValue == true)
			$addHtml .= " data-defaultchecked=\"true\"";

		if($value)
			$addHtml .= " data-initchecked=\"true\"";

		$class = $this->getInputClassAttr($setting);

		?>
			<input type="checkbox" id="<?php echo esc_attr($settingID)?>" <?php 
			s_echo($class);?> name="<?php echo esc_attr($setting["name"])?>" <?php 
			s_echo($checked);?> <?php 
			s_echo($addHtml);?>/>
		<?php
		if(!empty($textNear))
			s_echo($textNear);
	}


	/**
	 * draw select input
	 */
	protected function drawSelectInput($setting){

		$isPostSelect = UniteFunctionsUC::getVal($setting, "post_select");
		$isPostSelect = UniteFunctionsUC::strToBool($isPostSelect);

		if($isPostSelect === true){
			$this->drawPostPickerInput($setting);

			return;
		}

		$id = UniteFunctionsUC::getVal($setting, "id");
		$type = UniteFunctionsUC::getVal($setting, "type");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$value = UniteFunctionsUC::getVal($setting, "value");
		$disabled = UniteFunctionsUC::getVal($setting, "disabled");
		$disabled = UniteFunctionsUC::strToBool($disabled);
		$items = UniteFunctionsUC::getVal($setting, "items", array());
		$items = empty($items) === false ? $items: array();
		$args = UniteFunctionsUC::getVal($setting, "args");

		if(is_array($value) === false && strpos($value, ",") !== false)
			$value = explode(",", $value);

		$multiple = ($type === "multiselect");

		$useSelect2 = false;

		if($multiple === true)
			$useSelect2 = true;

		$defaultClass = "";

		if($useSelect2 === true)
			$defaultClass = "select2";

		$class = $this->getInputClassAttr($setting, $defaultClass);

		?>
		<select
			id="<?php echo esc_attr($id); ?>"
			name="<?php echo esc_attr($name); ?>"
			<?php 
			s_echo($class); ?>
			<?php 
				$this->getDefaultAddHtml($setting, true);
				if($multiple === true)
					echo " multiple";

				if($disabled === true)
					echo " disabled";
			?>
			<?php 
			s_echo($args); ?>
		>
			<?php foreach($items as $itemText => $itemValue): ?>
				<?php

				$itemSelected = is_array($value) === true
					? in_array($itemValue, $value) === true
					: $itemValue == $value;

				?>
				<option
					value="<?php echo esc_attr($itemValue); ?>"
					<?php echo ($itemSelected ? "selected" : ""); ?>
				>
					<?php echo esc_html($itemText); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}


	/**
	 * draw select input
	 */
	protected function drawMultiSelectInput($setting){
		$this->drawSelectInput($setting);
	}

	/**
	 * draw text row
	 */
	protected function drawTextRow($setting){
		echo "draw text row - override this function";
	}

	/**
	 * draw hr row - override
	 */
	protected function drawHrRow($setting){
		echo "draw hr row - override this function";
	}

	/**
	 * draw switcher setting
	 */
	protected function drawSwitcherSetting($setting){
		echo "draw switcher setting - override this function";
	}

	/**
	 * draw dimentions setting
	 */
	protected function drawDimentionsSetting($setting){
		echo "draw dimentions setting - override this function";
	}

	/**
	 * draw input additinos like unit / description etc
	 */
	protected function drawInputAdditions($setting,$showDescription = true){

		$description = UniteFunctionsUC::getVal($setting, "description");
		if($showDescription === false)
			$description = "";
		$unit = UniteFunctionsUC::getVal($setting, "unit");
		$required = UniteFunctionsUC::getVal($setting, "required");
		$addHtml = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDTEXT);

		?>

		<?php if(!empty($unit)):?>
		<span class='setting_unit'><?php echo esc_html($unit)?></span>
		<?php endif?>
		<?php if(!empty($required)):?>
		<span class='setting_required'>*</span>
		<?php endif?>
		<?php if(!empty($addHtml)):?>
		<span class="settings_addhtml"><?php echo esc_html($addHtml)?></span>
		<?php endif?>
		<?php if(!empty($description) && $this->showDescAsTips == false):?>
		<span class="description"><?php echo esc_attr($description)?></span>
		<?php endif?>

		<?php
	}



	/**
	 * get options
	 */
	protected function getOptions(){

		$idPrefix = $this->settings->getIDPrefix();

		$options = array();
		$options["show_saps"] = $this->showSaps;
		$options["saps_type"] = $this->sapsType;
		$options["id_prefix"] = $idPrefix;

		//add google fonts
		$fontData = HelperUC::getFontPanelData();
		$googleFonts = UniteFunctionsUC::getVal($fontData, "arrGoogleFonts");

		$options["google_fonts"] = $googleFonts;

		return($options);
	}


	/**
	* set form id
	 */
	public function setFormID($formID){

		if(isset(self::$arrIDs[$formID]))
			UniteFunctionsUC::throwError("Can't output settings with the same ID: $formID");

		self::$arrIDs[$formID] = true;

		UniteFunctionsUC::validateNotEmpty($formID, "formID");

		$this->formID = $formID;

	}


	/**
	 *
	 * insert settings into saps array
	 */
	private function groupSettingsIntoSaps(){

			$arrSaps = $this->settings->getArrSaps();
			$arrSettings = $this->settings->getArrSettings();

			//group settings by saps
			foreach($arrSettings as $key=>$setting){

					$sapID = $setting["sap"];

					if(isset($arrSaps[$sapID]["settings"]))
							$arrSaps[$sapID]["settings"][] = $setting;
							else
									$arrSaps[$sapID]["settings"] = array($setting);
			}

			return($arrSaps);
	}


	private function a______SUB_SETTINGS_____(){}

	/**
	 * draw sub settings
	 */
	private function drawSubSettings($setting){

		if($this->isSidebar === false){
			dmp("the attribute will be available in elementor");

			return;
		}

		$id = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$type = UniteFunctionsUC::getVal($setting, "type");

		$this->addSubSettingsDialog($type);

		?>
		<div
			id="<?php echo esc_attr($id); ?>"
			class="unite-sub-settings unite-setting-input-object"
			data-settingtype="<?php echo esc_attr($type); ?>"
			data-name="<?php echo esc_attr($name); ?>"
			data-dialog-id="<?php echo esc_attr($type); ?>"
			<?php $this->getDefaultAddHtml($setting); ?>
		>
			<button
				class="unite-sub-settings-reset unite-setting-button uc-tip unite-hidden"
				type="button"
				title="<?php esc_attr_e("Reset", "unlimited-elements-for-elementor"); ?>"
			>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12">
					<path d="M10.606 9.008a5.5 5.5 0 1 1 .68-4.535"/>
					<path d="M11.5.5v4l-3.969-.493"/>
				</svg>
			</button>
			<button
				class="unite-sub-settings-edit unite-setting-button uc-tip"
				type="button"
				title="<?php esc_attr_e("Edit", "unlimited-elements-for-elementor"); ?>"
			>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12">
					<path d="m9 1 2 2-7 7-3 1 1-3 7-7Z" />
				</svg>
			</button>
		</div>
		<?php
	}

	/**
	 * add sub settings dialog
	 */
	private function addSubSettingsDialog($type){

		$this->subSettingsDialogs[$type] = $type;
	}

	/**
	 * draw sub settings dialogs
	 */
	private function drawSubSettingsDialogs(){

		foreach($this->subSettingsDialogs as $type){
			$settings = new UniteCreatorSettings();
			$settings->addDialogSettings($type);

			$output = new UniteSettingsOutputSidebarUC();
			$output->init($settings);

			?>
			<div
				class="unite-sub-settings-dialog unite-settings-exclude"
				data-id="<?php echo esc_attr($type, "unlimited-elements-for-elementor"); ?>"
			>
				<?php $output->draw($type . "_sub_settings_dialog", false); ?>
			</div>
			<?php
		}
	}


	private function a______DRAW_GENERAL_____(){}

	/**
	 * get controls for client side
	 * eliminate only one setting in children
	 */
	private function getControlsForJS(){

		$controls = $this->settings->getArrControls(true);
		$arrChildren = $controls["children"];

		if(empty($arrChildren))
			return($controls);

		$arrChildrenNew = array();

		foreach($arrChildren as $name=>$arrChild){
			if(count($arrChild)>1)
				$arrChildrenNew[$name] = $arrChild;
		}

		$controls["children"] = $arrChildrenNew;

		return($controls);
	}


	/**
	 * draw wrapper start
	 */
	public function drawWrapperStart(){

		UniteFunctionsUC::validateNotEmpty($this->settingsMainClass, "settings main class not found, please use wide, inline or sidebar output");

		//get options
		$options = $this->getOptions();
		$strOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options);

		//get controls
		$controls = $this->getControlsForJS();

		/*
		if(!empty($controls["children"])){
			dmp($controls);exit();
		}
		*/

		$addHtml = "";
		if(!empty($controls)){
			$strControls = UniteFunctionsUC::jsonEncodeForHtmlData($controls);
			$addHtml = " data-controls=\"{$strControls}\"";
		}


		if(!empty($this->addCss)):
		?>
			<!-- settings add css -->
			<style type="text/css">
				<?php 
				s_echo($this->addCss)?>
			</style>
		<?php
		endif;

		?>
		<div id="<?php echo esc_attr($this->wrapperID)?>" data-options="<?php echo esc_attr($strOptions)?>" <?php 
			s_echo($addHtml);?> autofocus="true" class="unite_settings_wrapper <?php 
			s_echo($this->settingsMainClass);?> unite-settings unite-inputs">

		<?php
	}


	/**
	 * draw wrapper end
	 */
	public function drawWrapperEnd(){

		?>

		</div>
		<?php
	}


	/**
	 * function for override
	 */
	protected function setDrawOptions(){}

	/**
	 *
	 * draw settings function
	 * @param $drawForm draw the form yes / no
	 * if filter sapid present, will be printed only current sap settings
	 */
	public function draw($formID, $drawForm = false){

		if(empty($this->settings))
			UniteFunctionsUC::throwError("No settings are inited. Please init the settings in output class");

		$this->wrapperID = $formID."_".self::$serial;

		$this->setDrawOptions();

		$this->setFormID($formID);

		$this->drawWrapperStart();


		if($this->showSaps == true){

				 switch($this->sapsType){
						 case self::SAPS_TYPE_INLINE:
								 $this->drawSapsTabs();
						 break;
						 case self::SAPS_TYPE_CUSTOM:
								 $this->drawSaps();
						 break;
				 }

		}


		if($drawForm == true){

			if(empty($formID))
				UniteFunctionsUC::throwError("The form ID can't be empty. you must provide it");

			?>
			<form name="<?php echo esc_attr($formID)?>" id="<?php echo esc_attr($formID)?>">
				<?php $this->drawSettings() ?>
			</form>
			<?php
		}else
			$this->drawSettings();

		?>

		<?php

		$this->drawWrapperEnd();

	}


	/**
	 * draw wrapper before settings
	 */
	protected function drawSettings_before(){
	}


	/**
	* draw wrapper end after settings
	*/
	protected function drawSettingsAfter(){

	}


	/**
	 * draw single setting
	 */
	public function drawSingleSetting($name){

		$arrSetting = $this->settings->getSettingByName($name);

		$this->drawInputs($arrSetting);
		$this->drawInputAdditions($arrSetting);
	}


	/**
	 * function for override
	 */
	protected function drawSaps(){}


	/**
	 * draw saps tabs
	 */
	protected function drawSapsTabs(){

		$arrSaps = $this->settings->getArrSaps();

		?>
		<div class="unite-settings-tabs">

			<?php foreach($arrSaps as $key=>$sap){
				$text = $sap["text"];
				UniteFunctionsUC::validateNotEmpty($text,"sap $key text");

				$class = "";
				if($key == $this->activeSap)
					$class = "class='unite-tab-selected'";

				?>
				<a href="javascript:void(0)" <?php 
				s_echo($class);?> data-sapnum="<?php echo esc_attr($key)?>" onfocus="this.blur()"><?php echo esc_html($text)?></a>
				<?php

			}
			?>

		</div>
		<?php

	}

	/**
	 * draw setting row by type
	 *
	 */
	private function drawSettingsRowByType($setting, $mode){

		switch($setting["type"]){
			case UniteSettingsUC::TYPE_HR:
				$this->drawHrRow($setting);
			break;
			case UniteSettingsUC::TYPE_STATIC_TEXT:
				$this->drawTextRow($setting);
			break;
			default:
				$this->drawSettingRow($setting, $mode);
			break;
		}
	}


	/**
	 * draw settings - all together
	 */
	private function drawSettings_settings($filterSapID = null, $mode = null, $arrSettings = null){

		if(is_null($arrSettings))
			$arrSettings = $this->arrSettings;

		$this->drawSettings_before();

		foreach($arrSettings as $setting){
			if(isset($setting[UniteSettingsUC::PARAM_NODRAW]))
				continue;

			if($filterSapID !== null){
				$sapID = UniteFunctionsUC::getVal($setting, "sap");
				if($sapID != $filterSapID)
					continue;
			}

			$this->drawSettingsRowByType($setting, $mode);
		}

		$this->drawSettingsAfter();
	}


	/**
	 * draw sap before override
	 * @param unknown $sap
	 */
	protected function drawSapBefore($sap, $key){
			dmp("function for override");

	}

	protected function drawSapAfter(){
			dmp("function for override");
	}

	/**
	 * draw the bottom of the settings
	 */
	protected function drawSettingsBottom(){

		if($this->isSidebar === true)
			$this->drawSubSettingsDialogs();
	}

	/**
	 * draw the tabs
	 */
	private function drawSettings_saps_accordion_tabs(){

		?>
		<div class="unite-settings-accordion-saps-tabs">
			<a href="javascript:void(0)" class="unite-settings-tab unite-active" data-id="content"><?php esc_attr_e("Content","unlimited-elements-for-elementor"); ?></a>
			<a href="javascript:void(0)" class="unite-settings-tab" data-id="style"><?php esc_attr_e("Style","unlimited-elements-for-elementor"); ?></a>
		</div>
		<?php
	}

	/**
	 * draw settings - all together
	 */
	private function drawSettings_saps($filterSapID = null, $mode=null){

		 $isHasStyleTab = false;

		 $arrSaps = $this->groupSettingsIntoSaps();


			 foreach($arrSaps as $key=>$sap){

				$tab = UniteFunctionsUC::getVal($sap, "tab");

				if($tab == UniteSettingsUC::TAB_STYLE)
					$isHasStyleTab = true;
			 }

		if($isHasStyleTab == true)
			$this->drawSettings_saps_accordion_tabs();

					//draw settings - advanced - with sections
					foreach($arrSaps as $key=>$sap):

							$arrSettings = UniteFunctionsUC::getVal($sap, "settings");

							$nodraw = UniteFunctionsUC::getVal($sap, "nodraw");
							if($nodraw === true)
								continue;

									$this->drawSapBefore($sap, $key);

									if(!empty($arrSettings))
						$this->drawSettings_settings($filterSapID, $mode, $arrSettings);

					$this->drawSapAfter();

					endforeach;

		 $this->drawSettingsBottom();

	}



	/**
	 * draw all settings
	 */
	public function drawSettings($filterSapID = null){

		$this->prepareToDraw();

		$arrSettings = $this->settings->getArrSettings();
		if(empty($arrSettings))
				$arrSettings = array();

		$this->arrSettings = $arrSettings;

		//set special mode
		$mode = "";
		if(count($arrSettings) == 1 && $arrSettings[0]["type"] == UniteSettingsUC::TYPE_EDITOR)
				$mode = "single_editor";

		if($this->showSaps == true && $this->sapsType == self::SAPS_TYPE_ACCORDION)
				$this->drawSettings_saps($filterSapID, $mode);
		else
				$this->drawSettings_settings($filterSapID, $mode);
	}

}
