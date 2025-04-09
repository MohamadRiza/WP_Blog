<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UniteCreatorElementorPagination{

	const SHOW_DEBUG = false;		//please turn it off
	
	/**
	 * get data of the controls
	 */
	private function getPaginationControlsData($postListParam){

		$disablePagination = UniteFunctionsUC::getVal($postListParam, "disable_pagination");
		$disablePagination = UniteFunctionsUC::strToBool($disablePagination);

		if($disablePagination == true)
			return(null);

		$condition = UniteFunctionsUC::getVal($postListParam, "condition");

		$isFilterable = UniteFunctionsUC::getVal($postListParam, "is_filterable");
		$isFilterable = UniteFunctionsUC::strToBool($isFilterable);

		$enableAjax = UniteFunctionsUC::getVal($postListParam, "enable_ajax");
		$enableAjax = UniteFunctionsUC::strToBool($enableAjax);

		$paramName = UniteFunctionsUC::getVal($postListParam, "name");

		$data = array();

		$textSection = esc_html__("Posts Pagination", "unlimited-elements-for-elementor");

		if($isFilterable == true)
			$textSection = esc_html__("Posts Pagination and Filtering", "unlimited-elements-for-elementor");

		if($enableAjax == true)
			$textSection = esc_html__("Posts Pagination and Filtering", "unlimited-elements-for-elementor");

			$data["section"] = array(
			"name"=>"section_pagination",
			"label"=>$textSection,
			"condition"=>$condition,
		);

		$arrSettings = array(

			array(
				"name"=>"pagination_heading",
				"type"=>UniteCreatorDialogParam::PARAM_HEADING,
				"label"=>__( 'When turned on, the pagination will appear in archive or single pages, you have option to use the "Posts Pagination" widget for all the styling options', "unlimited-elements-for-elementor"),
				"default"=>""
			),

			array(
				"name"=>"pagination_type",
				"type"=>UniteCreatorDialogParam::PARAM_DROPDOWN,
				"label"=>__( 'Pagination', "unlimited-elements-for-elementor"),
				"default"=>"",
				'options' => array(
					'' => __( 'None', "unlimited-elements-for-elementor"),
					'numbers' => __( 'Numbers', "unlimited-elements-for-elementor"),
					'pagination_widget' => __( 'Using Pagination Widget', "unlimited-elements-for-elementor")
				)
			)

		);

		if($enableAjax == true){

			$arrAjaxSettings = array(

				array(
					"name"=>$paramName.'_isajax',
					"type"=>UniteCreatorDialogParam::PARAM_RADIOBOOLEAN,
					"label"=>__( 'Enable Post Filtering', "unlimited-elements-for-elementor"),
					"default"=>"",
					'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
					'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
					'return_value' => 'true',
					'separator' => 'before',
					'description'=>__('When turned on, you can use all the post filters widgets like tabs filter, load more etc with this grid', 'unlimited-elements-for-elementor')
				),

				array(
					"name"=>$paramName.'_ajax_seturl',
					"type"=>UniteCreatorDialogParam::PARAM_DROPDOWN,
					"label"=>__( 'Filters Behaviour', "unlimited-elements-for-elementor"),
					"default"=>"ajax",
					'options' => array(
						'ajax' => __( 'Ajax', "unlimited-elements-for-elementor"),
						//'url' => __( 'Url Change Only', "unlimited-elements-for-elementor"),
						'mixed' => __( 'Ajax and Url Change', "unlimited-elements-for-elementor"),
						'mixed_back' => __( 'Ajax, Url Change and Back Button', "unlimited-elements-for-elementor")
					),
					'condition' => array($paramName.'_isajax'=>"true"),
					'description'=>__('Choose the filters behaviour for the current grid. If third mode selected - after ajax it will remember the current grid and filters state in the url so you can get back to it later', 'unlimited-elements-for-elementor')
				),

				array(
					"name"=>$paramName.'_filtering_group',
					"type"=>UniteCreatorDialogParam::PARAM_DROPDOWN,
					"label"=>__( 'Group', "unlimited-elements-for-elementor"),
					"default"=>"",
					'options' => array(
						'' => __( '[No Group]', "unlimited-elements-for-elementor"),
						'group1' => __( 'Group1', "unlimited-elements-for-elementor"),
						'group2' => __( 'Group2', "unlimited-elements-for-elementor"),
						'group3' => __( 'Group3', "unlimited-elements-for-elementor"),
						'group4' => __( 'Group4', "unlimited-elements-for-elementor"),
						'group5' => __( 'Group5', "unlimited-elements-for-elementor")
					),
					'condition' => array($paramName.'_isajax'=>"true"),
					'description' => __( 'Allow filtering group of widgets at once', "unlimited-elements-for-elementor")
				),

				array(
					"name"=>$paramName.'_disable_other_hooks',
					"type"=>UniteCreatorDialogParam::PARAM_RADIOBOOLEAN,
					'label' => __( 'Disable Third Party Modifications', "unlimited-elements-for-elementor"),
					"default"=>"",
					'return_value' => 'yes',
					'separator' => 'before',
					'condition' => array($paramName.'_isajax'=>"true"),
					'description'=>__('Disable other themes or plugins hooks so their code so they will not influence on the query', 'unlimited-elements-for-elementor')
				),


			);


			$arrSettings = array_merge($arrSettings, $arrAjaxSettings);
		}



		$data["settings"] = $arrSettings;

		return($data);
	}


	/**
	 * add content controls
	 */
	private function addElementorControls_content($widget, $postListParam){

		$data = $this->getPaginationControlsData($postListParam);

		if(empty($data))
			return(false);


		$arrSection = UniteFunctionsUC::getVal($data, "section");
		$textSection = UniteFunctionsUC::getVal($arrSection, "label");

		$nameSection = UniteFunctionsUC::getVal($arrSection, "name");
		$condition = UniteFunctionsUC::getVal($arrSection, "condition");


		$arrSectionSettings = array(
             'label' => $textSection,
		);

		if(!empty($condition))
			$arrSectionSettings["condition"] = $condition;

		$widget->start_controls_section(
                $nameSection, $arrSectionSettings
         );

        $arrControls = UniteFunctionsUC::getVal($data, "settings");

        foreach($arrControls as $control){

        	$type = UniteFunctionsUC::getVal($control, "type");
        	$name = UniteFunctionsUC::getVal($control, "name");
        	$label = UniteFunctionsUC::getVal($control, "label");
        	$default = UniteFunctionsUC::getVal($control, "default");
        	$options = UniteFunctionsUC::getVal($control, "options");
        	$description = UniteFunctionsUC::getVal($control, "description");
        	$condition = UniteFunctionsUC::getVal($control, "condition");

        	switch($type){
        		case UniteCreatorDialogParam::PARAM_HEADING:

					$widget->add_control(
						$name,
						array(
							'label' => $label,
							'type' => \Elementor\Controls_Manager::HEADING,
							'default' => ''
						)
					);

        		break;
        		case UniteCreatorDialogParam::PARAM_DROPDOWN:

					$widget->add_control(
						$name,
						array(
							'label' => $label,
							'type' => \Elementor\Controls_Manager::SELECT,
							'default' => $default,
							'options' => $options,
							'condition' => $condition,
							'description' => $description
						)
					);

        		break;
        		case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:

        			$returnValue = UniteFunctionsUC::getVal($control, "return_value");

					$widget->add_control(
						$name,
						array(
							'label' => $label,
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
							'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
							'return_value' => $returnValue,
							'default' => '',
							'separator' => 'before',
							'condition' => $condition,
							'description'=>$description
						)
					);

        		break;
        		default:

        			UniteFunctionsUC::throwError("Wrong setting type: $type");

        		break;
        	}
        }


        $widget->end_controls_section();

	}



	/**
	 * add elementor controls
	 */
	public function addElementorSectionControls($widget, $postListParam = null){

		$this->addElementorControls_content($widget,$postListParam);

	}

	/**
	 * add unite settings section
	 */
	public function addUniteSettingsSection($settings, $postListParam = null){

		$data = $this->getPaginationControlsData($postListParam);

		if(empty($data))
			return(false);

		$arrSection = UniteFunctionsUC::getVal($data, "section");

		$textSection = UniteFunctionsUC::getVal($arrSection, "label");
		$nameSection = UniteFunctionsUC::getVal($arrSection, "name");

		$sectionCondition = UniteFunctionsUC::getVal($arrSection, "condition");

		$settings->addSap($textSection, $nameSection);

		//add section control by condition

		if(!empty($sectionCondition)){

			$controlParent = UniteFunctionsUC::getFirstNotEmptyKey($sectionCondition);

			$controlValues = UniteFunctionsUC::getVal($sectionCondition, $controlParent);

			$settings->addControl($controlParent, $nameSection, "show", $controlValues, true);
		}


        $arrControls = UniteFunctionsUC::getVal($data, "settings");

        foreach($arrControls as $control){

        	$type = UniteFunctionsUC::getVal($control, "type");
        	$name = UniteFunctionsUC::getVal($control, "name");
        	$label = UniteFunctionsUC::getVal($control, "label");
        	$default = UniteFunctionsUC::getVal($control, "default");
        	$options = UniteFunctionsUC::getVal($control, "options");
        	$description = UniteFunctionsUC::getVal($control, "description");
        	$condition = UniteFunctionsUC::getVal($control, "condition");

        	$arrParams = array();
        	$arrParams["description"] = $description;

        	switch($type){
        		case UniteCreatorDialogParam::PARAM_HEADING:

        			$settings->addStaticText($label, $name);

        		break;
        		case UniteCreatorDialogParam::PARAM_DROPDOWN:

        			$options = array_flip($options);

        			$settings->addSelect($name, $options, $label, $default, $arrParams);

        		break;
        		case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:

        			$returnValue = UniteFunctionsUC::getVal($control, "return_value");

        			$default = UniteFunctionsUC::strToBool($default);

        			$arrParams["return_value"] = $returnValue;

        			$settings->addRadioBoolean($name, $label, $default, "Yes","No", $arrParams);

        		break;
        		default:

        			UniteFunctionsUC::throwError("Wrong setting type: $type");

        		break;
        	}

        	//add conditions

        	if(!empty($condition))
        		$settings->addControl_byElementorConditions($name, $condition);

        }



	}


	/**
	 * put pagination
	 */
	public function getHTMLPaginationByElementor($arrValues, $isArchivePage){

		$paginationType = UniteFunctionsUC::getVal($arrValues, "pagination_type");

		if($paginationType != "numbers")
			return(false);

		if(is_front_page() == true)
			return(false);

		$options = array();
		$options["prev_next"] = false;

		//$options["mid_size"] = 2;
		//$options["prev_text"] = __( 'Newer', "unlimited-elements-for-elementor");
		//$options["next_text"] = __( 'Older', "unlimited-elements-for-elementor");
		//$options["total"] = 10;
		//$options["current"] = 3;

		if($isArchivePage == true){

			$options = $this->getArchivePageOptions($options);

			$pagination = get_the_posts_pagination($options);
		}
		else{

			$options = $this->getSinglePageOptions($options);

			if(isset($options["current"]) == false)
				return(false);

			$pagination = paginate_links($options);
		}

		$html = "<div class='uc-posts-pagination'>$pagination</div>";
		
		return($html);
	}

	/**
	 * get archive options
	 */
	private function getArchivePageOptions($options){

		//output demo pagination
		$isEditMode = GlobalsProviderUC::$isInsideEditor;
		if($isEditMode == true){
			$options["total"] = 5;
			$options["current"] = 2;
			return($options);
		}

		return($options);
	}

	/**
	 * get ucpage from get options
	 */
	private function getUCPageFromGET(){
		
		$ucpage = UniteFunctionsUC::getGetVar("ucpage","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$ucpage = (int)$ucpage;
				
		if(empty($ucpage))
			return(null);

		return($ucpage);
	}


	/**
	 * get current page
	 */
	private function getCurrentPage(){
		
		//return by ucpage in case ajax request

		$objFilters = new UniteCreatorFiltersProcess();
		$isFrontAjax = $objFilters->isFrontAjaxRequest();
		
		if($isFrontAjax == true){
		
			$ucpage = $this->getUCPageFromGET();

			if(!empty($ucpage))
				return($ucpage);
		}

		$currentPage = 1;
		if(!empty(GlobalsProviderUC::$lastPostQuery_page)){

			$currentPage = GlobalsProviderUC::$lastPostQuery_page;
		}
		else{
			$currentPage = get_query_var("page");
		}

		$currentPage = (int)$currentPage;
		
		
		return($currentPage);
	}

	/**
	 * get total pages from current query
	 */
	private function getTotalPages(){

		if(empty(GlobalsProviderUC::$lastPostQuery))
			return(0);

		$numPages = GlobalsProviderUC::$lastPostQuery->max_num_pages;
		if($numPages <= 1)
			return(0);

		$numPages = (int)$numPages;

		return($numPages);
	}


	/**
	 * get single page options
	 */
	private function getSinglePageOptions($options, $forceFormat = null, $isDebug = false){

		//output demo pagination
		$isEditMode = GlobalsProviderUC::$isInsideEditor;

		if($isEditMode == true){

			if($isDebug == true){
				dmp("edit mode!!!");
			}

			$options["total"] = 5;
			$options["current"] = 2;
			return($options);
		}

		if(empty(GlobalsProviderUC::$lastPostQuery)){

			if($isDebug == true)
				dmp("no last post query");

			return($options);
		}


		$numPages = GlobalsProviderUC::$lastPostQuery->max_num_pages;
		if($numPages <= 1){

			if($isDebug == true)
				dmp("no pages found");

			return($options);
		}

		if($isDebug == true){
			dmp("pagination query:");
			dmp(GlobalsProviderUC::$lastPostQuery->query);
			
			$totalPosts = GlobalsProviderUC::$lastPostQuery->found_posts;
			
			dmp("Total Posts: $totalPosts");
		}

		global $wp_rewrite;
		$isUsingPermalinks = $wp_rewrite->using_permalinks();

		if( $isUsingPermalinks == true){		//with permalinks - add /2

			$permalink = get_permalink();
			
			$isFront = is_front_page();
			$isArchive = UniteFunctionsWPUC::isArchiveLocation();
			
			if($isFront == true){
				$permalink = GlobalsUC::$url_site;
				
				if($isDebug == true)
					dmp("url site permalink".GlobalsUC::$url_site);
			}
			
			if($isArchive == true){
				
				$urlCurrentPage = UniteFunctionsWPUC::getUrlCurrentPage(true);
				
				$permalink = $urlCurrentPage;
				
				if($isDebug == true)
					dmp("take current page as permalink: $urlCurrentPage");
				
			}
			
			$options['base'] = trailingslashit( $permalink ) . '%_%';
							
			if($isDebug)
				dmp("the base: ".$options['base']);
			
			$options['format'] = user_trailingslashit( '%#%', 'single_paged' );

			if($isFront || $isArchive || $forceFormat == "page")
				$options['format'] = user_trailingslashit( 'page/%#%', 'single_paged' );

		}else{		//if not permalinks
			$options['format'] = '?page=%#%';		// add ?page=2
		}

		$options["total"] = $numPages;

		//set current page
		$currentPage = 1;
		if(!empty(GlobalsProviderUC::$lastPostQuery_page)){
			$currentPage = GlobalsProviderUC::$lastPostQuery_page;

			if(self::SHOW_DEBUG == true){
				dmp("current: $currentPage from the lastPostQuery_page var");
			}

		}
		else{
			$currentPage = get_query_var("paged");
			dmp("current: $currentPage from the get_query_var");
		}

		if(empty($currentPage))
			$currentPage = 1;

		$options["current"] = $currentPage;

		return($options);
	}

	/**
	 * get has more by last post query
	 */
	private function getNextOffsetByQuery(){

		//define has more by last post query
		$foundPosts = GlobalsProviderUC::$lastPostQuery->found_posts;

		if(empty($foundPosts))
			return(-1);

		$numPosts = GlobalsProviderUC::$lastPostQuery->post_count;

		$queryVars = GlobalsProviderUC::$lastPostQuery->query_vars;

		$offset = UniteFunctionsUC::getVal($queryVars, "offset");

		if(empty($offset))
			$offset = 0;

		$lastPost = $offset + $numPosts;

		if($lastPost >= $foundPosts)
			return(-1);

		return($lastPost);
	}


	/**
	 * get last request paging data
	 */
	public function getPagingData(){

		$currentPage = $this->getCurrentPage();
		$totalPages = $this->getTotalPages();

		$nextOffset = null;
		
		if(GlobalsProviderUC::$lastPostQuery){

			$currentOffset = GlobalsProviderUC::$lastPostQuery_offset;

			$nextOffset = $this->getNextOffsetByQuery();

			$hasMore = $nextOffset >= 0;


		}else{

			$hasMore = false;
			if(!empty($currentPage) && !empty($totalPages) && $currentPage < $totalPages)
				$hasMore = true;
		}

		$nextPage = $currentPage+1;

		$output = array();
		$output["current"] = $currentPage;
		$output["next"] = $nextPage;
		$output["total"] = $totalPages;
		$output["has_more"] = $hasMore;

		if($hasMore == true)
			$output["next_offset"] = $nextOffset;
		
		
		return($output);
	}


	/**
	 * put pagination widget html
	 */
	public function putPaginationWidgetHtml($args){
		
		
		$putPrevNext = UniteFunctionsUC::getVal($args, "put_prev_next_buttons");
		$putPrevNext = UniteFunctionsUC::strToBool($putPrevNext);

		$midSize = UniteFunctionsUC::getVal($args, "mid_size", 2);
		$midSize = (int)$midSize;

		$endSize = UniteFunctionsUC::getVal($args, "end_size", 1);
		$endSize = (int)$endSize;

		$showAll = UniteFunctionsUC::getVal($args, "show_all");
		$showAll = UniteFunctionsUC::strToBool($showAll);

		$isShowText = UniteFunctionsUC::getVal($args, "show_text");
		$isShowText = UniteFunctionsUC::strToBool($isShowText);

		$prevText = UniteFunctionsUC::getVal($args, "prev_text");
		$nextText = UniteFunctionsUC::getVal($args, "next_text");

		$prevText = trim($prevText);
		$nextText = trim($nextText);

		$isDebug = UniteFunctionsUC::getVal($args, "debug_pagination_options");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);
		
		$addDisabledButtons = UniteFunctionsUC::getVal($args, "add_disabled_buttons");
		$addDisabledButtons = UniteFunctionsUC::strToBool($addDisabledButtons);
		
		
		if(self::SHOW_DEBUG == true)
			$isDebug = true;
		
		if(GlobalsUC::$showQueryDebugByUrl == true)
			$isDebug = true;
		
		$isPaginationDebug = HelperUC::hasPermissionsFromQuery("ucpaginationdebug");
		
		if($isPaginationDebug == true)
			$isDebug = true;
		
		$forceFormat = UniteFunctionsUC::getVal($args, "force_format");
		if($forceFormat == "none")
			$forceFormat = null;
		
		//--------- prepare options

		$options = array();

		$options["show_all"] = $showAll;
		$options["mid_size"] = $midSize;
		$options["end_size"] = $endSize;

		$options["prev_next"] = $putPrevNext;

		if(!empty($prevText))
			$options["prev_text"] = $prevText;

		if(!empty($nextText))
			$options["next_text"] = $nextText;

		if(empty($nextText))
			$options["next_text"] = _x( 'Next', 'next set of posts', "unlimited-elements-for-elementor" );

		if(empty($prevText))
			$options["prev_text"] = _x( 'Previous', 'previous set of posts', "unlimited-elements-for-elementor" );

		//disable the text, leave only icon
		if($isShowText == false){

			$options["next_text"] = "";
			$options["prev_text"] = "";
		}

		//$options["total"] = 10;
		//$options["current"] = 3;
		
		//-------- put pagination html

		$isArchivePage = UniteFunctionsWPUC::isArchiveLocation();
		
		if($isDebug == true){
			echo "<div class='uc-pagination-debug'>";

			dmp("is archive (original): ".$isArchivePage);

			if(!empty($forceFormat))
				dmp("Force Format: ".$forceFormat);
		}

		//on ajax - always take the last grid response - single

		$objFilters = new UniteCreatorFiltersProcess();
		$isAjax = $objFilters->isFrontAjaxRequest();

		if($isAjax == true)
			$isArchivePage = false;

		//fix the archive
		
		//prefer to take by the query
		
		if($isArchivePage == true && !empty(GlobalsProviderUC::$lastPostQuery)){
			
			$isArchivePage = false;
			
			if($isDebug == true){
				dmp("last query found");
				dmp("change to custom");
			}
		}
		
		if($isArchivePage == true && !empty(GlobalsProviderUC::$lastPostQuery_paginationType) && GlobalsProviderUC::$lastPostQuery_paginationType != GlobalsProviderUC::QUERY_TYPE_CURRENT){
			
			$isArchivePage = false;

			if($isDebug == true){
				dmp("last pagination type: ");
				dmp(GlobalsProviderUC::$lastPostQuery_paginationType);

				dmp("change to custom");
			}
		}
		
		//force format yes/no

		switch($forceFormat){
			case "archive":
				$isArchivePage = true;
			break;
			case "custom":
				$isArchivePage = false;
			break;
		}
		
		if($isArchivePage == true){
			
			$options = $this->getArchivePageOptions($options);
			
			$ucpage = $this->getUCPageFromGET();
			
			if(!empty($ucpage))
				$options["current"] = $ucpage;
				
			$pagination = get_the_posts_pagination($options);
						
			//put debug
			if($isDebug == true){
				
				dmP("Archive Pagination");

				global $wp_query;

				if(!empty($wp_query)){

					$queryVars = $wp_query->query_vars;

					$queryVars = UniteFunctionsWPUC::cleanQueryArgsForDebug($queryVars);

					dmp("Current query vars");
					dmp($queryVars);

					dmp("max pages: ".$wp_query->max_num_pages);

					//$currentPage = $this->getCurrentPage();
					//dmp("current page: ".$currentPage);

				}

			}

		}else{		//on single
			
			//skip for home pages
			$options = $this->getSinglePageOptions($options, $forceFormat, $isDebug);
			
			if($isDebug == true)
				dmp("custom query pagination");
			
			if(isset($options["current"]) == false){
				
				if($isDebug == true){
					dmp("<b>Pagination Options (custom) </b>: <br>");
					dmp("No pagination found for the last query <br>");
					dmp($options);
				}

				return(false);
			}
			
			$pagination = paginate_links($options);
		}

	
		if($isDebug == true){

			dmp("<b>Pagination Options</b>: ");
			dmp($options);

			echo "</div>";
		}
		
		// add disabled prev / next buttons if option selected
		
		if($addDisabledButtons == true){
			
			$total = UniteFunctionsUC::getVal($options, "total", -1);
			$current = UniteFunctionsUC::getVal($options, "current", -1);
			
			$prevText = UniteFunctionsUC::getVal($options, "prev_text");
			$nextText = UniteFunctionsUC::getVal($options, "next_text");
			
			if($current === 1)
				$pagination = "<a class='prev page-numbers uc-disabled' href='javascript:void(0)'> {$prevText} </a>".$pagination;
			
			if($current == $total)
				$pagination = $pagination."<a class='next page-numbers uc-disabled' href='javascript:void(0)'> {$nextText} </a>";
		}
		
		
		$addArgs = "data-e-disable-page-transition=\"true\"";
		
		$pagination = $this->addHtmlArguments($pagination, $addArgs);
		
		s_echo($pagination);
	}

	/**
	 * add some arguments to the html links
	 */
	private function addHtmlArguments($pagination, $addArgs){
 		
	    if (empty($addArgs))
	        return $pagination;
	
	    $paginationWithAttributes = preg_replace('/<a\s([^>]*)>/', '<a $1' . " ".$addArgs . '>', $pagination);
		
	    return $paginationWithAttributes;		
	}
	
	/**
	 * get load more data
	 */
	public function getLoadmoreData($isEditMode = false){

		//editor mode
		if($isEditMode == true){

			$output = array();
			$output["attributes"] = "";
			$output["style"] = "";
			$output["more"] = true;

			return($output);
		}

		$arrPagingData = $this->getPagingData();
		
		$hasMore = UniteFunctionsUC::getVal($arrPagingData, "has_more");

		$nextPage = UniteFunctionsUC::getVal($arrPagingData, "next");

		$nextOffset = UniteFunctionsUC::getVal($arrPagingData, "next_offset");

		$attributes = "";
		$style = "";

		if($hasMore == true){
			$attributes = "data-more=\"$hasMore\"";

			if(!empty($nextOffset))
				$attributes .= " data-nextoffset=\"$nextOffset\" ";
			else
				$attributes .= " data-nextpage=\"$nextPage\" ";

		}
		else
			$style = "style='display:none'";


		$output = array();
		$output["attributes"] = $attributes;
		$output["style"] = $style;
		$output["more"] = $hasMore;


		return($output);
	}


}
