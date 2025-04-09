<?php

namespace SmashBalloon\TikTokFeeds\Common\Settings;

use Smashballoon\Customizer\V3\Settings_Builder;
use SmashBalloon\TikTokFeeds\Common\Container;
use SmashBalloon\TikTokFeeds\Common\Utils;
use SmashBalloon\TikTokFeeds\Common\Services\SettingsManagerService;
use SmashBalloon\TikTokFeeds\Common\AuthorizationStatusCheck;
use SmashBalloon\TikTokFeeds\Common\Services\PluginUpgraderService;
use SmashBalloon\TikTokFeeds\Common\Integrations\WPCode;

/**
 * Settings Builder class.
 */
class SettingsBuilder extends Settings_Builder
{
	/**
	 * Settings Menu Info
	 *
	 * @var array
	 */
	protected $menu;

	/**
	 *  Settings Tabs Path
	 *
	 * @var string
	 */
	protected $settingspage_tabs_path;

	/**
	 *  Settings Tabs Name Space
	 *
	 * @var string
	 */
	protected $settingspage_tabs_namespace;

	/**
	 *  Settings Tabs Order
	 *
	 * @var array
	 */
	protected $tabs_order;

	/**
	 *  Add to Menu
	 *
	 * @var bool
	 */
	protected $add_to_menu;

	/**
	 *  Plugin Status
	 *
	 * @var AuthorizationStatusCheck
	 */
	protected $plugin_status;

	/**
	 *  Global Settings
	 *
	 * @var SettingsManagerService
	 */
	protected $global_settings;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->menu                        = [
			'parent_menu_slug' => "sbtt",
			'page_title'       => "Settings",
			'menu_title'       => "Settings",
			'menu_slug'        => "sbtt-settings",
		];
		$this->settingspage_tabs_path      = SBTT_SETTINGSPAGE_TABS_PATH;
		$this->settingspage_tabs_namespace = SBTT_SETTINGSPAGE_TABS_NAMESPACE;
		$this->tabs_order                  = [ 'sb-general-tab', 'sb-feeds-tab', 'sb-advanced-tab', 'sb-code-snippets-tab' ];

		$this->add_to_menu     = !Utils::sbtt_is_pro() ? true : Utils::is_license_valid();
		$this->plugin_status   = new AuthorizationStatusCheck();
		$this->global_settings = Container::get_instance()->get(SettingsManagerService::class);
	}

	/**
	 * Retrieves the custom settings data for the plugin.
	 *
	 * @return array The custom settings data.
	 */
	public function customSettingsData()
	{
		$settings_data = [
			'nonce'          => wp_create_nonce('sbtt-admin'),
			'pluginSettings' => $this->global_settings->get_global_settings(),
			'currentTab'     => 'sb-general-tab',
			'assetsURL'      => SBTT_COMMON_ASSETS,
			'sourcesList'    => Utils::get_sources_list(),
			'feedsList'      => Utils::get_feeds_list(),
			'connectionURLs' => sbtt_get_tiktok_connection_urls(true),
			'pluginStatus'   => $this->plugin_status->get_statuses(),
			'isPro'          => Utils::sbtt_is_pro(),
			'aboutPageUrl'   => admin_url('admin.php?page=sbtt-about'),
			'isSocialWallActive' => Utils::is_sb_plugin_active('social-wall'),
			'socialWallLinks'    => Utils::get_social_wall_links(),
			'isDevUrl'       => PluginUpgraderService::is_dev_url(home_url()),
			'tieredFeatures' => Utils::get_tiered_features_list(),
			'upsellContent' => Utils::get_upsell_modal_content(),
			'wpCode' => array(
				'snippets' => WPCode::load_snippets(),
				'pluginInstalled' => WPCode::is_plugin_installed(),
				'pluginActive' => WPCode::is_plugin_active(),
				'isProInstalled' => WPCode::is_pro_installed(),
			),
			'adminNoticeContent' => apply_filters('sbtt_admin_notices_filter', 1),
		];

		$newly_retrieved_source_connection_data = Utils::maybe_source_connection_data();
		if ($newly_retrieved_source_connection_data) {
			$settings_data['newSourceData'] = $newly_retrieved_source_connection_data;
		}

		return $settings_data;
	}
}
