<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$error_message = get_transient('uc_changelog_import_error');
$success_message = get_transient('uc_changelog_import_success');
$isChangelogImportDisabled = HelperProviderUC::isAddonChangelogImportDisabled();
if($isChangelogImportDisabled){
	echo '<div class="error"><p>'.esc_attr_e( "The import operation disabled in the general settings.", "unlimited-elements-for-elementor" ).'</p></div>';
	return false;
}
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        jQuery('#changelog-import-form').on('submit', function(e) {
            var confirmation = confirm("<?php esc_attr_e("This operation will delete all your old change log records, and put the new ones. Continue?", "unlimited-elements-for-elementor"); ?>");
            if (!confirmation) {
                e.preventDefault();
            }
        });
    });
</script>
<div class="wrap">
    <?php if($error_message): ?>
        <div class="error"><p><?php 
			s_echo( $error_message ); ?></p></div>
        <?php delete_transient('uc_changelog_import_error'); ?>
    <?php endif; ?>

	<?php if($success_message): ?>
        <div class="updated"><p><?php 
			s_echo( $success_message ); ?></p></div>
		<?php delete_transient('uc_changelog_import_success'); ?>
	<?php endif; ?>

	<h1><?php esc_attr_e("Changelog Import", "unlimited-elements-for-elementor"); ?></h1>
	
	<br>
	
	<p><?php esc_attr_e("Upload a JSON file containing the changelog data below:", "unlimited-elements-for-elementor"); ?></p>
	<p><?php esc_attr_e("Note, that all the old changelog data will be overwrited by the new one.", "unlimited-elements-for-elementor"); ?></p>
	
	<form id="changelog-import-form" method="POST" enctype="multipart/form-data" action="admin.php?page=unlimitedelements_changelog">
		<?php wp_nonce_field('import_json_changelog_action', 'nonce'); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="json_file"><?php esc_attr_e("Choose Export Changelog JSON file", "unlimited-elements-for-elementor"); ?></label></th>
				<td><input type="file" name="json_file" id="json_file" accept=".json" required/></td>
			</tr>
		</table>
        <br>
		<p class="submit">
			<input type="submit" name="import_json_changelog" id="import_json_changelog" class="button-primary" value="<?php esc_attr_e("Import Changelog", "unlimited-elements-for-elementor"); ?>"/>
		</p>
        <input type="hidden" name="action" value="import-json">
	</form>
	<br>
	<br>
    <p>
       <a href="admin.php?page=unlimitedelements_changelog"><?php esc_attr_e("Back to Changelog", "unlimited-elements-for-elementor"); ?></a>
    </p>
	<br>
	<br>
	
</div>
