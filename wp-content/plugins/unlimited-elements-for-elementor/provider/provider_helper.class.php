<?php

class HelperProviderUC{

	private static $numQueriesStart = null;

	private function _______SETTINGS_________(){} 

	/*** Freemius ***/	
	
	/**
	 * is activated by freemius
	 */
	public static function isActivatedByFreemius(){

		global $uefe_fs;

		if(isset($uefe_fs) === false)
			return (false);

		$isActivated = $uefe_fs->is_paying();

		return ($isActivated);
	}

	/**
	 * get freemius account url
	 */
	public static function getFreemiusAccountUrl(){

		global $uefe_fs;

		if(isset($uefe_fs) === false)
			return "";

		$url = $uefe_fs->get_account_url();

		return $url;
	}

	/*** End Freemius ***/	

	/**
	 * get sort filter default values
	 */
	public static function getSortFilterDefaultValues(){

		$arrValues = array();
		$arrValues["default"] = __("Default","unlimited-elements-for-elementor");
		$arrValues["meta"] = __("Meta Field","unlimited-elements-for-elementor");
		$arrValues["id"] = __("ID","unlimited-elements-for-elementor");
		$arrValues["date"] = __("Date","unlimited-elements-for-elementor");
		$arrValues["title"] = __("Title","unlimited-elements-for-elementor");
		$arrValues["price"] = __("Price","unlimited-elements-for-elementor");
		$arrValues["sale_price"] = __("Sale Price","unlimited-elements-for-elementor");
		$arrValues["sales"] = __("Number Of Sales","unlimited-elements-for-elementor");
		$arrValues["rating"] = __("Rating","unlimited-elements-for-elementor");
		$arrValues["name"] = __("Name","unlimited-elements-for-elementor");
		$arrValues["author"] = __("Author","unlimited-elements-for-elementor");
		$arrValues["modified"] = __("Last Modified","unlimited-elements-for-elementor");
		$arrValues["comment_count"] = __("Number Of Comments","unlimited-elements-for-elementor");
		$arrValues["rand"] = __("Random","unlimited-elements-for-elementor");
		$arrValues["none"] = __("Unsorted","unlimited-elements-for-elementor");
		$arrValues["menu_order"] = __("Menu Order","unlimited-elements-for-elementor");
		$arrValues["parent"] = __("Parent Post","unlimited-elements-for-elementor");
		
		$output = array();

		foreach($arrValues as $type=>$title){
			$output[] = array("type"=>$type,"title"=>$title);
		}

		return($output);
	}


	/**
	 * get sort filter repeater fields
	 */
	public static function getSortFilterRepeaterFields(){

		$settings = new UniteCreatorSettings();

		//--- field type -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrSort = UniteFunctionsWPUC::getArrSortBy(true, true);

		$arrSort = array_flip($arrSort);

		$settings->addSelect("type", $arrSort, __("Field Type","unlimited-elements-for-elementor"),"default",$params);


		//--- field Title -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		$settings->addTextBox("title", "", __("Field Title","unlimited-elements-for-elementor"),$params);
		
		
		if(UniteCreatorWpmlIntegrate::isWpmlExists()){
			
			$objWPML = new UniteCreatorWpmlIntegrate();
			$arrLanguages = $objWPML->getLanguagesShort(false, true);
			
			if(empty($arrLanguages))
				$arrLanguages = array();
				
			foreach($arrLanguages as $lang=>$langName){
				
				$settings->addTextBox("title_{$lang}", "", __("Field Title - ","unlimited-elements-for-elementor").$langName,$params);
			}
			
		}
		
		
		//--- meta field name -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = array("type"=>"meta");

		$settings->addTextBox("meta_name", "", __("Meta Field Name","unlimited-elements-for-elementor"),$params);
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrMetaType = array("Text"=>"text","Number"=>"number");

		$settings->addSelect("meta_type", $arrMetaType, __("Meta Type","unlimited-elements-for-elementor"),"text",$params);


		return($settings);
	}


	/**
	 * get data for meta compare select
	 */
	public static function getArrMetaCompareSelect(){

		$arrItems = array();
		$arrItems["="] = "Equals";
		$arrItems["!="] = "Not Equals";
		$arrItems[">"] = "More Then";
		$arrItems["<"] = "Less Then";
		$arrItems[">="] = "More Or Equal";
		$arrItems["<="] = "Less Or Equal";
		$arrItems["LIKE"] = "LIKE";
		$arrItems["NOT LIKE"] = "NOT LIKE";

		$arrItems["IN"] = "IN";
		$arrItems["NOT IN"] = "NOT IN";
		$arrItems["BETWEEN"] = "BETWEEN";
		$arrItems["NOT BETWEEN"] = "NOT BETWEEN";

		$arrItems["EXISTS"] = "EXISTS";
		$arrItems["NOT EXISTS"] = "NOT EXISTS";

		return($arrItems);
	}


	/**
	 * get date select
	 */
	public static function getArrPostsDateSelect(){

		$arrDate = array(
			"all"=>__("All","unlimited-elements-for-elementor"),
			"this_day"=>__("Today","unlimited-elements-for-elementor"),
			"today"=>__("Past Day","unlimited-elements-for-elementor"),
			"yesterday"=>__("Past 2 days","unlimited-elements-for-elementor"),

			"past_from_today"=>__("Past From Today","unlimited-elements-for-elementor"),
			"past_from_yesterday"=>__("Past From Yesterday","unlimited-elements-for-elementor"),

			"this_week"=>__("This Week","unlimited-elements-for-elementor"),
			"next_week"=>__("Next Week","unlimited-elements-for-elementor"),
			"week"=>__("Past Week","unlimited-elements-for-elementor"),

			"month"=>__("Past Month","unlimited-elements-for-elementor"),
			"three_months"=>__("Past 3 Months","unlimited-elements-for-elementor"),
			"year"=>__("Past Year","unlimited-elements-for-elementor"),
			"this_month"=>__("This Month","unlimited-elements-for-elementor"),
			"next_month"=>__("Next Month","unlimited-elements-for-elementor"),

			"future"=>__("Future From Today","unlimited-elements-for-elementor"),
			"future_tomorrow"=>__("Future From Tomorrow","unlimited-elements-for-elementor"),
			"custom"=>__("Custom","unlimited-elements-for-elementor")
		);

		return($arrDate);
	}

	/**
	 * get select post status
	 */
	public static function getArrPostStatusSelect(){

		$arrStatus = array(
			"publish"=>__("Publish","unlimited-elements-for-elementor"),
			"future"=>__("Future","unlimited-elements-for-elementor"),
			"draft"=>__("Draft","unlimited-elements-for-elementor"),
			"pending"=>__("Pending Review","unlimited-elements-for-elementor"),
			"private"=>__("Private","unlimited-elements-for-elementor"),
			"inherit"=>__("Inherit","unlimited-elements-for-elementor"),
		);

		return($arrStatus);
	}

