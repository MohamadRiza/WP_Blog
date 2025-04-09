<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UniteCreatorPluginIntegrations{
	
	private function ___________JET_ENGINE_________(){}
	
	/**
	 * check if the jet engin exists
	 */
	public static function isJetEngineExists(){
							
		$isExists = class_exists( 'Jet_Engine' );
		
		return($isExists);
	}
	
	private function ___________WP_POPULAR_POSTS_________(){}
	
	/**
	 * return if exists wp popular posts
	 */
	public static function isWPPopularPostsExists(){
		
		$isExists = defined("WPP_VERSION"); 
		
		return($isExists);
	}
	
	/**
	 * get single post views using wpp
	 */
	public static function WPP_getPostViews($postID){
				
		if(self::isWPPopularPostsExists() == false)
			return(0);
		
		if(empty($postID))
			return(0);
			
		if(function_exists("wpp_get_views") == false)
			return(0);
			
		$numViews = wpp_get_views($postID);
		
		return($numViews);
	}
	
	/**
	 * get popular posts
	 * args - post_type, cat, limit, range
	 */
	public function WPP_getPopularPosts($args, $addDebug = false){
		
		$isExists = self::isWPPopularPostsExists();
		
		if($isExists == false)
			return(false);
		
		$postType = UniteFunctionsUC::getVal($args, "post_type");
		
		if(is_array($postType))
			$postType = implode(",",$postType);
		
		if(empty($postType))
			$postType = "post";
		
		$limit = UniteFunctionsUC::getVal($args, "limit", 5);
		$range = UniteFunctionsUC::getVal($args, "range", "last7days");
		$cat = UniteFunctionsUC::getVal($args, "cat", "");
		
		if(is_array($cat))
			$cat = $cat[0];
		
		if($cat == "all")
			$cat = null;
		
		$params = array();
		$params["post_type"] = $postType;
		$params["limit"] = $limit;
		$params["range"] = $range;
		
		if(!empty($cat))
			$params["cat"] = $cat;
		
		$query = new \WordPressPopularPosts\Query($params);
		
		$arrPosts = $query->get_posts();
		
		if(empty($arrPosts))
			$arrPosts = array();
		
		$arrPosts = UniteFunctionsUC::convertStdClassToArray($arrPosts);
		
		$strDebug = "";
		$arrPostIDs = array();
		
		if($addDebug == true){
		
			$strDebug .= "Popular posts query arguments:";
			$strDebug .= "<pre>";
			$strDebug .= print_r($params, true);
			$strDebug .= "</pre>";
	
			$numPosts = count($arrPosts);
			if(!empty($numPosts))
				$strDebug .= "Found $numPosts posts: <br>";
		}
		
		foreach($arrPosts as $index => $post){
			
			$num = $index+1;
			
			$id = UniteFunctionsUC::getVal($post, "id");
			$title = UniteFunctionsUC::getVal($post, "title");
			$pageviews = UniteFunctionsUC::getVal($post, "pageviews");
			
			if($addDebug == true)
				$strDebug .= "{$num}. $title ($id): $pageviews views <br>";
			
			$arrPostIDs[] = $id;
		}
		
		if(empty($arrPosts) && $addDebug == true)
			$strDebug .= "No popular posts found <br>";
		
		//empty the selection if not found
		if(empty($arrPostIDs))
			$arrPostIDs = array("0");
		
		$output = array();
		$output["post_ids"] = $arrPostIDs;
		$output["debug"] = $strDebug;
		
		return($output);
		
		
        // Return cached results
        /*
        if ( $this->config['tools']['cache']['active'] ) {
            $key = 'wpp_' . md5(json_encode($params));
            $query = \WordPressPopularPosts\Cache::get($key);

            if ( false === $query ) {
                $query = new Query($params);

                $time_value = $this->config['tools']['cache']['interval']['value'];
                $time_unit = $this->config['tools']['cache']['interval']['time'];

                // No popular posts found, check again in 1 minute
                if ( ! $query->get_posts() ) {
                    $time_value = 1;
                    $time_unit = 'minute';
                }

                \WordPressPopularPosts\Cache::set(
                    $key,
                    $query,
                    $time_value,
                    $time_unit
                );
            }
        } // Get real-time popular posts
        
		*/
		
        return $query;
	}

	private function ___________STICKY_POSTS_STITCH_________(){}
	
	/**
	 * check if enabled sticky posts switch plugin
	 */
	public static function isStickySwitchPluginEnabled(){
		
		$isExists = class_exists('WP_Sticky_Posts_Switch');
		
		return($isExists);
	}
	
	
	/**
	 * add sticky posts to a post list
	 */
	public static function checkAddStickyPosts($arrPosts, $args){
		
		$isExists = self::isStickySwitchPluginEnabled();
		
		if($isExists == false)
			return($arrPosts);
		
        $arrStickyPostIDs = get_option('sticky_posts');
		
        if(empty($arrStickyPostIDs))
        	return($arrPosts);
                	
        $arrStickyAssoc = UniteFunctionsUC::arrayToAssoc($arrStickyPostIDs);
        	
        $arrPostsNew = array();
        
        $countSticky = 0;
        
        $numOriginal = count($arrPosts);
        
        //remove the sticky from the list to the sticky assoc array if exists
        
        foreach($arrPosts as $post){
        	
        	$postID = $post->ID;

        	$isSticky = isset($arrStickyAssoc[$postID]);
		
        	if($isSticky == false){
        		$arrPostsNew[] = $post;
        		continue;
        	}
        	
        	$arrStickyAssoc[$postID] = $post;        	
        	$countSticky++;
        }
        
        //if all sticky found - then use the array, if not - get new posts
		
		if($countSticky != count($arrStickyAssoc)){
			
			$postType = UniteFunctionsUC::getVal($args, "post_type");
			
			if(empty($postType) || $postType == "post")
				return($arrPosts);
			
			$argsSticky = array();
			$argsSticky["post_type"] = $postType;
			$argsSticky["post__in"] = $arrStickyPostIDs;
			$argsSticky["post_status"] = "publish";
			$argsSticky["nopaging"] = true;
			$argsSticky["orderby"] = "post__in";
			
			$arrStickyAssoc = get_posts($argsSticky);
		}
        
		if(empty($arrStickyAssoc))
			return($arrPosts);
		
		//connect the arrays - sticky at the top
		
		$arrPostsOutput = array_values($arrStickyAssoc);

		$numPostsNew = count($arrPostsOutput);
				
		foreach($arrPostsNew as $post){
			
			$arrPostsOutput[] = $post;
			
			//avoid more then original number of posts
			
			if($numPostsNew >= $numOriginal)
				break;
						
			$numPostsNew++;
		}
		
		
		return($arrPostsOutput);
	}

	private function ___________CONTACT_FORM_7_________(){}
	
	
	/**
	 * check if contact form 7 installed
	 */
	public static function isContactFrom7Installed(){
		
		if(defined("WPCF7_VERSION"))
			return(true);
			
		return(false);
	}
	
	
	/**
	 * get contact from 7 array
	 */
	public static function getArrContactForm7(){
		
		$arrPosts = UniteFunctionsWPUC::getPostsByType("wpcf7_contact_form");
		
		if(empty($arrPosts))
			return(array());
		
		$arrForms = array();
		
		$arrForms["[ Select From ]"] = __("Please Select Contact From 7","unlimited-elements-for-elementor");
		
		foreach($arrPosts as $post){
		
			$postID = $post["ID"];
			
			$title = $post["post_title"];
			
			$title = esc_attr($title);
			
			$shortcode = "[contact-form-7 id=\"{$postID}\" title=\"{$title}\"]";
			
			if(isset($arrForms[$title]))
				$title = "$title ($postID)";
			
			$arrForms[$title] = $shortcode;
		}
		
		
		return($arrForms);
	}
	
	private function ___________JET_WISHLIST_________(){}
	
	/**
	 * put woocommerce jet wishlist button if exist
	 */
	public static function putJetWooWishlistButton(){
		
		
		if(GlobalsProviderUC::$isInsideEditor == true)
			return(false);
		
		if(class_exists("Jet_CW") == false)
			return(false);
		
		$objJetCW = Jet_CW();
		
		if(empty($objJetCW))
			return(false);
		
		$isEnabled = $objJetCW->wishlist_enabled;
		
		if($isEnabled == false)
			return(false);
		
		$objSettings = $objJetCW->settings;
		
		if(empty($objSettings))
			return(false);
		
		$isAddDefault = $objSettings->get("add_default_wishlist_button");
		
		$isAddDefault = UniteFunctionsUC::strToBool($isAddDefault);
		
		if($isAddDefault == false)
			return(false);
					
		if(empty($objJetCW->wishlist_integration))
			return(false);
		
		if(method_exists($objJetCW->wishlist_integration,"add_wishlist_button_default") == false)
			return(false);
		
		$objJetCW->wishlist_integration->add_wishlist_button_default();
		
	}
	
	
	/**
	 * check if installed multi language plugins - polylang or wpml, and add "lang" argument
	 */
	public static function checkPostQueryLanguage($args){
		
		if(function_exists('pll_current_language') || UniteCreatorWpmlIntegrate::isWpmlExists())
			$args['lang'] = UniteFunctionsWPUC::getLanguage();
		
		return($args);
	}
	
	
	
	private function ___________SIMPLE_AUTHOR_BOX_________(){}
	
	
	/**
	 * modify get user data
	 */
	public function saboxGetUserData($arrData){
		
		$userID = UniteFunctionsUC::getVal($arrData, "id");
		
		if(empty($userID))
			return($arrData);
			
		$arrMeta = UniteFunctionsWPUC::getAllUserMeta($userID);
		
		if(empty($arrMeta))
			return($arrData);
			
		$urlProfileImage = UniteFunctionsUC::getVal($arrMeta, "sabox-profile-image");
		
		if(!empty($urlProfileImage))
			$arrData["avatar_url"] = $urlProfileImage;
		
		
		return($arrData);
	}
	
	
	/**
	 * simple author box
	 */
	private function initSABoxIntegration(){
		
		add_filter("unlimited_elements_get_user_data",array($this,"saboxGetUserData"));
				
	}
	
	private function ___________FVPLAYER_________(){}
	
	/**
	 * fvplayer - modify includeby
	 */
	public function fvplayerModifyPostsIncludeby($includeBy){
		
		$includeBy["fvplayers_user_watched"] = __("FVPlayer - User Watched Posts", "unlimited-elements-for-elementor");
		
		return($includeBy);
	}
	
	/**
	 * get custom post id's
	 */
	public function fvplayerGetCustomPostIDs($arrIDs, $includeBY, $limit){
		
		$arrIDs = array();
		
		switch($includeBY){
			case "fvplayers_user_watched":
				
				$arrIDs = fv_player_get_user_watched_post_ids(array("count"=>$limit));
				
				//show debug
				
				if(GlobalsProviderUC::$showPostsQueryDebug == true){
					dmp("FVPlayer - get user recently watched posts by function: fv_player_get_user_watched_post_ids");
					dmp($arrIDs);
				}
				
			break;
		}
				
		if(empty($arrIDs))
			$arrIDs = array();
					
		return($arrIDs);
	}
	
	/**
	 * check if fv player active
	 */
	private function initFvPlayerIntegrations(){
		
		//check if exists
		global $fv_wp_flowplayer_ver;
		
		if(empty($fv_wp_flowplayer_ver))
			return(false);
			
		//double check
		
		if(function_exists("fv_player_get_user_watched_post_ids") == false)
			return(false);
		
		add_filter("ue_modify_post_select_includeby",array($this,"fvplayerModifyPostsIncludeby"));
		
		add_filter("ue_get_custom_includeby_postids",array($this,"fvplayerGetCustomPostIDs"),10,3);
		
	}
	
	private function ___________TRANSLATE_PRESS_________(){}
	
	/**
	 * modify post data
	 */
	public function translatePressModifyPostData($data){
		
		if(empty($data))
			return($data);
		
		if(is_array($data) == false)
			return($data);
			
		$trp = TRP_Translate_Press::get_trp_instance();
		
		if(empty($trp))
			return($data);
		
		$translation_render = $trp->get_component('translation_render');
		
		foreach($data as $key=>$value){
			
			if(is_string($value) == false)
				continue;
			
			$data[$key] = $translation_render->translate_page($value);
		}
		
		
		return($data);
	}
	
	/**
	 * translate press
	 */
	private function initTranslatePressIntegration(){
		
		if(class_exists('TRP_Translate_Press') == false)
			return(false);
		
		add_filter("ue_modify_post_data",array($this,"translatePressModifyPostData"));
	}
	
	private function ___________FAVORITES_PLUGIN_________(){}
	
	/**
	 * favorites plugin posts includeby
	 */
	public function favoritesModifyPostsIncludeby($includeBy){
		
		$includeBy["favorites_get_user_posts"] = __("Favorites Plugin - Get User Posts", "unlimited-elements-for-elementor");

		return($includeBy);
	}
	
	private function ___________RELEVANCY_________(){}
	
	/**
	 * check integrate with relevancy
	 * add "relevancy" if search found and relevancy plugin exists
	 * this function is not functional right now
	 */
	public static function checkPostQueryRelevanssi($args){
		
		if(function_exists("relevanssi_init") == false)
			return($args);
		
		$search = UniteFunctionsUC::getVal($args, "s");
		
		if(empty($search))
			return($args);
		
		$args["relevanssi"] = true;
		
		return($args);
	}
	
	/**
	 * check integrate with relevancy
	 * add "relevancy" if search found and relevancy plugin exists
	 */
	public static function checkDisableRelevanssi($args){
		
		if(function_exists("relevanssi_init") == false)
			return($args);
		
		if(GlobalsProviderUC::$isUnderAjax == false)
			return($args);
		
		$search = UniteFunctionsUC::getVal($args, "s");
		
		if(empty($search))
			return($args);
			
		unset($args["relevanssi"]);		
		
		remove_filter('posts_request', 'relevanssi_prevent_default_request');
				
		return($args);
	}
	
	
	
	private function ___________GENERAL_INIT_INTEGRATIONS_________(){}
		
	
	/**
	 * modify post query integrations
	 */
	public static function modifyPostQueryIntegrations($args){
		
		$args = self::checkDisableRelevanssi($args);	//disable relevanssi plugin
		
		$args = self::checkPostQueryLanguage($args);
		
		return($args);
	}
	
	
	/**
	 * get user post ids
	 */
	public function favoritesGetUserPostIDs($arrIDs, $includeBY, $limit){
		
		$arrIDs = array();
		
		switch($includeBY){
			case "favorites_get_user_posts":
				
				$exists = class_exists("Favorites\Entities\User\UserRepository");
				
				$response = null;
				
				if($exists == true){
					$userRepository = new Favorites\Entities\User\UserRepository();
					$response = $userRepository->getAllFavorites();
				}
				
				if(!empty($response)){
					$arrRespones = $response[0];
					$arrIDs = UniteFunctionsUC::getVal($arrRespones, "posts");
				}
				
				//show debug
				
				if(GlobalsProviderUC::$showPostsQueryDebug == true){
					dmp("Favorites plugin - get usre favorites");
					dmp($arrIDs);
				}
				
			break;
		}
				
		if(empty($arrIDs))
			$arrIDs = array();
					
		return($arrIDs);
		
		
	}
	
	/**
	 * init favoritest plugin integration
	 */
	private function initFavoritesIntegration(){
		
		if(function_exists("favorites_check_versions") == false)
			return(false);
			
		add_filter("ue_modify_post_select_includeby",array($this,"favoritesModifyPostsIncludeby"));
		
		add_filter("ue_get_custom_includeby_postids",array($this,"favoritesGetUserPostIDs"),10,3);
		
	}
	
	/**
	 * init plugin integrations - on plugins loaded
	 */
	public function initPluginIntegrations(){
		
		//simple author box
		
		if(class_exists("Simple_Author_Box"))
			$this->initSABoxIntegration();
		
		$this->initFvPlayerIntegrations();
		
		if(defined("FAVORITES_PLUGIN_FILE"))
			$this->initFavoritesIntegration();
		
		//if(function_exists("trp_enable_translatepress"))
			//$this->initTranslatePressIntegration();
			
	}
	
	
	
}