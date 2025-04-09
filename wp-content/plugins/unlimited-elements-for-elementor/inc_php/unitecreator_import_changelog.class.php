<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

class UniteCreatorImportExportChangelog{

	/**
	 *  Export Changelog.
	 *
	 * @return void
	 */
	public function exportChangelog($typeExport) {
		
		global $wpdb;


		$changelogTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_CHANGELOG_NAME);
		$addonsTable = GlobalsUc::$table_addons;

		$query = "
            SELECT changelog.type, changelog.text, changelog.plugin_version, changelog.created_at, addons.name 
                AS addon_name  
              FROM $changelogTable 
                AS changelog
         LEFT JOIN $addonsTable 
                AS addons
                ON changelog.addon_id = addons.id
        ";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$items = $wpdb->get_results($query);

		$arrayItems = array();

		foreach($items as $item){
			$arrayItems[] = $item;
		}

		$filename = "changelog-" . current_time("mysql") . ".json";
		$content  = json_encode($arrayItems);

		if($typeExport == 'export-file')
			UniteFunctionsUC::downloadJson($filename, $content);

		if($typeExport == 'export-json')
			s_echo($content);
		
	}


	/**
	 *  Import Changelog.
	 *
	 * @return void
	 */
	public function importChangelog() {
		$nonce = UniteFunctionsUC::getPostGetVariable("nonce", "", UniteFunctionsUC::SANITIZE_NOTHING);
		$urlViewImport = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_CHANGELOG_IMPORT);

		//check nonce

		$hasPermissions = UniteFunctionsWPUC::isCurrentUserHasPermissions();

		$isNonceValid = wp_verify_nonce($nonce, 'import_json_changelog_action');

		if ($hasPermissions == false || $isNonceValid == false) {

			$this->processImportJsonAction_error("Security check failed or insufficient permissions.", "unlimited-elements-for-elementor");

			return(false);
		}

		//check that data is valid

		if (empty($_FILES['json_file']['tmp_name']) || $_FILES['json_file']['type'] !== 'application/json') {

			$this->processImportJsonAction_error(__("Invalid file or upload error.", "unlimited-elements-for-elementor"));

			return(false);
		}

		$jsonData = UniteFunctionsUC::fileGetContents($_FILES['json_file']['tmp_name']);

		$decodedData = json_decode($jsonData, true);

		if(empty($decodedData))
			$this->processImportJsonAction_error(__("Invalid JSON data found", "unlimited-elements-for-elementor"));

		$success = $this->processRecordChangelogAction($decodedData);

		if($success == false)
			$this->processImportJsonAction_error(__("Import processing failed", "unlimited-elements-for-elementor"));


		//if all ok, show success data

		set_transient("uc_changelog_import_success", __("Change Log imported successfully!", "unlimited-elements-for-elementor"), 30);

		wp_redirect($urlViewImport);
	}

	/**
	 * Process the record changelog json action.
	 *
	 * @return void
	 */

	public function processRecordChangelogAction($data){

		$isChangelogImportDisabled = HelperProviderUC::isAddonChangelogImportDisabled();
		if($isChangelogImportDisabled){
			echo esc_attr(__( "The import operation disabled in the general settings.", "unlimited-elements-for-elementor" ));
			exit;
		}


		global $wpdb;

		if(empty($data))
			return(false);

		$objAddons = new UniteCreatorAddons();
		$addons = $objAddons->getArrAddonsShort('', array(), GlobalsUC::ADDON_TYPE_ELEMENTOR);

		$arrAddonsAssoc = array();

		//prepare assoc array of addons

		foreach ($addons as $addon) {

			$arrAddonsAssoc[$addon["name"]] = array(
				'id' => $addon['id'],
				'title' => $addon['title']
			);

		}

		$changelogTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_CHANGELOG_NAME);

		//delete the table

		$wpdb->query("TRUNCATE TABLE {$changelogTable}");

		$adminUserID = $this->getAdminID();

		foreach ($data as $item) {

			$addon = UniteFunctionsUC::getVal($arrAddonsAssoc, $item['addon_name']);

			if(empty($addon))
				continue;

			$data_to_insert = array(
				'addon_id'       => $addon['id'],
				'user_id'        => $adminUserID,
				'type'           => $item['type'],
				'text'           => $item['text'],
				'plugin_version' => $item['plugin_version'],
				'created_at'     => $item['created_at'],
				'addon_title'    => $addon['title']
			);

			$wpdb->insert($changelogTable, $data_to_insert);
		}

		return true;
	}

	/**
	 * get admin user id
	 */
	private function getAdminID() {

		$arrUsers = UniteFunctionsWPUC::getAdminUsers();

		if(empty($arrUsers))
			return(null);

		$firstUser = $arrUsers[0];

		$userID = $firstUser->ID;

		return($userID);
	}

	/**
	 * set error message
	 */
	private function processImportJsonAction_error($errorMessage){

		$keyError = "uc_changelog_import_error";
		$urlViewImport = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_CHANGELOG_IMPORT);

		set_transient($keyError, $errorMessage, 30);
		wp_redirect($urlViewImport);
	}

}
