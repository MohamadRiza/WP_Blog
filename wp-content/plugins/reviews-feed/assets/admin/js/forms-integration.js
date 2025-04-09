/**
 * Misc Methods for form integrations
 *
 * @since 1.6
 */
'use strict';
var SBRFormIntegrationManager = window.SBRFormIntegrationManager || ( function( document, window, $ ) {
	const app = {

		/**
		 * Init App
		 *
		 * @since 1.6
		 */
		init: () => {
			$( window ).on( 'load', function() {
				if ( $.isFunction( $.ready.then ) ) {
					$.ready.then( app.load );
				} else {
					app.load();
				}
			} );
		},

		/**
		 * App Load
		 *
		 * @since 1.6
		 */
		load: () => {
			app.initWpFormsSearch()
			app.initFormidableFormsSearch()
		},
		/**
		 * Check Page URL to init Form Searchs
		 *
		 * @since 1.6
		 */
		checkPageUrlParams: (params = []) => {
			if (params?.length === 0) {
				return false;
			}
			const pageURL = new URL(window.location.href);

			const searchParams = params?.filter((param) => {
				return pageURL.searchParams.has(param?.name) &&
						pageURL.searchParams.get(param?.name) === param?.value;
			});
			return searchParams.length === params.length;
		},

		/**
		 * Init WP Forms Search
		 *
		 * @since 1.6
		 */
		initWpFormsSearch: () => {
			if (window?.WPFormsFormTemplates !== undefined) {
				const params = [
					{
						name : 'page',
						value : 'wpforms-builder'
					},
					{
						name : 'sbrfeeds',
						value : 'reviews'
					}
				];
				const searchParams = app.checkPageUrlParams(params);
				if (searchParams === true) {
					document.getElementById("wpforms-setup-template-search").value = "User Review";
					window.WPFormsFormTemplates.performSearch("User Review");
				}
			}
		},

		/**
		 * Init Formidable Search
		 *
		 * @since 1.6
		 */
		initFormidableFormsSearch: () => {
			if (window?.frmFormTemplatesVars !== undefined) {
				const params = [
					{
						name : 'page',
						value : 'formidable-form-templates'
					},
					{
						name : 'sbrfeeds',
						value : 'reviews'
					}
				];
				const searchParams = app.checkPageUrlParams(params);
				if (searchParams === true) {
					document.getElementById("template-search-input").value = "User Review";
					document.getElementById("template-search-input").dispatchEvent(new Event('change'))
				}
			}
		},



	}
	return app;
}( document, window, jQuery ) );

SBRFormIntegrationManager.init();
