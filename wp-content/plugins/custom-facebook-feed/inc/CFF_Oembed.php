<?php
/**
 * Class CFF_Oembed
 *
 * Replaces the native WordPress functionality for Facebook oembed
 * to allow authenticated oembeds
 *
 * @since 2.16/3.16
 */

namespace CustomFacebookFeed;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class CFF_Oembed
{
	/**
	 * CFF_Oembed constructor.
	 *
	 * If an account has been connected, hooks are added
	 * to change how Facebook links are handled for oembeds
	 *
	 * @since 2.16/3.16
	 */
	public function __construct() {
		if ( CFF_Oembed::can_do_oembed() ) {
			if ( CFF_Oembed::can_check_for_old_oembeds() ) {
				add_action('init', array('CustomFacebookFeed\CFF_Oembed', 'clear_checks'));
				add_action('admin_init', array($this, 'cffOembedNotice'));
			}
			add_filter( 'oembed_providers', array( 'CustomFacebookFeed\CFF_Oembed', 'oembed_providers' ), 10, 1 );
			add_filter( 'oembed_fetch_url', array( 'CustomFacebookFeed\CFF_Oembed', 'oembed_set_fetch_url' ), 10, 3 );
			add_filter( 'oembed_result', array( 'CustomFacebookFeed\CFF_Oembed', 'oembed_result' ), 10, 3 );
		}
		if ( CFF_Oembed::should_extend_ttl() ) {
			add_filter( 'oembed_ttl', array( 'CustomFacebookFeed\CFF_Oembed', 'oembed_ttl' ), 10, 4 );
		}
	}

	/**
	 * Check to make sure there is a saved access token to
	 * enable authenticated oembeds
	 *
	 * @return bool
	 *
	 * @since 2.16/3.16
	 */
	public static function can_do_oembed() {
		$oembed_token_settings = get_option( 'cff_oembed_token', array() );

		if (isset($oembed_token_settings['disabled']) && $oembed_token_settings['disabled'] === true) {
			return false;
		}

		$access_token = CFF_Oembed::last_access_token();
		if ( ! $access_token ) {
			return false;
		}

		return true;
	}

	/**
	 * The "time to live" for Instagram oEmbeds is extended if the access token expires.
	 * Even if new oEmbeds will not use the Instagram Feed system due to an expired token
	 * the time to live should continue to be extended.
	 *
	 * @return bool
	 *
	 * @since 2.16/3.16
	 */
	public static function should_extend_ttl() {
		$oembed_token_settings = get_option( 'cff_oembed_token', array() );

		if ( isset( $oembed_token_settings['disabled'] ) && $oembed_token_settings['disabled'] ) {
			return false;
		}

		$will_expire = CFF_Oembed::oembed_access_token_will_expire();
		if ( $will_expire ) {
			return true;
		}

		return false;
	}

	/**
	 * Checking for old oembeds makes permanent changes to posts
	 * so we want the user to turn it off and on
	 *
	 * @return bool
	 *
	 * @since 2.16/3.16
	 */
	public static function can_check_for_old_oembeds() {
		$cff_statuses = get_option('cff_statuses', array());
		if (isset($cff_statuses['oembed_api_change_notice'])) {
			return false;
		}
		return true;
	}

	/**
	 * Filters the WordPress list of oembed providers to
	 * change what url is used for remote requests for the
	 * oembed data
	 *
	 * @param array $providers
	 *
	 * @return mixed
	 *
	 * @since 2.16/3.16
	 */
	public static function oembed_providers( $providers ) {
		$oembed_url = CFF_Oembed::oembed_url();
		if ( $oembed_url ) {
			$post_embed_providers = CFF_Oembed::post_providers();
			foreach ( $post_embed_providers as $post_provider ) {
				$providers[ $post_provider ] = array( $oembed_url . 'oembed_post', true );
			}

			$video_embed_providers = CFF_Oembed::video_providers();
			foreach ( $video_embed_providers as $video_provider ) {
				$providers[ $video_provider ] = array( $oembed_url . 'oembed_video', true );
			}
		}

		return $providers;
	}

