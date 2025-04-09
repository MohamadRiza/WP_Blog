<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

class UniteCreatorSettings extends UniteCreatorSettingsWork{
	
	const SELECTOR_PLACEHOLDER = "{{selector}}";
	
	/**
	 * add settings provider types
	 */
	protected function addSettingsProvider($type, $name,$value,$title,$extra ){

		$isAdded = false;

		return($isAdded);
	}


	/**
	 * show taxanomy
	 */
	private function showTax(){

		$showTax = UniteFunctionsUC::getGetVar("maxshowtax", "", UniteFunctionsUC::SANITIZE_NOTHING);
		$showTax = UniteFunctionsUC::strToBool($showTax);

		if($showTax == true){

			$args = array("taxonomy"=>"");
			$cats = get_categories($args);

			$arr1 = UniteFunctionsWPUC::getTaxonomiesWithCats();

			$arrPostTypes = UniteFunctionsWPUC::getPostTypesAssoc();
			$arrTax = UniteFunctionsWPUC::getTaxonomiesWithCats();
			$arrCustomTypes = get_post_types(array('_builtin' => false));

			$arr = get_taxonomies();

			$taxonomy_objects = get_object_taxonomies( 'post', 'objects' );
   			dmp($taxonomy_objects);

			dmp($arrCustomTypes);
			dmp($arrPostTypes);
			exit();
		}

	}

	/**
	 * add template picker
	 */
	protected function addTemplatePicker($name,$value,$title,$extra){

        $arrTemplates = HelperProviderCoreUC_EL::getArrElementorTemplatesShort();
		$arrTemplates = UniteFunctionsUC::addArrFirstValue($arrTemplates, __("[No Template Selected]","unlimited-elements-for-elementor"),"__none__");

		$arrTemplates = array_flip($arrTemplates);

		$params = array();
		$params["origtype"] = "select2";

		if(empty($title))
			$title = __("Choose Template", "unlimited-elements-for-elementor");

		$this->addSelect($name."_templateid", $arrTemplates, $title ,"__none__", $params);

		//get the edit template button


		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RAW_HTML;
		$params["html"] = "<div class='uc-edit-template-button'><a href='javascript:void(0)' class='uc-edit-template-button__link unite-setting-special-select' data-settingtype='template_button' style='display:none' data-selectid='{$name}_templateid' target='_blank'>Edit Template</a></div>";



		$this->addTextBox($name."_templateid_button", "", $title , $params);

	}


	/**
	 * get categories from all post types
	 */
	protected function getCategoriesFromAllPostTypes($arrPostTypes){

		if(empty($arrPostTypes))
			return(array());

		$arrAllCats = array();
		$arrAllCats[__("All Categories", "unlimited-elements-for-elementor")] = "all";

		foreach($arrPostTypes as $name => $arrType){
		
			if($name == "page")
				continue;

			$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");

			$cats = UniteFunctionsUC::getVal($arrType, "cats");

			if(empty($cats))
				continue;

			foreach($cats as $catID => $catTitle){

				if($name != "post")
					$catTitle = $catTitle." ($postTypeTitle type)";

				$arrAllCats[$catTitle] = $catID;
			}

		}


		return($arrAllCats);
	}



