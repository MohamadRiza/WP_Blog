<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Oembed;
use CustomFacebookFeed\CFF_GDPR_Integrations;
use CustomFacebookFeed\CFF_Feed_Locator;
use CustomFacebookFeed\SB_Facebook_Data_Manager;

add_action('group_post_scheduler_cron', 'cff_group_cache_function');
function cff_group_cache_function(){
    CustomFacebookFeed\CFF_Group_Posts::cron_update_group_persistent_cache();
}

//Create Style page
/**
 * @deprecated
 */
function cff_style_page() {} //End Style_Page

 //Create Settings page
/**
 * @deprecated
 */
function cff_settings_page() {} //End Settings_Page

/**
 * @deprecated
 */
function cff_oembeds_page() {}

/**
 * @deprecated
 */
function cff_social_wall_page() {}

function cff_lite_dismiss() {
	check_ajax_referer( 'cff_nonce' , 'cff_nonce');

	$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
	$cap = apply_filters( 'cff_settings_pages_capability', $cap );
	if ( ! current_user_can( $cap ) ) {
		wp_send_json_error(); // This auto-dies.
	}

	set_transient( 'facebook_feed_dismiss_lite', 'dismiss', 1 * WEEK_IN_SECONDS );

	die();
}
add_action( 'wp_ajax_cff_lite_dismiss', 'cff_lite_dismiss' );

//If PPCA notice is dismissed then don't show again
add_action('admin_init', 'cff_nag_ppca_ignore');
function cff_nag_ppca_ignore() {
global $current_user;
	$cap = current_user_can('manage_custom_facebook_feed_options') ? 'manage_custom_facebook_feed_options' : 'manage_options';
	$cap = apply_filters('cff_settings_pages_capability', $cap);
	if (!current_user_can($cap)) {
		return;
	}

	$user_id = $current_user->ID;
	if (isset($_GET['cff_nag_ppca_ignore']) && '0' == $_GET['cff_nag_ppca_ignore']) {
		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'cff_nonce')) {
			add_user_meta($user_id, 'cff_ignore_ppca_notice', 'true', true);
		}
	}
}


// Add a Settings link to the plugin on the Plugins page
$cff_plugin_file = 'custom-facebook-feed/custom-facebook-feed.php';
add_filter( "plugin_action_links_{$cff_plugin_file}", 'cff_add_settings_link', 10, 2 );

//modify the link by unshifting the array
function cff_add_settings_link( $links, $file ) {
	$pro_link = '<a href="https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=plugins-page&utm_medium=upgrade-link" target="_blank" style="font-weight: bold; color: #1da867;">' . __( 'Try the Pro Demo', 'custom-facebook-feed' ) . '</a>';
    $cff_settings_link = '<a href="' . admin_url( 'admin.php?page=cff-feed-builder' ) . '">' . __( 'Settings', 'cff-feed-builder', 'custom-facebook-feed' ) . '</a>';
    array_unshift( $links, $pro_link, $cff_settings_link );

    return $links;
}


//Delete cache
function cff_delete_cache(){
    global $wpdb;
    $table_name = $wpdb->prefix . "options";
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_%')
        " );
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_tle\_%')
        " );
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_cff\_%')
        " );

    //Clear cache of major caching plugins
    if(isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){
        $GLOBALS['wp_fastest_cache']->deleteCache();
    }
    //WP Super Cache
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
    //W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }
    if (function_exists('sg_cachepress_purge_cache')) {
        sg_cachepress_purge_cache();
    }

    // Litespeed Cache
    if ( method_exists( 'LiteSpeed_Cache_API', 'purge' ) ) {
        LiteSpeed_Cache_API::purge( 'esi.custom-facebook-feed' );
    }

    \CustomFacebookFeed\Admin\CFF_Support_Tool::delete_expired_users();

}

//Cron job to clear transients
add_action('cff_cron_job', 'cff_cron_clear_cache');
function cff_cron_clear_cache() {
    //Delete all transients
    cff_delete_cache();
}

//NOTICES
function cff_get_current_time() {
    $current_time = time();

    // where to do tests
    // $current_time = strtotime( 'November 25, 2020' );

    return $current_time;
}

