<?php
/*
Plugin Name: Feeds for YouTube
Plugin URI: https://smashballoon.com/youtube-feed
Description: The Feeds for YouTube plugin allows you to display customizable YouTube feeds from any YouTube channel.
Version: 2.4.0
Requires PHP: 7.4
Author: Smash Balloon YouTube Team
Author URI: https://smashballoon.com/
Text Domain: feeds-for-youtube
*/

/*
Copyright 2024 Smash Balloon LLC (email : hey@smashballoon.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

use Smashballoon\Customizer\Container;
use Smashballoon\Customizer\PreviewProvider;
use SmashBalloon\YouTubeFeed\Admin\SBY_Notifications;
use SmashBalloon\YouTubeFeed\Admin\SBY_New_User;
use SmashBalloon\YouTubeFeed\Admin\SBY_Tracking;
use SmashBalloon\YouTubeFeed\Blocks\SBY_Blocks;
use SmashBalloon\YouTubeFeed\Customizer\ShortcodePreviewProvider;
use SmashBalloon\YouTubeFeed\Feed_Locator;
use SmashBalloon\YouTubeFeed\SBY_Cron_Updater;
use SmashBalloon\YouTubeFeed\SBY_Posts_Manager;
use SmashBalloon\YouTubeFeed\Services\ActivationService;
use SmashBalloon\YouTubeFeed\Services\Admin\AdminServiceContainer;

require_once trailingslashit(plugin_dir_path(__FILE__)) . 'activation.php';
require_once trailingslashit(plugin_dir_path(__FILE__)) . 'bootstrap.php';

// The ID of the product. Used for renewals
$sby_download_id = 762236; // 762236, 762320, 762322

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
if ( ! defined( 'SBY_STORE_URL' ) ) {
    define( 'SBY_STORE_URL', 'https://smashballoon.com/' );
}
if ( ! defined( 'SBY_PLUGIN_EDD_NAME' ) ) {
    define( 'SBY_PLUGIN_EDD_NAME', 'YouTube Feed Pro Personal' );
}
if ( ! defined( 'SBYVER' ) ) {
    define( 'SBYVER', '2.4.0' );
}
if ( ! defined( 'SBY_DBVERSION' ) ) {
    define( 'SBY_DBVERSION', '2.3' );
}

if ( ! defined( 'SBY_BUILDER_DIR' ) ) {
    define( 'SBY_BUILDER_DIR', SBY_PLUGIN_DIR . 'admin/builder/' );
}
if ( ! defined( 'SBY_BUILDER_URL' ) ) {
    define( 'SBY_BUILDER_URL', SBY_PLUGIN_URL . 'admin/builder/' );
}
if ( ! defined( 'SBY_API_URL' ) ) {
	define( 'SBY_API_URL', 'https://reviews.smashballoon.com/api/v1.0/' );
}

// Setup customizer
$customizer_container = Container::getInstance();
$customizer_container->set( PreviewProvider::class, new ShortcodePreviewProvider() );
$customizer_container->set( \Smashballoon\Customizer\DB::class, new \SmashBalloon\YouTubeFeed\Customizer\DB() );
$customizer_container->set( \Smashballoon\Customizer\Config::class, new \SmashBalloon\YouTubeFeed\Customizer\Config() );
$customizer_container->set( \Smashballoon\Customizer\ProxyProvider::class, new \SmashBalloon\YouTubeFeed\Customizer\ProxyProvider() );

if ( ! function_exists( 'sby_init' ) ) {

    /**
     * Define constants and load plugin files
     *
     * @since  2.0
     */
    function sby_init() {
        // Plugin Base Name
        if ( ! defined( 'SBY_PLUGIN_BASENAME' ) ) {
            define( 'SBY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        }
        // Cron Updating Cache Time 60 days
        if ( ! defined( 'SBY_CRON_UPDATE_CACHE_TIME' ) ) {
            define( 'SBY_CRON_UPDATE_CACHE_TIME', 60 * 60 * 24 * 60 );
        }
        // Plugin Base Name
        if ( ! defined( 'SBY_BACKUP_PREFIX' ) ) {
            define( 'SBY_BACKUP_PREFIX', '!' );
        }
        if ( ! defined( 'SBY_USE_BACKUP_PREFIX' ) ) {
            define( 'SBY_USE_BACKUP_PREFIX', '&' );
        }
        if ( ! defined( 'SBY_CHANNEL_CACHE_PREFIX' ) ) {
            define( 'SBY_CHANNEL_CACHE_PREFIX', '~sby_' );
        }
        // Max Records in Database for Image Resizing
        if ( ! defined( 'SBY_MAX_RECORDS' ) ) {
            define( 'SBY_MAX_RECORDS', 100 );
        }
        if ( ! defined( 'SBY_MAX_SINGLE_PAGE' ) ) {
            define( 'SBY_MAX_SINGLE_PAGE', 50 );
        }
        if ( ! defined( 'SBY_TEXT_DOMAIN' ) ) {
            define( 'SBY_TEXT_DOMAIN', 'feeds-for-youtube' );
        }
        if ( ! defined( 'SBY_SLUG' ) ) {
            define( 'SBY_SLUG', 'youtube-feed' );
        }
        if ( ! defined( 'SBY_SEARCH_SLUG' ) ) {
            define( 'SBY_SEARCH_SLUG', 'youtube-feed-search' );
        }
        if ( ! defined( 'SBY_SEARCH_NAME' ) ) {
            define( 'SBY_SEARCH_NAME', 'sbys' );
        }
        if ( ! defined( 'SBY_PLUGIN_NAME' ) ) {
            define( 'SBY_PLUGIN_NAME', __( 'Feeds for YouTube', 'feeds-for-youtube' ) );
        }
        if ( ! defined( 'SBY_INDEF_ART' ) ) {
            define( 'SBY_INDEF_ART', __( 'a', 'feeds-for-youtube' ) );
        }
        if ( ! defined( 'SBY_SOCIAL_NETWORK' ) ) {
            define( 'SBY_SOCIAL_NETWORK', __( 'YouTube', 'feeds-for-youtube' ) );
        }
        if ( ! defined( 'SBY_SETUP_URL' ) ) {
            define( 'SBY_SETUP_URL', 'https://smashballoon.com/youtube-feed/free' );
        }
        if ( ! defined( 'SBY_SUPPORT_URL' ) ) {
            define( 'SBY_SUPPORT_URL', 'https://smashballoon.com/youtube-feed/support' );
        }
        if ( ! defined( 'SBY_OAUTH_PROCESSOR_URL' ) ) {
            define( 'SBY_OAUTH_PROCESSOR_URL', 'https://smashballoon.com/youtube-login/?return_uri=' );
        }
        if ( ! defined( 'SBY_DEMO_URL' ) ) {
            define( 'SBY_DEMO_URL', 'https://smashballoon.com/youtube-feed/demo' );
        }
        if ( ! defined( 'SBY_PRO_LOGO' ) ) {
            define( 'SBY_PRO_LOGO', 'https://smashballoon.com/wp-content/themes/smashballoon/img/smash-balloon-logo-small.png' );
        }

        if ( ! defined( 'SBY_MENU_SLUG' ) ) {
            define( 'SBY_MENU_SLUG', 'sby-feed-builder' );
        }
        require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/sby-functions.php';

        $container = new \SmashBalloon\YouTubeFeed\Services\ServiceContainer();
        $container->register();
        global $sby_settings;
        $sby_settings = get_option( 'sby_settings', array() );
        $sby_settings = wp_parse_args( $sby_settings, sby_settings_defaults() );

    		$sby_blocks = new SBY_Blocks( \Smashballoon\Customizer\Feed_Builder::instance(), new Smashballoon\Customizer\DB );

        if ( $sby_blocks->allow_load() ) {
            $sby_blocks->load();
        }
        if ( is_admin() ) {
            require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/admin-functions.php';
            sby_admin_init();

            $admin_container = new AdminServiceContainer();
            $admin_container->register();

            if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0
                && version_compare( get_bloginfo( 'version' ), '4.6', '>' ) ) {
                require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/SBY_Notifications.php';
                $sby_notifications = new SBY_Notifications();
                $sby_notifications->init();

                require_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/SBY_New_User.php';
                $sby_new_user = new SBY_New_User();
                $sby_new_user->init();
            }
        }

        \SmashBalloon\YouTubeFeed\Container::getInstance()->get( SBY_Tracking::class );
        \Smashballoon\Customizer\Feed_Builder::instance();
        sby_builder_pro();

        // Include upgrader hooks
        $sby_upgrader = new \SmashBalloon\YouTubeFeed\Admin\SBY_Upgrader();
        $sby_upgrader->hooks();

        global $sby_posts_manager;
        $sby_posts_manager = new SBY_Posts_Manager( 'sby', get_option( 'sby_errors', array() ), get_option( 'sby_ajax_status', array(
            'tested'     => false,
            'successful' => false
        ) ) );
    }

    add_action( 'plugins_loaded', 'sby_init' );

    /**
     * Add the custom interval of 30 minutes for cron caching
     *
     * @param array $schedules current list of cron intervals
     *
     * @return array
     *
     * @since  2.0
     */
    function sby_cron_custom_interval( $schedules ) {
        $schedules['sby30mins'] = array(
            'interval' => 30 * 60,
            'display'  => __( 'Every 30 minutes', 'feeds-for-youtube' )
        );
        $schedules['sbyweekly'] = array(
            'interval' => 3600 * 24 * 7,
            'display'  => __( 'Weekly', 'feeds-for-youtube' )
        );

        return $schedules;
    }

    add_filter( 'cron_schedules', 'sby_cron_custom_interval' );

    /**
     * Create database tables, schedule cron events, initiate capabilities
     *
     * @param bool $network_wide is a multisite network activation
     *
     * @since  2.0 database tables and capabilties created
     * @since  1.0
     */
    function sby_activate( $network_wide ) {
        include_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/sby-functions.php';
        //Clear page caching plugins and autoptomize
        if ( is_callable( 'sby_clear_page_caches' ) ) {
            sby_clear_page_caches();
        }

        //Run cron twice daily when plugin is first activated for new users
        if ( ! wp_next_scheduled( 'sby_cron_job' ) ) {
            wp_schedule_event( time(), 'twicedaily', 'sby_cron_job' );
        }
        if ( ! wp_next_scheduled( 'sby_notification_update' ) ) {
            $timestamp    = strtotime( 'next monday' );
            $timestamp    = $timestamp + ( 3600 * 24 * 7 );
            $six_am_local = $timestamp + sby_get_utc_offset() + ( 6 * 60 * 60 );

            wp_schedule_event( $six_am_local, 'sbyweekly', 'sby_notification_update' );
        }
        $sby_settings = get_option( 'sby_settings', array() );
        if ( isset( $sby_settings['caching_type'] ) && $sby_settings['caching_type'] === 'background' ) {
            require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/SBY_Cron_Updater.php';
            SBY_Cron_Updater::start_cron_job( $sby_settings['cache_cron_interval'], $sby_settings['cache_cron_time'], $sby_settings['cache_cron_am_pm'] );
        }

        if ( is_multisite() && $network_wide && function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
            // Get all blogs in the network and activate plugin on each one
            $sites = get_sites();
            foreach ( $sites as $site ) {
                switch_to_blog( $site->blog_id );
                $upload     = wp_upload_dir();
                $upload_dir = $upload['basedir'];
                $upload_dir = trailingslashit( $upload_dir ) . SBY_UPLOADS_NAME;
                if ( ! file_exists( $upload_dir ) ) {
                    $created = wp_mkdir_p( $upload_dir );
                }
                restore_current_blog();
            }

        } else {
            $upload     = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_dir = trailingslashit( $upload_dir ) . SBY_UPLOADS_NAME;
            if ( ! file_exists( $upload_dir ) ) {
                $created = wp_mkdir_p( $upload_dir );
            }
        }

        global $wp_roles;
        $wp_roles->add_cap( 'administrator', 'manage_youtube_feed_options' );
        //sby_videos

        //Delete all transients
        global $wpdb;
        $table_name = $wpdb->prefix . "options";
        $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_sby\_%')
        " );
        $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_sby\_%')
        " );
        $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_&sby\_%')
        " );
        $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_&sby\_%')
        " );
        $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_\$sby\_%')
        " );
        $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_\$sby\_%')
        " );


        $sby_statuses_option = get_option( 'sby_statuses', array() );
		if( !isset( $sby_statuses_option['wizard_dismissed'] ) || $sby_statuses_option['wizard_dismissed'] === false){
			add_option('sby_plugin_do_activation_redirect', true);
		}
    }

    register_activation_hook( __FILE__, 'sby_activate' );


     /**
     * Activation redirect
     *
     * @since  2.2.4
     * 
     * @return bool|void
     */
    function sby_activation_plugin_redirect() {

		if( !sby_current_user_can( 'manage_youtube_feed_options' ) ){
			return false;
		}

		if (get_option('sby_plugin_do_activation_redirect', false)) {
			delete_option('sby_plugin_do_activation_redirect');
			wp_safe_redirect( admin_url( '/admin.php?page=youtube-feed-setup' )  );
			exit();
		}
	}

	add_action('admin_init', 'sby_activation_plugin_redirect');

    /**
     * Stop cron events when deactivated
     *
     * @since  1.0
     */
    function sby_deactivate() {
        wp_clear_scheduled_hook( 'sby_cron_job' );
        wp_clear_scheduled_hook( 'sby_notification_update' );
        wp_clear_scheduled_hook( 'sby_feed_update' );
        wp_clear_scheduled_hook( 'sby_usage_tracking_cron' );
    }

    register_deactivation_hook( __FILE__, 'sby_deactivate' );

    /**
     * Compares previous plugin version and updates database related
     * items as needed
     *
     * @since  2.0
     */
    function sby_check_for_db_updates() {

        $db_ver = get_option( 'sby_db_version', SBY_DBVERSION );

        if ( version_compare( $db_ver, '1.2', '<' ) ) {
            sby_add_caps();

            update_option( 'sby_db_version', '2.0' );
        }

        if ( version_compare( $db_ver, '1.3', '<' ) ) {

            if ( ! wp_next_scheduled( 'sby_notification_update' ) ) {
                $timestamp    = strtotime( 'next monday' );
                $timestamp    = $timestamp + ( 3600 * 24 * 7 );
                $six_am_local = $timestamp + sby_get_utc_offset() + ( 6 * 60 * 60 );

                wp_schedule_event( $six_am_local, 'sbyweekly', 'sby_notification_update' );
            }
        }
        

    }

    add_action( 'wp_loaded', 'sby_check_for_db_updates' );

    /**
     * Create database tables for sub-site if multisite
     *
     * @param int $blog_id
     * @param int $user_id
     * @param string $domain
     * @param string $path
     * @param string $site_id
     * @param array $meta
     *
     * @since  2.0
     */
    function sby_on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        if ( is_plugin_active_for_network( 'youtube-feed-pro/youtube-feed-pro.php' ) ) {
            switch_to_blog( $blog_id );
            restore_current_blog();
        }
    }

    add_action( 'wpmu_new_blog', 'sby_on_create_blog', 10, 6 );

    /**
     * Delete custom tables if not preserving settings
     *
     * @param array $tables tables to drop
     *
     * @return array
     *
     * @since  2.0
     */
    function sby_on_delete_blog( $tables ) {
        $options           = get_option( 'sby_settings' );
        $preserve_settings = $options['preserve_settings'];
        if ( $preserve_settings ) {
            return;
        }

        global $wpdb;
        $tables[] = $wpdb->prefix . 'sby_items';
        $tables[] = $wpdb->prefix . 'sby_items_feeds';

        return $tables;
    }

    add_filter( 'wpmu_drop_tables', 'sby_on_delete_blog' );

    function sby_settings_defaults() {
        $defaults = array(
            'connected_accounts'       => array(),
            'type'                     => 'channel',
            'channel'                  => '',
            'num'                      => 9,
            'nummobile'                => 9,
            'widthresp'                => true,
            'class'                    => '',
            'height'                   => '',
            'heightunit'               => '%',
            'disablemobile'            => false,
            'itemspacing'              => 5,
            'itemspacingunit'          => 'px',
            'background'               => '',
            'headercolor'              => '',
            'subscribecolor'           => '',
            'subscribehovercolor'      => '',
            'subscribetextcolor'       => '',
            'buttoncolor'              => '',
            'buttonhovercolor'         => '',
            'buttontextcolor'          => '',
            'layout'                   => 'grid',
            'feedtemplate'             => 'default',
            'playvideo'                => 'automatically',
            'sortby'                   => 'none',
            'imageres'                 => 'auto',
            'showheader'               => true,
            'headerstyle'              => 'standard',
            'customheadertext'         => __( 'We are on YouTube', 'feeds-for-youtube' ),
            'customheadersize'         => 'small',
            'customheadertextcolor'    => '',
            'showdescription'          => true,
            'showbutton'               => true,
            'headersize'               => 'small',
            'headeroutside'            => false,
            'showsubscribe'            => true,
            'buttontext'               => __( 'Load More...', 'feeds-for-youtube' ),
            'subscribetext'            => __( 'Subscribe', 'feeds-for-youtube' ),
            'caching_type'             => 'page',
            'cache_time'               => 1,
            'cache_time_unit'          => 'hours',
            'backup_cache_enabled'     => true,
            'resizeprocess'            => 'background',
            'disable_resize'           => true,
            'storage_process'          => 'background',
            'favor_local'              => false,
            'disable_js_image_loading' => false,
            'ajax_post_load'           => false,
            'ajaxtheme'                => false,
            'enqueue_css_in_shortcode' => false,
            'font_method'              => 'svg',
            'customtemplates'          => false,
            'cols'                     => 3,
            'colsmobile'               => 2,
            'playerratio'              => '9:16',
            'eagerload'                => false,
            'custom_css'               => '',
            'custom_js'                => '',
            'gdpr'                     => 'auto',
            'disablecdn'               => false,
            'allowcookies'             => false,

            // pro only
            'usecustomsearch'          => false,
            'headerchannel'            => '',
            'customsearch'             => '',
            'showpast'                 => true,
            'showlikes'                => true,
            'carouselcols'             => 3,
            'carouselcolsmobile'       => 2,
            'carouselarrows'           => true,
            'carouselpag'              => true,
            'carouselautoplay'         => false,
            'infoposition'             => 'below',
            'include'                  => array( 'icon' ),
            'hoverinclude'             => array( 'title' ),
            'descriptionlength'        => 150,
            'userelative'              => true,
            'dateformat'               => '0',
            'customdate'               => '',
            'showsubscribers'          => false,
            'enablelightbox'           => true,
            'subscriberstext'          => __( 'subscribers', 'feeds-for-youtube' ),
            'viewstext'                => __( 'views', 'feeds-for-youtube' ),
            'agotext'                  => __( 'ago', 'feeds-for-youtube' ),
            'beforedatetext'           => __( 'Streaming live', 'feeds-for-youtube' ),
            'beforestreamtimetext'     => __( 'Streaming live in', 'feeds-for-youtube' ),
            'minutetext'               => __( 'minute', 'feeds-for-youtube' ),
            'minutestext'              => __( 'minutes', 'feeds-for-youtube' ),
            'hourstext'                => __( 'hours', 'feeds-for-youtube' ),
            'thousandstext'            => __( 'K', 'feeds-for-youtube' ),
            'millionstext'             => __( 'M', 'feeds-for-youtube' ),
            'watchnowtext'             => __( 'Watch Now', 'feeds-for-youtube' ),
            'cta'                      => 'related',
            'colorpalette'             => 'inherit',
            'linktext'                 => __( 'Learn More', 'feeds-for-youtube' ),
            'linkurl'                  => '',
            'linkopentype'             => 'same',
            'linkcolor'                => '',
            'linktextcolor'            => '',
            'videocardstyle'           => 'regular',
            'videocardlayout'          => 'vertical',
            'custombgcolor1'           => '',
            'customtextcolor1'         => '',
            'customtextcolor2'         => '',
            'customlinkcolor1'         => '',
            'custombuttoncolor1'       => '',
            'custombuttoncolor2'       => '',
            'boxedbgcolor'             => '#ffffff',
            'boxborderradius'			=> '12',
            'enableboxshadow'			=> false,
            'descriptiontextsize'		=> '13px',
            'subscribelinkcolorbg'		=> '',
            'subscribebtnprimarycolor'	=> '',
            'subscribebtnsecondarycolor'=> '',
            'subscribebtntextcolor'		=> '',

            // Video elements color
            'playiconcolor'				=> '',
            'videotitlecolor'			=> '',
            'videouserecolor'			=> '',
            'videoviewsecolor'			=> '',
            'videocountdowncolor'		=> '',
            'videostatscolor'			=> '',
            'videodescriptioncolor'		=> '',
            'enablesubscriberlink'		=> true,

            //cron
			'cache_cron_interval'		=> '1hour',
			'cache_cron_time'			=> '1:00',
			'cache_cron_am_pm'			=> 'am',
			'disable_wp_posts'			=> false,
        );

        return $defaults;
    }

    // Add a Settings link to the plugin on the Plugins page
    $plugin_file = 'feeds-for-youtube/youtube-feed.php';
    add_filter( "plugin_action_links_{$plugin_file}", 'sby_add_settings_link', 10, 2 );
    function sby_add_settings_link( $links, $file ) {
        $pro_link          = '<a href="https://smashballoon.com/youtube-feed/demo/?utm_campaign=youtube-free&utm_source=plugins-page&utm_medium=upgrade-link" target="_blank" style="font-weight: bold; color: #1da867;">' . __( 'Try the Pro Demo', 'feeds-for-youtube' ) . '</a>';
        $sby_settings_link = '<a href="' . admin_url( 'admin.php?page=sby-feed-builder' ) . '">' . __( 'Settings', 'feeds-for-youtube' ) . '</a>';
        array_unshift( $links, $pro_link, $sby_settings_link );

        return $links;
    }

    function sby_text_domain() {
        load_plugin_textdomain( 'feeds-for-youtube', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
    }

    add_action( 'init', 'sby_text_domain' );

    //BUILDER CODE
    function sby_builder_pro() {
        return \Smashballoon\Customizer\Feed_Builder::instance();
    }
}
