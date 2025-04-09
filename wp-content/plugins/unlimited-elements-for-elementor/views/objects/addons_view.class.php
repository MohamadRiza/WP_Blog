<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * addons list view
 *
 */

class UniteCreatorAddonsView{
	
	protected $showButtons = true;
	protected $showHeader = true;
	protected $headerTextInner = null;
	protected $addonType, $objAddonType;
	protected $objManager;
	protected $product;
	protected $pluginTitle;
	
	
	/**
	 * constructor
	 */
	public function __construct(){

		$this->initAddonType();
		
		$this->init();
		
		$this->putHtml();
	}
	
	
	/**
	 * init addon types
	 */
	protected function initAddonType(){
		
		if(empty($this->addonType)){
			$this->addonType = UniteFunctionsUC::getGetVar("addontype", null, UniteFunctionsUC::SANITIZE_KEY);
		}

		$this->objAddonType = UniteCreatorAddonType::getAddonTypeObject($this->addonType);
		
		UniteCreatorAdmin::setAdminGlobalsByAddonType($this->objAddonType);
		
	}
	
	
	
	/**
	 * get header text
	 * @return unknown
	 */
	protected function getHeaderText(){
		
		if(!empty($this->objAddonType->managerHeaderPrefix))
			GlobalsUC::$alterViewHeaderPrefix = $this->objAddonType->managerHeaderPrefix;
		
		$headerTitle = esc_html__("Manage", "unlimited-elements-for-elementor")." ".$this->objAddonType->textPlural;
		
		
		return($headerTitle);
	}
	
	/**
	 * validate addon type
	 */
	private function validateAddonType(){
		
		if(empty($this->objAddonType))
			UniteFunctionsUC::throwError("This view should have an addon type");
		
	}
	
	/**
	 * validate addon type
	 */
	private function validateManager(){
		
		if(empty($this->objManager))
			UniteFunctionsUC::throwError("The manager is not inited");
	}
	
	
	/**
	 * init the view, function for override
	 */
	protected function init(){
		
		$view = UniteCreatorAdmin::getView();
		
		$this->objManager = new UniteCreatorManagerAddons();
		$this->objManager->init($this->addonType);
		
		if(!empty($this->product))
			$this->objManager->addPassData("product", $this->product);
		
		if(!empty($this->headerTextInner))
			$this->objManager->setHeaderLineText($this->headerTextInner);
		
	}
	
	
	/**
	 * put view html
	 */
	protected function putHtml(){
		
		$this->validateAddonType();
		$this->validateManager();
		
		if($this->showHeader == true){
			
			$headerTitle = $this->getHeaderText();
				
			require HelperUC::getPathTemplate("header");
		}else
			require HelperUC::getPathTemplate("header_missing");
		
		$pluginName = GlobalsUC::PLUGIN_NAME;

		
		$replacingMessage = null;
		
		if($this->addonType == GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR_TEMPLATE)
			$replacingMessage = __("We are working on a new, updated Templates Catalog. 
				<br><br> Will be available in the next version release.
				<br><br>
				 Meanwhile you can visit <a href='https://unlimited-elements.com/elementor-templates/' target='_blank'>templates demo</a> in our site and copy paste directly from there.
				 
				","unlimited-elements-for-elementor");
		
	?>
		
		<?php 
			if($this->showButtons == true)
				UniteProviderFunctionsUC::putAddonViewAddHtml()
		?>
		
		<div class="content_wrapper unite-content-wrapper">
			<?php 
				
				if(!empty($replacingMessage)){
					?>
					<div class="unite-replacing-message">
						<?php echo wp_kses($replacingMessage, HelperUC::getKsesAllowedHTML()); ?>
					</div>
					<?php 
				}
				else
					$this->objManager->outputHtml() 
			?>
		</div>
		
		<div class="uc-addons-bottom">
		
		
		</div>
		<?php 
					
	}
	

}
