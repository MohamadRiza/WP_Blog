<?php

namespace SmashBalloon\TikTokFeeds\Common\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\FeedSettings;
use SmashBalloon\TikTokFeeds\Common\Feed;
use SmashBalloon\TikTokFeeds\Common\FeedCache;

class ShortcodeService extends ServiceProvider
{
	/**
	 * Register the shortcode.
	 *
	 * @return void
	 */
	public function register()
	{
		add_shortcode('sbtt-tiktok', array( $this, 'render' ));
	}

	/**
	 * Render the TikTok Feed Shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render($atts = array())
	{
		$feed_id = ! empty($atts['feed']) ? absint($atts['feed']) : 0;

		$feed_data = new FeedSettings($feed_id);
		$feed_settings = $feed_data->get_feed_settings();

		if (empty($feed_settings)) {
			return $this->render_error(
				[
					[
						'message'    => wp_sprintf(__('No feed with the ID %d found.', 'feeds-for-tiktok'), $feed_id),
						'directions' => wp_sprintf(__('Please go to %1$sTikTok Feeds%2$s settings page to create a feed.', 'feeds-for-tiktok'), '<a href="' . esc_url(admin_url('admin.php?page=sbtt')) . '" target="_blank" rel="noopener noreferrer">', '</a>'),
					],
				]
			);
		}

		do_action('sbtt_before_shortcode_render', $feed_id);

		$feed_style = $feed_data->get_feed_style();

		$feed = new Feed($feed_settings, $feed_id, new FeedCache($feed_id, 2 * DAY_IN_SECONDS));
		$feed->init();
		$feed->get_set_cache();

		$header_data = $feed->get_header_data();
		$header_data = is_array($header_data) ? $header_data : [];

		$posts     = $feed->get_post_set_page();
		$next_page = $feed->has_next_page();
		$next_page = $next_page !== false ? $next_page : '';
		if (isset($feed_settings['sortRandomEnabled']) && $feed_settings['sortRandomEnabled'] === true) {
			shuffle($posts);
		}

		wp_enqueue_script('sbtt-tiktok-feed');
		wp_enqueue_style('sbtt-tiktok-feed');

		ob_start();

		if (! empty($feed->get_errors())) {
			$error_html = $this->render_error($feed->get_errors());
			echo wp_kses_post($error_html);
		}

		?>
			<style><?php echo esc_attr($feed_style); ?></style>
			<div class="sbtt-tiktok-feed" id="sbtt-tiktok-feed-<?php echo esc_attr($feed_id); ?>" data-feed-settings="<?php echo esc_attr(wp_json_encode($feed_settings)); ?>" data-feed-id="<?php echo esc_attr($feed_id); ?>" data-feed-posts="<?php echo esc_attr(wp_json_encode($posts)); ?>" data-feed-header="<?php echo esc_attr(wp_json_encode($header_data)); ?>" data-next-page="<?php echo esc_attr($next_page); ?>"></div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the error message.
	 *
	 * @param array $errors The errors to render.
	 * @return string
	 */
	public function render_error($errors)
	{
		if (! current_user_can('manage_options')) {
			return '';
		}

		if (empty($errors)) {
			return '';
		}

		if (! is_array($errors)) {
			$errors = array( $errors );
		}

		ob_start();
		?>
			<div class="sbtt-tiktok-feed-error" style="border: 1px solid #ddd; background: #eee; color: #333; margin: 0 auto 10px; padding: 10px 15px; font-size: 13px; text-align: center;">
				<span style="font-size: 12px; font-style: italic">
					<?php esc_html_e('This error message is only visible to WordPress admins.', 'feeds-for-tiktok'); ?>
				</span>
				<?php foreach ($errors as $error) : ?>
					<?php if (is_string($error)) : ?>
						<p><?php echo wp_kses_post($error); ?></p>
					<?php endif; ?>

					<?php if (is_array($error) && isset($error['message']) && isset($error['directions'])) : ?>
						<p><strong><?php echo wp_kses_post($error['message']); ?></strong></p>
						<p><?php echo wp_kses_post($error['directions']); ?></p>
					<?php endif; ?>

				<?php endforeach; ?>
			</div>
		<?php
		return ob_get_clean();
	}
}
