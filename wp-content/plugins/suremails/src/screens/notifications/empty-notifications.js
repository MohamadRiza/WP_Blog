import { __ } from '@wordpress/i18n';
import { Plus } from 'lucide-react';
import { toast, Loader } from '@bsf/force-ui';
import { NoNotifications } from 'assets/icons';
import EmptyState from '@components/empty-state/empty-state';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import { useState } from 'react';

const EmptyNotifications = ( {
	isSureTriggersInstalled,
	isSureTriggersActive,
} ) => {
	const queryClient = useQueryClient();
	const [ isLoading, setIsLoading ] = useState( false );

	// Mutation for installing the SureTriggers plugin
	const installMutation = useMutation( {
		mutationFn: ( plugin ) =>
			apiFetch( {
				path: '/suremails/v1/install-plugin',
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.suremails?.nonce,
				},
				data: {
					slug: plugin.slug,
				},
			} ),
		onSuccess: ( response ) => {
			if ( response.success ) {
				toast.success( __( 'Installation Complete', 'suremails' ), {
					description: __(
						'Plugin installed successfully.',
						'suremails'
					),
				} );

				// Update the cache with the new installed plugins
				if ( response.plugins ) {
					queryClient.setQueryData( [ 'installed-plugins' ], {
						installed: response.plugins.installed,
						active: response.plugins.active,
					} );
				}
			} else {
				throw new Error(
					response.message ||
						__( 'Failed to install plugin.', 'suremails' )
				);
			}
		},
		onError: ( error ) => {
			toast.error( __( 'Installation Failed', 'suremails' ), {
				description:
					__( 'Failed to install plugin: ', 'suremails' ) +
					( error.message || '' ),
			} );
		},
	} );

	// Mutation for activating the SureTriggers plugin
	const activateMutation = useMutation( {
		mutationFn: ( plugin ) =>
			apiFetch( {
				path: '/suremails/v1/activate-plugin',
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.suremails?.nonce,
				},
				data: {
					slug: plugin.slug,
				},
			} ),
		onSuccess: ( response ) => {
			if ( response.success ) {
				toast.success( __( 'Activation Complete', 'suremails' ), {
					description: __(
						'Plugin activated successfully.',
						'suremails'
					),
				} );

				// Update the cache with the new active plugins
				if ( response.plugins ) {
					queryClient.setQueryData( [ 'installed-plugins' ], {
						installed: response.plugins.installed,
						active: response.plugins.active,
					} );
				}
			} else {
				throw new Error(
					response.message ||
						__( 'Failed to activate plugin.', 'suremails' )
				);
			}
		},
		onError: ( error ) => {
			toast.error( __( 'Activation Failed', 'suremails' ), {
				description:
					__( 'Failed to activate plugin: ', 'suremails' ) +
					( error.message || '' ),
			} );
		},
	} );

	const handleAction = async () => {
		setIsLoading( true ); // Start loading

		try {
			if ( ! isSureTriggersInstalled ) {
				// Install the plugin
				await installMutation.mutateAsync( { slug: 'suretriggers' } );

				// After successful installation, activate the plugin
				await activateMutation.mutateAsync( { slug: 'suretriggers' } );
			} else if ( isSureTriggersInstalled && ! isSureTriggersActive ) {
				// Activate the plugin
				await activateMutation.mutateAsync( { slug: 'suretriggers' } );
			}
		} catch ( error ) {
			// Errors are handled in the mutation's onError callbacks
		} finally {
			setIsLoading( false ); // End loading
		}
	};

	const getActionIcon = () => {
		if ( isLoading ) {
			return (
				<Loader
					variant="primary"
					size="sm"
					className="mr-2"
					aria-hidden="true"
					aria-label={ __( 'Loading', 'suremails' ) }
				/>
			);
		}

		if ( ! isSureTriggersInstalled || isSureTriggersActive ) {
			return <Plus />;
		}

		return null;
	};

	// Determine button text based on plugin status
	let buttonText = __( 'Install and Activate', 'suremails' );
	if ( isSureTriggersInstalled && ! isSureTriggersActive ) {
		buttonText = __( 'Activate SureTriggers', 'suremails' );
	} else if ( isSureTriggersInstalled && isSureTriggersActive ) {
		buttonText = __( 'Active', 'suremails' );
	}

	return (
		<EmptyState
			image={ NoNotifications }
			title={ __( 'Setup Notification via SureTriggers', 'suremails' ) }
			description={ __(
				'SureTriggers integrates with SureMail, enabling real-time alerts and seamless app connections.',
				'suremails'
			) }
			bulletPoints={ [
				__(
					'Instantly receive notifications when an email fails.',
					'suremails'
				),
				__(
					'Connect with your favorite tools like Slack, Telegram, etc.',
					'suremails'
				),
				__(
					'Automatically resend failed emails or alert your team.',
					'suremails'
				),
			] }
			action={ {
				variant: 'primary',
				size: 'md',
				icon: getActionIcon(),
				iconPosition: 'left',
				onClick: handleAction,
				className: 'font-medium',
				children: buttonText,
				disabled: isLoading,
			} }
		/>
	);
};

export default EmptyNotifications;
