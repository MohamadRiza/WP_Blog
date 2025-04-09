<?php
/**
 * Reviews Feed Saver
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Settings;

use Smashballoon\Customizer\V2\Settings_Builder;
use SmashBalloon\Reviews\Common\AuthorizationStatusCheck;
use SmashBalloon\Reviews\Common\Builder\SBR_Sources;
use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\Services\SBR_Upgrader;
use SmashBalloon\Reviews\Common\Util;
use SmashBalloon\Reviews\Common\Utils\EmailVerification;

class SBR_Settings_Builder extends Settings_Builder {

    /**
     *  Settings Menu Info
     * @since 1.0
     */
    protected $menu;

    /**
     *  Settings Tabs Path
     * @since 1.0
     */
    protected $settingspage_tabs_path;

    /**
     *  Settings Tabs Name Space
     * @since 1.0
     */
    protected $settingspage_tabs_namespace;
    protected $tabs_order;
    protected $add_to_menu;

    protected $db;
    protected $plugin_status;


    public function __construct(){
        $this->menu = [
            'parent_menu_slug'  => "sbr",
            'page_title'        => "Settings",
            'menu_title'        => "Settings",
            'menu_slug'        => "sbr-settings",
        ];
        $this->settingspage_tabs_path       = SBR_SETTINGSPAGE_TABS_PATH;
        $this->settingspage_tabs_namespace = SBR_SETTINGSPAGE_TABS_NAMESPACE;
        $this->tabs_order                   = [ 'sb-general-tab', 'sb-feeds-tab', 'sb-advanced-tab', 'sb-translation-tab' ];

        $this->db = new DB();
        $this->add_to_menu = !Util::sbr_is_pro() ? true : check_license_valid();
        $this->plugin_status = new AuthorizationStatusCheck();
    }

    public function custom_settings_data(){
        EmailVerification::catch_email_verification();

        $settings_data = [
            'nonce' => wp_create_nonce('sbr-admin'),
            'apiKeys' => get_option('sbr_apikeys', []),
            'apiKeyLimits' => get_option('sbr_apikeys_limit', []),
            'pluginSettings' => sbr_recursive_parse_args(get_option('sbr_settings', []), sbr_plugin_settings_defaults() ),
            'currentTab' => 'sb-general-tab',
            'sourcesList' => SBR_Sources::get_sources_list(),
            'sourcesCount' => SBR_Sources::get_sources_count(),
            'providers' => Util::get_providers(),
            'feedsList' => DB::get_feeds_list(),
            'connectFBUrls' => sbr_get_fb_connection_urls( true ),
            'pluginNotices' => Util::get_plugin_notices(),
            'pluginStatus' => $this->plugin_status->get_statuses(),
            'isPro' => Util::sbr_is_pro(),
            'assetsURL' => SB_COMMON_ASSETS,
            'upsellContent' => Util::upsell_modal_content(),
            'adminNoticeContent' => apply_filters('sbr_admin_notices_filter', 1),
            'collectionsPageUrl' => admin_url('admin.php?page=sbr-collections'),
            'aboutPageUrl' => admin_url('admin.php?page=sbr-about'),
            'isDevUrl' => SBR_Upgrader::is_dev_url(  home_url() ),
            'builderUrl'           => admin_url( 'admin.php?page=sbr'),
            'collectionsList' => DB::get_collections_list(),
            'bulkHistorySources'    => get_option('sbr_bulk_sources', []),
            'freeRetrieverData'     => Util::get_free_retriever_data(),
            'emailVerificationURL'    => EmailVerification::build_email_verification_url(),
            'isEmailVerified'    => EmailVerification::check_verified()
        ];

        if (!empty($_GET['sbr_email_token']) && !empty($_GET['verified_email'])) {
            $settings_data['openSourceModal'] = true;
        }
        if( isset( $_GET['manualsource'] ) && $_GET['manualsource'] == true){
			$settings_data['manualSourcePopupInit'] = true;
		}
        $newly_retrieved_source_connection_data = Util::maybe_source_connection_data();
        if ($newly_retrieved_source_connection_data) {
            $settings_data['newSourceData'] = $newly_retrieved_source_connection_data;
        }
        return $settings_data;
    }

}
