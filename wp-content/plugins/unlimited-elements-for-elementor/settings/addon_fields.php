<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

		$filepathAddonSettings = GlobalsUC::$pathSettings."addon_fields.xml";
		
		UniteFunctionsUC::validateFilepath($filepathAddonSettings);
		
		$generalSettings = new UniteCreatorSettings();
		
		if(isset($this->objAddon)){
			$generalSettings->setCurrentAddon($this->objAddon);
		}
		
		$generalSettings->loadXMLFile($filepathAddonSettings);
