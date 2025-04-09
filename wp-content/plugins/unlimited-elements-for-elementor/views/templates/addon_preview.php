<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div id="uc_preview_addon_wrapper" class="uc-preview-addon-wrapper" data-addonid="<?php echo esc_attr($addonID); ?>">

	<?php UniteProviderFunctionsUC::putInitHelperHtmlEditor(); ?>

	<div class="uc-preview-addon-left">
		<div id="uc_settings_loader" class="uc-settings-loader">
			<?php esc_html_e("Loading settings...", "unlimited-elements-for-elementor"); ?>
		</div>
		<div id="uc_settings_wrapper" class="uc-settings-wrapper"></div>
	</div>

	<div class="uc-preview-addon-right">
		<div id="uc_preview_loader" class="uc-preview-loader">
			<?php esc_html_e("Loading preview...", "unlimited-elements-for-elementor"); ?>
		</div>
		<div id="uc_preview_wrapper" class="uc-preview-wrapper"></div>
	</div>

</div>
