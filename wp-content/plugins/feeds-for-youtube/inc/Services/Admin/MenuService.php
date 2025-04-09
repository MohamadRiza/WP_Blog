<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin;

use Smashballoon\Customizer\Container;
use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Admin\SBY_Notifications;

class MenuService extends ServiceProvider {

	/**
	 * @var mixed|Feed_Builder
	 */
	private $builder;

	/**
	 * @var SBY_Notifications
	 */
  private $notifications;

	public function __construct(SBY_Notifications $notifications) {
		$this->builder = Container::getInstance()->get(Feed_Builder::class);
    	$this->notifications = $notifications;
	}

	public function register() {
		add_action('admin_menu', [$this, 'register_menus']);
	}

	public function register_menus() {
		$cap = current_user_can( 'manage_options' ) ? 'manage_options' : 'manage_youtube_feed_options';

		add_menu_page(
			__('YouTube Feed', 'feeds-for-youtube'),
			__('YouTube Feed', 'feeds-for-youtube') . $this->alert_html(),
			$cap,
			SBY_MENU_SLUG,
			null,
			'dashicons-video-alt3',
			99
		);

		add_submenu_page(
			SBY_MENU_SLUG,
			__( 'All Feeds', 'feeds-for-youtube' ),
			__( 'All Feeds', 'feeds-for-youtube' ),
			$cap,
			SBY_MENU_SLUG,
			array( $this->builder, 'feed_builder' ),
			0
		);

		if ( (sby_is_pro() && empty( Util::get_license_key() )) || Util::is_license_expired() || !sby_is_pro() ) {
			add_submenu_page(
				SBY_MENU_SLUG,
				__( 'Upgrade to Pro', 'feeds-for-youtube' ),
				__( '<span class="sby_get_pro">Try the Pro Demo</span>', 'feeds-for-youtube' ),
				$cap,
				'https://smashballoon.com/youtube-feed/demo/?utm_campaign=youtube-free&utm_source=menu-link&utm_medium=upgrade-link',
				''
			);
		}

		if ( sby_should_add_free_plugin_submenu( 'facebook' ) ) {
			add_submenu_page(
                SBY_MENU_SLUG,
                __( 'Facebook Feed', 'feeds-for-youtube' ),
                '<span class="sby_get_cff">' . __( 'Facebook Feed', 'feeds-for-youtube' ) . '</span>',
                'manage_options',
                'admin.php?page=sby-feed-builder&tab=more',
                5
            );
		}

		if ( sby_should_add_free_plugin_submenu( 'instagram' ) ) {
			add_submenu_page(
                SBY_MENU_SLUG,
                __( 'Instagram Feed', 'feeds-for-youtube' ),
                '<span class="sby_get_sbi">' . __( 'Instagram Feed', 'feeds-for-youtube' ) . '</span>',
                'manage_options',
                'admin.php?page=sby-feed-builder&tab=more',
                6
            );
		}

		if ( sby_should_add_free_plugin_submenu( 'twitter' ) ) {
			add_submenu_page(
                SBY_MENU_SLUG,
                __( 'Twitter Feed', 'feeds-for-youtube' ),
                '<span class="sby_get_ctf">' . __( 'Twitter Feed', 'feeds-for-youtube' ) . '</span>',
                'manage_options',
                'admin.php?page=sby-feed-builder&tab=more',
                7
            );
		}

		if ( sby_should_add_free_plugin_submenu( 'tiktok' ) ) {
			add_submenu_page(
                SBY_MENU_SLUG,
                __( 'Tiktok Feed', 'feeds-for-youtube' ),
                '<span class="sby_get_sbtt">' . __( 'Tiktok Feed', 'feeds-for-youtube' ) . '</span>',
                'manage_options',
                'admin.php?page=sby-feed-builder&tab=more',
                8
            );
		}

		if ( sby_should_add_free_plugin_submenu( 'reviews' ) ) {
			add_submenu_page(
                SBY_MENU_SLUG,
                __( 'Reviews Feed', 'feeds-for-youtube' ),
                '<span class="sby_get_sbr">' . __( 'Reviews Feed', 'feeds-for-youtube' ) . '</span>',
                'manage_options',
                'admin.php?page=sby-feed-builder&tab=more',
                9
            );
		}
	}

	private function alert_html() {
		$notice = '';
		$notice_bubble = '';

		$notifications = $this->notifications->get();

		if ( empty( $notice ) && ! empty( $notifications ) && is_array( $notifications ) ) {
			$notice_bubble = ' <span class="sby-notice-alert"><span>' . count( $notifications ) . '</span></span>';
		}

		if ( sby_is_pro() && empty( Util::get_license_key() ) || Util::is_license_expired() ) {
			$notice_bubble = ' <span class="sby-notice-alert"><span>1</span></span>';
		}

		return $notice_bubble . $notice;
	}
}
