import {addAction, applyFilters, createHooks, doAction, hasFilter} from "@wordpress/hooks";
let Builder,
	sketch = VueColor.Sketch,
	dummyLightBoxComponent = 'sby-dummy-lightbox-component';


SB_Customizer.initPromise.then((customizer) => {
	const extraMethods =  {
		...customizer.extraMethods,
		updateColorValue : function(id){
			var self = this;
			self.customizerFeedData.settings[id] = (self.customizerFeedData.settings[id].a == 1) ? self.customizerFeedData.settings[id].hex : self.customizerFeedData.settings[id].hex8;
		},

		
		sw_feed_params: function() {
			let sw_feed_param = '';
			if ( this.sw_feed ) {
				sw_feed_param += '&sw-feed=true';
			}
			if ( this.sw_feed_id ) {
				sw_feed_param += '&sw-feed-id=' + this.sw_feed_id;
			}
			return sw_feed_param;
		},

		swfeedReturnUrl: function() {
			var self = this;
			let sw_return_url = '';
			if ( self.sw_feed ) {
				sw_return_url = 'admin.php?page=sbsw#/create-feed'
			}
			if ( self.sw_feed_id ) {
				sw_return_url = 'admin.php?page=sbsw&feed_id=' + self.sw_feed_id
			}
			return sw_return_url;
		},

		customizerStyleMaker : function(){
			var self = this;
			if(self.customizerSidebarBuilder){
				self.feedStyle = '';
				Object.values(self.customizerSidebarBuilder).map( function(tab) {
					self.customizerSectionStyle(tab.sections);
				});
				return '<style type="text/css">' + self.feedStyle + '</style>';
			}
			return false;
		},

		customizerSectionStyle : function(sections){
			var self = this;
			Object.values(sections).map(function(section){
				if(section.controls){
					Object.values(section.controls).map(function(control){
						self.returnControlStyle(control);
					});
				}
				if(section.nested_sections){
					self.customizerSectionStyle(section.nested_sections);
					Object.values(section.nested_sections).map(function(nestedSections){
						Object.values(nestedSections.controls).map(function(nestedControl){
							if(nestedControl.section){
								self.customizerSectionStyle(nestedControl);
							}
						});
					});
				}
			});
		},
		returnControlStyle : function( control ){
			var self = this;
			if(control.style){
				Object.entries(control.style).map( function(css) {
					var condition = control.condition != undefined || control.checkExtension != undefined ? self.checkControlCondition(control.condition, control.checkExtension) : true;
					if( condition ){
						self.feedStyle +=
							css[0] + '{' +
							css[1].replaceAll("{{value}}", self.customizerFeedData.settings[control.id]) +
							'}';
					}
				});
			}
		},

		/**
		 * Ajax Post Action
		 *
		 * @since 2.0
		 */
		ajaxPost : function(data, callback){
			var self = this;
			data['nonce'] = this.nonce;
			self.$http.post(self.ajaxHandler,data).then(callback);
		},

		/**
		 * Show & Hide View
		 *
		 * @since 2.0
		 */
		activateView : function(viewName, sourcePopupType = 'creation', ajaxAction = false){
			var self = this;
			self.viewsActive[viewName] = (self.viewsActive[viewName] == false ) ? true : false;
			self.shouldShowFeedAPIForm = false;
			self.shouldShowManualConnect = false;
			self.shouldShowFeedAPIBackBtn = false;
			
			if(viewName == 'editName'){
				document.getElementById("sbc-csz-hd-input").focus();
			}
			if(viewName === 'feedtypesPopup'){
				self.viewsActive.feedTemplateElement = null;
				document.querySelector('body').classList.toggle('overflow-hidden');
			}
			if(viewName === 'feedtemplatesPopup'){
				self.viewsActive.feedTemplateElement = null;
				document.querySelector('body').classList.toggle('overflow-hidden');
			}
			if(viewName == 'embedPopup' && ajaxAction == true){
				self.saveFeedSettings();
			}

			Builder.$forceUpdate();
		},
				/**
		 * Triggers plugin install lightbox.
		 *
		 * @since 4.0
		 */
		TriggerInstallLightbox: function (plugin) {
			if (plugin.installed && plugin.activated) {
				window.location = plugin.dashboard_permalink;
				return;
			}
			if( plugin?.class ) {
				jQuery(plugin.class).parent('a').trigger('click');
			}
		},
		/**
		 * Switch Customizer Tab
		 *
		 * @sicne 2.0
		 */
		switchCustomizerTab : function(tabId){
			var self = this,
				domBody = document.getElementsByTagName("body")[0];
			self.customizerScreens.activeTab = tabId;
			self.customizerScreens.activeSection = null;
			self.customizerScreens.activeSectionData = null;
			self.highLightedSection = 'all';

			self.dummyLightBoxScreen = false;
			domBody.classList.remove("no-overflow");

			Builder.$forceUpdate();
		},
		switchCustomizerSection : function(sectionId, section, isNested = false, isBackElements){
			var self = this;
			self.customizerScreens.parentActiveSection = null;
			self.customizerScreens.parentActiveSectionData = null;
			if(isNested){
				self.customizerScreens.parentActiveSection = self.customizerScreens.activeSection;
				self.customizerScreens.parentActiveSectionData = self.customizerScreens.activeSectionData;
			}
			self.customizerScreens.activeSection = sectionId;
			self.customizerScreens.activeSectionData = section;
			if(!isBackElements){
				self.enableHighLightSection(sectionId);
			}
		},
		/**
		 * Ajax Action : Save Feed Settings
		 *
		 * @since 2.0
		 */
		saveFeedSettings : function( leavePage = false ){
			var self = this,
				sources = [],
				updateFeedData = {
					action : 'sby_feed_saver_manager_builder_update',
					update_feed	: 'true',
					feed_id : self.customizerFeedData.feed_info.id,
					feed_name : self.customizerFeedData.feed_info.feed_name,
					settings : self.customizerFeedData.settings,
				};
			self.loadingBar = true;
			self.ajaxPost(updateFeedData, function(_ref){
				var data = _ref.data;
				if(data && data.success === true){
					self.processNotification('feedSaved');
					self.customizerFeedDataInitial = self.customizerFeedData;
					if( leavePage === true){
						setTimeout(function(){
							window.location.href = self.builderUrl;
						}, 1500)
					}
				}else{
					self.processNotification('feedSavedError');
				}
			});
			Builder.$forceUpdate();
		},

		/**
		 * Activate license key from license error post grace period header notice 
		 * 
		 * @since 2.0.2
		 */
		activateLicense: function() {
			var self = this;
			
			self.licenseBtnClicked = true;

			if ( self.licenseKey == null ) {
				self.licenseBtnClicked = false;
				return;
			}

			let licenseData = {
				action : 'sby_license_activation',
				nonce : sbc_builder.nonce,
				license_key: self.licenseKey
			};
			self.ajaxPost(licenseData, function(_ref){
				self.licenseBtnClicked = false;
				var data = _ref.data;

				if(data && data.success == false) {
					self.processNotification("licenseError");
					return;
				}
				if( data !== false ){
					self.processNotification("licenseActivated");
					// remove license notices
					self.viewsActive.licenseLearnMore = false;
					jQuery('#sby-license-expired-agp').slideUp();
					jQuery('#sbc-builder-app').removeClass('sbc-builder-app-lite-dismiss');
					jQuery('.sby_get_pro_highlight, .sby_get_cff, .sby_get_sbi, .sby_get_ctf').closest('li').remove();
				}
			})
		},

		/**
		 * Ajax Action : Clear Single Feed Cache
		 * Update Feed Preview Too
		 * @since 2.0
		 */
		clearSingleFeedCache  : function(){
			var self = this,
				sources = [],
				clearFeedData = {
					action : 'sby_feed_saver_manager_clear_single_feed_cache',
					feedID : self.customizerFeedData.feed_info.id,
					feedName : self.customizerFeedData.feed_info.feed_name,
					previewSettings : self.customizerFeedData.settings,
				};
			self.loadingBar = true;
			self.ajaxPost(clearFeedData, function(_ref){
				var data = _ref.data;
				if( data !== false ){
					self.updatedTimeStamp = new Date().getTime();
					self.template = String("<div>"+data.feed_html+"</div>");
					self.processNotification('cacheCleared');
				}else{
					self.processNotification("unkownError");
				}
			})
			Builder.$forceUpdate();
		},


		//Section Checkbox
		changeCheckboxSectionValue : function(settingID, value, ajaxAction = false, checkBoxAction = false){
			var self = this;
			if(checkBoxAction !== false){
				self.customizerFeedData.settings[settingID] = self.customizerFeedData.settings[settingID] == checkBoxAction.options.enabled ? checkBoxAction.options.disabled : checkBoxAction.options.enabled;
			}else{
				var settingValue = self.customizerFeedData.settings[settingID];
				if(!Array.isArray(settingValue) && settingID == 'type'){
					settingValue = [settingValue];
				}
				if(settingValue.includes(value)){
					settingValue.splice(settingValue.indexOf(value),1);
				}else{
					settingValue.push(value);
				}
				if(settingID == 'type'){
					self.processFeedTypesSources( settingValue );
				}
				//settingValue = (settingValue.length == 1 && settingID == 'type') ? settingValue[0] : settingValue;
				self.customizerFeedData.settings[settingID] = settingValue;
			}

			if(ajaxAction !== false){
				self.customizerControlAjaxAction(ajaxAction);
			}
			event.stopPropagation()

		},
		checkboxSectionValueExists : function(settingID = 'includes', value){
			var self = this;
			var settingValue = self.customizerFeedData.settings[settingID];
			return settingValue.includes(value) ? true : false;
		},

		/**
		 * Loading Bar & Notification
		 *
		 * @since 2.0
		 */
		processNotification : function( notificationType ){
			var self = this,
				notification = self.genericText.notification[ notificationType ];
			self.loadingBar = false;
			self.notificationElement =  {
				type : notification.type,
				text : notification.text,
				shown : "shown"
			};
			setTimeout(function(){
				self.notificationElement.shown =  "hidden";
			}, 5000);
		},
		updateInputWidth : function(){
			this.customizerScreens.inputNameWidth = ((document.getElementById("sbc-csz-hd-input").value.length + 6) * 8) + 'px';
		},
		/**
		 * Enable Highlight Section
		 *
		 * @since 2.0
		 */
		enableHighLightSection : function(sectionId){
			var self = this,
				listPostSection = ['customize_feedlayout', 'customize_colorscheme', 'customize_videos','post_style','individual_elements'],
				headerSection = ['customize_header'],
				followButtonSection = ['customize_followbutton'],
				loadeMoreSection = ['customize_loadmorebutton'],
				lightBoxSection = ['customize_lightbox'],
				domBody = document.getElementsByTagName("body")[0];

			self.dummyLightBoxScreen = false;
			domBody.classList.remove("no-overflow");

			if( listPostSection.includes(sectionId) ){
				self.highLightedSection = 'postList';
				self.scrollToHighLightedSection("sbi_images");
			}else if( headerSection.includes(sectionId) ){
				self.highLightedSection = 'header';
				self.scrollToHighLightedSection("sb_instagram_header");
			}else if( followButtonSection.includes(sectionId) ){
				self.highLightedSection = 'followButton';
				self.scrollToHighLightedSection("sbi_load");
			}else if( loadeMoreSection.includes(sectionId) ){
				self.highLightedSection = 'loadMore';
				self.scrollToHighLightedSection("sbi_load");
			}else if( lightBoxSection.includes(sectionId) ){
				self.highLightedSection = 'lightBox';
				self.dummyLightBoxScreen = true;
				document.body.scrollTop = 0;
				document.documentElement.scrollTop = 0;
				domBody.classList.add("no-overflow");
			}else{
				self.highLightedSection = 'all';
				self.dummyLightBoxScreen = false;
				domBody.classList.remove("no-overflow");
			}
		},

		/**
		 * Scroll to Highlighted Section
		 *
		 * @since 2.0
		 */
		scrollToHighLightedSection : function(sectionId){
			const element = document.getElementById(sectionId) !== undefined && document.getElementById(sectionId) !== null ?
				document.getElementById(sectionId) :
				( document.getElementsByClassName(sectionId)[0] !== undefined && document.getElementsByClassName(sectionId)[0] !== null ? document.getElementsByClassName(sectionId)[0] : null );


			if(element != undefined && element != null){
				const y = element.getBoundingClientRect().top - 120 + window.pageYOffset - 10;
				window.scrollTo({top: y, behavior: 'smooth'});
			}
		},

		ctaToggleFeatures: function() {
			var self = this;
			self.freeCtaShowFeatures = !self.freeCtaShowFeatures;
			Builder.$forceUpdate();
		},

		/**
		 * Show Control
		 *
		 * @since 2.0
		 */
		isControlShown : function( control ){
			var self = this;
			if( control.checkViewDisabled != undefined ){
				return !self.viewsActive[control.checkViewDisabled];
			}
			if( control.checkView != undefined ){
				return !self.viewsActive[control.checkView];
			}

			if(control.checkExtension != undefined && control.checkExtension != false && !self.checkExtensionActive(control.checkExtension)){
				return self.checkExtensionActive(control.checkExtension);
			}

			if(control.conditionDimmed != undefined && self.checkControlCondition(control.conditionDimmed) )
				return self.checkControlCondition(control.conditionDimmed);
			if(control.overrideColorCondition != undefined){
				return self.checkControlOverrideColor( control.overrideColorCondition );
			}

			return ( control.conditionHide != undefined && control.condition != undefined || control.checkExtension != undefined )
				? self.checkControlCondition(control.condition, control.checkExtension)
				: true;
		},

		/**
		 * Check Color Override Condition
		 *
		 * @since 2.0
		 */
		checkControlOverrideColor : function(overrideConditionsArray = []){
			var self = this,
				isConditionTrue = 0;
			overrideConditionsArray.map(function(condition, index){
				if(self.checkNotEmpty(self.customizerFeedData.settings[condition]) && self.customizerFeedData.settings[condition].replace(/ /gi,'') != '#'){
					isConditionTrue += 1
				}
			});
			return (isConditionTrue >= 1) ? true : false;
		},
		switchNestedSection : function(sectionId, section){
			var self = this;
			if(section !== null){
				self.customizerScreens.activeSection = sectionId;
				self.customizerScreens.activeSectionData = section;
			}else{
				var sectionArray = sectionId['sections'];
				var elementSectionData = self.customizerSidebarBuilder;

				sectionArray.map(function(elm, index){
					elementSectionData = (elementSectionData[elm] != undefined && elementSectionData[elm] != null) ? elementSectionData[elm] : null;
				});
				if(elementSectionData != null){
					self.customizerScreens.activeSection = sectionId['id'];
					self.customizerScreens.activeSectionData = elementSectionData;
				}
			}
			Builder.$forceUpdate();
		},
		/**
		 * Check Control Condition
		 *
		 * @since 2.0
		 */
		checkControlCondition : function(conditionsArray = [], checkExtensionActive = false, checkExtensionActiveDimmed = false){
			var self = this,
				isConditionTrue = 0;
			Object.keys(conditionsArray).forEach(function(condition, index){
				if(conditionsArray[condition].indexOf(self.customizerFeedData.settings[condition]) !== -1)
					isConditionTrue += 1
			});
			var extensionCondition = checkExtensionActive != undefined && checkExtensionActive != false ? self.checkExtensionActive(checkExtensionActive) : true,
				extensionCondition = checkExtensionActiveDimmed != undefined && checkExtensionActiveDimmed != false && !self.checkExtensionActive(checkExtensionActiveDimmed) ? false : extensionCondition;

			return (isConditionTrue == Object.keys(conditionsArray).length) ? ( extensionCondition ) : false;
		},

		//Change Switcher Settings
		changeSwitcherSettingValue : function(settingID, onValue, offValue, ajaxAction = false) {
			var self = this;
			self.customizerFeedData.settings[settingID] = self.customizerFeedData.settings[settingID] == onValue ? offValue : onValue;
			if(ajaxAction !== false){
				self.customizerControlAjaxAction(ajaxAction);
			}

			self.regenerateLayout(settingID);
		},
		selectedFeedTypeCustomizer : function(feedtype){
			var self 	= this,
				result 	= false;

			if (self.customizerFeedData.settings.type === feedtype) {
				result = true;
			}
			return result;
		},
		selectedFeedTemplateCustomizer : function(feedtemplate){
			var self 	= this,
				result 	= false;
			var self = this, result = false;
			if(
				(self.viewsActive.feedTemplateElement === null && self.customizerFeedData.settings.feedtemplate === feedtemplate) ||
				(self.viewsActive.feedTemplateElement !== null && self.viewsActive.feedTemplateElement == feedtemplate)
			){
				result = true;
			}
			return result;
		},
		chooseCustomizerFeedType: function( feedType ) {
			var self = this;
			self.selectedFeed = feedType.type;
			let ifFeedAvailable = self.hasFeature(self.selectedFeed + '_feeds');

			if ( self.selectedFeed != 'channel' && !self.apiKeyStatus && self.selectedFeed !== 'social_wall' ) {
				self.viewsActive.feedtypesPopup = false;
				self.activateView('accountAPIPopup');
				self.shouldShowFeedAPIForm = true;
				return;
			}

			if ( self.selectedFeed === 'social_wall' ) {
				if ( self.socialWallActivated ) {
					window.location.href = self.socialWallAdminPage;
					return;
				}
				self.activateView('feedtypesPopup');
				self.viewsActive.extensionsPopupElement = 'social_wall';
			} else {
				if( ! ifFeedAvailable) {
					self.viewsActive.extensionsPopupElement = self.selectedFeed;
				} else {
					self.customizerFeedData.settings.type = feedType.type
				}
			}

			Builder.$forceUpdate();
		},
		chooseFeedTemplate: function( feedTemplate, iscustomizerPopup = false ) {
			var self = this;
			self.selectedFeedTemplate = feedTemplate.type;
			if ( iscustomizerPopup ) {
				if ( !self.sbyIsPro || self.sbyLicenseNoticeActive || self.sbyLicenseInactiveState || !self.hasFeature('feeds_templates') ) {
					self.activateView('feedtemplatesPopup');
					self.viewsActive.extensionsPopupElement = 'feedTemplate';
				} else {
					self.viewsActive.feedTemplateElement = feedTemplate.type;
				}
			}
			Builder.$forceUpdate();
		},
		customizerFeedTypePrint : function(){
			var self = this;
			// Support for versions before v4.2
			if ( self.customizerFeedData.settings.type == undefined ) {
				self.customizerFeedData.settings.type = 'default';
			}
			let result = self.feedTypes.filter(function(tp){
				return tp.type === self.customizerFeedData.settings.type
			});
			self.customizerScreens.printedTemplate = result.length > 0 ? result[0] : [];
			return result.length > 0 ? true : false;
		},
		customizerFeedTemplatePrint : function(){
			var self = this;
			// Support for versions before v4.2
			if ( self.customizerFeedData.settings.feedtemplate == undefined ) {
				self.customizerFeedData.settings.feedtemplate = 'default';
			}
			let result = self.feedTemplates.filter(function(tp){
				return tp.type === self.customizerFeedData.settings.feedtemplate
			});
			self.customizerScreens.printedTemplate = result.length > 0 ? result[0] : [];
			return result.length > 0 ? true : false;
		},
		updateFeedTemplateCustomizer : function(){
			var self = this;
			self.customizerFeedData.settings.feedtemplate = self.viewsActive.feedTemplateElement != null ? self.viewsActive.feedTemplateElement : self.customizerFeedData.settings.feedtemplate;
			self.viewsActive.feedTemplateElement = null;
			self.viewsActive.feedtemplatesPopup = false;
			self.customizerControlAjaxAction('feedTemplateFlyPreview');
			Builder.$forceUpdate();
		},
		updateFeedTypeCustomizer : function(){
			var self = this;
			self.viewsActive.feedtypesPopup = false;
			self.customizerControlAjaxAction('feedTypeFlyPreview');
			Builder.$forceUpdate();
		},
		/**
		 * Check if Data Setting is Enabled
		 *
		 * @since 2.0
		 *
		 * @return boolean
		 */
		valueIsEnabled : function(value){
			return value == 1 || value == true || value == 'true' || value == 'on';
		},

		/**
		 * Check if Value is Empty
		 *
		 * @since 2.0
		 *
		 * @return boolean
		 */
		checkNotEmpty : function(value){
			return value != null && value.replace(/ /gi,'') != '';
		},

		/**
		 * Hide Color Picker
		 *
		 * @since 4.0
		 */
		hideColorPickerPospup : function(){
			this.customizerScreens.activeColorPicker = null;
		},

		switchScreen: function(screenType, screenName){
			this.viewsActive[screenType] = screenName;
			Builder.$forceUpdate();
		},

		/**
		 * Parse JSON
		 *
		 * @since 4.0
		 *
		 * @return jsonObject / Boolean
		 */
		jsonParse : function(jsonString){
			try {
				return JSON.parse(jsonString);
			} catch(e) {
				return false;
			}
		},

		/**
		 * Creation process check action
		 *
		 * @since 2.0
		 */
		creationProcessCheckAction : function(){
			var self = this, checkBtnNext = false;
			switch (self.viewsActive.selectedFeedSection) {
				case 'feedsType':
					checkBtnNext = self.selectedFeed != null ? true : false;
					window.ctfSelectedFeed = self.selectedFeed;
					break;
				case 'selectSource':
					checkBtnNext = self.creationProcessCheckAppCredentials();
					break;
				case 'selectTemplate':
					checkBtnNext = self.creationProcessCheckAppCredentials();
					break;
				case 'feedsTypeGetProcess':

					break;
			}
			return checkBtnNext;
		},

		/**
		 * Back to elements
		 *
		 * @since 2.0
		 */
		backToPostElements : function(){
			var self = this,
				individual_elements = self.customizerSidebarBuilder['customize'].sections.customize_videos.nested_sections.individual_elements;
			self.customizerScreens.activeSection = 'customize_videos';
			self.customizerScreens.activeSectionData= self.customizerSidebarBuilder['customize'].sections.customize_videos;
			self.switchCustomizerSection('individual_elements', individual_elements, true, true);
			Builder.$forceUpdate();
		},

		/**
		 * Back to elements
		 *
		 * @since 2.0
		 */
		backToLightboxExperience : function(){
			var self = this,
				lightbox_experience = self.customizerSidebarBuilder['customize'].sections.customize_videos.nested_sections.customize_lightbox;
			self.customizerScreens.activeSection = 'customize_lightbox';
			self.customizerScreens.activeSectionData = self.customizerSidebarBuilder['customize'].sections.customize_videos;
			self.switchCustomizerSection('customize_lightbox', lightbox_experience, true, true);
			Builder.$forceUpdate();
		},

		/**
		 * Creation process next
		 *
		 * @since 2.0
		 */
		creationProcessNext : function(){
			var self = this;
			switch (self.viewsActive.selectedFeedSection) {
				case 'feedsType':
					// if feed type is channel
					if ( self.selectedFeed === 'channel' ) {
						if ( self.connectedAccountStatus || self.apiKeyStatus ) {
							self.switchScreen('selectedFeedSection', 'selectSource');
						}
						if ( !self.connectedAccountStatus && !self.apiKeyStatus ) {
							self.activateView('accountAPIPopup');
						}
						return;
					}

					// if feed type is not channel
					if ( self.selectedFeed !== 'channel' && !self.apiKeyStatus  ) {
						self.activateView('accountAPIPopup');
						self.shouldShowFeedAPIForm = true;
						return;
					}

					if(self.selectedFeed !== null){
						self.switchScreen('selectedFeedSection', 'selectSource');
					}
					break;
				case 'selectSource':
					var selectedFeedTypeSource = self.selectedFeedModel[self.selectedFeed];
					if ( selectedFeedTypeSource ) {
						if ( self.sbyLicenseNoticeActive || ! self.hasFeature('feeds_templates') ) {
							self.isCreateProcessGood = true;
						} else {
							let sourceVerified = self.verifySource();
							if ( sourceVerified ) {
								self.switchScreen('selectedFeedSection', 'selectTemplate');
							} else {
								jQuery('.sbc-select-source-content ul').removeClass('highlight-rules').addClass('highlight-rules')
							}
						}
					}
					break;
				case 'selectTemplate':
					self.isCreateProcessGood = true;
				break;
				case 'feedsTypeGetProcess':
					break;
			}
			if(self.isCreateProcessGood)
				self.submitNewFeed();

		},

		verifySource : function() {
			var self = this;
			let feedType = self.selectedFeed;
			let sourceURL = self.selectedFeedModel[self.selectedFeed];
			var sourceVerified = true;
			
			const regex = /youtube\.com\/(channel|user|playlist|watch)/;
			if (regex.test(sourceURL)) {
				sourceVerified = false;
			}

			return sourceVerified;

		},

		/**
		 * Copy text to clipboard
		 *
		 * @since 2.0
		 */
		copyToClipBoard : function(value){
			var self = this;
			const el = document.createElement('textarea');
			el.className = 'ctf-fb-cp-clpboard';
			el.value = value;
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
			self.notificationElement =  {
				type : 'success',
				text : this.genericText.copiedClipboard,
				shown : "shown"
			};
			setTimeout(function(){
				self.notificationElement.shown =  "hidden";
			}, 3000);
			Builder.$forceUpdate();
		},


		/**
		 * Duplicate Feed
		 *
		 * @since 2.0
		 */
		feedActionDuplicate : function(feed){
			var self = this,
				feedsDuplicateData = {
					action : 'sby_feed_saver_manager_duplicate_feed',
					feed_id : feed.id
				};
			self.ajaxPost(feedsDuplicateData, function(_ref){
				var data = _ref.data;
				self.feedsList = Object.values(Object.assign({}, data));
				//self.feedsList = data;
			});
			Builder.$forceUpdate();
		},

		/**
		 * Select Single Feed in List
		 *
		 * @since 2.0
		 */
		selectFeedCheckBox : function(feedID){
			if(this.feedsSelected.includes(feedID)){
				this.feedsSelected.splice(this.feedsSelected.indexOf(feedID),1);
			}else{
				this.feedsSelected.push(feedID);
			}
			Builder.$forceUpdate();
		},


		/**
		 * Select All Feeds in List
		 *
		 * @since 2.0
		 */
		selectAllFeedCheckBox : function(){
			var self = this;
			if( !self.checkAllFeedsActive() ){
				self.feedsSelected = [];
				self.feedsList.forEach( function(feed) {
					self.feedsSelected.push(feed.id);
				});
			}else{
				self.feedsSelected = [];
			}
		},

		/**
		 * Check if All Feeds are Selected
		 *
		 * @since 2.0
		 */
		checkAllFeedsActive : function(){
			var self = this,
				result = true;
			self.feedsList.forEach( function(feed) {
				if(!self.feedsSelected.includes(feed.id)){
					result = false;
				}
			});

			return result;
		},

		/**
		 * Switch Bulk Action
		 *
		 * @since 2.0
		 */
		bulkActionClick : function(){
			var self = this;
			switch (self.selectedBulkAction) {
				case 'delete':
					if(self.feedsSelected.length > 0){
						self.openDialogBox('deleteMultipleFeeds')
					}
					break;
			}
			Builder.$forceUpdate();
		},

		switchCustomizerPreviewDevice : function(previewScreen){
			var self = this;
			self.customizerScreens.previewScreen = previewScreen;
			self.loadingBar = true;
			self.customizerControlAjaxAction('feedFlyPreview', '', true);
			setTimeout(function(){
				self.setShortcodeGlobalSettings(true);
				self.loadingBar = false;
			},200)
			Builder.$forceUpdate();
		},

		/**
		 * Reset color from the customizer color picker
		 *
		 * @since 2.0
		 */
		resetColor: function(controlId){
			this.customizerFeedData.settings[controlId] = '';
		},

		/**
		 * Show Tooltip on Hover
		 *
		 * @since 2.0
		 */
		 toggleElementTooltip : function(tooltipText, type, align = 'center'){
			var self = this,
				target = window.event.currentTarget,
				tooltip = (target != undefined && target != null) ? document.querySelector('.sb-control-elem-tltp-content') : null;
			if(tooltip != null && type == 'show'){
				self.tooltip.text = tooltipText;
				var position = target.getBoundingClientRect(),
					left = position.left + 10,
					top = position.top - 10;
				tooltip.style.left = left + 'px';
				tooltip.style.top = top + 'px';
                tooltip.style.textAlign = align;
				self.tooltip.hover = true;
			}
			if(type == 'hide'){
				setTimeout(function(){
					if(self.tooltip.hoverType != 'inside'){
						self.tooltip.hover = false;
					}
				}, 200)
			}
		},

		/**
		 * Hover Tooltip
		 *
		 * @since 2.0
		 */
		hoverTooltip : function(type, hoverType){
			this.tooltip.hover = type;
			this.tooltip.hoverType = hoverType;
		},

		reCheckLicenseKey: function() {
			var self = this;
			var licenseNoticeWrapper = document.querySelector('.sb-license-notice');
            this.recheckLicenseStatus = 'loading';
            let data = new FormData();
            data.append( 'action', 'sby_recheck_connection' );
            data.append( 'license_key', self.licenseKey );
            data.append( 'nonce', self.nonce );
            fetch(this.ajaxHandler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if ( data.success == true ) {
                    if ( data.data.license == 'valid' ) {
                        this.recheckLicenseStatus = 'success';
                    }
                    if ( data.data.license != 'valid' ) {
                        this.recheckLicenseStatus = 'error';
                    }
                    // if the api license status has changed from old stored license status
                    // then reload the page to show proper error message and notices
                    // or hide error messages and notices

                    setTimeout(function() {
                        this.recheckLicenseStatus = null;
						if ( data.data.license == 'valid' ) {
							licenseNoticeWrapper.remove();
						}
                    }.bind(this), 3000);
                }
                return;
            });
        },
        recheckBtnText: function( btnName ) {
			var self = this;
            if ( self.recheckLicenseStatus == null ) {
                return self.genericText.recheckLicenseKey;
            } else if ( self.recheckLicenseStatus == 'loading' ) {
                return self.svgIcons['loader'];
            } else if ( self.recheckLicenseStatus == 'success' ) {
                return self.svgIcons.checkmarkCircleSVG + ' ' + self.genericText.licenseValid;
            } else if ( self.recheckLicenseStatus == 'error' ) {
                return self.svgIcons.timesSVG + self.genericText.licenseExpired;
            }
        },

		/**
		 * Back Click in the Creation Process
		 *
		 * @since 2.0
		 */
		creationProcessBack : function(){
			var self = this;
			switch (self.viewsActive.selectedFeedSection) {
				case 'feedsType':
					self.switchScreen('pageScreen', 'welcome');
					break;
				case 'selectSource':
					self.switchScreen('selectedFeedSection', 'feedsType');
					break;
				case 'selectTemplate':
					self.switchScreen('selectedFeedSection', 'selectSource');
					break;
				case 'feedsTypeGetProcess':
					self.switchScreen('selectedFeedSection', 'selectSource');
					break;
			}
			Builder.$forceUpdate();
		},

		/**
		 * Feed List Pagination
		 *
		 * @since 2.0
		 */
		 feedListPagination : function(type){
			var self = this,
				currentPage = self.feedPagination.currentPage,
				pagesNumber = self.feedPagination.pagesNumber;
			self.loadingBar = true;
			if((currentPage != 1 && type == 'prev') || (currentPage <  pagesNumber && type == 'next')){
				self.feedPagination.currentPage = (type == 'next') ?
					(currentPage < pagesNumber ? (parseInt(currentPage) + 1) : pagesNumber) :
					(currentPage > 1 ? (parseInt(currentPage) - 1) : 1);

				var postData = {
	                action : 'sby_feed_saver_manager_get_feed_list_page',
					page : self.feedPagination.currentPage
				};
	            self.ajaxPost(postData, function(_ref){
	                var data = _ref.data;
	                if(data){
	                	self.feedsList = data;
	                }
					self.loadingBar = false;
	            });
				Builder.$forceUpdate();
			}
		},

		/**
		 * Choose Feed Type
		 *
		 * @since 2.0
		 */
		chooseFeedType : function(feedTypeEl, iscustomizerPopup = false){
			var self = this;
			self.selectedFeed = feedTypeEl.type;
			let ifFeedAvailable = self.hasFeature(self.selectedFeed + '_feeds');

			if ( self.selectedFeed === 'social_wall' ) {
				if ( self.socialWallActivated ) {
					window.location.href = self.socialWallAdminPage;
					return;
				}
				self.selectedFeed = null;
				self.viewsActive.extensionsPopupElement = 'social_wall';
			} else {
				if( ! ifFeedAvailable) {
					self.viewsActive.extensionsPopupElement = self.selectedFeed;
					self.selectedFeed = null;
				}
			}

			Builder.$forceUpdate();
		},

		activateProExtPopup: function ( feedType ) {
			var self = this;
			self.viewsActive.extensionsPopupElement = feedType.type;
		},

		/**
		 * Activate API Form
		 * 
		 * @since 2.0
		 */
		activateAPIForm : function() {
			var self = this;
			self.viewsActive.accountAPIPopup = true;
			self.shouldShowFeedAPIForm = true;
		},

		/**
		 * Close Onboarding Process
		 *
		 * @since 2.0
		 */
		onboardingClose : function(){
			var self = this,
				wasActive = self.viewsActive.onboardingPopup ? 'newuser' : 'customizer';

			document.getElementById("sbc-builder-app").classList.remove('sb-onboarding-active');

			self.viewsActive.onboardingPopup = false;
			self.viewsActive.onboardingCustomizerPopup = false;
			self.customizerScreens.activeTab = 'customize';

			self.viewsActive.onboardingStep = 0;
			var postData = {
				action : 'sby_dismiss_onboarding',
				was_active : wasActive
			};
			self.ajaxPost(postData, function(_ref){
				var data = _ref.data;
			});
			Builder.$forceUpdate();
		},

		/**
		 * Onboarding Process Next
		 *
		 * @since 2.0
		 */
		onboardingNext : function(){
			this.viewsActive.onboardingStep ++;
			this.onboardingHideShow();
			Builder.$forceUpdate();
		},

		/**
		 * Onboarding Process Prev
		 *
		 * @since 2.0
		 */
		onboardingPrev : function(){
			this.viewsActive.onboardingStep --;
			this.onboardingHideShow();
			Builder.$forceUpdate();
		},

		/**
		 * Onboarding hide and show
		 *
		 * @since 2.0
		 */
		onboardingHideShow : function() {
			var tooltips = document.querySelectorAll(".sb-onboarding-tooltip");
			for (var i = 0; i < tooltips.length; i++){
				tooltips[i].style.display = "none";
			}
			document.querySelectorAll(".sb-onboarding-tooltip-"+this.viewsActive.onboardingStep)[0].style.display = "block";

			if (this.viewsActive.onboardingCustomizerPopup) {
				if (this.viewsActive.onboardingStep === 2) {
					this.switchCustomizerTab('customize');
				} else if (this.viewsActive.onboardingStep === 3) {
					this.switchCustomizerTab('settings');
				}
			}
		},

		/**
		 * Position onboarding
		 *
		 * @since 2.0
		 */
		positionOnboarding : function() {
			var self = this,
				onboardingElem = document.querySelectorAll(".sb-onboarding-overlay")[0],
				wrapElem = document.getElementById("sbc-builder-app");

			if (onboardingElem === null || typeof onboardingElem === 'undefined') {
				return;
			}

			if (self.viewsActive.onboardingCustomizerPopup && self.iscustomizerScreen && self.customizerFeedData) {
				if (document.getElementById("sb-onboarding-tooltip-customizer-1") !== null) {
					wrapElem.classList.add('sb-onboarding-active');

					var step1El = document.querySelectorAll(".sbc-yt-header")[0];
					step1El.appendChild(document.getElementById("sb-onboarding-tooltip-customizer-1"));

					var step2El = document.querySelectorAll(".sb-customizer-sidebar-sec1")[0];
					step2El.appendChild(document.getElementById("sb-onboarding-tooltip-customizer-2"));

					var step3El = document.querySelectorAll(".sb-customizer-sidebar-sec1")[0];
					step3El.appendChild(document.getElementById("sb-onboarding-tooltip-customizer-3"));

					self.onboardingHideShow();
				}
			} else if (self.viewsActive.onboardingPopup && !self.iscustomizerScreen) {
				if (sbc_builder.allFeedsScreen.onboarding.type === 'single') {
					if (document.getElementById("sb-onboarding-tooltip-single-1") !== null) {
						wrapElem.classList.add('sb-onboarding-active');

						var step1El = document.querySelectorAll(".ctf-fb-wlcm-header .sb-positioning-wrap")[0];
						step1El.appendChild(document.getElementById("sb-onboarding-tooltip-single-1"));

						var step2El = document.querySelectorAll(".ctf-table-wrap")[0];
						step2El.appendChild(document.getElementById("sb-onboarding-tooltip-single-2"));
						self.onboardingHideShow();
					}
				} else {
					if (document.getElementById("sb-onboarding-tooltip-multiple-1") !== null) {
						wrapElem.classList.add('sb-onboarding-active');

						var step1El = document.querySelectorAll(".ctf-fb-wlcm-header .sb-positioning-wrap")[0];
						step1El.appendChild(document.getElementById("sb-onboarding-tooltip-multiple-1"));

						var step2El = document.querySelectorAll(".ctf-fb-lgc-ctn")[0];
						step2El.appendChild(document.getElementById("sb-onboarding-tooltip-multiple-2"));

						var step3El = document.querySelectorAll(".ctf-legacy-table-wrap")[0];
						step3El.appendChild(document.getElementById("sb-onboarding-tooltip-multiple-3"));

						self.activateView('legacyFeedsShown');
						self.onboardingHideShow();
					}
				}
			}
		},

		/**
		 * Customizer Control Ajax
		 * Some of the customizer controls need to perform Ajax
		 * Calls in order to update the preview
		 *
		 * @since 2.0
		 */
		customizerControlAjaxAction : function( actionType, settingID = false, hidePreviewNotification = false ){
			var self = this;
			switch (actionType) {
				case 'feedFlyPreview':
					self.loadingBar = true;
					self.templateRender = false;
					var previewFeedData = {
						action : 'sby_feed_saver_manager_fly_preview',
						feedID : self.customizerFeedData.feed_info.id,
						previewSettings : self.customizerFeedData.settings,
						previewScreen : self.customizerScreens.previewScreen,
						feedName : self.customizerFeedData.feed_info.feed_name,
					};
					self.ajaxPost(previewFeedData, function(_ref){
						var data = _ref.data;
						if( data !== false ){
							self.updatedTimeStamp = new Date().getTime();
							self.template = String("<div>"+data.feed_html+"</div>");
							// document.querySelector('body').classList.toggle('overflow-hidden');
							self.setShortcodeGlobalSettings(true);
							if(!hidePreviewNotification) {
								self.processNotification("previewUpdated");
							} else {
								self.loadingBar = false;
							}
						}else{
							self.processNotification("unkownError");
						}
					});
					break;
				case 'feedTypeFlyPreview':
					self.loadingBar = true;
					self.templateRender = false;
					var previewFeedData = {
						action : 'sby_feed_saver_manager_fly_preview',
						feedID : self.customizerFeedData.feed_info.id,
						previewSettings : self.customizerFeedData.settings,
						feedName : self.customizerFeedData.feed_info.feed_name,
						isFeedTypesPopup : true,
					};
					self.ajaxPost(previewFeedData, function(_ref){
						var data = _ref.data;
						if( data !== false ){
							self.customizerFeedData.settings = data.customizerDataSettings;
							self.updatedTimeStamp = new Date().getTime();
							self.template = String("<div>"+data.feed_html+"</div>");
							document.querySelector('body').classList.toggle('overflow-hidden');
							self.processNotification("previewUpdated");
							self.loadingBar = false;
							setTimeout(function(){
								self.setShortcodeGlobalSettings(true)
							}, 500)
						}else{
							self.processNotification("unkownError");
						}
					});
					break;
				case 'feedTemplateFlyPreview':
					self.loadingBar = true;
					self.templateRender = false;
					var previewFeedData = {
						action : 'sby_feed_saver_manager_fly_preview',
						feedID : self.customizerFeedData.feed_info.id,
						previewSettings : self.customizerFeedData.settings,
						feedName : self.customizerFeedData.feed_info.feed_name,
						isFeedTemplatesPopup : true,
					};
					self.ajaxPost(previewFeedData, function(_ref){
						var data = _ref.data;
						if( data !== false ){
							self.customizerFeedData.settings = data.customizerDataSettings;
							self.updatedTimeStamp = new Date().getTime();
							self.template = String("<div>"+data.feed_html+"</div>");
							self.processNotification("previewUpdated");
							document.querySelector('body').classList.toggle('overflow-hidden');
							self.loadingBar = false;
							setTimeout(function(){
								self.setShortcodeGlobalSettings(true)
							}, 500)
						}else{
							self.processNotification("unkownError");
						}
					});
					break;
				case 'feedRefresh':
					self.loadingBar = true;
					self.templateRender = false;
					var previewFeedData = {
						action : 'sby_feed_refresh',
						feedID : self.customizerFeedData.feed_info.id,
						previewSettings : self.customizerFeedData.settings,
						feedName : self.customizerFeedData.feed_info.feed_name,
					};
					self.ajaxPost(previewFeedData, function(_ref){
						var data = _ref.data;
						if( data !== false ){
							self.customizerFeedData.settings = data.customizerDataSettings;
							self.updatedTimeStamp = new Date().getTime();
							self.template = String("<div>"+data.feed_html+"</div>");
							setTimeout(function(){
								self.setShortcodeGlobalSettings(true);
								self.loadingBar = false;
							},200)
							self.processNotification("previewUpdated");
							self.loadingBar = false;
						}else{
							self.processNotification("unkownError");
						}
					});
					break;
				case 'filtersAndModeration':
					self.loadingBar = true;
					self.templateRender = false;
					var previewFeedData = {
						action : 'sby_feed_saver_manager_fly_preview',
						feedID : self.customizerFeedData.feed_info.id,
						previewSettings : self.customizerFeedData.settings,
						feedName : self.customizerFeedData.feed_info.feed_name,
						clearCache : true,
					};
					self.ajaxPost(previewFeedData, function(_ref){
						var data = _ref.data;
						if( data !== false ){
							self.updatedTimeStamp = new Date().getTime();
							self.template = String("<div>"+data.feed_html+"</div>");
							// document.querySelector('body').classList.toggle('overflow-hidden');
							self.setShortcodeGlobalSettings(true);
							self.processNotification("previewUpdated");
						}else{
							self.processNotification("unkownError");
						}
					});
					break;
				case 'feedPreviewRender':
					setTimeout(function(){
					}, 150);
					break;
				case 'feedHandleFlyPreview':
					self.loadingBar = true;
					self.templateRender = false;
					var previewFeedData = {
						action : 'sby_feed_handle_saver_manager_fly_preview',
						feedID : self.customizerFeedData.feed_info.id,
						previewSettings : self.customizerFeedData.settings,
						feedName : self.customizerFeedData.feed_info.feed_name,
						feedType : self.customizerFeedData.settings.type,
					};
					self.ajaxPost(previewFeedData, function(_ref){
						var data = _ref.data;
						if( data !== false ){
							self.updatedTimeStamp = new Date().getTime();
							self.template = String("<div>"+data.feed_html+"</div>");
							self.setShortcodeGlobalSettings(true);
							self.customizerFeedData.settings = data.customizerDataSettings;
							self.processNotification("previewUpdated");
						}else{
							self.processNotification("unkownError");
						}
					});
					break;
			}
		},
		/**
		 * Clear & Reset Color Override
		 *
		 * @since 4.0
		*/
		resetColorOverride : function(settingID){
			this.customizerFeedData.settings[settingID] = '';
		},
		/**
		 * View Feed Instances
		 *
		 * @since 4.0
		 */
		viewFeedInstances : function(feed){
			var self = this;
			self.viewsActive.instanceFeedActive = feed;
			self.movePopUp();
			Builder.$forceUpdate();
		},
		processDomList : function(selector, attributes){
			document.querySelectorAll(selector).forEach( function(element) {
				attributes.map( function(attrName) {
					element.setAttribute(attrName[0], attrName[1]);
				});
			});
		},
		openTooltipBig : function(){
			var self = this, elem = window.event.currentTarget;
			self.processDomList('.sbc-fb-onbrd-tltp-elem', [['data-active', 'false']]);
			elem.querySelector('.sbc-fb-onbrd-tltp-elem').setAttribute('data-active', 'true');
			Builder.$forceUpdate();
		},
		closeTooltipBig : function(){
			var self = this;
			self.processDomList('.sbc-fb-onbrd-tltp-elem', [['data-active', 'false']]);
			window.event.stopPropagation();
			Builder.$forceUpdate();
		},
		movePopUp : function(){
			var overlay = document.querySelectorAll("sb-fs-boss");
			if (overlay.length > 0) {
				document.getElementById("wpbody-content").prepend(overlay[0]);
			}
		},
		checkObjectArrayElement : function(objectArray, object, byWhat){
			var objectResult = objectArray.filter(function(elem){
				return elem[byWhat] == object[byWhat];
			});
			return (objectResult.length > 0) ? true : false;
		},
		getModerationShoppableMode : function(){
			if( this.viewsActive.moderationMode || this.customizerScreens.activeSection == 'settings_shoppable_feed'){
				this.moderationShoppableMode = true;
			}else{
				this.moderationShoppableMode = false;
			}
			return this.moderationShoppableMode;
		},
		getModerationShoppableModeOffset : function(){
			return this.moderationShoppableModeOffset > 0;
		},

		formatSubscriberCount : function( strings ) {
			console.log(strings);
		},

		hasFeature : function ( feature_name ) {
			var self = this;
			return self.license_tier_features.includes( feature_name );
		}
	};
	const extraData = {
		...customizer.extraData,
		$parent : this,
		nonce : sbc_builder.nonce,
		template :  sbc_builder.feedInitOutput,
		freeCtaShowFeatures : false,
		upgradeUrl : sbc_builder.upgradeUrl,
		supportPageUrl: sbc_builder.supportPageUrl,
		pluginURL : sbc_builder.pluginURL,
		builderUrl 	: sbc_builder.builderUrl,
		pluginType	: sbc_builder.pluginType,
		genericText	: sbc_builder.genericText,
		genericLink	: sbc_builder.genericLink,
		plugins: sbc_builder.installPluginsPopup,
		sourcesScreenText	: sbc_builder.sourcesScreenText,
		apiKeyPopupScreen	: sbc_builder.apiKeyPopupScreen,
		selectTemplate	: sbc_builder.selectTemplate,
		ajaxHandler : sbc_builder.ajaxHandler,
		adminPostURL : sbc_builder.adminPostURL,
		welcomeScreen	 : sbc_builder.welcomeScreen,
		svgIcons 	: sbc_builder.svgIcons,
		license_tier_features : sbc_builder.license_tier_features,
		customizerFeedDataInitial : null,
		customizerFeedData 	: sbc_builder.customizerFeedData,
		customizerHeaderData : sbc_builder.headerData,
		iscustomizerScreen  : (sbc_builder.customizerFeedData != undefined && sbc_builder.customizerFeedData != false),
		selectFeedTypeScreen 	: sbc_builder.selectFeedTypeScreen,
		customizerSidebarBuilder : sbc_builder.customizerSidebarBuilder,
		feedTypes 	: sbc_builder.feedTypes,
		advancedFeedTypes 	: sbc_builder.advancedFeedTypes,
		extensionsPopup 	: sbc_builder.extensionsPopup,
		apiKeyStatus : sbc_builder.apiKeyStatus,
		connectedAccountStatus : sbc_builder.connectedAccountStatus,
		sbyAPIKey : null,
		apiKeyBtnLoader : false,
		apiKeyError : false,
		accessTokenError : false,
		feedStyle : '',
		isCreateProcessGood : false,
		socialWallActivated : sbc_builder.pluginsInfo.social_wall.activated,
		licenseKey : sbc_builder.licenseKey,
		sbyIsPro: (sbc_builder.sbyIsPro === '1'),
		sbyLicenseNoticeActive: (sbc_builder.sbyLicenseNoticeActive === '1'),
		sbyLicenseInactiveState: (sbc_builder.sbyLicenseInactiveState === '1'),
		socialWallAdminPage : sbc_builder.pluginsInfo.social_wall.settingsPage,
		recheckLicenseStatus : null,
		customizerScreens : {
			activeTab 		: 'customize',
			printedType 	: {},
			printedTemplate : {},
			activeSection 	: null,
			previewScreen 	: 'desktop',
			sourceExpanded 	: null,
			sourcesChoosed 	: [],
			inputNameWidth 	: '0px',
			activeSectionData 	: null,
			parentActiveSection : null, //For nested Setions
			parentActiveSectionData : null, //For nested Setions
			activeColorPicker : null
		},
		previewScreens: [
			'desktop',
			'tablet',
			'mobile'
		],
		nestedStylingSection : [
			'playicon_styling_title',
			'video_styling_title',
			'user_styling_title',
			'views_styling_title',
			'countdown_styling_title',
			'stats_styling_title',
			'date_styling_title',
			'description_styling_title',
		],
		embedPopupScreen : sbc_builder.embedPopupScreen,
		customizeScreensText 	: sbc_builder.customizeScreens,
		highLightedSection : 'all',
		dummyLightBoxScreen 	: false,
		dialogBoxPopupScreen   	: sbc_builder.dialogBoxPopupScreen,
		selectFeedTemplateScreen 	: sbc_builder.selectFeedTemplateScreen,
		shouldShowFeedAPIBackBtn : false,
		dialogBox : {
			active : false,
			type : null, //deleteSourceCustomizer
			heading : null,
			description : null,
			customButtons : undefined
		},
		sourceToDelete : {},
		viewsActive : {
			//Screens where the footer widget is disabled
			footerDiabledScreens : [
				'welcome',
				'selectFeed'
			],
			footerWidget : false,

			// welcome, selectFeed
			pageScreen : 'welcome',

			// feedsType, selectSource, feedsTypeGetProcess
			selectedFeedSection : 'feedsType',
			manualSourcePopupInit : sbc_builder.manualSourcePopupInit,
			sourcePopup : false,
			feedtypesPopup : false,
			feedtemplatesPopup : false,
			feedTemplateElement : null,
			feedtypesCustomizerPopup : false,
			sourcesListPopup : false,
			// step_1 [Add New Source] , step_2 [Connect to a user pages/groups], step_3 [Add Manually]
			sourcePopupScreen : 'redirect_1',

			extensionsPopupElement : false,
			// creation or customizer
			sourcePopupType : 'creation',
			accountAPIPopup : false,
			instanceFeedActive : null,
			clipboardCopiedNotif : false,
			legacyFeedsShown : false,
			editName : false,
			embedPopup : false,
			embedPopupScreen : 'step_1',
			embedPopupSelectedPage : null,
			// onboarding
			onboardingPopup : sbc_builder.allFeedsScreen.onboarding.active,
			onboardingStep : 1,
			licenseLearnMore : false,
			whyRenewLicense : false,
			// customizer onboarding
			onboardingCustomizerPopup : sbc_builder.customizeScreens.onboarding.active,

			// plugin install popup
			installPluginPopup : false,
			installPluginModal: 'facebook'
		},
		wordpressPageLists  : sbc_builder.wordpressPageLists,
		widgetsPageURL : sbc_builder.widgetsPageURL,
		feedTemplates: sbc_builder.feedTemplates,
		selectedBulkAction : false,
		selectedFeed : 'channel',
		selectedFeedPopup : [],
		// Selected Feed Template
		selectedFeedTemplate : 'default',
		feedsSelected : [],
		extensionsPopup : sbc_builder.extensionsPopup,
		tooltip : {
			text : '',
			hover : false,
			hoverType : 'outside'
		},
		activeExtensionsFull : sbc_builder.activeExtensions,
		activeExtensions  : [],
		inActiveExtensions  : [],
		//Loading Bar
		fullScreenLoader : false,
		appLoaded : false,
		previewLoaded : false,
		loadingBar : true,
		licenseBtnClicked : false,
		notificationElement : {
			type : 'success', // success, error, warning, message
			text : '',
			shown : null
		},

		//Feeds Pagination
		feedPagination : {
			feedsCount  : sbc_builder.feedsCount != undefined ? sbc_builder.feedsCount : null,
			pagesNumber : 1,
			currentPage : 1,
			itemsPerPage : sbc_builder.itemsPerPage != undefined ? sbc_builder.itemsPerPage : null,
		},
		
		sw_feed: false,
		sw_feed_id: false
	};

	Vue.component( dummyLightBoxComponent , {
		template: '#' + dummyLightBoxComponent,
		props: ['customizerFeedData','parent','dummyLightBoxScreen', 'customizerHeaderData']
	});

	/**
	 * VueJS Global App Builder
	 *
	 * @since 4.0
	 */
	Builder = new Vue({
		el: '#sbc-builder-app',
		http: {
			emulateJSON: true,
			emulateHTTP: true
		},
		components: {
			'sketch-picker': sketch,
		},
		mixins: [VueClickaway.mixin],
		data: extraData,
		updated: function() {
			let self = this;
			if ( self.iscustomizerScreen ) {
				this.setShortcodeGlobalSettings( true );
			}
		},
		computed : {
			feedStyleOutput : function(){
				return this.customizerStyleMaker();
			},
		},
		created : function() {
			var self = this;
			const urlParams = new URLSearchParams(window.location.search);
			// get the socail wall link feed url params
			self.sw_feed = urlParams.get('sw-feed');
			self.sw_feed_id = urlParams.get('sw-feed-id');
			setTimeout(() => {
				const queryString = window.location.search,
					urlParams = new URLSearchParams(queryString),
					page = urlParams.get('page'),
					sby_access_token = urlParams.get('sby_access_token'),
					sby_refresh_token = urlParams.get('sby_refresh_token');

				self.loadingBar = false;
				this.$parent = self;

				if( self.customizerFeedData ){
					self.customizerFeedDataInitial = JSON.parse(JSON.stringify(self.customizerFeedData));
				}

				if(self.customizerFeedData == undefined) {
					self.feedPagination.pagesNumber = self.feedPagination.feedsCount != null ? Math.ceil(self.feedPagination.feedsCount / self.feedPagination.itemsPerPage) : 1;
				}

				// check if access token is available on the feed builder page then switch the screen
				if ( page == 'sby-feed-builder' && sby_access_token && sby_refresh_token ) {
					self.switchScreen('pageScreen', 'selectFeed');
					self.switchScreen('selectedFeedSection', 'selectSource');
				}

				self.activeExtensionsFull.map( element => self.activeExtensions[element['type']] = element['active'] );
				self.activeExtensionsFull.map( element => {
					if( element['active'] == false ){
						element['ids'].forEach((id) => {
							self.inActiveExtensions[id] = element['type'];
						})
					}
				});
				/* Onboarding - move elements so the position is in context */
				self.positionOnboarding();
				document.querySelector('#sbc-builder-app').classList.add('initialized');
			}, 100);
		},
		methods: extraMethods
	})
})