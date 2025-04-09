<?php

class UniteCreatetorParamsProcessorMultisource{

	private $objProcessor;
	private $addon;
	private $name, $nameParam, $param, $processType, $inputData, $arrValues;
	private $isInsideEditor = false;
	private $itemsType;
	private $debugJsonCsv = false;
	private $showDebugData = false;
	private $showDataType = null;		//input / output / input_output
	private $showDebugMeta = false;
	private $addData = array();		//addition to return data
	private $arrDefaults = array();		//addition to return data
	private $arrItemsImageSizes = array();
	private $arrParamsItems = array();

	private static $showItemsDebug = false;		//show items debug next output


	const SOURCE_REPEATER = "repeater";
	const SOURCE_JSONCSV = "json_csv";
	const SOURCE_POSTS = "posts";
	const SOURCE_PRODUCTS = "products";
	const SOURCE_DEMO = "demo";
	const SOURCE_TERMS = "terms";
	const SOURCE_USERS = "users";
	const SOURCE_MENU = "menu";
	const SOURCE_INSTAGRAM = "instagram";
	const SOURCE_GALLERY = "gallery";
	const SOURCE_RSS = "rss";
	const SOURCE_API = "api";



	/**
	 *
	 * init the class
	 */
	public function init($objProcessor){

		$this->objProcessor = $objProcessor;
		$this->addon = $objProcessor->getAddon();

	}

	/**
	 * validate that the param size exists - if not - put error
	 */
	private function validateImageSizeExists(){
		
		//check if there is some size
		if(!empty($this->arrItemsImageSizes))
			return;
		
		//check if there is some params
		$params = $this->arrParamsItems;

		if(empty($params))
			return;

		// check that there is image param
		$imageTitle = null;

		foreach($params as $param){
			$type = UniteFunctionsUC::getVal($param, "type");
			
			if($type == UniteCreatorDialogParam::PARAM_IMAGE){
				$imageTitle = UniteFunctionsUC::getVal($param, "title");
			}
			
		}
		
		
		//some precautions
		if(empty($imageTitle))
			return;
		
		$addonTitle = $this->addon->getTitle();
		
		
		//if no image param - show some message
		HelperHtmlUC::outputErrorMessage("Multisource Error: Missing <b>image size attribute</b> for: <b>$imageTitle</b> image attribute. in widget: <b>$addonTitle</b>. Please add it to attributes list. Special Attribute -> Image Size");
		
	}


	private function _______GET_DATA________(){}

	/**
	 * get posts data
	 */
	private function getData_posts($forWooProducts = false){

		$paramPosts = $this->param;
		$paramPosts["name"] = $this->nameParam;
		$paramPosts["name_listing"] = $this->name;
		$paramPosts["use_for_listing"] = true;

		if($forWooProducts === true)
			$paramPosts["for_woocommerce_products"] = true;

		$dataResponse = $this->objProcessor->getPostListData($this->arrValues, $paramPosts["name"], $this->processType, $paramPosts, $this->inputData);

		//add the filters to output data
		$filteringAttributes = UniteFunctionsUC::getVal($dataResponse, "uc_filtering_attributes");
		$filteringAddClass = UniteFunctionsUC::getVal($dataResponse, "uc_filtering_addclass");

		if(!empty($filteringAttributes)){
			$this->addData["uc_filtering_attributes"] = $filteringAttributes;
			$this->addData["uc_filtering_addclass"] = $filteringAddClass;
		}

		$arrPosts = UniteFunctionsUC::getVal($dataResponse, $this->name . "_items");

		//debug meta - show meta fields
		if($this->showDebugMeta === true)
			HelperUC::$operations->putPostsCustomFieldsDebug($arrPosts, true);

		//get the post items array
		$arrPostItems = array();

		foreach($arrPosts as $post){
			$postItem = $this->objProcessor->getPostDataByObj($post, null, null, array("skip_images" => true));

			$arrPostItems[] = $postItem;
		}

		return $arrPostItems;
	}


	/**
	 * get terms data
	 */
	private function getData_terms(){

		$paramTerms = $this->param;

		$paramTerms["name"] = $this->nameParam;
		$paramTerms["name_listing"] = $this->name;
		$paramTerms["use_for_listing"] = true;

		$arrTerms = $this->objProcessor->getWPTermsData($this->arrValues, $paramTerms["name"], $this->processType, $paramTerms, $this->inputData);

		if($this->showDebugMeta == true)
			HelperUC::$operations->putTermsCustomFieldsDebug($arrTerms, true);


		return($arrTerms);
	}