// generates the html for the admin notices
function cff_notices_html() {
    // reset everything for testing
    /*
    global $current_user;
    $user_id = $current_user->ID;
    // delete_user_meta( $user_id, 'cff_ignore_bfcm_sale_notice' );
    // delete_user_meta( $user_id, 'cff_ignore_new_user_sale_notice' );
    // $cff_statuses_option = array( 'first_install' => strtotime( 'December 8, 2017' ) );
    // $cff_statuses_option = array( 'first_install' => time() );

    // update_option( 'cff_statuses', $cff_statuses_option, false );
    // delete_option( 'cff_rating_notice');
    // delete_transient( 'custom_facebook_rating_notice_waiting' );

    // set_transient( 'custom_facebook_rating_notice_waiting', 'waiting', 2 * WEEK_IN_SECONDS );
    delete_transient('custom_facebook_rating_notice_waiting');
    update_option( 'cff_rating_notice', 'pending', false );
    */
}

function cff_get_future_date( $month, $year, $week, $day, $direction ) {
    if ( $direction > 0 ) {
        $startday = 1;
    } else {
        $startday = date( 't', mktime(0, 0, 0, $month, 1, $year ) );
    }

    $start = mktime( 0, 0, 0, $month, $startday, $year );
    $weekday = date( 'N', $start );

    $offset = 0;
    if ( $direction * $day >= $direction * $weekday ) {
        $offset = -$direction * 7;
    }

    $offset += $direction * ($week * 7) + ($day - $weekday);
    return mktime( 0, 0, 0, $month, $startday + $offset, $year );
}

function cff_admin_hide_unrelated_notices() {

	// Bail if we're not on a cff screen or page.
	if ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'cff') === false ) {
		return;
	}

	// Extra banned classes and callbacks from third-party plugins.
	$blacklist = array(
		'classes'   => array(),
		'callbacks' => array(
			'cffdb_admin_notice', // 'Database for cff' plugin.
		),
	);

	global $wp_filter;

	foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
		if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
			continue;
		}
		foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
					continue;
				}
				$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
				if (
					! empty( $class ) &&
					strpos( $class, 'cff' ) !== false &&
					! in_array( $class, $blacklist['classes'], true )
				) {
					continue;
				}
				if (
					! empty( $name ) && (
						strpos( $name, 'cff' ) === false ||
						in_array( $class, $blacklist['classes'], true ) ||
						in_array( $name, $blacklist['callbacks'], true )
					)
				) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}
add_action( 'admin_print_scripts', 'cff_admin_hide_unrelated_notices' );

/**
 * Remove admin notices from inside our plugin screens so we can show our customized notices
 *
 * @since 4.0
 */
