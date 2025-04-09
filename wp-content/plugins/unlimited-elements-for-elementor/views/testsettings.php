<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;



function ueCheckCatalog(){

	HelperProviderUC::showPostsDebug($arrPosts);
	
	$webAPI = new UniteCreatorWebAPI();
	
	$response = $webAPI->checkUpdateCatalog();

	$lastAPIData = $webAPI->getLastAPICallData();
	
	$arrAddons = $webAPI->getCatalogAddonsByTags(UniteCreatorWebAPI::TAG_ANIMATION);
	
	
	dmp("addons that support animation");
	UniteFunctionsUC::getGetVar("preview_id","",UniteFunctionsUC::SANITIZE_KEY);
	dmp($arrAddons);
	exit();
	
	
}

function checkSomeFunc(){

	$webAPI = new UniteCreatorWebAPI();
	$data = $webAPI->getCatalogArray_pages();
	
	dmp($data);
	
	dmp("check some func");
	exit();
}


checkSomeFunc();


exit();

