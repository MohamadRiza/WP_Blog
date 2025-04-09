<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<h1>Unlimited Elements - List of All DB Tables</h1>
 

<?php 

	HelperProviderUC::showDebugDBTables();

	$admin = UniteProviderAdminUC::getInstance();
	
	$response = $admin->createTables();
	
	dmp("Create Tables Response:");
	dmp($response);