	/**
	 * get users data
	 */
	private function getData_users(){

		$paramUsers = $this->param;

		$paramUsers["name"] = $this->nameParam;
		$paramUsers["name_listing"] = $this->name;
		$paramUsers["use_for_listing"] = true;

		$arrUsers = $this->objProcessor->getWPUsersData($this->arrValues, $paramUsers["name"], $this->processType, $paramUsers);

		return($arrUsers);
	}





	/**
	 * get menu data
	 */
	private function getData_menu(){

		$menuID = UniteFunctionsUC::getVal($this->arrValues, $this->nameParam."_id");
		$depth = UniteFunctionsUC::getVal($this->arrValues, $this->nameParam."_depth");

		$depth = (int)$depth;

		//get first menu
		if(empty($menuID))
			return(array());

		$isOnlyParents = false;
		if($depth == 1)
			$isOnlyParents = true;

		$arrItems = UniteFunctionsWPUC::getMenuItems($menuID, $isOnlyParents);

		if($this->showDebugMeta == true)
			HelperUC::$operations->putMenuCustomFieldsDebug($arrItems, true);

		return($arrItems);
	}



	/**
	 * get repeater data
	 */
	private function getData_repeater(){
		
		$arrRepeaterItems = HelperProviderUC::getRepeaterItems($this->arrValues, $this->name, $this->showDebugData, $this->showDebugMeta);

		return($arrRepeaterItems);
	}


	/**
	 * add dynamic field items
	 */
	private function getData_jsonCsv(){


		if($this->showDebugData == true){
			dmp("---- the debug is ON, please turn it off before release --- ");
		}

		$arrData = HelperProviderUC::getRepeaterItems_json($this->arrValues, $this->name, $this->showDebugData);
		
		return($arrData);
	}


	/**
	 * get instagram data
	 */
	private function getData_instagram(){

		$paramInstagram = $this->param;

		$paramInstagram["name"] = $this->nameParam;

		$arrData = $this->objProcessor->getInstagramData($this->arrValues, $this->nameParam, $paramInstagram);

		if(empty($arrData))
			return(array());

		$items = UniteFunctionsUC::getVal($arrData, "items");

		if(empty($items))
			return(array());

		//modify items - add type

		foreach($items as $key=>$item){

			unset($item["video_class"]);
			unset($item["num_video_views"]);
			unset($item["num_likes"]);
			unset($item["num_comments"]);

			$isVideo = UniteFunctionsUC::getVal($item, "isvideo");

			$item["type"] = $isVideo?"video":"image";

			$item["caption_text"] = UniteFunctionsUC::getVal($item, "caption");


			unset($item["isvideo"]);
			unset($item["caption"]);


			$items[$key] = $item;
		}

		return($items);
	}

	/**
	 * get api data
	 */
	private function getData_api(){

		// un-prefix param keys
		$prefix = $this->name . "_api_";
		$params = array();

		foreach($this->arrValues as $key => $value) {
			if (strpos($key, $prefix) === 0) {
				$key = str_replace($prefix, "", $key);

				$params[$key] = $value;
			}
		}

		// get api data
		$type = UniteFunctionsUC::getVal($params, "type");
		$data = UniteCreatorAPIIntegrations::getInstance()->getDataForMultisource($type, $params);

		return $data;
	}

	/**
	 * get gallery data
	 */
	private function getData_gallery(){

		$arrImages = UniteFunctionsUC::getVal($this->arrValues, $this->name."_gallery");

		if(empty($arrImages))
			return(array());

		$arrGallery = array();

		//cache
		$arrCacheIDs = array();

		foreach($arrImages as $index => $image){

			$id = UniteFunctionsUC::getVal($image, "id");
			if(!empty($id))
				$arrCacheIDs[] = $id;
		}

		//cache queries
		if(!empty($arrCacheIDs))
			UniteFunctionsWPUC::cachePostMetaQueries($arrCacheIDs);


		foreach($arrImages as $index => $image){

			$id = UniteFunctionsUC::getVal($image, "id");
			$url = UniteFunctionsUC::getVal($image, "url");

			$arrImage = array();

			//demo images

			if($id === 0){
				$counter = $index+1;
				$title = "Demo Image Title $counter";
				$caption = "Demo Image Caption $counter";
				$alt = "Demo Image Alt $counter";
				$description = "Demo Image Description $counter";

				$arrImage["image_imageid"] = $url;
				$arrImage["image_title"] = $title;
				$arrImage["image_alt"] = $alt;
				$arrImage["image_caption"] = $caption;
				$arrImage["image_description"] = $description;

				$arrGallery[] = $arrImage;
				continue;
			}

			//get image data

			$imageData = $this->getImageData($id);

			$arrGallery[] = $imageData;
		}


		return($arrGallery);
	}

