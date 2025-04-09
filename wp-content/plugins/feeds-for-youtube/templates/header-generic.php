<?php
// Don't load directly
use SmashBalloon\YouTubeFeed\SBY_Display_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$header_style_attr       = SBY_Display_Elements::get_style_att( 'items', $settings );
$header_text_color_style = SBY_Display_Elements::get_header_text_color_styles( $settings ); // style="color: #517fa4;" already escaped
$size_class              = SBY_Display_Elements::get_header_size_class( $settings );
$header_display_condition = SBY_Display_Elements::get_header_display_condition( $settings );
$icon_type = $settings['font_method'];
?>
<div class="sb_youtube_header sby_header_type_generic <?php echo esc_attr( $size_class ); ?>"<?php echo $header_style_attr; ?> <?php echo $header_display_condition;?>>
	<div class="sby_header_text sby_no_bio">
		<h3 <?php echo $header_text_color_style; ?>>YouTube</h3>
	</div>
	<div class="sby_header_img">
		<div class="sby_header_icon"><?php echo SBY_Display_Elements::get_icon( 'newlogo', $icon_type ); ?></div>
	</div>
</div>