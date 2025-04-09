<?php

namespace SmashBalloon\TikTokFeeds\Common\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Utils;

class NotificationService extends ServiceProvider
{
	protected const SOURCE_URL = 'https://plugin.smashballoon.com/notifications.json';
	protected const OPTION_NAME = 'sbtt_notifications';
	protected const PLUGIN = 'tiktok';

	/**
	 * Cache the option value.
	 *
	 * @var bool|array
	 */
	protected $cache_option_value = false;

	/**
	 * Registers the action hooks for notifications.
	 *
	 * @return void
	 */
	public function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('sbtt_admin_notices_filter', array($this, 'outputNotifications'));
		add_action('sbtt_notification_update', array($this, 'updateNotifications'));
		add_action('wp_ajax_sbtt_dashboard_notification_dismiss', array($this, 'dismissNotification'));
	}

	/**
	 * Get option name.
	 *
	 * @return string
	 */
	public function getOptionName()
	{
		return self::OPTION_NAME;
	}

	/**
	 * Get source URL.
	 *
	 * @return string
	 */
	public function getSourceUrl()
	{
		return self::SOURCE_URL;
	}

	/**
	 * Check if the user has access and notifications are enabled
	 *
	 * @return bool
	 */
	public function hasAccess()
	{
		if (!current_user_can('manage_options')) {
			return false;
		}

		return apply_filters('sbtt_admin_notifications_has_access', true);
	}

	/**
	 * Get option value.
	 *
	 * @param bool $cache Use cache if available.
	 * @return mixed
	 */
	public function getOptionValue($cache = true)
	{
		if ($cache && $this->cache_option_value !== false) {
			return $this->cache_option_value;
		}

		$option_value = get_option($this->getOptionName(), []);
		$this->cache_option_value = [
			'update' => $option_value['update'] ?? 0,
			'events' => $option_value['events'] ?? [],
			'feed' => $option_value['feed'] ?? [],
			'dismissed' => $option_value['dismissed'] ?? []
		];

		return $this->cache_option_value;
	}

	/**
	 * Fetch the notifications from the source URL.
	 *
	 * @return array
	 */
	public function fetchNotifications()
	{
		$response = wp_remote_get($this->getSourceUrl());

		if (is_wp_error($response)) {
			return [];
		}

		$body = wp_remote_retrieve_body($response);
		if (empty($body)) {
			return [];
		}

		$body = str_replace(array('sbi_', 'sbi-'), array('sbtt_', 'sbtt-'), $body);
		$data = json_decode($body, true);
		if (JSON_ERROR_NONE !== json_last_error()) {
			return [];
		}

		return $this->verifyNotifications($data);
	}

	/**
	 * Verify the notifications from the source URL.
	 *
	 * @param array $notifications The notifications to verify.
	 *
	 * @return array
	 */
	public function verifyNotifications($notifications)
	{
		$filtered = $this->filterNotifications($notifications);
		if (empty($filtered)) {
			return [];
		}

		$verifiedNotifications = [];
		foreach ($filtered as $notification) {
			if (empty($notification['content']) || empty($notification['type'])) {
				continue;
			}

			if ($this->isNotificationValid($notification)) {
				$verifiedNotifications[] = $notification;
			}
		}

		return $verifiedNotifications;
	}

	/**
	 * Get the notifications.
	 *
	 * @return array
	 */
	public function getNotifications()
	{
		if (!$this->hasAccess()) {
			return [];
		}

		$option = $this->getOptionValue();
		if (empty($option['update']) || time() > $option['update'] + DAY_IN_SECONDS) {
			$this->updateNotifications();
		}

		$events = !empty($option['events']) ? $this->verifyActiveNotifications($option['events']) : [];
		$feed = !empty($option['feed']) ? $this->verifyActiveNotifications($option['feed']) : [];

		$new_user = new NewUserService();
		$new_user_notifications = $new_user->getNotifications();

		if (!empty($new_user_notifications)) {
			$events = array_merge($new_user_notifications, $events);
		}

		return array_merge($events, $feed);
	}

	/**
	 * Verify saved notification data for active notifications.
	 *
	 * @param array $notifications The notifications to verify.
	 *
	 * @return array
	 */
	public function verifyActiveNotifications($notifications)
	{
		if (! is_array($notifications) || empty($notifications)) {
			return [];
		}

		$active_notifications = [];
		$current_time = time();

		foreach ($notifications as $notification) {
			if (empty($notification['recent_install_override']) && $this->isRecentlyInstalled()) {
				continue;
			}

			$start_time = !empty($notification['start']) ? strtotime($notification['start']) : null;
			$end_time = !empty($notification['end']) ? strtotime($notification['end']) : null;

			$is_within_time_range = ($start_time && $end_time)
				? ($current_time >= $start_time && $current_time <= $end_time)
				: true;

			if ($is_within_time_range && $this->isNotificationValid($notification)) {
				$active_notifications[] = $notification;
			}
		}

		return $active_notifications;
	}

	/**
	 * Update the notifications.
	 *
	 * @return void
	 */
	public function updateNotifications()
	{
		$notifications = $this->fetchNotifications();
		$option = $this->getOptionValue();

		update_option(
			$this->getOptionName(),
			[
				'update' => time(),
				'feed' => $notifications,
				'events' => $option['events'],
				'dismissed' => $option['dismissed'],
			]
		);
	}

	/**
	 * Enqueue the scripts for the notifications.
	 *
	 * @return void
	 */
	public function enqueueScripts()
	{
		if (!$this->hasAccess()) {
			return;
		}

		$notifications = $this->getNotifications();
		if (empty($notifications)) {
			return;
		}

		wp_enqueue_style(
			'sbtt-admin-notifications',
			SBTT_PLUGIN_URL . "assets/css/admin-notifications.css",
			array(),
			SBTTVER
		);

		wp_enqueue_script(
			'sbtt-admin-notifications',
			SBTT_PLUGIN_URL . "assets/js/admin-notifications.js",
			array('jquery'),
			SBTTVER,
			true
		);

		wp_localize_script('sbtt-admin-notifications', 'sbtt_admin', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'sbtt_nonce' => wp_create_nonce('sbtt-nonce'),
		));
	}

	/**
	 * Output the notifications.
	 *
	 * @return null|string
	 */
	public function outputNotifications()
	{
		if (isset($_GET['feed_id'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}

		$notifications = $this->getNotifications();
		if (empty($notifications)) {
			return null;
		}

		$wrapper_class = $this->getWrapperClass($notifications[0]['id']);
		$dismiss_button = $this->getDismissButtonHtml($notifications[0]['id']);
		$navigation_buttons = $this->getNavigationButtonsHtml(count($notifications));

		$notifications_html = array_map(function ($notification, $index) {
			return $this->renderNotification($notification, $index);
		}, $notifications, array_keys($notifications));
		$notifications_content = sprintf('<div class="messages">%s</div>', implode('', $notifications_html));

		return sprintf(
			'<div class="sbtt-notifications-wrap%s" id="sbtt-notifications">%s%s%s</div>',
			esc_attr($wrapper_class),
			wp_kses_post($dismiss_button),
			wp_kses_post($navigation_buttons),
			wp_kses_post($notifications_content)
		);
	}

	/**
	 * Dismiss the notification.
	 *
	 * @return void
	 */
	public function dismissNotification()
	{
		check_ajax_referer('sbtt-nonce', 'sbtt_nonce');

		if (!$this->hasAccess() || empty($_POST['id'])) {
			wp_send_json_error('No access or no id');
		}

		$notification_id = sanitize_text_field(wp_unslash($_POST['id']));

		$option = $this->getOptionValue();
		$type   = is_numeric($notification_id) ? 'feed' : 'events';

		$option['dismissed'][] = $notification_id;
		$option['dismissed']   = array_unique($option['dismissed']);

		$option[$type] = array_filter($option[$type], function ($notification) use ($notification_id) {
			return $notification['id'] != $notification_id;
		});

		update_option($this->getOptionName(), $option);

		wp_send_json_success();
	}

	/**
	 * Filter the notifications for the plugin.
	 *
	 * @param array $notifications The notifications to filter.
	 *
	 * @return array
	 */
	protected function filterNotifications($notifications)
	{
		$plugin = self::PLUGIN;
		$license = Utils::sbtt_is_pro() ? 'pro' : 'free';

		return array_filter($notifications, function ($notification) use ($plugin, $license) {
			return isset($notification['plugin'])
				&& in_array($plugin, $notification['plugin'], true)
				&& !empty($notification['type'])
				&& in_array($license, $notification['type'], true);
		});
	}

	/**
	 * Check if a notification is valid.
	 *
	 * @param array $notification The notification to check.
	 *
	 * @return bool
	 */
	protected function isNotificationValid($notification)
	{
		if (!$this->checkVersionRequirements($notification)) {
			return false;
		}

		if (!$this->checkStatusRequirement($notification)) {
			return false;
		}

		if ($this->isDismissed($notification) || $this->isExpired($notification)) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the notification is dismissed.
	 *
	 * @param array $notification The notification to check.
	 *
	 * @return bool
	 */
	protected function isDismissed($notification)
	{
		$option = $this->getOptionValue();
		return !empty($option['dismissed']) && in_array($notification['id'], $option['dismissed']);
	}

	/**
	 * Check if the notification is expired.
	 *
	 * @param array $notification The notification to check.
	 *
	 * @return bool
	 */
	protected function isExpired($notification)
	{
		return !empty($notification['end']) && time() > strtotime($notification['end']);
	}

	/**
	 * Check version requirements for a notification.
	 *
	 * @param array $notification The notification to check.
	 *
	 * @return bool
	 */
	protected function checkVersionRequirements($notification)
	{
		$wp_version = get_bloginfo('version');
		if (!empty($notification['maxwpver']) && version_compare($wp_version, $notification['maxwpver'], '>')) {
			return false;
		}

		if (!empty($notification['maxver']) && version_compare($notification['maxver'], SBTTVER) < 0) {
			return false;
		}

		if (!empty($notification['minver']) && version_compare($notification['minver'], SBTTVER) > 0) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the notification's status requirement is met.
	 *
	 * @param array $notification The notification to check.
	 *
	 * @return bool
	 */
	protected function checkStatusRequirement($notification)
	{
		if (empty($notification['statuscheck'])) {
			return true;
		}

		$status_key = sanitize_key($notification['statuscheck']);
		$sbtt_statuses_option = get_option('sbtt_statuses', array());

		return !empty($sbtt_statuses_option[$status_key]);
	}

	/**
	 * Check if the plugin is recently installed.
	 *
	 * @return bool
	 */
	protected function isRecentlyInstalled()
	{
		$sbtt_statuses = get_option('sbtt_statuses', array());
		if (empty($sbtt_statuses['first_install'])) {
			return false;
		}

		return (int)$sbtt_statuses['first_install'] > time() - 7 * DAY_IN_SECONDS;
	}

	/**
	 * Get the wrapper class based on the first notification type.
	 *
	 * @param string $notification_id The ID of the notification.
	 * @return string The wrapper class.
	 */
	protected function getWrapperClass($notification_id)
	{
		$class_map = [
			'review' => ' sbtt_review_notice',
			'discount' => ' sbtt_discount_notice',
		];

		return $class_map[$notification_id] ?? '';
	}

	/**
	 * Render a single notification.
	 *
	 * @param array $notification The notification to render.
	 * @param int   $index The index of the notification.
	 * @return string The notification HTML.
	 */
	protected function renderNotification($notification, $index)
	{
		$notifications_html = '';
		$type = $notification['id'];
		$buttons_html = $this->getButtonsHtml($notification);
		$image_html = $this->getImageHtml($notification);
		$content = $this->getNotificationContent($notification);
		$class = $index === 0 ? ' current' : '';

		// if type is review and consent is not set, add a consent step.
		$sbtt_review_consent = get_option('sbtt_review_consent', false);
		if ($type === 'review' && !$sbtt_review_consent) {
			$notifications_html .= $this->getConsentStep($notification);
			$class = ' sbtt_review_step2_notice';
		}

		$notifications_html .= sprintf(
			'<div class="message%s" data-message-id="%s">%s%s%s</div>',
			esc_attr($class),
			esc_attr($type),
			$image_html,
			$content,
			$buttons_html
		);

		return $notifications_html;
	}

	/**
	 * Get Consent for Review Notice
	 *
	 * @param array $notification The notification to get the consent step for.
	 *
	 * @return string The consent step HTML.
	 */
	protected function getConsentStep($notification)
	{
		$image_html = $this->getImageHtml($notification);
		$sbtt_open_feedback_url = 'https://smashballoon.com/feedback/?plugin=feeds-for-tiktok';

		$consent_step = sprintf(
			'<div class="message current sbtt_review_step1_notice" data-message-id="review">
				%s
				<h3 class="title">%s</h3>
				<div class="sbtt-notice-consent-btns">
					<button class="sbtt-btn-link" id="sbtt_review_consent_yes">%s</button>
					<a href="%s" target="_blank" class="sbtt-btn-link" id="sbtt_review_consent_no">%s</a>
				</div>
			</div>',
			$image_html,
			esc_html__('Are you enjoying the TikTok Feeds Plugin?', 'feeds-for-tiktok'),
			esc_html__('Yes', 'feeds-for-tiktok'),
			esc_url($sbtt_open_feedback_url),
			esc_html__('No', 'feeds-for-tiktok')
		);

		return $consent_step;
	}

	/**
	 * Get the buttons HTML.
	 *
	 * @param array $notification The notification to get the buttons HTML for.
	 *
	 * @return string
	 */
	protected function getButtonsHtml($notification)
	{
		if (empty($notification['btns']) || !is_array($notification['btns'])) {
			return '';
		}

		$buttons_html = '';
		foreach ($notification['btns'] as $btn_type => $btn) {
			$class = $this->getButtonClass($notification, $btn, $btn_type);
			$url = $this->getButtonUrl($notification, $btn);
			$target = !empty($btn['attr']) ? '_blank' : '';
			$rel = $target === '_blank' ? 'noopener' : '';
			$text = !empty($btn['text']) ? sanitize_text_field($btn['text']) : '';

			$buttons_html .= sprintf(
				'<a href="%s" class="%s" target="%s" rel="%s">%s</a>',
				esc_url($url),
				esc_attr($class),
				esc_attr($target),
				esc_attr($rel),
				esc_html($text)
			);
		}

		$buttons_html = '<div class="buttons">' . $buttons_html . '</div>';

		return $buttons_html;
	}

	/**
	 * Get the button class based on notification type and button type.
	 *
	 * @param array  $notification The notification data.
	 * @param array  $btn The button data.
	 * @param string $btn_type The type of button.
	 * @return string The CSS class for the button.
	 */
	protected function getButtonClass($notification, $btn, $btn_type)
	{
		$notification_type = $notification['id'];
		$base_class = 'sbtt-btn';

		$type_class = in_array($notification_type, ['review', 'discount']) ? 'sbtt-btn-blue' : 'sbtt-btn-orange';
		$btn_class = $btn_type === 'primary' ? $type_class : 'sbtt-btn-grey';

		if (isset($btn['class']) && !empty($btn['class'])) {
			$btn_class .= ' ' . $btn['class'];
		}

		return $base_class . ' ' . $btn_class;
	}

	/**
	 * Get the button URL, handling both string and array formats.
	 *
	 * @param array $notification The notification data.
	 * @param array $btn The button data.
	 * @return string The processed URL.
	 */
	protected function getButtonUrl($notification, $btn)
	{
		$notification_type = $notification['id'];
		if (is_array($btn['url'])) {
			$args = array_map('sanitize_key', $btn['url']);
			return wp_nonce_url(add_query_arg($args), 'sbtt-' . $notification_type, 'sbtt_nonce');
		}

		return $this->replaceMergeFields($btn['url'], $notification);
	}

	/**
	 * Get the image HTML.
	 *
	 * @param array $notification The notification to get the image HTML for.
	 * @return string The image HTML.
	 */
	protected function getImageHtml($notification)
	{
		$image = $this->getImageData($notification);

		$image_html = str_replace('{src}', esc_url($image['src']), $image['wrap']);
		$image_html = str_replace('{alt}', esc_attr($image['alt']), $image_html);

		if (isset($image['overlay']) && isset($image['overlay_wrap'])) {
			$overlay_html = str_replace('{overlay}', $image['overlay'], $image['overlay_wrap']);
			$image_html = str_replace('{overlay}', $overlay_html, $image_html);
		}

		return $image_html;
	}

	/**
	 * Get the image data for the notification.
	 *
	 * @param array $notification The notification to get the image data for.
	 * @return array The image data.
	 */
	protected function getImageData($notification)
	{
		if (empty($notification['image'])) {
			return [
				'src'  => SBTT_PLUGIN_URL . 'assets/images/sbtt-bell.svg',
				'alt'  => 'notice',
				'wrap' => '<div class="bell"><img src="{src}" alt="{alt}"></div>',
			];
		}

		if ($notification['image'] === 'balloon') {
			return [
				'src'  => SBTT_PLUGIN_URL . 'assets/images/balloon.svg',
				'alt'  => 'notice',
				'wrap' => '<div class="bell"><img src="{src}" alt="{alt}"></div>',
			];
		}

		$image_filename = sanitize_text_field(str_replace('sbi', 'sbtt', $notification['image']));
		$image = [
			'src'  => SBTT_PLUGIN_URL . 'assets/images/' . $image_filename,
			'alt'  => 'notice',
		];

		if (in_array($notification['id'], ['review', 'discount'], true)) {
			$image['wrap'] = '<div class="bell"><img src="{src}" alt="{alt}"></div>';
		} else {
			$image['overlay'] = $notification['image_overlay'] ?? '';
			$image['overlay'] = str_replace('%', '%%', $image['overlay']);
			$image['overlay_wrap'] = '<div class="overlay">{overlay}</div>';
			$image['wrap'] = '<div class="thumb"><img src="{src}" alt="{alt}">{overlay}</div>';
		}

		return $image;
	}

	/**
	 * Replace the merge fields in the content.
	 *
	 * @param string $content The content to replace the merge fields in.
	 * @param array  $notification The notification to replace the merge fields in.
	 *
	 * @return string
	 */
	public function replaceMergeFields($content, $notification)
	{
		$merge_fields = array(
			'{plugin}' => 'TikTok Feeds',
			'{amount}' => $notification['amount'] ?? '',
			'{platform}' => 'TikTok',
			'{lowerplatform}' => 'tiktok',
			'{review-url}' => 'https://wordpress.org/support/plugin/feeds-for-tiktok/reviews/',
			'{slug}' => 'tiktok-feed',
			'{campaign}' => 'tiktok-free'
		);

		if (Utils::sbtt_is_pro()) {
			$merge_fields['{campaign}'] = 'tiktok-pro';
			$merge_fields['{plugin}'] = 'TikTok Feeds Pro';
		}

		foreach ($merge_fields as $find => $replace) {
			$content = str_replace($find, $replace, $content);
		}

		return $content;
	}

	/**
	 * Get the notification content.
	 *
	 * @param array $notification The notification to get the content for.
	 * @return string The notification content.
	 */
	protected function getNotificationContent($notification)
	{
		$content_allowed_tags = [
			'em'     => [],
			'strong' => [],
			'span'   => ['style' => []],
			'a'      => ['href' => [], 'target' => [], 'rel' => []],
		];

		$title = $this->getProcessedTitle($notification);
		$content = $this->getProcessedContent($notification, $content_allowed_tags);

		return $title . $content;
	}

	/**
	 * Process and return the notification title.
	 *
	 * @param array $notification The notification data.
	 * @return string The processed title HTML.
	 */
	protected function getProcessedTitle($notification)
	{
		if (empty($notification['title'])) {
			return '';
		}

		$title = $this->replaceMergeFields(sanitize_text_field($notification['title']), $notification);
		return sprintf('<h3 class="title">%s</h3>', $title);
	}

	/**
	 * Process and return the notification content.
	 *
	 * @param array $notification The notification data.
	 * @param array $allowed_tags The allowed HTML tags.
	 * @return string The processed content HTML.
	 */
	protected function getProcessedContent($notification, $allowed_tags)
	{
		if (empty($notification['content'])) {
			return '';
		}

		$content = $this->replaceMergeFields($notification['content'], $notification);
		$sanitized_content = wp_kses($content, $allowed_tags);
		return sprintf('<p class="content">%s</p>', $sanitized_content);
	}

	/**
	 * Get the navigation buttons HTML.
	 *
	 * @param int $count The number of notifications.
	 * @return string The navigation buttons HTML.
	 */
	protected function getNavigationButtonsHtml($count)
	{
		if ($count <= 1) {
			return '';
		}

		$button_template = '<a class="%s disabled" title="%s"><img src="%s" alt="%s"></a>';
		$buttons = [
			'prev' => [
				'title' => __('Previous message', 'feeds-for-tiktok'),
				'image' => 'sbtt-carousel-prev.svg'
			],
			'next' => [
				'title' => __('Next message', 'feeds-for-tiktok'),
				'image' => 'sbtt-carousel-next.svg'
			]
		];

		$html = '<div class="navigation">';
		foreach ($buttons as $class => $data) {
			$html .= sprintf(
				$button_template,
				$class,
				esc_attr($data['title']),
				esc_url(SBTT_PLUGIN_URL . 'assets/images/' . $data['image']),
				esc_attr($data['title'])
			);
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get the dismiss button HTML.
	 *
	 * @param string $type The type of notification.
	 * @return string The dismiss button HTML.
	 */
	protected function getDismissButtonHtml($type)
	{
		$dismiss_text = __('Dismiss this message', 'feeds-for-tiktok');
		$dismiss_alt = __('Dismiss', 'feeds-for-tiktok');
		$icon_url = SBTT_PLUGIN_URL . 'assets/images/sbtt-dismiss-icon.svg';

		$dismiss_url = '';
		if ($type === 'review' || $type === 'discount') {
			$dismiss_url = wp_nonce_url(add_query_arg(array('sbtt_dismiss' => $type)), 'sbtt-' . $type, 'sbtt_nonce');
		}

		$href_attr = $dismiss_url ? sprintf(' href="%s"', esc_url($dismiss_url)) : '';

		return sprintf(
			'<a class="dismiss" title="%s"%s>
				<img src="%s" alt="%s">
			</a>',
			esc_attr($dismiss_text),
			$href_attr,
			esc_url($icon_url),
			esc_attr($dismiss_alt)
		);
	}
}
