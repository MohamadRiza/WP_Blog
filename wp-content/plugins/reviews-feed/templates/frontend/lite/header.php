<?php
/**
 * Smash Balloon Reviews Feed Header Template
 * Adds account information and an avatar to the top of the feed
 *
 * @version 1.0 Reviews Feed by Smash Balloon
 *
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
use SmashBalloon\Reviews\Common\Parser;

$parser = new Parser();
?>
<section class="sb-feed-header" data-align="left">
	<div class="sb-feed-header-content">
		<?php if ( $this->should_show( 'header', 'heading' ) ) : ?>
		    <div class="sb-feed-header-heading"><span class="sb-relative"><?php echo esc_html( $this->get_header_heading_content() ); ?></span></div>
		<?php endif; ?>
		<?php if ( $this->should_show( 'header', 'button' ) ) : ?>
            <div class="sb-feed-header-bottom sb-fs">
                <?php if ( $this->should_show( 'header', 'button' ) ) : ?>
                    <div class="sb-feed-header-btn-ctn sb-relative">
                        <a href="<?php echo esc_url( $this->get_review_link( $header_data ) ); ?>" target="_blank" rel="noopener noreferrer" class="sb-btn sb-feed-header-btn" data-icon-position="left" data-onlyicon="true">
                            <span class="sb-btn-icon">
                                <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 8.62501V10.5H3.375L8.905 4.97001L7.03 3.09501L1.5 8.62501ZM10.705 3.17001L8.83 1.29501L7.565 2.56501L9.44 4.44001L10.705 3.17001Z" fill="#141B38"></path></svg>
                            </span>
                            <?php echo esc_html( $this->get_header_button_text() ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
		<?php endif; ?>
	</div>
</section>

