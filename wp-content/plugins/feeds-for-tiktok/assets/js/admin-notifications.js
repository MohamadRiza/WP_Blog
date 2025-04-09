/**
 * SBTT Admin Notifications.
 *
 * @since 1.4
 */

'use strict';

var SBTTAdminNotifications = window.SBTTAdminNotifications || (function (document, window, $) {

	/**
	 * Elements holder.
	 *
	 * @since 1.4
	 *
	 * @type {object}
	 */
	var el = {

		$notifications: $('#sbtt-notifications'),
		$nextButton: $('#sbtt-notifications .navigation .next'),
		$prevButton: $('#sbtt-notifications .navigation .prev'),
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.4
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.4
		 */
		init: function () {
			el.$notifications.find('.messages a').each(function () {
				if ($(this).attr('href').indexOf('dismiss=') > -1) {
					$(this).addClass('button-dismiss');
				}
			})

			$(app.ready);
		},

		/**
		 * Document ready.
		 *
		 * @since 1.4
		 */
		ready: function () {

			app.updateNavigation();
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.4
		 */
		events: function () {

			el.$notifications
				.on('click', '.dismiss', app.dismiss)
				.on('click', '.button-dismiss', app.buttonDismiss)
				.on('click', '.next', app.navNext)
				.on('click', '.prev', app.navPrev)
				.on('click', '#sbtt_review_consent_yes', app.handleReviewConsentYes)
				.on('click', '#sbtt_review_consent_no', app.handleReviewConsentNo);
		},

		/**
		 * Click on a dismiss button.
		 *
		 * @since 1.4
		 */
		buttonDismiss: function (event) {
			event.preventDefault();
			app.dismiss();
		},

		/**
		 * Click on the Dismiss notification button.
		 *
		 * @since 1.4
		 *
		 * @param {object} event Event object.
		 */
		dismiss: function (event) {

			if (el.$currentMessage.length === 0) {
				return;
			}

			// Remove notification.
			var $nextMessage = el.$nextMessage.length < 1 ? el.$prevMessage : el.$nextMessage,
				messageId = el.$currentMessage.data('message-id');

			if ($nextMessage.length === 0) {
				el.$notifications.remove();
			} else {
				el.$currentMessage.remove();
				$nextMessage.addClass('current');
				app.updateNavigation();
			}

			// AJAX call - update option.
			var data = {
				action: 'sbtt_dashboard_notification_dismiss',
				sbtt_nonce: sbtt_admin.sbtt_nonce,
				id: messageId,
			};

			$.post(sbtt_admin.ajax_url, data, function (res) {
			}).fail(function (xhr, textStatus, e) {
			});
		},

		/**
		 * Click on the Next notification button.
		 *
		 * @since 1.4
		 *
		 * @param {object} event Event object.
		 */
		navNext: function (event) {

			if (el.$nextButton.hasClass('disabled')) {
				return;
			}

			el.$currentMessage.removeClass('current');
			el.$nextMessage.addClass('current');
			if (!$('.message[data-message-id="review"]').hasClass('current')) {
				$('.sbtt_review_step1_notice').hide()
			}

			app.updateNavigation();
		},

		/**
		 * Click on the Previous notification button.
		 *
		 * @since 1.4
		 *
		 * @param {object} event Event object.
		 */
		navPrev: function (event) {

			if (el.$prevButton.hasClass('disabled')) {
				return;
			}

			el.$currentMessage.removeClass('current');
			el.$prevMessage.addClass('current');
			if ($('.message[data-message-id="review"]').hasClass('current') && $('.message[data-message-id="review"]').is(":hidden")) {
				$('.sbtt_review_step1_notice').show()
			}

			app.updateNavigation();
		},

		/**
		 * Update navigation buttons.
		 *
		 * @since 1.4
		 */
		updateNavigation: function () {

			el.$currentMessage = el.$notifications.find('.message.current');
			el.$nextMessage = el.$currentMessage.next('.message');
			el.$prevMessage = el.$currentMessage.prev('.message');

			if (el.$notifications.find('.sbtt_review_step1_notice').length > 0) {
				var is = el.$currentMessage.hasClass('sbtt_review_step1_notice');
				var isReviewStep2 = el.$currentMessage.prev('.message').hasClass('sbtt_review_step2_notice');

				el.$nextMessage = is ? el.$currentMessage.next('.message').next('.message') : el.$nextMessage;
				el.$prevMessage = isReviewStep2 ? el.$currentMessage.prev('.message').prev('.message') : el.$prevMessage;
			}

			if (el.$nextMessage.length === 0) {
				el.$nextButton.addClass('disabled');
			} else {
				el.$nextButton.removeClass('disabled');
			}

			if (el.$prevMessage.length === 0) {
				el.$prevButton.addClass('disabled');
			} else {
				el.$prevButton.removeClass('disabled');
			}
		},

		handleReviewConsentNo: function () {
			el.$notifications.remove();

			// AJAX call - update option.
			var data = {
				action: 'sbtt_review_notice_consent_update',
				consent: 'no',
				sbtt_nonce: sbtt_admin.sbtt_nonce,
			};

			$.post(sbtt_admin.ajax_url, data, function (res) {
			}).fail(function (xhr, textStatus, e) {
			});
		},

		handleReviewConsentYes: function () {
			if (el.$notifications.find('.sbtt_review_step1_notice').length > 0) {
				el.$nextMessage = el.$currentMessage.next('.message');
				el.$currentMessage.remove();
				el.$nextMessage.addClass('current');
				app.updateNavigation();
			}

			// AJAX call - update option.
			var data = {
				action: 'sbtt_review_notice_consent_update',
				consent: 'yes',
				sbtt_nonce: sbtt_admin.sbtt_nonce,
			};

			$.post(sbtt_admin.ajax_url, data, function (res) {
			}).fail(function (xhr, textStatus, e) {
			});
		},
	};

	return app;

}(document, window, jQuery));

// Initialize.
SBTTAdminNotifications.init();
