<?php
// Don't load directly
use SmashBalloon\YouTubeFeed\Pro\SBY_Display_Elements_Pro;
use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_Parse;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$avatar                     = SBY_Parse::get_avatar( $header_data, $settings );
$display_avatar             = SBY_Display_Elements::get_display_avatar( $header_data, $settings );
$channel_title              = SBY_Parse::get_channel_title( $header_data );
$channel_description        = SBY_Parse::get_channel_description( $header_data );
$subscriber_count           = SBY_Parse::get_subscriber_count( $header_data );
$should_show_subscriber     = $settings['showsubscribers'] && $subscriber_count !== '';
$permalink                  = SBY_Parse::get_channel_permalink( $header_data );
$header_style_attr          = SBY_Display_Elements::get_style_att( 'items', $settings );
$header_text_color_style    = SBY_Display_Elements::get_header_text_color_styles( $settings ); // style="color: #517fa4;" already escaped
$size_class                 = SBY_Display_Elements::get_header_size_class( $settings );
$should_show_bio            = $settings['showdescription'] && $channel_description !== '';
$bio_class                  = ! $should_show_bio ? ' sby_no_bio' : ' sby_has_bio';
$subscribers_class          = ! $should_show_subscriber ? ' sby_no_sub' : ' sby_has_sub';
$header_text_attr           = SBY_Display_Elements::get_header_text_attr( $settings );
$header_display_condition   = SBY_Display_Elements::get_header_display_condition( $settings );
$icon_type                  = $settings['font_method'];

?>
<div class="sb_youtube_header <?php echo esc_attr( $size_class ); ?>"<?php echo $header_style_attr; ?> <?php echo $header_display_condition;?>>
    <a href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" title="@<?php echo esc_attr( $channel_title ); ?>" class="sby_header_link" data-channel-description="<?php echo $settings['showdescription'] ? 'visible' : 'hidden'; ?>">
        <div class="sby_header_text<?php echo esc_attr( $bio_class . $subscribers_class ); ?>" <?php echo $header_text_attr; ?>>
            <h3 <?php echo $header_text_color_style; ?>><?php echo esc_html( $channel_title ); ?></h3>
			<?php if ( $should_show_bio || sby_doing_customizer( $settings ) ) : ?>
                <p class="sby_bio" <?php echo SBY_Display_Elements::get_description_data_attributes( $settings ); ?> <?php echo $header_text_color_style; ?>><?php echo sby_esc_html_with_br( $channel_description ); ?></p>
			<?php endif; ?>
        </div>
        <div class="sby_header_img" data-avatar-url="<?php echo esc_attr( $avatar ); ?>">
            <div class="sby_header_img_hover"><?php echo SBY_Display_Elements::get_icon( 'newlogo', $icon_type ); ?></div>
            <img src="<?php echo esc_url( $display_avatar ); ?>" alt="<?php echo esc_attr( $channel_title ); ?>" width="50" height="50">
        </div>
    </a>
</div>