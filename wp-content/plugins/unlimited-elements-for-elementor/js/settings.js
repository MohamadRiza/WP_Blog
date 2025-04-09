"use strict";

function UniteSettingsUC(){

	var g_arrControls = {};
	var g_arrChildrenControls = {};

	var g_IDPrefix = "#unite_setting_";
	var g_selectorWrapperID = null;
	var g_colorPicker, g_colorPickerWrapper, g_iconsHash={};
	var g_objParent = null, g_objWrapper = null, g_objSapTabs = null;
	var g_objProvider = new UniteProviderAdminUC();

	var g_vars = {
		NOT_UPDATE_OPTION: "unite_settings_no_update_value",
		keyupTrashold: 500,
		animationDuration: 300,
	};

	var g_temp = {
		settingsID:null,
		handle: null,
		triggerChangeQueue: 0,
		cacheValues: null,
		objItemsManager: null,
		isSidebar: false,
		isInited: false,
		customSettingsKey: "custom_setting_type",
		colorPickerType: null,
		linkAutocompleteRequest: null,
		isRepeaterExists: false,
		disableExcludeSelector:false
	};

	this.events = {
			CHANGE: "settings_change",
			INSTANT_CHANGE: "settings_instant_change",
			AFTER_INIT: "after_init",
			OPEN_CHILD_PANEL: "open_child_panel",
			SELECTORS_CHANGE: "selectors_change",
			RESPONSIVE_TYPE_CHANGE: "responsive_type_change",
	};

	var g_options = {
			show_saps:false,
			saps_type:""
	};

	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();

	var t=this;


	/**
	 * validate that the parent exists
	 */
	function validateInited() {
		if (!g_objParent || g_objParent.length === 0)
			throw new Error("Settings must be initialized first");
	}

	/**
	 * compare control values
	 */
	function isInputValuesEqual(controlValue, value) {
		var isEqual;

		if (jQuery.isArray(value) === true) {
			isEqual = jQuery.inArray(String(controlValue), value) !== -1;
		} else {
			if (jQuery.isArray(controlValue) === true)
				isEqual = jQuery.inArray(String(value), controlValue) !== -1;
			else if (value === "true" || value === "false") {
				value = g_ucAdmin.strToBool(value);
				controlValue = g_ucAdmin.strToBool(controlValue);

				isEqual = (controlValue === value);
			} else
				isEqual = (String(controlValue) === String(value));
		}

		return isEqual;
	}

	/**
	 * get input by name and filter by type.
	 * if not found or filtered, return null
	 */
	this.getInputByName = function(name, type){

		var inputID = g_IDPrefix+name;
		var objInput = jQuery(inputID);
		if(objInput.length == 0)
			return(null);

		if(!type)
			return(objInput);

		var inputType = objInput[0].type;
		if(type != inputType)
			return(null);

		return(objInput);
	};


	this.__________OTHER_EXTERNAL__________ = function(){};


	/**
	 * init tipsy
	 */
	function initTipsy(){
		if (typeof jQuery("body").tipsy !== "function")
			return;

		g_objWrapper.tipsy({
			selector: ".uc-tip",
			delayIn: 200,
			offset: 4,
			html: true,
			gravity: function () {
				var objTip = jQuery(this);

				return objTip.data("tipsy-gravity") || "s";
			}
		});

		jQuery(document).on("click", ".uc-tip", function () {
			var objTip = jQuery(this);

			// check if the element is still visible
			if (objTip.is(":visible") === true) {
				// trigger title update
				objTip.tipsy("hide");
				objTip.tipsy("show");
			} else {
				// remove the tipsy to fix the disappearance bug
				// https://github.com/CreativeDream/jquery.tipsy#bugs
				jQuery(".tipsy").remove();
			}
		});
	}

/**
	 * get all settings inputs
	 */
    var _objInputCache = new Map();
	function getObjInputs(controlsOnly, force = false) {
		validateInited();
	
		var selectors = "input, textarea, select, .unite-setting-inline-editor, .unite-setting-input-object";
		var selectorNot = "input[type='button'], input[type='range'], input[type='search'], .unite-responsive-picker, .unite-units-picker";
	
		if (g_temp.disableExcludeSelector !== true)
			selectorNot += ", .unite-settings-exclude *";
	
		if (g_temp.objItemsManager) {
			selectors += ", .uc-setting-items-panel";
			selectorNot += ", .uc-setting-items-panel select, .uc-setting-items-panel input, .uc-setting-items-panel textarea";
		}
	
		if (g_temp.isRepeaterExists === true)
			selectorNot += ", .unite-setting-repeater *";
	
		if (controlsOnly === true)
			selectors = "input[type='radio'], select";

		const cacheKey = `${selectors}-${selectorNot}`;
	
		if (!force && _objInputCache.has(cacheKey)) {
			return _objInputCache.get(cacheKey);
		}
		
		const objInputs = g_objParent.find(selectors).not(selectorNot);
	
		_objInputCache.set(cacheKey, objInputs);
		return objInputs;
		// return g_objParent.find(selectors).not(selectorNot);
	}


	/**
	 * get input name
	 */
	function getInputName(objInput){
		var name = objInput.attr("name");
		if(!name)
			name = objInput.data("name");

		return(name);
	}


	/**
	 * get input basic type
	 */
	function getInputBasicType(objInput){

		if(!objInput){
			console.trace();
			throw new Error("empty input, can't get basic type");
		}

		var type = objInput[0].type;
		if(!type)
			type = objInput.prop("tagName").toLowerCase();

		switch(type){
			case "select-one":
			case "select-multiple":
				type = "select";
			break;
		}

		return(type);
	}


	/**
	 * get input type
	 */
	function getInputType(objInput){

		if(!objInput){
			console.trace();
			throw new Error("empty input, can't get type");
		}

		if(!objInput || objInput.length == 0){
			console.trace();
			throw new Error("getInputType - objInput is empty");
		}

		if(!objInput[0]){
			trace("wrong input object");
			console.trace();
		}

		var type = objInput[0].type;
		if(!type)
			type = objInput.prop("tagName").toLowerCase();

		var customType = objInput.data("settingtype");

		switch(type){
			case "select-multiple":
			case "multiple_select":  // obj name fix
				type = "multiselect";

				if(objInput.hasClass("select2"))
					type = "select2";

				if(customType)
					type = customType;
			break;
			case "select-one":
				type = "select";

				if(customType)
					type = customType;

				if(objInput.hasClass("select2"))
					type = "select2";
			break;
			case "text":
				if(objInput.hasClass("unite-color-picker"))
					type = "color";
				else if (objInput.hasClass("unite-setting-mp3-input"))
					type = "mp3";
				else if (objInput.hasClass("unite-postpicker-input"))
					type = "post";
				else if (objInput.hasClass("unite-setting-link"))
					type = "link";
			break;
			case "textarea":
				if(objInput.hasClass("mce_editable") || objInput.hasClass("wp-editor-area"))
					type = "editor_tinymce";
			break;
			case "hidden":
				if (objInput.hasClass("unite-iconpicker-input"))
					type = "icon";
			break;
			case "span":
			case "div":
				type = customType;

				if (!type) {
					if (objInput.hasClass("uc-setting-items-panel"))
						type = "items";
					else if (objInput.hasClass("uc-setting-fonts-panel"))
						type = "fonts";
					else if (objInput.hasClass("unite-setting-inline-editor"))
						type = "editor_tinymce";
				}
			break;
		}

		return(type);
	}


	/**
	 * get input value
	 */
	function getSettingInputValue(objInput){

		var name = getInputName(objInput);
		var type = getInputType(objInput);
		var value = objInput.val();
		var id = objInput.prop("id");

		if(!name)
			return(g_vars.NOT_UPDATE_OPTION);

		var flagUpdate = true;

		switch(type){
			case "hidden":		//allow to pass objects
				var hiddenValue = objInput.data("input_value");
				if(hiddenValue)
					value = hiddenValue;
			break;
			case "checkbox":
				value = objInput.prop("checked");
			break;
			case "radio":
				if(objInput.prop("checked") === false)
					flagUpdate = false;
			break;
			case "button":
				flagUpdate = false;
			break;
			case "editor_tinymce":
				if (typeof window.tinyMCE !== "undefined") {
					var objEditor = window.tinyMCE.EditorManager.get(id);

					if (objEditor)
						value = objEditor.getContent();
				}
			break;
			case "mp3":
				var source = objInput.data("source");

				//convert to relative url if not addon
				if(source !== "addon" && jQuery.isNumeric(value) === false)
					value = g_ucAdmin.urlToRelative(value);
			break;
			case "items":
				value = g_temp.objItemsManager.getItemsData();
			break;
			case "map":
				value = objInput.data("mapdata");
			break;
			case "repeater":
				value = getRepeaterValues(objInput);
			break;
			case "multiselect":
				value = multiSelectModifyAfterGet(value);
			break;
			case "dimentions":
				value = getDimentionsValue(objInput);
			break;
			case "image":
				value = getImageInputValue(objInput);
			break;
			case "range":
				value = getRangeSliderValue(objInput);
			break;
			case "switcher":
				value = getSwitcherValue(objInput);
			break;
			case "tabs":
				flagUpdate = false;
			break;
			case "typography":
			case "textshadow":
			case "textstroke":
			case "boxshadow":
			case "css_filters":
				value = getSubSettingsValue(objInput);
			break;
			case "gallery":
				value = getGalleryValues(objInput);
			break;
			case "buttons_group":
				value = getButtonsGroupValue(objInput);
			break;
			case "icon":
				value = getIconInputData(objInput);
			break;
			case "link":
				value = getLinkInputValue(objInput);
			break;
			case "post":
				value = getPostPickerValue(objInput);
			break;
			default:
				//custom settings
				var objCustomType = getCustomSettingType(type);
				if(objCustomType){
					if(objCustomType.funcGetValue)
						value = objCustomType.funcGetValue(objInput, t);
					else
						value = "";
				}
			break;
		}

		if(flagUpdate == false)
			return(g_vars.NOT_UPDATE_OPTION);

		return(value);
	}


	/**
	 * get settings values object by the parent
	 */
	this.getSettingsValues = function (controlsOnly, isChangedOnly) {
		validateInited();

		var objValues = {};
		var objInputs;

		if (controlsOnly === true)
			objInputs = getObjInputs(controlsOnly);
		else
			objInputs = getObjInputs().not(".unite-setting-transparent");

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);

			// skip hidden/disabled setting
			if (objInput.closest(".unite-setting-row").hasClass("unite-setting-hidden") === true)
				return;

			var name = getInputName(objInput);
			var type = getInputType(objInput);
			var value = getSettingInputValue(objInput);

			if (value === g_vars.NOT_UPDATE_OPTION)
				return;

			// remain only changed values from default values
			if (isChangedOnly === true) {
				var defaultValue = getInputDefaultValue(objInput);

				if (defaultValue === value)
					return;
			}

			// set additional vars
			switch (type) {
				case "checkbox":
					value = objInput.prop("checked");
				break;
			}

			objValues[name] = value;
		});

		return objValues;
	};

	/**
	 * get default value
	 */
	function getInputDefaultValue(objInput){

		var type = getInputType(objInput);
		var name = getInputName(objInput);

		var dataname = "default";
		var checkboxDataName = "defaultchecked";

		var defaultValue;

		switch(type){
			case "checkbox":
			case "radio":
				defaultValue = objInput.data(checkboxDataName);
				defaultValue = g_ucAdmin.strToBool(defaultValue);
			break;
			default:
				if(!name)
					return(false);

				defaultValue = objInput.data(dataname);

				if(typeof defaultValue == "object")
					defaultValue = JSON.stringify(defaultValue);

				if(type === "select"){
					if(defaultValue === true)
						defaultValue = "true";
					if(defaultValue === false)
						defaultValue = "false";
				}
			break;
		}

		if(jQuery.isNumeric(defaultValue))
			defaultValue = defaultValue.toString();

		return(defaultValue);
	}


	/**
	 * clear input
	 */
	function clearInput(objInput, dataname, checkboxDataName, skipControl){

		var name = getInputName(objInput);
		var type = getInputType(objInput);
		var id = objInput.prop("id");
		var defaultValue;

		if(!dataname)
			dataname = "default";

		if(!checkboxDataName)
			checkboxDataName = "defaultchecked";

		switch(type){
			case "select":
			case "select2":
			case "textarea":
			case "text":
			case "number":
			case "password":
				if (!name)
					return;

				defaultValue = objInput.data(dataname);

				if (typeof defaultValue === "object")
					defaultValue = JSON.stringify(defaultValue);

				var isSelect = (type === "select" || type === "select2");

				if (isSelect === true) {
					if (defaultValue === true)
						defaultValue = "true";
					else if (defaultValue === false)
						defaultValue = "false";
				}

				objInput.val(defaultValue);

				if (isSelect === true)
					objInput.trigger("change.select2");
			break;
			case "hidden":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
			break;
			case "icon":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
				objInput.trigger("input");
			break;
			case "dimentions":
				defaultValue = objInput.data(dataname);

				setDimentionsValue(objInput, defaultValue);
			break;
			case "range":
				defaultValue = objInput.data(dataname);

				setRangeSliderValue(objInput, defaultValue);
			break;
			case "switcher":
				defaultValue = objInput.data(dataname);

				setSwitcherValue(objInput, defaultValue);
			break;
			case "tabs":
				defaultValue = objInput.data(dataname);

				setTabsValue(objInput, defaultValue);
			break;
			case "typography":
			case "textshadow":
			case "textstroke":
			case "boxshadow":
			case "css_filters":
				defaultValue = objInput.data(dataname);

				setSubSettingsValue(objInput, defaultValue);
			break;
			case "image":
				defaultValue = objInput.data(dataname);

				setImageInputValue(objInput, defaultValue);
			break;
			case "color":

				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);

				if(g_colorPicker)
					g_colorPicker.linkTo(objInput);

				objInput.trigger("change");

				//clear manually
				if(defaultValue == "")
					objInput.attr("style","");

			break;
			case "checkbox":
			case "radio":
				defaultValue = objInput.data(checkboxDataName);
				defaultValue = g_ucAdmin.strToBool(defaultValue);

				objInput.prop("checked", defaultValue);
			break;
			case "editor_tinymce":
				var objEditorWrapper = objInput.parents(".unite-editor-setting-wrapper");

				defaultValue = objEditorWrapper.data(dataname);

				objInput.val(defaultValue);

				if (typeof window.tinyMCE !== "undefined") {
					var objEditor = window.tinyMCE.EditorManager.get(id);

					if (objEditor)
						objEditor.setContent(defaultValue);
				}
			break;
			case "addon":
			case "mp3":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
				objInput.trigger("change");
			break;
			case "link":
				defaultValue = objInput.data(dataname);

				setLinkInputValue(objInput, defaultValue);
			break;
			case "post":
				defaultValue = objInput.data(dataname);

				setPostPickerValue(objInput, defaultValue);
			break;
			case "post_ids":
				defaultValue = objInput.data(dataname);

				setPostIdsPickerValue(objInput, defaultValue);
			break;
			case "items":
				if(dataname != "initval")
					g_temp.objItemsManager.clearItemsPanel();
			break;
			case "repeater":
				setRepeaterValues(objInput, null, true);
			break;
			case "col_layout":
				//don't clear col layout
			break;
			case "multiselect":
				defaultValue = objInput.data(dataname);
				defaultValue = multiSelectModifyForSet(defaultValue);

				objInput.val(defaultValue);
			break;
			case "gallery":
				defaultValue = objInput.data(dataname);

				setGalleryValues(objInput, defaultValue);
			break;
			case "buttons_group":
				defaultValue = objInput.data(dataname);

				setButtonsGroupValue(objInput, defaultValue);
			break;
			case "group_selector":
			case "map":
				// no clear
			break;
			default:

				var objCustomType = getCustomSettingType(type);

				if(objCustomType){
					if(objCustomType.funcClearValue)
						objCustomType.funcClearValue(objInput);
				}
				else{

					var success = g_ucAdmin.clearProviderSetting(type, objInput, dataname);
					if(success == false){
						trace("for clear - wrong type: " + type);
						trace(objInput);
					}

				}
			break;
		}

		objInput.removeData("unite_setting_oldvalue");

		if(skipControl !== true)
			processControlSettingChange(objInput);

	}


	/**
	 * set input value
	 */
	function setInputValue(objInput, value, objValues){
		
		var name = getInputName(objInput);
		var type = getInputType(objInput);
		var id = objInput.prop("id");

		if(value == g_vars.NOT_UPDATE_OPTION)
			return(false);

		switch(type){
			case "select":
			case "select2":
			case "textarea":
			case "text":
			case "number":
			case "password":
				objInput.val(value);

				if (type === "select" || type === "select2")
					objInput.trigger("change.select2");
			break;
			case "hidden":
				if(typeof value == "object"){
					objInput.data("input_value", value);
				}
				else{
					objInput.val(value);
					objInput.data("input_value", null);
				}
			break;
			case "addon":
				objInput.val(value);
				objInput.trigger("change");
			break;
			case "color":
				objInput.val(value);

				if(g_colorPicker)
					g_colorPicker.linkTo(objInput);

				objInput.trigger("change");
			break;
			case "checkbox":
				value = g_ucAdmin.strToBool(value);

				objInput.prop("checked", value === true);
			break;
			case "radio":
				var radioValue = objInput.val();

				if(radioValue === "true" || radioValue === "false"){
					radioValue = g_ucAdmin.strToBool(radioValue);
					value = g_ucAdmin.strToBool(value);
				}

				objInput.prop("checked", radioValue === value);
			break;
			case "editor_tinymce":
				objInput.val(value);

				if (typeof window.tinyMCE !== "undefined") {
					var objEditor = window.tinyMCE.EditorManager.get(id);

					if (objEditor)
						objEditor.setContent(value);
				}
			break;
			case "mp3":
				objInput.val(value);
				objInput.trigger("change");
			break;
			case "dimentions":
				setDimentionsValue(objInput, value);
			break;
			case "range":
				setRangeSliderValue(objInput, value);
			break;
			case "switcher":
				setSwitcherValue(objInput, value);
			break;
			case "tabs":
				setTabsValue(objInput, value);
			break;
			case "typography":
			case "textshadow":
			case "textstroke":
			case "boxshadow":
			case "css_filters":
				setSubSettingsValue(objInput, value);
			break;
			case "icon":
				setIconInputValue(objInput, value);
			break;
			case "image":
								 
				if (jQuery.isPlainObject(value) === false) {
					if (jQuery.isNumeric(value) === true) {
						value = {
							id: value,
							url: g_ucAdmin.getVal(objValues, name + "_url")
						};
					} else {
						value = {
							id: g_ucAdmin.getVal(objValues, name + "_imageid"),
							url: value
						};
					}
				}
								
				setImageInputValue(objInput, value);
			break;
			case "link":
				setLinkInputValue(objInput, value);
			break;
			case "post":
				setPostPickerValue(objInput, value);
			break;
			case "post_ids":
				setPostIdsPickerValue(objInput, value);
			break;
			case "items":
				g_temp.objItemsManager.setItemsFromData(value);
			break;
			case "repeater":
				setRepeaterValues(objInput, value);
			break;
			case "multiselect":
				value = multiSelectModifyForSet(value);
				objInput.val(value);
			break;
			case "gallery":
				setGalleryValues(objInput, value);
			break;
			case "buttons_group":
				setButtonsGroupValue(objInput, value);
			break;
			case "group_selector":
			case "map":
				// no set
			break;
			default:
				var objCustomType = getCustomSettingType(type);
				if(objCustomType)
					if(objCustomType.funcSetValue)
						objCustomType.funcSetValue(objInput, value);
				else{
					//check for provider
					var success =  g_ucAdmin.providerSettingSetValue(type, objInput, value);

					//trace error
					if(success == false){
						trace(objInput);
						trace("for setvalue - wrong type: " + type);
					}
				}
			break;
		}

		processControlSettingChange(objInput);
	}

	/**
	 * clear settings
	 */
	this.clearSettings = function (dataname, checkboxDataName) {
		validateInited();

		t.disableTriggerChange();

		var objInputs = getObjInputs();

		jQuery.each(objInputs, function (index, input) {

			var objInput = jQuery(input);

			clearInput(objInput, dataname, checkboxDataName, true);
		});

		t.enableTriggerChange();
	};


	/**
	 * get field names by type
	 */
	this.getFieldNamesByType = function (type) {
		validateInited();

		var objInputs = getObjInputs();
		var arrFieldsNames = [];

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var inputName = getInputName(objInput);
			var inputType = getInputType(objInput);

			if (inputType === type)
				arrFieldsNames.push(inputName);
		});

		return arrFieldsNames;
	};


	/**
	 * clear settings
	 */
	this.clearSettingsInit = function(){

		validateInited();

		t.clearSettings("initval","initchecked");

	};


	/**
	 * set single setting value
	 */
	this.setSingleSettingValue = function(name, value){

		var objInput = t.getInputByName(name);

		if(!objInput || objInput.length == 0)
			return(false);

		t.disableTriggerChange();

		setInputValue(objInput, value);

		t.enableTriggerChange();

	};


	/**
	 * set values, clear first
	 */
	this.setValues = function (objValues) {
		
		validateInited();

		t.disableTriggerChange();

		t.clearSettings();

		var objInputs = getObjInputs();

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var name = getInputName(objInput);

			if (objValues.hasOwnProperty(name)) {
				var value = objValues[name];

				setInputValue(objInput, value, objValues);
			}
		});

		t.enableTriggerChange();
	};

	/**
	 * determine whether the change event trigger is disabled
	 */
	this.isTriggerChangeDisabled = function () {
		return g_temp.triggerChangeQueue > 0;
	};

	/**
	 * enable the change event trigger
	 */
	this.enableTriggerChange = function () {
		g_temp.triggerChangeQueue = Math.max(g_temp.triggerChangeQueue - 1, 0);
	};

	/**
	 * disable the change event trigger
	 */
	this.disableTriggerChange = function () {
		g_temp.triggerChangeQueue++;
	};

	/**
	 * switch the responsive type
	 */
	this.setResponsiveType = function (type) {
		var validTypes = getResponsiveTypes();

		if (jQuery.inArray(type, validTypes) === -1)
			throw new Error("Invalid responsive type: " + type);

		g_objWrapper.find(".unite-setting-row[data-responsive-type]")
			.addClass("unite-responsive-hidden")
			.filter("[data-responsive-type=\"" + type + "\"]")
			.removeClass("unite-responsive-hidden");

		g_objWrapper.find(".unite-responsive-picker")
			.val(type)
			.trigger("change.select2");
	};

	/**
	 * get a list of responsive types
	 */
	function getResponsiveTypes() {
		return ["desktop", "tablet", "mobile"];
	}


	function _________CUSTOM_SETTING_TYPES__________(){}

	/**
	 * get custom setting type, if empty setting name - returrn all types
	 */
	function getCustomSettingType(settingName){

		var objCustomSettings = g_ucAdmin.getGlobalData(g_temp.customSettingsKey);
		if(!objCustomSettings)
			objCustomSettings = {};

		if(!settingName)
			return(objCustomSettings);

		var objType = g_ucAdmin.getVal(objCustomSettings, settingName, null);

		return(objType);
	}


	function _______RANGE_SLIDER_____(){}

	/**
	 * init range slider
	 */
	function initRangeSlider(objWrapper, funcChange) {
		var objSlider = objWrapper.find(".unite-setting-range-slider");
		var objInput = objWrapper.find(".unite-setting-range-input");
		var funcChangeThrottled = g_ucAdmin.throttle(funcChange);

		objSlider.slider({
			min: objSlider.data("min"),
			max: objSlider.data("max"),
			step: objSlider.data("step"),
			value: objSlider.data("value"),
			range: "min",
			slide: function (event, ui) {
				objInput.val(ui.value);

				funcChangeThrottled(null, objWrapper);
			},
			change: function () {
				funcChange(null, objWrapper);
			}
		});

		objInput.on("input", function () {
			objSlider.slider("value", objInput.val());

			funcChange(null, objWrapper);
		});

		setUnitsPickerChangeHandler(objWrapper, function () {
			funcChange(null, objWrapper);
		});
	}

	/**
	 * destroy range sliders
	 */
	function destroyRangeSliders() {
		let slider = 
		g_objWrapper.find(".unite-setting-range-slider").each(function() {
			let slider = jQuery(this);
			if(slider.hasClass('ui-slider')){ 
				slider.slider("destroy");
			}
		});
		
		g_objWrapper.find(".unite-setting-range-input")?.off("input");
	}

	/**
	 * get range slider value
	 */
	function getRangeSliderValue(objWrapper) {
		var data = {};

		data["size"] = objWrapper.find(".unite-setting-range-input").val();
		data["unit"] = getUnitsPickerValue(objWrapper);

		return data;
	}

	/**
	 * set range slider value
	 */
	function setRangeSliderValue(objWrapper, value) {
		objWrapper.find(".unite-setting-range-input")
			.val(value["size"])
			.trigger("input");

		setUnitsPickerValue(objWrapper, value["unit"]);
	}


	function _______SELECT2_____(){}

	/**
	 * init select2
	 */
    function initSelect2(objInput, options) {
        if (!objInput || !objInput[0]) {
            console.error("Invalid objInput element:", objInput);
            return;
        }
    
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    observer.disconnect();
    
                    const dropdownParent = objInput.closest(".unite-setting-input, .unite-inputs").length 
                        ? objInput.closest(".unite-setting-input, .unite-inputs") 
                        : jQuery("body");
    
                    const settings = jQuery.extend({
                        dropdownParent,
                        closeOnSelect: true,
                        minimumResultsForSearch: 10,
                        templateResult: prepareTemplate,
                        templateSelection: prepareTemplate,
                    }, options);
    
                    objInput
                        .select2(settings)
                        .on("change", () => {
                            appendPlusButton();
                            t.onSettingChange(null, objInput);
                        })
                        .on("select2:closing", () => {
                            jQuery(".select2-dropdown").find(".uc-tip").trigger("mouseleave");
                        });
    
                    appendPlusButton();
    
                    function appendPlusButton() {
                        const objOptions = objInput.find("option");
                        const objSelectedOptions = objOptions.filter(":selected");
    
                        if (settings.ajax || objOptions.length === objSelectedOptions.length) return;
    
                        objInput
                            .next(".select2")
                            .find(".select2-search--inline")
                            .before('<li class="select2-selection__choice select2-selection__uc-plus-button">+</li>');
                    }
    
                    function prepareTemplate(data) {
                        const content = jQuery(data.element).data("content");
                        return content ? jQuery(content) : data.text;
                    }
                }
            });
        }, { threshold: 0.1 });
    
        observer.observe(objInput[0]);
    }

	function _______MULTI_SELECT_____(){}

	/**
	 * modify value for save, turn to array
	 */
	function multiSelectModifyForSet(value){

		if(typeof value != "string")
			return(value);

		value = value.split(",");

		return(value);
	}


	/**
	 * modify value after get
	 */
	function multiSelectModifyAfterGet(value){

		if(jQuery.isArray(value) == false)
			return(false);

		value = value.join(",");

		return(value);
	}


	function _______COLOR_PICKER_____(){}

	/**
	 * init color picker
	 */
	function initColorPicker(objInput, funcChange) {
		g_temp.colorPickerType = g_ucAdmin.getGeneralSetting("color_picker_type");

		switch (g_temp.colorPickerType) {
			case "farbtastic":
				initColorPicker_farbtastic(objInput, funcChange);
			break;
			case "spectrum":
				initColorPicker_spectrum(objInput, funcChange);
			break;
			default:
				throw new Error("Color picker type \"" + g_temp.colorPickerType + "\" is not implemented.");
		}
	}

	/**
	 * init color picker - farbtastic
	 */
	function initColorPicker_farbtastic(objInput, funcChange) {
		g_colorPickerWrapper = jQuery("#divColorPicker");

		if (g_colorPickerWrapper.length === 0) {
			jQuery("body").append("<div id=\"divColorPicker\" style=\"display:none;\"></div>");

			g_colorPickerWrapper = jQuery("#divColorPicker");
		}

		var isInited = g_colorPickerWrapper.data("inited");

		if (isInited !== true) {
			g_colorPickerWrapper.on("click", function () {
				return false;	// prevent hiding
			});

			jQuery(document).on("click", function () {
				g_colorPickerWrapper.hide();
			});

			g_colorPickerWrapper.data("inited", true);
		}

		if (!g_colorPicker) {
			g_colorPicker = jQuery.unite_farbtastic("#divColorPicker", null, function () {
				funcChange(null, objInput);
			});

			objInput.on("click", function () {
				return false;	// prevent hiding
			});

			objInput.on("focus", function () {
				g_colorPicker.linkTo(objInput);
				g_colorPickerWrapper.show();

				var bodyWidth = jQuery("body").width();
				var wrapperWidth = g_colorPickerWrapper.width();
				var wrapperHeight = g_colorPickerWrapper.height();
				var inputWidth = objInput.width();
				var inputHeight = objInput.height();
				var inputOffset = objInput.offset();
				var posTop = inputOffset.top - wrapperHeight - inputHeight + 10;
				var posLeft = inputOffset.left - wrapperWidth / 2 + inputWidth / 2;
				var posRight = posLeft + wrapperWidth;

				if (posTop < 0)
					posTop = 0;

				if (posLeft < 0)
					posLeft = 0;

				if (posRight > bodyWidth)
					posLeft = bodyWidth - wrapperWidth;

				g_colorPickerWrapper.css({
					"left": posLeft,
					"top": posTop
				});
			});
		}
	}

	/**
	 * init color picker - spectrum
	 */
	function initColorPicker_spectrum(objInput, funcChange) {
		var funcChangeThrottled = g_ucAdmin.throttle(function () {
			// wait for the next tick to get the updated input value
			setTimeout(function () {
				funcChange(null, objInput);
			});
		});

		objInput.spectrum({
			move: funcChangeThrottled,
		});
	}

	/**
	 * destroy color pickers
	 */
	function destroyColorPickers() {
		g_temp.colorPickerType = g_ucAdmin.getGeneralSetting("color_picker_type");

		switch (g_temp.colorPickerType) {
			case "farbtastic":
				destroyColorPickers_farbtastic();
			break;
			case "spectrum":
				destroyColorPickers_spectrum();
			break;
			default:
				throw new Error("Color picker type \"" + g_temp.colorPickerType + "\" is not implemented.");
		}
	}

	/**
	 * destroy color pickers - farbtastic
	 */
	function destroyColorPickers_farbtastic() {
		g_objWrapper.find(".unite-color-picker").off("click").off("focus");

		g_colorPickerWrapper.remove();
		g_colorPicker.remove();
	}

	/**
	 * destroy color pickers - spectrum
	 */
	function destroyColorPickers_spectrum() {
		g_objWrapper.find(".unite-color-picker").spectrum("destroy");
	}


	function _______MP3_SETTING_____(){}

	/**
	 * update image url base
	 */
	this.updateMp3FieldState = function(objInput, isEnable){

		var objButton = objInput.siblings(".unite-button-choose");
		var objError = objInput.siblings(".unite-setting-mp3-error");

		objInput.trigger("change");

		if(!isEnable){				//set disabled mode

			if(objError.length)
				objError.show();

			g_ucAdmin.disableInput(objInput);
			g_ucAdmin.disableButton(objButton);

		}else{						//set enabled mode

			if(objError.length)
				objError.hide();

			g_ucAdmin.enableInput(objInput);
			g_ucAdmin.enableButton(objButton);
		}


	};


	/**
	 * on change image click - change the image
	 */
	function onChooseMp3Click(){
		var objButton = jQuery(this);

		if(g_ucAdmin.isButtonEnabled(objButton) == false)
			return(true);

		var objInput = objButton.siblings(".unite-setting-mp3-input");
		var source = objInput.data("source");

		g_ucAdmin.openAddMp3Dialog(g_uctext.choose_audio,function(urlMp3){

			if(source == "addon"){		//in that case the url is an object
				var inputValue = urlMp3.url_assets_relative;
				var fullUrl = urlMp3.full_url;
				objInput.data("url", fullUrl);

				setInputValue(objInput, inputValue);
			}else{
				setInputValue(objInput, urlMp3);
			}

			objInput.trigger("change");

		},false, source);

	}

	function _______IMAGE_SETTING_____(){}
	
	/**
	 * update image url base
	 */
	this.updateImageFieldState = function (objWrapper, urlBase) {
		var objError = objWrapper.find(".unite-setting-image-error");
		var objUrl = objWrapper.find(".unite-setting-image-url");
		var enabled = !!urlBase;

		objWrapper.toggleClass("unite-disabled", enabled === false);
		objUrl.prop("disabled", enabled === false).trigger("input");
		objError.toggle(enabled === false);
	};

	/**
	 * on update assets path (update url base for all image addon inputs)
	 */
	function onUpdateAssetsPath(event, urlBase) {
		validateInited();

		var objInputs = getObjInputs();

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var type = getInputType(objInput);

			if (type !== "image" || type !== "mp3")
				return;

			var source = objInput.data("source");

			if (source === "addon")
				t.updateImageFieldState(objInput, urlBase);
		});
	}
	
	
	/**
	 * init image chooser
	 */
	t.initImageChooser = function (objWrapper, funcChange) {
		
		var source = objWrapper.data("source");
		
		var objPreview = objWrapper.find(".unite-setting-image-preview");
		var objChooseButton = objWrapper.find(".unite-setting-image-choose");
		var objClearButton = objWrapper.find(".unite-setting-image-clear");
		var objUrl = objWrapper.find(".unite-setting-image-url");
		var objSize = objWrapper.find(".unite-setting-image-size");
		
		if(source == "addon")
			objSize = null;
			
		// make compatible with widget params dialog
		if (typeof funcChange !== "function") {
			funcChange = function (){
				objUrl.trigger("input");
			};
		}
		
		objUrl.on("change",function(){
			objUrl.trigger("input");
		});
		
		objUrl.on("input", function (event) {
						
			var url = objUrl.val();

			if (url === "")
				objPreview.removeAttr("style");
			else
				objPreview.css("background-image", "url('" + url + "')");

			// reset image id if the input has been changed manually
			if (event.originalEvent)
				objWrapper.data("image-id", null);

			var imageId = objWrapper.data("image-id");

			objUrl.closest(".unite-setting-image-section").toggleClass("unite-hidden", !!imageId);
			
			if(objSize)
				objSize.closest(".unite-setting-image-section").toggleClass("unite-hidden", !imageId);
			
		});
		
		
		if(objSize){
			
			objSize.on("change", function () {
				
				objSize.prop("disabled", true);
				
				const data = getImageInputValue(objWrapper);
				
				g_ucAdmin.ajaxRequest("get_image_url", data, function (response) {
					data.url = response.url;

					setImageInputValue(objWrapper, data);
					
					funcChange(null, objWrapper);
				}).always(function () {
					objSize.prop("disabled", false);
				});
			});
			
		}

		objChooseButton.on("click", function (event) {
			
			event.stopPropagation();

			var source = objWrapper.data("source");

			g_ucAdmin.openAddImageDialog(g_uctext.choose_image, function (imageUrl, imageId) {
								
				if (source === "addon")
					setImageInputValue(objWrapper, imageUrl.url_assets_relative);
				else
					setImageInputValue(objWrapper, { id: imageId, url: imageUrl });

				funcChange(null, objWrapper);
				
			}, false, source);
		});

		objClearButton.on("click", function (event) {
			event.stopPropagation();

			setImageInputValue(objWrapper, "");

			funcChange(null, objWrapper);
		});

		objPreview.on("click", function () {
			objChooseButton.trigger("click");
		});
	}

	/**
	 * destroy image chooser
	 */
	function destroyImageChoosers() {
		g_objWrapper.find(".unite-setting-image-preview").off("click");
		g_objWrapper.find(".unite-setting-image-choose").off("click");
		g_objWrapper.find(".unite-setting-image-clear").off("click");
		g_objWrapper.find(".unite-setting-image-url").off("input");
		g_objWrapper.find(".unite-setting-image-size").off("change");
	}

	/**
	 * get image input value
	 */
	function getImageInputValue(objWrapper) {
		var id = objWrapper.data("image-id");
		var url = objWrapper.find(".unite-setting-image-url").val();
		var size = objWrapper.find(".unite-setting-image-size").val();

		var data = {
			id: id,
			url: g_ucAdmin.urlToRelative(url),
			size: size
		};

		return data;
	}

	/**
	 * set image input value
	 */
	function setImageInputValue(objWrapper, value) {
		
		if (typeof value === "string")
			value = { url: value };

		value.id = value.id || null;
		value.url = g_ucAdmin.urlToFull(value.url);
		value.size = value.size || "full";

		objWrapper.data("image-id", value.id);
		objWrapper.find(".unite-setting-image-url").val(value.url).trigger("input");
		objWrapper.find(".unite-setting-image-size").val(value.size);
	}
	
	

	function _______GALLERY_____(){}

	/**
	 * init gallery
	 */
	function initGallery(objWrapper, funcChange) {
		var handleChange = function (values) {
			setGalleryValues(objWrapper, values);

			funcChange(null, objWrapper);
		};

		objWrapper.find(".unite-setting-gallery-add").on("click", function () {
			var values = getGalleryValues(objWrapper);

			openGalleryFrame(values.length > 0 ? "edit" : "add", values, handleChange);
		});

		objWrapper.find(".unite-setting-gallery-edit").on("click", function () {
			var values = getGalleryValues(objWrapper);

			openGalleryFrame("edit", values, handleChange);
		});

		objWrapper.find(".unite-setting-gallery-clear").on("click", function () {
			var text = jQuery(this).data("text");
			var confirmed = confirm(text);

			if (confirmed)
				handleChange([]);
		});
	}

	/**
	 * destroy galleries
	 */
	function destroyGalleries() {
		g_objWrapper.find(".unite-setting-gallery-add").off("click");
		g_objWrapper.find(".unite-setting-gallery-edit").off("click");
		g_objWrapper.find(".unite-setting-gallery-clear").off("click");
	}

	/**
	 * open gallery frame
	 */
	function openGalleryFrame(action, images, onChange) {
		var states = {
			add: "gallery-library",
			edit: "gallery-edit"
		};

		var options = {
			frame: "post",
			state: states[action],
			multiple: true
		};

		if (images.length > 0) {
			var ids = images.map(function (image) {
				return image.id;
			});

			var attachments = wp.media.query({
				type: "image",
				perPage: -1,
				post__in: ids,
				orderby: "post__in",
				order: "ASC"
			});

			options.selection = new wp.media.model.Selection(attachments.models, {
				props: attachments.props.toJSON(),
				multiple: true
			});
		}

		var frame = wp.media(options);

		frame.on("update", function (selection) {
			var images = [];

			selection.each(function (image) {
				images.push({
					id: image.get("id"),
					url: g_ucAdmin.urlToRelative(image.get("url")),
				});
			});

			onChange(images);
		});

		frame.on("content:render:browse", function (browser) {
			browser.sidebar.on("ready", function () {
				browser.sidebar.unset("gallery");
			});
		});

		frame.open();
	}

	/**
	 * render gallery view
	 */
	function renderGalleryView(objWrapper) {
		var objHeader = objWrapper.find(".unite-setting-gallery-header");
		var objItems = objWrapper.find(".unite-setting-gallery-items");
		var objEmpty = objWrapper.find(".unite-setting-gallery-empty");
		var values = getGalleryValues(objWrapper);
		var count = values.length;

		objEmpty.toggle(count === 0);

		var title = objHeader.data("text-default");

		if (count === 0)
			title = objHeader.data("text-none");
		else if (count === 1)
			title = objHeader.data("text-one");

		objHeader.text(title.replace("%d", count));

		if(!Array.isArray(values)) {
			values = JSON.parse(values);
		}
		var html = values
			.slice(0, 7) // max 7 images
			.map(function (image) {
				return '<div class="unite-setting-gallery-item">'
					+ ' <div class="unite-setting-gallery-image">'
					+ '  <img src="' + g_ucAdmin.urlToFull(image.url) + '" alt="' + image.id + '" />'
					+ ' </div>'
					+ '</div>';
			})
			.join("");

		objItems
			.find(".unite-setting-gallery-item:not(:last)")
			.remove()
			.end()
			.prepend(html);
	}

	/**
	 * get gallery values
	 */
	function getGalleryValues(objWrapper) {
		return objWrapper.data("values") || [];
	}

	/**
	 * set gallery values
	 */
	function setGalleryValues(objWrapper, values) {
		objWrapper.data("values", values || []);

		renderGalleryView(objWrapper);
	}


	function _______BUTTONS_GROUP_____(){}

	/**
	 * init buttons group
	 */
	function initButtonsGroup(objWrapper, funcChange) {
		objWrapper.find(".unite-setting-button").on("click", function () {
			var value = jQuery(this).data("value");

			setButtonsGroupValue(objWrapper, value);

			funcChange(null, objWrapper);
		});
	}

	/**
	 * destroy buttons groups
	 */
	function destroyButtonsGroups() {
		g_objWrapper.find(".unite-setting-button").off("click");
	}

	/**
	 * get buttons group value
	 */
	function getButtonsGroupValue(objWrapper) {
		return objWrapper.data("value") || null;
	}

	/**
	 * set buttons group value
	 */
	function setButtonsGroupValue(objWrapper, value) {
		value = value || null;

		var deselectable = objWrapper.data("deselectable");
		var selectedValue = objWrapper.find(".unite-setting-button.unite-active").data("value");

		if (deselectable && value === selectedValue)
			value = null;

		objWrapper
			.find(".unite-setting-button")
			.removeClass("unite-active")
			.filter("[data-value=\"" + value + "\"]")
			.addClass("unite-active");

		objWrapper.data("value", value);
	}


	function _______SAPS_____(){}

	/**
	 * get all sap tabs
	 */
	function getAllSapTabs(){

		var objTabs = g_objSapTabs.children("a");

		return(objTabs);
	}


	/**
	 * show sap elmeents
	 */
	function showSapInlineElements(numSap){

		var elementClass = ".unite-sap-" + numSap;
		var objElements = g_objParent.find(".unite-sap-element");

		if(objElements.length == 0)
			return(false);

		var objSapElements = g_objParent.find(elementClass);

		objElements.not(objSapElements).addClass("unite-setting-hidden");

		objSapElements.removeClass("unite-setting-hidden");
	}


	/**
	 * on sap tab click
	 */
	function onSapTabClick(){

		var classSelected = "unite-tab-selected";

		var objTab = jQuery(this);

		if(objTab.hasClass(classSelected))
			return(false);

		var allTabs = getAllSapTabs();

		allTabs.not(objTab).removeClass(classSelected);

		objTab.addClass(classSelected);

		var sapNum = objTab.data("sapnum");

		showSapInlineElements(sapNum);

	}

	/**
	 * init saps tabs
	 */
	function initSapsTabs(){
		
		if(!g_objWrapper){
			g_objSapTabs = null;
			return(false);
		}

		g_objSapTabs = g_objWrapper.find(".unite-settings-tabs");

		if(g_objSapTabs.length == 0){

			g_objSapTabs = null;
			return(false);
		}

		g_objSapTabs.children("a").on("click",onSapTabClick);
	}



	/**
	 * init saps accordion type
	 */
	function initSapsAccordion(){
		
		var objTabs = g_objWrapper.children(".unite-settings-accordion-saps-tabs").children(".unite-settings-tab");
		var objAccordions = g_objWrapper.children(".unite-postbox:not(.unite-no-accordion)");
		var objAccordionTitles = objAccordions.children(".unite-postbox-title");

		objTabs.on("click", function () {
						
			var objTab = jQuery(this);
						
			var objRoot = objTab.closest(".unite-settings-accordion-saps-tabs");
			var id = objTab.data("id");

			objRoot.find(".unite-settings-tab").removeClass("unite-active");
			objTab.addClass("unite-active");
			
			objAccordions.hide();
			
			var objContents = objAccordions.filter("[data-tab='" + id + "']");
			objContents.show();
			
			if (objContents.filter(".unite-active").length === 0)
				objContents.filter(":first").find(".unite-postbox-title").trigger("click");
		});

		objAccordionTitles.on("click", function () {
			
			var objRoot = jQuery(this).closest(".unite-postbox");
			var tab = objRoot.data("tab");

			objAccordions.filter("[data-tab='" + tab + "']")
				.not(objRoot)
				.removeClass("unite-active")
				.find(".unite-postbox-inside")
				.stop()
				.slideUp(g_vars.animationDuration);

			objRoot
				.toggleClass("unite-active")
				.find(".unite-postbox-inside")
				.stop()
				.slideToggle(g_vars.animationDuration);
		});

		if (objTabs.length > 0) {
			objTabs.filter(":first").trigger("click");
		}
		
		else {
			objAccordions.show();

			if (objAccordionTitles.length > 0)
				objAccordionTitles.filter(":first").trigger("click");
			else
				objAccordions.filter(":first").find(".unite-postbox-inside").show();
		}
	}


	/**
	 * init saps
	 */
	function initSaps(){

		if(g_options.show_saps == false)
			return(false);

		if(!g_objWrapper)
			return(false);

		switch(g_options.saps_type){
			case "saps_type_inline":
				initSapsTabs();
			break;
			case "saps_type_accordion":
				initSapsAccordion();
			break;
			default:
				throw new Error("Init saps error: wrong saps type: " + g_options.saps_type);
			break;
		}

	}

	function ______ADDON_PICKER____(){}


	/**
	 * get addons browser object
	 */
	this.getObjAddonBrowser = function(addonType){

		var keyCache = "uc_obj_addons_browsers";
		var objAddonBrowsersCache = g_ucAdmin.getGlobalData(keyCache);
		if(!objAddonBrowsersCache)
			objAddonBrowsersCache = {};

		var objBrowser = g_ucAdmin.getVal(objAddonBrowsersCache, addonType);

		//init browser if not inited yet
		if(!objBrowser){

			var browserID = "uc_addon_browser_"+addonType;

			var objBrowserWrapper = jQuery("#" + browserID);
			g_ucAdmin.validateDomElement(objBrowserWrapper,"addons browser with id: "+browserID);

			var objBrowser = new UniteCreatorBrowser();
			objBrowser.init(objBrowserWrapper);

			objAddonBrowsersCache[addonType] = objBrowser;
			g_ucAdmin.storeGlobalData(keyCache, objAddonBrowsersCache);
		}

		return(objBrowser);
	};


	/**
	 * init addon picker
	 */
	function initAddonPicker(objInput){

		var addonType = objInput.data("addontype");
		var objSelectButton = objInput.siblings(".unite-addonpicker-button");
		var objWrapper = objInput.parents(".unite-settings-addonpicker-wrapper");
		var objButtons = objWrapper.find(".uc-action-button");
		var objTitle = objInput.siblings(".unite-addonpicker-title");
		var objBrowser = t.getObjAddonBrowser(addonType);
		var settingName = objInput.prop("name");
		var settingDataName = settingName + "_data";
		var objAddonConfig = new UniteCreatorAddonConfig();
		var settingDataName = settingName + "_data";
		var objInputData = t.getInputByName(settingDataName);


		objInput.change(function(){

			var addonname = objInput.val();

			//set empty
			if(!addonname){
				objWrapper.addClass("unite-empty-content");
				objSelectButton.css("background-image", "none");
				if(objTitle.length)
					objTitle.html("");

				return(true);
			}

			var objData = objBrowser.getAddonData(addonname);

			if(!objData)
				return(true);

			if(objData.bgimage)
				objSelectButton.css("background-image", objData.bgimage);

			objWrapper.removeClass("unite-empty-content");

			if(objTitle.length){
				var title = g_ucAdmin.getVal(objData, "title");
				if(!title)
					title = addonname;

				objTitle.show().html(title);
			}

			//update data
			if(objInputData){

				var addonData = objAddonConfig.getGridAddonDataFromBrowserData(objData);
				setInputValue(objInputData, addonData);

			}

		});


		//---- select button

		objSelectButton.on("click",function(event){

			event.stopPropagation();
			event.stopImmediatePropagation();

			objBrowser.openAddonsBrowser(null, function(objData){

				var addonName = objData.name;

				//clear
				if(!addonName){

					objInput.val("");

					objWrapper.addClass("unite-empty-content");
					objSelectButton.css("background-image", "");

					if(objTitle.length)
						objTitle.html("");

				}else{

					objInput.val(addonName);

					if(objData.bgimage)
						objSelectButton.css("background-image", objData.bgimage);

					objWrapper.removeClass("unite-empty-content");

					if(objTitle.length)
						objTitle.html(objData.title);

					//update data
					if(objInputData){
						var addonData = objAddonConfig.getGridAddonDataFromBrowserData(objData);
						setInputValue(objInputData, addonData);
					}

				}

				t.onSettingChange(null, objInput);
			}, objSelectButton);
		});

		// ------------ action buttons

		objButtons.on("click",function(){
			var objButton = jQuery(this);
			var action = objButton.data("action");

			switch(action){
				case "clear":
					objInput.val("");
					objInput.trigger("change");
				break;
				case "configure":

					var configureAction = objButton.data("configureaction");
					g_ucAdmin.validateNotEmpty(configureAction, "configure action");

					g_ucAdmin.validateDomElement(objInputData, "addon picker input data");
					var addonData = getSettingInputValue(objInputData);

					if(!addonData){
						var addonName = objInput.val();
						addonData = objAddonConfig.getEmptyAddonData(addonName, addonType);
					}

					//open the panel
					var sendData = objAddonConfig.getSendDataFromAddonData(addonData);
					var panelTitle = objAddonConfig.getAddonTitle(addonData);
					var panelData = objAddonConfig.getPanelData(addonData);

					var options = {
							pane_name: "addon-settings",
							send_data: sendData,
							panel_title: panelTitle,
							panel_data: panelData,
							setting_name: settingDataName,
							changing_setting_name: settingName,
							addon_data: addonData
					};


					triggerEvent(t.events.OPEN_CHILD_PANEL, options);
				break;
			}

		});

	}


	function ______ICON_PICKER____(){}

	/**
	 * init icon picker
	 */
	function initIconPicker(objInput, funcChange) {
		var iconsType = objInput.data("icons_type");

		if (!iconsType)
			iconsType = "fa";

		var objDialogWrapper = iconPicker_initDialog(iconsType);

		if (!objDialogWrapper || objDialogWrapper.length === 0) {
			trace("Icon picker dialog not initialized.");
			return;
		}

		var objPickerWrapper = objInput.closest(".unite-iconpicker");
		var objPickerInput = objPickerWrapper.find(".unite-iconpicker-input");
		var objPickerError = objPickerWrapper.find(".unite-iconpicker-error");
		var objPickerButton = objPickerWrapper.find(".unite-iconpicker-button");
		var objPickerButtonNone = objPickerButton.filter("[data-action='none']");
		var objPickerButtonUpload = objPickerButton.filter("[data-action='upload']");
		var objPickerButtonLibrary = objPickerButton.filter("[data-action='library']");
		var objPickerUploadedIcon = objPickerWrapper.find(".unite-iconpicker-uploaded-icon");
		var objPickerLibraryIcon = objPickerWrapper.find(".unite-iconpicker-library-icon");
		var pickerErrorTimeout = -1;

		objPickerButtonNone.on("click", function () {
			objPickerInput.val(null).trigger("input");
		});

		objPickerButtonUpload.on("click", function () {
			g_ucAdmin.openAddImageDialog(g_uctext.choose_icon, function (imageUrl, imageId) {
				var fileName = imageUrl.split("/").pop();
				var fileExtension = fileName.split(".").pop();

				if (fileExtension !== "svg") {
					clearTimeout(pickerErrorTimeout);

					objPickerError.html("Icon must be of type SVG.").show();

					pickerErrorTimeout = setTimeout(function () {
						objPickerError.hide();
					}, 5000);

					return;
				}

				objPickerError.hide();

				objPickerInput
					.data("image-id", imageId)
					.val(imageUrl)
					.trigger("input");
			}, false);
		});

		objPickerButtonLibrary.on("click", function () {
			if (objDialogWrapper.dialog("isOpen")) {
				objDialogWrapper.dialog("close");
			} else {
				// set selected icon
				var iconName = objPickerInput.data("icon-name");

				objDialogWrapper
					.find(".unite-iconpicker-dialog-icon")
					.removeClass("icon-selected")
					.filter("[data-name='" + iconName + "']")
					.addClass("icon-selected");

				objDialogWrapper
					.data("objpicker", objPickerWrapper)
					.dialog("open");
			}
		});

		objPickerInput.on("input", function (event) {
			var value = objPickerInput.val().trim();

			// trigger settings change
			funcChange(event, objPickerInput);

			// deactivate buttons
			objPickerButton.removeClass("unite-active");

			// check for uploaded icon
			var isUpload = value.indexOf(".svg") > -1;

			if (isUpload === true){
				objPickerButtonUpload.addClass("unite-active");
				objPickerUploadedIcon.attr("src", value);

				return;
			}

			// check for library icon
			var icon = value;

			if (iconsType === "fa")
				icon = value.replace("fa fa-", "");

			var iconHash = icon + "_" + iconsType;

			if (g_iconsHash[iconHash]) {
				objPickerButtonLibrary.addClass("unite-active");

				var objType = iconPicker_getObjIconsType(iconsType);
				var iconHtml = iconPicker_getIconHtmlFromTemplate(objType.template, icon);

				objPickerLibraryIcon.html(iconHtml);

				return;
			}

			// fallback to the "none"
			objPickerButtonNone.addClass("unite-active");
		});

		objPickerInput.trigger("input");
	}

	/**
	 * init icon picker dialog
	 */
	function iconPicker_initDialog(type) {
		
		if (!type)
			type = "fa";

		var dialogID = "unite_icon_picker_dialog_" + type;

		var objDialogWrapper = jQuery("#" + dialogID);

		if (objDialogWrapper.length !== 0) {
			g_iconsHash = jQuery("body").data("uc_icons_hash");

			return objDialogWrapper;
		}
		
		if (type === "elementor" && g_ucFaIcons.length === 0)
			type = "fa";

		if (type === "fa") {
			iconPicker_addIconsType(type, g_ucFaIcons, function (icon) {
				if (icon.indexOf("fa-") === -1)
					icon = "fa fa-" + icon;

				var html = "<i class=\"" + icon + "\"></i>";

				return html;
			});
		} else if (type === "elementor") {
			iconPicker_addIconsType(type, g_ucElIcons, function (icon) {
				var html = "<i class=\"" + icon + "\"></i>";

				return html;
			});
		}

		var objType = iconPicker_getObjIconsType(type);
		// var isAddNew = g_ucAdmin.getVal(objType, "add_new");

		var htmlDialog = "<div id=\"" + dialogID + "\" class=\"unite-iconpicker-dialog unite-inputs unite-picker-type-" + type + "\" style=\"display:none\">";
		htmlDialog += "<div class=\"unite-iconpicker-dialog-top\">";
		htmlDialog += "<input class=\"unite-iconpicker-dialog-input-filter\" type=\"text\" placeholder=\"Type to filter\" value=\"\">";
		htmlDialog += "<span class=\"unite-iconpicker-dialog-icon-name\"></span>";

		// add new functionality
		// if (isAddNew === true) {
		// 	htmlDialog += "<a class=\"unite-button-secondary unite-iconpicker-dialog-button-addnew\">Add New Shape</a>";
		// }

		htmlDialog += "</div>";
		htmlDialog += "<div class=\"unite-iconpicker-dialog-icons-container\"></div></div>";

		jQuery("body").append(htmlDialog);

		objDialogWrapper = jQuery("#" + dialogID);

		var objContainer = objDialogWrapper.find(".unite-iconpicker-dialog-icons-container");
		var objFilter = objDialogWrapper.find(".unite-iconpicker-dialog-input-filter");
		var objIconName = objDialogWrapper.find(".unite-iconpicker-dialog-icon-name");

		// add icons
		var arrIcons = objType.icons;
		var isArray = jQuery.isArray(arrIcons);

		jQuery.each(arrIcons, function (index, icon) {
			var iconTitle = null;

			if (isArray === false) {
				iconTitle = icon;
				icon = index;
			}

			var iconHtml = iconPicker_getIconHtmlFromTemplate(objType.template, icon);
			var objIcon = jQuery("<span class=\"unite-iconpicker-dialog-icon\">" + iconHtml + "</span>");

			var iconName = icon;

			if (objType && typeof objType.getIconName === "function")
				iconName = objType.getIconName(icon);

			var iconHash = icon + "_" + type;

			if (g_iconsHash.hasOwnProperty(iconHash) === false) {
				objIcon.attr("data-name", iconName)
				objIcon.data("title", iconTitle);
				objIcon.data("name", iconName);
				objIcon.data("value", icon);

				objContainer.append(objIcon);

				g_iconsHash[iconHash] = objIcon;
			}
		});

		jQuery("body").data("uc_icons_hash", g_iconsHash);

		var dialogTitle = "Choose Icon";

		if (type === "shape")
			dialogTitle = "Choose Shape";

		objDialogWrapper.dialog({
			autoOpen: false,
			height: 500,
			width: 800,
			dialogClass: "unite-ui unite-ui2",
			title: dialogTitle,
			open: function (event, ui) {
				objContainer.scrollTop(0);

				var objSelectedIcon = objContainer.find(".icon-selected");

				if (objSelectedIcon.length === 0)
					return false;

				if (objSelectedIcon.is(":hidden") === true)
					return false;

				// scroll to icon
				var containerHeight = objContainer.height();
				var iconPos = objSelectedIcon.position().top;

				if (iconPos > containerHeight)
					objContainer.scrollTop(iconPos - (containerHeight / 2 - 50));
			}
		});

		// on filter input
		objFilter.on("input", function () {
			var value = objFilter.val().trim();

			objDialogWrapper.find(".unite-iconpicker-dialog-icon").each(function () {
				var objIcon = jQuery(this);
				var name = objIcon.data("name");
				var isVisible = false;

				if (value === "" || name.indexOf(value) > -1)
					isVisible = true;

				objIcon.toggle(isVisible);
			});
		});

		// on icon click
		objContainer.on("click", ".unite-iconpicker-dialog-icon", function () {
			var objIcon = jQuery(this);
			var iconName = objIcon.data("name");
			var iconValue = objIcon.data("value");

			// select icon
			objDialogWrapper
				.find(".unite-iconpicker-dialog-icon")
				.removeClass("icon-selected")
				.filter(objIcon)
				.addClass("icon-selected");

			// update picker value
			var inputValue = iconValue;

			if (type === "fa") {
				if (iconName.indexOf("fa-") === -1)
					inputValue = "fa fa-" + iconName;
				else
					inputValue = iconName;
			}

			objDialogWrapper
				.data("objpicker")
				.find(".unite-iconpicker-input")
				.data("icon-name", iconName)
				.val(inputValue)
				.trigger("input");

			// close dialog
			objDialogWrapper.dialog("close");
		});

		// on icon mouseenter
		objContainer.on("mouseenter", ".unite-iconpicker-dialog-icon", function () {
			var objIcon = jQuery(this);
			var iconName = objIcon.data("name");
			var iconTitle = objIcon.data("title");

			if (iconTitle)
				iconName = iconTitle;

			if (type === "fa") {
				iconName = iconName.replace("fa-", "");

				if (iconName.indexOf("fab ") === 0)
					iconName = iconName.replace("fab ", "") + " [brand]";
				else if (iconName.indexOf("fal ") === 0)
					iconName = iconName.replace("fal ", "") + " [light]";
				else if (iconName.indexOf("far ") === 0)
					iconName = iconName.replace("far ", "") + " [regular]";
				else
					iconName = iconName + " [solid]";
			}

			objIconName.text(iconName);
		});

		// on icon mouseleave
		objContainer.on("mouseleave", ".unite-iconpicker-dialog-icon", function () {
			objIconName.text("");
		});

		return objDialogWrapper;
	}

	/**
	 * icon picker - add icons type
	 */
	function iconPicker_addIconsType(name, arrIcons, iconsTemplate, optParams) {
		var key = "icon_picker_type_" + name;
		var objType = g_ucAdmin.getGlobalData(key);

		if (objType)
			return;

		var params = {
			name: name,
			icons: arrIcons,
			template: iconsTemplate
		};

		if (optParams)
			jQuery.extend(params, optParams);

		g_ucAdmin.storeGlobalData(key, params);
	}

	/**
	 * icon picker - get icons type object
	 */
	function iconPicker_getObjIconsType(name) {
		var key = "icon_picker_type_" + name;
		var objType = g_ucAdmin.getGlobalData(key);

		if (!objType)
			throw new Error("Icons type \"" + name + "\" not found.");

		return objType;
	}

	/**
	 * icon picker - get icons type object
	 */
	function iconPicker_getIconHtmlFromTemplate(template, icon) {
		if (!template)
			throw new Error("Icon template not found.");

		if (typeof template == "function")
			return template(icon);

		return template.replace("[icon]", icon);
	}

	/**
	 * destroy icon pickers
	 */
	function destroyIconPickers() {
		g_objWrapper.find(".unite-iconpicker-button").off("click");
		g_objWrapper.find(".unite-iconpicker-input").off("input");
	}

	/**
	 * get icon input value
	 */
	function getIconInputData(objInput) {
		var inputValue = objInput.val();
		var isUpload = inputValue.indexOf(".svg") > -1;

		if (isUpload === true) {
			var imageId = objInput.data("image-id");
			var imageUrl = g_ucAdmin.urlToRelative(inputValue);

			var svgArray = [{
				id: imageId,
				url: imageUrl,
				library: 'svg',
			}];

			return svgArray;
		}

		return inputValue;
	}

	/**
	 * set icon input value
	 */
	function setIconInputValue(objInput, value){
		if (jQuery.isArray(value) === true) {
			var image = value[0];

			objInput.data("image-id", image.id);

			value = g_ucAdmin.urlToFull(image.url);
		}

		objInput.val(value).trigger("input");
	}



	function __________TABS__________(){}

	/**
	 * init tabs
	 */
	function initTabs(objWrapper) {
		objWrapper.find(".unite-setting-tabs-item-input").on("change", function () {
			var objInput = jQuery(this);
			var id = objInput.attr("name");
			var value = objInput.val();

			objInput.closest(".unite-list-settings")
				.find(".unite-setting-row[data-tabs-id=\"" + id + "\"]")
				.addClass("unite-tabs-hidden")
				.filter("[data-tabs-value=\"" + value + "\"]")
				.removeClass("unite-tabs-hidden");
		});
	}

	/**
	 * destroy tabs
	 */
	function destroyTabs() {
		g_objWrapper.find(".unite-setting-tabs-item-input").off("change");
	}

	/**
	 * set tabs value
	 */
	function setTabsValue(objWrapper, value) {
		objWrapper.find(".unite-setting-tabs-item-input").each(function () {
			var objInput = jQuery(this);

			objInput.prop("checked", objInput.val() === value);
		});

		objWrapper.find(".unite-setting-tabs-item-input:checked").trigger("change");
	}


	function _______DIMENTIONS_____(){}

	/**
	 * init dimentions
	 */
	function initDimentions(objWrapper, funcChange) {
		var objInputs = objWrapper.find(".unite-dimentions-field-input");
		var objLink = objWrapper.find(".unite-dimentions-link");

		objInputs.on("input", function (event) {
			if (objLink.hasClass("unite-active") === true) {
				var objInput = jQuery(this);
				var value = objInput.val();

				objInputs.not(objInput).val(value);
			}

			initDimentions_updateInputPlaceholders(objWrapper);

			funcChange(event, objWrapper);
		});

		objLink.on("click", function (event) {
			objLink.toggleClass("unite-active");

			if (objLink.hasClass("unite-active") === true) {
				var value = objInputs.first().val();

				objInputs.val(value);
			}

			initDimentions_updateInputPlaceholders(objWrapper);
			initDimentions_updateLinkTitle(objLink);

			funcChange(event, objWrapper);
		});

		initDimentions_updateInputPlaceholders(objWrapper);
		initDimentions_updateLinkTitle(objLink);

		setUnitsPickerChangeHandler(objWrapper, function () {
			funcChange(null, objWrapper);
		});
	}

	/**
	 * init dimentions - update input placeholders
	 */
	function initDimentions_updateInputPlaceholders(objWrapper) {
		var responsiveTypes = getResponsiveTypes();
		var id = getResponsivePickerId(objWrapper);
		var objRows = g_objWrapper.find("[data-responsive-id=\"" + id + "\"]");
		var previousValue = null;

		for (var index in responsiveTypes) {
			var type = responsiveTypes[index];
			var objRow = objRows.filter("[data-responsive-type=\"" + type + "\"]");

			if (previousValue !== null) {
				objRow.find(".unite-dimentions-field-input").each(function () {
					var objInput = jQuery(this);
					var key = objInput.data("key");

					objInput.attr("placeholder", previousValue[key]);
				});
			}

			var value = getDimentionsValue(objRow.find(".unite-dimentions"));

			previousValue = previousValue || {};

			for (var key in value) {
				previousValue[key] = value[key] || previousValue[key] || "";
			}
		}
	}

	/**
	 * init dimentions - update link title
	 */
	function initDimentions_updateLinkTitle(objLink) {
		var title = objLink.hasClass("unite-active") === true
			? objLink.data("title-unlink")
			: objLink.data("title-link");

		objLink.attr("title", title);
	}

	/**
	 * destroy dimentions
	 */
	function destroyDimentions() {
		g_objWrapper.find(".unite-dimentions-field-input").off("input");
		g_objWrapper.find(".unite-dimentions-link").off("click");
	}

	/**
	 * get dimentions value
	 */
	function getDimentionsValue(objWrapper) {
		var data = {};

		objWrapper.find(".unite-dimentions-field-input").each(function () {
			var objInput = jQuery(this);
			var value = objInput.val();
			var key = objInput.data("key");

			data[key] = value;
		});

		objWrapper.find(".unite-dimentions-link").each(function () {
			var objLink = jQuery(this);
			var value = objLink.hasClass("unite-active") === true;
			var key = objLink.data("key");

			data[key] = value;
		});

		data["unit"] = getUnitsPickerValue(objWrapper);

		return data;
	}

	/**
	 * set dimentions value
	 */
	function setDimentionsValue(objWrapper, value) {
		objWrapper.find(".unite-dimentions-field-input").each(function () {
			var objInput = jQuery(this);
			var key = objInput.data("key");

			objInput.val(value[key]);
		});

		objWrapper.find(".unite-dimentions-link").each(function () {
			var objLink = jQuery(this);
			var key = objLink.data("key");

			objLink.toggleClass("unite-active", value[key] === true);

			initDimentions_updateLinkTitle(objLink);
		});

		initDimentions_updateInputPlaceholders(objWrapper);

		setUnitsPickerValue(objWrapper, value["unit"]);
	}


	function ______POST_PICKER____(){}

	/**
	 * init post picker
	 */
	function initPostPicker(objWrapper, data, selectedValue) {
		var objSelect = objWrapper.find(".unite-setting-post-picker");
		var selectedPostId = objSelect.data("selected-post-id");
		var selectedPostTitle = objSelect.data("selected-post-title");

		if (!data)
			data = [];

		if (selectedPostId && selectedPostTitle) {
			data.push({
				id: selectedPostId,
				text: selectedPostTitle,
			});
		}

		initSelect2(objSelect, {
			allowClear: true,
			data: data, 
			minimumInputLength: 1,
			placeholder: objSelect.data("placeholder"),
			ajax: {
				url: g_ucAdmin.getUrlAjax("get_posts_list_forselect"),
				dataType: "json",
				data: function (params) {
					params.q = params.term;

					return params;
				},
			}
		});

		if (selectedValue)
			objSelect.val(selectedValue).trigger("change.select2");

		objSelect.on("change", function () {
			t.onSettingChange(null, objWrapper);
		});
	}

	/**
	 * get post picker value
	 */
	function getPostPickerValue(objWrapper) {
		return objWrapper.find(".unite-setting-post-picker").val();
	}

	/**
	 * set post picker value
	 */
	function setPostPickerValue(objWrapper, value) {
		value = value || [];

		if (jQuery.isArray(value) === false)
			value = [value];

		if (value.length === 0) {
			initPostPicker(objWrapper);

			return;
		}

		g_ucAdmin.ajaxRequest("get_select2_post_titles", { post_ids: value }).then(function (response) {
			initPostPicker(objWrapper, response.select2_data, value);
		});
	}


	function ______POST_IDS_PICKER____(){}

	/**
	 * init post ids picker
	 */
	function initPostIdsPicker(objInput, data, selectedValue) {
		var multiple = objInput.data("issingle") !== true;
		var dataType = objInput.data("datatype");
		var postType = null;

		if (!data)
			data = [];

		if (objInput.data("woo") === "yes")
			postType = "product";
		else if (dataType === "elementor_template")
			postType = "elementor_template";

		var action = "get_posts_list_forselect";

		if (dataType === "terms")
			action = "get_terms_list_forselect";
		else if (dataType === "users")
			action = "get_users_list_forselect";

		initSelect2(objInput, {
			allowClear: multiple === false,
			data: data,
			minimumInputLength: 1,
			multiple: multiple === true,
			placeholder: objInput.data("placeholdertext"),
			ajax: {
				url: g_ucAdmin.getUrlAjax(action),
				dataType: "json",
				data: function (params) {
					params.q = params.term;

					if (postType)
						params.post_type = postType;

					return params;
				},
			},
		});

		if (selectedValue)
			objInput.val(selectedValue).trigger("change.select2");
	}

	/**
	 * set post ids picker value
	 */
	function setPostIdsPickerValue(objInput, value) {
		value = value || [];

		if (jQuery.isArray(value) === false)
			value = [value];

		if (!value.length) {
			initPostIdsPicker(objInput);

			return;
		}

		var dataType = objInput.data("datatype");
		var action = "get_select2_post_titles";

		if (dataType === "terms")
			action = "get_select2_terms_titles";
		else if (dataType === "users")
			action = "get_select2_users_titles";

		g_ucAdmin.ajaxRequest(action, { post_ids: value }).then(function (response) {
			initPostIdsPicker(objInput, response.select2_data, value);
		});
	}


	function _______LINK_____(){}

	/**
	 * init link
	 */
	function initLink(objInput, funcChange) {
		var objWrapper = objInput.closest(".unite-setting-link-wrapper");
		var objToggle = objWrapper.find(".unite-setting-link-toggle");
		var objOptions = objWrapper.find(".unite-setting-link-options");
		var objExternal = objWrapper.find(".unite-setting-link-external");
		var objNofollow = objWrapper.find(".unite-setting-link-nofollow");
		var objAttributes = objWrapper.find(".unite-setting-link-attributes");
		var objAutocomplete = objWrapper.find(".unite-setting-link-autocomplete");
		var objAutocompleteLoader = objAutocomplete.find(".unite-setting-link-autocomplete-loader");
		var objAutocompleteItems = objAutocomplete.find(".unite-setting-link-autocomplete-items");
		var funcChangeDebounced = g_ucAdmin.debounce(funcChange);
		var loadAutocompleteDebounced = g_ucAdmin.debounce(loadAutocomplete);

		objInput.on("input", loadAutocompleteDebounced);
		objInput.on("focus", loadAutocomplete);

		objToggle.on("click", function () {
			hideAutocomplete();

			objOptions.stop().slideToggle(g_vars.animationDuration);
		});

		objExternal.on("change", function () {
			funcChange(null, objInput);
		});

		objNofollow.on("change", function () {
			funcChange(null, objInput);
		});

		objAttributes.on("input", function () {
			funcChangeDebounced(null, objInput);
		});

		objAutocomplete.on("click", ".unite-setting-link-autocomplete-item", function () {
			var url = jQuery(this).data("url");

			hideAutocomplete();

			objInput.val(url);

			funcChange(null, objInput);
		});

		jQuery(document).on("click", function (event) {
			var objElement = jQuery(event.target);

			if (objElement.closest(".unite-setting-link-wrapper").length === 1)
				return;

			hideAutocomplete();
		});

		function loadAutocomplete() {
			hideAutocomplete();

			if (g_temp.linkAutocompleteRequest !== null)
				g_temp.linkAutocompleteRequest.abort();

			var query = objInput.val().trim().toLowerCase();

			if (query.length < 2
				|| query.indexOf("http:") === 0
				|| query.indexOf("https:") === 0)
				return;

			objAutocomplete.show();
			objAutocompleteLoader.show();
			objAutocompleteItems.hide();

			g_temp.linkAutocompleteRequest = g_ucAdmin.ajaxRequest("get_link_autocomplete", { query: query }, function (response) {
				var items = [];

				jQuery(response.results).each(function (index, item) {
					var objItem = jQuery(
						"<div class='unite-setting-link-autocomplete-item'>"
						+ "  <div class='unite-setting-link-autocomplete-item-content'>"
						+ "    <div class='unite-setting-link-autocomplete-item-label'></div>"
						+ "    <div class='unite-setting-link-autocomplete-item-value'></div>"
						+ "  </div>"
						+ "  <div class='unite-setting-link-autocomplete-item-aside'></div>"
						+ "</div>",
					);

					// add leading slash and remove trailing slash
					var relativeUrl = "/" + g_ucAdmin.urlToRelative(item.url).replace(/\/$/, "");

					objItem.data("url", item.url);
					objItem.find(".unite-setting-link-autocomplete-item-label").text(item.title);
					objItem.find(".unite-setting-link-autocomplete-item-value").text(relativeUrl);
					objItem.find(".unite-setting-link-autocomplete-item-aside").text(item.type);

					items.push(objItem);
				});

				objAutocompleteItems.empty().append(items).show();

				if (response.results.length === 0)
					hideAutocomplete();
			}).always(function () {
				objAutocompleteLoader.hide();
			});
		}

		function hideAutocomplete() {
			objAutocomplete.hide();
		}
	}

	/**
	 * destroy links
	 */
	function destroyLinks() {
		g_objWrapper.find(".unite-setting-link").off("input").off("focus");
		g_objWrapper.find(".unite-setting-link-toggle").off("click");
		g_objWrapper.find(".unite-setting-link-external").off("change");
		g_objWrapper.find(".unite-setting-link-nofollow").off("change");
		g_objWrapper.find(".unite-setting-link-attributes").off("input");
		g_objWrapper.find(".unite-setting-link-autocomplete").off("click");
	}

	/**
	 * get link value
	 */
	function getLinkInputValue(objInput) {
		var objWrapper = objInput.closest(".unite-setting-link-wrapper");
		var url = objWrapper.find(".unite-setting-link").val();
		var external = objWrapper.find(".unite-setting-link-external").prop("checked") ? "on" : "";
		var nofollow = objWrapper.find(".unite-setting-link-nofollow").prop("checked") ? "on" : "";
		var attributes = objWrapper.find(".unite-setting-link-attributes").val();

		var value = {
			url: url,
			is_external: external,
			nofollow: nofollow,
			custom_attributes: attributes,
		};

		return value;
	}

	/**
	 * set link value
	 */
	function setLinkInputValue(objInput, value) {
		var objWrapper = objInput.closest(".unite-setting-link-wrapper");

		if (typeof value === "string")
			value = { url: value };

		objWrapper.find(".unite-setting-link").val(value.url);
		objWrapper.find(".unite-setting-link-external").prop("checked", value.is_external === "on");
		objWrapper.find(".unite-setting-link-nofollow").prop("checked", value.nofollow === "on");
		objWrapper.find(".unite-setting-link-attributes").val(value.custom_attributes);
	}


	function _______ANIMATIONS_____(){}

	/**
	 * on settings animation change, run the demo
	 */
	function onAnimationSettingChange(){

		var objSelect = jQuery(this);
		var objParent = objSelect.parents("table");
		if(objParent.length == 0)
			objParent = objSelect.parents("ul");

		g_ucAdmin.validateDomElement(objParent, "Animation setting parent");

		var objDemo = objParent.find(".uc-animation-demo span");
		var animation = objSelect.val();

		g_ucAdmin.validateDomElement(objDemo, "Animation setting demo");

		var className = animation + ' animated';
		objDemo.removeClass().addClass(className).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
		      jQuery(this).removeClass();
		 });

	}

	/**
	 * init animations selector settings
	 */
	function initAnimationsSelector() {

		getObjInputs()
			.find("select.uc-select-animation-type")
			.on("change", onAnimationSettingChange);
	}

	function _____ITEMS_PANEL_____(){}


	/**
	 * open the items manager in dialog
	 */
	function onItemsPanelEditItemsClick(){

		var objButton = jQuery(this);
		var objSetting = objButton.parents(".uc-setting-items-panel");
		var settingID = objSetting.prop("id");
		var dialogID = settingID+"_dialog";

		var objDialog = jQuery("#"+dialogID);

		g_ucAdmin.validateDomElement(objDialog, "items dialog");

		var buttonOpts = {};

		buttonOpts[g_uctext.update] = function(){
			objDialog.dialog("close");

			var objItemsWrapper = g_objParent.find(".uc-setting-items-panel");
			t.onSettingChange(null, objItemsWrapper);
		};

		var dialogOptions = {
				buttons:buttonOpts,
				minWidth:800,
				modal:true,
				dialogClass:"unite-ui",
				open:function(){
				}
			};

		objDialog.dialog(dialogOptions);

	}


	/**
	 * init items panel setting
	 */
	function initItemsPanel(){

		var objItemsWrapper = g_objParent.find(".uc-setting-items-panel");
		if(objItemsWrapper.length == 0)
			return(false);

		if(objItemsWrapper.length != 1){
			throw new Error("There must be only 1 items panel");
		}

		g_temp.objItemsManager = new UCManagerAdmin();
		g_temp.objItemsManager.initManager();

		//side panel dialog

		var objButtonEditItems = objItemsWrapper.find(".uc-setting-items-panel-button");
		if(objButtonEditItems.length){
			objButtonEditItems.on("click",onItemsPanelEditItemsClick);
		}

	}

	function __________SUB_SETTINGS__________(){}

	/**
	 * init sub settings
	 */
	function initSubSettings(objWrapper, funcChange) {
		initSubSettingsDialog(objWrapper);

		var objDialog = getSubSettingsDialog(objWrapper);
		var objResetButton = objWrapper.find(".unite-sub-settings-reset");
		var objEditButton = objWrapper.find(".unite-sub-settings-edit");

		objResetButton.on("click", function () {
			setSubSettingsValue(objWrapper);
		});

		objEditButton.on("click", function (event) {
			event.stopPropagation();

			var dialogId = objWrapper.data("dialog-id");
			var containsDialog = objWrapper.find(objDialog).length === 1;

			// check if the dialog already exists, if so - show/hide,
			// otherwise append to the wrapper and show
			if (containsDialog === true) {
				objDialog.stop().slideToggle(g_vars.animationDuration);
			} else {
				objWrapper.append(objDialog);

				objDialog.stop().slideDown(g_vars.animationDuration);
			}

			// hide other dialogs
			g_objParent
				.find(".unite-sub-settings-dialog:not([data-id='" + dialogId + "'])")
				.stop()
				.slideUp(g_vars.animationDuration);

			setSubSettingsDialogChangeHandler(objWrapper, function (value, css) {
				objWrapper.data("value", value).data("css", css);
				objResetButton.removeClass("unite-hidden");

				funcChange(null, objWrapper);
			});

			setSubSettingsDialogValue(objWrapper, objWrapper.data("value"));
		});

		jQuery(document).on("click", function (event) {
			if (objDialog.is(":hidden") === true)
				return;

			var objElement = jQuery(event.target);

			if (objElement.closest(".unite-sub-settings-dialog").length === 1)
				return;

			objDialog.stop().slideUp(g_vars.animationDuration);
		});
	}

	/**
	 * destroy sub settings
	 */
	function destroySubSettings() {
		g_objWrapper.find(".unite-sub-settings-reset").off("click");
		g_objWrapper.find(".unite-sub-settings-edit").off("click");
	}

	/**
	 * init sub settings dialog
	 */
	function initSubSettingsDialog(objWrapper) {
		var objDialog = getSubSettingsDialog(objWrapper);

		if (objDialog.length === 0)
			throw new Error("Missing sub settings dialog.");

		var isInited = objDialog.data("inited");

		if (isInited === true)
            return;

		var objSettings = new UniteSettingsUC();
		var objSettingsElement = objDialog.find(".unite-settings");
		var options = { disable_exclude_selector: true };

					objSettings.init(objSettingsElement, options);

					objSettings.setEventOnSelectorsChange(function () {
						var value = objSettings?.getSettingsValues();
						var css = objSettings?.getSelectorsCss();
						var onChange = objDialog.data("on_change");

						if (typeof onChange === "function")
							onChange(value, css);
					});

					objDialog.data("inited", true).data("settings", objSettings);
				}

	/**
	 * get sub settings dialog
	 */
	function getSubSettingsDialog(objWrapper) {
		var id = objWrapper.data("dialog-id");

		return g_objWrapper.find(".unite-sub-settings-dialog[data-id='" + id + "']");
	}

	/**
	 * get sub settings dialog css
	 */
	function getSubSettingsDialogCss(objWrapper) {
		var objSettings = getSubSettingsDialog(objWrapper).data("settings");

		return objSettings?.getSelectorsCss();
	}

	/**
	 * set sub settings dialog value
	 */
	function setSubSettingsDialogValue(objWrapper, value) {
		var objSettings = getSubSettingsDialog(objWrapper).data("settings");

		objSettings?.setValues(value);
	}

	/**
	 * set sub settings dialog change handler
	 */
	function setSubSettingsDialogChangeHandler(objWrapper, handler) {
		getSubSettingsDialog(objWrapper).data("on_change", handler);
	}

	/**
	 * get sub settings value
	 */
	function getSubSettingsValue(objWrapper) {
		return objWrapper.data("value") || {};
	}

	/**
	 * set sub settings value
	 */
	function setSubSettingsValue(objWrapper, value) {
		value = value || {};

		setSubSettingsDialogValue(objWrapper, value);

		var css = getSubSettingsDialogCss(objWrapper);

		objWrapper.data("value", value).data("css", css);

		t.onSettingChange(null, objWrapper);

		objWrapper.find(".unite-sub-settings-reset").toggleClass("unite-hidden", jQuery.isEmptyObject(value));
	}


	function __________TYPOGRAPHY__________(){}

	/**
	 * get typography selector includes
	 */
	function getTypographySelectorIncludes(objWrapper) {
		var includes = {};
		var value = getSubSettingsValue(objWrapper);

		if (g_ucGoogleFonts.fonts[value.font_family]) {
			var slug = value.font_family
				.replace(/\s+/g, "_") // replace spaces with underscore
				.replace(/[^\da-z_-]/ig, "") // remove special characters
				.toLowerCase();

			var handle = "ue_google_font_" + slug;
			var variations = ["100", "100i", "200", "200i", "300", "300i", "400", "400i", "500", "500i", "600", "600i", "700", "700i", "800", "800i", "900", "900i"].join(",");
			var url = g_ucGoogleFonts.base_url + "?display=swap&family=" + encodeURIComponent(value.font_family) + ":" + variations;

			includes[handle] = {
				handle: handle,
				type: "css",
				url: url
			};
		}

		return includes;
	}


	function _______REPEATERS_____(){}

	/**
	 * init repeaters
	 */
	function initRepeaters() {
		var objRepeaters = g_objWrapper.find(".unite-setting-repeater");

		if (objRepeaters.length === 0)
			return;

		g_temp.isRepeaterExists = true;
	}

	/**
	 * init repeater
	 */
	function initRepeater(objWrapper, funcChange) {
		objWrapper.sortable({
			items: ".unite-repeater-item",
			handle: ".unite-repeater-item-header",
			cursor: "move",
			axis: "y",
			update: function () {
				funcChange(null, objWrapper);
			},
		});

		objWrapper.on("click", ".unite-repeater-add", addRepeaterItem);

		objWrapper.on("click", ".unite-repeater-item-title", function () {
			var objItem = jQuery(this).closest(".unite-repeater-item");

			objItem
				.closest(".unite-repeater-items")
				.find(".unite-repeater-item")
				.not(objItem)
				.find(".unite-repeater-item-content")
				.stop()
				.slideUp(g_vars.animationDuration);

			objItem
				.find(".unite-repeater-item-content")
				.stop()
				.slideToggle(g_vars.animationDuration);
		});

		objWrapper.on("click", ".unite-repeater-item-delete", function () {
			jQuery(this).closest(".unite-repeater-item").remove();

			if (objWrapper.find(".unite-repeater-item").length === 0)
				objWrapper.find(".unite-repeater-empty").show();

			funcChange(null, objWrapper);
		});

		objWrapper.on("click", ".unite-repeater-item-duplicate", function () {
			var objItem = jQuery(this).closest(".unite-repeater-item");
			var itemValues = objItem.data("objsettings").getSettingsValues();

			addRepeaterItem(null, objWrapper, itemValues, objItem);
		});
	}

	/**
	 * add repeater item
	 */
	function addRepeaterItem(event, objWrapper, itemValues, objItemInsertAfter) {
		if (!objWrapper)
			objWrapper = jQuery(this).closest(".unite-setting-repeater");

		var objSettingsTemplate = objWrapper.find(".unite-repeater-template");
		var objItemsWrapper = objWrapper.find(".unite-repeater-items");
		var objEmpty = objWrapper.find(".unite-repeater-empty");

		g_ucAdmin.validateDomElement(objItemsWrapper, "items wrapper");
		g_ucAdmin.validateDomElement(objSettingsTemplate, "settings template");

		objEmpty.hide();

		// prepare item values
		if (!itemValues) {
			var itemNumber = objWrapper.find(".unite-repeater-item").length + 1;
			var itemTitle = objWrapper.data("item-title") + " " + itemNumber;

			itemValues = {
				title: itemTitle,
			};
		}

		if (!itemValues._generated_id)
			itemValues._generated_id = g_ucAdmin.getRandomString(5);

		// get item html
		var textDelete = objWrapper.data("text-delete");
		var textDuplicate = objWrapper.data("text-duplicate");

		var html = "<div class='unite-repeater-item'>";
		html += " <div class='unite-repeater-item-header'>";
		html += "	 <div class='unite-repeater-item-title'>" + itemValues.title + "</div>";
		html += "	 <div class='unite-repeater-item-actions'>";
		html += "	  <button class='unite-repeater-item-action unite-repeater-item-duplicate uc-tip' title='" + textDuplicate + "'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'><path d='M8.625.375H.375v8.25h8.25V.375Z' /><path d='M10.125 3.375h1.5v8.25h-8.25v-1.5' /></svg></button>";
		html += "		<button class='unite-repeater-item-action unite-repeater-item-delete uc-tip' title='" + textDelete + "'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'><path d='m1.5 1.5 9 9M10.5 1.5l-9 9' /></svg></button>";
		html += "	 </div>";
		html += "	</div>";
		html += "	<div class='unite-repeater-item-content'>";
		html += objSettingsTemplate.html();
		html += "	</div>";
		html += "</div>";

		// change item settings IDs
		var objItem = jQuery(html);
		var objItemSettingsWrapper = objItem.find(".unite_settings_wrapper");

		g_ucAdmin.validateDomElement(objItemSettingsWrapper, "item settings wrapper");

		var options = objItemSettingsWrapper.data("options");
		var idPrefix = options.id_prefix;
		var newID = idPrefix + "item_" + itemValues._generated_id + "_";

		html = g_ucAdmin.replaceAll(html, idPrefix, newID);

		// change item settings wrapper ID
		objItem = jQuery(html);
		objItemSettingsWrapper = objItem.find(".unite_settings_wrapper");
		objItemSettingsWrapper.attr("id", "unite_settings_repeater_" + newID);

		if (objItemInsertAfter)
			objItemInsertAfter.after(objItem);
		else
			objItemsWrapper.append(objItem);

		// init item settings
		var objSettings = new UniteSettingsUC();

		objSettings.init(objItemSettingsWrapper);
		objSettings?.setValues(itemValues);

		objItem.data("objsettings", objSettings);

		// init item title change
		var objTitleInput = objSettings.getInputByName("title");
		var objItemTitle = objItem.find(".unite-repeater-item-title");

		objTitleInput.on("input", function () {
			var value = objTitleInput.val();

			objItemTitle.text(value);
		});

		t.onSettingChange(null, objWrapper);
	}

	/**
	 * destroy repeater items
	 */
	function destroyRepeaterItems(objWrapper) {
		objWrapper.find(".unite-repeater-item").each(function () {
			var objItem = jQuery(this);

			objItem.data("objsettings").destroy();

			objItem.find(".unite-repeater-item-title").off("click");
			objItem.find(".unite-repeater-item-delete").off("click");
			objItem.find(".unite-repeater-item-duplicate").off("click");

			objItem.remove();
		});
	}

	/**
	 * destroy repeaters
	 */
	function destroyRepeaters() {
		g_objWrapper.find(".unite-repeater").sortable("destroy");
		g_objWrapper.find(".unite-repeater-add").off("click");

		g_objWrapper.find(".unite-setting-repeater").each(function () {
			destroyRepeaterItems(jQuery(this));
		});
	}

	/**
	 * get repeater values
	 */
	function getRepeaterValues(objWrapper) {
		var values = [];

		objWrapper.find(".unite-repeater-item").each(function () {
			var itemValues = jQuery(this).data("objsettings").getSettingsValues();

			values.push(itemValues);
		});

		return values;
	}

	/**
	 * set repeater values
	 */
	function setRepeaterValues(objWrapper, values, useDefault) {
		destroyRepeaterItems(objWrapper);

		if (useDefault === true)
			values = objWrapper.data("itemvalues");

		if (jQuery.isArray(values) === false)
			return;

		jQuery.each(values, function (index, itemValues) {
			addRepeaterItem(null, objWrapper, itemValues);
		});
	}


	function _______SWITCHER_____(){}

	/**
	 * init switcher
	 */
	function initSwitcher(objWrapper, funcChange) {
		objWrapper.on("click", function () {
			objWrapper.toggleClass("unite-checked");

			funcChange(null, objWrapper);
		});
	}

	/**
	 * get switcher value
	 */
	function getSwitcherValue(objWrapper) {
		var checkedValue = objWrapper.data("checkedvalue");
		var uncheckedValue = objWrapper.data("uncheckedvalue");

		return objWrapper.hasClass("unite-checked") ? checkedValue : uncheckedValue;
	}

	/**
	 * set switcher value
	 */
	function setSwitcherValue(objWrapper, value) {
		var checkedValue = objWrapper.data("checkedvalue");

		checkedValue = g_ucAdmin.strToBool(checkedValue);
		value = g_ucAdmin.strToBool(value);

		objWrapper.toggleClass("unite-checked", value === checkedValue);
	}


	function _______CONTROLS_____(){}

	/**
	 * get control action
	 */
	function getControlAction(parent, control) {
		var isEqual = isInputValuesEqual(parent.value, control.value);
		var action = null;

		switch (control.type) {
			case "enable":
			case "disable":
				if ((control.type === "enable" && isEqual === true)
					|| (control.type === "disable" && isEqual === false))
					action = "enable";
				else
					action = "disable";
			break;
			case "show":
			case "hide":
				if ((control.type === "show" && isEqual === true)
					|| (control.type === "hide" && isEqual === false))
					action = "show";
				else
					action = "hide";
			break;
		}

		return action;
	}

	/**
	 * get action of multiple parents
	 */
	function getControlActionMultiple(parent, control, arrParents) {
		if (g_temp.cacheValues === null)
			g_temp.cacheValues = t.getSettingsValues(true);

		var action = null;
		var mainAction = null;
		var isShow = null;
		var isEnable = null;

		jQuery.each(arrParents, function (index, parentID) {
			if (parentID === parent.id) {
				action = getControlAction(parent, control);
				mainAction = action;
			} else {
				var objControl = g_arrControls[parentID][control.idChild];
				var parentValue = g_temp.cacheValues[parentID];

				var objParent = {
					id: parentID,
					value: parentValue 
				};

				action = getControlAction(objParent, objControl);
			}

			switch (action) {
				case "show":
					if (isShow === null)
						isShow = true;
				break;
				case "hide":
					isShow = false;
				break;
				case "enable":
					if (isEnable === null)
						isEnable = true;
				break;
				case "disable":
					isEnable = false;
				break;
			}
		});

		if (isEnable === null && isShow === null)
			return;

		var showAction = (isShow === true) ? "show" : "hide";
		var enableAction = (isEnable === true) ? "enable" : "disable";

		if (isEnable !== null && isShow !== null) {
			if (mainAction === "show" || mainAction === "hide")
				return showAction;

			return enableAction;
		}

		if (isShow !== null)
			return showAction;

		return enableAction;
	}

	/**
	 * process control setting change
	 */
	function processControlSettingChange(objInput) {
		var allowedTypes = ["select", "select2", "radio", "switcher"];
		var controlType = getInputType(objInput);

		if (jQuery.inArray(controlType, allowedTypes) === -1)
			return;

		var controlID = getInputName(objInput);

		if (!controlID)
			return;

		if (!g_arrControls[controlID])
			return;

		var controlValue = getSettingInputValue(objInput);
		var arrChildControls = g_arrControls[controlID];

		g_temp.cacheValues = null;

		var objParent = {
			id: controlID,
			value: controlValue
		};

		jQuery.each(arrChildControls, function (childName, objControl) {
			var isSap = g_ucAdmin.getVal(objControl, "forsap");
			var rowID;
			var objChildInput = null;

			if (isSap === true) {	//sap
				rowID = g_IDPrefix + "ucsap_" + childName;
			} else { //setting
				rowID = g_IDPrefix + childName + "_row";
				objChildInput = jQuery(g_IDPrefix + childName);
			}

			var objChildRow = jQuery(rowID);

			if (objChildRow.length === 0)
				return;

			objControl.idChild = childName;

			// check multiple parents
			var arrParents = g_ucAdmin.getVal(g_arrChildrenControls, childName);
			var action;

			if (arrParents)
				action = getControlActionMultiple(objParent, objControl, arrParents);
			else
				action = getControlAction(objParent, objControl);

			var isChildRadio = false;
			var isChildColor = false;

			if (objChildInput && objChildInput.length > 0) {
				var inputTagName = objChildInput.get(0).tagName;

				isChildRadio = inputTagName === "SPAN" && objChildInput.hasClass("unite-radio-wrapper");
				isChildColor = objChildInput.hasClass("unite-color-picker");
			}

			switch (objControl.type) {
				case "enable":
				case "disable":
					var isDisable = (action === "disable");

					objChildRow.toggleClass("setting-disabled", isDisable);

					if (!objChildInput)
						return;

					objChildInput.prop("disabled", isDisable);

					if (isChildRadio === true) {
						objChildInput
							.children("input")
							.prop("disabled", isDisable)
							.toggleClass("disabled", isDisable);
					} else if (isChildColor === true) {
						if (g_temp.colorPickerType === "spectrum")
							objChildInput.spectrum(isDisable ? "disable" : "enable");

						if (isDisable === false && g_colorPicker)
							g_colorPicker.linkTo(objChildInput);
					}
				break;
				case "show":
				case "hide":
					var isShow = (action === "show");
					var isHidden = objChildRow.hasClass("unite-setting-hidden");

					objChildRow.toggleClass("unite-setting-hidden", !isShow);

					if (!objChildInput)
						return;

					t.disableTriggerChange();

					jQuery.each(objChildInput, function () {
						var objInput = jQuery(this);
						var value = getSettingInputValue(objInput);

						if (isShow === true && isHidden === true) {
							value = objInput.data("previous-value") || value;

							setInputValue(objInput, value);

							return;
						}

						if (isShow === false && isHidden === false) {
							objInput.data("previous-value", value);

							clearInput(objInput);
						}
					});

					t.enableTriggerChange();
				break;
			}
		});
	}


	function _______RESPONSIVE_PICKER_____(){}

	/**
	 * init responsive picker
	 */
	function initResponsivePicker() {
		g_objWrapper.find(".unite-responsive-picker").each(function () {
			var objPicker = jQuery(this);

			objPicker.on("change", function () {
				var type = objPicker.val();

				t.setResponsiveType(type);

				triggerEvent(t.events.RESPONSIVE_TYPE_CHANGE, [type]);
			});

			initSelect2(objPicker, {
				dropdownParent: objPicker.parent(),
			});
		});

		t.setResponsiveType("desktop");
	}

	/**
	 * get responsive picker id for element
	 */
	function getResponsivePickerId(objElement) {
		return objElement.closest(".unite-setting-row").data("responsive-id");
	}

	/**
	 * get responsive picker value for element
	 */
	function getResponsivePickerValue(objElement) {
		return objElement.closest(".unite-setting-row").data("responsive-type") || "desktop";
	}


	function _______UNITS_PICKER_____(){}

	/**
	 * init units picker
	 */
	function initUnitsPicker() {

		g_objWrapper.find(".unite-units-picker").each(function () {
			var objPicker = jQuery(this);

			initSelect2(objPicker, {
				dropdownParent: objPicker.parent(),
			});

			objPicker.on("change", function () {
				var value = objPicker.val();
				var onChange = objPicker.data("on_change");

				if (typeof onChange === "function")
					onChange(value);
			});
		});
	}

	/**
	 * get units picker for element
	 */
	function getUnitsPickerForElement(objElement) {
		return objElement.closest(".unite-setting-row").find(".unite-units-picker");
	}

	/**
	 * get units picker value for element
	 */
	function getUnitsPickerValue(objElement) {
		return getUnitsPickerForElement(objElement).val() || "px";
	}

	/**
	 * set units picker value for element
	 */
	function setUnitsPickerValue(objElement, value) {
		getUnitsPickerForElement(objElement).val(value).trigger("change.select2");
	}

	/**
	 * set units picker change handler
	 */
	function setUnitsPickerChangeHandler(objElement, handler) {
		getUnitsPickerForElement(objElement).data("on_change", handler);
	}


	function _________SELECTORS__________(){}

	/**
	 * get selectors includes
	 */
	this.getSelectorsIncludes = function () {
		var objInputs = getObjInputs();
		var includes = {};

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var type = getInputType(objInput);
			var inputIncludes = {};

			switch (type) {
				case "typography":
					inputIncludes = getTypographySelectorIncludes(objInput);
				break;
			}

			if (inputIncludes)
				jQuery.extend(includes, inputIncludes);
		});

		return includes;
	}

	/**
	 * get selectors css
	 */
	this.getSelectorsCss = function () {
		var objInputs = getObjInputs();
		var css = "";

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);

			// skip hidden/disabled setting
			if (objInput.closest(".unite-setting-row").hasClass("unite-setting-hidden") === true)
				return;

			var type = getInputType(objInput);
			var style;

			switch (type) {
				case "repeater":
					style = processRepeaterSelectors(objInput);
				break;
				default:
					style = processInputSelectors(objInput);
				break;
			}

			if (style)
				css += style;
		});

		return css;
	}

	/**
	 * prepare selector
	 */
	function prepareSelector(selector) {
		return selector
			.split(",")
			.map(function (selector) {
				selector = selector.trim();

				if (g_selectorWrapperID)
					selector = "#" + g_selectorWrapperID + " " + selector;

				return selector;
			})
			.join(",");
	}

	/**
	 * combine selectors
	 */
	function combineSelectors(selectors) {
		return Object.keys(selectors).join(",");
	}

	/**
	 * process selector replaces
	 */
	function processSelectorReplaces(css, replaces) {
		jQuery.each(replaces, function (placeholder, replace) {
			if (typeof replace === "string" || typeof replace === "number") {
				css = g_ucAdmin.replaceAll(css, placeholder.toLowerCase(), replace);
				css = g_ucAdmin.replaceAll(css, placeholder.toUpperCase(), replace);
			}
		});

		return css;
	}

	/**
	 * get selectors style
	 */
	function getSelectorsStyle(selectors, replaces) {
		var style = "";

		for (var selector in selectors) {
			var value = selectors[selector];
			var css = processSelectorReplaces(value, replaces);

			style += selector + "{" + css + "}";
		}

		return style;
	}

	/**
	 * get dimentions selector replaces
	 */
	function getDimentionsSelectorReplaces(value) {
		return {
			"{{top}}": value.top + value.unit,
			"{{right}}": value.right + value.unit,
			"{{bottom}}": value.bottom + value.unit,
			"{{left}}": value.left + value.unit
		};
	}

	/**
	 * get image selector replaces
	 */
	function getImageSelectorReplaces(value) {
		value.url = g_ucAdmin.urlToFull(value.url);

		if (!value.url)
			return;

		return { "{{value}}": value.url };
	}

	/**
	 * get range slider selector replaces
	 */
	function getRangeSliderSelectorReplaces(value) {
		if (!value.size)
			return;

		return {
			"{{value}}": value.size + value.unit,
			"{{size}}": value.size,
			"{{unit}}": value.unit
		};
	}

	/**
	 * get input selector replaces
	 */
	function getInputSelectorReplaces(objInput) {
		var value = getSettingInputValue(objInput);

		if (!value)
			return;

		var type = getInputType(objInput);

		switch (type) {
			case "dimentions":
				return getDimentionsSelectorReplaces(value);
			case "image":
				return getImageSelectorReplaces(value);
			case "range":
				return getRangeSliderSelectorReplaces(value);
		}

		return { "{{value}}": value };
	}

	/**
	 * determine if the input has selector
	 */
	function isInputHasSelector(objInput) {
		var groupSelector = objInput.data("group-selector");

		if (groupSelector)
			return true;

		var selectors = objInput.data("selectors");

		if (selectors)
			return true;

		return false;
	}

	/**
	 * get input selectors
	 */
	function getInputSelectors(objInput) {
		var selectors = objInput.data("selectors");

		if (!selectors)
			return {};

		var keys = ["selector", "selector1", "selector2", "selector3"];
		var data = {};

		for (var index in keys) {
			var key = keys[index];
			var selector = g_ucAdmin.getVal(selectors, key);
			var selectorValue = g_ucAdmin.getVal(selectors, key + "_value");

			if (!selector)
				continue;

			selector = prepareSelector(selector);

			data[selector] = selectorValue;
		}

		return data;
	}

	/**
	 * process input selectors
	 */
	function processInputSelectors(objInput) {
		var groupSelector = objInput.data("group-selector");

		if (groupSelector)
			return; // skip individual input and process the group at once

		var selectors = getInputSelectors(objInput);

		if (jQuery.isEmptyObject(selectors) === true)
			return;

		var type = getInputType(objInput);
		var style;

		switch (type) {
			case "group_selector":
				style = getGroupSelectorsStyle(objInput, selectors);
			break;
			case "typography":
			case "textshadow":
			case "textstroke":
			case "boxshadow":
			case "css_filters":
				style = getSubSettingsSelectorsStyle(objInput, selectors);
			break;
			default:
				style = getInputSelectorsStyle(objInput, selectors);
			break;
		}

		if (!style)
			return;

		var device = getResponsivePickerValue(objInput);

		switch (device) {
			case "tablet":
				style = "@media(max-width:1024px){" + style + "}";
			break;
			case "mobile":
				style = "@media(max-width:768px){" + style + "}";
			break;
		}

		return style;
	}

	/**
	 * process repeater selectors
	 */
	function processRepeaterSelectors(objWrapper) {
		var style = "";

		objWrapper.find(".unite-repeater-item").each(function () {
			var objSettings = jQuery(this).data("objsettings");

			objSettings?.setSelectorWrapperID(g_selectorWrapperID);

			var value = objSettings?.getSettingsValues();
			var css = objSettings?.getSelectorsCss();

			css = processSelectorReplaces(css, { "{{current_item}}": ".elementor-repeater-item-" + value?._generated_id });

			style += css;
		});

		return style;
	}

	/**
	 * get group selectors style
	 */
	function getGroupSelectorsStyle(objWrapper, selectors) {
		var selectorReplace = objWrapper.data("replace");
		var replaces = {};

		for (var selectorPlaceholder in selectorReplace) {
			var inputName = selectorReplace[selectorPlaceholder];
			var objInput = t.getInputByName(inputName);
			var inputReplaces = getInputSelectorReplaces(objInput);

			// skip processing if the input is empty
			if (!inputReplaces)
				return;

			// only inputs that have the "value" placeholder are currently supported
			replaces[selectorPlaceholder] = processSelectorReplaces("{{value}}", inputReplaces);
		}

		return getSelectorsStyle(selectors, replaces);
	}

	/**
	 * get sub settings selectors style
	 */
	function getSubSettingsSelectorsStyle(objWrapper, selectors) {
		var css = objWrapper.data("css") || "";
		var selector = combineSelectors(selectors);

		css = processSelectorReplaces(css, { "{{selector}}": selector });

		return css;
	}

	/**
	 * get input selectors style
	 */
	function getInputSelectorsStyle(objInput, selectors) {
		var replaces = getInputSelectorReplaces(objInput);

		if (!replaces)
			return;

		return getSelectorsStyle(selectors, replaces);
	}


	function _______EVENTS_____(){}

	/**
	 * update events (in case of ajax set)
	 */
	this.updateEvents = function(){

		initControls();
		initSettingsEvents();
		initTipsy();

		if(typeof g_objProvider.onSettingsUpdateEvents == "function")
			g_objProvider.onSettingsUpdateEvents(g_objParent);

	};

	/**
	 * set on change event
	 */
	this.setEventOnChange = function (handler) {
		t.onEvent(t.events.CHANGE, handler);
	};

	/**
	 * set on selectors change event
	 */
	this.setEventOnSelectorsChange = function (handler) {
		t.onEvent(t.events.SELECTORS_CHANGE, handler);
	};

	/**
	 * set on responsive type change event
	 */
	this.setEventOnResponsiveTypeChange = function (handler) {
		t.onEvent(t.events.RESPONSIVE_TYPE_CHANGE, handler);
	};


	/**
	 * update input child placeholders if avilable
	 */
	function updateInputChildPlaceholders(objInput){

		if(!objInput)
			return(false);

		if(objInput.length == 0)
			return(false);

		var arrPlaceholderGroup = objInput.data("placeholder_group");

		if(!arrPlaceholderGroup)
			return(false);

		if(jQuery.isArray(arrPlaceholderGroup) == false)
			return(false);

		var valuePrev = "";

		jQuery.each(arrPlaceholderGroup, function(index, inputID){

			var objChildInput = jQuery("#" + inputID);
			if(objChildInput.length == 0)
				throw new Error("input not found with id: " + inputID);

			if(index > 0){

				objChildInput.attr("placeholder", valuePrev);
				objChildInput.trigger("placeholder_change");
			}

			var value = objChildInput.val();
			if(value !== "")
				valuePrev = value;


		});


	}


	/**
	 * run on setting change
	 */
	this.onSettingChange = function (event, objInput, isInstantChange) {
		if (t.isTriggerChangeDisabled() === true)
			return;

		var dataOldValue = "unite_setting_oldvalue";

		if (isInstantChange === true)
			dataOldValue = "unite_setting_oldvalue_instant";

		if (!objInput)
			objInput = jQuery(event.target);

		if (!objInput || objInput.length === 0)
			return;

		var type = getInputType(objInput);

		if (!type)
			return;
        
		var value = getSettingInputValue(objInput);

		switch (type) {
			case "radio":
			case "select":
			case "items":
			case "map":
				//
			break;
			default:
				//check by value
				var oldValue = objInput.data(dataOldValue);

				if (value === oldValue)
					return;

				objInput.data(dataOldValue, value);
			break;
		}

		//process control change
		processControlSettingChange(objInput);
        
		//trigger event by type
		var hasSelector = isInputHasSelector(objInput);
		var eventName;

		if (hasSelector === true) {
			eventName = t.events.SELECTORS_CHANGE;
		} else {
			if (isInstantChange === true)
				eventName = t.events.INSTANT_CHANGE;
			else
				eventName = t.events.CHANGE;
		}
        
		var name = getInputName(objInput);

		triggerEvent(eventName, {
			name: name,
			value: value,
		});
	};


	/**
	 * trigger event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			params = null;

		if(g_objParent)
			g_objParent.trigger(eventName, params);
	}


	/**
	 * on event name
	 */
	this.onEvent = function(eventName, func){
		validateInited();

		g_objParent.on(eventName,func);
	};


	/**
	 * combine controls to one object, and init control events.
	 */
	function initControls() {
		if (!g_objWrapper)
			return;

		var objControls = g_objWrapper.data("controls");

		if (!objControls)
			return;

		g_objWrapper.removeAttr("data-controls");

		g_arrControls = objControls.parents;
		g_arrChildrenControls = objControls.children;
	}


	/**
	 * init mp3 chooser
	 */
	this.initMp3Chooser = function(objMp3Setting){

		if(objMp3Setting.length == 0)
			return(false);


		objMp3Setting.find(".unite-button-choose").on("click",onChooseMp3Click);
	};



	/**
	 * trigger on keyup
	 */
	this.triggerKeyupEvent = function (objInput, event, funcChange) {
		if (t.isTriggerChangeDisabled() === true)
			return;

		if (!funcChange)
			funcChange = t.onSettingChange;

		// run instant
		funcChange(event, objInput, true);

		g_ucAdmin.runWithTrashold(funcChange, event, objInput);
	};


	/**
	 * init single input event
	 */
	function initInputEvents(objInput, funcChange) {
		
		if (!funcChange)
			funcChange = t.onSettingChange;

		var type = getInputType(objInput);
		var basicType = getInputBasicType(objInput);

        if(type == 'repeater') {
            initRepeater(objInput, funcChange);
            return;
        }
        if(type == 'icon') {
            initIconPicker(objInput, funcChange);
            return;
        }        

        const initialize = () => {

		//init by type
		switch (type) {
			case "color":
				initColorPicker(objInput, funcChange);
			break;
			case "image":
				t.initImageChooser(objInput, funcChange);
			break;
			case "link":
				initLink(objInput, funcChange);
			break;
			case "dimentions":
				initDimentions(objInput, funcChange);
			break;
			case "range":
				initRangeSlider(objInput, funcChange);
			break;
			case "switcher":
				initSwitcher(objInput, funcChange);
			break;
			case "tabs":
				initTabs(objInput);
			break;
			case "typography":
			case "textshadow":
			case "textstroke":
			case "boxshadow":
			case "css_filters":
				initSubSettings(objInput, funcChange);
			break;
			case "addon":
				initAddonPicker(objInput);
			break;
			case "post":
				initPostPicker(objInput);
			break;
			case "post_ids":
				initPostIdsPicker(objInput);
			break;
			case "multiselect":
				objInput.on("input", funcChange);
			break;
			case "select2":
				initSelect2(objInput);
			break;
			case "gallery":
				initGallery(objInput, funcChange);
			break;
			case "buttons_group":
				initButtonsGroup(objInput, funcChange);
			break;
			default:
				//custom setting
				var objCustomType = getCustomSettingType(type);

				if (objCustomType) {
					if (objCustomType.funcInit)
					objCustomType.funcInit(objInput, t);
				} else	//provider setting
					g_ucAdmin.initProviderSettingEvents(type, objInput);
			    break;
			}
	
            //init by base type
            switch (basicType) {
                case "div":
                    //
                break;
                case "checkbox":
                case "radio":
                    objInput.on("click", funcChange);
                break;
                default:
                    objInput.on("change", funcChange);

                    objInput.on("keyup", function (event) {
                        t.triggerKeyupEvent(objInput, event, funcChange);
                        });
                break;
			}
        }

        const observer = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					initialize();
	
					observer.unobserve(entry.target);
				}
			});
		});
		observer.observe(objInput[0]);
	}

	/**
	 * init settings events
	 */
	function initSettingsEvents() {
		
		var objInputs = getObjInputs();

		jQuery.each(objInputs, function () {
			initInputEvents(jQuery(this));
		});

		//init mp3 input events
		var objMp3Settings = g_objParent.find(".unite-setting-mp3");
		t.initMp3Chooser(objMp3Settings);
	}

	/**
	 * init global events - not repeating
	 */
	function initGlobalEvents(){
		g_ucAdmin.onEvent("update_assets_path", onUpdateAssetsPath);
	}

	/**
	 * init options
	 */
	function initOptions(){

		if(!g_objWrapper)
			return(false);

		var objOptions = g_objWrapper.data("options");

		if(typeof objOptions != "object")
			throw new Error("The options should be an object");

		g_objWrapper.removeAttr("data-options");

		var arrOptions = ["show_saps","saps_type","id_prefix"];

		jQuery.each(arrOptions, function(index, optionKey){
			g_options[optionKey] = g_ucAdmin.getVal(objOptions, optionKey, g_options[optionKey]);

			//delete option key
			objOptions[optionKey] = true;
			delete objOptions[optionKey];

		});

		//merge with other options
		jQuery.extend(g_options, objOptions);

		if(g_options["id_prefix"])
			g_IDPrefix = "#"+g_options["id_prefix"];

	}


	/**
	 * update placeholders
	 */
	this.updatePlaceholders = function(objPlaceholders){

		if(!g_objParent)
			return(false);

		jQuery.each(objPlaceholders, function(key, value){

			var objInput = t.getInputByName(key);
			if(!objInput)
				return(true);

			objInput.attr("placeholder", value);
			objInput.trigger("placeholder_change");
		});

	};



	/**
	 * focus first input
	 */
	this.focusFirstInput = function () {
		var focusableTypes = ["text", "textarea"];
		var objInputs = getObjInputs();

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var type = getInputType(objInput);

			if (jQuery.inArray(type, focusableTypes) !== -1) {
				objInput.focus();

				return false;
			}
		});
	};



	/**
	 * destroy settings object
	 */
	this.destroy = function () {
		if (t.isInited() === false)
			return;

		g_ucAdmin.offEvent("update_assets_path");

		var objInputs = g_objParent.find("input,textarea,select").not("input[type='radio']");
		objInputs.off("change");

		var objInputsClick = g_objParent.find("input[type='radio'],.unite-setting-switcher");
		objInputsClick.off("click");

		//destroy control events
		g_objParent.find("select, input").off("change");

		//destroy loaded events
		g_objParent.off(t.events.CHANGE);
		g_objParent.off(t.events.SELECTORS_CHANGE);
		g_objParent.off(t.events.RESPONSIVE_TYPE_CHANGE);

		//destroy tabs events
		if (g_objSapTabs)
			g_objSapTabs.children("a").off("click");

		//destroy accordion events
		if (g_objWrapper)
			g_objWrapper.find(".unite-postbox .unite-postbox-title").off("click");

		g_objProvider.destroyEditors(t);

		//destroy items manager
		if (g_temp.objItemsManager) {
			g_temp.objItemsManager?.destroy();
			g_temp.objItemsManager = null;
			g_objParent.find(".uc-setting-items-panel-button").off("click");
		}

		destroyTabs();
		destroyDimentions();
		destroyColorPickers();
		destroyIconPickers();
		destroyImageChoosers();
		destroyGalleries();
		destroyButtonsGroups();
		destroyLinks();
		destroyRangeSliders();
		destroySubSettings();
		destroyRepeaters();

		//destroy custom setting types
		var objCustomTypes = getCustomSettingType();

		if (objCustomTypes) {
			jQuery.each(objCustomTypes, function (index, objType) {
				if (objType.funcDestroy && g_objParent && g_objParent.length)
					objType.funcDestroy(g_objParent);
			});
		}

		//destroy addon picker
		g_objParent.find(".unite-addonpicker-button").off("click");
		g_objParent.find(".unite-button-primary, .unite-button-secondary").off("click");

		//null parent object so it won't pass the validation
		g_objParent = null;
	};

	/**
	 * set id prefix
	 */
	this.setIDPrefix = function (idPrefix) {
		g_IDPrefix = "#" + idPrefix;
	};

	/**
	 * get id prefix
	 */
	this.getIDPrefix = function () {
		return g_IDPrefix;
	};

	/**
	 * set selector wrapper id
	 */
	this.setSelectorWrapperID = function (id) {
		g_selectorWrapperID = id;
	};

	/**
	 * get wrapper
	 */
	this.getObjWrapper = function () {
		return g_objParent;
	};

	/**
	 * return if the settings are in sidebar
	 */
	this.isSidebar = function(){
		return g_temp.isSidebar;
	};

	/**
	 * run custom command
	 */
	this.runCommand = function(command){

		switch(command){
			case "open_items_panel":
				var objButton = g_objParent.find(".uc-setting-items-panel-button");
				if(objButton.length)
					objButton.trigger("click");
			break;
		}

	};




	/**
	 * add custom type
	 * fields: type, funcInit, funcSetValue, funcGetValue, funcClearValue
	 */
	this.addCustomSettingType = function(type, objType){

		g_ucAdmin.validateObjProperty(objType, ["funcInit",
                               "funcSetValue",
                               "funcGetValue",
                               "funcClearValue",
                               "funcDestroy",
		 ],"custom setting type object");

		var objCustomSettings = getCustomSettingType();

		var existing = g_ucAdmin.getVal(objCustomSettings, type);
		if(existing)
			throw new Error("The custom settings type: "+type+" alrady exists");

		objCustomSettings[type] = objType;

		g_ucAdmin.storeGlobalData(g_temp.customSettingsKey, objCustomSettings);
	};

	/**
	 * determine if the settings are initialized
	 */
	this.isInited = function () {
		return g_objParent !== null;
	};

	/**
	 * init the settings
	 */
	this.init = function (objParent, options) {
		if (!g_ucAdmin)
			g_ucAdmin = new UniteAdminUC();

		g_objParent = objParent;

		if (g_objParent.length > 1) {
			trace(g_objParent);
			throw new Error("Settings must have a single parent");
		}

		if (g_objParent.hasClass("unite_settings_wrapper"))
			g_objWrapper = g_objParent;
		else
			g_objWrapper = g_objParent.children(".unite_settings_wrapper");

		if (g_objWrapper.length === 0)
			g_objWrapper = g_objParent.closest(".unite_settings_wrapper");

		if (g_objWrapper.length === 0)
			g_objWrapper = null;

		if (!g_objWrapper)
			throw new Error("Unable to detect settings wrapper.");
		
		
		g_temp.settingsID = g_objWrapper.prop("id");
		g_temp.isSidebar = g_objWrapper.hasClass("unite-settings-sidebar");
		g_temp.disableExcludeSelector = g_ucAdmin.getVal(options, "disable_exclude_selector");

		t.disableTriggerChange();

		validateInited();

		initOptions();
		initItemsPanel();
        getObjInputs(false, true);
		initRepeaters();

		initSaps();

		initResponsivePicker();
		initUnitsPicker();
		initAnimationsSelector();
		initGlobalEvents();


		t.updateEvents();
		t.clearSettingsInit();

		g_objProvider.initEditors(t);

		t.enableTriggerChange();

		g_temp.isInited = true;

	};

}
