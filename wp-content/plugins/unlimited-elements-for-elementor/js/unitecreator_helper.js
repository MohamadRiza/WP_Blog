"use strict";

function UniteCreatorHelper() {
	if (!g_ucAdmin)
		g_ucAdmin = new UniteAdminUC();

	/**
	 * get random string
	 */
	this.getRandomString = g_ucAdmin.getRandomString

	/**
	 * put includes
	 */
	this.putIncludes = function (windowElement, includes, onLoaded) {
		
		var objWindow = jQuery(windowElement.document);
		
		// make a list of handles
		var arrHandles = {};

		jQuery.each(includes, function (index, include) {
			var handle = prepareIncludeHandle(include);

			// skip jquery for now
			if (include.handle === "jquery")
				return;

			arrHandles[handle] = include;
		});

		// load css files and first js file
		var isFirstJS = true;
		
		jQuery.each(includes, function (event, include) {
			if (include.type === "css") {
				loadIncludeFile(include);
			} else { // js (load first only)
				if (isFirstJS === true) {
					isFirstJS = false;

					loadIncludeFile(include);
				}
			}
		});
		
		checkAllFilesLoaded();

		// prepare include handle
		function prepareIncludeHandle(include) {
			return "uc_include_" + include.type + "_" + include.handle;
		}

		// check that all files loaded by handle
		function checkAllFilesLoaded() {
									
			if (jQuery.isEmptyObject(arrHandles) === false)
				return;
			
			if (typeof onLoaded === "function"){
				setTimeout(onLoaded, 500);
			}
			
		}

		// load include file
		function loadIncludeFile(objInclude) {
			
			var handle = prepareIncludeHandle(objInclude);
			var type = objInclude.type;
			var url = objInclude.url;
			var isModule = objInclude.is_module === true;

			// skip jquery for now
			if (objInclude.handle === "jquery") {
				checkAllFilesLoaded();
				onJsFileLoaded();

				return;
			}

			var data = {
				objWindow: objWindow,
				name: "uc_include_file",
				replaceID: handle,
				ismodule: isModule,
			};

			// onload trigger event when all scripts loaded
			data.onload = function (obj, handle) {
								
				var objDomInclude = jQuery(obj);

				objDomInclude.data("isloaded", true);

				// delete the handle from the list, and check for all files loaded
				if (arrHandles.hasOwnProperty(handle) === true) {
					delete arrHandles[handle];
					
					checkAllFilesLoaded();
				}

				var tagName = objDomInclude.prop("tagName").toLowerCase();
				
				if (tagName === "script")
					onJsFileLoaded();
				
			};

			//if file not included - include it
			var objDomInclude = objWindow.find("#" + handle);

			if (objDomInclude.length === 0) {
				loadDOMIncludeFile(type, url, data);
			} else {

				//if the files is in the loading list but still not loaded,
				//wait until they will be loaded and then check for firing the finish event (addons with same files)

				//check if the file is loaded
				var isLoaded = objDomInclude.data("isloaded");

				if (isLoaded === true) {
					//if it's already included - remove from handle
					if (arrHandles.hasOwnProperty(handle) === true)
						delete arrHandles[handle];

					var tagName = objDomInclude.prop("tagName").toLowerCase();

					if (tagName === "script")
						onJsFileLoaded();
				} else {
					var timeoutHandle = setInterval(function () {
						var isLoaded = objDomInclude.data("isloaded");

						if (isLoaded === true) {
							clearInterval(timeoutHandle);

							if (arrHandles.hasOwnProperty(handle) === true)
								delete arrHandles[handle];

							checkAllFilesLoaded();

							var tagName = objDomInclude.prop("tagName").toLowerCase();

							if (tagName === "script")
								onJsFileLoaded();
						}
					}, 100);
				}

			}
		}

		// on js file loaded - load next file
		function onJsFileLoaded() {
						
			for (var index in arrHandles) {
				var include = arrHandles[index];

				if (include.type === "js") {
					loadIncludeFile(include);

					return;
				}
			}
		}
	};

	/**
	 * load include file, js or css
	 */
	function loadDOMIncludeFile(type, url, data) {
		if (!url)
			return;

		var objWindow = g_ucAdmin.getVal(data, "objWindow");
		var name = g_ucAdmin.getVal(data, "name");
		var replaceID = g_ucAdmin.getVal(data, "replaceID");
		var isModule = g_ucAdmin.getVal(data, "ismodule");
		var noRand = g_ucAdmin.getVal(data, "norand");
		var onload = g_ucAdmin.getVal(data, "onload");

		// add random number at the end
		if (!noRand) {
			var rand = Math.floor((Math.random() * 100000) + 1);
			var char = (url.indexOf("?") === -1) ? "?" : "&";

			url += char + "rand=" + rand;
		}

		if (replaceID)
			objWindow.find("#" + replaceID).remove();

		switch (type) {
			case "js":
				var objTag = jQuery("<script />")
					.attr("src", url)
					.attr("type", (isModule === true) ? "module" : null);

				objWindow.find("script:first").before(objTag);
			break;
			case "css":
				var objTag = jQuery("<link />")
					.attr("rel", "stylesheet")
					.attr("type", "text/css")
					.attr("href", url);

				objWindow.find("head").append(objTag);
			break;
			default:
				throw Error("Include type \"" + type + "\" is not implemented.");
		}

		if (replaceID)
			objTag.attr("id", replaceID);

		if (name)
			objTag.attr("name", name);

		if (onload) {
			objTag.attr("onload", function () {
				onload(jQuery(this), replaceID);
			});
		}
	}
}
