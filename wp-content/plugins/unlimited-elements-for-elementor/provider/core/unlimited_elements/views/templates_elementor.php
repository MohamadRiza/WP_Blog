<?php

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

require HelperUC::getPathViewObject("addons_view.class");


class UniteCreatorAddonsElementorView extends UniteCreatorAddonsView{

	protected $showButtons = true;
	protected $showHeader = false;
	protected $pluginTitle = null;


	/**
	 * get header text
	 * @return unknown
	 */
	protected function getHeaderText(){

		$headerTitle = esc_html__("Manage Templates for Elementor", "unlimited-elements-for-elementor");

		return($headerTitle);
	}


	/**
	 * addons view provider
	 */
	public function __construct(){
		
		if(GlobalsUnlimitedElements::$enableElementorSupport == false)
			UniteFunctionsUC::throwError("Elementor templates view not available.");
		
			
		$this->addonType = GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR_TEMPLATE;
		$this->product = GlobalsUnlimitedElements::PLUGIN_NAME;
		$this->pluginTitle = GlobalsUnlimitedElements::$pluginTitleCurrent;
		$this->headerTextInner = __("Elementor Templates", "unlimited-elements-for-elementor");


		parent::__construct();
	}


}


new UniteCreatorAddonsElementorView();