add_action( 'in_admin_header', 'cff_remove_admin_notices' );
function cff_remove_admin_notices() {
    $current_screen = get_current_screen();
    $not_allowed_screens = array(
        'facebook-feed_page_cff-feed-builder',
        'facebook-feed_page_cff-settings',
        'facebook-feed_page_cff-oembeds-manager',
        'facebook-feed_page_cff-extensions-manager',
        'facebook-feed_page_cff-about-us',
        'facebook-feed_page_cff-support',
    );

    if ( in_array( $current_screen->base, $not_allowed_screens ) || strpos( $current_screen->base, 'cff-' ) !== false ) {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
}

function cff_free_add_caps() {
	global $wp_roles;

	$wp_roles->add_cap( 'administrator', 'manage_custom_facebook_feed_options' );

}
add_action( 'admin_init', 'cff_free_add_caps', 90 );





function cff_oembed_disable() {
    check_ajax_referer( 'cff_nonce' , 'cff_nonce');

	$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
	$cap = apply_filters( 'cff_settings_pages_capability', $cap );
	if ( ! current_user_can( $cap ) ) {
		wp_send_json_error(); // This auto-dies.
	}

	$oembed_settings = get_option( 'cff_oembed_token', array() );
	$oembed_settings['access_token'] = '';
	$oembed_settings['disabled'] = true;
	echo '<strong>';
	if ( update_option( 'cff_oembed_token', $oembed_settings ) ) {
		_e( 'Facebook oEmbeds will no longer be handled by Custom Facebook Feed.', 'custom-facebook-feed' );
	} else {
		_e( 'An error occurred when trying to disable your oEmbed token.', 'custom-facebook-feed' );
	}
	echo '</strong>';

	die();
}
add_action( 'wp_ajax_cff_oembed_disable', 'cff_oembed_disable' );



function cff_custom_cssjs_notice() {
    $cff_statuses_option = get_option( 'cff_statuses', array() );
    if ( ! empty( $cff_statuses_option['custom_js_css_dismissed'] ) ) {
        return;
    }

    if ( ! empty( $_GET['cff_dismiss_notice'] ) && $_GET['cff_dismiss_notice'] === 'customjscss' ) {
        $cff_statuses_option['custom_js_css_dismissed'] = true;
        update_option( 'cff_statuses', $cff_statuses_option, false );
        return;
    }
    $cff_style_settings 					= get_option( 'cff_style_settings' );

    $custom_js_not_empty = ! empty( $cff_style_settings['cff_custom_js'] ) && trim($cff_style_settings['cff_custom_js']) !== '';
    $custom_css_not_empty = ! empty( $cff_style_settings['cff_custom_css_read_only'] ) && trim($cff_style_settings['cff_custom_css_read_only']) !== '';

    if ( ! $custom_js_not_empty && ! $custom_css_not_empty ) {
        return;
    }

	$cff_notifications = new \CustomFacebookFeed\Admin\CFF_Notifications();
	$notifications = $cff_notifications->get();

	if ( ! empty( $notifications ) && ( ! empty( $_GET['page'] ) && strpos( $_GET['page'], 'cff-' ) !== false ) ) {
	   return;
	}
    $close_href = add_query_arg( array( 'cff_dismiss_notice' => 'customjscss' ) );

    ?>
    <div class="notice notice-warning is-dismissible cff-dismissible">
        <p><?php if ( $custom_js_not_empty ) : ?>
        <?php echo sprintf( __( 'You are currently using Custom CSS or JavaScript in the Custom Facebook Feed plugin, however, these settings have now been deprecated. To continue using any custom code, please go to the Custom CSS and JS settings %shere%s and follow the directions.', 'custom-facebook-feed' ), '<a href="' . admin_url( 'admin.php?page=cff-settings&view=feeds' ) . '">', '</a>' ); ?>
        <?php else : ?>
        <?php echo sprintf( __( 'You are currently using Custom CSS in the Custom Facebook Feed plugin, however, this setting has now been deprecated. Your CSS has been moved to the "Additional CSS" field in the WordPress Customizer %shere%s instead.', 'custom-facebook-feed' ), '<a href="' . esc_url( wp_customize_url() ) . '">', '</a>' ); ?>
        <?php endif; ?>
        &nbsp;<a href="<?php echo esc_attr( $close_href ); ?>"><?php echo __( 'Dismiss', 'custom-facebook-feed' ); ?></a>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'cff_custom_cssjs_notice' );
add_action( 'cff_admin_notices', 'cff_custom_cssjs_notice' );

function cff_dismiss_custom_cssjs_notice() {
    check_ajax_referer( 'cff_nonce' , 'cff_nonce');

    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    $cap = apply_filters( 'cff_settings_pages_capability', $cap );
    //Only display notice to admins
    if ( !current_user_can( $cap ) ) return;

    $cff_statuses_option = get_option( 'cff_statuses', array() );
    $cff_statuses_option['custom_js_css_dismissed'] = true;
    update_option( 'cff_statuses', $cff_statuses_option, false );
}
add_action( 'wp_ajax_cff_dismiss_custom_cssjs_notice', 'cff_dismiss_custom_cssjs_notice' );


function cff_ppca_token_check_flag() {
	check_ajax_referer( 'cff_nonce' , 'cff_nonce');

	$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
	$cap = apply_filters( 'cff_settings_pages_capability', $cap );
	if ( ! current_user_can( $cap ) ) {
		wp_send_json_error(); // This auto-dies.
	}

    if( get_transient('cff_ppca_access_token_invalid') ){
        print_r(true);
    } else {
        print_r(false);
    }

    die();
}
add_action( 'wp_ajax_cff_ppca_token_check_flag', 'cff_ppca_token_check_flag' );

/**
 * Adds CSS to the end of the customizer "Additonal CSS" setting
 *
 * @param $custom_css
 *
 * @return bool|int
 *
 * @since 4.0.2/4.0.7
 */
function cff_transfer_css( $custom_css ) {
    $value   = '';
    $post    = wp_get_custom_css_post( get_stylesheet() );
    if ( $post ) {
        $value = $post->post_content;
    }
    $value .= "\n\n/* Custom Facebook Feed */\n" . $custom_css . "\n/* Custom Facebook Feed - End */";

    $r = wp_update_custom_css_post(
        $value,
        array(
            'stylesheet' => get_stylesheet(),
        )
    );

    if ( $r instanceof WP_Error ) {
        return false;
    }
    $post_id = $r->ID;

    return $post_id;
}

/**
 * Validates CSS to detect anything that might be harmful
 *
 * @param $css
 *
 * @return bool|WP_Error
 *
 * @since 4.0.2/4.0.7
 */
function cff_validate_css( $css ) {
    $validity = new WP_Error();

    if ( preg_match( '#</?\w+#', $css ) ) {
        $validity->add( 'illegal_markup', __( 'Markup is not allowed in CSS.' ) );
    }

    if ( ! $validity->has_errors() ) {
        $validity = true;
    }
    return $validity;
}

/**
 * Check to see if CSS has been transferred
 *
 * @since 4.0.2/4.0.7
 */
function cff_check_custom_css() {
    $cff_style_settings = get_option( 'cff_style_settings', array() );
    $custom_css = isset( $cff_style_settings['cff_custom_css'] ) ? stripslashes( trim( $cff_style_settings['cff_custom_css'] ) ) : '';

    // only try once
    if ( empty( $custom_css ) ) {
        return;
    }

    // custom css set to nothing after trying the update once
    $cff_style_settings['cff_custom_css_read_only'] = $custom_css;
    $cff_style_settings['cff_custom_css'] = '';
    update_option( 'cff_style_settings', $cff_style_settings );
    if ( ! function_exists( 'wp_get_custom_css_post' )
        || ! function_exists( 'wp_update_custom_css_post' ) ) {
        return;
    }

    // make sure this is valid CSS or don't transfer
    if ( is_wp_error( cff_validate_css( $custom_css ) ) ) {
        return;
    }

    cff_transfer_css( $custom_css );
}
add_action( 'init', 'cff_check_custom_css' );

function cff_doing_openssl() {
	return extension_loaded( 'openssl' );
}
function cff_delete_all_platform_data(){
	$manager = new SB_Facebook_Data_Manager();
	$manager->delete_caches();
	\cff_main()->cff_error_reporter->add_action_log( 'Deleted all platform data.' );
	\cff_main()->cff_error_reporter->reset_api_errors();
}


function cff_encrypt_decrypt($action, $string)
{
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'SMA$H.BA[[OON#23121';
    $secret_iv = '1231394873342102221';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action === 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action === 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function update_connected_accounts($connected_accounts)
{
    update_option('cff_connected_accounts', $connected_accounts);
    return $connected_accounts;
}

/**
 * Return a combination of legacy and new sources with new sources
 * overriding legacy sources.
 *
 * @return StdClass
 *
 * @since 4.0
 */
function get_connected_accounts_list()
{
    $connected_accounts = CustomFacebookFeed\CFF_Utils::cff_get_connected_accounts_object();

    if (empty($connected_accounts)) {
        $connected_accounts = [];
    }

    $new_sources = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_source_list();

    $encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

    foreach ($new_sources as $new_source) {
        if (!empty($new_source['account_id'])) {
            $account_id = $new_source['account_id'];
            array_push(
                $connected_accounts,
                [
                    'id' => $account_id,
                    'accesstoken' => $encryption->decrypt($new_source['access_token']) ? $encryption->decrypt($new_source['access_token']) : $new_source['access_token'],
                    'pagetype' => $new_source['account_type'],
                    'name' => $new_source['username'],
                    'avatar' => $new_source['avatar_url']
                ]
            );
        }

    }

    return $connected_accounts;
}


/**
 * oEmbed Connection App URL
 *
 * @return StdClass
 *
 * @since 4.0
 */
function cffGetOembedConnectionUrl()
{
	$admin_url_state = admin_url('admin.php?page=cff-oembeds-manager');
	$nonce           = wp_create_nonce('cff_con');
	// If the admin_url isn't returned correctly then use a fallback
	if ($admin_url_state == '/wp-admin/admin.php?page=cff-oembeds-manager') {
		$admin_url_state = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	}

	return array(
		'connect' => CFF_OEMBED_CONNECT_URL,
		'cff_con' => $nonce,
		'stateURL' => $admin_url_state
	);
}