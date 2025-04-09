<?php

use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_Parse;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$context = 'player';
$post_id = SBY_Parse::get_post_id( $placeholder_post );
$timestamp = SBY_Parse::get_timestamp( $placeholder_post );
$video_id = SBY_Parse::get_video_id( $placeholder_post );
$protocol = is_ssl() ? 'https' : 'http';
$media_url               = SBY_Display_Elements::get_optimum_media_url( $placeholder_post, $settings );
$media_full_res          = SBY_Parse::get_media_url( $placeholder_post );
$media_all_sizes_json    = SBY_Parse::get_media_src_set( $placeholder_post );
$permalink = SBY_Parse::get_permalink( $placeholder_post );
$img_alt                 = SBY_Parse::get_caption( $placeholder_post, __( 'Image for post', 'feeds-for-youtube' ) . ' ' . $post_id );
$player_outer_wrap_style_attr = SBY_Display_Elements::get_style_att( 'player_outer_wrap', $settings );

$player_attributes = SBY_Display_Elements::get_player_attributes( $settings );
?>
<div id="sby_player_<?php echo esc_attr( $post_id ); ?>" class="sby_player_outer_wrap sby_player_item" <?php echo $player_outer_wrap_style_attr; echo $player_attributes; ?>>
    <div class="sby_video_thumbnail_wrap">
        <a class="sby_video_thumbnail sby_player_video_thumbnail" href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" data-full-res="<?php echo esc_url( $media_full_res ); ?>" data-img-src-set="<?php echo esc_attr( wp_json_encode( $media_all_sizes_json ) ); ?>" data-video-id="<?php echo esc_attr( $video_id ); ?>">
            <span class="sby-screenreader"><?php echo sprintf( __( 'YouTube Video %s', 'feeds-for-youtube' ), $post_id ); ?></span>
            <img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
            <span class="sby_loader sby_hidden" style="background-color: rgb(255, 255, 255);"></span>
        </a>
        <?php if ( SBY_Display_Elements::should_show_element( 'icon', $context, $settings ) ) : ?>
            <div class="sby_play_btn">
            <?php echo SBY_Display_Elements::get_icon( 'play', 'svg' ); ?>
                <span class="sby_play_btn_bg"></span>
            </div>
        <?php endif; ?>
        <div class="sby_player_wrap">
            <div class="sby_player"></div>
        </div>
    </div>
</div>
