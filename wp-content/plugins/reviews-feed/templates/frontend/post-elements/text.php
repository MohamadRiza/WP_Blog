<?php
/**
 * Smash Balloon Reviews Feed Rating Template
 * Adds a review paragraph
 *
 * @version 1.0 Reviews Feed by Smash Balloon
 *
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="sb-item-text sb-fs">
    <?php echo wp_kses_post( nl2br( $this->get_review_text( $post ) ) ); ?>
</div>
<div class="sb-expand">
    <a href="#" data-link="<?php echo esc_url( $this->more_link( $post ) ); ?>">
        <span class="sb-more">...</span>
    </a>
</div>

