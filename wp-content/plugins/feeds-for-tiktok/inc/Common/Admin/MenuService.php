<?php

namespace SmashBalloon\TikTokFeeds\Common\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\TikTokFeeds\Common\Utils;

class MenuService extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		add_action('admin_menu', [ $this, 'register_menus' ]);
		add_action('in_admin_header', [ $this, 'remove_admin_notices' ]);

		add_filter('plugin_action_links_' . SBTT_PLUGIN_BASENAME, [ $this, 'add_plugin_action_links' ]);
	}

	/**
	 * Registers the menus for the TikTok Feeds plugin.
	 */
	public function register_menus()
	{
		$svg = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.3626 4.28024C12.2749 4.23489 12.1895 4.18518 12.1067 4.13129C11.866 3.97217 11.6453 3.78468 11.4494 3.57285C10.9592 3.01199 10.7762 2.44299 10.7087 2.04462H10.7114C10.6551 1.71395 10.6784 1.5 10.6819 1.5H8.44929V10.1332C8.44929 10.2491 8.44929 10.3637 8.44441 10.4769C8.44441 10.4909 8.44306 10.5039 8.44225 10.5191C8.44225 10.5253 8.44225 10.5318 8.44089 10.5383C8.44089 10.54 8.44089 10.5416 8.44089 10.5432C8.41736 10.853 8.31806 11.1522 8.15174 11.4146C7.98542 11.677 7.75717 11.8944 7.48707 12.0479C7.20556 12.208 6.88717 12.292 6.5633 12.2916C5.52308 12.2916 4.68002 11.4434 4.68002 10.3959C4.68002 9.34836 5.52308 8.50015 6.5633 8.50015C6.76021 8.49997 6.9559 8.53095 7.14313 8.59196L7.14583 6.3187C6.57748 6.24528 6.00008 6.29045 5.45006 6.45136C4.90004 6.61226 4.38933 6.88541 3.95016 7.25357C3.56534 7.58792 3.24182 7.98687 2.99417 8.43245C2.89992 8.59494 2.54433 9.24788 2.50127 10.3076C2.47419 10.9091 2.65483 11.5322 2.74095 11.7898V11.7952C2.79511 11.9469 3.005 12.4644 3.34704 12.9007C3.62286 13.2507 3.94872 13.5581 4.31414 13.8131V13.8077L4.31956 13.8131C5.4004 14.5476 6.59878 14.4994 6.59878 14.4994C6.80623 14.491 7.50115 14.4994 8.29032 14.1253C9.16561 13.7107 9.66392 13.093 9.66392 13.093C9.98226 12.7239 10.2354 12.3032 10.4125 11.8491C10.6145 11.318 10.6819 10.6811 10.6819 10.4265V5.84639C10.709 5.86264 11.0697 6.10123 11.0697 6.10123C11.0697 6.10123 11.5894 6.43434 12.4003 6.65126C12.982 6.80563 13.7658 6.83813 13.7658 6.83813V4.62174C13.4911 4.65153 12.9335 4.56487 12.3626 4.28024Z" fill="white"/></svg>';
		$svg = base64_encode($svg);
		$menu_title = __('All Feeds', 'feeds-for-tiktok');

		$notice_bubble = '';
		$notifications_count = Utils::get_notifications_count();
		if ($notifications_count > 0) {
			$notice_bubble = ' <span class="sbtt-notice-alert"><span>' . $notifications_count . '</span></span>';
		}

		add_menu_page(
			$menu_title,
			__('TikTok Feeds', 'feeds-for-tiktok') . $notice_bubble,
			'manage_options',
			SBTT_MENU_SLUG,
			'',
			'data:image/svg+xml;base64,' . $svg
		);
	}

	/**
	 * Removes admin notices if the current page starts with 'sbtt'.
	 */
	public function remove_admin_notices()
	{
		if (!empty($_GET['page']) && strpos($_GET['page'], 'sbtt') === 0) {
			remove_all_actions('admin_notices');
			remove_all_actions('all_admin_notices');
		}
	}

	/**
	 * Adds plugin action links to the WordPress admin menu.
	 *
	 * @param array $links An array of existing plugin action links.
	 * @return array The modified array of plugin action links.
	 */
	public function add_plugin_action_links($links)
	{
		$show_settings = !Utils::sbtt_is_pro() ? true : Utils::is_license_valid();
		$settings_page = $show_settings ? admin_url('admin.php?page=sbtt-settings') : admin_url('admin.php?page=sbtt');

		$settings_link = '<a href="' . $settings_page . '">' . __('Settings', 'feeds-for-tiktok') . '</a>';
		array_unshift($links, $settings_link);

		return $links;
	}
}
