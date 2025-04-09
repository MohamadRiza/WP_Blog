<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

if ( ! defined( 'ABSPATH' ) ) exit;

class GlobalsUnlimitedElements{

	public static $enableDashboard = true;
	
	public static $enableElementorSupport = true;
	public static $enableGutenbergSupport = false;
	
	public static $isGutenbergOnly = false;
	
	public static $showAdminNotices = false;		//show the banner
	public static $debugAdminNotices = false;
	
	public static $blackFridayMode = false;
	
	public static $enableApiIntegrations = true;

	public static $enableGoogleAPI = true;
	public static $enableWeatherAPI = true;
	public static $enableCurrencyAPI = true;
	
	public static $enableGoogleCalendarScopes = false;
	public static $enableGoogleYoutubeScopes = false;

	public static $enableInsideNotification = true;
	public static $insideNotificationModal = false;		//inside notification modal open
	
	public static $enableInstagramErrorMessage = false;
  	
	public static $enableLimitProFunctionality = true;	//enable limit pro options in elementor
	
	public static $enableEditProOptions = false;	 //set to enable edit pro options
	
	//public static $insideNotificationText = "🖤 Black Friday Sale! <br> Don’t Miss Out on the <br> BIGGEST SALE of the Year! 🎉<br> <a style='text-decoration:underline;' href='https://unlimited-elements.com/pricing/' target='_blank'>Get Deal Now!</a> ";
	public static $insideNotificationText = "Unlock Access To All Pro Widgets and Features.  <a href='https://unlimited-elements.com/pricing/' target='_blank'>Upgrade Now</a> ";
	public static $insideNotificationUrl = "https://unlimited-elements.com/pricing/";
	

	const PLUGIN_NAME = "unlimitedelements";
	const VIEW_DASHBOARD = "dashboard";
	const VIEW_ADDONS_ELEMENTOR = "addons_elementor";
	const VIEW_LICENSE_ELEMENTOR = "licenseelementor";
	const VIEW_SETTINGS_ELEMENTOR = "settingselementor";
	const VIEW_TEMPLATES_ELEMENTOR = "templates_elementor";
	const VIEW_SECTIONS_ELEMENTOR = "sections_elementor";
	const VIEW_CUSTOM_POST_TYPES = "custom_posttypes";
	const VIEW_ICONS = "svg_shapes";
	const VIEW_BACKGROUNDS = "backgrounds";
	const VIEW_FORM_ENTRIES = "form_entries";
	const VIEW_CHANGELOG = "changelog";
	const VIEW_CHANGELOG_IMPORT = "changelog_import";

	const LINK_BUY = "https://unlimited-elements.com/pricing/";

	const SLUG_BUY_BROWSER = "page=unlimitedelements-pricing";

	const GENERAL_SETTINGS_KEY = "unlimited_elements_general_settings";
	const ADDONSTYPE_ELEMENTOR = "elementor";
	const ADDONSTYPE_ELEMENTOR_TEMPLATE = "elementor_template";
	const ADDONSTYPE_CUSTOM_POSTTYPES = "posttype";

	const PLUGIN_TITLE = "Unlimited Elements";
	const PLUGIN_TITLE_GUTENBERG = "Unlimited Blocks";
	const POSTTYPE_ELEMENTOR_LIBRARY = "elementor_library";
	const META_TEMPLATE_TYPE = '_elementor_template_type';
	const META_TEMPLATE_SOURCE = "_unlimited_template_source";  //the value is unlimited
	const META_TEMPLATE_SOURCE_NAME = "_unlimited_template_sourceid";

	const POSTTYPE_UNLIMITED_ELEMENS_LIBRARY = "unelements_library";

	const ALLOW_FEEDBACK_ONUNINSTALL = false;
	const EMAIL_FEEDBACK = "support@unitecms.net";

	const FREEMIUS_PLUGIN_ID = "4036";

	const GOOGLE_CONNECTION_URL = "https://unlimited-elements.com/google-connect/connect.php";
	const GOOGLE_CONNECTION_CLIENTID = "916742274008-sji12chck4ahgqf7c292nfg2ofp10qeo.apps.googleusercontent.com";