	/**
	 * get array for users order by select
	 */
	public static function getArrUsersOrderBySelect(){

		$arrOrderby = array(
			"default"=>__("Default", "unlimited-elements-for-elementor"),
			"ID"=>__("User ID", "unlimited-elements-for-elementor"),
			"manual"=>__("Manual Order", "unlimited-elements-for-elementor"),
			"display_name"=>__("Display Name", "unlimited-elements-for-elementor"),
			"name"=>__("Username", "unlimited-elements-for-elementor"),
			"login"=>__("User Login", "unlimited-elements-for-elementor"),
			"nicename"=>__("Nice Name", "unlimited-elements-for-elementor"),
			"email"=>__("Email", "unlimited-elements-for-elementor"),
			"url"=>__("User Url", "unlimited-elements-for-elementor"),
			"registered"=>__("Registered Date", "unlimited-elements-for-elementor"),
			"post_count"=>__("Number of Posts", "unlimited-elements-for-elementor")
		);

		return($arrOrderby);
	}

	/**
	 * get remote parent names
	 */
	public static function getArrRemoteParentNames($isSecond = false, $putCustom = true){

		$arrNames = array();

		if($isSecond == false)
			$arrNames["auto"] = __("Auto Detectable", "unlimited-elements-for-elementor");

		$arrNames["first"] = __("First", "unlimited-elements-for-elementor");
		$arrNames["second"] = __("Second", "unlimited-elements-for-elementor");
		$arrNames["third"] = __("Third", "unlimited-elements-for-elementor");
		$arrNames["fourth"] = __("Fourth", "unlimited-elements-for-elementor");

		if($isSecond == false && $putCustom == true)
			$arrNames["custom"] = __("Custom Name", "unlimited-elements-for-elementor");

		return($arrNames);
	}

	/**
	 * get remote sync names
	 */
	public static function getArrRemoteSyncNames(){

		$arrNames = array();
		$arrNames["group1"] = __("Sync Group 1", "unlimited-elements-for-elementor");
		$arrNames["group2"] = __("Sync Group 2", "unlimited-elements-for-elementor");
		$arrNames["group3"] = __("Sync Group 3", "unlimited-elements-for-elementor");
		$arrNames["group4"] = __("Sync Group 4", "unlimited-elements-for-elementor");
		$arrNames["group5"] = __("Sync Group 5", "unlimited-elements-for-elementor");
		$arrNames["group6"] = __("Sync Group 6", "unlimited-elements-for-elementor");
		$arrNames["group7"] = __("Sync Group 7", "unlimited-elements-for-elementor");
		$arrNames["group8"] = __("Sync Group 8", "unlimited-elements-for-elementor");
		$arrNames["group9"] = __("Sync Group 9", "unlimited-elements-for-elementor");
		$arrNames["group10"] = __("Sync Group 10", "unlimited-elements-for-elementor");

		return($arrNames);
	}

