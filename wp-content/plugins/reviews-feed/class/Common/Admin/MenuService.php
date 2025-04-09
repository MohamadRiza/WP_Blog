<?php

namespace SmashBalloon\Reviews\Common\Admin;

use Smashballoon\Stubs\Services\ServiceProvider;

class MenuService extends ServiceProvider
{

    public function register()
    {
        add_action('admin_menu', [$this, 'register_menus']);
	    add_action( 'in_admin_header', array( $this, 'remove_admin_notices' ) );
    }

    public function register_menus()
    {
        $notice = '';
        $is_notice = SBR_Admin_Notice::check_menu_notice_bubble();
        if ( $is_notice ) {
            $notice = ' <span class="sbr-notice-alert update-plugins"><span>!</span></span>';
        }

        $sbr_notifications = new SBR_Notifications();
	    $notifications = $sbr_notifications->get();

        $notice_bubble = '';
        if ( empty( $notice ) && ! empty( $notifications ) && is_array( $notifications ) ) {
            $notice_bubble = ' <span class="sbr-notice-alert"><span>'.count( $notifications ).'</span></span>';
        }
        $cap = current_user_can('manage_reviews_feed_options') ? 'manage_reviews_feed_options' : 'manage_options';
		$cap = apply_filters('sbr_settings_pages_capability', $cap);


        $menu_title = check_license_valid() ? __('All Feeds', 'reviews-feed') : __('Set up Plugin', 'reviews-feed');
        $page = add_menu_page(
            $menu_title,
            __('Reviews Feed', 'reviews-feed') . $notice . $notice_bubble,
            $cap,
            SBR_MENU_SLUG
        );

        add_action('load-' . $page, [$this, 'enqueue_assets']);
		add_action( 'admin_enqueue_scripts', [ $this, 'global_assets' ] );
    }

    public function enqueue_assets()
    {
        wp_register_script('sbr_settings', SBR_PLUGIN_URL . '/public/js/noop.js');
        wp_localize_script('sbr_settings', 'sbr_settings', [
            'supportedProviders' => apply_filters('sbr_supported_providers', [])
        ]);

        wp_enqueue_script('sbr_settings');
    }

	public function global_assets()
	{
		wp_enqueue_style( 'sbr-admin-global', SBR_PLUGIN_URL . '/public/admin-global.css', [], SBRVER );
	}

	public function remove_admin_notices() {
		$current_screen      = get_current_screen();
		$not_allowed_screens = array(
			'instagram-feed_page_sbi-feed-builder',
			'instagram-feed_page_sbi-settings',
			'instagram-feed_page_sbi-oembeds-manager',
			'instagram-feed_page_sbi-extensions-manager',
			'instagram-feed_page_sbi-about-us',
			'instagram-feed_page_sbi-support',
		);

		if ( ! empty( $_GET['page'] ) && strpos( $_GET['page'], 'sbr' ) === 0 ) {
			remove_all_actions('admin_notices');
			remove_all_actions('all_admin_notices');
		}
	}
}