    /**
     * get rss data
     */
    private function getData_rss(){
    	
    	$objRss = new UniteCreatorRSS();
    	
        $data = $objRss->getRssFeedData(array(), $this->arrValues, $this->name, $this->showDebugData);
		
        $dataRss = UniteFunctionsUC::getVal($data, $this->name);
        
        return $dataRss;
    }

	/**
	 * get multisource data
	 */
	private function getData($source){

		switch($source){
			case self::SOURCE_POSTS:
				return $this->getData_posts();
			case self::SOURCE_PRODUCTS:
				return $this->getData_posts(true);
			case self::SOURCE_REPEATER:
				return $this->getData_repeater();
			case self::SOURCE_JSONCSV:
				return $this->getData_jsonCsv();
			case self::SOURCE_TERMS:
				return $this->getData_terms();
			case self::SOURCE_USERS:
				return $this->getData_users();
			case self::SOURCE_MENU:
				return $this->getData_menu();
			case self::SOURCE_INSTAGRAM:
				return $this->getData_instagram();
			case self::SOURCE_GALLERY:
				return $this->getData_gallery();
            case self::SOURCE_RSS:
                return $this->getData_rss();
			case self::SOURCE_API:
				return $this->getData_api();
			default:
				UniteFunctionsUC::throwError("getData error, Wrong items source: $source");
		}
	}


	/**
	 * check debug vars before get data
	 */
	private function checkDebugBeforeData($itemsSource){

		switch($itemsSource){
			case self::SOURCE_JSONCSV:

				$isDebugJsonCsv = UniteFunctionsUC::getVal($this->arrValues, $this->name."_debug_jsoncsv_data");
				$isDebugJsonCsv = UniteFunctionsUC::strToBool($isDebugJsonCsv);

				if($isDebugJsonCsv == true)
					$this->debugJsonCsv = true;

				//show json and csv examples

				$isShowExamples = UniteFunctionsUC::getVal($this->arrValues, $this->name."_show_example_jsoncsv");
				$isShowExamples = UniteFunctionsUC::strToBool($isShowExamples);

				if($isShowExamples == true)
					$this->printJsonCsvExamples();

			break;
		}


	}

	/**
	 * print debug json or csv examples
	 */
	private function printJsonCsvExamples(){

		dmp("----- Show JSON CSV examples is ON. Please turn it OFF before release");

		//-------- show the json

		dmp("JSON content example:");

		$arrExample = array();
		$arrExample[] = array("title"=>"Google","number"=>10, "link"=>"https://google.com");
		$arrExample[] = array("title"=>"Yahoo","number"=>20,"link"=>"https://yahoo.com");
		$arrExample[] = array("title"=>"Bing","number"=>30,"link"=>"https://bing.com");

		$json = json_encode($arrExample);

		$css = "border:1px solid gray;background-color:lightgray;padding:10px;";

		s_echo("<div style='{$css}'>");
			dmp($json);
		echo "</div>";


		//------- show the csv

		dmp("CSV content example:");

		$csv = UniteFunctionsUC::arrayToCsv($arrExample);

		$css = "border:1px solid gray;background-color:lightgray;padding:10px;;margin-top:20px;margin-bottom:20px;";

		s_echo("<div style='{$css}'>");
			dmp($csv);
		echo "</div>";

		echo "<br>";
		echo "<br>";

	}



	/**
	 * get all fields from the values
	 */
	private function getFields(){

		$arrFields = array();

		foreach($this->arrValues as $key => $value){
			$prefix = $this->nameParam . "_field_source_";

			if(strpos($key, $prefix) === false)
				continue;

			$arrFields[$key] = $value;
		}

		return $arrFields;
	}

	private function _______GET_FIELD_VALUE________(){}