	/**
	 * get gallery defaults
	 */
	public static function getArrDynamicGalleryDefaults(){
		
		$urlImages = GlobalsUC::$urlPluginImages;
		
		$arrItems = array();

		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery1.jpg","title"=>"Gallery 1 Title");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery2.jpg","title"=>"Gallery 2 Title");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery3.jpg","title"=>"Gallery 3 Title");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery4.jpg","title"=>"Gallery 4 Title");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery5.jpg","title"=>"Gallery 5 Title");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery6.jpg","title"=>"Gallery 6 Title");

		return($arrItems);
	}



	/**
	 * get post addditions array from options
	 */
	public static function getPostAdditionsArray_fromAddonOptions($arrOptions){

		$arrAdditions = array();

		$enableCustomFields = UniteFunctionsUC::getVal($arrOptions, "dynamic_post_enable_customfields");
		$enableCustomFields = UniteFunctionsUC::strToBool($enableCustomFields);

		$enableCategory = UniteFunctionsUC::getVal($arrOptions, "dynamic_post_enable_category");
		$enableCategory = UniteFunctionsUC::strToBool($enableCategory);

		/*
		$enableTaxonomies = UniteFunctionsUC::getVal($this->addonOptions, "dynamic_post_enable_taxonomies");
		$enableTaxonomies = UniteFunctionsUC::strToBool($enableTaxonomies);
		*/

		if($enableCustomFields == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS;

		if($enableCategory == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CATEGORY;


		return($arrAdditions);
	}


	/**
	 * get post data additions
	 */
	public static function getPostDataAdditions($addCustomFields, $addCategory){

		$arrAdditions = array();

		$addCustomFields = UniteFunctionsUC::strToBool($addCustomFields);
		$addCategory = UniteFunctionsUC::strToBool($addCategory);

		if($addCustomFields == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS;

		if($addCategory == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CATEGORY;

		return($arrAdditions);
	}

	/**
	 * get image sizes param from post list param
	 */
	public static function getImageSizesParamFromPostListParam($paramImage){
		
    	$type = UniteFunctionsUC::getVal($paramImage, "type");
    	$title = UniteFunctionsUC::getVal($paramImage, "title");
    	$name = UniteFunctionsUC::getVal($paramImage, "name");

    	$copyKeys = array("enable_condition","condition_attribute","condition_operator","condition_value");
		
    	$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();
		    	
    	$arrSizes = array_flip($arrSizes);

    	$param = array();
    	$param["type"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

    	if($type == UniteCreatorDialogParam::PARAM_POSTS_LIST){
	    	$param["title"] = $title .= " ".__("Image Size","unlimited-elements-for-elementor");
    		$param["name"] = $name .= "_imagesize";
    	}
    	else{
	    	$param["title"] = $title .= " ".__("Size","unlimited-elements-for-elementor");
    		$param["name"] = $name .= "_size";
    	}

    	$param["options"] = $arrSizes;
    	$param["default_value"] = "medium_large";

    	//duplicate all keys
    	foreach($copyKeys as $key)
    		$param[$key] = UniteFunctionsUC::getVal($paramImage, $key);
		    		
		return($param);
	}
	
	
    /**
     * get white label settings
     */
    public static function getWhiteLabelSettings(){

        $activateWhiteLabel = HelperUC::getGeneralSetting("activate_white_label");
        $activateWhiteLabel = UniteFunctionsUC::strToBool($activateWhiteLabel);

        if($activateWhiteLabel == false)
            return(null);

        $whiteLabelText = HelperUC::getGeneralSetting("white_label_page_builder");
        if(empty($whiteLabelText))
            return(null);

            $whiteLabelSingle = HelperUC::getGeneralSetting("white_label_single");
            if(empty($whiteLabelSingle))
                return(null);

            $arrSettings = array();
            $arrSettings["plugin_text"] = trim($whiteLabelText);
            $arrSettings["single"] = trim($whiteLabelSingle);

           return($arrSettings);
    }

    

	/**
	 * modify memory limit setting
	 */
	public static function modifyGeneralSettings_memoryLimit($objSettings){

		//modify memory limit

		$memoryLimit = ini_get('memory_limit');
		$htmlLimit = "<b> {$memoryLimit} </b>";

		$isGB = false;

		if(strpos($memoryLimit, "G") !== false)
			$isGB = true;

		$numLimit = (int)$memoryLimit;

		if($numLimit < 10 && $isGB == true)
			$numLimit *= 1024;

		if($numLimit < 512)
			$htmlLimit .= "<div style='color:red;font-size:13px;padding-top:4px;'> Recommended 512M, please increase php memory.</div>";

		$setting = $objSettings->getSettingByName("memory_limit_text");
		if(empty($setting))
			UniteFunctionsUC::throwError("Must be memory limit troubleshooter setting");

		$setting["text"] = str_replace("[memory_limit]", $htmlLimit, $setting["text"]);
		$objSettings->updateArrSettingByName("memory_limit_text", $setting);


		return($objSettings);
	}


	/**
	 * add all post types
	 */
	private static function modifyGeneralSettings_postType(UniteSettingsUC $objSettings){

		$arrPostTypes = UniteFunctionsWPUC::getPostTypesAssoc();

		if(count($arrPostTypes) <= 2)
			return($objSettings);

		unset($arrPostTypes["elementor_library"]);
		unset($arrPostTypes["uc_layout"]);
		unset($arrPostTypes[GlobalsProviderUC::POST_TYPE_LAYOUT]);

		$arrPostTypes = array_flip($arrPostTypes);

		$objSettings->updateSettingItems("post_types", $arrPostTypes);


		return($objSettings);
	}


	/**
	 * modify general settings
	 */
	private static function modifyGeneralSettings(UniteSettingsUC $objSettings){

		//update memory limit

		$objSettings = self::modifyGeneralSettings_postType($objSettings);


		return($objSettings);
	}
	
	private function _______GOOGLE_SHEETS_SETTINGS_AND_DATA_________(){}
	
	
	/**
	 * add google sheets repeater settings
	 */
	public static function addGoogleSheetsRepeaterSettings($objSettings, $name, $condition){
		
		$objIntegrations = UniteCreatorAPIIntegrations::getInstance();
		
		$arrFields = $objIntegrations->getGoogleSheetsSettingsFields();
		
		$objSettings = self::addSettingsFields($objSettings, $arrFields, $name,$condition);
		
		return($objSettings);
	}
	
	/**
	 * get google sheets data
	 */
	public static function getRepeaterItems_sheets($arrValues, $name, $showDebugData){
		
		$objIntegrations = new UniteCreatorAPIIntegrations();
		
		if($showDebugData == true)
			dmp("Getting data from google sheet.");
		
		$arrItems = $objIntegrations->getGoogleSheetsData($arrValues, $name);
		
		return($arrItems);
	}
	
	private function _______CSV_SETTINGS_AND_DATA_________(){}
	
	/**
	 * add json and csv repeater settings
	 */
	public static function addJsonCsvRepeaterSettings($objSettings, $name, $condition){
		
		//-------------- csv location ----------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $condition;

		$text = __("JSON or CSV Location", "unlimited-elements-for-elementor");

		$arrLocations = array();
		$arrLocations["textarea"] = __("Dynamic Textarea", "unlimited-elements-for-elementor");
		$arrLocations["url"] = __("Url", "unlimited-elements-for-elementor");

		$arrLocations = array_flip($arrLocations);

		$objSettings->addSelect($name."_json_csv_location", $arrLocations, $text, "textarea", $params);

		//-------------- dynamic field ----------------

		$conditionField = $condition;
		$conditionField[$name."_json_csv_location"] = "textarea";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTAREA;
		$params["elementor_condition"] = $conditionField;
		$params["description"] = __("Put some JSON data or CSV data of array with the items, or choose from dynamic field", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;

		$text = __("JSON or CSV Items Data", "unlimited-elements-for-elementor");

		$objSettings->addTextBox($name."_json_csv_dynamic_field", "", $text, $params);

		//-------------- csv url ----------------

		$conditionUrl = $condition;
		$conditionUrl[$name."_json_csv_location"] = "url";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = $conditionUrl;
		$params["description"] = __("Enter url of the the file or webhook. inside or outside of the website", "unlimited-elements-for-elementor");
		$params["placeholder"] = "Example: https://yoursite.com/yourfile.json";
		$params["add_dynamic"] = true;
		$params["label_block"] = true;

		$text = __("Url with the JSON or CSV", "unlimited-elements-for-elementor");
		
		$objSettings->addTextBox($name."_json_csv_url", "", $text, $params);
		
		//-------------- main key ----------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = $condition;
		$params["description"] = __("Optional. Enter the main array key where the actual data is located. Also paths like item.subitem can be used.", "unlimited-elements-for-elementor");
		$params["placeholder"] = "";
		$params["add_dynamic"] = true;
		$params["label_block"] = false;

		$text = __("Main Array Key", "unlimited-elements-for-elementor");
		
		$objSettings->addTextBox($name."_json_csv_mainkey", "", $text, $params);
		
		return($objSettings);
	}
	
	/**
	 * get repeater items - from json
	 */
	public static function getRepeaterItems_json($arrValues, $name, $showDebugData = false, $showDebugContent = false){
		
		$contentLocation = UniteFunctionsUC::getVal($arrValues, $name."_json_csv_location");

		if($contentLocation == "url"){

			$url = UniteFunctionsUC::getVal($arrValues, $name."_json_csv_url");

			if(empty($url)){

				if($showDebugData)
					dmp("no url found for json csv");

				return(null);
			}
			
			$dynamicFieldValue = HelperUC::$operations->getUrlContents($url, $showDebugData);
			
		}else{
			$dynamicFieldValue = UniteFunctionsUC::getVal($arrValues, $name."_json_csv_dynamic_field");
		}

		if(empty($dynamicFieldValue)){

			if($showDebugData)
				dmp("no data given in the dynamic field");

			return(null);
		}

		//try json

		$arrData = UniteFunctionsUC::maybeJsonDecode($dynamicFieldValue);
		
		//debug JSON

		if($showDebugData == true && is_array($arrData)){

			dmp("JSON data found ");
			//dmp($arrData);
			
			dmp("------------------------------");
		}

		//if not, try csv
		if(is_array($arrData) == false){
			$arrData = UniteFunctionsUC::maybeCsvDecode($arrData);

			//debug CSV
	
			if($showDebugData == true && is_array($arrData)){
	
				dmp("CSV data found ");
				dmp("------------------------------");
				
				//dmp($arrData);
			}
			
		}
		
		$arrDataOriginal = $arrData;
		
		//get by main key
		$mainKey = UniteFunctionsUC::detectArrayMainKey($arrData);
		
		if(!empty($mainKey) && isset($arrData[$mainKey]) && is_array($arrData[$mainKey])){
			$arrData = UniteFunctionsUC::getVal($arrData, $mainKey);
		}
				
		//debug content
		
		if($showDebugContent == true){
			
			if(!empty($mainKey))
				dmp("Main key detected: <b>$mainKey</b>");
						
			dmp("Original Data Found: ");
			
			HelperHtmlUC::putHtmlDataDebugBox($arrDataOriginal);
		}

		if(is_array($arrData) == false){

			if($showDebugData == true){
				dmp("No CSV or JSON data found. The input is: ");
				echo "<div style='background-color:lightgray'>";
				dmp(htmlspecialchars($dynamicFieldValue));
				echo "</div>";
				dmp("------------------------------");
			}

			return(null);
		}

		//trim by main key
		
		$dataMainKey = UniteFunctionsUC::getVal($arrValues, $name."_json_csv_mainkey");
		
		if(!empty($dataMainKey)){
			$arrData = UniteFunctionsUC::getArrayValueByPath($arrData, $dataMainKey);
			
			if(empty($arrData) && $showDebugData == true)
				dmp("data by main key: <b>$dataMainKey</b> not found");
			
		}
		
		if($showDebugData == true && is_array($arrData) && !empty($dataMainKey)){
			dmp("get the array data from the key: {$dataMainKey}");
		}
		
		return($arrData);
	}
	
	
	private function _______REPEATER_SETTINGS_AND_DATA_________(){}
	
	/**
	 * get demo repeater items
	 */
	public static function getDemoRepeaterItems(){
	    // Define demo items
	    $demoItems = array(
	        array(
	            'Column1' => 'Data 1A',
	            'Column2' => 'Data 2A',
	            'Column3' => 'Data 3A',
	            'Column4' => 'Data 4A',
	        ),
	        array(
	            'Column1' => 'Data 1B',
	            'Column2' => 'Data 2B',
	            'Column3' => 'Data 3B',
	            'Column4' => 'Data 4B',
	        ),
	        array(
	            'Column1' => 'Data 1C',
	            'Column2' => 'Data 2C',
	            'Column3' => 'Data 3C',
	            'Column4' => 'Data 4C',
	        ),
	    );
	
	    // Return the demo items
	    return $demoItems;
	}
	
	/**
	 * add repeater settings
	 */
	public static function addRepeaterSettings($objSettings, $name, $condition = null, $addDebug = false, $addSource = false){
		
		$conditionDebugData = $condition;
		$conditionDebugMeta = $condition;
		
		if($addSource == true){
			
			// ------- repeater source
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
	
			$arrType = array();
			$arrType["meta"] = __("Meta Fields", "unlimited-elements-for-elementor");
			$arrType["json"] = __("JSON / CSV", "unlimited-elements-for-elementor");
			$arrType["sheets"] = __("Google Sheets", "unlimited-elements-for-elementor");
			
			$arrType = array_flip($arrType);
			
			$objSettings->addSelect($name."_repeater_source", $arrType, __("Repeater Source", "unlimited-elements-for-elementor"), "meta", $params);
			
			// ------- hr 
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
			
			$objSettings->addHr($name."_after_repeater_source", $params);
			
			$condition = array($name."_repeater_source"=>"meta");
			
			$conditionDebugData = null;
			$conditionDebugMeta = $condition;

			//------- json / csv
			
			$conditionJson = array($name."_repeater_source"=>"json");
			
			$objSettings = self::addJsonCsvRepeaterSettings($objSettings, $name, $conditionJson);
			
			//------- google sheets 
			
			$conditionSheets = array($name."_repeater_source"=>"sheets");
			
			$objSettings = self::addGoogleSheetsRepeaterSettings($objSettings, $name, $conditionSheets);
			
		}
		
		
		$isAcfExists = UniteCreatorAcfIntegrate::isAcfActive();

		//-------------- repeater meta name ----------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		if(!empty($condition))
			$params["elementor_condition"] = $condition;
	
		if($isAcfExists == false)
			$params["description"] = __("Choose meta field name it should be some array at the output", "unlimited-elements-for-elementor");
		else
			$params["description"] = __("Choose ACF field name. Repeater, Media, or types with items array output", "unlimited-elements-for-elementor");


		if($isAcfExists == false)
			$text = __("Meta Field Name", "unlimited-elements-for-elementor");
		else
			$text = __("ACF Field Name", "unlimited-elements-for-elementor");

		$objSettings->addTextBox($name."_repeater_name", "", $text, $params);

		// --- fields location -----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		if(!empty($condition))
			$params["elementor_condition"] = $condition;

		if($isAcfExists == false)
			$text = __("Meta Field Location", "unlimited-elements-for-elementor");
		else
			$text = __("ACF Field Location", "unlimited-elements-for-elementor");

		$arrLocations = array();
		$arrLocations["current_post"] = __("Current Post", "unlimited-elements-for-elementor");
		$arrLocations["parent_post"] = __("Parent Post", "unlimited-elements-for-elementor");
		$arrLocations["selected_post"] = __("Select Post", "unlimited-elements-for-elementor");
		$arrLocations["current_term"] = __("Current Term", "unlimited-elements-for-elementor");
		$arrLocations["parent_term"] = __("Parent Term", "unlimited-elements-for-elementor");
		$arrLocations["current_user"] = __("Current User", "unlimited-elements-for-elementor");

		$arrLocations = array_flip($arrLocations);
		
		$objSettings->addSelect($name."_repeater_location", $arrLocations, $text, "current_post", $params);

		// --- location post select -----------

		if($isAcfExists == false)
			$text = __("Meta Field From Post", "unlimited-elements-for-elementor");
		else
			$text = __("ACF Field From Post", "unlimited-elements-for-elementor");

		$conditionRepeaterPost = $condition;
		
		if(empty($conditionRepeaterPost))
			$conditionRepeaterPost = array();
		
		$conditionRepeaterPost[$name."_repeater_location"] = "selected_post";

		$objSettings->addPostIDSelect($name."_repeater_post", $text, $conditionRepeaterPost, "single");
		
		if($addDebug == false)
			return(false);

		// ----- ADD DEBUG OPTIONS
			
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
			
		$objSettings->addHr($name."_repeater_before_debug", $params);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		if(!empty($conditionDebugData))
			$params["elementor_condition"] = $conditionDebugData;
		
		$objSettings->addRadioBoolean($name."_repeater_debug_data", __("Show Debug Data", "unlimited-elements-for-elementor"), false,"Yes", "No", $params);
		
		if(!empty($conditionDebugMeta))
			$params["elementor_condition"] = $conditionDebugMeta;
		
		$objSettings->addRadioBoolean($name."_repeater_debug_meta", __("Show Debug Meta", "unlimited-elements-for-elementor"), false,"Yes", "No", $params);
		
	}
	
	/**
	 * print debug values
	 */
	private static function getRepeaterItems_printDebugValues($arrRepeaterItems){
				
		if(empty($arrRepeaterItems)){
			HelperHtmlUC::putHtmlDataDebugBox("Empty Response. No Repeater Items Found");
			return(false);
		}

		if(is_array($arrRepeaterItems) == false){
			HelperHtmlUC::putHtmlDataDebugBox("not array response");
			return(false);
		}
			
		$numItems = count($arrRepeaterItems);
		
		dmp("Final Response: <b style='color:blue;'>$numItems</b> Repeater Items:");
						
		HelperHtmlUC::putHtmlDataDebugBox($arrRepeaterItems);
		
	}
	
	/**
	 * get repeater data
	 */
	public static function getRepeaterItems($arrValues, $name, $showDebugData = false, $showDebugMeta = false){
		
		if(empty($arrValues))
			return(false);
		
		//get from another sources
		
		$repeaterSource = UniteFunctionsUC::getVal($arrValues, "{$name}_repeater_source");
		
		switch($repeaterSource){
			case "json":
				
				$arrRepeaterItems = self::getRepeaterItems_json($arrValues, $name, $showDebugData, $showDebugData);
				
				if($showDebugData == true)
					self::getRepeaterItems_printDebugValues($arrRepeaterItems);
				
				return($arrRepeaterItems);
			break;
			case "sheets":
				
				$arrRepeaterItems = self::getRepeaterItems_sheets($arrValues, $name, $showDebugData);
				
				if($showDebugData == true)
					self::getRepeaterItems_printDebugValues($arrRepeaterItems);
				
				return($arrRepeaterItems);
			break;
		}
				
			
		$repeaterName = UniteFunctionsUC::getVal($arrValues, "{$name}_repeater_name");

		$location = UniteFunctionsUC::getVal($arrValues, "{$name}_repeater_location");
		
		$arrRepeaterItems = array();

		$postID = null;
		$post = null;
		$termID = null;
		$userID = null;
		
		
		switch($location){
			case "selected_post":

				$repeaterPostID = UniteFunctionsUC::getVal($arrValues, "{$name}_repeater_post");

				if(empty($repeaterPostID) || is_numeric($repeaterPostID) == 0){

					if($showDebugData == true)
						dmp("wrong post id $repeaterPostID");
	
					return(null);
				}

				$postID = $repeaterPostID;

				$post = get_post($postID);

				if(empty($post)){

					if($showDebugData == true)
						dmp("post with id: $postID not found");

					return(null);
				}

			break;
			case "current_post":

				$post = get_post();

				if(empty($post)){

					if($showDebugData == true)
						dmp("get data from current post - no current post found");

					return(null);
				}

				$postID = $post->ID;

			break;
			case "parent_post":

				$post = get_post_parent();

				if(empty($post)){

					if($showDebugData == true)
						dmp("get data from parent post - no parent post found");

					return(null);
				}

				$postID = $post->ID;

			break;
			case "current_term":

				$termID = UniteFunctionsWPUC::getCurrentTermID();

				if(empty($termID)){

					if($showDebugData == true)
						dmp("get data from current term - no current term found. try to load from some category archive page.");

					return(null);
				}

			break;
			case "parent_term":

				$termID = UniteFunctionsWPUC::getCurrentTermID();

				if(empty($termID)){

					if($showDebugData == true)
						dmp("get parent term - no current term found. try to load from some category archive page.");

					return(null);
				}

				$termID = wp_get_term_taxonomy_parent_id($termID);

				if(empty($termID)){

					if($showDebugData == true)
						dmp("get parent term - no parent term found from term id: $termID. check this term if it has parent.");

					return(null);
				}

			break;
			case "current_user":

				$userID = get_current_user_id();

				if(empty($userID)){

					if($showDebugData == true)
						dmp("get current user no logged in user found.");

					return(null);
				}

			break;
			default:
				dmp("repeater location not found!");
				dmp("repeater - get data from location: $location");
			break;
		}
		
		
		$arrCustomFields = array();
		
		//---- load from post

		if(!empty($postID)){

			$arrCustomFields = UniteFunctionsWPUC::getPostCustomFields($postID, false);
		}

		//------ load from term

		if(!empty($termID)){

			$arrCustomFields = UniteFunctionsWPUC::getTermCustomFields($termID, false);
		}

		if(!empty($userID))
			$arrCustomFields = UniteFunctionsWPUC::getUserCustomFields($userID, false);
					
		
		//show debug meta text

		if($showDebugMeta == true){

			if(!empty($postID)){

				$text = "Post <b>".$post->post_title." ($postID)</b>";

				HelperUC::$operations->putCustomFieldsArrayDebug($arrCustomFields, $text);
			}

			if(!empty($termID)){

				$text = "Term <b>".$term->name." ($termID)</b>";

				HelperUC::$operations->putCustomFieldsArrayDebug($arrCustomFields, $text);
			}

			if(!empty($userID)){

				$text = "User <b>".$user["name"]." ($userID)</b>";

				HelperUC::$operations->putCustomFieldsArrayDebug($arrCustomFields, $text);
			}


			if(empty($repeaterName)){

				dmp("items from repeater: please enter repeater name");
				return(array());
			}

		}
		
		
		if($showDebugData == true){
			
			dmp("<b>Custom Fields Found!</b>");
			
			$arrShow = UniteFunctionsUC::modifyDataArrayForShow($arrCustomFields);
			
			dmp($arrShow);
		}
		
		
		//get the items

		$arrRepeaterItems = UniteFunctionsUC::getVal($arrCustomFields, $repeaterName);
		
		//show debug data text

		if($showDebugData == true){

			$text = "Getting meta data from field: <b>$repeaterName</b> from <b>$location</b>";
			
			switch($location){
				case "parent_post":
				case "selected_post":
				case "current_post":
						$text .= ", <b>".$post->post_title."</b>";
				break;
				case "current_term":
				case "parent_term":

					$term = get_term($termID);

					$text .= ", <b>".$term->name."</b>";
				break;
				case "current_user":

					$user = UniteFunctionsWPUC::getUserData($userID);

					$userName = UniteFunctionsUC::getVal($user, "name");

					$text .= ", <b>".$userName."</b>";

				break;
			}

			dmp($text);
		}


		//get the data from repeater

		if(empty($arrRepeaterItems) && !empty($postID) ){
			
			$previewID = UniteFunctionsUC::getGetVar("preview_id","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);

			if(!empty($previewID)){
				dmp("preview data from repeater: you are under elementor preview, the output may be wrong. Please open the post without the preview");
			}
			
		}

		//try to get the array type: field_array (output from acf)
		
		if(is_array($arrRepeaterItems) == false && !empty($arrRepeaterItems)){
			
			$arrRepeaterItems = UniteFunctionsUC::getVal($arrCustomFields, "{$repeaterName}_array");
			
			if(empty($arrRepeaterItems))
				return(array());
			
			$arrRepeaterItems = UniteFunctionsUC::arrayToArrAssocItems($arrRepeaterItems,"title");
			
			return($arrRepeaterItems);
		}
		
		
		//get demo data
		if(empty($arrRepeaterItems) && GlobalsProviderUC::$isInsideEditor == true){
			
			if($showDebugData == true)
				dmp("No repeater items found. Getting demo items in editor only");
			
			$arrRepeaterItems = self::getDemoRepeaterItems();
		}
				
		if($showDebugData == true)
			self::getRepeaterItems_printDebugValues($arrRepeaterItems);
		
		
		return($arrRepeaterItems);
	}
	
	private function _______SETTINGS_FIELDS_________(){}
	
	/**
	 * add settings fields
	 */
	public static function addSettingsFields($settingsManager, $fields, $name, $condition = null){
		
		foreach($fields as $field){
			
			$params = array();
			$params["origtype"] = $field["type"];
			$params["description"] = UniteFunctionsUC::getVal($field, "desc");
			$params["label_block"] = UniteFunctionsUC::getVal($field, "label_block", false);
			
			if(isset($field["placeholder"])) {
                $params["placeholder"] = $field["placeholder"];
            }

			if(!empty($condition))
				$params["elementor_condition"] = $condition;

            if (isset($field['conditions'])) {
                foreach($field['conditions'] as $condition_key => $field_condition){
                    $params["elementor_condition"][$name . "_" . $condition_key] = $field_condition;
                }
            }
				
			$paramName = $name . "_" . $field["id"];
			$paramDefault = isset($field["default"]) ? $field["default"] : "";
			
			switch($field["type"]){
				case UniteCreatorDialogParam::PARAM_STATIC_TEXT:
					$settingsManager->addStaticText($field["text"], $paramName, $params);
				break;
				case UniteCreatorDialogParam::PARAM_TEXTAREA:
					$params["add_dynamic"] = true;
					$settingsManager->addTextArea($paramName, $paramDefault, $field["text"], $params);
				break;
				case UniteCreatorDialogParam::PARAM_TEXTFIELD:
					
					$params["add_dynamic"] = true;
					
					$settingsManager->addTextBox($paramName, $paramDefault, $field["text"], $params);
				break;
				case UniteCreatorDialogParam::PARAM_DROPDOWN:
					$params["add_dynamic"] = true;
					$settingsManager->addSelect($paramName, array_flip($field["options"]), $field["text"], $paramDefault, $params);
				break;
                case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:
                    $settingsManager->addRadioBoolean($paramName, $field["text"], $paramDefault, "Yes", "No", $params);
                break;
				default:
					UniteFunctionsUC::throwError(__FUNCTION__ . " Error: Field type \"{$field["type"]}\" is not implemented");
			}
		}

		return $settingsManager;
	}
	
	
	private function _______OTHERS_________(){}
	

	/**
	 * check if layout editor plugin exists, or exists addons for it
	 */
	public static function isLayoutEditorExists(){

		$classExists = class_exists("LayoutEditorGlobals");
		if($classExists == true)
			return(true);

		return(false);
	}



	/**
	 * on plugins loaded, load textdomains
	 */
	public static function onPluginsLoaded(){
				
		GlobalsUC::initAfterPluginsLoaded();
				
		GlobalsUnlimitedElements::initAfterPluginsLoaded();
		
		load_plugin_textdomain("unlimited-elements-for-elementor", false, GlobalsUC::$pathWPLanguages);
		
		UniteCreatorWooIntegrate::initActions();
				
	}

	/**
	 * on php error message
	 */
	public static function onPHPErrorMessage($message, $error){

		$errorMessage = UniteFunctionsUC::getVal($error, "message");

		$file = UniteFunctionsUC::getVal($error, "file");
		$line = UniteFunctionsUC::getVal($error, "line");

		if(is_string($errorMessage))
			$message .= "Unlimited Elements Troubleshooting: \n<br><pre>{$errorMessage}</pre>";

		if(!empty($file))
			$message .= "in : <b>$file</b>";

		if(!empty($line))
			$message .= " on line <b>$line</b>";

		$arrDebug = HelperUC::getDebug();

		if(!empty($arrDebug))
			$message .= "<br>\nDebug: \n".print_r($arrDebug, true);
		else
			$message .= "<br>\n no other debug provided";

		$usage = memory_get_usage(true);

		$message .= "<br>\n Memory Usage: $usage";


		/*
		$arrTrace = debug_backtrace();

		if(!empty($arrTrace))
			$message .= "<br>\nTrace: \n".print_r($arrTrace, true);
		else
			$message .= "<br>\n no trace provided";
		*/

		return($message);
	}

	/**
	 * global init function that common to the admin and front
	 */
	public static function globalInit(){

		//disable deprecated warnings - global setting

		$disableDeprecated = HelperProviderCoreUC_EL::getGeneralSetting("disable_deprecated_warnings");
		$disableDeprecated = UniteFunctionsUC::strToBool($disableDeprecated);

		if($disableDeprecated == true)
			UniteFunctionsUC::disableDeprecatedWarnings();
		
		$showPHPError = HelperProviderCoreUC_EL::getGeneralSetting("show_php_error");
		$showPHPError = UniteFunctionsUC::strToBool($showPHPError);
		
		if($showPHPError == true)
			add_filter("wp_php_error_message", array("HelperProviderUC", "onPHPErrorMessage"), 100, 2);
		
		add_action("plugins_loaded", array("HelperProviderUC", "onPluginsLoaded"));
		
		//add_action("wp_loaded", array("HelperProviderUC", "onWPLoaded"));
	}

	/**
	 * on plugins loaded call plugin
	 */
	public static function onPluginsLoadedCallPlugins(){

		do_action("addon_library_register_plugins");

		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_EDIT_GLOBALS);


		//init woocommerce integration

		if(UniteCreatorWooIntegrate::isWooActive() == true){

			UniteCreatorWooIntegrate::initMiniCartIntegration();

		}


	}


	/**
	 * register plugins
	 */
	public static function registerPlugins(){

		add_action("plugins_loaded", array("HelperProviderUC","onPluginsLoadedCallPlugins"));

	}


	/**
	 * output custom styles
	 */
	public static function outputCustomStyles(){

	    $arrStyles = UniteProviderFunctionsUC::getCustomStyles();
	    if(!empty($arrStyles)){
	        echo "\n<!--   Unlimited Elements Styles  --> \n";

	        echo "<style type='text/css' id='unlimited-elements-styles'>";

	        foreach ($arrStyles as $style) {
				s_echo( $style . "\n");
	        }

	        echo "</style>\n";
	    }

	}


	/**
	 * print custom scripts
	 */
	public static function onPrintFooterScripts($isFront = false, $scriptType = "all"){

		//print custom styles
		if($scriptType != "js"){

			self::outputCustomStyles();
		}

		//print inline admin html

		if($isFront == false){

			//print inline html
			$arrHtml = UniteProviderFunctionsUC::getInlineHtml();
			if(!empty($arrHtml)){
				foreach($arrHtml as $html){
					s_echo($html);
				}
			}

		}

		//print custom JS script

		if($scriptType != "css"){
			
			$isSaparateScripts = HelperProviderCoreUC_EL::getGeneralSetting("js_saparate");
			$isSaparateScripts = UniteFunctionsUC::strToBool($isSaparateScripts);

			$arrScrips = UniteProviderFunctionsUC::getCustomScripts();
			$version = UNLIMITED_ELEMENTS_VERSION;

			if(!empty($arrScrips)){
				s_echo( "\n<!--   Unlimited Elements $version Scripts --> \n" );

				$arrScriptsOutput = array();
				$arrModulesOutput = array();

				foreach ($arrScrips as $key=>$script){

					$isModule = (strpos($key, "module_") !== false);

					if($isModule == true)
						$arrModulesOutput[$key] = $script;
					else
						$arrScriptsOutput[$key] = $script;
				}

				//print the scripts

				if(!empty($arrScriptsOutput)){

					if($isSaparateScripts == false){		//one script tag

						echo "<script type='text/javascript' id='unlimited-elements-scripts'>\n";

							foreach ($arrScriptsOutput as $script){
								s_echo($script."\n");
							}

						echo "</script>\n";
					}
					else{			//multiple script tags

						foreach ($arrScriptsOutput as $handle => $script){

							s_echo( "\n<script type='text/javascript' id='{$handle}'>\n");
							
							s_echo($script."\n");

							echo "</script>\n";
						}

					}


				}

				//print the modules

				if(!empty($arrModulesOutput)){

					foreach($arrModulesOutput as $script){

						echo "<script type='module'>\n";
						s_echo($script."\n");
						echo "</script>\n";

					}

				}

			}//if not empty scripts

		}//if js

	}


	/**
	 * change elementor template to page, by it's name
	 */
	public static function changeElementorTemplateToPage($templateID, $pageName){

		$pageName = trim($pageName);

		UniteFunctionsUC::validateNotEmpty($pageName,__("Page Name", "unlimited-elements-for-elementor"));

		$arrUpdate = array();
		$arrUpdate["post_type"] = "page";
		$arrUpdate["post_title"] = $pageName;
		$arrUpdate["post_name"] = "";

		UniteFunctionsWPUC::updatePost($templateID, $arrUpdate);

	}

	/**
	 *
	 * get imported template links
	 */
	public static function getImportedTemplateLinks($templateID){

		$urlTemplate = get_post_permalink($templateID);
		$urlEditWithElementor = UniteFunctionsWPUC::getPostEditLink_editWithElementor($templateID);

		$response = array();
		$response["url"] = $urlTemplate;
		$response["url_edit"] = $urlEditWithElementor;

		return($response);
	}

	/**
	 * get post term for template
	 //arg1 - postID
	 //arg2 - taxonomy
	 //arg3 - term slug
	 */
	public static function getPostTermForTemplate($arg1, $arg2, $arg3){

		if(is_numeric($arg1) == false)
			return(false);

		//no slug found
		if(empty($arg3) || empty($arg2)){

			dmp("get_post_term. please enter second or third parameter - taxonomy or slug ");

			$post = get_post($arg1);
			$arrTerms = UniteFunctionsWPUC::getPostTerms($post);

			dmp("post terms: ");
			dmp($arrTerms);

			return(null);
		}

		$term = UniteFunctionsWPUC::getPostTerm($arg1,$arg2,$arg3);

		return($term);
	}


	/**
	 * check if user has some operations permissions
	 */
	public static function isUserHasOperationsPermissions(){

		$permission = HelperProviderCoreUC_EL::getGeneralSetting("edit_permission");

		$capability = "manage_options";
		if($permission == "editor")
			$capability = "edit_pages";
		
		$isUserHasPermission = current_user_can($capability);

		return($isUserHasPermission);
	}

	/**
	 * verify admin permisison of the plugin, use it before ajax actions
	 */
	public static function verifyAdminPermission(){

		$hasPermission = self::isUserHasOperationsPermissions();

		if($hasPermission == false)
			UniteFunctionsUC::throwError("The user don't have permission to do this operation");
	}

	/**
	 * check if addon changelog is enabled
	 */
	public static function isAddonChangelogEnabled(){

		$isChangelogEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_changelog");
		$isChangelogEnabled = UniteFunctionsUC::strToBool($isChangelogEnabled);

		return $isChangelogEnabled;
	}


	/**
	 * check if addon changelog is enabled
	 */
	public static function isAddonChangelogImportDisabled(){

		$isChangelogImportDisabled = HelperProviderCoreUC_EL::getGeneralSetting("disable_import_changelog");
		$isChangelogImportDisabled = UniteFunctionsUC::strToBool($isChangelogImportDisabled);

		return $isChangelogImportDisabled;
	}

	/**
	 * verify if addon changelog is enabled, use it before ajax actions
	 */
	public static function verifyAddonChangelogEnabled(){

		$isChangelogEnabled = self::isAddonChangelogEnabled();

		if($isChangelogEnabled === false)
			UniteFunctionsUC::throwError("The changelog is disabled.");
	}

	/**
	 * check if addon revisions are enabled
	 */
	public static function isAddonRevisionsEnabled(){

		$isRevisionsEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_revisions");
		$isRevisionsEnabled = UniteFunctionsUC::strToBool($isRevisionsEnabled);

		return $isRevisionsEnabled;
	}

	/**
	 * verify if addon revisions are enabled, use it before ajax actions
	 */
	public static function verifyAddonRevisionsEnabled(){

		$isRevisionsEnabled = self::isAddonRevisionsEnabled();

		if($isRevisionsEnabled === false)
			UniteFunctionsUC::throwError("The revisions are disabled.");
	}

	/**
	 * check if backgrounds enabled
	 */
	public static function isBackgroundsEnabled(){
		
		if(GlobalsUnlimitedElements::$enableElementorSupport == false)
			return(false);
		
		$isBackgroundsEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_backgrounds");
		$isBackgroundsEnabled = UniteFunctionsUC::strToBool($isBackgroundsEnabled);

		return $isBackgroundsEnabled;
	}

	/**
	 * check if form entries are enabled
	 */
	public static function isFormEntriesEnabled(){

		$isEntriesEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_form_entries");
		$isEntriesEnabled = UniteFunctionsUC::strToBool($isEntriesEnabled);

		return $isEntriesEnabled;
	}

	/**
	 * check if form logs saving is enabled
	 */
	public static function isFormLogsSavingEnabled(){

		$isLogsSavingEnabled = HelperProviderCoreUC_EL::getGeneralSetting("save_form_logs");
		$isLogsSavingEnabled = UniteFunctionsUC::strToBool($isLogsSavingEnabled);

		return $isLogsSavingEnabled;
	}

	/**
	 * get google connect credentials
	 */
	public static function getGoogleConnectCredentials(){

		$credentials = HelperProviderCoreUC_EL::getGeneralSetting("google_connect_credentials");
		$credentials = UniteFunctionsUC::decodeContent($credentials);

		return $credentials;
	}

	/**
	 * save google connect credentials
	 */
	public static function saveGoogleConnectCredentials($credentials){

		$settings["google_connect_credentials"] = UniteFunctionsUC::encodeContent($credentials);

		HelperUC::$operations->updateUnlimitedElementsGeneralSettings($settings);
	}
	
	/**
	 * print wordpress filter callbacks
	 */
	public static function printFilterCallbacks($arrActions){
		
		if(empty($arrActions)){
			return(false);
		}
		
		$count = 0;
		foreach($arrActions as $order=>$arrCallbacks){
			
			if(is_array($arrCallbacks) == false){
				dmp($arrCallbacks);
				continue;
			}
			
			foreach($arrCallbacks as $function=>$arrCallback){
								
				$count++;
				
				$function = UniteFunctionsUC::getVal($arrCallback, "function");
				
				if(is_array($function) == false){
					
					if(isset($function) && is_object($function)){
						
						$className = get_class($function);
						
						dmp("{$count}. ".$className);
						
						if(empty($className) || $className == "Closure")
							dmp($function);
						
						continue;
					}
					
					dmp($function);
					continue;
				}
				
				if(count($function) == 1){
					dmp($function);
				}
				else{
					$object = $function[0];
					$method = $function[1];
					
					if(is_string($object))
						$className = $object;
					else
					if(is_object($object))
						$className = get_class($object);
					else
						$className = "";
					
					dmp("{$count}. "."{$className}->{$method}()");
				}
					
			}
			
		} //order foreach
				
	}
	
	private function _______DEBUG_________(){}
	
	/**
	 * remember the current query
	 */
	public static function startDebugQueries(){

		global $wpdb;
		$queries = $wpdb->queries;

		self::$numQueriesStart = count($queries);


	}

	/**
	 * print queries debug
	 * debug db queries debugdbquery
	 */
	public static function printDebugQueries($showTrace = false){

		global $wpdb;
		$queries = $wpdb->queries;

		if(empty($queries)){
			dmp("queries not collected");
			exit();
		}

		$numQueries = count($queries);

		dmp("num querie found: ".$numQueries);

		$start = 0;
		if(!empty(self::$numQueriesStart))
			$start = self::$numQueriesStart;

		if(!empty($start) && $start == $numQueries){

			dmp("nothing changed since the start : $start");
			exit();
		}

		if(!empty($start)){

			$numToShow = $numQueries - $start;

			dmp("Showing $numToShow queries");
		}

		echo "<div style='font-size:12px;color:black;'>";
		
		$numQuery = 0;
		
		foreach($queries as $index => $query){
			
			if($index < $start)
				continue;

			if(empty($query))
				continue;

			$numQuery++;
				
			$color = "";

			$sql = $query[0];

			$strTrace = $query[2];


			if(strpos($sql, "wp_postmeta") !== false)
				$color = "red";

			s_echo( "<div style='padding:10px;border-bottom:1px solid lightgray;color:$color'> $numQuery: {$sql} </div>");

			if($showTrace){
				echo "<div>";
				dmp($strTrace);
				echo "<div>";
			}

		}

		echo "<div style='font-size:10px;'>";

	}
	
	
	/**
	 * debug function
	 */
	public static function debugFunction($str){
		
		if(GlobalsProviderUC::$showDebugFunction == false)
			return(false);
			
		dmp($str);
	}
	
	
	/**
	 * show debug db tables
	 */
	public static function showDebugDBTables(){
		
		$db = HelperUC::getDB();
	
		$response = $db->fetchSql("SHOW TABLES");
		
		echo "<div style='padding-left:30px;padding-top:20px;'>";
		
		foreach($response as $row){
			
			if(is_string($row)){
				dmp($row);
				continue;
			}
			
			$value = UniteFunctionsUC::getArrFirstValue($row);
			
			dmp($value);
		}
		
		echo "</div>";
		
	}
	
	
	/**
	 * show last posts queries
	 */
	public static function showLastQueryPosts(){

		if(empty(GlobalsProviderUC::$lastPostQuery))
			return(false);

    	$arrLastPosts = GlobalsProviderUC::$lastPostQuery->posts;

    	if(empty($arrLastPosts))
    		return(false);

	    HelperUC::$operations->putPostsCustomFieldsDebug($arrLastPosts);

	}
	
	/**
	 * show posts debug
	 */
	public static function showPostsDebug($arrPosts,$includePostObject = false){
		
		HelperUC::$operations->putPostsFullDebug($arrPosts, $includePostObject);
	}
	
	/**
	 * show current user meta data for debug
	 */
	public static function showCurrentUserMetaDataDebug(){
		
		$userID = get_current_user_id();

		if(empty($userID))
			dmp("No current user found");
		else{
			$userData = UniteFunctionsWPUC::getUserData($userID);
			$username = UniteFunctionsUC::getVal($userData, "username");
			$userMeta = UniteFunctionsWPUC::getUserCustomFields($userID, false);
			
			$htmlFields = HelperHtmlUC::getHtmlArrayTable($userMeta, "No Meta Fields Found");
			
			dmp("User logged in: <b>$username</b>");
			
			dmp("Meta Data:");
			
			dmp($htmlFields);
		}
		
	}
	
	/**
	 * show current post meta debug
	 */
	public static function showCurrentPostMetaDebug(){
		
		$post = get_post();
		
		HelperUC::$operations->putPostCustomFieldsDebug($post->ID);
				
	}
	
	/**
	 * show current post meta debug
	 */
	public static function showCurrentPostTermsDebug(){
		
		$post = get_post();
		
		$arrTermsTitles = UniteFunctionsWPUC::getPostTermsTitles($post, true);
		
		$postTitle = $post->post_title;
		
		dmp("Post Terms for post <b>$postTitle</b>: ");
		dmp($arrTermsTitles);
		
	}
	
	

}
