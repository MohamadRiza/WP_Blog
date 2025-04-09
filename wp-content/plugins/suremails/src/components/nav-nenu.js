// src/components/NavMenu.js
import { useState, useEffect, renderToString } from '@wordpress/element';
import { useLocation, Link, useNavigate } from 'react-router-dom';
import { Topbar, HamburgerMenu, Badge, Button } from '@bsf/force-ui';
import { CircleHelp, Megaphone } from 'lucide-react';
import { SureMailIcon } from 'assets/icons';
import { cn } from '@utils/utils';
import useWhatsNewRSS from '../../lib/useWhatsNewRSS';
import { __ } from '@wordpress/i18n';
const NavMenu = () => {
	const location = useLocation();
	const navigate = useNavigate();
	const version = window.suremails?.version || '1.0.0';
	// Define navigation items
	const navItems = [
		{ name: 'Dashboard', path: '/dashboard' },
		{ name: 'Settings', path: '/settings' },
		{ name: 'Connections', path: '/connections' },
		{ name: 'Email Logs', path: '/logs' },
		{ name: 'Notifications', path: '/notifications', comingSoon: true },
	];

	// Get the current active path
	const [ activePath, setActivePath ] = useState( location.pathname );

	// Update activePath when the location changes
	useEffect( () => {
		setActivePath( location.pathname );
	}, [ location.pathname ] );

	const handleIconClick = () => {
		navigate( '/dashboard' );
	};

	useWhatsNewRSS( {
		uniqueKey: 'suremails',
		rssFeedURL: 'https://suremails.com/whats-new/feed/', // TODO: domain name change to surerank.
		selector: '#suremails_whats_new',
		flyout: {
			title: __( "What's New?", 'suremails' ),
			className: 'suremails-whats-new-flyout',
		},
		triggerButton: {
			icon: renderToString(
				<Megaphone
					className="size-4 m-1 text-icon-primary"
					strokeWidth={ 1.5 }
				/>
			),
		},
	} );

	return (
		<>
			<Topbar className="relative shadow-sm bg-background-primary h-16 z-[1] p-0 gap-0">
				{ /* Left Section: Logo */ }
				<Topbar.Left className="p-5 gap-5">
					<HamburgerMenu className="lg:hidden">
						<HamburgerMenu.Toggle className="size-6" />
						<HamburgerMenu.Options>
							{ navItems.map( ( option ) =>
								option.comingSoon ? (
									<HamburgerMenu.Option
										key={ option.name }
										className=" cursor-not-allowed opacity-50"
									>
										{ option.name }
										<Badge
											label="Coming Soon"
											size="xs"
											variant="blue"
											className="pl-1 pr-1 ml-1"
										/>
									</HamburgerMenu.Option>
								) : (
									<HamburgerMenu.Option
										key={ option.name }
										tag={ Link }
										to={ option.path }
										active={
											activePath.trim() === option.path
										}
									>
										{ option.name }
									</HamburgerMenu.Option>
								)
							) }
						</HamburgerMenu.Options>
					</HamburgerMenu>
					<Topbar.Item>
						<div
							onClick={ handleIconClick }
							className="flex items-center justify-center cursor-pointer"
						>
							<SureMailIcon className="h-6 w-6" />
						</div>
					</Topbar.Item>
				</Topbar.Left>

				{ /* Middle Section: Navigation */ }
				<Topbar.Middle
					className="h-full lg:flex hidden"
					align="left"
					gap="xs"
				>
					<Topbar.Item className="h-full">
						<nav className="h-full space-x-4">
							{ navItems.map( ( item ) =>
								item.comingSoon ? (
									<span
										key={ item.name }
										className={ cn(
											'inline-flex items-center relative h-full content-center px-1 text-sm text-text-disabled font-medium bg-transparent cursor-not-allowed opacity-50',
											activePath.trim() === item.path
												? 'text-text-primary'
												: ''
										) }
									>
										{ item.name }
										<Badge
											label="Coming Soon"
											size="xs"
											variant="blue"
											className="pl-1 pr-1 ml-1"
										/>
									</span>
								) : (
									<Link
										key={ item.name }
										to={ item.path }
										className={ cn(
											'inline-block relative h-full content-center px-1 text-sm text-text-secondary font-medium no-underline bg-transparent focus:outline-none shadow-none border-1 hover:text-text-primary transition-colors duration-300',
											activePath.trim() === item.path
												? 'text-text-primary border-none after:content-[""] after:absolute after:bottom-0 after:inset-x-0 after:h-px after:bg-border-interactive after:transition-all after:duration-300'
												: ''
										) }
									>
										{ item.name }
									</Link>
								)
							) }
						</nav>
					</Topbar.Item>
				</Topbar.Middle>

				{ /* Right Section: Version Badge and Icons */ }
				<Topbar.Right className="p-5">
					<Topbar.Item>
						<Badge
							label={ `V ${ version }` }
							size="xs"
							variant="neutral"
						/>
					</Topbar.Item>
					<Topbar.Item className="gap-2">
						<Button
							variant="ghost"
							size="xs"
							icon={ <CircleHelp /> }
							onClick={ () =>
								window.open( suremails?.docsURL, '_blank' )
							}
							href=""
						/>
						{ /** What's New Integration */ }
						<div
							id="suremails_whats_new"
							className="[&>a]:p-0.5 [&>a]:pl-0"
						></div>
					</Topbar.Item>
				</Topbar.Right>
			</Topbar>
		</>
	);
};

export default NavMenu;
