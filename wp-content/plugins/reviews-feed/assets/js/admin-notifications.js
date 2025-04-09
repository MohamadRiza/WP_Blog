/**
 * SBR Admin Notifications.
 *
 * @since 2.18
 */

'use strict';

var SBRAdminNotifications = window.SBRAdminNotifications || ( function( document, window, $ ) {

	/**
	 * Elements holder.
	 *
	 * @since 2.18
	 *
	 * @type {object}
	 */
	var el = {

		$notifications:    $( '#sbr-notifications' ),
		$nextButton:       $( '#sbr-notifications .navigation .next' ),
		$prevButton:       $( '#sbr-notifications .navigation .prev' ),
		$adminBarCounter:  $( '#wp-admin-bar-wpforms-menu .sbr-menu-notification-counter' ),
		$adminBarMenuItem: $( '#wp-admin-bar-sbr-notifications' ),

	};

	/**
	 * Public functions and properties.
	 *
	 * @since 2.18
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 2.18
		 */
		init: function() {
			el.$notifications.find( '.messages a').each(function() {
				if ($(this).attr('href').indexOf('dismiss=') > -1 ) {
					$(this).addClass('button-dismiss');
				}
			})

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 2.18
		 */
		ready: function() {

			app.updateNavigation();
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 2.18
		 */
		events: function() {

			el.$notifications
				.on( 'click', '.dismiss', app.dismiss )
				.on( 'click', '.button-dismiss', app.buttonDismiss )
				.on( 'click', '.next', app.navNext )
				.on( 'click', '.prev', app.navPrev );
		},

		/**
		 * Click on a dismiss button.
		 *
		 * @since 2.18
		 */
		buttonDismiss: function( event ) {
			event.preventDefault();
			app.dismiss();
		},

		/**
		 * Click on the Dismiss notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		dismiss: function( event ) {

			if ( el.$currentMessage.length === 0 ) {
				return;
			}

			// Update counter.
			var count = parseInt( el.$adminBarCounter.text(), 10 );
			if ( count > 1 ) {
				--count;
				el.$adminBarCounter.html( '<span>' + count + '</span>' );
			} else {
				el.$adminBarCounter.remove();
				el.$adminBarMenuItem.remove();
			}

			// Remove notification.
			var $nextMessage = el.$nextMessage.length < 1 ? el.$prevMessage : el.$nextMessage,
				messageId = el.$currentMessage.data( 'message-id' );

			if ( $nextMessage.length === 0 ) {
				el.$notifications.remove();
			} else {
				el.$currentMessage.remove();
				$nextMessage.addClass( 'current' );
				app.updateNavigation();
			}

			// AJAX call - update option.
			var data = {
				action: 'sbr_dashboard_notification_dismiss',
				nonce: sbrA.sbr_nonce,
				id: messageId,
			};

			$.post( sbrA.ajax_url, data, function( res ) {

				if ( ! res.success ) {
					//SBRAdmin.debug( res );
				}
			} ).fail( function( xhr, textStatus, e ) {

				//SBRAdmin.debug( xhr.responseText );
			} );
		},

		/**
		 * Click on the Next notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		navNext: function( event ) {

			if ( el.$nextButton.hasClass( 'disabled' ) ) {
				return;
			}


			el.$currentMessage.removeClass( 'current' );
			el.$nextMessage.addClass( 'current' );
			if( !$('.message[data-message-id="review"]').hasClass('current') ){
				$('.sbr_review_step1_notice').hide()
			}

			app.updateNavigation();
		},

		/**
		 * Click on the Previous notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		navPrev: function( event ) {

			if ( el.$prevButton.hasClass( 'disabled' ) ) {
				return;
			}

			el.$currentMessage.removeClass( 'current' );
			el.$prevMessage.addClass( 'current' );
			if( $('.message[data-message-id="review"]').hasClass('current') && $('.message[data-message-id="review"]').is(":hidden")){
				$('.sbr_review_step1_notice').show()
			}
			app.updateNavigation();
		},

		/**
		 * Update navigation buttons.
		 *
		 * @since 2.18
		 */
		updateNavigation: function() {

			el.$currentMessage = el.$notifications.find( '.message.current' );
			el.$nextMessage = el.$currentMessage.next( '.message' );
			el.$prevMessage = el.$currentMessage.prev( '.message' );

			if ( el.$nextMessage.length === 0 ) {
				el.$nextButton.addClass( 'disabled' );
			} else {
				el.$nextButton.removeClass( 'disabled' );
			}

			if ( el.$prevMessage.length === 0 ) {
				el.$prevButton.addClass( 'disabled' );
			} else {
				el.$prevButton.removeClass( 'disabled' );
			}
		},
	};

	return app;

}( document, window, jQuery ) );

// Initialize.
SBRAdminNotifications.init();

jQuery(document).ready(function($) {
	/**
	 * Dismiss the renewed license notice
	 *
	 * @since 4.0
	 */
	$(document).on('click', "#sbr-hide-notice", function() {
		let sbrLicenseNotice = $('#sbr-license-notice');
		let sbrLicenseModal = $('.sbr-sb-modal');
		sbrLicenseNotice.remove();
		sbrLicenseModal.remove();
	});

	/**
	 * Dismiss the license notice on dashboard page
	 *
	 * @since 4.0
	 */
	$(document).on('click', "#sb-dismiss-notice", function() {
		let sbrLicenseNotice = $('#sbr-license-notice');
		let sbrLicenseModal = $('.sbr-sb-modal');
		sbrLicenseNotice.remove();
		sbrLicenseModal.remove();
		$.ajax({
			url: ajaxurl,
			data: {
				action: 'sbr_dismiss_license_notice',
				sbr_nonce: sbrA.sbr_nonce

			},
			success: function(result){
				console.log('notice dismissed');
		  	}
		});
	});

	jQuery('body').on('click', '#sbr_review_consent_yes', function(e) {
		let reviewStep1 = jQuery('.sbr_review_notice_step_1, .sbr_review_step1_notice');
		let reviewStep2 = jQuery('.sbr_notice.sbr_review_notice, .rn_step_2');

		reviewStep1.hide();
		reviewStep2.show();

		$.ajax({
			url : sbrA.ajax_url,
			type : 'post',
			data : {
				action : 'sbr_review_notice_consent_update',
				consent : 'yes',
				sbr_nonce: sbrA.sbr_nonce
			},
			success : function(data) {
			}
		}); // ajax call

	});

	jQuery('body').on('click', '#sbr_review_consent_no', function(e) {
		let reviewStep1 = jQuery('.sbr_review_notice_step_1, #sbr-notifications');
		reviewStep1.hide();

		$.ajax({
			url : sbrA.ajax_url,
			type : 'post',
			data : {
				action : 'sbr_review_notice_consent_update',
				consent : 'no',
				sbr_nonce: sbrA.sbr_nonce
			},
			success : function(data) {
			}
		}); // ajax call

	});
});