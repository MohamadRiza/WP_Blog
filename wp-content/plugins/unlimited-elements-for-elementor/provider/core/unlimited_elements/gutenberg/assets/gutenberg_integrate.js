(function (wp) {
	
	var g_debug = false;
	
	/**
	 * output some string
	 */
	function trace(str){
		console.log(str);
	}
	
	/**
	 * output text only if debug on
	 */
	function debug(str){
		
		if(g_debug == false)
			return(false);
		
		trace(str);
	}
	
	
	var wbe = wp.blockEditor;
	var wc = wp.components;
	var wd = wp.data;
	var we = wp.element;
	var el = we.createElement;
		
	// trigger block focus in case widget prevents clicks (carousels etc.)
	jQuery(document).on("click", ".ue-gutenberg-widget-wrapper", function () {
		jQuery(this).closest("[tabindex]").focus();
	});

	// prevent link clicks inside widgets
	jQuery(document).on("click", ".ue-gutenberg-widget-wrapper a", function (event) {
		event.preventDefault();
	});
	
	
	/**
	 * init in start
	 */
	function initInStart(){
	
		if(g_debug == true){
			trace("start gutenberg integrate");
			trace(g_gutenbergBlocks);
		}
		
		//add debug div
		jQuery(document).ready(function(){
			
			jQuery("body").append("<div id='div_debug' class='unite-div-debug'></div>");
			
		});
				
				
	}
	
	initInStart();
	
	var edit = function (props) {
		
		debug("edit function start");
		
		var previewUrl = props.attributes._preview;

		if (previewUrl)
			return el("img", { src: previewUrl, style: { width: "100%", height: "auto" } });

		var blockProps = wbe.useBlockProps();
		var widgetContentState = we.useState(null);
		var settingsVisibleState = we.useState(false);
		var settingsContentState = we.useState(null);

		var widgetRef = we.useRef(null);
		var widgetLoaderRef = we.useRef(null);
		var widgetRequestRef = we.useRef(null);
		var keepWidgetContentRef = we.useRef(false);
		var ucSettingsRef = we.useRef(new UniteSettingsUC());
		var ucHelperRef = we.useRef(new UniteCreatorHelper());

		var isEditorSidebarOpened = wd.useSelect(function (select) {
			return select("core/edit-post").isEditorSidebarOpened();
		});

		var activeGeneralSidebarName = wd.useSelect(function (select) {
			return select("core/edit-post").getActiveGeneralSidebarName();
		});

		var previewDeviceType = wd.useSelect(function (select) {
			// since version 6.5
			var editor = select("core/editor");

			if (editor.getDeviceType)
				return editor.getDeviceType();

			// fallback
			return select("core/edit-post").__experimentalGetPreviewDeviceType();
		});

		var widgetId = "ue-gutenberg-widget-" + props.clientId;
		var settingsId = "ue-gutenberg-settings-" + props.clientId;
		var settingsTempId = settingsId + "-temp";
		var settingsErrorId = settingsId + "-error";

		var settingsVisible = settingsVisibleState[0];
		var setSettingsVisible = settingsVisibleState[1];

		var settingsContent = settingsContentState[0];
		var setSettingsContent = settingsContentState[1];

		var widgetContent = widgetContentState[0];
		var setWidgetContent = widgetContentState[1];

		var ucSettings = ucSettingsRef.current;
		var ucHelper = ucHelperRef.current;
		
		var initSettings = function (){
						
			ucSettings.destroy();

			var settingsElement = getSettingsElement();

			if (!settingsElement)
				return;
			
			debug("settings element");
			debug(settingsElement);
			
			ucSettings.init(settingsElement);
			ucSettings.setSelectorWrapperID(widgetId);
			ucSettings.setResponsiveType(previewDeviceType.toLowerCase());
			
			ucSettings.setEventOnChange(function () {
								
				saveSettings();
			});

			ucSettings.setEventOnSelectorsChange(function () {
								
				keepWidgetContentRef.current = true;

				saveSettings();

				var css = ucSettings.getSelectorsCss();
				var includes = ucSettings.getSelectorsIncludes();

				jQuery(widgetRef.current).find("[name=uc_selectors_css]").text(css);

				if (includes) {
					var windowElement = getPreviewWindowElement();

					ucHelper.putIncludes(windowElement, includes);
				}
			});

			ucSettings.setEventOnResponsiveTypeChange(function (event, type) {
				var deviceType = type.charAt(0).toUpperCase() + type.substring(1);

				wd.dispatch("core/edit-post").__experimentalSetPreviewDeviceType(deviceType);
			});

			// restore current settings, otherwise apply current
			var values = getSettings();

			if (values !== null)
				ucSettings.setValues(values);
			else
				saveSettings();
		};

		var getSettings = function () {
			return props.attributes.data ? JSON.parse(props.attributes.data) : null;
		};

		var saveSettings = function () {
			props.setAttributes({
				_rootId: ucHelper.getRandomString(5),
				data: JSON.stringify(ucSettings.getSettingsValues()),
			});
		};

		var getSettingsElement = function () {
			
			if (!settingsContent)
				return;

			var settingsElement = jQuery("#" + settingsId);
			var settingsTempElement = jQuery("#" + settingsTempId);

			settingsTempElement.remove();

			if (settingsElement.length)
				return settingsElement;

			settingsTempElement = jQuery("<div id='" + settingsTempId + "' />")
				.hide()
				.html(settingsContent)
				.appendTo("body");

			return settingsTempElement;
		};

		var getPreviewWindowElement = function () {
			return window.frames["editor-canvas"] || window;
		};

		var loadSettingsContent = function () {
			
			debug("Load settings content");
			
			g_ucAdmin.setErrorMessageID(settingsErrorId);
			
			g_ucAdmin.ajaxRequest("get_addon_settings_html", {
				id: props.attributes._id,
				config: getSettings(),
				platform: "gutenberg",
				source: "editor"				
			}, function (response) {
				var html = g_ucAdmin.getVal(response, "html");
				
				setSettingsContent(html);
			});
		};

		var loadWidgetContent = function () {
			
			debug("load widget content");
			
			if (!widgetContent) {
				// load existing widgets from the page
				for (var index in g_gutenbergParsedBlocks) {
					var block = g_gutenbergParsedBlocks[index];

					if (block.name === props.name) {
						setWidgetContent(block.html);
						
						delete g_gutenbergParsedBlocks[index];

						return;
					}
				}
			}

			var settings = getSettings();

			if (!settings)
				return; // wait for the settings
			
			if (widgetRequestRef.current !== null)
				widgetRequestRef.current.abort();

			var loaderElement = jQuery(widgetLoaderRef.current);

			loaderElement.show();
			
			widgetRequestRef.current = g_ucAdmin.ajaxRequest("get_addon_output_data", {
				id: props.attributes._id,
				root_id: props.attributes._rootId,
				platform: "gutenberg",
				source: "editor",
				settings: settings,
				selectors: true,
			}, function (response) {
				var html = g_ucAdmin.getVal(response, "html");
				var includes = g_ucAdmin.getVal(response, "includes");
				var windowElement = getPreviewWindowElement();
				
				debug("put includes");
				debug(includes);
				
				ucHelper.putIncludes(windowElement, includes, function () {
					
					debug("set widget content");
					//debug(html);

					setWidgetContent(html);
				});
			}).always(function () {
				loaderElement.hide();
			});
		};

		we.useEffect(function () {
			// load the settings on the block mount
			loadSettingsContent();

			// remove loaded styles from the page
			jQuery("#unlimited-elements-styles").remove();
			
			return function () {
				// destroy the settings on the block unmount
				ucSettings.destroy();
			};
		}, []);

		we.useEffect(function () {
			// settings are visible if:
			// - the block is selected
			// - the sidebar is opened
			// - the "block" tab is selected
			setSettingsVisible(
				props.isSelected
				&& isEditorSidebarOpened
				&& activeGeneralSidebarName === "edit-post/block"
			);
		}, [props.isSelected, isEditorSidebarOpened, activeGeneralSidebarName]);

		we.useEffect(function () {
			if (ucSettings.isInited())
				ucSettings.setResponsiveType(previewDeviceType.toLowerCase());
		}, [previewDeviceType]);

		we.useEffect(function () {
			if (!settingsVisible)
				return;

			initSettings();
		}, [settingsVisible]);

		we.useEffect(function () {
			if (!settingsContent)
				return;

			initSettings();
		}, [settingsContent]);

		we.useEffect(function () {
			if (!widgetContent)
				return;

			// insert the widget html manually for the inline script to work
			jQuery(widgetRef.current).html(widgetContent);
		}, [widgetContent]);

		we.useEffect(function () {
			if (keepWidgetContentRef.current) {
				keepWidgetContentRef.current = false;
			} else {
				loadWidgetContent();
			}
		}, [props.attributes.data]);

		var settings = el(
			wbe.InspectorControls, {},
			el("div", { className: "ue-gutenberg-settings-error", id: settingsErrorId }),
			settingsContent && el("div", { id: settingsId, dangerouslySetInnerHTML: { __html: settingsContent } }),
			!settingsContent && el("div", { className: "ue-gutenberg-settings-spinner" }, el(wc.Spinner)),
		);

		var widget = el(
			"div", { className: "ue-gutenberg-widget-wrapper" },
			widgetContent && el("div", { className: "ue-gutenberg-widget-content", id: widgetId, ref: widgetRef }),
			widgetContent && el("div", { className: "ue-gutenberg-widget-loader", ref: widgetLoaderRef }, el(wc.Spinner)),
			!widgetContent && el("div", { className: "ue-gutenberg-widget-placeholder" }, el(wc.Spinner)),
		);

		return el("div", blockProps, settings, widget);
	};

	for (var name in g_gutenbergBlocks) {
		var block = g_gutenbergBlocks[name];
		var args = jQuery.extend(block, { edit: edit });

		// convert the svg icon to element
		if (args.icon && args.icon.indexOf("<svg ") === 0)
			args.icon = el("span", { dangerouslySetInnerHTML: { __html: args.icon } });
		
		wp.blocks.registerBlockType(name, args);
	}
})(wp);
