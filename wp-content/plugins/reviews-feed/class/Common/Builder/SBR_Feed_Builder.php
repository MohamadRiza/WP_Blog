<?php
/**
 * Reviews Feed Saver
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Builder;

use Smashballoon\Customizer\V2\Feed_Builder;
use SmashBalloon\Reviews\Common\AuthorizationStatusCheck;
use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\FeedCache;
use SmashBalloon\Reviews\Common\SBR_Settings;
use SmashBalloon\Reviews\Common\Util;
use SmashBalloon\Reviews\Common\Utils\EmailVerification;

class SBR_Feed_Builder extends Feed_Builder {


    /**
     *  Customizer Tabs Path
     * @since 1.0
     */
    protected $tabs_path;

    /**
     *  Customizer Tabs NameSpace
     * @since 1.0
     */
    protected $tabs_namespace;

    /**
     *  Settings Page Tabs Path
     * @since 1.0
     */
    protected $settingspage_tabs_path;

    /**
     *  Settings Page Tabs NameSpace
     * @since 1.0
     */
    protected $settingspage_tabs_namespace;

    /**
     *  MEnu Slug
     * @since 1.0
     */
    protected $builder_menu_slug;
    protected $db;

    protected $plugin_status;

	/**
	 * @var array
	 */
	protected $menu;

	public function __construct(){
        $this->menu = [
            'parent_menu_slug' => SBR_MENU_SLUG,
            'page_title' => "Reviews Feed",
            'menu_title' => "All Feeds",
            'menu_slug' => SBR_MENU_SLUG,
        ];

        $this->tabs_path                        = SBR_CUSTOMIZER_TABS_PATH;
        $this->tabs_namespace                   = SBR_CUSTOMIZER_TABS_NAMESPACE;
        $this->builder_menu_slug                = SBR_CUSTOMIZER_MENU_SLUG;
        $this->db = new DB();
        $this->plugin_status = new AuthorizationStatusCheck();
    }

    public function dequeue_smash_plugins_style() {
        wp_dequeue_style('cff_custom_wp_admin_css');
        wp_deregister_style('cff_custom_wp_admin_css');
    }

    public function custom_builder_data(){
        EmailVerification::catch_email_verification();

        $builder_data = [
            'nonce' => wp_create_nonce('sbr-admin'),
            'feedsList' => DB::get_feeds_list(),
            'feedsCount' => DB::feeds_list_count(),
            'apiKeys' => get_option('sbr_apikeys', []),
            'apiKeyLimits' => get_option('sbr_apikeys_limit', []),
            'sourcesList' => $this->get_sources_list(),
            'sourcesCount' => SBR_Sources::get_sources_count(),
            'providers' => Util::get_providers(),
            'pluginSettings' => sbr_recursive_parse_args(get_option('sbr_settings', []), sbr_plugin_settings_defaults()),
            'connectFBUrls' => sbr_get_fb_connection_urls(),
            'assetsURL' => SB_COMMON_ASSETS,
            'pluginNotices' => Util::get_plugin_notices(),
            'pluginStatus' => $this->plugin_status->get_statuses(),
            'isPro' => Util::sbr_is_pro(),
            'upsellContent' => Util::upsell_modal_content(),
            'upsellSidebarCards' => Util::sidebar_upsell_cards(),
            'adminNoticeContent' => apply_filters('sbr_admin_notices_filter', 1),
            'themeSupportsWidgets' => current_theme_supports( 'widgets' ),
            'collectionsPageUrl' => admin_url('admin.php?page=sbr-collections'),
            'aboutPageUrl' => admin_url('admin.php?page=sbr-about'),
            'builderUrl'           => admin_url( 'admin.php?page=sbr'),
            'bulkHistorySources'    => get_option('sbr_bulk_sources', []),
            'freeRetrieverData'     => Util::get_free_retriever_data(),
            'emailVerificationURL'    => EmailVerification::build_email_verification_url(admin_url( 'admin.php?page=sbr')),
            'isEmailVerified'    => EmailVerification::check_verified()
        ];

        if (!empty($_GET['sbr_email_token']) && !empty($_GET['verified_email'])) {
            $builder_data['openSourceModal'] = true;
        }

        if( isset( $_GET['manualsource'] ) && $_GET['manualsource'] == true){
			$builder_data['manualSourcePopupInit'] = true;
		}


        $newly_retrieved_source_connection_data = Util::maybe_source_connection_data();
        if ( $newly_retrieved_source_connection_data ) {
            $builder_data['newSourceData'] = $newly_retrieved_source_connection_data;
        }

        return $builder_data;
    }

    /**
	 * Returns an associate array of all existing feeds along with their data
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public static function get_feeds_list( $feeds_args = array() ) {
        return DB::get_feeds_list( $feeds_args );
    }

    /**
	 * Get Templates
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_templates_list(){
        return [
            [
                'type' => 'default',
				'title'=> __( 'Default', 'reviews-feed' ),
            ],
            [
                'type' => 'simplecards',
				'title'=> __( 'Simple Cards', 'reviews-feed' ),
                'upsellModal' => 'templateModal',
            ],
            [
                'type' => 'masonry',
				'title'=> __( 'Masonry', 'reviews-feed' ),
                'upsellModal' => 'templateModal',
            ],
            /*
            [
                'type' => 'grid',
				'title'=> __( 'Grid', 'reviews-feed' ),
            ],
            */
            [
                'type' => 'singlereview',
				'title'=> __( 'Single Review', 'reviews-feed' ),
                'upsellModal' => 'templateModal',
            ],
            [
                'type' => 'showcasecarousel',
				'title'=> __( 'Showcase Carousel', 'reviews-feed' ),
                'upsellModal' => 'templateModal',
            ],
            [
                'type' => 'carousel',
				'title'=> __( 'Carousel', 'reviews-feed' ),
                'upsellModal' => 'templateModal',
            ],
            [
                'type' => 'gridcarousel',
				'title'=> __( 'Grid Carousel', 'reviews-feed' ),
                'upsellModal' => 'templateModal',
            ]
        ];
    }

    /**
    * Get Feed Info
    * Settings
    *
    * @return array
    *
    * @since 1.0
    */
    public function customizer_feed_data(){
        if ( isset( $_GET['feed_id'] ) ){
            $feed_id = sanitize_key( $_GET['feed_id'] );
            $feed_saver = new SBR_Feed_Saver( $feed_id );
            $settings = $feed_saver->get_feed_settings();
			$feed_db_data = $feed_saver->get_feed_db_data();

	        $feed_settings = SBR_Settings::get_settings_by_feed_id( $feed_id );

	        $feed = Util::sbr_is_pro() ? new \SmashBalloon\Reviews\Pro\Feed($feed_settings, $feed_id, new \SmashBalloon\Reviews\Pro\FeedCache($feed_id, 3000)) : new \SmashBalloon\Reviews\Common\Feed( $feed_settings, $feed_id, new FeedCache( $feed_id, 3000 ) );

	        $feed->init();
            $feed->get_set_cache();
	        $posts = $feed->get_post_set_page();
            if( isset( $settings['sortRandomEnabled'] ) && $settings['sortRandomEnabled'] === true){
                shuffle( $posts );
            }

            return [
                'feed_info' => $feed_db_data,
                'settings' => $settings,
                'posts' => !empty( $posts ) ? $posts : [],
                'sourcesList' => SBR_Sources::get_sources_list([
                    'id' => !empty($settings['sources']) && isset($settings['sources']) ? $settings['sources'] : [],
                ])
            ];
        }
        return [];
    }

    /**
     * Get Sources
     *
     * @return array
     *
     * @since 1.0
     */
    public function get_sources_list(){
         return $this->db->source_query();
    }

}
