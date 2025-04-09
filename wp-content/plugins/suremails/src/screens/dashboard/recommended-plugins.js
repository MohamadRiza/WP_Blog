// File: src/components/RecommendedPlugins.js
import { useState, memo } from '@wordpress/element';
import { Container, Label, Button, toast, Loader } from '@bsf/force-ui';
import {
	SureFormsLogo,
	SpectraLogo,
	SureCartLogo,
	SureTriggersLogo,
} from 'assets/icons';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import Title from '@components/title/title';

const recommendedPluginsData = [
	{
		id: '1',
		badgeText: __( 'Free', 'suremails' ),
		svg: <SureFormsLogo />,
		title: __( 'SureForms', 'suremails' ),
		description: __( 'Best no code WordPress form builder.', 'suremails' ),
		slug: 'sureforms',
		name: __( 'SureForms', 'suremails' ),
	},
	{
		id: '2',
		badgeText: __( 'Free', 'suremails' ),
		svg: <SpectraLogo />,
		title: __( 'Spectra', 'suremails' ),
		description: __( 'Free WordPress Page Builder.', 'suremails' ),
		slug: 'ultimate-addons-for-gutenberg',
		name: __( 'Spectra', 'suremails' ),
	},
	{
		id: '3',
		badgeText: __( 'Free', 'suremails' ),
		svg: <SureCartLogo />,
		title: __( 'SureCart', 'suremails' ),
		description: __( 'The new way to sell on WordPress.', 'suremails' ),
		slug: 'surecart',
		name: __( 'SureCart', 'suremails' ),
	},
	{
		id: '4',
		badgeText: __( 'Free', 'suremails' ),
		svg: <SureTriggersLogo />,
		title: __( 'SureTriggers', 'suremails' ),
		description: __( 'Automate your WordPress setup.', 'suremails' ),
		slug: 'suretriggers',
		name: __( 'SureTriggers', 'suremails' ),
	},
];

/**
 * Utility function to handle plugin operations (install/activate)
 *
 * @param {Object}   plugin          - The plugin object
 * @param {string}   operation       - The operation to perform ('install' or 'activate')
 * @param {Function} setLoadingState - Function to set loading state
 * @return {Promise} - Resolves when operation is complete
 */
const handlePluginOperation = ( plugin, operation, setLoadingState ) => {
	return new Promise( async ( resolve, reject ) => {
		const isInstall = operation === 'install';

		if ( ! wp.updates ) {
			reject(
				new Error(
					__( 'WordPress updates API not available.', 'suremails' )
				)
			);
			return;
		}

		setLoadingState( ( prev ) => [ ...prev, plugin.slug ] );

		const operationFunction = isInstall
			? wp.updates.installPlugin
			: wp.ajax.send;

		const data = isInstall
			? { slug: plugin.slug }
			: {
					slug: `${ plugin.slug }/${ plugin.slug }.php`,
					_ajax_nonce: window.suremails?._ajax_nonce,
			  };
		const commonOptions = {
			success: () => {
				resolve();
			},
			error: ( error ) => {
				reject(
					new Error(
						error.errorMessage ||
							__( 'Operation failed.', 'suremails' )
					)
				);
			},
		};

		if ( isInstall ) {
			operationFunction( {
				...data,
				...commonOptions,
			} );
		} else {
			operationFunction( 'suremails-activate_plugin', {
				data,
				...commonOptions,
			} );
		}
	} );
};

