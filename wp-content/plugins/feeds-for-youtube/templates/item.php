<?php
/**
 * Item Template
 * Adds an image, link, and other data for each post in the feed
 *
 * @version 2.0 by Smash Balloon
 *
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
// Don't load directly
use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_Parse;

$context = 'item';
$classes = SBY_Display_Elements::get_item_classes( $settings, $offset );
$post_id = SBY_Parse::get_post_id( $post );
$timestamp = SBY_Parse::get_timestamp( $post );
$video_id = SBY_Parse::get_video_id( $post );
$media_url               = SBY_Display_Elements::get_optimum_media_url( $post, $settings );
$media_full_res          = SBY_Parse::get_media_url( $post );
$media_all_sizes_json    = SBY_Parse::get_media_src_set( $post );
$permalink = SBY_Parse::get_permalink( $post );
$img_alt                 = SBY_Parse::get_caption( $post, __( 'Image for post', 'feeds-for-youtube' ) . ' ' . $post_id );
$items_style_attr = SBY_Display_Elements::get_style_att( 'item', $settings );
$title = SBY_Parse::get_video_title( $post );

$additional_atts = apply_filters( 'sby_item_additional_data_atts', array(), $post, array() );
$additional_atts_string = SBY_Display_Elements::escaped_data_att_string( $additional_atts );

// customizer only attribues 
$play_icon_attr     = SBY_Display_Elements::get_element_attribute( 'icon', $settings );
$video_title_attr   = SBY_Display_Elements::get_element_attribute( 'title', $settings );
$hover_video_title_attr = SBY_Display_Elements::get_element_attribute( 'hover_title', $settings );
?>
<div class="sby_item <?php echo esc_attr( $classes ); ?>" id="sby_<?php echo esc_html( $post_id ); ?>" data-date="<?php echo esc_html( $timestamp ); ?>" data-video-id="<?php echo esc_attr( $video_id ); ?>"<?php echo $items_style_attr; ?><?php echo $additional_atts_string; ?>>
    <div class="sby_inner_item">
        <div class="sby_video_thumbnail_wrap sby_item_video_thumbnail_wrap">
            <a class="sby_video_thumbnail sby_item_video_thumbnail" href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" data-full-res="<?php echo esc_url( $media_full_res ); ?>" data-img-src-set="<?php echo esc_attr( wp_json_encode( $media_all_sizes_json ) ); ?>" data-video-id="<?php echo esc_attr( $video_id ); ?>" data-video-title="<?php echo sby_esc_attr_with_br( $title ); ?>">
                <img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">

                <div class="sby_thumbnail_hover sby_item_video_thumbnail_hover">
                    <div class="sby_thumbnail_hover_inner">
                        <?php if ( SBY_Display_Elements::should_show_element( 'title', $context . '-hover', $settings ) ) : ?>
                            <span class="sby_video_title" <?php echo $hover_video_title_attr; ?>><?php echo esc_html( $title ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ( SBY_Display_Elements::should_show_element( 'icon', $context, $settings ) ) : ?>
                    <div class="sby_play_btn" <?php echo $play_icon_attr; ?>>
                        <span class="sby_play_btn_bg"></span>
                        <?php echo SBY_Display_Elements::get_icon( 'play', 'svg' ); ?>
                    </div>
                <?php endif; ?>
                <span class="sby_loader sby_hidden" style="background-color: rgb(255, 255, 255);"></span>
            </a>

            <?php if ( $settings['layout'] === 'list' ) : ?>
            <div id="sby_player_<?php echo esc_html( $video_id ); ?>" class="sby_player_wrap"></div>
            <?php endif; ?>
        </div>
    </div>
</div>