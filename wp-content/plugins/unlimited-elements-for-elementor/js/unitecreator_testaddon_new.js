"use strict";

function UniteCreatorTestAddonNew() {
	var g_slot = 1;
	var g_addonPreview = new UniteAddonPreviewAdmin();

	if (!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();

	/**
	 * init the view
	 */
	this.init = function () {
		g_addonPreview.init();

		initEvents();
	};

	/**
	 * init events
	 */
	function initEvents() {
		jQuery("#uc_testaddon_button_save").on("click", onSaveDataClick);
		jQuery("#uc_testaddon_button_restore").on("click", onRestoreDataClick);
		jQuery("#uc_testaddon_button_delete").on("click", onDeleteDataClick);
		jQuery("#uc_testaddon_button_clear").on("click", onClearClick);
		jQuery("#uc_testaddon_button_check").on("click", onCheckClick);
	}

	/**
	 * on save data event
	 */
	function onSaveDataClick() {
		var values = g_addonPreview.getSettingsValues();

		var data = {
			id: g_addonPreview.getAddonId(),
			settings_values: values,
		};

		trace("saving settings:");
		trace(values);

		g_ucAdmin.setAjaxLoadingButtonID("uc_testaddon_button_save");

		g_ucAdmin.ajaxRequest("save_test_addon", data, function () {
			jQuery("#uc_testaddon_button_restore").show();
			jQuery("#uc_testaddon_button_delete").show();
		});
	}

	/**
	 * on restore data event
	 */
	function onRestoreDataClick() {
		g_ucAdmin.setAjaxLoadingButtonID("uc_testaddon_button_restore");

		g_addonPreview.restoreSlot(g_slot);
	}

	/**
	 * on delete data event
	 */
	function onDeleteDataClick() {
		var data = {
			id: g_addonPreview.getAddonId(),
			slotnum: g_slot,
		};

		g_ucAdmin.setAjaxLoadingButtonID("uc_testaddon_button_delete");

		g_ucAdmin.ajaxRequest("delete_test_addon_data", data, function () {
			jQuery("#uc_testaddon_button_restore").hide();
			jQuery("#uc_testaddon_button_delete").hide();
		});
	}

	/**
	 * on clear event
	 */
	function onClearClick() {
		trace("clear settings");

		g_addonPreview.clearSettings();
	}

	/**
	 * on check event
	 */
	function onCheckClick() {
		var values = g_addonPreview.getSettingsValues();
		var selectorsCss = g_addonPreview.getSelectorsCss();

		trace("settings values:");
		trace(values);

		if (selectorsCss) {
			trace("selectors css:");
			trace(selectorsCss);
		}
	}
}
