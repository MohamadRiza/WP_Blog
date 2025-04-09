// @api/dashboard.js
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * Fetches dashboard data.
 *
 * @return {Promise<Object>} The response containing dashboard data.
 */
export const fetchDashboardData = async () => {
	try {
		const response = await apiFetch( {
			path: '/suremails/v1/dashboard-data',
			method: 'GET',
			headers: {
				'X-WP-Nonce': suremails.nonce,
			},
		} );

		return response;
	} catch ( error ) {
		throw new Error(
			error.message ||
				__( 'There was an issue fetching dashboard data.', 'suremails' )
		);
	}
};

/**
 * Fetches email statistics based on a date range.
 *
 * @param {Object} dates      - The date range.
 * @param {string} dates.from - The start date in 'yyyy/MM/dd' format.
 * @param {string} dates.to   - The end date in 'yyyy/MM/dd' format.
 * @return {Promise<Object>} The response containing email statistics.
 */
export const fetchEmailStats = async ( dates ) => {
	if ( ! dates.from || ! dates.to ) {
		throw new Error(
			__( 'Both start and end dates are required.', 'suremails' )
		);
	}

	try {
		const response = await apiFetch( {
			path: '/suremails/v1/email-stats',
			method: 'POST',
			headers: {
				'X-WP-Nonce': suremails.nonce,
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( {
				date_from: dates.from,
				date_to: dates.to,
			} ),
		} );

		return response;
	} catch ( error ) {
		throw new Error(
			error.message ||
				__(
					'There was an issue fetching email statistics.',
					'suremails'
				)
		);
	}
};

/**
 * Fetches the list of installed plugins.
 *
 * @return {Promise<Object>} The response containing installed plugins.
 */
export const fetchInstalledPlugins = async () => {
	try {
		const data = await apiFetch( {
			path: '/suremails/v1/installed-plugins',
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': suremails.nonce,
			},
		} );

		if ( Array.isArray( data.plugins ) ) {
			return data.plugins;
		}
		throw new Error(
			__( 'Invalid data received from server.', 'suremails' )
		);
	} catch ( error ) {
		throw new Error(
			error.message ||
				__(
					'There was an issue fetching installed plugins.',
					'suremails'
				)
		);
	}
};

/**
 * Installs a specified plugin.
 *
 * @param {string} pluginSlug - The slug of the plugin to install.
 * @return {Promise<Object>} The response from the server.
 */
export const installPlugin = async ( pluginSlug ) => {
	if ( ! pluginSlug ) {
		throw new Error( __( 'Plugin slug is required.', 'suremails' ) );
	}

	try {
		const response = await apiFetch( {
			path: '/suremails/v1/install-plugin',
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': suremails.nonce,
			},
			body: JSON.stringify( {
				slug: pluginSlug,
			} ),
		} );

		return response;
	} catch ( error ) {
		throw new Error(
			error.message ||
				__( 'There was an issue installing the plugin.', 'suremails' )
		);
	}
};
