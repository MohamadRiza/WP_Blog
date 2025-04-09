<?php
namespace SmashBalloon\Reviews\Common;
use SmashBalloon\Reviews\Common\Builder\SBR_Sources;
use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\Helpers\SBR_Error_Handler;

class Util
{

    public static function isProduction() {
		return SBR_PRODUCTION;
    }

    public static function UTMCampaign() {
        return '';
    }

    public static function get_providers(){
        return [
            [
                'type'    => 'google',
                'name'    => 'Google',
                'heading' => __( 'Place ID', 'reviews-feed' ),
                'placeholder' => __( 'Enter Place ID', 'reviews-feed' ),
                'docLink' => 'https://smashballoon.com/doc/creating-a-google-api-key/'
            ],
            [
                'type' => 'facebook',
                'name' => 'Facebook'
            ],
            [
                'type' => 'tripadvisor',
                'name' => 'TripAdvisor',
                'heading' => __( 'Page URL', 'reviews-feed' ),
                'placeholder' => __( 'https://tripadvisor.com/...', 'reviews-feed' ),
                'apiKey' => true,
                'mandatoryApiKey'    => true,
                'docLink' => 'https://smashballoon.com/doc/creating-a-tripadvisor-api-key/'
            ],
            [
                'type' => 'yelp',
                'name' => 'Yelp',
                'heading' => __( 'Page URL', 'reviews-feed' ),
                'placeholder' => __( 'https://yelp.com/...', 'reviews-feed' ),
                'docLink' => 'https://smashballoon.com/doc/creating-a-yelp-api-key/'
            ],
            [
                'type' => 'trustpilot',
                'name' => 'TrustPilot',
                'heading' => __( 'Page URL', 'reviews-feed' ),
                'placeholder' => __( 'https://trustpilot.com/review/...', 'reviews-feed' ),
                'onlyDetails' => true
            ],
            [
                'type' => 'wordpress.org',
                'name' => 'WordPress.org',
                'heading' => __( 'Page URL', 'reviews-feed' ),
                'placeholder' => __( 'https://wordpress.org/...', 'reviews-feed' ),
                'onlyDetails' => true
            ]
        ];
    }

    /**
    * Used as a listener for the account connection process. If
    * data is returned from the account connection processed it's used
    * to generate the list of possible sources to chose from.
    *
    * @return array|bool
    *
    * @since 1.0
    */
    public static function maybe_source_connection_data() {
        $nonce = !empty($_GET['cff_con']) ? sanitize_key($_GET['cff_con']) : '';
		if (!wp_verify_nonce($nonce, 'cff_con')) {
			return false;
		}

		if (
			isset($_GET['cff_access_token'])
			&& isset($_GET['cff_final_response'])
			&& $_GET['cff_final_response'] == 'true'
		) {

            $access_token = sanitize_text_field( wp_unslash( $_GET['cff_access_token'] ) );
            $return = Util::retrieve_available_pages($access_token);

            if ( $return ) {
                return $return;
            } else {
                return array('error' => __('Unable to connect to Facebook to retrieve account information.', 'reviews-feed'));
            }
        }
        return false;
    }

