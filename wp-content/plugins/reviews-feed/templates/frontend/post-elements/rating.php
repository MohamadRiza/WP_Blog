<?php
/**
 * Smash Balloon Reviews Feed Rating Template
 * Adds a star rating
 *
 * @version 1.0 Reviews Feed by Smash Balloon
 *
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="sb-item-rating sb-fs">
        <span class="sb-relative">
            <div class='sb-item-rating-ctn'>
                <?php echo $this->star_rating_display($post, $settings); ?>
            </div>
        </span>
</div>