	/**
	* get image data by id
	 */
	private function getImageData($id){

		$itemParam = array();
		$itemParam["type"] = UniteCreatorDialogParam::PARAM_IMAGE;
		$itemParam["name"] = "image";

		$item = array();
		$item = $this->objProcessor->getProcessedParamData($item, $id, $itemParam, UniteCreatorParamsProcessorWork::PROCESS_TYPE_OUTPUT);

		return($item);
	}


	/**
	 * modify param value
	 */
	private function modifyParamValue($value, $param){

		$value = $this->addon->convertFromUrlAssets($value);


		$paramType = UniteFunctionsUC::getVal($param, "type");

		switch($paramType){
			case UniteCreatorDialogParam::PARAM_NUMBER:
			case UniteCreatorDialogParam::PARAM_SLIDER:

				//protection - set to default if not numeric
				if(is_string($value) && is_numeric($value) == false){

					if(empty($value))
						$value = 0;
					else
						$value = UniteFunctionsUC::getVal($param, "default_value");

				}

			break;
		}

		return($value);
	}

	/**
	 * get taxonomy values
	 */
	private function getTaxonomyValue($dataItem, $taxonomy){

		$postID = UniteFunctionsUC::getVal($dataItem, "id");

		if(empty($postID))
			return("");

		//select with taxonomy
		if(!empty($taxonomy)){

			$arrTitles = UniteFunctionsWPUC::getPostSingleTermsTitles($postID, $taxonomy);
		}else{

			//select withtout given taxonomy

			$post = get_post($postID);
			$arrTitles = UniteFunctionsWPUC::getPostTermsTitles($post, false);
		}

		if(empty($arrTitles))
			return("");

		$firstTerm = $arrTitles[0];

		return($firstTerm);
	}


	/**
	 * get meta key value from objects
	 */
	private function getMetaValue($dataItem, $metaKey){

		switch($this->itemsType){
			case self::SOURCE_MENU:
			case self::SOURCE_POSTS:
			case self::SOURCE_PRODUCTS:

				$postID = UniteFunctionsUC::getVal($dataItem, "id");

				$arrCustomFieldValues = UniteFunctionsWPUC::getPostCustomFields($postID, false);

				$value = UniteFunctionsUC::getVal($arrCustomFieldValues, $metaKey);

				//show debug
				if($this->showDebugData == true && $this->showDataType == "input"){

					$title = UniteFunctionsUC::getVal($dataItem, "title");

					$debugVal = $value;
					if(is_array($value))
						$debugVal = print_r($value, true);

					$strDebug = "Get meta <b>$metaKey</b> for post: $title ($postID) is: <b>$debugVal</b>";

					$this->outputDebugDataBox($strDebug);

				}


			break;
			case self::SOURCE_TERMS:

				$termID = UniteFunctionsUC::getVal($dataItem, "id");

				if(empty($termID))
					return("");

				$arrFields = UniteFunctionsWPUC::getTermCustomFields($termID, false);

				$value = UniteFunctionsUC::getVal($arrFields, $metaKey);


			break;
			case self::SOURCE_USERS:

				$userID = UniteFunctionsUC::getVal($dataItem, "id");

				$arrMeta = UniteFunctionsWPUC::getUserMeta($userID, array($metaKey));

				$value = UniteFunctionsUC::getVal($arrMeta, $metaKey);

			break;
			default:

				UniteFunctionsUC::throwError("getMetaValue error - wrong source: ".$this->itemsType);
			break;
		}


		return($value);
	}

	/**
	 * get user avatar image
	 */
	private function getUserAvatarImage($dataItem, $defaultValue){

		if($this->itemsType != self::SOURCE_USERS)
			return($defaultValue);

		$userID = UniteFunctionsUC::getVal($dataItem, "id");

		if(empty($userID))
			return($defaultValue);

		$arrImage = UniteFunctionsWPUC::getUserAvatarData($userID);

		$urlAvatar = UniteFunctionsUC::getVal($arrImage, "avatar_url");

		if(empty($urlAvatar))
			return($defaultValue);

		return($urlAvatar);

	}

	/**
	 * get number of posts of some user
	 */
	private function getUserNumPosts($dataItem, $defaultValue){

		if($this->itemsType != self::SOURCE_USERS)
			return($defaultValue);

		$userID = UniteFunctionsUC::getVal($dataItem, "id");

		if(empty($userID))
			return($defaultValue);

		$numPosts = count_user_posts($userID);

		return($numPosts);
	}