	/**
	 * Add the access token from a connected account to make an authenticated
	 * call to get oembed data from Facebook
	 *
	 * @param string $provider
	 * @param string $url
	 * @param array $args
	 *
	 * @return string
	 *
	 * @since 2.16/3.16
	 */
	public static function oembed_set_fetch_url( $provider, $url, $args ) {
		$access_token = CFF_Oembed::last_access_token();
		if ( ! $access_token ) {
			return $provider;
		}

		if ( strpos( $provider, 'oembed_post' ) !== false
		     || strpos( $provider, 'oembed_video' ) !== false ) {

			if ( strpos( $url, '?' ) !== false ) {
				$provider = self::get_provider_from_url_with_query_vars( $provider, $url );
			}
			$provider = add_query_arg( 'access_token', $access_token, $provider );
		}

		return $provider;
	}

	/**
	 * URLs with query variables are handled specially
	 *
	 * @param $provider
	 * @param $url
	 *
	 * @return array|mixed|string|string[]
	 */
	public static function get_provider_from_url_with_query_vars( $provider, $url ) {
		$exploded = explode( '?', $url );
		if ( ! empty( $exploded[1] ) ) {
			if ( strpos( $url, '?v=' ) !== false ) {
				$exploded = explode( '&', $url );
				$provider = str_replace( urlencode( '&' . $exploded[1] ), '', $provider );
			}
		}

		return $provider;
	}

	/**
	 * New oembeds are wrapped in a div for easy detection of older oembeds
	 * that will need to be updated
	 *
	 * @param string $html
	 * @param string $url
	 * @param array $args
	 *
	 * @return string
	 *
	 * @since 2.16/3.16
	 */
	public static function oembed_result( $html, $url, $args ) {
		$post_embed_providers = CFF_Oembed::post_providers();
		foreach ( $post_embed_providers as $post_provider ) {
			if ( preg_match( $post_provider, $url ) === 1 ) {
				if ( strpos( $html, 'class="fb-post"' ) !== false ) {
					$html = '<div class="cff-embed-wrap cff-post-embed-wrap">' . str_replace( 'class="fb-post"', 'class="fb-post cff-embed cff-post-embed"', $html ) . '</div>';
				}
			}
		}

		$video_embed_providers = CFF_Oembed::video_providers();
		foreach ( $video_embed_providers as $video_provider ) {
			if ( preg_match( $video_provider, $url ) === 1 ) {
				if ( strpos( $html, 'class="fb-video"' ) !== false ) {
					$html = '<div class="cff-embed-wrap cff-video-embed-wrap">' . str_replace( 'class="fb-video"', 'class="fb-video cff-embed cff-video-embed"', $html ) . '</div>';
				}
			}
		}

		return $html;
	}

	/**
	 * Extend the "time to live" for oEmbeds created with access tokens that expire
	 *
	 * @param $ttl
	 * @param $url
	 * @param $attr
	 * @param $post_ID
	 *
	 * @return float|int
	 *
	 * @since 2.16/3.16
	 */
	public static function oembed_ttl( $ttl, $url, $attr, $post_ID ) {
		$providers = CFF_Oembed::post_providers();
		foreach ( $providers as $provider ) {
			if ( preg_match( $provider, $url ) === 1 ) {
				$ttl = 30 * YEAR_IN_SECONDS;
			}
		}

		$providers = CFF_Oembed::video_providers();
		foreach ( $providers as $provider ) {
			if ( preg_match( $provider, $url ) === 1 ) {
				$ttl = 30 * YEAR_IN_SECONDS;
			}
		}

		return $ttl;
	}

	/**
	 * Only one api URL for FB
	 *
	 * @return bool|string
	 *
	 * @since 2.16/3.16
	 */
	public static function oembed_url() {
		return 'https://graph.facebook.com/';
	}

