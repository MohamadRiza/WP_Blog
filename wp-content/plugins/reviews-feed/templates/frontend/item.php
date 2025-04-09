<?php
/**
 * Smash Balloon Reviews Feed Item Template
 * Adds an image, link, and other data for each post in the feed
 *
 * @version 1.0 Reviews Feed by Smash Balloon
 *
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$item_classes = $this->item_classes( $post );
$no_icon = ['wpforms', 'formidable'];
?>
<div class="sb-post-item-wrap sb-new <?php echo esc_attr( $item_classes ); ?>">
	<div class="sb-post-item">
		<?php if (isset($post['provider']['name']) && $post['provider']['name'] !== 'none' && !in_array($post['provider']['name'], $no_icon)) { ?>
			<span class="sb-item-provider-icon">
				<img src="<?php echo esc_html( $this->provider_icon_url( $post, $settings ) ); ?>" alt="<?php echo esc_html( $this->parser->get_provider_name( $post ) ); ?>" />
			</span>
		<?php } ?>
		<?php $this->render_post_elements( $post ); ?>
	</div>
</div>
