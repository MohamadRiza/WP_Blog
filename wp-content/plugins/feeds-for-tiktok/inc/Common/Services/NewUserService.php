<?php

namespace SmashBalloon\TikTokFeeds\Common\Services;

use SmashBalloon\TikTokFeeds\Common\Utils;

class NewUserService extends NotificationService
{
	protected const SOURCE_URL = 'https://plugin.smashballoon.com/newuser.json';
	protected const OPTION_NAME = 'sbtt_newuser_notifications';

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
	 * Registers the action hooks for notifications.
	 *
	 * @return void
	 */
	public function register()
	{
		add_action('admin_notices', array($this, 'outputNotifications'));
		add_action('admin_init', array($this, 'dismissNotification'));
		add_action('wp_ajax_sbtt_review_notice_consent_update', array($this, 'handleReviewConsent'));
	}

	/**
	 * Output the notifications.
	 *
	 * @return void
	 */
	public function outputNotifications()
	{
		if (isset($_GET['feed_id'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$notifications = $this->getNotifications();
		if (empty($notifications)) {
			return;
		}

		$wrapper_class = $this->getWrapperClass($notifications[0]['id']);
		$dismiss_button = $this->getDismissButtonHtml($notifications[0]['id']);
		$navigation_buttons = $this->getNavigationButtonsHtml(count($notifications));

		$notifications_html = array_map(function ($notification, $index) {
			return $this->renderNotification($notification, $index);
		}, $notifications, array_keys($notifications));
		$notifications_content = sprintf('<div class="messages">%s</div>', implode('', $notifications_html));

		printf(
			'<div class="sbtt-notifications-wrap%s" id="sbtt-notifications">%s%s%s</div>',
			esc_attr($wrapper_class),
			wp_kses_post($dismiss_button),
			wp_kses_post($navigation_buttons),
			wp_kses_post($notifications_content)
		);
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
		if (empty($option['update'])) {
			$this->updateNotifications();
		}

		$events = !empty($option['events']) ? $this->verifyActiveNotifications($option['events']) : [];
		$feed = !empty($option['feed']) ? $this->verifyActiveNotifications($option['feed']) : [];

		return array_merge($events, $feed);
	}

	/**
	 * Verify the active notifications.
	 *
	 * @param array $notifications The notifications to verify.
	 * @return array
	 */
	public function verifyActiveNotifications($notifications)
	{
		if (! is_array($notifications) || empty($notifications)) {
			return array();
		}

		foreach ($notifications as $notification) {
			if (!empty($notification['id'])) {
				if ($notification['id'] === 'review' && $this->showReviewNotice($notification)) {
					return array($notification);
				}

				if ($notification['id'] === 'discount' && $this->showDiscountNotice($notification)) {
					return array($notification);
				}
			}
		}

		return array();
	}

	/**
	 * Update the notifications based on the above sample content and desired content
	 *
	 * @param array $notifications The notifications to verify.
	 * @return array
	 */
	public function verifyNotifications($notifications)
	{
		if (! is_array($notifications) || empty($notifications)) {
			return [];
		}

		$activeNotifications = [];
		foreach ($notifications as $notification) {
			$content = $this->getNoticeAndDiscountContent($notification['id']);
			if ($content) {
				$notification = array_merge($notification, $content);
			}

			if ($this->isNotificationValid($notification)) {
				$activeNotifications[] = $notification;
			}
		}

		return $activeNotifications;
	}

	/**
	 * Dismiss the notification.
	 *
	 * @return void
	 */
	public function dismissNotification()
	{
		$this->handleRatingNoticeDismissal();
		$this->handleNewUserSaleNoticeDismissal();
		$this->handleBlackFridaySaleNoticeDismissal();
		$this->handleGeneralNoticeDismissal();
	}

	/**
	 * Handle the review notice consent update.
	 *
	 * @return void
	 */
	public function handleReviewConsent()
	{
		check_ajax_referer('sbtt-nonce', 'sbtt_nonce');

		if (!$this->hasAccess()) {
			wp_send_json_error(__('Access denied', 'feeds-for-tiktok'));
		}

		$consent = isset($_POST['consent']) ? sanitize_text_field($_POST['consent']) : '';
		update_option('sbtt_review_consent', $consent);

		if ('no' === $consent) {
			$sbtt_statuses = get_option('sbtt_statuses', array());
			update_option('sbtt_rating_notice', 'dismissed', false);
			$sbtt_statuses['rating_notice_dismissed'] = current_time('timestamp');
			update_option('sbtt_statuses', $sbtt_statuses, false);
		}

		wp_send_json_success();
	}

	/**
	 * Check if the notification is valid.
	 *
	 * @param array $notification The notification to check.
	 * @return bool
	 */
	protected function isNotificationValid($notification)
	{
		if (empty($notification['content'])) {
			return false;
		}

		if ($this->isDismissed($notification)) {
			return false;
		}

		return true;
	}

	/**
	 * Get the notice and discount content.
	 *
	 * @param string $id The ID of the notice.
	 * @return array|null
	 */
	private function getNoticeAndDiscountContent($id)
	{
		$content = [
			'review' => [
				'title' => __('Glad to hear you are enjoying it. Would you consider leaving a positive review?', 'feeds-for-tiktok'),
				'content' => __('It really helps to support the plugin and help others to discover it too!', 'feeds-for-tiktok'),
			],
			'discount' => [
				'title' => __('Exclusive offer - 60% off!', 'feeds-for-tiktok'),
				'content' => __('We don’t run promotions very often, but for a limited time we’re offering 60% Off our Pro version to all users of our free TikTok Feeds.', 'feeds-for-tiktok'),
			],
		];

		return $content[$id] ?? null;
	}

	/**
	 * Show the discount notice.
	 *
	 * @param array $notification The notification to show.
	 * @return bool
	 */
	private function showDiscountNotice($notification)
	{
		if (Utils::sbtt_is_pro()) {
			return false;
		}

		$sbtt_statuses = get_option('sbtt_statuses', array());
		if (empty($sbtt_statuses['first_install'])) {
			return false;
		}

		$ignore_new_user_sale_notice = get_user_meta(get_current_user_id(), 'sbtt_ignore_new_user_sale_notice', true);
		if ($ignore_new_user_sale_notice === 'always') {
			return false;
		}

		if (time() > (int)$sbtt_statuses['first_install'] + (int)$notification['wait'] * DAY_IN_SECONDS) {
			return true;
		}

		return false;
	}

	/**
	 * Show the review notice.
	 *
	 * @param array $notification The notification to show.
	 * @return bool
	 */
	private function showReviewNotice($notification)
	{
		$sbtt_statuses = get_option('sbtt_statuses', array());
		if (empty($sbtt_statuses['first_install'])) {
			return false;
		}

		$sbtt_rating_notice = get_option('sbtt_rating_notice', false);
		$sbtt_rating_notice_waiting = get_transient('tiktok_feed_rating_notice_waiting');

		if ($sbtt_rating_notice_waiting === 'waiting' || $sbtt_rating_notice === 'dismissed') {
			return false;
		}

		if (time() > (int)$sbtt_statuses['first_install'] + (int)$notification['wait'] * DAY_IN_SECONDS) {
			return true;
		}

		return false;
	}

	/**
	 * Handle the rating notice dismissal.
	 *
	 * @return void
	 */
	private function handleRatingNoticeDismissal()
	{
		if (!isset($_GET['sbtt_ignore_rating_notice_nag'])) {
			return;
		}

		if (!wp_verify_nonce($_GET['sbtt_nonce'] ?? '', 'sbtt-review')) {
			return;
		}

		$rating_ignore = sanitize_text_field($_GET['sbtt_ignore_rating_notice_nag']);
		$sbtt_statuses = get_option('sbtt_statuses', array());

		if ('1' === $rating_ignore) {
			update_option('sbtt_rating_notice', 'dismissed', false);
			$sbtt_statuses['rating_notice_dismissed'] = current_time('timestamp');
			update_option('sbtt_statuses', $sbtt_statuses, false);
		} elseif ('later' === $rating_ignore) {
			set_transient('tiktok_feed_rating_notice_waiting', 'waiting', 2 * WEEK_IN_SECONDS);
			delete_option('sbtt_review_consent');
			update_option('sbtt_rating_notice', 'pending', false);
		}
	}

	/**
	 * Handle the new user sale notice dismissal.
	 *
	 * @return void
	 */
	private function handleNewUserSaleNoticeDismissal()
	{
		if (!isset($_GET['sbtt_ignore_new_user_sale_notice'])) {
			return;
		}

		if (!wp_verify_nonce($_GET['sbtt_nonce'] ?? '', 'sbtt-discount')) {
			return;
		}

		$new_user_ignore = sanitize_text_field($_GET['sbtt_ignore_new_user_sale_notice']);

		if ('always' === $new_user_ignore) {
			update_user_meta(get_current_user_id(), 'sbtt_ignore_new_user_sale_notice', 'always');

			$current_month = (int) gmdate('n');
			if ($current_month > 5) {
				update_user_meta(get_current_user_id(), 'sbtt_ignore_bfcm_sale_notice', gmdate('Y'));
			}
		}
	}

	/**
	 * Handle the black friday sale notice dismissal.
	 *
	 * @return void
	 */
	private function handleBlackFridaySaleNoticeDismissal()
	{
		if (!isset($_GET['sbtt_ignore_bfcm_sale_notice'])) {
			return;
		}

		if (!wp_verify_nonce($_GET['sbtt_nonce'] ?? '', 'sbtt-bfcm')) {
			return;
		}

		$bfcm_ignore = sanitize_text_field($_GET['sbtt_ignore_bfcm_sale_notice']);

		if ('always' === $bfcm_ignore) {
			update_user_meta(get_current_user_id(), 'sbtt_ignore_bfcm_sale_notice', 'always');
		} elseif (gmdate('Y') === $bfcm_ignore) {
			update_user_meta(get_current_user_id(), 'sbtt_ignore_bfcm_sale_notice', gmdate('Y'));
		}
		update_user_meta(get_current_user_id(), 'sbtt_ignore_new_user_sale_notice', 'always');
	}

	/**
	 * Handle the general notice dismissal.
	 *
	 * @return void
	 */
	private function handleGeneralNoticeDismissal()
	{
		if (!isset($_GET['sbtt_dismiss'])) {
			return;
		}

		$notice_dismiss = sanitize_text_field($_GET['sbtt_dismiss']);
		$nonce_action = 'review' === $notice_dismiss ? 'sbtt-review' : 'sbtt-discount';

		if (!wp_verify_nonce($_GET['sbtt_nonce'] ?? '', $nonce_action)) {
			return;
		}

		if ('review' === $notice_dismiss) {
			$sbtt_statuses = get_option('sbtt_statuses', []);
			update_option('sbtt_rating_notice', 'dismissed', false);
			$sbtt_statuses['rating_notice_dismissed'] = time();
			update_option('sbtt_statuses', $sbtt_statuses, false);

			update_user_meta(get_current_user_id(), 'sbtt_ignore_new_user_sale_notice', 'always');
		} elseif ('discount' === $notice_dismiss) {
			$current_month_number = (int) gmdate('n');
			if ($current_month_number > 5) {
				update_user_meta(get_current_user_id(), 'sbtt_ignore_bfcm_sale_notice', gmdate('Y'));
			}

			update_user_meta(get_current_user_id(), 'sbtt_ignore_new_user_sale_notice', 'always');
		}
	}
}
