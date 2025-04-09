<?php

use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_Parse;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$feed_styles = SBY_Display_Elements::get_feed_style( $settings );
$cols_setting = SBY_Display_Elements::get_cols( $settings );
$mobile_cols_setting = SBY_Display_Elements::get_cols_mobile( $settings );
$items_wrap_classes = $settings['infoposition'] === 'side' ? ' sby_info_side' : '';
$items_wrap_style_attr = SBY_Display_Elements::get_style_att( 'items_wrap', $settings );
$feed_classes = SBY_Display_Elements::get_feed_container_css_classes( $settings, $additional_classes );
$sby_main_atts = SBY_Display_Elements::get_feed_container_main_attributes( $settings );
if ( $header_data ) {
	$subscriber_count = SBY_Parse::get_subscriber_count( $header_data );
	$subscriber_count_with_text = SBY_Display_Elements::escaped_formatted_count_string( $subscriber_count, 'subscribers' );
}

$gallery_player_attr              = SBY_Display_Elements::get_element_attribute( 'show_gallery_player', $settings );

$num_setting = $settings['num'];
$nummobile_setting = $settings['nummobile'];

$data_channel_header_colors = array(
    'channelName' => !empty($settings['subscribebtnprimarycolor']) ? $settings['subscribebtnprimarycolor'] : '',
    'subscribeCount' => !empty($settings['subscribebtnsecondarycolor']) ? $settings['subscribebtnsecondarycolor'] : '',
    'buttonBackground' => !empty($settings['subscribelinkcolorbg']) ? $settings['subscribelinkcolorbg'] : '',
    'buttonText' => !empty($settings['subscribebtntextcolor']) ? $settings['subscribebtntextcolor'] : '',
);

if ( $settings['showheader'] && ! empty( $posts ) && $settings['headeroutside'] ) {
	include sby_get_feed_template_part( 'header', $settings );
}
?>


<div id="sb_youtube_<?php echo esc_attr( preg_replace( "/[^A-Za-z0-9 ]/", '', $feed_id ) ); ?>" 
	<?php echo $feed_classes; ?> 
	data-feedid="<?php echo esc_attr( $feed_id ); ?>" 
	data-shortcode-atts="<?php echo esc_attr( $shortcode_atts ); ?>" 
	data-cols="<?php echo esc_attr( $cols_setting ); ?>" 
	data-colsmobile="<?php echo esc_attr( $mobile_cols_setting ); ?>" 
	data-num="<?php echo esc_attr( $num_setting ); ?>" 
	data-nummobile="<?php echo esc_attr( $nummobile_setting ); ?>" 
	<?php $header_data ? printf( 'data-channel-subscribers="%s"', esc_attr( $subscriber_count_with_text ) ) : ''; ?>
	data-subscribe-btn="<?php echo esc_attr( $settings['enablesubscriberlink'] ); ?>" 
	data-subscribe-btn-text="<?php echo esc_attr( SBY_Display_Elements::get_subscribe_btn_text( $settings ) ); ?>" 
	data_channel_header_colors ="<?php echo esc_attr(wp_json_encode($data_channel_header_colors)); ?>"
	<?php echo $other_atts . $feed_styles; ?> 
	<?php echo $sby_main_atts ?> 
>
	<?php
	if ( ( $settings['showheader'] && ! empty( $posts ) && !$settings['headeroutside'] ) || sby_doing_customizer( $settings ) ) {
		SBY_Display_Elements::display_header( $header_data, $settings );
	}
	?>
    <?php if ( $settings['layout'] === 'gallery' && isset( $posts[0] )  || sby_doing_customizer( $settings ) ) {
        $placeholder_post = isset( $posts[0] ) ? $posts[0] : null;
	    $misc_data = $this->get_misc_data( $this->regular_feed_transient_name, $posts );
	    include sby_get_feed_template_part( 'player', $settings );
    } ?>
    <div class="sby_items_wrap<?php echo esc_attr($items_wrap_classes);?>"<?php echo $items_wrap_style_attr; ?>>
		<?php
		if ( ! in_array( 'ajaxPostLoad', $flags, true ) ) {
		    $settings['feed_id'] = $feed_id;
			$this->posts_loop( $posts, $settings );
		}
		?>
    </div>
    <?php if ( isset( $posts[0] ) ) $this->maybe_add_live_html( $posts[0] ); ?>

	<?php if ( ! empty( $posts ) ) { include sby_get_feed_template_part( 'footer', $settings ); } ?>

    <?php if ( ( $settings['layout'] === 'grid' || $settings['layout'] === 'carousel' ) && sby_is_pro() ) {
		 include sby_get_feed_template_part( 'cta', $settings ); 
	} ?>

	<?php
	/**
	 * Things to add before the closing "div" tag for the main feed element. Several
	 * features rely on this hook such as local images and some error messages
	 *
	 * @param object SBY_Feed
	 * @param string $feed_id
	 *
	 * @since 1.0
	 */
	do_action( 'sby_before_feed_end', $this, $feed_id ); ?>

</div>

<?php do_action( 'sby_after_feed', $feed_id, $posts, $settings );?>