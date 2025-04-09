"use strict";

function UniteSettingsUC(){





	/**
	 * set input value
	 */
	function setInputValue(objInput, value, objValues){
		switch(type){
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
							url: g_ucAdmin.getVal(objValues, name + "_url"),
						};
					} else {
						value = {
							id: g_ucAdmin.getVal(objValues, name + "_imageid"),
							url: value,
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



}
