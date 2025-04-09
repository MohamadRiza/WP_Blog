import React, { memo, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Container, toast } from '@bsf/force-ui';
import { useQuery } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import EmptyNotifications from './empty-notifications';
import CreateWorkflow from './create-workflow';
import NotificationsSkeleton from './notifications-skeleton'; // Import the Skeleton component

const Notifications = () => {
	// Fetch installed and active plugins using React Query
	const {
		data: pluginsData,
		isLoading,
		error,
	} = useQuery( {
		queryKey: [ 'installed-plugins' ],
		queryFn: async () => {
			const response = await apiFetch( {
				path: '/suremails/v1/installed-plugins',
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.suremails?.nonce, // Ensure nonce is correctly passed
				},
			} );

			if (
				response?.success &&
				response?.plugins?.installed &&
				response?.plugins?.active
			) {
				return {
					installed: response.plugins.installed,
					active: response.plugins.active,
				};
			}

			throw new Error(
				__( 'Invalid data received from server.', 'suremails' )
			);
		},
		refetchInterval: 100000, // Refetch every ~28 hours
		refetchOnMount: false,
		refetchOnWindowFocus: false,
		refetchOnReconnect: true,
	} );

	// Determine if SureTriggers is installed and active
	const isSureTriggersInstalled =
		pluginsData?.installed.includes( 'suretriggers' );
	const isSureTriggersActive = pluginsData?.active.includes( 'suretriggers' );

	// Handle error state with toast notifications
	useEffect( () => {
		if ( error ) {
			toast.error( __( 'Error loading notifications.', 'suremails' ), {
				description:
					error.message ||
					__(
						'There was an issue fetching notifications.',
						'suremails'
					),
			} );
		}
	}, [ error ] );

	// Determine the component to render
	const renderContent = () => {
		if ( isLoading ) {
			return <NotificationsSkeleton />; // Show Skeleton while loading
		}

		if ( isSureTriggersInstalled && isSureTriggersActive ) {
			return <CreateWorkflow />;
		}

		return (
			<EmptyNotifications
				isSureTriggersInstalled={ isSureTriggersInstalled }
				isSureTriggersActive={ isSureTriggersActive }
			/>
		);
	};

	return (
		<div className="flex items-start justify-center h-full px-8 py-8 overflow-hidden bg-background-secondary">
			<div className="w-full h-auto px-4 py-4 space-y-2 border-0.5 border-solid shadow-sm opacity-100 rounded-xl border-border-subtle bg-background-primary">
				{ /* Header */ }
				{ isSureTriggersInstalled && isSureTriggersActive && (
					<div className="flex items-center justify-between w-full gap-2 px-2 py-2.25 opacity-100">
						<h4 className="m-0 text-xl font-semibold text-text-primary leading-7.5">
							{ __( 'Notifications', 'suremails' ) }
						</h4>
					</div>
				) }
				{ /* Content Area */ }
				<Container>{ renderContent() }</Container>
			</div>
		</div>
	);
};

export default memo( Notifications );