	/**
	 * get field data from data item
	 */
	private function getFieldValue($item, $paramName, $source, $dataItem, $param){
		
		
		//set as default value

		$defaultValue = UniteFunctionsUC::getVal($param, "default_value");

		$item[$paramName] = $defaultValue;

		if($source == "default" || empty($source)){

			$item = $this->objProcessor->getProcessedParamData($item, $defaultValue, $param, UniteCreatorParamsProcessorWork::PROCESS_TYPE_OUTPUT);

			return($item);
		}


		//some protections

		if(empty($dataItem))
			return($item);

		if(!is_array($dataItem))
			return($item);


		$isProcessReturn = false;

		//process multiple sources

		$textBefore = null;
		$textAfter = null;
		$sap = "";
		$isTruncate = false;

		$emptyItem = array();


		if(is_array($source)){

			foreach($source as $index => $singleSource){

				switch($singleSource){
					case "text_before":
						$emptyItem = $this->getFieldValue($emptyItem, $paramName, $singleSource, $dataItem, $param);
						$textBefore = UniteFunctionsUC::getVal($emptyItem, $paramName);
					break;
					case "text_after":
						$emptyItem = $this->getFieldValue($emptyItem, $paramName, $singleSource, $dataItem, $param);
						$textAfter = UniteFunctionsUC::getVal($emptyItem, $paramName);
					break;
					case "separator":
						$emptyItem = $this->getFieldValue($emptyItem, $paramName, $singleSource, $dataItem, $param);
						$sap = UniteFunctionsUC::getVal($emptyItem, $paramName);

						unset($source[$index]);
					break;
					case "truncate":

						$arrTruncate = $this->getFieldValue(array(), $paramName, $singleSource, $dataItem, $param);

						$truncateChars = UniteFunctionsUC::getVal($arrTruncate, $paramName,100);

						$isTruncate = true;
					break;
				}

			}
		}

		//convert from array to single

		if(is_array($source) && count($source) == 1){

			$firstSource = $source[0];

			if($firstSource == "text_before"
				|| $firstSource == "text_after"
				|| $firstSource == "separator"
				|| $firstSource == "truncate")
				$source = array($firstSource,"default");	//default + special one
			else
			 $source = $source[0];		//convert to simple
		}

		//multiple sources


		if(is_array($source)){

			$numItem = 0;

			foreach($source as $singleSource){

				if($singleSource == "text_before" || $singleSource == "text_after" || $singleSource == "truncate")
					continue;

				$numItem++;

				$value = UniteFunctionsUC::getVal($item, $paramName);

				$item = $this->getFieldValue($item, $paramName, $singleSource, $dataItem, $param);

				$valueAfter = UniteFunctionsUC::getVal($item, $paramName);

				if($numItem == 1){
					 $item[$paramName] = $valueAfter;
					 continue;
				}

				if(empty($value) && !empty($valueAfter))
					  $item[$paramName] = $valueAfter;

				if(empty($valueAfter) && !empty($value))
					  $item[$paramName] = $value;

				if(!empty($value) && !empty($valueAfter) &&
					is_array($value) == false && is_array($valueAfter) == false)
					  $item[$paramName] = $value.$sap.$valueAfter;
			}


			//add text before and after

			if(!isset($item[$paramName])){
			}

			//return if array - no text operations

			if(is_array($item[$paramName])){

				return($item);
			}

			//truncate content - if the truncate is in this sources

			if($isTruncate == true){

				$text = $item[$paramName];

				$text = UniteFunctionsUC::truncateString($text, $truncateChars, true, "");

				$item[$paramName] = $text;
			}


			if(!empty($textBefore))
				$item[$paramName] = $textBefore.$sap.$item[$paramName];

			if(!empty($textAfter))
				$item[$paramName] = $item[$paramName].$sap.$textAfter;


			return($item);
		}


		//process static value

		switch($source){
			case "text_before":
			case "text_after":
			case "separator":
			case "truncate":

				$textBeforeKey = $this->nameParam."_{$source}_{$paramName}";

				$value = UniteFunctionsUC::getVal($this->arrValues, $textBeforeKey);

				$item[$paramName] = $value;

				return($item);
			break;
			case "static_value":
				$staticValueKey = $this->nameParam."_field_value_{$paramName}";

				$value = UniteFunctionsUC::getVal($this->arrValues, $staticValueKey);

				$isProcessReturn = true;
			break;
			case "term_field":

				$taxonomyField = $this->nameParam."_field_taxonomy_{$paramName}";

				$taxonomy = UniteFunctionsUC::getVal($this->arrValues, $taxonomyField);

				$value = $this->getTaxonomyValue($dataItem, $taxonomy);

				$isProcessReturn = true;

			break;
			case "meta_field":

				$metaField = $this->nameParam."_field_meta_{$paramName}";

				$metaKey = UniteFunctionsUC::getVal($this->arrValues, $metaField);

				$value = $defaultValue;

				if(!empty($metaKey))
					$value = $this->getMetaValue($dataItem, $metaKey);

				$isProcessReturn = true;

			break;
			case "user_avatar_image":

				$value = $this->getUserAvatarImage($dataItem, $defaultValue);

				$isProcessReturn = true;
			break;
			case "user_num_posts":

				$value = $this->getUserNumPosts($dataItem, $defaultValue);

				$isProcessReturn = true;
			break;
		}


		//return the static value or meta field

		if($isProcessReturn == true){

			$value = $this->modifyParamValue($value, $param);

			$item[$paramName] = $value;

			//modify the image size

			$type = UniteFunctionsUC::getVal($param, "type");

			$item = $this->objProcessor->getProcessedParamData($item, $value, $param, UniteCreatorParamsProcessorWork::PROCESS_TYPE_OUTPUT);

			return($item);
		}


		//get the source name for field
		if($source == "field")
			$source = UniteFunctionsUC::getVal($this->arrValues, $this->nameParam."_field_name_".$paramName);
		
		//post values source

		$isFound = false;
		
		$value = UniteFunctionsUC::getArrayValueByPath($dataItem, $source);

		$isFound = ($value !== null);
		
		if($isFound == false && isset($dataItem[$source]))
			$isFound = true;

		if($isFound === true){
			$value = $this->modifyParamValue($value, $param);
			$item[$paramName] = $value;
			$item = $this->objProcessor->getProcessedParamData($item, $value, $param, UniteCreatorParamsProcessorWork::PROCESS_TYPE_OUTPUT);
		}
						
		//get children fields values
				
		foreach($dataItem as $name => $value){
						
			if($this->itemsType != self::SOURCE_GALLERY){

				if(strpos($name, $source."_") === 0){

					$suffix = substr($name, strlen($source));

					$item[$paramName.$suffix] = $value;
				}
			}
		}
		
		
		/**
		 * handle if param not found, process it anyway
		 */
		if($isFound == false){

			$value = $this->modifyParamValue($defaultValue, $param);

			$item = $this->objProcessor->getProcessedParamData($item, $value, $param, UniteCreatorParamsProcessorWork::PROCESS_TYPE_OUTPUT);
		}
		
		
		return($item);
	}

