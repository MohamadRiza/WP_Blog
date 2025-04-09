<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin\Settings;

use Smashballoon\Customizer\Feed_Builder;
use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Admin\SBY_Notifications;
use SmashBalloon\YouTubeFeed\Container;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\Pro\SBY_CPT;
use SmashBalloon\YouTubeFeed\SBY_View;
use Smashballoon\Customizer\YouTube_License_Tier;

abstract class BaseSettingPage extends ServiceProvider {

	protected $has_menu = false;
	protected $has_assets = false;
	protected $page_title = "";
	protected $menu_title = "";
	protected $menu_slug = "";
	protected $template_file = "";
	protected $menu_position = 0;
	protected $has_page_restriction = false;
	protected $menu_position_free_version = 0;

	public function register() {
		if ( true === $this->has_menu ) {
			add_action('admin_menu', [$this, 'register_menu_page']);
		}

		if ( ( true === $this->has_assets ) && isset( $_GET['page'] ) && false !== strpos( $_GET['page'],
				SBY_SLUG . '-' . $this->menu_slug ) ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		}

	}

	public function render() {
		SBY_View::render($this->template_file);
	}

	public function enqueue_assets() {
		wp_enqueue_style(
			'sby-global-style',
			CUSTOMIZER_PLUGIN_URL . 'assets/css/global.css',
			false,
			SBYVER
		);
		//settings page script
		$script_file = SBY_PLUGIN_URL . 'frontend/build/static/js/main.js';

		if ( ! Util::isProduction() ) {
			$script_file = "http://localhost:3000/static/js/main.js";
		} else {
			wp_enqueue_style(
				'sby-settings-style',
				SBY_PLUGIN_URL . 'frontend/build/static/css/main.css',
				false,
				SBYVER
			);
		}

		wp_enqueue_script(
			'sby-settings',
			$script_file,
			array( 'wp-i18n' ),
			SBYVER,
			true
		);

		wp_localize_script(
			'sby-settings',
			'sby_settings',
			$this->get_settings_object()
		);

		wp_set_script_translations('sby-settings', 'feeds-for-youtube', SBY_PLUGIN_DIR . 'languages/');
	}

	public function register_menu_page() {
		$menu_slug = SBY_SLUG . '-' . $this->menu_slug;
		if ($this->has_page_restriction) {
			$menu_slug = 'admin.php?page=sby-feed-builder&tab=more';
		}

		add_submenu_page(
			SBY_MENU_SLUG,
			$this->page_title,
			$this->menu_title,
			'manage_options',
			$menu_slug,
			[$this, 'render'],
			$this->menu_position
		);
	}

	private function get_notifications() {
		return Container::get_instance()->get(SBY_Notifications::class)->output_return();
	}

	protected function get_settings_object() {
		return apply_filters( 'sby_localized_settings', [
			'admin_url'           => admin_url(),
			'ajax_handler'        => admin_url( 'admin-ajax.php' ),
			'nonce'               => wp_create_nonce( 'sby-admin' ),
			'setupPageUrl'      => admin_url( 'admin.php?page=youtube-feed-setup' ),
			'supportPageUrl'      => admin_url( 'admin.php?page=youtube-feed-support' ),
			'settingsPageUrl'     => admin_url( 'admin.php?page=youtube-feed-settings' ),
			'builderUrl'          => admin_url( 'admin.php?page=sby-feed-builder' ),
			'connectionURL'       => Feed_Builder::oauth_connet_url(),
			'manageLicense'       => 'https://smashballoon.com/account/downloads/?utm_campaign='. sby_utm_campaign() .'&utm_source=settings&utm_medium=manage-license',
			'single_video_settings'  => sby_is_pro() ? SBY_CPT::get_sby_cpt_settings() : [],
			'single_video_admin_url' => admin_url("admin.php?page=youtube-feed-settings#/single-videos"),
			'pluginItemName'      => SBY_PLUGIN_EDD_NAME,
			'licenseType'         => 'pro',
			'socialWallLinks'     => Feed_Builder::get_social_wall_links(),
			'socialWallActivated' => is_plugin_active( 'social-wall/social-wall.php' ),
			'genericText'         => Feed_Builder::get_generic_text(),
			'licenseTierFeatures' => (new YouTube_License_Tier)->tier_features(),
			'tooltipHelpSvg'      => '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.1665 8H10.8332V6.33333H9.1665V8ZM9.99984 17.1667C6.32484 17.1667 3.33317 14.175 3.33317 10.5C3.33317 6.825 6.32484 3.83333 9.99984 3.83333C13.6748 3.83333 16.6665 6.825 16.6665 10.5C16.6665 14.175 13.6748 17.1667 9.99984 17.1667ZM9.99984 2.16666C8.90549 2.16666 7.82186 2.38221 6.81081 2.801C5.79976 3.21979 4.8811 3.83362 4.10728 4.60744C2.54448 6.17024 1.6665 8.28986 1.6665 10.5C1.6665 12.7101 2.54448 14.8298 4.10728 16.3926C4.8811 17.1664 5.79976 17.7802 6.81081 18.199C7.82186 18.6178 8.90549 18.8333 9.99984 18.8333C12.21 18.8333 14.3296 17.9554 15.8924 16.3926C17.4552 14.8298 18.3332 12.7101 18.3332 10.5C18.3332 9.40565 18.1176 8.32202 17.6988 7.31097C17.28 6.29992 16.6662 5.38126 15.8924 4.60744C15.1186 3.83362 14.1999 3.21979 13.1889 2.801C12.1778 2.38221 11.0942 2.16666 9.99984 2.16666ZM9.1665 14.6667H10.8332V9.66666H9.1665V14.6667Z" fill="#434960"/></svg>',
			'svgIcons'            => Feed_Builder::builder_svg_icons(),
			'smashBalloonInfo'    => Feed_Builder::get_smashballoon_info(),
			'assetsURL'          => SBY_PLUGIN_URL . 'frontend/build',
			'notifications' => $this->get_notifications(),
			'shouldShowPostGracePeriodNotice'	=> Util::expiredLicenseWithGracePeriodEnded(),
			'isLicenseInactive'	=> empty( Util::get_license_key() ),
		] );
	}
}