	/**
	 * Any access token will work for oembeds so the access token
	 * saved in settings is used
	 *
	 * @return bool|string
	 *
	 * @since 2.16/3.16
	 */
	public static function last_access_token() {
		$oembed_token_settings = get_option( 'cff_oembed_token', array() );
		$will_expire = CFF_Oembed::oembed_access_token_will_expire();
		$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

		if ( ! empty( $oembed_token_settings['access_token'] )
		     && (! $will_expire || $will_expire > time()) ) {
			$oembed_token_settings['access_token'] = $encryption->maybe_decrypt( $oembed_token_settings['access_token'] );

			return str_replace(":", ":02Sb981f26534g75h091287a46p5l63", $oembed_token_settings['access_token']);
		} else {
			$settings_access_token = trim(get_option('cff_access_token'));
			$settings_access_token = $encryption->maybe_decrypt( $settings_access_token );
			if ( ! empty( $settings_access_token ) ) {
				return str_replace(":", ":02Sb981f26534g75h091287a46p5l63", $settings_access_token);
			}

			if ( class_exists( 'SB_Instagram_Oembed' ) ) {
				$sbi_oembed_token_settings = get_option( 'sbi_oembed_token', array() );
				if ( ! empty( $sbi_oembed_token_settings['access_token'] ) ) {
					$sbi_oembed_token_settings['access_token'] = $encryption->maybe_decrypt( $sbi_oembed_token_settings['access_token'] );
					return $sbi_oembed_token_settings['access_token'];
				}
			}
		}

		return false;
	}

	/**
	 * Access tokens created from FB accounts not connected to an
	 * FB page expire after 60 days.
	 *
	 * @return bool|int
	 */
	public static function oembed_access_token_will_expire() {
		$oembed_token_settings = get_option( 'cff_oembed_token', array() );
		$will_expire = isset( $oembed_token_settings['expiration_date'] ) && (int)$oembed_token_settings['expiration_date'] > 0 ? (int)$oembed_token_settings['expiration_date'] : false;

		return $will_expire;
	}


	/**
	 * Loop through post meta data and if it's an oembed and has content
	 * that looks like a Facebook oembed, delete it
	 *
	 * @param $post_ID
	 *
	 * @return int number of old oembed caches found
	 *
	 * @since 2.16/3.16
	 */
	public static function delete_facebook_oembed_caches( $post_ID ) {
		$post_metas = get_post_meta( $post_ID );
		if ( empty( $post_metas ) ) {
			return 0;
		}

		$total_found = 0;
		foreach ( $post_metas as $post_meta_key => $post_meta_value ) {
			if ( '_oembed_' === substr( $post_meta_key, 0, 8 ) ) {
				if ( strpos( $post_meta_value[0], 'class="fb-post"' ) !== false
				     && strpos( $post_meta_value[0], 'cff-embed-wrap' ) === false ) {
					$total_found++;
					delete_post_meta( $post_ID, $post_meta_key );
					if ( '_oembed_time_' !== substr( $post_meta_key, 0, 13 ) ) {
						delete_post_meta( $post_ID, str_replace( '_oembed_', '_oembed_time_', $post_meta_key ) );
					}
				} elseif ( strpos( $post_meta_value[0], 'class="fb-video"' ) !== false
				           && strpos( $post_meta_value[0], 'cff-embed-wrap' ) === false ) {
					$total_found++;
					delete_post_meta( $post_ID, $post_meta_key );
					if ( '_oembed_time_' !== substr( $post_meta_key, 0, 13 ) ) {
						delete_post_meta( $post_ID, str_replace( '_oembed_', '_oembed_time_', $post_meta_key ) );
					}
				}
			}
		}

		return $total_found;
	}

	/**
	 * Current list of regex to identify FB URLs that could become oembeds using
	 * the 'oembed_post' endpoint.
	 *
	 * @return array
	 *
	 * @since 2.16/3.16
	 */
	public static function post_providers() {
		$post_embed_providers = array(
			'#https?://www\.facebook\.com/.*/posts/.*#i',
			'#https?://www\.facebook\.com/.*/activity/.*#i',
			'#https?://www\.facebook\.com/.*/photos/.*#i',
			'#https?://www\.facebook\.com/photo(s/|\.php).*#i',
			'#https?://www\.facebook\.com/permalink\.php.*#i',
			'#https?://www\.facebook\.com/media/.*#i',
			'#https?://www\.facebook\.com/questions/.*#i',
			'#https?://www\.facebook\.com/notes/.*#i',
		);

		return $post_embed_providers;
	}