	private function _______DEBUG________(){}

	/**
	 * output debug data box
	 */
	private function outputDebugDataBox($text){
		
		echo "<div style='background-color:#E5F7E1;font-size:12px;padding:5px;'>";
		dmp($text);
		echo "</div>";
	}

	/**
	 * show debug
	 */
	private function showDebug_input($source, $arrData){

		if($this->showDataType == "output")
			return(false);

		if($this->showDebugData == false)
			return(false);

		HelperHtmlUC::putHtmlDataDebugBox_start();
		
			if($source == self::SOURCE_DEMO){
				dmp("Switching to demo data source in editor only.");
			}
	
			$numItems = 0;
	
			if(is_array($arrData))
				$numItems = count($arrData);
			
			dmp("Input data from: <b>$source</b>, found: $numItems");
			
			$arrData = UniteFunctionsUC::modifyDataArrayForShow($arrData);
			
			dmp($arrData);
		
		HelperHtmlUC::putHtmlDataDebugBox_end();
	}

	/**
	 * show debug
	 */
	private function showDebug_output($arrItems){

		if($this->showDebugData == false)
			return(false);

		if($this->showDataType == "input")
			return(false);

		echo "<div style='background-color:lightgray;font-size:12px;margin-top:20px;margin-bottom:20px;padding:5px;'>";

		dmp("------------------------------------------");

		dmp("input data settings");

		dmp($arrItems);

		echo "</div>";
	}


