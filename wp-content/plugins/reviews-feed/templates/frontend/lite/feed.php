<?php
/**
 * Smash Balloon Reviews Feed Main Template
 * Creates the wrapping HTML and adds settings as attributes
 *
 * @version 1.0 Reviews Feed by Smash Balloon
 *
 */

/**
 * Add HTML or execute code before the feed displays.
 * sbr_after_feed works the same way but executes
 * after the feed
 *
 * @param array $posts Instagram posts in feed
 * @param array $settings settings specific to this feed
 *
 * @since 2.2
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
use SmashBalloon\Reviews\Common\Parser;

$parser = new Parser();
do_action( 'sbr_before_feed', $posts, $settings );

$classes = $this->feed_classes( $settings );
$misc_atts = $this->misc_atts();
?>
<section id="<?php echo esc_attr(sbr_container_id($feed_id)); ?>" class="sbr-feed">
	<?php echo $this->error_html(); ?>
    <section class="sb-feed-container <?php echo esc_attr( $classes ); ?>" data-layout="<?php echo esc_attr( $settings['layout'] ); ?>"  data-gutter="<?php echo esc_attr( $settings['horizontalSpacing'] ); ?>"  data-post-style="<?php echo esc_attr( $settings['postStyle'] ); ?>" data-shortcode-atts="<?php echo esc_attr( $shortcode_atts ); ?>" data-feed-id="<?php echo esc_attr( $feed_id ); ?>"<?php echo $misc_atts; ?>>
        <?php if ( $this->should_show( 'header') ): include sbr_get_feed_template_part( 'header', $settings ); endif; ?>

        <section class="sb-feed-posts" data-icon-size="small" data-avatar-size="medium">
            <?php $this->posts_loop( $posts, $settings ); ?>
        </section>

        <?php
        /**
         * Things to add before the closing "div" tag for the main feed element. Several
         * features rely on this hook such as local images and some error messages
         *
         * @param object Feed
         * @param string $feed_id
         *
         * @since 1.0
         */
        do_action( 'sbr_before_feed_end', $this, $feed_id ); ?>
    </section>
</section>

<?php do_action( 'sbr_after_feed', $posts, $settings );?>
