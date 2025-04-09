<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

class UEConnectivityTestView{
	
	/**
	 * construction
	 */
	public function __construct(){
		
		$this->putHTML();
	}

/**
 * check zip file request
 */
private function checkZipFile(){

	//request single file
	dmp("requesting widget zip from API");

	$response = UEHttp::make()->post(GlobalsUC::URL_API, array(
		"action" => "get_addon_zip",
		"name" => "team_member_box_overlay",
		"cat" => "Team Members",
		"type" => "addons",
		"catalog_date" => "1563618449",
		"code" => "",
	));

	$data = $response->body();

	if(empty($data))
		UniteFunctionsUC::throwError("Empty server response");

	$len = strlen($data);

	dmp("api response OK, received string size: $len");
}



/**
 * check zip file request
 */
private function checkCatalogRequest(){

		dmp("requesting catalog check");
		
		$response = UEHttp::make()->post(GlobalsUC::URL_API, array(
			"action" => "check_catalog",
			"catalog_date" => "1563618449",
			"include_pages" => false,
			"domain" => "localhost",
			"platform" => "wp",
		));
		
		$data = $response->body();
	
		if(empty($data))
			UniteFunctionsUC::throwError("Empty server response");

		$len = strlen($data);
	
		if($len < 5000){
			
			dmp("The wrong response: ");
			dmpHtml($data);
			 
			UniteFunctionsUC::throwError("Response has wrong size: $len");
		}
		
		dmp("api response OK, received string size: $len");
		
		
}

/**
 * various
 */
private function checkVariousOptions(){ 

	$urlAPI = GlobalsUC::URL_API;
	
	dmp("checking get contents from the api: $urlAPI");
 	
	$response = UniteFunctionsUC::fileGetContents($urlAPI);
	
	$len = strlen($response);
	
	if($len == 0)
		UniteFunctionsUC::throwError("No response from API. Recieved string size: 0");
				
	if($len > 1000){
		
		dmp("Response has wrong size: $len");
		
		dmpHtml($response);
				
		return(false);
	}
	
	dmp("file get contents OK, received string size: $len");

}

/**
 * check and update catalog
 */
private function checkUpdateCatalog(){

	dmp("Trying to update the catalog from the api... Printing Debug...");

	$webAPI = new UniteCreatorWebAPI();

	$webAPI->checkUpdateCatalog(true);

	$arrDebug = $webAPI->getDebug();

	dmp($arrDebug);

	//print option content
	$optionCatalog = UniteCreatorWebAPI::OPTION_CATALOG;

	dmp("Option catalog raw data: $optionCatalog");
	
	$data = get_option($optionCatalog);

	dmp($data);

}


/**
 * check if catalog data is saved well
 */
private function checkingCatalogData(){

	$webAPI = new UniteCreatorWebAPI();
	$data = $webAPI->getCatalogData();
		
	dmp("Checking saved widgets catalog data");

	if(empty($data)){
	
		dmp("No catalog widgets data found!");

		$this->checkUpdateCatalog();

		return(false);
	}

	if(is_array($data) == false)
		UniteFunctionsUC::throwError("Catalog data is not array");

	$stamp = UniteFunctionsUC::getVal($data, "stamp");
	$catalog = UniteFunctionsUC::getVal($data, "catalog");

	if(empty($stamp))
		UniteFunctionsUC::throwError("No stamp found");

	if(empty($catalog))
		UniteFunctionsUC::throwError("Empty widgets catalog");

	$date = UniteFunctionsUC::timestamp2Date($stamp);

	dmp("catalog data found OK from date: $date");

	$showData = UniteFunctionsUC::getGetVar("showdata","", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
	$showData = UniteFunctionsUC::strToBool($showData);

	if($showData == true)
		dmp($data);

}
	
	
	/**
	 * put view html
	 */
	private function putHTML(){
		?>
		
		
<h1>Unlimited Elements - API Access Test</h1>

<br>
		
<?php 
		
try{
	
		ini_set("display_errors",1);
		
		$this->checkVariousOptions();
	
		echo "<br><br>";
	
		$this->checkCatalogRequest();
	
		echo "<br><br>";
	
		$this->checkZipFile();
	
		echo "<br><br>";
	
		$this->checkingCatalogData();

}catch(Exception $e){

		$urlPHPFile = GlobalsUC::$urlPlugin."views/api-connect-test.php";
	 	
		$serverIP = $_SERVER["SERVER_ADDR"];
				
		?>
		
		<div style="font-size:18px;line-height:35px;">
			
			<hr>
		
		<?php 
			s_echo( $e->getMessage() );
		?>
			<hr>
			
			The request to the catalog url has failed. <br> Requesting from website ip: <?php echo esc_html($serverIP) ?> <br>
			
			Please contact your hosting provider and request to open firewall access to this address: 
			
			<br>
			
			<a href="https://api.unlimited-elements.com/">https://api.unlimited-elements.com/</a>
			
			<br>
			
			Also, you can test the very simple plain PHP file with the connectiviry test:
					
			<a href="<?php echo esc_url($urlPHPFile); ?>">api-connect-test.php</a>
			
			<br>
			
			If it will fail as well, please show this file to your server support.
		
		</div>

		<?php 

}
		
		
	}//end putHTML()
	
}


new UEConnectivityTestView();

