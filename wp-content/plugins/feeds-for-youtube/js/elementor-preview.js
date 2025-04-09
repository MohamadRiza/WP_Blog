'use strict';

var CustomYouTubeFeedElementor = window.CustomYouTubeFeedElementor || ( function( document, window, $ ) {
	var vars = {};
	var app = {
		init: function() {
			app.events();
		},

		events: function() {
			$( window ).on('elementor/frontend/init', function ( $scope ) {
				elementorFrontend.hooks.addAction('frontend/element_ready/sby-widget.default', app.frontendWidgetInit);
				if( 'undefined' !== typeof elementor ){
					elementor.hooks.addAction( 'panel/open_editor/widget/sby-widget', app.widgetPanelOpen );
				}
			});
		},

		SbyInitWidget: function() {
			window.sby_init();
		},

		registerWidgetEvents: function( $scope ) {
			$scope
				.on( 'change', '.sby-feed-block-cta-feedselector', app.selectFeedInPreview );
			$scope
				.on( 'click', '.sby-feed-block-cta-btn,.sby-feed-block-link', app.anchorTrigger );

		},

		frontendWidgetInit : function( $scope ){
			app.SbyInitWidget();
			app.registerWidgetEvents( $scope );
		},

		findFeedSelector: function( event ) {
			vars.$select = event && event.$el ?
				event.$el.closest( '#elementor-controls' ).find( 'select[data-setting="feed_id"]' ) :
				window.parent.jQuery( '#elementor-controls select[data-setting="feed_id"]' );
		},


		selectFeedInPreview : function( event ){
			vars.feedId = $( this ).val();
			app.findFeedSelector();
			vars.$select.val( vars.feedId ).trigger( 'change' );
		},

		anchorTrigger : function( event ){
			vars.href = $( this ).attr('href');
			window.open(vars.href ,'_blank');
		},

		widgetPanelOpen: function( panel, model ) {
			panel.$el.find( '.elementor-control.elementor-control-feed_id' ).find( 'select' ).on( 'change', function(){
				setTimeout(function(){
					app.SbyInitWidget();
				}, 4000)
			});
		},
	};

	return app;
}( document, window, jQuery ) );

CustomYouTubeFeedElementor.init();