	/**
	 * get taxonomies array for terms picker
	 */
	private function addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax){
		
		$arrAllTax = array();

		//make taxonomies data
		$arrTaxonomies = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){

			$arrItemTax = UniteFunctionsUC::getVal($arrType, "taxonomies");

			$arrTaxOutput = array();

			//some fix that avoid double names
			$arrDuplicateValues = UniteFunctionsUC::getArrayDuplicateValues($arrItemTax);

			if(empty($arrItemTax))
				$arrItemTax = array();

			foreach($arrItemTax as $slug => $taxTitle){

				if(is_string($taxTitle) == false)
					continue;

				$isDuplicate = array_key_exists($taxTitle, $arrDuplicateValues);

				//some modification for woo
				if($taxTitle == "Tag" && $slug != "post_tag")
					$isDuplicate = true;
				
				if(isset($arrAllTax[$taxTitle]))
					$isDuplicate = true;

				if($isDuplicate == true)
					$taxTitle = UniteFunctionsUC::convertHandleToTitle($slug);

				$taxTitle = ucwords($taxTitle);
				
				//avoid duplicate title
				if(isset($arrAllTax[$taxTitle]))
					$taxTitle = "$taxTitle ($slug)";
				
				$arrTaxOutput[$slug] = $taxTitle;
				
				$arrAllTax[$taxTitle] = $slug;
			}

			if(!empty($arrTaxOutput))
				$arrTaxonomies[$typeName] = $arrTaxOutput;
		}
		
		
		$response = array();
		$response["post_type_tax"] = $arrTaxonomies;
		$response["taxonomies_simple"] = $arrAllTax;
		
		
		return($response);
	}


	/**
	 * add users picker
	 */
	protected function addUsersPicker($name,$value,$title,$extra){

		//----- custom or manual

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrType = array();
		$arrType["custom"] = __("Custom Query", "unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection", "unlimited-elements-for-elementor");

		$arrType = array_flip($arrType);

		$this->addSelect($name."_type", $arrType, __("Select Users By", "unlimited-elements-for-elementor"), "custom", $params);
	
		$arrConditionCustom = array();
		$arrConditionCustom[$name."_type"] = "custom";

		$arrConditionManual = array();
		$arrConditionManual[$name."_type"] = "manual";

		//----- roles in -------

		$arrRoles = UniteFunctionsWPUC::getRolesShort();

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["description"] = __("Leave empty for all the roles", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;

		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);

		$role = UniteFunctionsUC::getVal($value, $name."_role");
		if(empty($role))
			$role = UniteFunctionsUC::getArrFirstValue($arrRoles);

		$params["is_multiple"] = true;
		$params["placeholder"] = __("All Roles", "unlimited-elements-for-elementor");
		//$params["description"] = __("Get all the users if leave empty", "unlimited-elements-for-elementor");

		$this->addMultiSelect($name."_role", $arrRoles, __("Select Roles", "unlimited-elements-for-elementor"), $role, $params);


		//-------- exclude roles ----------

		$arrRoles = UniteFunctionsWPUC::getRolesShort();

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionCustom;

		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);

		$roleExclude = UniteFunctionsUC::getVal($value, $name."_role_exclude");

		$params["is_multiple"] = true;

		$this->addMultiSelect($name."_role_exclude", $arrRoles, __("Exclude Roles", "unlimited-elements-for-elementor"), $roleExclude, $params);

		//---- exclude user -----

		$arrAuthors = UniteFunctionsWPUC::getArrAuthorsShort();

		$arrAuthorsFlipped = array_flip($arrAuthors);


		//---------- exclude users new ---------

		$this->addPostIDSelect($name."_exclude_authors", __("Exclude Users", "unlimited-elements-for-elementor"), $arrConditionCustom, "users");


		//---- include users -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more users", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionManual;

		$this->addMultiSelect($name."_include_authors", $arrAuthorsFlipped, __("Select Specific Users", "unlimited-elements-for-elementor"), "", $params);


		//---- hr before max users -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrConditionCustom;

		$this->addHr($name."_hr_before_max", $params);

		//---- max users -----

		$params = array("unit"=>"users");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("all users if empty","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_maxusers", "", esc_html__("Max Number of Users", "unlimited-elements-for-elementor"), $params);

		//---- hr before order by -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_hr_before_orderby", $params);

		//---- orderby -----

		$arrOrderBy = HelperProviderUC::getArrUsersOrderBySelect();
		$arrOrderBy = array_flip($arrOrderBy);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), "default", $params);
		
		//---- Manual Order -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("example: 4,5,6,3","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_orderby"=>"manual");
		$params["description"] = __("You can show query debug to see the ids","unlimited-elements-for-elementor");
		$params["add_dynamic"] = false;
		
		$this->addTextBox($name."_order_manual", "", esc_html__("Manual Order IDs", "unlimited-elements-for-elementor"), $params);
		
		//---- manual order ids -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = array($name."_orderby"=>"manual");
		
		$this->addRadioBoolean($name."_show_order", __("Show Include IDs", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
		//--------- order direction -------------

		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrOrderDir = array_flip($arrOrderDir);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), "default", $params);

		//---- hr before meta -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_hr_before_metakeys", $params);

		//---- meta keys addition -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Get additional meta data by given meta keys comma separated","unlimited-elements-for-elementor");
		$params["placeholder"] = "meta_key1, meta_key2...";
		$params["label_block"] = true;
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_add_meta_keys", "", __("Additional Meta Data Keys", "unlimited-elements-for-elementor"), $params);

		//---- hr before debug -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_hr_before_debug", $params);

		//---- show debug -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

	}


	/**
	 * add menu picker
	 */
	protected function addMenuPicker($name, $value, $title, $extra){

		$useFor = UniteFunctionsUC::getVal($extra, "usefor");

		$showLimitedDepts = false;
		if($useFor == "multisource")
			$showLimitedDepts = true;


		$arrMenus = array();

		//if(GlobalsUC::$is_admin == true)
			$arrMenus = UniteFunctionsWPUC::getMenusListShort();

		$menuID = UniteFunctionsUC::getVal($value, $name."_id");

		if(empty($menuID))
			$menuID = UniteFunctionsUC::getFirstNotEmptyKey($arrMenus);

		$arrMenus = array_flip($arrMenus);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addSelect($name."_id", $arrMenus, __("Select Menu", "unlimited-elements-for-elementor"), $menuID, $params);

		//add depth

		$arrDepth = array();
		$arrDepth["0"] = __("All Depths", "unlimited-elements-for-elementor");
		$arrDepth["1"] = __("1", "unlimited-elements-for-elementor");

		if($showLimitedDepts == false){
			$arrDepth["2"] = __("2", "unlimited-elements-for-elementor");
			$arrDepth["3"] = __("3", "unlimited-elements-for-elementor");
		}

		$arrDepth = array_flip($arrDepth);
		$depth = UniteFunctionsUC::getVal($value, $name."_depth", "0");

		$this->addSelect($name."_depth", $arrDepth, __("Max Depth", "unlimited-elements-for-elementor"), $depth, $params);

	}

	private function __________TERMS_______(){}

	/**
	 * add post terms settings
	 */
	protected function addPostTermsPicker($name, $value, $title, $extra){

		$isForWooCommerce = UniteFunctionsUC::getVal($extra, "for_woocommerce");
		$isForWooCommerce = UniteFunctionsUC::strToBool($isForWooCommerce);

		$filterType = UniteFunctionsUC::getVal($extra, "filter_type");
		
		$arrPostTypesWithTax = UniteFunctionsWPUC::getPostTypesWithTaxomonies(GlobalsProviderUC::$arrFilterPostTypes, false);
		
		if($isForWooCommerce == true && isset($arrPostTypesWithTax["product"]))
			$arrPostTypesWithTax = array("product" => $arrPostTypesWithTax["product"]);

		$taxData = $this->addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax);

		$arrPostTypesTaxonomies = $taxData["post_type_tax"];

		$arrTaxonomiesSimple = $taxData["taxonomies_simple"];
		
		
		//----- add post types ---------

		//prepare post types array

		$arrPostTypes = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){

			$title = UniteFunctionsUC::getVal($arrType, "title");
			
			if(empty($title))
				$title = ucfirst($typeName);

			if(isset($arrPostTypes[$title]))
				$title = ucfirst($typeName);

			if(isset($arrPostTypes[$title]))
				$title = ucfirst($typeName." ".$title);

			$arrPostTypes[$title] = $typeName;
		}
				
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype");
		if(empty($postType))
			$postType = UniteFunctionsUC::getArrFirstValue($arrPostTypes);
		
		$params = array();

		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
		$dataTax = UniteFunctionsUC::encodeContent($arrPostTypesTaxonomies);

		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataTax' data-settingtype='select_post_taxonomy' data-settingprefix='{$name}'";
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		
		
		$this->addSelect($name."_posttype", $arrPostTypes, __("Select Post Type", "unlimited-elements-for-elementor"), $postType, $params);
		
		//---------- add taxonomy ---------

		$params = array();
		$params["datasource"] = "post_taxonomy";
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-taxonomy";
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrTax = UniteFunctionsUC::getVal($arrPostTypesTaxonomies, $postType, array());

		if(!empty($arrTax))
			$arrTax = array_flip($arrTax);

		$taxonomy = UniteFunctionsUC::getVal($value, $name."_taxonomy");
		if(empty($taxonomy))
			$taxonomy = UniteFunctionsUC::getArrFirstValue($arrTax);

		if($isForWooCommerce)
			$taxonomy = "product_cat";

			
		$this->addSelect($name."_taxonomy", $arrTaxonomiesSimple, __("Select Taxonomy", "unlimited-elements-for-elementor"), $taxonomy, $params);
		
		// --------- add include by -------------
		
		$arrIncludeBy = array();
		$arrIncludeBy["spacific_terms"] = __("Specific Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["parents"] = __("Children Of","unlimited-elements-for-elementor");
		$arrIncludeBy["children_of_current"] = __("Children Of Current Term","unlimited-elements-for-elementor");
		$arrIncludeBy["direct_children_of_selected_terms"] = __("Direct Children Of Selected Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["current_post_terms"] = __("Current Post Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["search"] = __("By Search Text","unlimited-elements-for-elementor");
		$arrIncludeBy["childless"] = __("Only Childless","unlimited-elements-for-elementor");
		$arrIncludeBy["no_parent"] = __("Not a Child of Other Term","unlimited-elements-for-elementor");
		$arrIncludeBy["only_direct_children"] = __("Only Direct Children","unlimited-elements-for-elementor");
		$arrIncludeBy["meta"] = __("Term Meta","unlimited-elements-for-elementor");

		$arrIncludeBy = array_flip($arrIncludeBy);

		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addMultiSelect($name."_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), "", $params);


		// --------- include by meta key -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Key","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"meta");

		$this->addTextBox($name."_include_metakey", "", esc_html__("Include by Meta Key", "unlimited-elements-for-elementor"), $params);

		// --------- include by meta compare -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		$params["description"] = __("Get only those terms that has the meta key/value. For IN, NOT IN, BETWEEN, NOT BETWEEN compares, use coma saparated values","unlimited-elements-for-elementor");

		$arrItems = HelperProviderUC::getArrMetaCompareSelect();

		$arrItems = array_flip($arrItems);

		$this->addSelect($name."_include_metacompare", $arrItems, esc_html__("Include by Meta Compare", "unlimited-elements-for-elementor"), "=", $params);


		// --------- include by meta value -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Value","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_include_metavalue", "", esc_html__("Include by Meta Value", "unlimited-elements-for-elementor"), $params);


		// --------- add include by specific term -------------

		$params = array();
		$params["description"] = __("Only those selected terms will be loaded", "unlimited-elements-for-elementor");

		$elementorCondition = array($name."_includeby"=>"spacific_terms");

		$addAttrib = "data-taxonomyname='{$name}_taxonomy'";

		$this->addPostIDSelect($name."_include_specific", __("Select Specific Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);


		// --------- add include by direct children of selected terms -------------

		$params = array();
		$params["placeholder"] = "all--terms";
		$params["description"] = __("Only direct children of those selected terms will be fetched", "unlimited-elements-for-elementor");
		 
		$elementorCondition = array($name."_includeby"=>"direct_children_of_selected_terms");
		
		$addAttrib = "data-taxonomyname='{$name}_taxonomy'";

		$this->addPostIDSelect($name."_include_direct_children_of_selected_terms", __("Select Parent Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);


		// --------- add terms parents -------------

		$params = array();
		$params["placeholder"] = "all--parents";

		$elementorCondition = array($name."_includeby"=>"parents");

		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");

		$addAttrib = "data-taxonomyname='{$name}_taxonomy' data-issingle='true'";

		$this->addPostIDSelect($name."_include_parent", __("Select Parent Term", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);

		// --------- add terms parents - direct switcher -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("If turned off, all the terms tree will be selected", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"parents");

		$this->addRadioBoolean($name."_include_parent_isdirect", __("Is Direct Parent", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);

		// --------- by search phrase -------------

		$params = array("unit"=>"terms");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Search Text","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"search");
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_include_search", "", esc_html__("Include by Search", "unlimited-elements-for-elementor"), $params);


		//---------- add hr ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$this->addHr($name."_after_include_by",$params);

		// --------- add exclude by -------------

		$arrExcludeBy = array();
		$arrExcludeBy["spacific_terms"] = __("Specific Terms","unlimited-elements-for-elementor");
		$arrExcludeBy["current_term"] = __("Current Term (for archive only)","unlimited-elements-for-elementor");
		$arrExcludeBy["current_post_terms"] = __("Current Post Terms","unlimited-elements-for-elementor");
		$arrExcludeBy["hide_empty"] = __("Hide Empty Terms","unlimited-elements-for-elementor");
		$arrExcludeBy["hide_first_level_terms"] = __("Root Terms (without parents)","unlimited-elements-for-elementor");

		$arrExcludeBy = array_flip($arrExcludeBy);

		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addMultiSelect($name."_excludeby", $arrExcludeBy, esc_html__("Exclude By", "unlimited-elements-for-elementor"), "", $params);


		//---------- add exclude ---------

		$elementorCondition = array($name."_excludeby"=>"spacific_terms");

		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");

		$addAttrib = "data-taxonomyname='{$name}_taxonomy' data-isalltax='true'";

		$this->addPostIDSelect($name."_exclude", __("Exclude Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib);

		//----- exclude all the parents tree --------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $elementorCondition;

		$this->addRadioBoolean($name."_exclude_tree", __("Exclude With All Children Tree", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);


		//----- add hr --------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_post_terms_before_additions", $params);

		//--------- add max terms -------------

		$params = array("unit"=>"terms");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 terms if empty","unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_maxterms", "", esc_html__("Max Number of Terms", "unlimited-elements-for-elementor"), $params);

		//------- add hr before order by -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_post_terms_before_orderby", $params);


		// --------- add order by -------------

		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy["include"] = __("Include - (specific terms order)", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value"] = __("Meta Value", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value_num"] = __("Meta Value - Numeric", "unlimited-elements-for-elementor");


		$arrOrderBy = array_flip($arrOrderBy);

		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "default");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);

		//--- meta value param -------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		$arrCondition = array();
		$arrCondition[$name."_orderby"] = array("meta_value","meta_value_num");

		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_orderby_meta_key", "" , __("&nbsp;&nbsp;Custom Field Name","unlimited-elements-for-elementor"), $params);


		//--------- add order direction -------------

		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();

		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);

		$arrOrderDir = array_flip($arrOrderDir);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;



		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);


		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_post_terms_before_queryid", $params);

		//---- show debug -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		//---- query id -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$title = __("Query ID", "unlimited-elements-for-elementor");
		$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. <a href='https://unlimited-elements.com/docs/work-with-query-id-in-terms-selection/'><a target='blank' href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'>See docs here</a></a>.","unlimited-elements-for-elementor");

		$this->addTextBox($name."_queryid", "", $title, $params);


		//--------- debug type terms ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name."_show_query_debug"=>"true");

		$arrType = array();
		$arrType["basic"] = __("Basic", "unlimited-elements-for-elementor");
		$arrType["show_query"] = __("Full", "unlimited-elements-for-elementor");

		$arrType = array_flip($arrType);

		$this->addSelect($name."_query_debug_type", $arrType, __("Debug Options", "unlimited-elements-for-elementor"), "basic", $params);


		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."post_terms_sap", $params);


	}


	/**
	 * add woo commerce categories picker
	 */
	protected function addWooCatsPicker($name, $value, $title, $extra){

		$conditionQuery = array(
			$name."_type" => "query",
		);

		$conditionManual = array(
			$name."_type" => "manual",
		);


		//---------- type choosing ---------

		$arrType = array();
		$arrType["query"] = __("Categories Query","unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection","unlimited-elements-for-elementor");

		$arrType = array_flip($arrType);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$type = UniteFunctionsUC::getVal($value, $name."_type", "query");

		$this->addSelect($name."_type", $arrType, __("Selection Type", "unlimited-elements-for-elementor"), $type, $params);

		//---------- add hr ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr("woocommere_terms_sap_type", $params);


		//---------- add parent ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1", "unlimited-elements-for-elementor");
		$params["description"] = __("Write parent category slug, if no parent leave empty", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionQuery;

		$parent = UniteFunctionsUC::getVal($value, $name."_parent", "");

		$this->addTextBox($name."_parent", $parent, __("Parent Category", "unlimited-elements-for-elementor"), $params);


		//---------- include children ---------

		$includeChildren = UniteFunctionsUC::getVal($value, $name."_children", "not_include");

		$arrChildren = array();
		$arrChildren["not_include"] = __("Don't Include", "unlimited-elements-for-elementor");
		$arrChildren["include"] = __("Include", "unlimited-elements-for-elementor");
		$arrChildren = array_flip($arrChildren);


		//---------- add children ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;

		$this->addSelect($name."_children", $arrChildren, __("Include Children", "unlimited-elements-for-elementor"), $includeChildren, $params);


		//---------- add exclude ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "Example: cat1,cat2";
		$params["description"] = "To exclude, enter comma separated term slugs";
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionQuery;

		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");

		$this->addTextBox($name."_exclude", $exclude, __("Exclude Categories", "unlimited-elements-for-elementor"), $params);

		// --------- add exclude categorized -------------

		$excludeUncat = UniteFunctionsUC::getVal($value, $name."_excludeuncat", "exclude");


		$arrExclude = array();
		$arrExclude["exclude"] = __("Exclude","unlimited-elements-for-elementor");
		$arrExclude["no_exclude"] = __("Don't Exclude","unlimited-elements-for-elementor");
		$arrExclude = array_flip($arrExclude);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;

		$this->addSelect($name."_excludeuncat", $arrExclude, __("Exclude Uncategorized Category", "unlimited-elements-for-elementor"), $excludeUncat, $params);

		// --------- hr -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;

		$this->addHr("woocommere_terms_sap1", $params);

		// --------- add order by -------------

		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy["meta_value"] = __("Meta Value", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value_num"] = __("Meta Value - Numeric", "unlimited-elements-for-elementor");


		$arrOrderBy = array_flip($arrOrderBy);

		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "name");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;

		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);

		//--- meta key param -------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		$arrCondition = $conditionQuery;
		$arrCondition[$name."_orderby"] = array("meta_value","meta_value_num");

		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;

		$this->addTextBox($name."_orderby_meta_key", "" , __("&nbsp;&nbsp;Meta Field Name","unlimited-elements-for-elementor"), $params);


		//--------- add order direction -------------

		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();

		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);

		$arrOrderDir = array_flip($arrOrderDir);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;

		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);


		//--------- add hide empty -------------

		$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty", "no_hide");

		$arrHide = array();
		$arrHide["no_hide"] = "Don't Hide";
		$arrHide["hide"] = "Hide";
		$arrHide = array_flip($arrHide);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;

		$this->addSelect($name."_hideempty", $arrHide, __("Hide Empty", "unlimited-elements-for-elementor"), $hideEmpty, $params);

		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;

		$this->addHr("woocommere_terms_sap", $params);


		//---------- include categories - manual selection ---------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1, cat2", "unlimited-elements-for-elementor");
		$params["description"] = __("Include specific categories by slug", "unlimited-elements-for-elementor");
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionManual;

		$cats = UniteFunctionsUC::getVal($value, $name."_include", "");

		$this->addTextBox($name."_include", $cats, __("Include Specific Categories", "unlimited-elements-for-elementor"), $params);


		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_post_terms_before_queryid", $params);

		//---- show debug -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);


		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."post_terms_sap", $params);


	}

	/**
	 * add background settings
	 */
	protected function addBackgroundSettings($name, $value, $title, $param, $extra){

		$baseParams = array_merge($extra, array(
			"selector" => UniteFunctionsUC::getVal($param, "selector"),
		));

		// type
		$types = array_flip(array(
			"none" => __("None", "unlimited-elements-for-elementor"),
			"solid" => __("Solid", "unlimited-elements-for-elementor"),
			"gradient" => __("Gradient", "unlimited-elements-for-elementor"),
		));

		$typeName = $name . "_type";
		$typeTitle = $title;
		$typeDefault = UniteFunctionsUC::getVal($param, "background_type", "none");

		$this->addSelect($typeName, $types, $typeTitle, $typeDefault, $baseParams);

		// solid type
		$solidParams = array_merge($baseParams, array(
			"elementor_condition" => array($typeName => "solid"),
		));

		$this->addBackgroundSettings_solid($name, $param, $solidParams);

		// gradient type
		$gradientParams = array_merge($baseParams, array(
			"elementor_condition" => array($typeName => "gradient"),
		));

		$this->addBackgroundSettings_gradient($name, $param, $gradientParams);
	}

	/**
	 * add background settings - solid type
	 */
	private function addBackgroundSettings_solid($name, $param, $extra){

		$responsive = array(
			"desktop" => "",
			"tablet" => "_tablet",
			"mobile" => "_mobile",
		);

		$baseParams = $extra;

		// color
		$colorName = $name . "_solid_color";
		$colorTitle = __("Color", "unlimited-elements-for-elementor");
		$colorDefault = UniteFunctionsUC::getVal($param, "solid_color");

		$colorParams = array_merge($baseParams, array(
			"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "color"),
		));

		$this->addColorPicker($colorName, $colorDefault, $colorTitle, $colorParams);

		// image
		$imageName = $name . "_solid_image";
		$imageTitle = __("Image", "unlimited-elements-for-elementor");
		$imageDefault = UniteFunctionsUC::getVal($param, "solid_bg_image");

		$imageParams = array_merge($baseParams, array(
			"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "image"),
			"is_responsive" => true,
		));

		foreach($responsive as $device => $suffix){
			$imageParams["responsive_type"] = $device;

			if($device !== "desktop")
				$imageDefault = " ";

			$this->addImage($imageName . $suffix, $imageDefault, $imageTitle, $imageParams);
		}

		// position
		$positions = array_flip(array(
			"" => __("Default", "unlimited-elements-for-elementor"),
			"center center" => __("Center Center", "unlimited-elements-for-elementor"),
			"center left" => __("Center Left", "unlimited-elements-for-elementor"),
			"center right" => __("Center Right", "unlimited-elements-for-elementor"),
			"top center" => __("Top Center", "unlimited-elements-for-elementor"),
			"top left" => __("Top Left", "unlimited-elements-for-elementor"),
			"top right" => __("Top Right", "unlimited-elements-for-elementor"),
			"bottom center" => __("Bottom Center", "unlimited-elements-for-elementor"),
			"bottom left" => __("Bottom Left", "unlimited-elements-for-elementor"),
			"bottom right" => __("Bottom Right", "unlimited-elements-for-elementor"),
		));

		$positionName = $name . "_solid_image_position";
		$positionTitle = __("Position", "unlimited-elements-for-elementor");
		$positionDefault = UniteFunctionsUC::getVal($param, "solid_bg_image_position");

		$positionParams = array_merge($baseParams, array(
			"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "position"),
			"is_responsive" => true,
		));

		foreach($responsive as $device => $suffix){
			$positionParams["responsive_type"] = $device;

			$this->addSelect($positionName . $suffix, $positions, $positionTitle, $positionDefault, $positionParams);
		}

		// attachment
		$attachments = array_flip(array(
			"" => __("Default", "unlimited-elements-for-elementor"),
			"scroll" => __("Scroll", "unlimited-elements-for-elementor"),
			"fixed" => __("Fixed", "unlimited-elements-for-elementor"),
		));

		$attachmentName = $name . "_solid_image_attachment";
		$attachmentTitle = __("Attachment", "unlimited-elements-for-elementor");
		$attachmentDefault = UniteFunctionsUC::getVal($param, "solid_bg_image_attachment");

		$attachmentParams = array_merge($baseParams, array(
			"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "attachment"),
		));

		$this->addSelect($attachmentName, $attachments, $attachmentTitle, $attachmentDefault, $attachmentParams);

		// repeat
		$repeats = array_flip(array(
			"" => __("Default", "unlimited-elements-for-elementor"),
			"no-repeat" => __("No Repeat", "unlimited-elements-for-elementor"),
			"repeat" => __("Repeat", "unlimited-elements-for-elementor"),
			"repeat-x" => __("Repeat X", "unlimited-elements-for-elementor"),
			"repeat-y" => __("Repeat Y", "unlimited-elements-for-elementor"),
		));

		$repeatName = $name . "_solid_image_repeat";
		$repeatTitle = __("Repeat", "unlimited-elements-for-elementor");
		$repeatDefault = UniteFunctionsUC::getVal($param, "solid_bg_image_repeat");

		$repeatParams = array_merge($baseParams, array(
			"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "repeat"),
			"is_responsive" => true,
		));

		foreach($responsive as $device => $suffix){
			$repeatParams["responsive_type"] = $device;

			$this->addSelect($repeatName . $suffix, $repeats, $repeatTitle, $repeatDefault, $repeatParams);
		}

		// size
		$sizes = array_flip(array(
			"" => __("Default", "unlimited-elements-for-elementor"),
			"auto" => __("Auto", "unlimited-elements-for-elementor"),
			"cover" => __("Cover", "unlimited-elements-for-elementor"),
			"contain" => __("Contain", "unlimited-elements-for-elementor"),
		));

		$sizeName = $name . "_solid_image_size";
		$sizeTitle = __("Display Size", "unlimited-elements-for-elementor");
		$sizeDefault = UniteFunctionsUC::getVal($param, "solid_bg_image_size");

		$sizeParams = array_merge($baseParams, array(
			"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "size"),
			"is_responsive" => true,
		));

		foreach($responsive as $device => $suffix){
			$sizeParams["responsive_type"] = $device;

			$this->addSelect($sizeName . $suffix, $sizes, $sizeTitle, $sizeDefault, $sizeParams);
		}
	}

	/**
	 * add background settings - gradient type
	 */
	private function addBackgroundSettings_gradient($name, $param, $extra){

		$linearGroupSelectorName = $name . "_gradient_linear_group";
		$radialGroupSelectorName = $name . "_gradient_radial_group";

		$baseCondition = UniteFunctionsUC::getVal($extra, "elementor_condition", array());

		$baseParams = array_merge($extra, array(
			"group_selector" => array($linearGroupSelectorName, $radialGroupSelectorName),
		));

		$stopParams = array_merge($baseParams, array(
			"min" => 0,
			"max" => 100,
			"step" => 1,
			"units" => array("%"),
		));

		// color 1
		$colorName1 = $name . "_gradient1_color";
		$colorTitle1 = __("Color One", "unlimited-elements-for-elementor");
		$colorDefault1 = UniteFunctionsUC::getVal($param, "gradient_color1");

		$this->addColorPicker($colorName1, $colorDefault1, $colorTitle1, $baseParams);

		// stop 1
		$stopName1 = $name . "_gradient1_stop";
		$stopTitle1 = __("Location", "unlimited-elements-for-elementor");
		$stopDefault1 = 0;

		$this->addRangeSlider($stopName1, $stopDefault1, $stopTitle1, $stopParams);

		// color 2
		$colorName2 = $name . "_gradient2_color";
		$colorTitle2 = __("Color Two", "unlimited-elements-for-elementor");
		$colorDefault2 = UniteFunctionsUC::getVal($param, "gradient_color2");

		$this->addColorPicker($colorName2, $colorDefault2, $colorTitle2, $baseParams);

		// stop 2
		$stopName2 = $name . "_gradient2_stop";
		$stopTitle2 = __("Location", "unlimited-elements-for-elementor");
		$stopDefault2 = 100;

		$this->addRangeSlider($stopName2, $stopDefault2, $stopTitle2, $stopParams);

		// type
		$types = array_flip(array(
			"linear" => __("Linear", "unlimited-elements-for-elementor"),
			"radial" => __("Radial", "unlimited-elements-for-elementor"),
		));

		$typeName = $name . "_gradient_type";
		$typeTitle = __("Type", "unlimited-elements-for-elementor");
		$typeDefault = "linear";

		$this->addSelect($typeName, $types, $typeTitle, $typeDefault, $baseParams);

		// angle
		$angleName = $name . "_gradient_angle";
		$angleTitle = __("Angle", "unlimited-elements-for-elementor");
		$angleDefault = 180;

		$angleParams = array_merge($baseParams, array(
			"elementor_condition" => array_merge($baseCondition, array($typeName => "linear")),
			"group_selector" => $linearGroupSelectorName,
			"min" => 0,
			"max" => 360,
			"step" => 1,
			"units" => array("deg"),
		));

		$this->addRangeSlider($angleName, $angleDefault, $angleTitle, $angleParams);

		// position
		$positions = array_flip(array(
			"center center" => __("Center Center", "unlimited-elements-for-elementor"),
			"center left" => __("Center Left", "unlimited-elements-for-elementor"),
			"center right" => __("Center Right", "unlimited-elements-for-elementor"),
			"top center" => __("Top Center", "unlimited-elements-for-elementor"),
			"top left" => __("Top Left", "unlimited-elements-for-elementor"),
			"top right" => __("Top Right", "unlimited-elements-for-elementor"),
			"bottom center" => __("Bottom Center", "unlimited-elements-for-elementor"),
			"bottom left" => __("Bottom Left", "unlimited-elements-for-elementor"),
			"bottom right" => __("Bottom Right", "unlimited-elements-for-elementor"),
		));

		$positionName = $name . "_gradient_position";
		$positionTitle = __("Position", "unlimited-elements-for-elementor");
		$positionDefault = "center center";

		$positionParams = array_merge($baseParams, array(
			"elementor_condition" => array_merge($baseCondition, array($typeName => "radial")),
			"group_selector" => $radialGroupSelectorName,
		));

		$this->addSelect($positionName, $positions, $positionTitle, $positionDefault, $positionParams);

		// linear group selector
		$linearGroupSelector = UniteFunctionsUC::getVal($param, "selector");
		$linearGroupSelectorValue = HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "linear-gradient");

		$linearGroupSelectorReplace = array(
			"{{ANGLE}}" => $angleName,
			"{{COLOR1}}" => $colorName1,
			"{{STOP1}}" => $stopName1,
			"{{COLOR2}}" => $colorName2,
			"{{STOP2}}" => $stopName2,
		);

		$linearGroupSelectorParams = array(
			"elementor_condition" => array($typeName => "linear"),
		);

		$this->addGroupSelector($linearGroupSelectorName, $linearGroupSelector, $linearGroupSelectorValue, $linearGroupSelectorReplace, $linearGroupSelectorParams);

		// radial group selector
		$radialGroupSelector = UniteFunctionsUC::getVal($param, "selector");
		$radialGroupSelectorValue = HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BACKGROUND, "radial-gradient");

		$radialGroupSelectorReplace = array(
			"{{POSITION}}" => $positionName,
			"{{COLOR1}}" => $colorName1,
			"{{STOP1}}" => $stopName1,
			"{{COLOR2}}" => $colorName2,
			"{{STOP2}}" => $stopName2,
		);

		$radialGroupSelectorParams = array(
			"elementor_condition" => array($typeName => "radial"),
		);

		$this->addGroupSelector($radialGroupSelectorName, $radialGroupSelector, $radialGroupSelectorValue, $radialGroupSelectorReplace, $radialGroupSelectorParams);
	}


	private function __________POSTS_______(){}

	
	/**
	 * add post ID select
	 * this function structure affects Elementor editor. on any change, need to validate that 
	 * elementor post id select (manual post query selection) still works.
	 */
	public function addPostIDSelect($settingName, $text = null, $elementorCondition = null, $isForWoo = false, $addAttribOpt = "", $params = array()){

		if(empty($text))
			$text = __("Search and Select Posts", "unlimited-elements-for-elementor");

		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-special-select";

		$placeholder = __("All Posts", "unlimited-elements-for-elementor");

		if($isForWoo === true)
			$placeholder = __("All Products", "unlimited-elements-for-elementor");

		$placeholder = str_replace(" ", "--", $placeholder);

		$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
		$loaderText = UniteFunctionsUC::encodeContent($loaderText);

		$addAttrib = "";
		if($isForWoo === true)
			$addAttrib = " data-woo='yes'";

		if($isForWoo === "elementor_template"){
			$addAttrib = " data-datatype='elementor_template' data-issingle='true'";
			$placeholder = "All";
		}

		if($isForWoo === "terms"){
			$addAttrib = " data-datatype='terms'";
			$placeholder = "All--Terms";
		}

		if($isForWoo === "users"){
			$addAttrib = " data-datatype='users'";
			$placeholder = "All--Users";
		}


		if(isset($params["placeholder"])){
			$placeholder = $params["placeholder"];
		}

		if($isForWoo === "single"){

			$addAttrib = " data-issingle='true'";
		}

		if(!empty($addAttribOpt))
			$addAttrib .= " ".$addAttribOpt;

		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-settingtype='post_ids' data-placeholdertext='{$placeholder}' data-loadertext='$loaderText' $addAttrib";

		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		$params["label_block"] = true;

		if(!empty($elementorCondition))
			$params["elementor_condition"] = $elementorCondition;

		$this->addSelect($settingName, array(), $text , "", $params);

	}
	
	/**
	 * add post list picker
	 */
	protected function addPostsListPicker($name, $value, $title, $extra){
		
		$isAdmin = GlobalsUC::$is_admin;
		
		
		$simpleMode = UniteFunctionsUC::getVal($extra, "simple_mode");
		$simpleMode = UniteFunctionsUC::strToBool($simpleMode);

		$allCatsMode = UniteFunctionsUC::getVal($extra, "all_cats_mode", true);
		$allCatsMode = UniteFunctionsUC::strToBool($allCatsMode);

		$isForWooProducts = UniteFunctionsUC::getVal($extra, "for_woocommerce_products");
		$isForWooProducts = UniteFunctionsUC::strToBool($isForWooProducts);

		$addCurrentPosts = UniteFunctionsUC::getVal($extra, "add_current_posts", true);
		$addCurrentPosts = UniteFunctionsUC::strToBool($addCurrentPosts);

		$defaultMaxPosts = UniteFunctionsUC::getVal($extra, "default_max_posts");
		$defaultMaxPosts = intval($defaultMaxPosts);

		$arrPostTypes = array();

		if($isAdmin == false){
			$simpleMode = true;
			$arrPostTypes = array("post"=>array("cats"=>array()));
		}
		else
			$arrPostTypes = UniteFunctionsWPUC::getPostTypesWithCats(GlobalsProviderUC::$arrFilterPostTypes);
		
		$textPosts = __("Posts", "unlimited-elements-for-elementor");
		$textPost = __("Post", "unlimited-elements-for-elementor");

		if($isForWooProducts === true){
			$textPosts = __("Products", "unlimited-elements-for-elementor");
			$textPost = __("Product", "unlimited-elements-for-elementor");
		}

		//fill simple types
		$arrTypesSimple = array();

		if($simpleMode === true)
			$arrTypesSimple = array("Post" => "post", "Page" => "page");
		else{
			foreach($arrPostTypes as $arrType){
				$postTypeName = UniteFunctionsUC::getVal($arrType, "name");
				$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");

				if(isset($arrTypesSimple[$postTypeTitle]))
					$arrTypesSimple[$postTypeName] = $postTypeName;
				else
					$arrTypesSimple[$postTypeTitle] = $postTypeName;
			}
		}

		$arrTypesSimple["Any"] = "any";

		//----- posts source ----
		//UniteFunctionsUC::showTrace();

		$arrNotCurrentElementorCondition = array();
		$arrCustomOnlyCondition = array();
		$arrRelatedOnlyCondition = array();
		$arrCurrentElementorCondition = array();
		$arrCustomAndCurrentElementorCondition = array();
		$arrNotManualElementorCondition = array();
		$arrCustomAndRelatedElementorCondition = array();
		$arrManualElementorCondition = array();

		if($addCurrentPosts === true){
			$arrCurrentElementorCondition = array($name . "_source" => "current");
			$arrNotCurrentElementorCondition = array($name . "_source!" => "current");
			$arrCustomAndCurrentElementorCondition = array($name . "_source" => array("current", "custom"));
			$arrCustomAndRelatedElementorCondition = array($name . "_source" => array("related", "custom"));
			$arrCustomOnlyCondition = array($name . "_source" => "custom");
			$arrRelatedOnlyCondition = array($name . "_source" => "related");
			$arrNotInRelatedCondition = array($name . "_source!" => "related");
			$arrNotManualElementorCondition = array($name . "_source!" => "manual");
			$arrManualElementorCondition = array($name . "_source" => "manual");
			
			
			$arrSourceOptions = array_flip(array(
				// translators: %s is a string
				"custom" => sprintf(__("Custom %s", "unlimited-elements-for-elementor"), $textPosts),
				// translators: %s is a string
				"current" => sprintf(__("Current Query %s", "unlimited-elements-for-elementor"), $textPosts),
				// translators: %s is a string
				"related" => sprintf(__("Related %s", "unlimited-elements-for-elementor"), $textPosts),
				"manual" => __("Manual Selection", "unlimited-elements-for-elementor"),
			));

			$source = UniteFunctionsUC::getVal($value, $name . "_source", "custom");

			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			//$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");

			// translators: %s is a string
			$this->addSelect($name . "_source", $arrSourceOptions, sprintf(esc_html__("%s Source", "unlimited-elements-for-elementor"), $textPosts), $source, $params);

			//-------- add static text - current --------
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			$params["elementor_condition"] = $arrCurrentElementorCondition;

			$maxPostsPerPage = get_option("posts_per_page");

			if($isForWooProducts === true)
				$maxPostsPerPage = UniteCreatorWooIntegrate::getDefaultCatalogNumPosts();

			$this->addStaticText("The current $textPosts are being used in archive pages. Posts per page: {$maxPostsPerPage}. Set this option in Settings -> Reading ", $name . "_currenttext", $params);

			//-------- add static text - related --------
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["elementor_condition"] = $arrRelatedOnlyCondition;

			$addition1 = "";

			if($isForWooProducts)
				$addition1 .= " or checkout page";

			$staticText = "The " . strtolower("related {$textPosts} are being used in single {$textPost} $addition1.  Posts from same post type and terms");

			$this->addStaticText($staticText, $name . "_relatedtext", $params);
		}

		//-------- add related posts options --------
		
		$arrRelatedModes = array_flip(array(
			"or" => "OR (default)",
			"and" => "AND",
			"grouping" => "GROUPING",
		));

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrRelatedOnlyCondition;
		$params["description"] = __("In grouping mode, between taxonomies will be 'and' relation and inside same taxonomy will be 'or' relation ", "unlimited-elements-for-elementor");

		$this->addSelect($name . "_related_mode", $arrRelatedModes, __("Related Posts Mode", "unlimited-elements-for-elementor"), "or", $params);
		
		//----- post type -----
		$defaultPostType = "post";

		if($isForWooProducts === true)
			$defaultPostType = "product";

		$postType = UniteFunctionsUC::getVal($value, $name . "_posttype", $defaultPostType);

		$params = array();

		if($simpleMode === false){
			$dataCats = UniteFunctionsUC::encodeContent($arrPostTypes);

			$params["datasource"] = "post_type";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
			$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataCats' data-settingtype='select_post_type' data-settingprefix='{$name}'";
		}

		$params["origtype"] = "uc_select_special";
		//$params["description"] = esc_html__("Select which Post Type or Custom Post Type you wish to display", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		$params["is_multiple"] = true;

		if($isForWooProducts === false)
			$this->addMultiSelect($name . "_posttype", $arrTypesSimple, esc_html__("Post Types", "unlimited-elements-for-elementor"), $postType, $params);

		//----- hr -------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$this->addHr($name . "_post_before_include", $params);

		// --------- Include BY some options -------------
		$arrIncludeBy = array();

		$isStickyPluginExists = UniteCreatorPluginIntegrations::isStickySwitchPluginEnabled();

		if($isForWooProducts === false || $isStickyPluginExists === true){
			$arrIncludeBy["sticky_posts"] = __("Include Sticky Posts", "unlimited-elements-for-elementor");
			$arrIncludeBy["sticky_posts_only"] = __("Get Only Sticky Posts", "unlimited-elements-for-elementor");
		}

		$arrIncludeBy["author"] = __("Author", "unlimited-elements-for-elementor");
		$arrIncludeBy["date"] = __("Date", "unlimited-elements-for-elementor");

		if($isForWooProducts === false)
			$arrIncludeBy["parent"] = __("Post Parent", "unlimited-elements-for-elementor");

		$arrIncludeBy["meta"] = __("Post Meta", "unlimited-elements-for-elementor");
		$arrIncludeBy["current_terms"] = __("Current Page Terms", "unlimited-elements-for-elementor");
		$arrIncludeBy["most_viewed"] = __("Most Viewed", "unlimited-elements-for-elementor");
		$arrIncludeBy["php_function"] = __("IDs from PHP function", "unlimited-elements-for-elementor");
		$arrIncludeBy["ids_from_meta"] = __("IDs from Post Meta", "unlimited-elements-for-elementor");
		$arrIncludeBy["ids_from_dynamic"] = __("Post IDs from Dynamic Field", "unlimited-elements-for-elementor");
		$arrIncludeBy["terms_from_dynamic"] = __("Terms from Dynamic Field", "unlimited-elements-for-elementor");
		$arrIncludeBy["terms_from_current_meta"] = __("Terms from Current Post Meta", "unlimited-elements-for-elementor");
		$arrIncludeBy["terms_from_user_meta"] = __("Terms from Current User Meta", "unlimited-elements-for-elementor");
		$arrIncludeBy["terms_free_selection"] = __("Terms Free Selection", "unlimited-elements-for-elementor");
		$arrIncludeBy["current_query_base"] = __("Current Query as a Base", "unlimited-elements-for-elementor");
		
		if($isForWooProducts === true){
			$arrIncludeBy["products_on_sale"] = __("Products On Sale Only (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["up_sells"] = __("Up Sells Products (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["cross_sells"] = __("Cross Sells Products (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["out_of_stock"] = __("Out Of Stock Products Only (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["recent"] = __("Recently Viewed Produts (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["products_from_post"] = __("Products From Post Content (woo)", "unlimited-elements-for-elementor");
		}
		
		$arrIncludeBy = apply_filters("ue_modify_post_select_includeby", $arrIncludeBy);
		
		
		$arrIncludeBy = array_flip($arrIncludeBy);
		$includeBy = UniteFunctionsUC::getVal($value, $name . "_includeby");
		$arrConditionIncludeBy = $arrCustomOnlyCondition;

		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionIncludeBy;

		$this->addMultiSelect($name . "_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), $includeBy, $params);

		
		//---- Include By Author -----
		
		if($isAdmin == false)
			$arrAuthors = array();
		else{
			$arrAuthors = UniteFunctionsWPUC::getArrAuthorsShort(true);
			$arrAuthors = array_flip($arrAuthors);
		}
		
		$arrConditionIncludeAuthor = $arrConditionIncludeBy;
		$arrConditionIncludeAuthor[$name . "_includeby"] = "author";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["placeholder"] = __("Select one or more authors", "unlimited-elements-for-elementor");
		$params["is_multiple"] = true;

		$arrConditionIncludeAuthor = $arrConditionIncludeBy;
		$arrConditionIncludeAuthor[$name."_includeby"] = "author";

		$params["elementor_condition"] = $arrConditionIncludeAuthor;

		$this->addMultiSelect($name . "_includeby_authors", $arrAuthors, __("Include By Authors From List", "unlimited-elements-for-elementor"), "", $params);

		//---- authors from dynamic field -----
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: 3,5,7", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["placeholder"] = __("Example: 3,5,7", "unlimited-elements-for-elementor");

		$params["elementor_condition"] = $arrConditionIncludeAuthor;

		$this->addTextBox($name . "_includeby_authors_dynamic", "", __("Or Include by Authors from Dynamic Field", "unlimited-elements-for-elementor"), $params);

		//---- Include By Date -----
		$arrDates = HelperProviderUC::getArrPostsDateSelect();
		$arrDates = array_flip($arrDates);

		$arrConditionIncludeByDate = $arrConditionIncludeBy;
		$arrConditionIncludeByDate[$name . "_includeby"] = "date";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionIncludeByDate;

		$this->addSelect($name . "_includeby_date", $arrDates, __("Include By Date", "unlimited-elements-for-elementor"), "all", $params);

		//----- add date before and after -------
		$arrConditionDateCustom = $arrConditionIncludeByDate;
		$arrConditionDateCustom[$name . "_includeby_date"] = "custom";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Choose Date", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionDateCustom;

		//after date (first)
		$params["description"] = __("Show all the posts published since the chosen date, inclusive. Format: year-month-day like \"2023-05-20\" or textual like \"sunday next week\"", "unlimited-elements-for-elementor");

		$this->addTextBox($name . "_include_date_after", "", __("Published After Date", "unlimited-elements-for-elementor"), $params);

		//before date (second)
		$params["description"] = __("Show all the posts published until the chosen date, inclusive. Format: year-month-day like \"2023-04-15\" or textual like \"monday next week\" ", "unlimited-elements-for-elementor");

		$this->addTextBox($name . "_include_date_before", "", __("Published Before Date", "unlimited-elements-for-elementor"), $params);

		//----- date meta field -------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Optional, Select custom field (like ACF) with date format 20210310 (Ymd). For example: event_date", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeByDate;

		$this->addTextBox($name . "_include_date_meta", "", __("Date by Meta Field", "unlimited-elements-for-elementor"), $params);

		//----- date meta format -------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Here you can set the date format for the meta field", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeByDate;

		$this->addTextBox($name . "_include_date_meta_format", "Ymd", __("Date by Meta Field - Format", "unlimited-elements-for-elementor"), $params);

		//----- add hr after date -------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrConditionIncludeByDate;

		$this->addHr($name . "_hr_after_date", $params);

		//---- Include By Post Parent -----
		$arrConditionIncludeParents = $arrConditionIncludeBy;
		$arrConditionIncludeParents[$name . "_includeby"] = "parent";

		// translators: %s is a string
		$this->addPostIDSelect($name . "_includeby_parent", sprintf(__("Select %s Parents", "unlimited-elements-for-elementor"), $textPosts), $arrConditionIncludeParents, $isForWooProducts);

		//-------- include by post parent - add the parent page--------
		$arrItems = array_flip(array(
			"no" => __("No", "unlimited-elements-for-elementor"),
			"start" => __("To Beginning", "unlimited-elements-for-elementor"),
			"end" => __("To End", "unlimited-elements-for-elementor"),
		));

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionIncludeParents;

		$this->addSelect($name . "_includeby_parent_addparent", $arrItems, esc_html__("Add The Parent As Well", "unlimited-elements-for-elementor"), "no", $params);

		//-------- include by recently viewed --------
		$arrConditionIncludeRecent = $arrConditionIncludeBy;
		$arrConditionIncludeRecent[$name . "_includeby"] = "recent";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = $arrConditionIncludeRecent;

		$this->addStaticText("Recently viewed by the current site visitor, taken from cookie: woocommerce_recently_viewed. Works only if active wordpress widget: \"Recently Viewed Products\" ", $name . "_includeby_recenttext", $params);

		//-------- include by Post Meta --------

		// --------- include by meta key -------------
		$arrConditionIncludeMeta = $arrConditionIncludeBy;
		$arrConditionIncludeMeta[$name . "_includeby"] = "meta";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Key", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeMeta;

		$this->addTextBox($name . "_includeby_metakey", "", esc_html__("Include by Meta Key", "unlimited-elements-for-elementor"), $params);

		// --------- include by meta compare -------------
		$arrItems = HelperProviderUC::getArrMetaCompareSelect();
		$arrItems = array_flip($arrItems);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["description"] = __("Get only those terms that has the meta key/value. For IN, NOT IN, BETWEEN, NOT BETWEEN compares, use coma separated values", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeMeta;

		$this->addSelect($name . "_includeby_metacompare", $arrItems, esc_html__("Include by Meta Compare", "unlimited-elements-for-elementor"), "=", $params);

		// --------- include by meta value -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = "";
		$params["placeholder"] = __("Meta Value", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["elementor_condition"] = $arrConditionIncludeMeta;

		$this->addTextBox($name . "_includeby_metavalue", "", esc_html__("Include by Meta Value", "unlimited-elements-for-elementor"), $params);
		$this->addTextBox($name . "_includeby_metavalue2", "", esc_html__("Include by Meta Value 2", "unlimited-elements-for-elementor"), $params);

		$params["description"] = "Special keywords you can use: {current_user_id}, or like this:  value1||value2||value3";

		$this->addTextBox($name . "_includeby_metavalue3", "", esc_html__("Include by Meta Value 3", "unlimited-elements-for-elementor"), $params);

		// --------- show another meta key -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $arrConditionIncludeMeta;

		$this->addRadioBoolean($name . "_includeby_meta_addsecond", __("Add Second Meta Key", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		// --------- include by SECOND meta key -------------
		$arrConditionMetaSecond = $arrConditionIncludeMeta;
		$arrConditionMetaSecond[$name . "_includeby_meta_addsecond"] = "true";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Second Meta Key", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionMetaSecond;

		$this->addTextBox($name . "_includeby_second_metakey", "", esc_html__("Include by Second Meta Key", "unlimited-elements-for-elementor"), $params);

		// --------- include by SECOND meta compare -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionMetaSecond;

		$this->addSelect($name . "_includeby_second_metacompare", $arrItems, esc_html__("Include by Second Meta Compare", "unlimited-elements-for-elementor"), "=", $params);

		// --------- include by SECOND meta value -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = "";
		$params["placeholder"] = __("Second Meta Value", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["elementor_condition"] = $arrConditionMetaSecond;

		$this->addTextBox($name . "_includeby_second_metavalue", "", esc_html__("Include by Second Meta Value", "unlimited-elements-for-elementor"), $params);

		// --------- Meta Fields Relation -------------
		$arrRelations = array(
			"AND" => "AND",
			"OR" => "OR",
		);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionMetaSecond;

		$this->addSelect($name . "_includeby_meta_relation", $arrRelations, esc_html__("Meta Fields Relation", "unlimited-elements-for-elementor"), "and", $params);

		// --------- debug post meta -------------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;

		$params["elementor_condition"] = $arrConditionIncludeMeta;

		$this->addRadioBoolean($name . "_includeby_meta_debug", __("Show Post Meta Fields for Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		// --------- include by PHP Function -------------
		$arrConditionIncludeFunction = $arrConditionIncludeBy;
		$arrConditionIncludeFunction[$name . "_includeby"] = "php_function";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("getMyIDs", "unlimited-elements-for-elementor");
		$params["description"] = __("Get post id's array from php function. \n For example: function getMyIDs(\$arg){return(array(\"32\",\"58\")). This function MUST begin with 'get'. }","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeFunction;

		$this->addTextBox($name . "_includeby_function_name", "", esc_html__("PHP Function Name", "unlimited-elements-for-elementor"), $params);

		// --------- include by PHP Function Add Parameter-------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("yourtext", "unlimited-elements-for-elementor");
		$params["description"] = __("Optional. Some argument to be passed to this function. For some \"IF\" statement.", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeFunction;

		$this->addTextBox($name . "_includeby_function_addparam", "", esc_html__("PHP Function Argument", "unlimited-elements-for-elementor"), $params);

		// --------- include by id's from meta -------------
		$textIDsFromMeta = __("Select Post (leave empty for current post)", "unlimited-elements-for-elementor");
		$arrConditionIncludePostMeta = $arrConditionIncludeBy;
		$arrConditionIncludePostMeta[$name . "_includeby"] = "ids_from_meta";

		$this->addPostIDSelect($name . "_includeby_postmeta_postid", $textIDsFromMeta, $arrConditionIncludePostMeta, false, "data-issingle='true'");

		// --------- include by id's from meta field name -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Choose meta field name that has the post id's on it. Good for acf relationship for example", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludePostMeta;

		$this->addTextBox($name . "_includeby_postmeta_metafield", "", esc_html__("Meta Field Name", "unlimited-elements-for-elementor"), $params);

		//----- include id's from dynamic field -------
		$arrConditionIncludeDynamic = $arrConditionIncludeBy;
		$arrConditionIncludeDynamic[$name . "_includeby"] = "ids_from_dynamic";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter post id's like 45,65,76, or select from dynamic tag", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["elementor_condition"] = $arrConditionIncludeDynamic;

		$this->addTextBox($name . "_includeby_dynamic_field", "", __("Include Posts by Dynamic Field", "unlimited-elements-for-elementor"), $params);

		//----- include terms from dynamic field by ids -------
		$arrConditionIncludeDynamic = $arrConditionIncludeBy;
		$arrConditionIncludeDynamic[$name . "_includeby"] = "terms_from_dynamic";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter term id's like 12,434,1289, or select from dynamic tag. You can use the term relation and include children options from below", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["elementor_condition"] = $arrConditionIncludeDynamic;

		$this->addTextBox($name . "_includeby_terms_dynamic_field", "", __("Include by Terms from Dynamic Field", "unlimited-elements-for-elementor"), $params);

		//----- include terms from current post meta field -------
		
		$arrConditionIncludeDynamic = $arrConditionIncludeBy;
		$arrConditionIncludeDynamic[$name . "_includeby"] = "terms_from_current_meta";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter current post meta field, that has the terms selection of the posts you want to bring. Use it to connect parent with children posts with terms.", "unlimited-elements-for-elementor");
		$params["placeholder"] = "Example: terms_select";
		$params["add_dynamic"] = false;
		$params["label_block"] = true;
		$params["elementor_condition"] = $arrConditionIncludeDynamic;

		$this->addTextBox($name . "_includeby_terms_from_meta", "", __("Current Post Terms Select Meta Field", "unlimited-elements-for-elementor"), $params);
		
		//----- include terms from current user meta field -------
		
		$arrConditionIncludeMeta = $arrConditionIncludeBy;
		$arrConditionIncludeMeta[$name . "_includeby"] = "terms_from_user_meta";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter current user meta field, that has the terms selection of the posts you want to bring. To show all fields write: <b>show</b>", "unlimited-elements-for-elementor");
		$params["placeholder"] = "Example: terms_select";
		$params["add_dynamic"] = false;
		$params["label_block"] = true;
		$params["elementor_condition"] = $arrConditionIncludeMeta;

		$this->addTextBox($name . "_includeby_terms_from_user_meta", "", __("Select User Meta Field", "unlimited-elements-for-elementor"), $params);
		
		
		// --------- terms free selection -------------
		
		$params = array();
		$params["description"] = __("Another way to select terms, not limited by number of terms","unlimited-elements-for-elementor");
		
		$arrConditionTermsFree = $arrConditionIncludeBy;
		$arrConditionTermsFree[$name . "_includeby"] = "terms_free_selection";
		
		$addAttrib = "data-posttypename='{$name}_posttype'";
		
		$this->addPostIDSelect($name."_include_terms_freeselect", __("Terms Free Selection", "unlimited-elements-for-elementor"), $arrConditionTermsFree, "terms", $addAttrib, $params);
		
		
		// --------- current query base -------------
		
		$arrConditionCurrentQueryBase = $arrConditionIncludeBy;
		$arrConditionCurrentQueryBase[$name . "_includeby"] = "current_query_base";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = $arrConditionCurrentQueryBase;

		$text = __("Get current query as a query base. Good for archive page customization. For simple uses use the 'Current Query' product source instead. ", "unlimited-elements-for-elementor");

		$this->addStaticText($text, $name . "_current_query_text", $params);

		// --------- include by most viewed -------------
		$isWPPExists = UniteCreatorPluginIntegrations::isWPPopularPostsExists();

		$arrConditionIncludeViewsCounter = $arrConditionIncludeBy;
		$arrConditionIncludeViewsCounter[$name . "_includeby"] = "most_viewed";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = $arrConditionIncludeViewsCounter;

		$text = __("Select most viewed posts, integration with plugin: 'WordPress Popular Posts' that should be installed", "unlimited-elements-for-elementor");

		if($isWPPExists === true)
			$text = __("'WordPress Popular Posts' plugin activated.", "unlimited-elements-for-elementor");

		$this->addStaticText($text, $name . "_text_includemostviewed", $params);

		// --------- most viewed range -------------
		if($isWPPExists === true){
			$arrItems = array_flip(array(
				"last30days" => "Last 30 Days",
				"last7days" => "Last 7 Days",
				"last24hours" => "Last 24 Hours",
				"daily" => "Daily",
				"weekly" => "Weekly",
				"monthly" => "Monthly",
				"all" => "All",
			));

			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			$params["elementor_condition"] = $arrConditionIncludeViewsCounter;
			$params["description"] = "Besides range, it supports single post type and single category, and order direction query options";

			$this->addSelect($name . "_includeby_mostviewed_range", $arrItems, esc_html__("Most Viewed Time Range", "unlimited-elements-for-elementor"), "last30days", $params);
		}

		// --------- add hr before categories -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$this->addHr($name . "_before_categories", $params);

		//----- add categories -------
		$arrCats = array();
		
		if($simpleMode === true){
			$arrCats = $arrPostTypes["post"]["cats"];
			$arrCats = array_flip($arrCats);
			$firstItemValue = reset($arrCats);
		}elseif($allCatsMode === true){
			//filter only product terms
			if($isForWooProducts === true)
				$arrPostTypes = array("product" => UniteFunctionsUC::getVal($arrPostTypes, "product"));

			$arrCats = $this->getCategoriesFromAllPostTypes($arrPostTypes);
			$firstItemValue = reset($arrCats);
		}else{
			$firstItemValue = "";
		}

		$category = UniteFunctionsUC::getVal($value, $name . "_category", $firstItemValue);

		$params = array();

		if($simpleMode === false){
			$params["datasource"] = "post_category";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-category";
		}

		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$paramsTermSelect = $params;

		$this->addMultiSelect($name . "_category", $arrCats, esc_html__("Include By Terms", "unlimited-elements-for-elementor"), $category, $params);

		// --------- Include by term relation -------------
		$arrRelationItems = array(
			"And" => "AND",
			"Or" => "OR",
		);

		$relation = UniteFunctionsUC::getVal($value, $name . "_category_relation", "AND");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$this->addSelect($name . "_category_relation", $arrRelationItems, __("Include By Terms Relation", "unlimited-elements-for-elementor"), $relation, $params);

		//--------- show children -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$isIncludeChildren = UniteFunctionsUC::getVal($value, $name . "_terms_include_children", false);
		$isIncludeChildren = UniteFunctionsUC::strToBool($isIncludeChildren);

		$this->addRadioBoolean($name . "_terms_include_children", __("Include Terms Children", "unlimited-elements-for-elementor"), $isIncludeChildren, "Yes", "No", $params);

		//---- manual selection search and replace -----
		// translators: %s is a string
		$textManualSelect = sprintf(__("Seach And Select %s", "unlimited-elements-for-elementor"), $textPosts);

		$this->addPostIDSelect($name . "_manual_select_post_ids", $textManualSelect, $arrManualElementorCondition, $isForWooProducts);

		// --------- add dynamic post ids -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = "Optional. Select some dynamic field, that has output of post ids (string or array) like 15,40,23";
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["elementor_condition"] = $arrManualElementorCondition;

		$this->addTextBox($name . "_manual_post_ids_dynamic", "", __("Or Select Post IDs 	", "unlimited-elements-for-elementor"), $params);

		// --------- add hr before avoid duplicates -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrManualElementorCondition;

		$this->addHr($name . "_before_avoid_duplicates_manual", $params);

		//----- avoid duplicates -------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("If turned on, those posts in another widgets won't be shown", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrManualElementorCondition;

		$this->addRadioBoolean($name . "_manual_avoid_duplicates", __("Avoid Duplicates", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		// --------- add hr before exclude -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$this->addHr($name . "_before_exclude_by", $params);

		// --------- add include by certain terms (for related posts) -------------
		$arrTaxonomies = UniteFunctionsWPUC::getAllTaxonomiesAssoc();
		$arrTaxonomies = array_flip($arrTaxonomies);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["description"] = __("When selected, posts with listed taxonomies only will be included", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrRelatedOnlyCondition;

		$this->addMultiSelect($name . "_related_taxonomies", $arrTaxonomies, __("Include By Taxonomies", "unlimited-elements-for-elementor"), "", $params);

		// --------- add exclude by -------------
		$arrExclude = array();

		if($isForWooProducts === true){
			$arrExclude["out_of_stock"] = __("Out Of Stock Products (woo)", "unlimited-elements-for-elementor");
			$arrExclude["out_of_stock_variation"] = __("Out Of Stock Variation Products (woo)", "unlimited-elements-for-elementor");
			$arrExclude["products_on_sale"] = __("Products On Sale (woo)", "unlimited-elements-for-elementor");
		}

		$arrExclude["terms"] = __("Terms", "unlimited-elements-for-elementor");
		// translators: %s is a string
		$arrExclude["current_post"] = sprintf(__("Current %s", "unlimited-elements-for-elementor"), $textPost);
		// translators: %s is a string
		$arrExclude["specific_posts"] = sprintf(__("Specific %s", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["author"] = __("Author", "unlimited-elements-for-elementor");
		// translators: %s is a string
		$arrExclude["no_image"] = sprintf(__("%s Without Featured Image", "unlimited-elements-for-elementor"), $textPost);
		// translators: %s is a string
		$arrExclude["current_category"] = sprintf(__("%s with Current Category", "unlimited-elements-for-elementor"), $textPosts);
		// translators: %s is a string
		$arrExclude["current_tags"] = sprintf(__("%s With Current Tags", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["offset"] = sprintf(__("Offset", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["avoid_duplicates"] = sprintf(__("Avoid Duplicates", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["ids_from_dynamic"] = sprintf(__("Post IDs from Dynamic Field", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude = array_flip($arrExclude);

		$conditionExcludeBy = $arrCustomAndRelatedElementorCondition;
		$arrExcludeValues = "";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["elementor_condition"] = $conditionExcludeBy;

		$this->addMultiSelect($name . "_excludeby", $arrExclude, __("Exclude By", "unlimited-elements-for-elementor"), $arrExcludeValues, $params);

		//----- exclude id's from dynamic field -------
		$conditionExcludeByDynamic = $conditionExcludeBy;
		$conditionExcludeByDynamic[$name . "_excludeby"] = "ids_from_dynamic";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter post id's like 45,65,76, or select from dynamic tag", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionExcludeByDynamic;

		$this->addTextBox($name . "_exclude_dynamic_field", "", __("Exclude Posts by Dynamic Field", "unlimited-elements-for-elementor"), $params);

		//------- Already Fetched --------
		$conditionExcludeByFetched = $conditionExcludeBy;
		$conditionExcludeByFetched[$name . "_excludeby"] = "avoid_duplicates";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = $conditionExcludeByFetched;

		$this->addStaticText(__("Avoid duplicate posts, that fetched by another post widgets in the page, and have this option selected (avoid duplicates)", "unlimited-elements-for-elementor"), $name . "_alreadyfethcedtext", $params);

		//------- Exclude By --- TERM --------
		$conditionExcludeByTerms = $conditionExcludeBy;
		$conditionExcludeByTerms[$name . "_excludeby"] = "terms";

		$params = $paramsTermSelect;
		$params["elementor_condition"] = $conditionExcludeByTerms;

		$this->addMultiSelect($name . "_exclude_terms", $arrCats, esc_html__("Exclude By Terms", "unlimited-elements-for-elementor"), "", $params);

		//------- Exclude By --- AUTHOR --------
		$arrConditionIncludeAuthor = $conditionExcludeBy;
		$arrConditionIncludeAuthor[$name . "_excludeby"] = "author";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["placeholder"] = __("Select one or more authors", "unlimited-elements-for-elementor");
		$params["is_multiple"] = true;
		$params["elementor_condition"] = $arrConditionIncludeAuthor;

		$this->addMultiSelect($name . "_excludeby_authors", $arrAuthors, __("Exclude By Author", "unlimited-elements-for-elementor"), "", $params);

		//------- Exclude By --- OFFSET --------
		$conditionExcludeByOffset = $conditionExcludeBy;
		$conditionExcludeByOffset[$name . "_excludeby"] = "offset";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_NUMBER;
		$params["description"] = __("Use this setting to skip over posts, not showing first posts to the offset given", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["elementor_condition"] = $conditionExcludeByOffset;

		$this->addTextBox($name . "_offset", "0", esc_html__("Offset", "unlimited-elements-for-elementor"), $params);

		//--------- show children -------------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionExcludeByTerms;

		$this->addRadioBoolean($name . "_terms_exclude_children", __("Exclude Terms With Children", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);

		//------- Exclude By --- SPECIFIC POSTS --------
		$conditionExcludeBySpecific = $conditionExcludeBy;
		$conditionExcludeBySpecific[$name . "_excludeby"] = "specific_posts";

		$params = array();
		$params["elementor_condition"] = $conditionExcludeBySpecific;

		// translators: %s is a string
		$this->addPostIDSelect($name . "_exclude_specific_posts", sprintf(__("Specific %s To Exclude", "unlimited-elements-for-elementor"), $textPosts), $conditionExcludeBySpecific, $isForWooProducts);

		//----- hr -------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrNotManualElementorCondition;

		$this->addHr($name . "_post_after_exclude", $params);

		//------- Post Status --------
		$arrStatuses = HelperProviderUC::getArrPostStatusSelect();
		$arrStatuses = array_flip($arrStatuses);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["placeholder"] = __("Select one or more statuses", "unlimited-elements-for-elementor");
		$params["is_multiple"] = true;
		$params["elementor_condition"] = $arrCustomOnlyCondition;

		$this->addMultiSelect($name . "_status", $arrStatuses, __("Post Status", "unlimited-elements-for-elementor"), array("publish"), $params);

		//------- max items --------
		
		$params = array("unit" => "posts");

		if(empty($defaultMaxPosts))
			$defaultMaxPosts = 10;

		$maxItems = UniteFunctionsUC::getVal($value, $name . "_maxitems", $defaultMaxPosts);
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 posts if empty", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["elementor_condition"] = $arrCustomAndRelatedElementorCondition;

		// translators: %s is a string
		$this->addTextBox($name . "_maxitems", $maxItems, sprintf(esc_html__("Max %s", "unlimited-elements-for-elementor"), $textPosts), $params);
		
		//------- manual max items --------
				
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("All Selected", "unlimited-elements-for-elementor");
		$params["description"] = __("Limit the max posts if you want to use load more or pagination filters. Keep empty for all selected.", "unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["elementor_condition"] = $arrManualElementorCondition;
		
		// translators: %s is a string
		$this->addTextBox($name . "_maxitems_manual", "", sprintf(esc_html__("Max %s", "unlimited-elements-for-elementor"), $textPosts), $params);
		
		
		//------- override post type --------
		$arrTypesCurrent = UniteFunctionsUC::addArrFirstValue($arrTypesSimple, "", "[Original Post Type]");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrCurrentElementorCondition;

		$this->addSelect($name . "_posttype_current", $arrTypesCurrent, esc_html__("Post Type Override", "unlimited-elements-for-elementor"), "", $params);
		
		//------- max items for current --------
		$params = array("unit" => "posts");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		// translators: %s is a string
		$params["description"] = sprintf(__("Override Number Of %s, remain empty for default. If you are using pagination widget, leave it empty", "unlimited-elements-for-elementor"), $textPosts);
		$params["add_dynamic"] = true;
		$params["elementor_condition"] = $arrCurrentElementorCondition;

		// translators: %s is a string
		$this->addTextBox($name . "_maxitems_current", "", sprintf(esc_html__("Max %s", "unlimited-elements-for-elementor"), $textPosts), $params);

		//----- hr before orderby --------
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name . "_hr_before_orderby", $params);

		//----- orderby --------
		$arrOrder = UniteFunctionsWPUC::getArrSortBy($isForWooProducts);
		$arrOrder = array_flip($arrOrder);

		$arrDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrDir = array_flip($arrDir);

		//---- orderby for custom and current -----
		$orderBY = UniteFunctionsUC::getVal($value, $name . "_orderby", "default");

		$params = array();
		//$params[UniteSettingsUC::PARAM_ADDFIELD] = $name."_orderdir1";
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select how you wish to order posts", "unlimited-elements-for-elementor");

		$this->addSelect($name . "_orderby", $arrOrder, __("Order By", "unlimited-elements-for-elementor"), $orderBY, $params);

		//--- meta value param -------
		$arrCondition = array();
		$arrCondition[$name . "_orderby"] = array(UniteFunctionsWPUC::SORTBY_META_VALUE, UniteFunctionsWPUC::SORTBY_META_VALUE_NUM);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["class"] = "alias";
		$params["add_dynamic"] = false;
		$params["elementor_condition"] = $arrCondition;

		$this->addTextBox($name . "_orderby_meta_key1", "", __("&nbsp;&nbsp;Custom Field Name", "unlimited-elements-for-elementor"), $params);

		//---- order dir -----
		$orderDir1 = UniteFunctionsUC::getVal($value, $name . "_orderdir1", "default");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select order direction. Descending A-Z or Accending Z-A", "unlimited-elements-for-elementor");

		$this->addSelect($name . "_orderdir1", $arrDir, __("&nbsp;&nbsp;Order By Direction", "unlimited-elements-for-elementor"), $orderDir1, $params);

		//---- hr before query id -----
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name . "_hr_after_order_dir", $params);

		//allow to modify settings by third party plugins
		do_action("ue_modify_post_list_settings", $this, $name);

		//---- query id -----
		$isPro = GlobalsUC::$isProVersion;

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		if($isPro === true){
			$title = __("Query ID", "unlimited-elements-for-elementor");
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. <a href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'><a target='blank' href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'>See docs here</a></a>.", "unlimited-elements-for-elementor");
		}else{    //free version
			$title = __("Query ID (pro)", "unlimited-elements-for-elementor");
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. This feature exists in a PRO Version only. <a target='blank' href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'>help</a>", "unlimited-elements-for-elementor");
			$params["disabled"] = true;
		}

		$queryID = UniteFunctionsUC::getVal($value, $name . "_queryid");

		$this->addTextBox($name . "_queryid", $queryID, $title, $params);

		//---- show debug -----
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($name . "_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		//--------- debug type posts ---------
		$arrType = array_flip(array(
			"basic" => __("Basic", "unlimited-elements-for-elementor"),
			"show_query" => __("Full", "unlimited-elements-for-elementor"),
		));

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name . "_show_query_debug" => "true");

		$this->addSelect($name . "_query_debug_type", $arrType, __("Debug Options", "unlimited-elements-for-elementor"), "basic", $params);
	}


	private function __________REMOTE_______(){}

	/**
	 * add remote parent settings
	 */
	private function addRemoteSettingsParent($name,$value,$title,$param){

		$prefix = $name."_";

		$remoteEnableName = $prefix."enable";
		$condition = array($remoteEnableName=>"true");

		//---- enable remote -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Enable the remote connection functionality for this widget", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($remoteEnableName, __("Enable Remote Connection", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		//widget name

		$arrNames = HelperProviderUC::getArrRemoteParentNames();
		$arrNames = array_flip($arrNames);

		$params = array(
			"description"=>__("This name will be used to connect and control this widget by other widgets", "unlimited-elements-for-elementor"),
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $condition,
		);

		$this->addSelect($prefix."name", $arrNames, __("Widget Name for Connection", "unlimited-elements-for-elementor"), "auto", $params);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = array($remoteEnableName=>"true",$prefix."name"=>"custom");

		$this->addTextBox($prefix."custom_name", "", __("Custom Name","unlimited-elements-for-elementor"), $params);

		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_HR,
		);

		$this->addHr("hr_before_sync",$params);

		//sync

		$remoteSyncName = $prefix."sync";
		$conditionSync = array($remoteSyncName=>"true");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Sync slide run with other widgets", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($prefix."sync", __("Sync", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		//sync with widget name

		$arrNames = HelperProviderUC::getArrRemoteSyncNames();
		$arrNames = array_flip($arrNames);

		$params = array(
			"description"=>__("Choose the sync group", "unlimited-elements-for-elementor"),
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $conditionSync,
		);

		$this->addSelect($prefix."sync_name", $arrNames, __("Sync Group", "unlimited-elements-for-elementor"), "group1", $params);

		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_HR,
		);

		$this->addHr("hr_before_debug",$params);

		//debug remote widgets

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show information about remote widgets that connected to this widget. Please turn off this option before release", "unlimited-elements-for-elementor");

		$this->addRadioBoolean($prefix."debug", __("Show Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);


	}


	/**
	 * add remote controller settings
	 */
	private function addRemoteSettingsController($name,$value,$title,$param){

		$prefix = $name."_";

		$arrNames = HelperProviderUC::getArrRemoteParentNames();
		$arrNames = array_flip($arrNames);

		$params = array(
			"description"=>__("Select the name of the parent for connetion", "unlimited-elements-for-elementor"),
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
		);
		
		$this->addSelect($prefix."name", $arrNames, __("Remote Parent Name", "unlimited-elements-for-elementor"), "auto", $params);
		
		$isMoreParents = UniteFunctionsUC::getVal($param, "controller_more_parents");
		$isMoreParents = UniteFunctionsUC::strToBool($isMoreParents);

		if($isMoreParents == true){

			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;

			$this->addRadioBoolean($prefix."more_parent", __("Connect To One More Parent", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

			$params = array(
				"description"=>__("Select the name of the second parent for connetion both parents in one click", "unlimited-elements-for-elementor"),
				"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
				"elementor_condition" => array($prefix."more_parent"=>"true"),
			);

			$arrNames = HelperProviderUC::getArrRemoteParentNames(true);
			$arrNames = array_flip($arrNames);

			$this->addSelect($prefix."name2", $arrNames, __("Remote Parent Name", "unlimited-elements-for-elementor"), "first", $params);

		}

		// ---- custom name

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = array($prefix."name"=>"custom");
		
		$this->addTextBox($prefix."custom_name", "", __("Custom Parent Name","unlimited-elements-for-elementor"), $params);


		// ---- show debug

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;

		$this->addRadioBoolean($prefix."show_debug", __("Show Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		// ---- hr

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr("hr_remote_child",$params);

	}

	/**
	 * add remote background settings
	 */
	protected function addRemoteSettingsBackground($name,$value,$title,$param){

		$prefix = $name."_";

		// --- sync ----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;

		$condition = UniteFunctionsUC::getVal($param, "elementor_condition");
		$params["elementor_condition"] = $condition;

		$this->addRadioBoolean($prefix."sync", __("Enable Sync and Remote", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		// --- sync name ----

		$arrNames = HelperProviderUC::getArrRemoteSyncNames();
		$arrNames = array_flip($arrNames);

		$conditionSync = $condition;
		$conditionSync[$prefix."sync"] = "true";

		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $conditionSync,
		);

		$this->addSelect($prefix."sync_name", $arrNames, __("Sync Group", "unlimited-elements-for-elementor"), "group1", $params);


		// --- remote name ----
		
		$arrNames = HelperProviderUC::getArrRemoteParentNames(false, false);
		$arrNames = array_flip($arrNames);
		
		$conditionSync = $condition;
		$conditionSync[$prefix."sync"] = "true";

		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $conditionSync,
		);

		$this->addSelect($prefix."remote_name", $arrNames, __("Remote Parent Name", "unlimited-elements-for-elementor"), "auto", $params);
		

		//  --- debug ---

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionSync;

		$this->addRadioBoolean($prefix."debug", __("Show Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);


	}


	/**
	 * add remote settings
	 */
	protected function addRemoteSettings($name,$value,$title,$param){

		$type = UniteFunctionsUC::getVal($param, "remote_type");

		switch($type){
			case "controller":

				$this->addRemoteSettingsController($name,$value,$title,$param);

			break;
			case "background":

				$this->addRemoteSettingsBackground($name,$value,$title,$param);

			break;
			default:
			case "parent":

				$this->addRemoteSettingsParent($name,$value,$title,$param);
			break;
		}

	}


	private function __________DYNAMIC_______(){}

	/**
	 * get gallery title title source options
	 */
	protected function getGalleryTitleSourceOptions($isDescription = false, $hasPosts = false){

		if($isDescription == false){

			$arrTitleOptions = array();

			if($hasPosts){
				$arrTitleOptions["post_title"] = __("Post Title", "unlimited-elements-for-elementor");
				$arrTitleOptions["post_excerpt"]= __("Post Excerpt", "unlimited-elements-for-elementor");
			}

			$arrTitleOptions["image_auto"] = __("Image Auto (title or alt or caption)", "unlimited-elements-for-elementor");
			$arrTitleOptions["image_title"] = __("Image Title", "unlimited-elements-for-elementor");
			$arrTitleOptions["image_alt"] = __("Image Alt", "unlimited-elements-for-elementor");
			$arrTitleOptions["image_caption"] = __("Image Caption", "unlimited-elements-for-elementor");

			$arrTitleOptions = array_flip($arrTitleOptions);

			return($arrTitleOptions);
		}

		//description

		$arrDescOptions = array();

		if($hasPosts == true){
			$arrDescOptions["post_excerpt"]= __("Post Excerpt", "unlimited-elements-for-elementor");
			$arrDescOptions["post_title"] = __("Post Title", "unlimited-elements-for-elementor");
			$arrDescOptions["post_content"] = __("Post Content", "unlimited-elements-for-elementor");
		}

		$arrDescOptions["image_description"] = __("Image Description", "unlimited-elements-for-elementor");
		$arrDescOptions["image_title"] = __("Image Title", "unlimited-elements-for-elementor");
		$arrDescOptions["image_alt"] = __("Image Alt", "unlimited-elements-for-elementor");
		$arrDescOptions["image_caption"] = __("Image Caption", "unlimited-elements-for-elementor");

		$arrDescOptions = array_flip($arrDescOptions);

		return($arrDescOptions);
	}


	/**
	 * add gallery field
	 */
	protected function addListingPicker_gallery($name,$value,$title,$param){

		//---- gallery option

		$conditionGallery = array($name."_source" => "gallery");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_GALLERY;
		$params["elementor_condition"] = $conditionGallery;

		$galleryDefaults = HelperProviderUC::getArrDynamicGalleryDefaults();

		$this->addTextBox($name."_gallery", $galleryDefaults, __("Choose Images","unlimited-elements-for-elementor"), $params);

		//============

		$conditionPost = array($name."_source" => "posts");
		$conditionPostProduct = array($name."_source" => array("posts","products") );


		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionPostProduct;

		$this->addHr($name."_hr_before_title_sources_post",$params);


		//---- posts options - title source

		$arrTitleOptions = $this->getGalleryTitleSourceOptions(false, true);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionPostProduct;

		$this->addSelect($name."_title_source_post", $arrTitleOptions, __("Image Title Source", "unlimited-elements-for-elementor"), "post_title", $params);

		//---- posts options - description source

		$arrDescOptions = $this->getGalleryTitleSourceOptions(true, true);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionPostProduct;

		$this->addSelect($name."_description_source_post", $arrDescOptions, __("Image Description Source", "unlimited-elements-for-elementor"), "post_excerpt", $params);

		//---- current post meta

		$conditionCurrentMeta = array($name."_source" => "current_post_meta");

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = $conditionCurrentMeta;

		$this->addTextBox($name."_current_metakey", "", __("Meta Key","unlimited-elements-for-elementor"), $params);

		//---- current post meta - DEBUG

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the current post meta fields, turn off it after choose the right one", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionCurrentMeta;

		$this->addRadioBoolean($name."_show_metafields", __("Debug - Show Meta Fields", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		//=========== GALLERY TITLE AND DESCRIPTION SOURCE =================

		//---- hr before title source

		$conditionTitleSource = array($name."_source" => array("gallery", "current_post_meta"));

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionTitleSource;

		$this->addHr($name."_hr_before_title_sources",$params);


		//---- gallery title source

		$arrTitleOptions = $this->getGalleryTitleSourceOptions(false, false);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionTitleSource;

		$this->addSelect($name."_title_source_gallery", $arrTitleOptions, __("Image Title Source", "unlimited-elements-for-elementor"), "image_auto", $params);


		//---- gallery description source

		$arrDescOptions = $this->getGalleryTitleSourceOptions(true, false);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionTitleSource;

		$this->addSelect($name."_description_source_gallery", $arrDescOptions, __("Image Description Source", "unlimited-elements-for-elementor"), "image_description", $params);

		//----- hr before image size
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_hr_before_imagesize",$params);

		//----- thumb image size

		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;

		$arrSizes = array_flip($arrSizes);
		$this->addSelect($name."_thumb_size", $arrSizes, __("Thumb Image Size", "unlimited-elements-for-elementor"), "medium_large", $params);


		//----- big image size

		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;

		$arrSizes = array_flip($arrSizes);
		$this->addSelect($name."_image_size", $arrSizes, __("Big Image Size", "unlimited-elements-for-elementor"), "large", $params);


		//=========== GALLERY POSTS VIDEOS =================

		//----- hr before videos

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;

		$this->addHr($name."_hr_before_videos",$params);

		//----- hr before videos

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionPost;

		$this->addRadioBoolean($name."_posts_enable_videos", "Enable Videos Items",false,"Yes","No",$params);

		//----- meta field for item type

		$condionEnableVideos = $conditionPost;
		$condionEnableVideos[$name."_posts_enable_videos"] = "true";

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "example: item_type";
		$params["description"] = "A custom fields that store item type text. The types are: image|youtube|vimeo";
		$params["elementor_condition"] = $condionEnableVideos;

		$this->addTextBox($name."_meta_itemtype", "", __("Meta Field for Item Type","unlimited-elements-for-elementor"), $params);

		//----- meta field for video id

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "example: video_id";
		$params["description"] = "A custom fields that store Youtube ID / link or Vimeo ID";
		$params["elementor_condition"] = $condionEnableVideos;

		$this->addTextBox($name."_meta_videoid", "", __("Meta Field for Video ID","unlimited-elements-for-elementor"), $params);

		//----- debug meta fields
		/*
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $condionEnableVideos;

		$this->addRadioBoolean($name."_debug_meta", "Debug Meta Fields",false,"Yes","No",$params);
		*/

	}


	/**
	 * add listing picker, function for override
	 */
	protected function addListingPicker($name,$value,$title,$param){

		//add template picker
		$useFor = UniteFunctionsUC::getVal($param, "use_for");

		if($useFor == "remote"){
			$this->addRemoteSettings($name, $value, $title, $param);
			return(false);
		}

		if($useFor == "items"){

			$this->addItemsMultisourceSettings($name, $value, $title, $param);

			return(false);
		}

		$isForGallery = ($useFor == "gallery");

		$isEnableVideoItems = UniteFunctionsUC::getVal($param, "gallery_enable_video");
		$isEnableVideoItems = UniteFunctionsUC::strToBool($isEnableVideoItems);

		//set text prefix
		$textPrefix = __("Items ","unlimited-elements-for-elementor");
		if($isForGallery == true)
			$textPrefix = __("Gallery Items ","unlimited-elements-for-elementor");

		//loop item
		if($isForGallery == false){
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_TEMPLATE;
			$this->addTextBox($name."_template", "", $textPrefix.__(" Item Template","unlimited-elements-for-elementor"), $params);
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_TEMPLATE;
			$params["description"] = __("Optional. Alternate template for the grid","unlimited-elements-for-elementor");
			
			$this->addTextBox($name."_template2", "", $textPrefix.__(" Item Alternate Template","unlimited-elements-for-elementor"), $params);
			
		}

		//-------------------

		// add type select

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrSource = array();

		if($isForGallery == true){
			$arrSource["gallery"] = __("Gallery", "unlimited-elements-for-elementor");

			if($isEnableVideoItems == true)
				$arrSource["image_video_repeater"] = __("Image And Video Items", "unlimited-elements-for-elementor");
			else
				$arrSource["image_video_repeater"] = __("Image Items", "unlimited-elements-for-elementor");

			$arrSource["instagram"] = __("Instagram", "unlimited-elements-for-elementor");
		}

		$arrSource["posts"] = __("Posts", "unlimited-elements-for-elementor");

		$isWooActive = UniteCreatorWooIntegrate::isWooActive();
		if($isWooActive == true)
			$arrSource["products"] = __("Products", "unlimited-elements-for-elementor");

		
		if($isForGallery == true && $isWooActive == true){
			$arrSource["current_product_gallery"] = __("Current Product Gallery", "unlimited-elements-for-elementor");
			$arrSource["current_product_variations"] = __("Current Product Variations", "unlimited-elements-for-elementor");
		}
		
		if($isForGallery == true){
			$arrSource["current_post_meta"] = __("Current Post Metafield", "unlimited-elements-for-elementor");
		}

		//$arrSource["terms"] = __("Terms", "unlimited-elements-for-elementor");

		$arrSource = array_flip($arrSource);

		$default = "posts";
		if($isForGallery == true)
			$default = "gallery";

		$this->addSelect($name."_source", $arrSource, $textPrefix.__("Source", "unlimited-elements-for-elementor"), $default, $params);

		if($isForGallery == true)
			$this->addListingPicker_gallery($name,$value,$title,$param);

	}


	private function __________MULTISOURCE_______(){}

	/**
	 * add items multisource
	 */
	protected function addItemsMultisourceSettings($name, $value, $title, $param){

		//pro version - add all settings

		if(GlobalsUC::$isProVersion == true){
			require_once GlobalsUC::$pathPro . "provider_settings_multisource_pro.class.php";
			$objMultisourceSettings = new UniteCreatorSettingsMultisourcePro();
		}else{
			//free version - add placeholders

			$objMultisourceSettings = new UniteCreatorSettingsMultisource();
		}

		$objMultisourceSettings->setSettings($this);
		$objMultisourceSettings->addItemsMultisourceSettings($name, $value, $title, $param);
	}


	private function __________DIALOG_SETTINGS_______(){}

	/**
	 * add dialog settings
	 */
	public function addDialogSettings($type){

		switch($type){
			case UniteCreatorSettings::TYPE_TYPOGRAPHY:
				$this->addTypographyDialogSettings();
			break;
			case UniteCreatorSettings::TYPE_TEXTSHADOW:
				$this->addTextShadowDialogSettings();
			break;
			case UniteCreatorSettings::TYPE_TEXTSTROKE:
				$this->addTextStrokeDialogSettings();
			break;
			case UniteCreatorSettings::TYPE_BOXSHADOW:
				$this->addBoxShadowDialogSettings();
			break;
			case UniteCreatorSettings::TYPE_CSS_FILTERS:
				$this->addCssFiltersDialogSettings();
			break;
			default:
				UniteFunctionsUC::throwError(__FUNCTION__ . " Error: Dialog type \"$type\" is not implemented");
		}
	}

	/**
	 * add typography dialog settings
	 */
	private function addTypographyDialogSettings(){

		$data = HelperUC::getFontPanelData();
		$type = UniteCreatorDialogParam::PARAM_TYPOGRAPHY;
		$units = array("px", "%", "em", "rem");
		$defaultTitle = __("Default", "unlimited-elements-for-elementor");

		$responsive = array(
			"desktop" => "",
			"tablet" => "_tablet",
			"mobile" => "_mobile",
		);

		// font family
		$fontFamily = UniteFunctionsUC::getVal($data, "arrFontFamily");
		$fontFamily = UniteFunctionsUC::addArrFirstValue($fontFamily, $defaultTitle);
		$fontFamily = array_flip($fontFamily);

		$params = array();
		$params["class"] = "select2";
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "family");
		$params["label_block"] = true;

		$this->addSelect("font_family", $fontFamily, __("Font Family", "unlimited-elements-for-elementor"), "", $params);

		// font weight
		$fontWeight = UniteFunctionsUC::getVal($data, "arrFontWeight");
		$fontWeight = UniteFunctionsUC::addArrFirstValue($fontWeight, $defaultTitle);
		$fontWeight = array_flip($fontWeight);

		$params = array();
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "weight");
		$params["label_block"] = true;

		$this->addSelect("font_weight", $fontWeight, __("Weight", "unlimited-elements-for-elementor"), "", $params);

		// font size
		$params = array();
		$params["units"] = $units;
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "size");
		$params["label_block"] = true;
		$params["show_slider"] = false;
		$params["is_responsive"] = true;

		foreach($responsive as $device => $suffix){
			$params["responsive_type"] = $device;

			$this->addRangeSlider("font_size" . $suffix, "", __("Size", "unlimited-elements-for-elementor"), $params);
		}

		// line height
		$params = array();
		$params["units"] = $units;
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "line-height");
		$params["label_block"] = true;
		$params["show_slider"] = false;
		$params["is_responsive"] = true;

		foreach($responsive as $device => $suffix){
			$params["responsive_type"] = $device;

			$this->addRangeSlider("line_height" . $suffix, "", __("Line Height", "unlimited-elements-for-elementor"), $params);
		}

		// text transform
		$textTransform = array(
			"capitalize" => array(
				"title" => __("Capitalize", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="m8 11.5-3.5-9-3.5 9M2.167 8.5h4.666M9.5 13.5s.908 1.125 2.5 1c1.395-.11 2.501-.947 2.501-2.287V5.5"/><path d="M12.001 11.512c1.381 0 2.501-1.346 2.501-3.006 0-1.66-1.12-3.006-2.501-3.006C10.62 5.5 9.5 6.846 9.5 8.506c0 1.66 1.12 3.006 2.501 3.006Z"/></svg>',
			),
			"uppercase" => array(
				"title" => __("Uppercase", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M7.5 12.5 4 3.5l-3.5 9M1.667 9.5h4.666M15.159 5.648c-.093-.165-.973-2.148-3.073-2.148-2.414 0-3.585 2.223-3.585 4.595 0 2.481 1.325 4.405 3.585 4.405 2.042 0 3.434-1.492 3.415-4h-3"/></svg>',
			),
			"lowercase" => array(
				"title" => __("Lowercase", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M2.1 5c.5-.8 1.4-1.3 2.4-1.2 1.2.1 1.9.9 1.9 1.9v4.4" /><path d="M6.4 8.1c-.6 1.3-1.7 2-2.7 2-.7 0-1.5-.5-1.7-1.3-.1-.7.3-1.6 1.3-1.9 1-.3 3.1-.2 3.1-.2M9.4 12s.9 1.1 2.5 1c1.4-.1 2.5-.9 2.5-2.3V4" /><path d="M11.9 10c1.4 0 2.5-1.3 2.5-3s-1.1-3-2.5-3-2.5 1.3-2.5 3 1.1 3 2.5 3Z" /></svg>',
			),
		);

		$params = array();
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "transform");
		$params["deselectable"] = true;

		$this->addButtonsGroup("text_transform", $textTransform, __("Transform", "unlimited-elements-for-elementor"), "", $params);

		// font style
		$fontStyle = array(
			"normal" => array(
				"title" => __("Normal", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8 13V3M6 3h4M6 13h4" /></svg>',
			),
			"italic" => array(
				"title" => __("Italic", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="m6.54 12.78 2.92-9.56M7.5 3h4m-7 10h4" /></svg>',
			),
		);

		$params = array();
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "style");
		$params["deselectable"] = true;

		$this->addButtonsGroup("font_style", $fontStyle, __("Style", "unlimited-elements-for-elementor"), "", $params);

		// text decoration
		$textDecoration = array(
			"underline" => array(
				"title" => __("Underline", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M3.5 14h9.4M12.1 2v5.6c0 2.1-1.7 3.9-3.9 3.9S4.3 9.8 4.3 7.6V2" /></svg>',
			),
			"overline" => array(
				"title" => __("Overline", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M2 2h12M12 14 8.5 5 5 14M6 11h4.7" /></svg>',
			),
			"line-through" => array(
				"title" => __("Strikethrough", "unlimited-elements-for-elementor"),
				"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M11.5 4.7c-.7-1.5-2-2.2-3.5-2.2-1.8 0-3.4 1-3.4 2.6s1.2 2.2 2.7 2.6M2.8 7.7H14M2 7.7h12M11.2 9.3c.3.4.5.9.5 1.5 0 1.6-1.7 2.8-3.6 2.9-1.6 0-3.2-.6-3.9-2.5" /></svg>',
			),
		);

		$params = array();
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "decoration");
		$params["deselectable"] = true;

		$this->addButtonsGroup("text_decoration", $textDecoration, __("Decoration", "unlimited-elements-for-elementor"), "", $params);

		// letter spacing
		$params = array();
		$params["units"] = $units;
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "letter-spacing");
		$params["label_block"] = true;
		$params["show_slider"] = false;
		$params["is_responsive"] = true;

		foreach($responsive as $device => $suffix){
			$params["responsive_type"] = $device;

			$this->addRangeSlider("letter_spacing" . $suffix, "", __("Letter Spacing", "unlimited-elements-for-elementor"), $params);
		}

		// word spacing
		$params = array();
		$params["units"] = $units;
		$params["selector"] = self::SELECTOR_PLACEHOLDER;
		$params["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type, "word-spacing");
		$params["label_block"] = true;
		$params["show_slider"] = false;
		$params["is_responsive"] = true;

		foreach($responsive as $device => $suffix){
			$params["responsive_type"] = $device;

			$this->addRangeSlider("word_spacing" . $suffix, "", __("Word Spacing", "unlimited-elements-for-elementor"), $params);
		}
	}

	/**
	 * add text shadow dialog settings
	 */
	private function addTextShadowDialogSettings(){

		$type = UniteCreatorDialogParam::PARAM_TEXTSHADOW;
		$groupSelectorName = $type . "_group";

		// color
		$colorName = "color";

		$params = array();
		$params["group_selector"] = $groupSelectorName;

		$this->addColorPicker($colorName, "", __("Color", "unlimited-elements-for-elementor"), $params);

		// blur
		$blurName = "blur";
		$blurDefault = 10;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($blurName, $blurDefault, __("Blur", "unlimited-elements-for-elementor"), $params);

		// x
		$xName = "x";
		$xDefault = 0;

		$params = array();
		$params["min"] = -100;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($xName, $xDefault, __("Horizontal", "unlimited-elements-for-elementor"), $params);

		// y
		$yName = "y";
		$yDefault = 0;

		$params = array();
		$params["min"] = -100;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($yName, $yDefault, __("Vertical", "unlimited-elements-for-elementor"), $params);

		// group selector
		$groupSelector = self::SELECTOR_PLACEHOLDER;
		$groupSelectorValue = HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_TEXTSHADOW);

		$groupSelectorReplace = array(
			"{{X}}" => $xName,
			"{{Y}}" => $yName,
			"{{BLUR}}" => $blurName,
			"{{COLOR}}" => $colorName,
		);

		$this->addGroupSelector($groupSelectorName, $groupSelector, $groupSelectorValue, $groupSelectorReplace);
	}


	/**
	 * add text stroke dialog settings
	 */
	private function addTextStrokeDialogSettings(){

		$type = UniteCreatorDialogParam::PARAM_TEXTSTROKE;
		$groupSelectorName = $type . "_group";

		// stroke color
		$colorName = "color";
		$colorDefault = "#000000";

		$params = array();
		$params["group_selector"] = $groupSelectorName;

		$this->addColorPicker($colorName, $colorDefault, __("Stroke Color", "unlimited-elements-for-elementor"), $params);

		// stroke width
		$widthName = "width";
		$widthDefault = "";

		$params = array();
		$params["min"] = 0;
		$params["max"] = 10;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($widthName, $widthDefault, __("Stroke Width", "unlimited-elements-for-elementor"), $params);

		// group selector
		$groupSelector = self::SELECTOR_PLACEHOLDER;
		$groupSelectorValue = HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_TEXTSTROKE);

		$groupSelectorReplace = array(
			"{{WIDTH}}" => $widthName,
			"{{COLOR}}" => $colorName,
		);

		$this->addGroupSelector($groupSelectorName, $groupSelector, $groupSelectorValue, $groupSelectorReplace);
	}



	/**
	 * add box shadow dialog settings
	 */
	private function addBoxShadowDialogSettings(){

		$type = UniteCreatorDialogParam::PARAM_BOXSHADOW;
		$groupSelectorName = $type . "_group";

		// color
		$colorName = "color";

		$params = array();
		$params["group_selector"] = $groupSelectorName;

		$this->addColorPicker($colorName, "", __("Color", "unlimited-elements-for-elementor"), $params);

		// x
		$xName = "x";
		$xDefault = 0;

		$params = array();
		$params["min"] = -100;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($xName, $xDefault, __("Horizontal", "unlimited-elements-for-elementor"), $params);

		// y
		$yName = "y";
		$yDefault = 0;

		$params = array();
		$params["min"] = -100;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($yName, $yDefault, __("Vertical", "unlimited-elements-for-elementor"), $params);

		// blur
		$blurName = "blur";
		$blurDefault = 10;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($blurName, $blurDefault, __("Blur", "unlimited-elements-for-elementor"), $params);

		// spread
		$spreadName = "spread";
		$spreadDefault = 0;

		$params = array();
		$params["min"] = -100;
		$params["max"] = 100;
		$params["step"] = 1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($spreadName, $spreadDefault, __("Spread", "unlimited-elements-for-elementor"), $params);

		// position
		$positions = array_flip(array(
			" " => __("Outline", "unlimited-elements-for-elementor"),
			"inset" => __("Inset", "unlimited-elements-for-elementor"),
		));

		$positionName = "position";
		$positionDefault = " ";

		$params = array();
		$params["group_selector"] = $groupSelectorName;

		$this->addSelect($positionName, $positions, __("Position", "unlimited-elements-for-elementor"), $positionDefault, $params);

		// group selector
		$groupSelector = self::SELECTOR_PLACEHOLDER;
		$groupSelectorValue = HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_BOXSHADOW);

		$groupSelectorReplace = array(
			"{{X}}" => $xName,
			"{{Y}}" => $yName,
			"{{BLUR}}" => $blurName,
			"{{SPREAD}}" => $spreadName,
			"{{COLOR}}" => $colorName,
			"{{POSITION}}" => $positionName,
		);

		$this->addGroupSelector($groupSelectorName, $groupSelector, $groupSelectorValue, $groupSelectorReplace);
	}

	/**
	 * add css filters dialog settings
	 */
	private function addCssFiltersDialogSettings(){

		$type = UniteCreatorDialogParam::PARAM_CSS_FILTERS;
		$groupSelectorName = $type . "_group";

		// blur
		$blurName = "blur";
		$blurDefault = 0;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 10;
		$params["step"] = 0.1;
		$params["units"] = array("px");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($blurName, $blurDefault, __("Blur", "unlimited-elements-for-elementor"), $params);

		// brightness
		$brightnessName = "brightness";
		$brightnessDefault = 100;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 200;
		$params["step"] = 1;
		$params["units"] = array("%");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($brightnessName, $brightnessDefault, __("Brightness", "unlimited-elements-for-elementor"), $params);

		// contrast
		$contrastName = "contrast";
		$contrastDefault = 100;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 200;
		$params["step"] = 1;
		$params["units"] = array("%");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($contrastName, $contrastDefault, __("Contrast", "unlimited-elements-for-elementor"), $params);

		// saturation
		$saturationName = "saturation";
		$saturationDefault = 100;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 200;
		$params["step"] = 1;
		$params["units"] = array("%");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($saturationName, $saturationDefault, __("Saturation", "unlimited-elements-for-elementor"), $params);

		// hue
		$hueName = "hue";
		$hueDefault = 0;

		$params = array();
		$params["min"] = 0;
		$params["max"] = 360;
		$params["step"] = 1;
		$params["units"] = array("deg");
		$params["group_selector"] = $groupSelectorName;

		$this->addRangeSlider($hueName, $hueDefault, __("Hue", "unlimited-elements-for-elementor"), $params);

		// group selector
		$groupSelector = self::SELECTOR_PLACEHOLDER;
		$groupSelectorValue = HelperHtmlUC::getCSSSelectorValueByParam(UniteCreatorDialogParam::PARAM_CSS_FILTERS);

		$groupSelectorReplace = array(
			"{{BLUR}}" => $blurName,
			"{{BRIGHTNESS}}" => $brightnessName,
			"{{CONTRAST}}" => $contrastName,
			"{{SATURATE}}" => $saturationName,
			"{{HUE}}" => $hueName,
		);

		$this->addGroupSelector($groupSelectorName, $groupSelector, $groupSelectorValue, $groupSelectorReplace);
	}

}
