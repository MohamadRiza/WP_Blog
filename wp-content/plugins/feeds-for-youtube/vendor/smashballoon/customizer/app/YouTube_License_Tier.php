<?php

/**
 * YouTube License Tier
 * 
 * @since 2.1
 */
namespace Smashballoon\Customizer;

use Smashballoon\Framework\Packages\License_Tier\License_Tier;
/** @internal */
class YouTube_License_Tier extends License_Tier
{
    /**
     * This gets the license key 
     */
    public $license_key_option_name = 'sby_license_key';
    /**
     * This gets the license status
     */
    public $license_status_option_name = 'sby_license_status';
    /**
     * This gets the license data
     */
    public $license_data_option_name = 'sby_license_data';
    /**
     * Item IDs
     */
    public $item_id_basic = 1722787;
    public $item_id_plus = 1722791;
    public $item_id_elite = 1722793;
    public $item_id_all_access_elite = 1724078;
    /**
     * Legacy item IDs
     */
    public $item_id_personal = 762236;
    // Item id for the personal tier.
    public $item_id_business = 762320;
    // Item id for the business tier.
    public $item_id_developer = 762322;
    // Item id for the developer tier.
    public $item_id_all_access = 789157;
    /**
     * Tier names
     */
    public $license_tier_free_name = 'free';
    // Basic tier name.
    public $license_tier_basic_name = 'basic';
    // Basic tier name.
    public $license_tier_plus_name = 'plus';
    // Plus tier name.
    public $license_tier_elite_name = 'elite';
    // Elite tier name.
    /**
     * Legacy tier names
     */
    public $license_tier_personal_name = 'personal';
    // Personal tier name.
    public $license_tier_business_name = 'business';
    // Business tier name.
    public $license_tier_developer_name = 'developer';
    // Developer tier name.
    public $edd_item_name = SBY_PLUGIN_EDD_NAME;
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * This defines the features list of the plugin
     * 
     * @return void
     */
    public function features_list()
    {
        $features_list = ['free' => ['channel_feeds'], 'basic' => ['channel_feeds', 'favorites_feeds', 'carousel_feeds', 'combine_feeds', 'performance_optimization', 'downtime_prevention_system', 'gbpr_compliant', 'playlist_feeds', 'single_feeds'], 'plus' => ['call_to_actions', 'search_feeds', 'feeds_templates', 'convert_videos_to_cpt'], 'elite' => ['live_feeds', 'video_filtering', 'feed_themes']];
        $this->plugin_features = $features_list;
    }
    /**
     * This defines features for legacy tiers
     *
     * @return void
     */
    public function legacy_features_list()
    {
        $legacy_features = ['personal' => [
            // List of features for personal tier.
            'channel_feeds',
            'favorites_feeds',
            'playlist_feeds',
            'carousel_feeds',
            'combine_feeds',
            'performance_optimization',
            'downtime_prevention_system',
            'gbpr_compliant',
            'call_to_actions',
            'search_feeds',
            'single_feeds',
            'feeds_templates',
            'convert_videos_to_cpt',
            'live_feeds',
            'video_filtering',
            'feed_themes',
        ], 'business' => [], 'developer' => []];
        $this->legacy_features = $legacy_features;
    }
}