	private function _______GET_ITEMS________(){}


	/**
	 * get multisource items
	 */
	private function getItems($itemsSource, $arrData){

		if(empty($arrData) === true)
			return array();

		// get fields from settings
		$arrFields = $this->getFields();

		if(empty($arrFields) === true && GlobalsProviderUC::$isUnderAjax === false)
			UniteFunctionsUC::throwError("multisource getItems error: $itemsSource fields not found");

		//get items params
		$arrItemParams = $this->arrParamsItems;
		$arrItemParams = UniteFunctionsUC::arrayToAssoc($arrItemParams, "name");

		//update image sizes
		if(empty($this->arrItemsImageSizes) === false)
			$arrItemParams = $this->objProcessor->getProcessedItemsData_modifyImageItem($arrItemParams, $this->arrItemsImageSizes);

		$arrItems = array();

		foreach($arrData as $dataItem){
			$item = array();

			//get the fields values from data item
			$arrUsedParams = array();

			if($itemsSource !== self::SOURCE_DEMO){
				foreach($arrFields as $fieldKey => $source){
					$paramName = str_replace($this->nameParam . "_field_source_", "", $fieldKey);

					$param = UniteFunctionsUC::getVal($arrItemParams, $paramName);

					$item = $this->getFieldValue($item, $paramName, $source, $dataItem, $param);

					$arrUsedParams[$paramName] = true;
				}
			}

			//add other default fields
			foreach($arrItemParams as $itemParam){
				$paramName = UniteFunctionsUC::getVal($itemParam, "name");
			
				if(isset($arrUsedParams[$paramName]) === true)
					continue;

				$value = UniteFunctionsUC::getVal($itemParam, "default_value");
				$paramType = UniteFunctionsUC::getVal($itemParam, "type");

				//set from items defaults
				switch($this->itemsType){
					case self::SOURCE_PRODUCTS:
					case self::SOURCE_POSTS:
						if($paramName === "title")
							$value = UniteFunctionsUC::getVal($dataItem, "title");

						if($paramType === UniteCreatorDialogParam::PARAM_IMAGE)
							$value = UniteFunctionsUC::getVal($dataItem, "image");
					break;
					case self::SOURCE_TERMS:
					case self::SOURCE_USERS:
						if($paramName === "title")
							$value = UniteFunctionsUC::getVal($dataItem, "name");
					break;
					case self::SOURCE_MENU:
						if($paramName === "title")
							$value = UniteFunctionsUC::getVal($dataItem, "title");
					break;
					case self::SOURCE_INSTAGRAM:
						if($paramName === "title")
							$value = UniteFunctionsUC::getVal($dataItem, "caption_text");

						if($paramType === UniteCreatorDialogParam::PARAM_IMAGE)
							$value = UniteFunctionsUC::getVal($dataItem, "image");
					break;
				}

				//set from defined defaults if exists (param option)
				if(isset($this->arrDefaults[$paramName]) === true)
					$value = $this->arrDefaults[$paramName];

				$item[$paramName] = $value;

				$item = $this->objProcessor->getProcessedParamData($item, $value, $itemParam, UniteCreatorParamsProcessorWork::PROCESS_TYPE_OUTPUT);
			}

			//modify demo fields
			if($itemsSource === self::SOURCE_DEMO){
				$item["title"] = UniteFunctionsUC::getVal($dataItem, "title");
			}

			//add extra fields
			if(isset($dataItem["dynamic_popup_link_class"]) === true)
				$item["dynamic_popup_link_class"] = $dataItem["dynamic_popup_link_class"];

			if(isset($dataItem["dynamic_popup_link_attributes"]) === true)
				$item["dynamic_popup_link_attributes"] = $dataItem["dynamic_popup_link_attributes"];
			
			if(isset($dataItem["dynamic_popup_div_attributes"]) === true)
				$item["dynamic_popup_div_attributes"] = $dataItem["dynamic_popup_div_attributes"];

			$item["item_source"] = $itemsSource;

			$elementorID = UniteFunctionsUC::getRandomString(5);
			$item["item_repeater_class"] = "elementor-repeater-item-" . $elementorID;

			$arrItems[] = array("item" => $item);
		}

		return ($arrItems);
	}

