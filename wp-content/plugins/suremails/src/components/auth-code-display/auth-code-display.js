import { useLayoutEffect } from '@wordpress/element';
import { useNavigate } from 'react-router-dom';
import { toast } from '@bsf/force-ui';
import { __ } from '@wordpress/i18n';

const AuthCodeDisplay = () => {
	const navigate = useNavigate();

	const redirectToGmailConnectionDrawer = () => {
		setTimeout( () => {
			navigate( '/connections', {
				state: {
					openDrawer: true,
					selectedProvider: 'GMAIL',
				},
			} );
		}, 300 );
	};

	const cleanUrlToDashboard = () => {
		window.history.replaceState(
			{},
			'',
			suremails.adminURL + '#/dashboard'
		);
	};

	useLayoutEffect( () => {
		const stored = localStorage.getItem( 'formStateValuesTimestamp' );

		if ( ! stored ) {
			return;
		}
		const storedFormStateTimeStamp = parseInt( stored, 10 );

		const currentTime = Date.now();
		if ( currentTime > storedFormStateTimeStamp ) {
			localStorage.removeItem( 'formStateValues' );
			localStorage.removeItem( 'formStateValuesTimestamp' );
			return;
		}

		const urlParams = new URLSearchParams( window.location.search );
		const state = urlParams.get( 'state' );

		if ( ! state || state !== 'gmail' ) {
			cleanUrlToDashboard();

			toast.error( __( 'Authorization Failed', 'suremails' ), {
				description: __(
					'Invalid state parameter. Please try again.',
					'suremails'
				),
				autoDismiss: false,
			} );

			redirectToGmailConnectionDrawer();
			return;
		}

		const code = urlParams.get( 'code' );

		if ( code ) {
			const storedFormState =
				JSON.parse( localStorage.getItem( 'formStateValues' ) ) || {};

			cleanUrlToDashboard();

			const updatedFormState = {
				...storedFormState,
				auth_code: code,
				type: 'GMAIL',
				refresh_token: '',
				force_save: true,
			};

			localStorage.setItem(
				'formStateValues',
				JSON.stringify( updatedFormState )
			);

			redirectToGmailConnectionDrawer();
			return;
		}

		toast.error( __( 'Authorization Failed', 'suremails' ), {
			description: __(
				'We could not receive the auth code. Please try again.',
				'suremails'
			),
			autoDismiss: false,
		} );

		const storedFormState =
			JSON.parse( localStorage.getItem( 'formStateValues' ) ) || {};
		const updatedFormState = {
			...storedFormState,
			type: 'GMAIL',
			refresh_token: '',
			force_save: true,
		};

		localStorage.setItem(
			'formStateValues',
			JSON.stringify( updatedFormState )
		);

		cleanUrlToDashboard();
		redirectToGmailConnectionDrawer();
	}, [ navigate ] );

	return null;
};

export default AuthCodeDisplay;
