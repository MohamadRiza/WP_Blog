<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

require_once GlobalsUC::$pathViewsObjects."addon_view.class.php";

$pathProviderAddon = GlobalsUC::$pathProvider."views/addon.php";

if(file_exists($pathProviderAddon) == true){
	require_once $pathProviderAddon;
	$objAddonView = new UniteCreatorAddonViewProvider();
}
else{
	$objAddonView = new UniteCreatorAddonView();
}

$objAddonView->runView();