    /**
     * Uses the Facebook API to retrieve a list of pages for the
     * access token
     *
     * @param string $access_token
     *
     * @return array|bool
     *
     * @since 1.0
     */
    public static function retrieve_available_pages( $access_token ){
        //Get User Info
        $user_url = 'https://graph.facebook.com/me?fields=name,id,picture&access_token=' . $access_token;
        $user_id_data = wp_remote_retrieve_body( wp_remote_get($user_url) );
        $user_id_data_arr = json_decode($user_id_data, true);

        $url = 'https://graph.facebook.com/me/accounts?fields=access_token,name,id,overall_star_rating,rating_count&limit=500&access_token=' . $access_token;
        $pages_data = wp_remote_retrieve_body( wp_remote_get( $url ) );
        $pages_data_arr = json_decode($pages_data, true);


        if ( isset( $pages_data_arr['data'] ) ) {
            $return = [
                'user' => $user_id_data_arr,
                'pages' => $pages_data_arr['data'],
            ];

            return $return;
        } else if ( isset( $pages_data_arr['error'] ) ) {
            return $pages_data_arr;
        } else {
            return [
                'error' => [
                    'code' => 'HTTP Request',
                    'message' => __('Your server could not complete a remote request to Facebook\'s API. Your host may be blocking access or
                    there may be a problem with your server.', 'reviews-feed')
                ]
            ];
        }

    }

    public static function currentPageIs($page){
        $current_screen = get_current_screen();
        return $current_screen !== null && !empty($current_screen) && strpos($current_screen->id, $page) !== false;
    }

    /**
     * Check Notices for no Provider API KEY
     *
     *
     * @return boolean
     *
     * @since 1.0
     */
    public static function check_notice_provider_nokey($api_limits)
    {
        $noapi_providers = ['wordpress.org', 'trustpilot'];
        $is_true = $api_limits == $noapi_providers ? false : true;

        foreach ($noapi_providers as $provider) {
            if( $api_limits == [$provider] ){
                $is_true = false;
            }
        }
        return $is_true;
    }

    /**
     * Returns a list of notices to be displayed in the SB Reviews Pages
     *
     *
     * @return array
     *
     * @since 1.0
     */
    public static function get_plugin_notices(){
        $notices = [];

        $api_limits = get_option('sbr_apikeys_limit', []);
        $should_show_notice = is_array($api_limits) && sizeof($api_limits) > 0 && Util::check_notice_provider_nokey($api_limits);
        if ( $should_show_notice ) {
            array_push( $notices, [
                'type' => 'error',
                'heading' => __( 'New reviews will not display until an API key is entered for your sources.', 'reviews-feed' ),
                'description' => __('Due to API limitations your feeds will not show new reviews until you enter an API key on the settings page.', 'reviews-feed'),
                'actions' => [
                    [
                        'type' => 'primary',
                        'text' => __('How to create an API key', 'reviews-feed'),
                        'link' => admin_url('admin.php?page=sbr-settings')
                    ],
                    [
                        'type' => 'secondary',
                        'text' => __('Go to Settings page', 'reviews-feed'),
                        'link' => admin_url('admin.php?page=sbr-settings')
                    ]
                ]
            ] );
        }

        $noAPIKeyProviders = ['trustpilot', 'wordpress.org'];
        $noAPIProviderxists = count(array_intersect($noAPIKeyProviders, $api_limits)) ? true : false;

        if( $noAPIProviderxists ){
            array_push( $notices, [
                'type' => 'error',
                'heading' => __( 'You have reached the number of allowed sources.', 'reviews-feed' ),
                'description' => __('Due to API limitations your feeds will not show new reviews because you have reached the number of allowed sources.', 'reviews-feed'),
            ] );
        }

        return $notices;
    }

    /**
     * Get Smahballoon Plugins Info
     *
     * @since 1.0
     */
    public static function get_plugins_info()
    {
        $installed_plugins = get_plugins();
        $plugins_list = [
            'facebook' => [
                'free' => 'custom-facebook-feed/custom-facebook-feed.php',
                'pro' => 'custom-facebook-feed-pro/custom-facebook-feed.php',
                'link' => 'https://smashballoon.com/custom-facebook-feed/'
            ],
            'instagram' => [
                'free' => 'instagram-feed/instagram-feed.php',
                'pro' => 'instagram-feed-pro/instagram-feed.php',
                'link' => 'https://smashballoon.com/instagram-feed/'
            ],
            'twitter' => [
                'free' => 'custom-twitter-feeds/custom-twitter-feed.php',
                'pro' => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
                'link' => 'https://smashballoon.com/custom-twitter-feeds/'
            ],
            'youtube' => [
                'free' => 'feeds-for-youtube/youtube-feed.php',
                'pro' => 'youtube-feed-pro/youtube-feed.php',
                'link' => 'https://smashballoon.com/youtube-feed/'
            ]
        ];

        foreach ($plugins_list as $name => $plugin) {
            $type = 'none';
            $activated = 'none';
            if (isset($installed_plugins[$plugin['free']])) {
                $type = 'free';
                $activated = is_plugin_active($plugin['free']);
            }
            if (isset($installed_plugins[$plugin['pro']])) {
                $type = 'pro';
                $activated = is_plugin_active($plugin['pro']);
            }
            $plugins_list[$name]['activated'] = $activated;
            $plugins_list[$name]['type'] = $type;
        }

        return [
            'facebook' => [
                'plugin' => $plugins_list['facebook']['pro'],
                'link' => $plugins_list['facebook']['link'],
                'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
                'title' => __('Custom Facebook Feed', 'reviews-feed'),
                'description' => __('Add Facebook posts from your timeline, albums and much more.', 'reviews-feed'),
                'icon' => 'fb-icon.svg',
                'activated' => $plugins_list['facebook']['activated'],
                'type' => $plugins_list['facebook']['type'],
            ],
            'instagram' => [
                'plugin' => $plugins_list['instagram']['pro'],
                'link' => $plugins_list['instagram']['link'],
                'download_plugin' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
                'title' => __('Instagram Feed', 'reviews-feed'),
                'description' => __('A quick and elegant way to add your Instagram posts to your website. ', 'reviews-feed'),
                'icon' => 'insta-icon.svg',
                'activated' => $plugins_list['instagram']['activated'],
                'type' => $plugins_list['instagram']['type'],
            ],
            'twitter' => [
                'plugin' => $plugins_list['twitter']['pro'],
                'link' => $plugins_list['twitter']['link'],
                'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
                'title' => __('Custom Twitter Feeds', 'reviews-feed'),
                'description' => __('A customizable way to display tweets from your Twitter account. ', 'reviews-feed'),
                'icon' => 'twitter-icon.svg',
                'activated' => $plugins_list['twitter']['activated'],
                'type' => $plugins_list['twitter']['type'],
            ],
            'youtube' => [
                'plugin' => $plugins_list['youtube']['pro'],
                'link' => $plugins_list['youtube']['link'],
                'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
                'title' => __('Feeds for YouTube', 'reviews-feed'),
                'description' => __('A simple yet powerful way to display videos from YouTube. ', 'reviews-feed'),
                'icon' => 'youtube-icon.svg',
                'activated' => $plugins_list['youtube']['activated'],
                'type' => $plugins_list['youtube']['type'],
            ]
        ];
    }

    /**
     * Get Smahballoon Recommended Plugins Info
     *
     * @since 1.0
     */
    public static function get_smashballoon_recommended_plugins_info()
    {
        $installed_plugins = get_plugins();
        return [
			    'wpforms'         => [
				    'plugin'          => 'wpforms-lite/wpforms.php',
				    'download_plugin' => 'https://downloads.wordpress.org/plugin/wpforms-lite.zip',
				    'title'           => __( 'WPForms', 'reviews-feed' ),
				    'description'     => __( 'The most beginner friendly drag & drop WordPress forms plugin allowing you to create beautiful contact forms, subscription forms, payment forms, and more in minutes, not hours!', 'reviews-feed' ),
				    'icon'            => 'plugin-wpforms.png',
				    'installed'       => isset( $installed_plugins['wpforms-lite/wpforms.php'] ),
				    'activated'       => is_plugin_active( 'wpforms-lite/wpforms.php' ),
			    ],
			    'monsterinsights' => [
				    'plugin'          => 'google-analytics-for-wordpress/googleanalytics.php',
				    'download_plugin' => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
				    'title'           => __( 'MonsterInsights', 'reviews-feed' ),
				    'description'     => __( 'MonsterInsights makes it “effortless” to properly connect your WordPress site with Google Analytics, so you can start making data-driven decisions to grow your business.', 'reviews-feed' ),
				    'icon'            => 'plugin-mi.png',
				    'installed'       => isset( $installed_plugins['google-analytics-for-wordpress/googleanalytics.php'] ),
				    'activated'       => is_plugin_active( 'google-analytics-for-wordpress/googleanalytics.php' ),
			    ],
			    'optinmonster'    => [
				    'plugin'          => 'optinmonster/optin-monster-wp-api.php',
				    'download_plugin' => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
				    'title'           => __( 'OptinMonster', 'reviews-feed' ),
				    'description'     => __( 'Our high-converting optin forms like Exit-Intent® popups, Fullscreen Welcome Mats, and Scroll boxes help you dramatically boost conversions and get more email subscribers.', 'reviews-feed' ),
				    'icon'            => 'plugin-om.png',
				    'installed'       => isset( $installed_plugins['optinmonster/optin-monster-wp-api.php'] ),
				    'activated'       => is_plugin_active( 'optinmonster/optin-monster-wp-api.php' ),
			    ],
			    'wp_mail_smtp'    => [
				    'plugin'          => 'wp-mail-smtp/wp_mail_smtp.php',
				    'download_plugin' => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
				    'title'           => __( 'WP Mail SMTP', 'reviews-feed' ),
				    'description'     => __( 'Make sure your website\'s emails reach the inbox. Our goal is to make email deliverability easy and reliable. Trusted by over 1 million websites.', 'reviews-feed' ),
				    'icon'            => 'plugin-smtp.png',
				    'installed'       => isset( $installed_plugins['wp-mail-smtp/wp_mail_smtp.php'] ),
				    'activated'       => is_plugin_active( 'wp-mail-smtp/wp_mail_smtp.php' ),
			    ],
			    'rafflepress'     => [
				    'plugin'          => 'rafflepress/rafflepress.php',
				    'download_plugin' => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
				    'title'           => __( 'RafflePress', 'reviews-feed' ),
				    'description'     => __( 'Turn your visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with powerful viral giveaways & contests.', 'reviews-feed' ),
				    'icon'            => 'plugin-rp.png',
				    'installed'       => isset( $installed_plugins['rafflepress/rafflepress.php'] ),
				    'activated'       => is_plugin_active( 'rafflepress/rafflepress.php' ),
			    ],
			    'aioseo'          => [
				    'plugin'          => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
				    'download_plugin' => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
				    'title'           => __( 'All in One SEO Pack', 'reviews-feed' ),
				    'description'     => __( 'Out-of-the-box SEO for WordPress. Features like XML Sitemaps, SEO for custom post types, SEO for blogs, business sites, or ecommerce sites, and much more.', 'reviews-feed' ),
				    'icon'            => 'plugin-seo.png',
				    'installed'       => isset( $installed_plugins['all-in-one-seo-pack/all_in_one_seo_pack.php'] ),
				    'activated'       => is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ),
			    ]
                ];
    }
    /**
     * Get other active plugins of Smash Balloon
     *
     * @since 4.4.0
     */
    public static function get_sb_active_plugins_info()
    {
        // get the WordPress's core list of installed plugins
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $installed_plugins = get_plugins();

        $is_facebook_installed = false;
        $facebook_plugin = 'custom-facebook-feed/custom-facebook-feed.php';
        if (isset($installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'])) {
            $is_facebook_installed = true;
            $facebook_plugin = 'custom-facebook-feed-pro/custom-facebook-feed.php';
        } else if (isset($installed_plugins['custom-facebook-feed/custom-facebook-feed.php'])) {
            $is_facebook_installed = true;
        }

        $is_instagram_installed = false;
        $instagram_plugin = 'instagram-feed/instagram-feed.php';
        if (isset($installed_plugins['instagram-feed-pro/instagram-feed.php'])) {
            $is_instagram_installed = true;
            $instagram_plugin = 'instagram-feed-pro/instagram-feed.php';
        } else if (isset($installed_plugins['instagram-feed/instagram-feed.php'])) {
            $is_instagram_installed = true;
        }

        $is_twitter_installed = false;
        $twitter_plugin = 'custom-twitter-feeds/custom-twitter-feed.php';
        if (isset($installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'])) {
            $is_twitter_installed = true;
            $twitter_plugin = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
        } else if (isset($installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'])) {
            $is_twitter_installed = true;
        }

        $is_youtube_installed = false;
        $youtube_plugin = 'feeds-for-youtube/youtube-feed.php';
        if (isset($installed_plugins['youtube-feed-pro/youtube-feed-pro.php'])) {
            $is_youtube_installed = true;
            $youtube_plugin = 'youtube-feed-pro/youtube-feed-pro.php';
        } elseif (isset($installed_plugins['feeds-for-youtube/youtube-feed.php'])) {
            $is_youtube_installed = true;
        }

        $is_social_wall_installed = isset($installed_plugins['social-wall/social-wall.php']) ? true : false;
        $social_wall_plugin = 'social-wall/social-wall.php';


        return array(
            'is_facebook_installed' => $is_facebook_installed,
            'is_instagram_installed' => $is_instagram_installed,
            'is_twitter_installed' => $is_twitter_installed,
            'is_youtube_installed' => $is_youtube_installed,
            'is_social_wall_installed' => $is_social_wall_installed,
            'facebook_plugin' => $facebook_plugin,
            'instagram_plugin' => $instagram_plugin,
            'twitter_plugin' => $twitter_plugin,
            'youtube_plugin' => $youtube_plugin,
            'social_wall_plugin' => $social_wall_plugin,
            'installed_plugins' => $installed_plugins
        );
    }

    /**
     * SBR Get Whitespace
     *
     * @since 1.0
     *
     * @param int $times
     *
     * @return string
     */
    public static function get_whitespace($times)
    {
        return str_repeat('&nbsp;', $times);
    }

    /**
     * Get Site and Server Info
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_site_n_server_info()
    {
        $allow_url_fopen = ini_get('allow_url_fopen') ? "Yes" : "No";
        $php_curl = is_callable('curl_init') ? "Yes" : "No";
        $php_json_decode = function_exists("json_decode") ? "Yes" : "No";
        $php_ssl = in_array('https', stream_get_wrappers()) ? "Yes" : "No";

        $output = '## SITE/SERVER INFO: ##' . "</br>";
        $output .= 'Plugin Version:' . self::get_whitespace(11) . SBR_MENU_SLUG . "</br>";
        $output .= 'Site URL:' . self::get_whitespace(17) . site_url() . "</br>";
        $output .= 'Home URL:' . self::get_whitespace(17) . home_url() . "</br>";
        $output .= 'WordPress Version:' . self::get_whitespace(8) . get_bloginfo('version') . "</br>";
        $output .= 'PHP Version:' . self::get_whitespace(14) . PHP_VERSION . "</br>";
        $output .= 'Web Server Info:' . self::get_whitespace(10) . esc_html( $_SERVER['SERVER_SOFTWARE'] ) . "</br>";
        $output .= 'PHP allow_url_fopen:' . self::get_whitespace(6) . $allow_url_fopen . "</br>";
        $output .= 'PHP cURL:' . self::get_whitespace(17) . $php_curl . "</br>";
        $output .= 'JSON:' . self::get_whitespace(21) . $php_json_decode . "</br>";
        $output .= 'SSL Stream:' . self::get_whitespace(15) . $php_ssl . "</br>";
        $output .= "</br>";

        return $output;
    }

    /**
     * Get Active Plugins
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_active_plugins_info()
    {
        $plugins = get_plugins();
        $active_plugins = get_option('active_plugins');
        $output = "## ACTIVE PLUGINS: ## </br>";

        foreach ($plugins as $plugin_path => $plugin) {
            if (in_array($plugin_path, $active_plugins)) {
                $output .= $plugin['Name'] . ': ' . $plugin['Version'] . "</br>";
            }
        }

        $output .= "</br>";

        return $output;
    }


    /**
     * Get Global Settings
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_global_settings_info()
    {
        $output = '## GLOBAL SETTINGS: ## </br>';
        $sbr_settings = get_option('sbr_settings', array());

        $plugin_status = new AuthorizationStatusCheck();

        $statuses = $plugin_status->get_statuses();

        if( Util::sbr_is_pro() ){
            $output .= 'License key: ';
            if ( isset( $sbr_settings['license_key'] ) ) {
                $output .= esc_html($sbr_settings['license_key']);
            } else {
                $output .= ' Not added';
            }

            $output .= '</br>';
            $output .= 'License Tier: ';
            if (isset($statuses['license_tier'])) {
                $output .= esc_html($statuses['license_tier']);
            } else {
                $output .= ' Not Set';
            }
            $output .= '</br>';
            $output .= 'License status: ';
            if (isset($sbr_settings['license_status'])) {
                $output .= $sbr_settings['license_status'];
            } else {
                $output .= ' Inactive';
            }
            $output .= '</br>';
        }
        $output .= 'Preserve settings if plugin is removed: ';
        $output .= isset($sbr_settings['preserve_settings']) && ($sbr_settings['preserve_settings']) ? 'Yes' : 'No';
        $output .= '</br>';
        $output .= 'Caching: ';
        $output .= $statuses['license_tier'] === 3 ? 'Twice daily' : 'daily';

        $output .= '</br>';
        $output .= 'GDPR: ';
        $output .= isset($sbr_settings['gdpr']) ? $sbr_settings['gdpr'] : ' Not setup';
        $output .= '</br>';
        $output .= 'Optimize Images: ';
        $output .= isset($sbr_settings['optimize_images']) && $sbr_settings['optimize_images'] === true ? 'Enabled' : 'Disabled';
        $output .= '</br>';
        $output .= 'Usage Tracking: ';
        $output .= isset($sbr_settings['usagetracking']) && $sbr_settings['usagetracking'] === true ? 'Enabled' : 'Disabled';
        $output .= '</br>';
        $output .= 'Enqueue in Head: ';
        $output .= isset($sbr_settings['enqueue_js_in_header']) && $sbr_settings['enqueue_js_in_header'] === true ? 'Enabled' : 'Disabled';
        $output .= '</br>';
        $output .= 'Admin Error Notice: ';
        $output .= isset($sbr_settings['admin_error_notices']) && $sbr_settings['admin_error_notices'] === true ? 'Enabled' : 'Disabled';
        $output .= '</br>';
        $output .= 'Feed Issue Email Reports: ';
        $output .= isset($sbr_settings['feed_issue_reports']) && $sbr_settings['feed_issue_reports'] === true ? 'Enabled' : 'Disabled';
        $output .= '</br>';
        $output .= '</br>';
        return $output;
    }

    /**
     * Get Feeds Settings
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_sources_settings_info()
    {
        $output = '## SOURCES: ## </br>';
        $source_list = SBR_Sources::get_sources_list();
        foreach ($source_list as $feed) {
            $output .= $feed['name'] . ' ( ' . strtoupper( $feed['provider'] ) . ' => ' . $feed['account_id'] . ' )';
            $output .= '</br>';
        }
        $output .= '</br>';

        return $output;
    }

    /**
     * Get Feeds Settings
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_api_settings_info()
    {
        $output = '## API KEYS: ## </br>';
        $api_keys = get_option('sbr_apikeys', []);

        foreach ($api_keys as $id => $api) {
            $output .= ucfirst( $id ) . ' => ' . $api;
            $output .= '</br>';
        }
        $output .= '</br>';

        return $output;
    }



    /**
     * Get Feeds Settings
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_feeds_settings_info()
    {
        $output = '## FEEDS: ## </br>';

        $feeds_list = DB::get_feeds_list();

        $i = 0;
        foreach ($feeds_list as $feed) {

            if ($i >= 25) {
                break;
            }
            $output .= $feed['feed_name'];
            if (isset($feed['settings'])) {
                $output .= '</br>';
                if (!empty($feed['sourcesList'])) {
                    foreach ($feed['sourcesList'] as $id => $source) {
                        $output .= esc_html($source['name']);
                        $output .= ' (' . esc_html(ucfirst($source['name'])) . ' => ' . esc_html($source['account_id']) . ')';
                        $output .= '</br>';
                    }
                }

            }
            $output .= '</br>';
            if (isset($feed['location_summary']) && count($feed['location_summary']) > 0) {
                $first_feed = $feed['location_summary'][0];
                if (!empty($first_feed['link'])) {
                    $output .= esc_html($first_feed['link']) . '?sb_debug';
                    $output .= '</br>';
                }
            }

            if ($i < (count($feeds_list) - 1)) {
                $output .= '</br>';
            }
            $i++;
        }
        $output .= '</br>';

        return $output;
    }

    /**
     * Get Posts Table Info
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_posts_table_info()
    {
        $output = '## POSTS: ## </br>';

        global $wpdb;
        $table_name = $wpdb->prefix . 'sbr_posts';
        $feeds_posts_table_name = $wpdb->prefix . 'sbr_reviews_posts';

        if ($wpdb->get_var("show tables like '$feeds_posts_table_name'") !== $feeds_posts_table_name) {
            $output .= 'no feeds posts table</br>';
        } else {
            $last_result = $wpdb->get_results("SELECT * FROM $feeds_posts_table_name ORDER BY id DESC LIMIT 1;");
            if (is_array($last_result) && isset($last_result[0])) {
                $output .= '## FEEDS POSTS TABLE ##</br>';
                foreach ($last_result as $column) {
                    foreach ($column as $key => $value) {
                        $output .= esc_html($key) . ': ' . esc_html($value) . '</br>';

                    }
                }
            } else {
                $output .= 'feeds posts has no rows';
                $output .= '</br>';
            }
        }
        $output .= '</br>';
        if ($wpdb->get_var("show tables like '$table_name'") !== $table_name) {
            $output .= 'no posts table</br>';
        } else {
            $last_result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 1;");
            if (is_array($last_result) && isset($last_result[0])) {
                $output .= '## POSTS TABLE ##';
                $output .= '</br>';
                foreach ($last_result as $column) {
                    foreach ($column as $key => $value) {
                        $output .= esc_html($key) . ': ' . esc_html($value) . '</br>';

                    }
                }
            } else {
                $output .= 'posts has no rows</br>';
            }
        }
        $output .= '</br>';

        return $output;
    }

    /**
     * List of possible languages/translations
     *
     * @since 1.0
     *
     * @return array
     */
    public static function get_translation_languages($include_default = false)
    {
        $languages = [
            'default' => 'Default',
            '' => 'No Translation',
            'en' =>	'English',
            'af' =>	'Afrikaans',
            'am' =>	'Amharic',
            'ar' =>	'Arabic',
            'az' =>	'Azerbaijani',
            'be' =>	'Belarusian',
            'bg' =>	'Bulgarian',
            'bn' =>	'Bengali',
            'bs' =>	'Bosnian',
            'ca' =>	'Catalan',
            'cs' =>	'Czech',
            'da' =>	'Danish',
            'de' =>	'German',
            'el' =>	'Greek',
            'en-AU' =>	'English (Australian)',
            'en-GB' =>	'English (Great Britain)',
            'es' =>	'Spanish',
            'es-419' =>	'Spanish (Latin America)',
            'et' =>	'Estonian',
            'eu' =>	'Basque',
            'fa' =>	'Farsi',
            'fi' =>	'Finnish',
            'fil' => 'Filipino',
            'fr' =>	'French',
            'fr-CA' =>	'French (Canada)',
            'gl' =>	'Galician',
            'gu' =>	'Gujarati',
            'hi' =>	'Hindi',
            'hr' =>	'Croatian',
            'hu' =>	'Hungarian',
            'hy' =>	'Armenian',
            'id' =>	'Indonesian',
            'is' =>	'Icelandic',
            'it' =>	'Italian	',
            'iw' =>	'Hebrew',
            'ja' =>	'Japanese',
            'ka' =>	'Georgian',
            'kk' =>	'Kazakh',
            'km' =>	'Khmer',
            'kn' =>	'Kannada',
            'ko' =>	'Korean',
            'ky' =>	'Kyrgyz',
            'lo' =>	'Lao',
            'lt' =>	'Lithuanian',
            'lv' =>	'Latvian',
            'mk' =>	'Macedonian',
            'ml' =>	'Malayalam',
            'mn' =>	'Mongolian',
            'mr' =>	'Marathi',
            'ms' =>	'Malay',
            'my' =>	'Burmese',
            'ne' =>	'Nepali',
            'nl' =>	'Dutch',
            'no' =>	'Norwegian',
            'pa' =>	'Punjabi',
            'pl' =>	'Polish',
            'pt' =>	'Portuguese',
            'pt-BR' => 'Portuguese (Brazil)',
            'pt-PT' => 'Portuguese (Portugal)',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'si' => 'Sinhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'zh' => 'Chinese',
            'zh-CN' => 'Chinese (Simplified)',
            'zh-HK' => 'Chinese (Hong Kong)',
            'zh-TW' => 'Chinese (Traditional)',
            'zu' => 'Zulu'
        ];
        if ($include_default === false) {
            array_shift($languages);
        }

        //Detect if WPMl is active then add the option
        if( defined('ICL_SITEPRESS_VERSION') && Util::sbr_is_pro() ){
            $position = $include_default ? 2 : 1;
            $languages = array_merge(
                array_slice($languages, 0, $position),
                ['wpml' => 'Automatically by WPML'],
                array_slice($languages, $position)
            );
        }

        return $languages;
    }

    /**
     * Get Language for API call
     *
     * @since 1.0
     *
     * @return string
     */

    public static function get_settings_language($settings) {
        $args = isset( $settings['localization'] ) && $settings['localization'] !== 'default'
                ? $settings
                : wp_parse_args(get_option('sbr_settings', []), sbr_plugin_settings_defaults());

        return $args['localization'];
    }

    /**
     * Get Language for API call
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_api_call_language($settings) {
        return Util::sbr_is_pro() ? \SmashBalloon\Reviews\Pro\Helpers\SBR_WPML::get_current_language( Util::get_settings_language( $settings ) ) : Util::get_settings_language( $settings );
    }

    /**
     * Is Plugin Pro
     *
     * @since 1.0
     *
     * @return boolean
     */
    public static function sbr_is_pro()
    {
        return defined( 'SBR_PRO' ) && SBR_PRO === true;
    }


    /**
     * Get List of Upsell Modal Content
     *
     * @since 1.0
     *
     * @return array
     */
    public static function upsell_modal_content()
    {
        if(Util::sbr_is_pro()){
            return [];
        }

        return [
            'facebookProvider' => [
                'heading' => __('Upgrade to Pro to display Facebook reviews', 'reviews-feed'),
                'description' => __('Upgrade to our "Plus" tier to display reviews from the well known social media platform.', 'reviews-feed'),
                'image' => 'upsell-facebook.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=facebook-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=facebook-modal&utm_content=Upgrade',
                # 'demo' => 'https://smashballoon.com/reviews-feed/demo/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=responsive-modal&utm_content=ViewDemo'
                ],

                'includeContent' => true
            ],
            'trustpilotProvider' => [
                'heading' => __('Upgrade to Pro to display TrustPilot reviews', 'reviews-feed'),
                'description' => __('Upgrade to our "Plus" tier to display reviews from the well known business review site.', 'reviews-feed'),
                'image' => 'upsell-trustpilot.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=trustpilot-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=trustpilot-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'tripadvisorProvider' => [
                'heading' => __('Upgrade to Pro to display TripAdvisor reviews', 'reviews-feed'),
                'description' => __('Upgrade to our "Elite" tier to display reviews from the well known travel advice site.', 'reviews-feed'),
                'image' => 'upsell-tripadvisor.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=tripadvisor-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=tripadvisor-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'wordpress.orgProvider' => [
                'heading' => __('Upgrade to Pro to display WordPress.org reviews', 'reviews-feed'),
                'description' => __('Upgrade to our "Elite" tier to display reviews for plugins and themes.', 'reviews-feed'),
                'image' => 'upsell-wordpress.org.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=wordpressorg-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=wordpressorg-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'carouselModal' => [
                'heading' => __('Upgrade to Pro to get Carousel layout', 'reviews-feed'),
                'description' => __('An eye-catching rotating slider of your videos to add extra content in minimal space on your website.', 'reviews-feed'),
                'image' => 'upsell-carousel.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=carousel-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=carousel-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'moreReviewsModal' => [
                'heading' => __('Upgrade to Pro to display more reviews', 'reviews-feed'),
                'description' => __('More layout settings to customize the look and feel of your reviews even more.', 'reviews-feed'),
                'image' => 'upsell-morereviews.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=num-reviews-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=num-reviews-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'averageRatingModal' => [
                'heading' => __('Upgrade to Pro to display average rating', 'reviews-feed'),
                'description' => __('Boost social proof to make more sales conversions with the number of ratings and an average rating.', 'reviews-feed'),
                'image' => 'upsell-averagerating.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=average-rating-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=average-rating-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'loadMoreModal' => [
                'heading' => __('Upgrade to Pro to add load more functionality', 'reviews-feed'),
                'description' => __('Overwhelm (in a good way) your visitors with additional reviews loaded on the page with a click.', 'reviews-feed'),
                'image' => 'upsell-loadmore.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=load-more-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=load-more-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'reviewsMediaModal' => [
                'heading' => __('Upgrade to Pro to add images', 'reviews-feed'),
                'description' => __('Display images from Yelp and Tripadvisor reviews.', 'reviews-feed'),
                'image' => 'upsell-reviewsmedia.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=lite-upgrade-footer-coupon&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=template-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'authorImageModal' => [
                'heading' => __('Upgrade to Pro to display author images', 'reviews-feed'),
                'description' => __('Build brand trust with positive reviews from real customers.', 'reviews-feed'),
                'image' => 'upsell-authorimage.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=author-avatar-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=author-avatar-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'filtersModal' => [
                'heading' => __('Upgrade to Pro to filter your reviews', 'reviews-feed'),
                'description' => __('Show only the most positive reviews and build brand trust with review filtering.', 'reviews-feed'),
                'image' => 'upsell-filters.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=star-filter-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=star-filter-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'moderationModal' => [
                'heading' => __('Upgrade to Pro to moderate your reviews', 'reviews-feed'),
                'description' => __('Take complete control of what reviews show in the feed using keyword filters and a visual moderation system.', 'reviews-feed'),
                'image' => 'upsell-moderation.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=moderation-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=moderation-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'templateModal' => [
                'heading' => __('Upgrade to Pro to get one-click templates!', 'reviews-feed'),
                'description' => __('Quickly create and preview new feeds with pre-configured options based on popular feed types.', 'reviews-feed'),
                'image' => 'upsell-template.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=template-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=template-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ],
            'responsiveModal' => [
                'heading' => __('Upgrade to Pro for responsive layouts', 'reviews-feed'),
                'description' => __('Take control of your feed layouts by customizing number of reviews & columns', 'reviews-feed'),
                'image' => 'upsell-responsive.png',
                'buttons' => [
                    'lite' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=all-feeds&utm_medium=responsive-modal&utm_content=LiteUsers50OFF',
                    'upgrade' => 'https://smashballoon.com/pricing/reviews-feed/?utm_campaign=reviews-free&utm_source=customizer&utm_medium=temresponsiveplate-modal&utm_content=Upgrade'
                ],
                'includeContent' => true
            ]
        ];
    }

    /**
     * Get List of Upsell Sidebar Cards
     *
     * @since 1.1
     *
     * @return array
     */
    public static function sidebar_upsell_cards()
    {
        if(Util::sbr_is_pro()){
            return [];
        }
        return [
            [
                'heading' => __('Display images', 'reviews-feed'),
                'description' => __('With the Pro version enable images like avatars and review  photos', 'reviews-feed'),
                'image'     => 'upswidget-images.png',
                'modal' => 'authorImageModal'
            ],
            [
                'heading' => __('Get carousel layout', 'reviews-feed'),
                'description' => __('Show your reviews in a neat carousel with Review Feed Pro', 'reviews-feed'),
                'image'     => 'upswidget-carousel.png',
                'modal' => 'carouselModal'
            ],
            [
                'heading' => __('Change your feed style with one-click templates!', 'reviews-feed'),
                'description' => __('Over 12 templates to choose from', 'reviews-feed'),
                'image'     => 'upswidget-templates.png',
                'modal' => 'templateModal'
            ],
            [
                'heading' => __('Only show selected reviews with Moderation mode', 'reviews-feed'),
                'description' => __('Hide or show only certain reviews', 'reviews-feed'),
                'image'     => 'upswidget-moderation.png',
                'modal' => 'moderationModal'
            ]
        ];
    }


     /**
     * Transform Single Review for storing purposes
     *
     * @since 1.4
     *
     * @return array
     */
    public static function parse_single_review($review, $provider_id, $review_id)
    {
        $name = $review['reviewer']['name'];
        $name_array = explode(' ',$name);
        $first_name = isset($review['reviewer']['first_name']) ? $review['reviewer']['first_name'] : $name_array[0];
        $last_name = isset($review['reviewer']['last_name']) ? $review['reviewer']['last_name'] : (isset($name_array[1]) ? $name_array[1] : '');

        $sanitized_review = [
			'time' 			=> $review['time'],
			'rating' 		=> $review['rating'],
			'provider_id' 	=> $provider_id,
			'review_id'		=> $review_id,
			'text' 			=> $review['text'],
			'title' 		=> isset($review['title']) ? $review['title'] : substr($review['text'], 0, 40),
			'reviewer'		=> [
				'name' 			=> $name,
				'first_name' 	=> $first_name,
				'last_name' 	=> $last_name,
				'avatar' 		=> $review['reviewer']['avatar']
			],
			'provider'		=> [
				'name' 	=> $review['provider']['name']
			],
			'source'		=> [
				'id' 	=> $provider_id,
				'url' 	=> ''
			]
		];
        return $sanitized_review;
    }


    public static function is_facebook_collection_post($post)
    {
        return ( isset($post['provider']) && isset($post['provider']['name']) && $post['provider']['name'] === 'facebook' &&  isset($post['provider_id']) && strpos($post['provider_id'], 'collection') !== false ) === true;
    }

    /**
     * Convert Object to Array
     *
     * @return array
     *
     * @since 1.0
     */
    public static function object_to_array($data)
    {
		if (is_object($data)) {
			$data = json_decode(json_encode($data), true);
		}
		return $data;
	}

    /**
     * Convert Object to Array
     *
     * @return array
     *
     * @since 1.0
     */
    public static function get_free_retriever_data()
    {
        if (Util::sbr_is_pro()) {
            $retriever = new \SmashBalloon\Reviews\Pro\Utils\FreeRetriever();
        } else {
            $retriever = new \SmashBalloon\Reviews\Common\Utils\FreeRetriever();
        }
        return $retriever->get_settings();
    }

     /**
     * Get Feeds Settings
     *
     * @since 1.0
     *
     * @return string
     */
    public static function get_settings_page_errors()
    {
        $output = '## ERROR LOGS: ## </br></br>';
        $errors = SBR_Error_Handler::get_errors();
        $errors = array_reverse($errors);
        foreach ($errors as $error) {
            $output .= json_encode($error);
            $output .= '</br></br>';
        }
        $output .= '</br>';

        return $output;
    }


}