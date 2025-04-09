<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UCFormEntryView{

	private $service;
	private $entry;

	/**
	 * Create a new view instance.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function __construct($id){

		$this->service = new UCFormEntryService();
		$this->entry = $this->getEntry($id);

		$this->service->readEntry($id);
	}

	/**
	 * Displays the view.
	 *
	 * @return void
	 */
	public function display(){

		$this->displayHeader();
		$this->displayContent();
		$this->displayFooter();
	}

	/**
	 * Get the entry data.
	 *
	 * @param int $id
	 *
	 * @return array
	 * @throws Exception
	 */
	private function getEntry($id){

		global $wpdb; 

		$table = $this->service->getTable();
		$sql = "
			SELECT *
			FROM {$table}
			WHERE id = %d
			LIMIT 1
		";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$sql = $wpdb->prepare($sql, array($id));
		
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$entry = $wpdb->get_row($sql, ARRAY_A);
		
		if(empty($entry) === true)
			UniteFunctionsUC::throwError("Entry with ID {$id} not found.");

		$sql = "
			SELECT *
			FROM {$this->service->getFieldsTable()}
			WHERE entry_id = %d
		";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$sql = $wpdb->prepare($sql, array($id));
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$entry["fields"] = $wpdb->get_results($sql, ARRAY_A);

		return $entry;
	}

	/**
	 * Display the header.
	 *
	 * @return void
	 */
	private function displayHeader(){
		// translators: %d is a number 
		$headerTitle = sprintf(__("Form Entry (ID %d)", "unlimited-elements-for-elementor"), $this->entry["id"]);

		require HelperUC::getPathTemplate("header");
	}

	/**
	 * Display the content.
	 *
	 * @return void
	 */
	private function displayContent(){

		$asides = array(
			__("Entry Information", "unlimited-elements-for-elementor") => array(
				__("Entry ID", "unlimited-elements-for-elementor") => $this->entry["id"],
				__("Form", "unlimited-elements-for-elementor") => $this->entry["form_name"],
				__("Page", "unlimited-elements-for-elementor") => $this->entry["post_title"],
				__("Date", "unlimited-elements-for-elementor") => $this->service->formatEntryDate($this->entry["created_at"]),
			),
			__("User Information", "unlimited-elements-for-elementor") => array(
				__("User ID", "unlimited-elements-for-elementor") => $this->entry["user_id"],
				__("User IP", "unlimited-elements-for-elementor") => $this->entry["user_ip"],
				__("User Agent", "unlimited-elements-for-elementor") => $this->entry["user_agent"],
			),
		);

		$fields = $this->service->formatEntryFields($this->entry["fields"]);

		?>
		<div id="poststuff">
			<div id="post-body" class="columns-2">

				<div id="post-body-content">
					<div class="postbox">
						<div class="postbox-header">
							<h2><?php echo esc_html__("Entry Fields", "unlimited-elements-for-elementor"); ?></h2>
						</div>
						<div class="inside">
							<table class="wp-list-table widefat">
								<tbody>
									<?php foreach($fields as $field): ?>
										<tr>
											<td><?php echo esc_html($field["title"]); ?></td>
											<td>
												<?php

												switch($field["type"]){
													case UniteCreatorForm::TYPE_FILES:
														$form = new UniteCreatorForm();
														s_echo( $form->getFilesFieldLinksHtml($field["value"], "<br />", true) );
													break;

													default:
														echo nl2br(esc_html($field["text"] ?: $field["value"]));
												}

												?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="postbox-container-1">
					<?php foreach($asides as $title => $fields): ?>
						<div class="postbox">
							<div class="postbox-header">
								<h2><?php echo esc_html($title); ?></h2>
							</div>
							<div class="inside">
								<div id="misc-publishing-actions">
									<?php foreach($fields as $label => $value): ?>
										<?php if(isset($value) === true): ?>
											<div class="misc-pub-section">
												<?php echo esc_html($label); ?>: <b><?php echo esc_html($value); ?></b>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
			<br class="clear" />
		</div>

		<style>
			#post-body-content .postbox .postbox-header {
				border-bottom: none;
			}

			#post-body-content .postbox .inside {
				margin: 0;
				padding: 0;
			}

			#post-body-content .postbox .wp-list-table {
				border: none;
				border-collapse: collapse;
			}

			#post-body-content .postbox .wp-list-table td {
				border-top: 1px solid #c3c4c7;
			}

			#post-body-content .postbox .wp-list-table td:first-child {
				width: 150px;
				background: #f6f7f7;
				font-weight: bold;
			}
		</style>
		<?php
	}

	/**
	 * Display the footer. 
	 *
	 * @return void
	 */
	private function displayFooter(){

		$page = (isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '');

		$url = wp_get_referer() ?: "?page=" . $page;


		?>
		<div>
			<a class="button" href="<?php echo esc_url($url); ?>">
				<?php echo esc_html__("Back to Form Entries", "unlimited-elements-for-elementor"); ?>
			</a>
		</div>
		<?php
	}

}
