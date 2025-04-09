import {addFilter, addAction} from "@wordpress/hooks";


SB_Customizer.initPromise = new Promise((resolve) => {
	SB_Customizer.extraData = {
		...SB_Customizer.extraData,
		allFeedsScreen : sbc_builder.allFeedsScreen,
		feedsList : sbc_builder.feeds,
		legacyFeedsList: sbc_builder.legacyFeeds,
		tooltipContent : sbc_builder.feedtypesTooltipContent,
		feedSettingsDomOptions : null,
		selectedFeedModel : {
			channel: sbc_builder.prefilledChannelId,
			playlist: '',
			favorites: sbc_builder.prefilledChannelId,
			search: '',
			live: sbc_builder.prefilledChannelId,
			single: '',
			apiKey: '',
			accessToken: ''
		},
		youtubeAccountConnectURL : sbc_builder.youtubeAccountConnectURL,
		connectSiteParameters: sbc_builder.youtubeAccountConnectParameters,
		prefilledChannelId: sbc_builder.prefilledChannelId,
		dismissLite: sbc_builder.youtube_feed_dismiss_lite,
		shouldShowFeedAPIForm : false,
		shouldShowManualConnect : false,
		showShowYTAccountWarning : false,

		sw_feed: false,
		sw_feed_id: false
	}

	SB_Customizer.extraMethods = {
		...SB_Customizer.extraMethods,
		/**
		 * Change Settings Value
		 *
		 * @since 2.0
		 */
		changeSettingValue : function(settingID, value, doProcess = true, ajaxAction = false) {
			var self = this;
			if(doProcess){
				self.customizerFeedData.settings[settingID] = value;
			}
			if(ajaxAction !== false){
				self.customizerControlAjaxAction(ajaxAction, settingID);
			}
			self.regenerateLayout(settingID);
		},

		checkExtensionActive : function(extension){
			var self = this;
			return self.activeExtensions[extension];
		},

		/**
		 * Should show overlay for the sidebar elements on top
		 * 
		 * @since 2.0
		 */
		shouldShowOverlay(control) {
			var self = this;
			if ( !self.sbyIsPro || 
					self.sbyLicenseNoticeActive || 
					( ( control.checkExtensionPopup == 'call_to_action' || control.checkExtensionPopup == 'advancedFilters' ) && 
					( !self.hasFeature('call_to_actions') || !self.hasFeature('advancedFilters') )
					) 
				) {
				return control.checkExtensionPopup != undefined || (
					control.condition != undefined || 
					control.checkExtension != undefined || 
					control.checkExtensionDimmed != undefined  ? 
					!self.checkControlCondition(control.condition, control.checkExtension, control.checkExtensionDimmed) : 
					false
					);
			} else {
				return control.condition != undefined || 
					control.checkExtension != undefined || 
					control.checkExtensionDimmed != undefined  ? 
					!self.checkControlCondition(control.condition, control.checkExtension, control.checkExtensionDimmed) : 
					false;
			}
		},

		/**
		 * Should show toggleset type cover
		 * 
		 * @since 2.0
		 */
		shouldShowTogglesetCover : function(toggle) {
			var self = this;
			if ( !self.sbyIsPro || self.sbyLicenseNoticeActive ) {
				return toggle.checkExtension != undefined && !self.checkExtensionActive(toggle.checkExtension)
			} else {
				return false
			}
		},

		/**
		 * Open extension popup from toggleset cover
		 * 
		 * @since 2.0
		 */
		togglesetExtPopup : function(toggle) {
			var self = this;
			self.viewsActive.extensionsPopupElement = toggle.checkExtension;
		},

		/**
		 * Shortcode Global Layout Settings
		 *
		 * @since 2.0
		 */
		regenerateLayout : function(settingID) {
			var self = this,
				regenerateFeedHTML = 	[
					'layout'
				],
				relayoutFeed = [
					'layout',
					'carouselarrows',
					'carouselpag',
					'carouselautoplay',
					'carouseltime',
					'carouselloop',
					'carouselrows',
					'cols',
					'colstablet',
					'colsmobile',
					'imagepadding'
				];
			if( relayoutFeed.includes( settingID ) ){
				setTimeout(function(){
					self.setShortcodeGlobalSettings(true);
				}, 200)
			}
		},

		/**
		 * Back to all feeds
		 *
		 * @since 2.0
		 */
		backToAllFeeds : function() {
			var self = this;
			if ( JSON.stringify(self.customizerFeedDataInitial) === JSON.stringify(self.customizerFeedData) ) {
				window.location = self.builderUrl;
			} else {
				self.openDialogBox('backAllToFeed');
			}
		},

		/**
		 * Open Dialog Box
		 *
		 * @since 2.0
		 */
		openDialogBox : function(type, args = []){
			var self = this,
				heading = self.dialogBoxPopupScreen[type].heading,
				description = self.dialogBoxPopupScreen[type].description,
				customButtons = self.dialogBoxPopupScreen[type].customButtons;
			switch (type) {
				case "deleteSingleFeed":
					self.feedToDelete = args;
					heading = heading.replace("#", self.feedToDelete.feed_name);
					break;
			}
			self.dialogBox = {
				active : true,
				type : type,
				heading : heading,
				description : description,
				customButtons : customButtons
			};
			window.event.stopPropagation();
		},

		/**
		 * Confirm Dialog Box Actions
		 *
		 * @since 2.0
		 */
		confirmDialogAction : function(){
			var self = this;
			switch (self.dialogBox.type) {
				case 'deleteSingleFeed':
					self.feedActionDelete([self.feedToDelete.id]);
					break;
				case 'deleteMultipleFeeds':
					self.feedActionDelete(self.feedsSelected);
					break;
				case 'backAllToFeed':
					window.location = self.builderUrl;
					break;
			}
		},

		/**
		 * Delete Feed
		 *
		 * @since 2.0
		 */
		feedActionDelete : function(feeds_ids){
			var self = this,
				feedsDeleteData = {
					action : 'sby_feed_saver_manager_delete_feeds',
					feeds_ids : feeds_ids
				};
			self.ajaxPost(feedsDeleteData, function(_ref){
				var data = _ref.data;
				self.feedsList = Object.values(Object.assign({}, data));
				self.feedsSelected = [];
			});
		},

		/**
		 * Enable & Show Color Picker
		 *
		 * @since 2.0
		 */
		showColorPickerPospup : function(controlId){
			this.customizerScreens.activeColorPicker = controlId;
		},

		/**
		 * Hide Color Picker
		 *
		 * @since 2.0
		 */
		hideColorPickerPopup : function(){
			this.customizerScreens.activeColorPicker = null;
		},

		/**
		 * Get Feed Preview Global CSS Class
		 *
		 * @since 2.0
		 * @return String
		 */
		getPaletteClass : function(context = ''){
			var self = this,
				colorPalette = self.customizerFeedData.settings.colorpalette;

			if(self.checkNotEmpty( colorPalette )){
				var feedID = colorPalette === 'custom'  ? ('_' + self.customizerFeedData.feed_info.id)  : '';
				console.log(colorPalette !== 'inherit' ? ' sby' + context + '_palette_' + colorPalette + feedID: '');
				return colorPalette !== 'inherit' ? ' sby' + context + '_palette_' + colorPalette + feedID: '';
			}
			return '';
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
		 * Get feed container class
		 *
		 * @since 2.0
		 *
		 * @returns string
		 */
		getFeedContainerClasses: function() {
			let self = this;
			let classes = [
				'sb_youtube',
				'sby_layout_' + self.customizerFeedData.settings.layout,
				'sby_col_' + self.getColSettings(),
				'sby_mob_col_' + self.getMobColSettings(),
				'sby_palette_' + self.getColorPaletteClass(),
			];
			return classes.join(' ');
		},

		getColorPaletteClass : function() {
			let self = this;
			if ( self.customizerFeedData.settings.colorpalette == 'custom' ) {
				return self.customizerFeedData.settings.colorpalette + '_' + self.customizerFeedData.feed_info.id;
			} else {
				return self.customizerFeedData.settings.colorpalette;
			}
		},

		/**
		 * Get Col Settings
		 *
		 * @since 2.0
		 */
		getColSettings: function() {
			let self = this;

			if ( self.customizerFeedData.settings['layout'] == 'list' || self.customizerScreens.previewScreen === 'mobile' ) {
				return 0;
			}

			if ( self.customizerFeedData.settings['cols'] ) {
				return self.customizerFeedData.settings['cols'];
			}

			return 0;
		},

		/**
		 * Get Mob Col Settings
		 *
		 * @since 2.0
		 */
		getMobColSettings: function() {
			let self = this;

			if ( self.customizerFeedData.settings['layout'] == 'list' ) {
				return 0;
			}
			if ( self.customizerFeedData.settings['colsmobile'] ) {
				return self.customizerFeedData.settings['colsmobile'];
			}

			return 0;
		},

		/**
		 * Check if header subscribers needs to show
		 *
		 * @since 2.0
		 */
		checkShouldShowSubscribers: function() {
			return this.customizerFeedData.settings.showsubscribe == true ? "shown" : '';
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

		//Change Switcher Settings
		changeSwitcherSettingValue : function(settingID, onValue, offValue, ajaxAction = false, extension) {
			var self = this;
			console.log(extension);
			if (Object.keys(self.inActiveExtensions).includes(settingID)) {
				self.viewsActive.extensionsPopupElement = self.inActiveExtensions[settingID];
			}
			self.customizerFeedData.settings[settingID] = self.customizerFeedData.settings[settingID] == onValue ? offValue : onValue;
			if(ajaxAction !== false){
				self.customizerControlAjaxAction(ajaxAction);
			}
			self.regenerateLayout(settingID);
		},

		/**
		 * Parse JSON
		 *
		 * @since 2.0
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
		 * Get custom header text
		 *
		 * @since 2.0
		 */
		getCustomHeaderText : function() {
			return this.customizerFeedData.settings.customheadertext;
		},

        /**
         * Should show the standard header
         *
         * @since 2.0
         */
        shouldShowStandardHeader: function() {
            let self = this;
            return self.customizerFeedData.settings.showheader && self.customizerFeedData.settings.headerstyle === 'standard';
        },

        /**
         * Should show the text style header
         *
         * @since 2.0
         */
        shouldShowTextHeader: function() {
            let self = this;
            return self.customizerFeedData.settings.showheader && self.customizerFeedData.settings.headerstyle === 'text';
        },



		/**
		 * Get flags attributes
		 *
		 * @since 2.0
		 */
		getFlagsAttr : function( ) {
			let self = this,
				flags = [];

			if ( self.customizerFeedData.settings['disable_resize'] ) {
				flags.push('resizeDisable');
			}
			if ( self.customizerFeedData.settings['favor_local'] ) {
				flags.push('favorLocal');
			}
			if ( self.customizerFeedData.settings['disable_js_image_loading'] ) {
				flags.push('imageLoadDisable');
			}
			if ( self.customizerFeedData.settings['ajax_post_load'] ) {
				flags.push('ajaxPostLoad');
			}
			if ( self.customizerFeedData.settings['playerratio'] === '3:4' ) {
				flags.push('narrowPlayer');
			}
			if ( self.customizerFeedData.settings['disablecdn'] ) {
				flags.push('disablecdn');
			}

			return flags.toString();
		},

        /**
         * Should show gallery layout player
         *
         * @since 2.0
         */
        shouldShowPlayer : function() {
            var self = this;
            if ( self.customizerFeedData.settings.layout != 'gallery' ) {
                return;
            }
            return true;
        },

		/**
		 * Switch to Videos sections
		 * From Feed Layout section bottom link
		 *
		 * @since 2.0
		 */
		switchToVideosSection: function() {
			var self = this;
			self.customizerScreens.parentActiveSection = null;
			self.customizerScreens.parentActiveSectionData = null;
			self.customizerScreens.activeSection = 'customize_videos';
			self.customizerScreens.activeSectionData = self.customizerSidebarBuilder.customize.sections.customize_videos;
		},

		/**
		 * Shortcode Global Layout Settings
		 *
		 * @since 2.0
		 */
		setShortcodeGlobalSettings : function(flyPreview = false){
			let self = this,
				youtubeFeed = jQuery("html").find(".sb_youtube"),
				feedSettings = self.jsonParse(youtubeFeed.attr('data-options')),
				customizerSettings = self.customizerFeedData.settings;

				if ( !youtubeFeed.length ) {
					return;
				}
			if( customizerSettings.layout === 'carousel' ){
				let arrows 		= self.valueIsEnabled( customizerSettings['carouselarrows'] ),
					pag 		= self.valueIsEnabled( customizerSettings['carouselpag'] ),
					autoplay 	= self.valueIsEnabled( customizerSettings['carouselautoplay'] ),
					time 		= autoplay ? parseInt(customizerSettings['carouseltime']) : false,
					loop 		= self.checkNotEmpty(customizerSettings['carouselloop']) && customizerSettings['carouselloop'] !== 'rewind' ? false : true,
					rows 		= customizerSettings['carouselrows']  ? Math.min( parseInt(customizerSettings['carouselrows']), 2) : 1;
				delete feedSettings['gallery'];
				delete feedSettings['masonry'];
				delete feedSettings['grid'];
				feedSettings['carousel'] = [arrows, pag, autoplay, time, loop, rows];
			}
			else if(customizerSettings.layout == 'grid'){
				delete feedSettings['gallery'];
				delete feedSettings['masonry'];
			}
			else if(customizerSettings.layout == 'masonry'){
				delete feedSettings['gallery'];
				delete feedSettings['grid'];
			}
			else if(customizerSettings.layout == 'gallery'){
				delete feedSettings['masonry'];
				delete feedSettings['grid'];
			}

			if(customizerSettings.layout !== 'carousel'){
				delete feedSettings['carousel'];
			}
			youtubeFeed.attr("data-options", JSON.stringify(feedSettings));

			if ( typeof window.sby_init !== 'undefined' && flyPreview ) {
				//setTimeout(function(){
					window.sby_init();
				//}, 2000)
			}
		},

		/**
		 * Should Show Manual Connect
		 * 
		 * @since 2.0
		 */
		showManualConnect : function() {
			var self = this;
			self.shouldShowManualConnect = true;
			self.shouldShowFeedAPIBackBtn = true;
		},

		/**
		 * Should Show Manual Connect
		 * 
		 * @since 2.0
		 */
		showFeedSourceManualConnect : function() {
			var self = this;
			self.viewsActive.accountAPIPopup = true;
			self.shouldShowManualConnect = true;
		},

		/**
		 * Show API connect form in feed creation flow
		 */
		showAPIConnectForm : function() {
			var self = this;
			self.shouldShowFeedAPIForm = true;
			self.shouldShowFeedAPIBackBtn = true;
		},
	
		/**
		 * Show the limitations of connecting with YouTube Account
		 * @since 2.3
		 */
		showYTAccountLimitations : function() {
			var self = this;
			self.showShowYTAccountWarning = true;
		},

		backToApiPopup : function() {
			var self = this;
			self.showShowYTAccountWarning = false;
			self.shouldShowManualConnect = false;
			self.shouldShowFeedAPIForm = false;
			self.shouldShowFeedAPIBackBtn = false;
		},

		/**
		 * Show API connect form in feed creation flow
		 */
		hideAPIConnectForm : function() {
			var self = this;
			self.shouldShowManualConnect = false;
			self.shouldShowFeedAPIForm = false;
			self.shouldShowFeedAPIBackBtn = false;
		},


		/**
		 * Add API Key from the select feed flow
		 * 
		 * @since 2.0
		 */
		addAPIKey : function() {
			var self = this;

			if ( !self.selectedFeedModel.apiKey ) {
				self.apiKeyError = true;
				return;
			}

			var self = this,
				addAPIKeyData = {
					action : 'sby_add_api_key',
					api : self.selectedFeedModel.apiKey
				};
			self.apiKeyBtnLoader = true;
			self.ajaxPost(addAPIKeyData, function(_ref){
				var data = _ref.data;
				self.apiKeyBtnLoader = false;
				self.apiKeyError = false;
				self.apiKeyStatus = true;
				self.activateView('accountAPIPopup');
			});
		},

		/**
		 * Add Access Tokoen from the select feed flow
		 * 
		 * @since 2.0
		 */
		addAccessToken : function() {
			var self = this;

			if ( !self.selectedFeedModel.accessToken ) {
				self.accessTokenError = true;
				return;
			}

			var self = this,
				addAPIKeyData = {
					action : 'sby_manual_access_token',
					sby_access_token : self.selectedFeedModel.accessToken
				};
			self.apiKeyBtnLoader = true;
			self.ajaxPost(addAPIKeyData, function(_ref){
				var data = _ref.data;
				self.apiKeyBtnLoader = false;
				self.accessTokenError = false;
				self.apiKeyStatus = true;
				self.activateView('accountAPIPopup');
			});
		},

		/**
		 * Create & Submit New Feed
		 *
		 * @since 2.0
		 */
		submitNewFeed : function(){
			var self = this,
				newFeedData = {
					action : 'sby_feed_saver_manager_builder_update',
					feedtype : self.selectedFeed,
					feedtemplate : self.selectedFeedTemplate,
					selectedFeedModel : self.selectedFeedModel,
					new_insert : 'true',
				};
			self.fullScreenLoader = true;
			self.ajaxPost(newFeedData, function(_ref){
				var data = _ref.data;
				if(data.feed_id && data.success){
					window.location = self.builderUrl + '&feed_id=' + data.feed_id + self.sw_feed_params();
				}
			});
		},

		/**
		 * Custom field click action
		 * Action
		 * @since 2.3.3
		 */
		fieldCustomClickAction : function( clickAction ){
			var self = this;
			switch (clickAction) {
				case 'clearCommentCache':
					self.clearCommentCache();
				break;
			}
		},

		/**
		 * Clear comment cache action
		 *
		 * @since 2.3.3
		 */
		clearCommentCache : function(){
			var self = this;
			self.loadingBar = true;
			var clearCommentCacheData = {
				action : 'sby_feed_saver_clear_comments_cache',
			};
			self.ajaxPost(clearCommentCacheData, function(_ref){
				var data = _ref.data;
				if( data === 'success' ){
					self.processNotification("commentCacheCleared");
				}else{
					self.processNotification("unkownError");
				}
			});
		},
	}
	resolve(SB_Customizer);
});