	/**
	 * get demo data for editor
	 */
	private function getDemoDataForEditor(){

		$arrDemo = array();

		$arrDemo[] = array("title"=>"Demo Item 1",
							"link"=> "Demo Link 1",
							"number"=> 10
		);

		$arrDemo[] = array("title"=>"Demo Item 2",
							"link"=> "Demo Link 2",
							"number"=> 20
		);

		$arrDemo[] = array("title"=>"Demo Item 3",
							"link"=> "Demo Link 3",
							"number"=> 30
		);


		return($arrDemo);
	}

	/**
	 * get attribute defaults
	 */
	private function getAttributeDefaults($param){

		$strDefaults = UniteFunctionsUC::getVal($param, "multisource_attributes_defaults");

		$strDefaults = trim($strDefaults);

		if(empty($strDefaults))
			return(array());

		$arrDefaults = explode(",",$strDefaults);

		$arrValues = array();

		foreach($arrDefaults as $strDefault){

			$arrDefault = explode("=",$strDefault);

			if(count($arrDefault) != 2)
				continue;

			$key = trim($arrDefault[0]);
			$value = trim($arrDefault[1]);

			$arrValues[$key] = $value;
		}

		return($arrValues);
	}


	/**
	 * get multisource data
	 */
	public function getMultisourceSettingsData($value, $name, $processType, $param, $data){
		
		$this->isInsideEditor = HelperUC::isEditMode();

		$itemsSource = UniteFunctionsUC::getVal($value, $name . "_source");

		if(empty($itemsSource) === true)
			$itemsSource = "items";

		//free type always items
		if(strpos($itemsSource, "_free") !== false)
			$itemsSource = "items";

		//set the inputs
		$this->arrValues = $value;
		$this->name = $name;
		$this->nameParam = $name . "_" . $itemsSource;
		$this->param = $param;
		$this->processType = $processType;
		$this->inputData = $data;
		$this->itemsType = $itemsSource;
		$this->arrDefaults = $this->getAttributeDefaults($param);
		$this->arrItemsImageSizes = $this->objProcessor->getProcessedItemsData_getImageSize($this->processType);
		$this->arrParamsItems = $this->addon->getParamsItems();
		
		//validate
		$this->validateImageSizeExists();

		//debug
		$isShowInputData = UniteFunctionsUC::getVal($this->arrValues, $this->name . "_show_input_data");
		$isShowInputData = UniteFunctionsUC::strToBool($isShowInputData);

		$this->showDebugData = $isShowInputData;
		$this->showDataType = UniteFunctionsUC::getVal($this->arrValues, $this->name . "_input_data_type");

		if(empty($this->showDataType))
			$this->showDataType = "input";

		$isShowMeta = UniteFunctionsUC::getVal($this->arrValues, $this->name . "_show_metafields");
		$isShowMeta = UniteFunctionsUC::strToBool($isShowMeta);

		$this->showDebugMeta = $isShowMeta;

		if($itemsSource == "items"){
			$data[$name] = "uc_items";

			if($this->showDebugData === true)
				self::$showItemsDebug = true;

			return $data;
		}

		$this->checkDebugBeforeData($itemsSource);

		$arrData = $this->getData($itemsSource);
		
		$this->showDebug_input($itemsSource, $arrData);

		//set empty demo output
		if(empty($arrData) === true
			&& $this->isInsideEditor === true
			&& ($itemsSource === self::SOURCE_JSONCSV || $itemsSource === self::SOURCE_REPEATER)){
			$arrData = $this->getDemoDataForEditor();

			$itemsSource = self::SOURCE_DEMO;

			$this->showDebug_input($itemsSource, $arrData);
		}

		$arrItems = $this->getItems($itemsSource, $arrData);
		
		$data[$name] = $arrItems;

		if($this->showDebugData === true)
			$this->showDebug_output($arrItems);

		//add additional data
		if(empty($this->addData) === false)
			$data = array_merge($this->addData, $data);

		return $data;
	}


	/**
	 * show items debug from the output if needed
	 */
	public static function checkShowItemsDebug($arrItemData){

		if(self::$showItemsDebug == false)
			return(false);

		self::$showItemsDebug = false;

		$arrOutput = array();

		if(empty($arrItemData)){
			dmp("no items data found");
			return(false);
		}

		foreach($arrItemData as $item){

			if(count($item) == 1 && isset($item["item"]))
				$arrOutput[] = $item["item"];
		}

		dmp("Getting data from the settings items repeater");

		dmp($arrOutput);

	}


}
