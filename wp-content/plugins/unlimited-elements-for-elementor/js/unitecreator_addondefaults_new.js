"use strict";

function UniteCreatorAddonDefaultsAdmin() {
	var g_slot = 2;
	var g_addonPreview = new UniteAddonPreviewAdmin();

	if (!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();

	/**
	 * init the view
	 */
	this.init = function () {
		g_addonPreview.initBySlot(g_slot);

		initEvents();
	};

	/**
	 * init events
	 */
	function initEvents() {
		jQuery("#uc_addondefaults_button_save").on("click", onSaveDataClick);
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

		g_ucAdmin.setAjaxLoadingButtonID("uc_addondefaults_button_save");

		g_ucAdmin.ajaxRequest("save_addon_defaults", data);
	}
}
