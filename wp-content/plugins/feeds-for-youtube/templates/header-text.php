<?php
/**
 * Smash Balloon Custom YouTube Feed Pro Text Header Template
 *
 * @version 2.0 YouTube Feed Pro
 * @author Smash Balloon
 */

use SmashBalloon\YouTubeFeed\SBY_Display_Elements;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$text_header_attrs = SBY_Display_Elements::get_text_header_attributes( $settings );
$text_header_display_condition = SBY_Display_Elements::get_text_header_display_condition( $settings );
$header_text_content = SBY_Display_Elements::get_text_header_content( $settings );
?>
<div <?php echo $text_header_display_condition; ?> class="sb_youtube_header sby-header-type-text" <?php echo $text_header_attrs ?>>
    <span <?php echo $header_text_content; ?>><?php echo esc_html( $settings['customheadertext'] ); ?></span>
</div>