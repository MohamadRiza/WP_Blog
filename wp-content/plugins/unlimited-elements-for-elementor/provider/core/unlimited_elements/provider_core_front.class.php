<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class UniteProviderCoreFrontUC_Elementor extends UniteProviderFrontUC{
	
	private $objFiltersProcess;

	
	/**
	 *
	 * the constructor
	 */
	public function __construct(){
		
		HelperProviderCoreUC_EL::globalInit();
		
		//run front filters process
		
		$this->objFiltersProcess = new UniteCreatorFiltersProcess();
		$this->objFiltersProcess->initWPFrontFilters();
		
		
		/*
		$disableFilters = HelperProviderCoreUC_EL::getGeneralSetting("disable_autop_filters");
		$disableFilters = UniteFunctionsUC::strToBool($disableFilters);
		
		if($disableFilters == true)
			$this->disableWpFilters();
		*/
		
		parent::__construct();
						
	}
	
	
}