	const LINK_HELP_POSTSLIST = "https://unlimited-elements.helpscoutdocs.com/article/69-post-list-query-usage";

	const PREFIX_ANIMATION_CLASS = "ue-animation-";
	const PREFIX_TEMPLATE_PERMALINK = "unlimited-";

	const FRAME_CACHE_EXPIRE_SECONDS = 28800;	//8 hours
	
	public static $enableCPT = false;
	public static $urlTemplatesList;
	public static $urlAccount;
	public static $renderingDynamicData;
	public static $currentRenderingWidget;
	public static $currentRenderingAddon;	//for ajax
	public static $isImporting = false;
	public static $pluginTitleCurrent;
	
	public static $urlPlugin;
	public static $urlPluginGutenberg;
	
	public static $pathPlugin;
	public static $pathPluginSettings;
	
	public static $isCachedContentOutput;
	
	
	/**
	 * init globals
	 */
	public static function initGlobals(){
		
		self::$pluginTitleCurrent = self::PLUGIN_TITLE;
		
		self::$urlTemplatesList = admin_url("edit.php?post_type=elementor_library&tabs_group=library");

		self::$urlAccount = admin_url("admin.php?page=unlimitedelements-account");

		UniteProviderFunctionsUC::addAction('admin_init', array("GlobalsUnlimitedElements", 'initAdminNotices'));
		
		if(GlobalsUC::$is_admin == true && HelperUC::hasPermissionsFromQuery("showadminnotices"))
			self::$debugAdminNotices = true;
		
			
		//set paths
		
		self::$pathPlugin = dirname(__FILE__)."/";
				
		self::$pathPlugin = UniteFunctionsUC::pathToUnix(self::$pathPlugin);
		
		self::$pathPluginSettings = self::$pathPlugin."settings/";
		
		$pathElementor = self::$pathPlugin."elementor/";

		$pathGutenberg = self::$pathPlugin."gutenberg/";
		
		if(defined("UE_ENABLE_GUTENBERG_SUPPORT")){
			self::$enableGutenbergSupport = true;
		}
		
		
		$serverName = UniteFunctionsUC::getVal($_SERVER, "SERVER_NAME");
		
		if($serverName == "work.unlimited-elements.com"){
			self::$enableGutenbergSupport = true;
		}

		if(defined("UE_DISABLE_ELEMENTOR_SUPPORT")){
			self::$enableElementorSupport = false;
			
			if(self::$enableElementorSupport == false && self::$enableGutenbergSupport == true)
				self::$isGutenbergOnly = true;
		}
		
		if(is_dir($pathElementor) == false && is_dir($pathGutenberg) == true){
			
			self::$isGutenbergOnly = true;
			self::$enableGutenbergSupport = true;
			self::$enableElementorSupport = false;
			
		}

		//debug functions
		if(GlobalsUC::$is_admin == true && HelperUC::hasPermissionsFromQuery("show_debug_function"))
			GlobalsProviderUC::$showDebugFunction = true;
		
	}

	
	/**
	 * init after loaded
	 */
	public static function initAfterPluginsLoaded(){
		
		self::$urlPlugin = HelperProviderCoreUC_EL::$urlCore;

		self::$urlPluginGutenberg = self::$urlPlugin."gutenberg/";
		
		
		if(self::$isGutenbergOnly == true){
			
			self::$pluginTitleCurrent = self::PLUGIN_TITLE_GUTENBERG;
		}
		
		if(defined("UE_ENABLE_FREEPRO_FUNCTIONALITY") == true)
			self::$enableLimitProFunctionality = true;		

		if(self::$enableLimitProFunctionality == false)
			self::$enableEditProOptions = false;	
		
	}
	
	
	/**
	 * init the admin notices
	 */
	public static function initAdminNotices(){

		if(GlobalsUnlimitedElements::$showAdminNotices === false)
			return;
		
		$arrBanners = array();
		
		if(self::$blackFridayMode == true)
			$arrBanners[] = new UCAdminNoticeBFBanner();
		
//			new UCAdminNoticeSimpleExample(),
//			new UCAdminNoticeDoubly(),
//			new UCAdminNoticeRating(),
		
		UCAdminNotices::init($arrBanners);

	}

	
	
}

GlobalsUnlimitedElements::initGlobals();