	/**
	 * Current list of regex to identify FB URLs that could become oembeds using
	 * the 'oembed_video' endpoint.
	 *
	 * @return array
	 *
	 * @since 2.16/3.16
	 */
	public static function video_providers() {
		$video_embed_providers = array(
			'#https?://www\.facebook\.com/.*/videos/.*#i',
			'#https?://www\.facebook\.com/video\.php.*#i',
			'#https?://www\.facebook\.com/watch/.*#i',
			'#https?://fb\.watch/.*#i'
		);

		return $video_embed_providers;
	}

	/**
	 * Used for clearing the oembed update check flag for all posts
	 *
	 * @since 2.16/3.16
	 */
	public static function clear_checks() {
		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . "postmeta" );
		$result = $wpdb->query("
		    DELETE
		    FROM $table_name
		    WHERE meta_key = '_cff_oembed_done_checking';");
	}

	/**
	 * Display oembed notice in the plugin's pages
	 *
	 * @since 6.3.7
	 */
	public function cffOembedNotice()
	{
		$allowed_screens = array(
			'cff-feed-builder',
			'cff-settings',
			'cff-oembeds-manager',
			'cff-extensions-manager',
			'cff-about-us',
			'cff-support',
		);
		$current_screen  = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
		$is_allowed      = in_array($current_screen, $allowed_screens);

		// We will display the notice only on those allowed screens.
		if (!$current_screen || ! $is_allowed) {
			return;
		}

		// Only display notice to admins.
		$cap = current_user_can('manage_custom_facebook_feed_options') ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters('cff_settings_pages_capability', $cap);
		if (!current_user_can($cap)) {
			return;
		}

        $cff_statuses = get_option('cff_statuses', array());
		if (isset($cff_statuses['oembed_api_change_notice'])) {
			return;
		}

		global $cff_notices;
		$title    = __( 'Account reconnection needed for Facebook and Instagram oEmbeds', 'custom-facebook-feed' );
		$message  = '<p>' . __( 'Starting May of 2024, Facebook is making some changes to their API that will affect your oEmbeds. Make sure to connect to our oEmbed specific Smash Balloon Tools app to avoid disruption.', 'custom-facebook-feed' ) . '</p>';

		$error_args = array(
			'class'     => 'cff-admin-notices',
			'title'     => array(
				'text'  => $title,
				'class' => 'sb-notice-title',
				'tag'   => 'h4',
			),
			'message'     => $message,
			'buttons' => array(
				array(
					'text'      => __( 'Reconnect', 'custom-facebook-feed' ),
					'class'     => 'sb-btn sb-reconnect-oembed',
					'tag'       => 'button',
				),
			),
			'buttons_wrap_start' => '<div class="buttons">',
			'buttons_wrap_end'   => '</div>',
			'priority'    => 1,
			'page'        => array(
				'cff-feed-builder',
				'cff-settings',
				'cff-oembeds-manager',
				'cff-extensions-manager',
				'cff-about-us',
				'cff-support',
			),
			'icon' => array(
				'src'  => CFF_PLUGIN_URL . 'admin/assets/img/cff-exclamation.svg',
				'wrap' => '<span class="sb-notice-icon sb-error-icon"><img {src}></span>',
			),
			'styles' => array(
				'display' => 'flex',
				'justify-content' => 'space-between',
				'gap' => '2rem',
			),
			'wrap_schema' => '<div {id} {class}>{icon}<div class="cff-notice-wrap" {styles}><div class="cff-notice-body">{title}{message}</div>{buttons}</div></div>',
		);

		$cff_notices->add_notice( 'oembed_api_change', 'information', $error_args );
		$cff_statuses['oembed_api_change_notice'] = true;
		update_option('cff_statuses', $cff_statuses );
	}
}

/*
function cffOembedInit() {
	return new CFF_Oembed();
}
cffOembedInit();
*/