// RecommendedPlugins Component
const RecommendedPlugins = () => {
	const [ installingPlugins, setInstallingPlugins ] = useState( [] );
	const [ activatingPlugins, setActivatingPlugins ] = useState( [] );
	const queryClient = useQueryClient();

	// Query for installed plugins
	const { data: pluginsData } = useQuery( {
		queryKey: [ 'installed-plugins' ],
		queryFn: async () => {
			const response = await apiFetch( {
				path: '/suremails/v1/installed-plugins',
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.suremails?.nonce,
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
		refetchInterval: 100000,
		refetchOnMount: false,
		refetchOnWindowFocus: false,
		refetchOnReconnect: true,
	} );

	/**
	 * Handle installation of a plugin
	 *
	 * @param {Object} plugin - The plugin object
	 */
	const handleInstallPlugin = async ( plugin ) => {
		if ( pluginsData?.installed.includes( plugin.slug ) ) {
			toast.info( __( 'Already Installed', 'suremails' ), {
				description: __( 'Plugin is already installed.', 'suremails' ),
			} );
			return;
		}

		if ( installingPlugins.length > 0 || activatingPlugins.length > 0 ) {
			toast.info( __( 'Installation in Progress', 'suremails' ), {
				description: __(
					'Another operation is in progress. Please wait.',
					'suremails'
				),
			} );
			return;
		}

		try {
			await handlePluginOperation(
				plugin,
				'install',
				setInstallingPlugins
			);

			toast.success( __( 'Installation Complete', 'suremails' ), {
				description: __(
					'Plugin installed successfully.',
					'suremails'
				),
			} );

			// Refresh the plugins data
			queryClient.invalidateQueries( [ 'installed-plugins' ] );

			// Automatically activate after installation
			await handleActivatePlugin( plugin );
		} catch ( error ) {
			toast.error( __( 'Installation Failed', 'suremails' ), {
				description:
					__( 'Failed to install plugin: ', 'suremails' ) +
					( error.message || '' ),
			} );
		} finally {
			setInstallingPlugins( ( prev ) =>
				prev.filter( ( slug ) => slug !== plugin.slug )
			);
		}
	};

	/**
	 * Handle activation of a plugin
	 *
	 * @param {Object} plugin - The plugin object
	 */
	const handleActivatePlugin = async ( plugin ) => {
		if ( pluginsData?.active.includes( plugin.slug ) ) {
			toast.info( __( 'Already Activated', 'suremails' ), {
				description: __( 'Plugin is already activated.', 'suremails' ),
			} );
			return;
		}

		if ( installingPlugins.length > 0 || activatingPlugins.length > 0 ) {
			toast.info( __( 'Operation in Progress', 'suremails' ), {
				description: __(
					'Another operation is in progress. Please wait.',
					'suremails'
				),
			} );
			return;
		}

		try {
			await handlePluginOperation(
				plugin,
				'activate',
				setActivatingPlugins
			);

			toast.success( __( 'Activation Complete', 'suremails' ), {
				description: __(
					'Plugin activated successfully.',
					'suremails'
				),
			} );

			// Refresh the plugins data
			queryClient.invalidateQueries( [ 'installed-plugins' ] );
		} catch ( error ) {
			toast.error( __( 'Activation Failed', 'suremails' ), {
				description:
					__( 'Failed to activate plugin: ', 'suremails' ) +
					( error.message || '' ),
			} );
		} finally {
			setActivatingPlugins( ( prev ) =>
				prev.filter( ( slug ) => slug !== plugin.slug )
			);
		}
	};

	/**
	 * Determine the action based on plugin state and render the appropriate UI element
	 *
	 * @param {Object} plugin - The plugin object
	 * @return {JSX.Element} - The Install button, Activate button, or Active button
	 */
	const renderActionButton = ( plugin ) => {
		const isInstalling = installingPlugins.includes( plugin.slug );
		const isActivating = activatingPlugins.includes( plugin.slug );
		const isInstalled = pluginsData?.installed.includes( plugin.slug );
		const isActive = pluginsData?.active.includes( plugin.slug );

		if ( ! isInstalled ) {
			return (
				<Button
					variant="outline"
					className="no-underline border-border-subtle text-text-primary hover:no-underline [&_svg]:size-4"
					size="xs"
					onClick={ () => handleInstallPlugin( plugin ) }
					icon={
						( isInstalling || isActivating ) && (
							<Loader variant="primary" />
						)
					}
					iconPosition="left"
					disabled={ isInstalling || isActivating }
				>
					{ __( 'Install & Activate', 'suremails' ) }
				</Button>
			);
		}

		if ( isInstalled && ! isActive ) {
			return (
				<Button
					variant="outline"
					className="no-underline bg-button-tertiary text-text-primary hover:no-underline border-border-subtle [&_svg]:size-4"
					size="xs"
					onClick={ () => handleActivatePlugin( plugin ) }
					disabled={ isInstalling || isActivating }
					icon={
						( isInstalling || isActivating ) && (
							<Loader variant="primary" />
						)
					}
					iconPosition="left"
				>
					{ __( 'Activate', 'suremails' ) }
				</Button>
			);
		}

		if ( isActive ) {
			return (
				<Button
					variant="outline"
					className="shadow-sm bg-badge-background-green text-text-primary border-border-subtle hover:no-underline"
					size="xs"
				>
					{ __( 'Activated', 'suremails' ) }
				</Button>
			);
		}

		return null;
	};

	return (
		<>
			<Container
				containerType="flex"
				gap="xs"
				direction="column"
				className="p-3 border-solid rounded-xl border-border-subtle border-0.5 gap-1"
			>
				<Container.Item className="md:w-full lg:w-full">
					<Container
						className="p-1"
						justify="between"
						gap="xs"
						align="center"
					>
						<Container.Item>
							<Title
								title={ __(
									'Extend Your Website',
									'suremails'
								) }
								tag="h4"
							/>
						</Container.Item>
					</Container>
				</Container.Item>
				<Container.Item className="rounded-lg md:w-full lg:w-full bg-field-primary-background">
					<Container
						containerType="grid"
						className="gap-1 p-1 grid-cols-2 md:grid-cols-4 min-[1020px]:grid-cols-1 xl:grid-cols-2"
					>
						{ recommendedPluginsData.map( ( card ) => (
							<Container.Item key={ card.id } className="flex">
								<Container
									containerType="flex"
									direction="column"
									className="w-[190px] min-w-[144px] min-h-[135px] flex-1 gap-1 p-2 rounded-md shadow-soft-shadow-inner bg-background-primary"
								>
									<Container.Item>
										<Container className="items-center gap-1.5 p-1">
											<Container.Item
												className="[&>svg]:size-5 flex"
												grow={ 0 }
												shrink={ 0 }
											>
												{ card.svg }
											</Container.Item>
											<Container.Item className="flex">
												<Label className="text-sm font-medium">
													{ card.title }
												</Label>
											</Container.Item>
										</Container>
									</Container.Item>
									<Container.Item className="gap-0.5 p-1">
										<Label
											variant="help"
											className="text-sm font-normal text-text-tertiary"
										>
											{ card.description }
										</Label>
									</Container.Item>

									<Container.Item className="gap-0.5 px-1 pt-2 pb-1 mt-auto">
										{ renderActionButton( card ) }
									</Container.Item>
								</Container>
							</Container.Item>
						) ) }
					</Container>
				</Container.Item>
			</Container>
		</>
	);
};

// Export the component as default
export default memo( RecommendedPlugins );
