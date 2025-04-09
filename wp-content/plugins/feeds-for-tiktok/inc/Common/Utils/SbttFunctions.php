<?php

use SmashBalloon\TikTokFeeds\Common\Utils;
use SmashBalloon\TikTokFeeds\Common\AuthorizationStatusCheck;

if (! defined('ABSPATH')) {
	exit;
}


/**
 * Encodes a PHP value into a JSON string.
 *
 * This function checks if the WordPress function `wp_json_encode()` is available and uses it to encode the value into JSON.
 * If `wp_json_encode()` is not available, it falls back to the native `json_encode()` function.
 *
 * @param mixed $thing The value to be encoded.
 * @return string The JSON-encoded string.
 */
function sbtt_json_encode($thing)
{
	if (function_exists('wp_json_encode')) {
		return wp_json_encode($thing);
	} else {
		return json_encode($thing);
	}
}

/**
 * Get the settings defaults
 *
 * @return array
 */
function sbtt_feed_settings_defaults()
{
	$is_pro = defined('SBTT_PRO') && SBTT_PRO === true;

	return [
		'feedType'                    => 'own_timeline',
		'sources'                     => [],
		'feedTemplate'                => 'default',

		// Layout.
		'layout'                      => 'grid',
		'verticalSpacing'             => 20,
		'horizontalSpacing'           => 16,
		'contentLength'               => 100,
		'numPostDesktop'              => 9,
		'numPostTablet'               => 8,
		'numPostMobile'               => 6,
		// Grid.
		'gridDesktopColumns'          => 3,
		'gridTabletColumns'           => 2,
		'gridMobileColumns'           => 1,
		// Masonry.
		'masonryDesktopColumns'       => 3,
		'masonryTabletColumns'        => 2,
		'masonryMobileColumns'        => 1,
		// Carousel.
		'carouselDesktopColumns'      => 3,
		'carouselTabletColumns'       => 2,
		'carouselMobileColumns'       => 1,
		'carouselDesktopRows'         => 1,
		'carouselTabletRows'          => 1,
		'carouselMobileRows'          => 1,
		'carouselLoopType'            => 'infinity',
		'carouselIntervalTime'        => 5000,
		'carouselShowArrows'          => false,
		'carouselShowPagination'      => true,
		'carouselEnableAutoplay'      => true,
		// Gallery.
		'galleryDesktopColumns'       => 3,
		'galleryTabletColumns'        => 2,
		'galleryMobileColumns'        => 1,

		// Header.
		'showHeader'                  => true,
		'headerContent'               => [ 'avatar', 'name', 'username', 'description', 'stats', 'button' ],
		'headerPadding'               => [],
		'headerMargin'                => [ 'bottom' => 50 ],
		// Profile Picture.
		'headerAvatar'                => $is_pro ? 'medium' : 'small',
		'headerAvatarPadding'         => [],
		'headerAvatarMargin'          => [],
		// Name.
		'headerNameFont'              => [
			'weight' => 600,
			'size'   => 16,
			'height' => 1,
		],
		'headerNameColor'             => '#141B38',
		'headerNamePadding'           => [],
		'headerNameMargin'            => [],
		// Username.
		'headerUsernameFont'          => [
			'weight' => 400,
			'size'   => 13,
			'height' => 1,
		],
		'headerUsernameColor'         => '#696D80',
		'headerUsernamePadding'       => [],
		'headerUsernameMargin'        => [],
		// Description.
		'headerDescriptionFont'       => [
			'weight' => 400,
			'size'   => 13,
			'height' => 1,
		],
		'headerDescriptionColor'      => '#696D80',
		'headerDescriptionPadding'    => [],
		'headerDescriptionMargin'     => [],
		// Stats.
		'headerStatsFont'             => [
			'weight' => 600,
			'size'   => 13,
			'height' => 1,
		],
		'headerStatsColor'            => '#141B38',
		// Stats Description.
		'headerStatsDescriptionFont'  => [
			'weight' => 400,
			'size'   => 13,
			'height' => 1,
		],
		'headerStatsDescriptionColor' => '#141B38',
		'headerStatsPadding'          => [],
		'headerStatsMargin'           => [ 'top' => 16 ],
		// Button.
		'headerButtonContent'         => __('Follow on TikTok', 'feeds-for-tiktok'),
		'headerButtonFont'            => [
			'weight' => 600,
			'size'   => 12,
			'height' => 1.5,
		],
		'headerButtonColor'           => '#ffffff',
		'headerButtonBg'              => '#FF3B5C',
		'headerButtonHoverColor'      => '#ffffff',
		'headerButtonHoverBg'         => '#FF3B5C',
		'headerButtonPadding'         => [
			'top'    => 6,
			'right'  => 8,
			'bottom' => 6,
			'left'   => 8,
		],
		'headerButtonMargin'          => [],

		// Post Style.
		'postStyle'                   => 'regular',
		'boxedBackgroundColor'        => '#ffffff',
		'boxedBoxShadow'              => [],
		'boxedBorderRadius'           => [],
		'postStroke'                  => [],
		'postPadding'                 => [
			'bottom' => 20,
		],

		'postElements'                => [ 'thumbnail', 'playIcon', 'views', 'likes', 'caption' ],
		'captionFont'                 => [
			'weight' => 400,
			'size'   => 13,
			'height' => '1.4em',
		],
		'captionColor'                => '#2C324C',
		'captionPadding'              => [
			'top'    => 12,
			'bottom' => 12,
		],
		'captionMargin'               => [],

		// Video Player Experience.
		'videoPlayer'                 => 'lightbox',

		// Loadmore Button.
		'showLoadButton'              => true,
		'loadButtonText'              => __('Load More', 'feeds-for-tiktok'),
		'loadButtonFont'              => [
			'weight' => 600,
			'size'   => 14,
			'height' => '1em',
		],
		'loadButtonColor'             => '#141B38',
		'loadButtonHoverColor'        => '#ffffff',
		'loadButtonBg'                => '#E6E6EB',
		'loadButtonHoverBg'           => '#FE544F',

		'loadButtonPadding'           => [
			'top'    => 15,
			'bottom' => 15,
		],
		'loadButtonMargin'            => [
			'top' => 20,
			'bottom' => 20,
		],

		// Sort.
		'sortFeedsBy'                 => 'latest',
		'sortRandomEnabled'           => false,

		// Filters.
		'includeWords'                => '',
		'excludeWords'                => '',
	];
}

/**
 * Get the TikTok connection URLs
 *
 * @param bool $is_settings Whether or not the URL is for the settings page.
 * @return array
 */
function sbtt_get_tiktok_connection_urls($is_settings = false)
{
	$urls            = array();
	$nonce           = wp_create_nonce('sbtt_con');
	$admin_url_state = ( $is_settings ) ? admin_url('admin.php?page=sbtt-settings') : admin_url('admin.php?page=sbtt');
	$sw_flag         = ! empty($_GET['sw-feed']) ? true : false;

	// If the admin_url isn't returned correctly then use a fallback.
	if ($admin_url_state == '/wp-admin/admin.php?page=sbtt' || $admin_url_state == '/wp-admin/admin.php?page=sbtt&tab=configuration') {
		$host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL);
		$request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
		$admin_url_state = esc_url('http://' . $host . $request_uri);
	}

	$urls['page']     = SBTT_CONNECT_URL;
	$urls['stateURL'] = $admin_url_state;
	$urls['sbtt_con'] = $nonce;
	$urls['sw_feed']  = $sw_flag;
	$urls['sbtt_connect_ver'] = sbtt_is_ssl() ? 2 : 1;

	return $urls;
}

/**
 * A function to check if the site is ssl enabled.
 *
 * @return bool
 */
function sbtt_is_ssl()
{
	// cloudflare.
	if (! empty($_SERVER['HTTP_CF_VISITOR'])) {
		$cfo = json_decode($_SERVER['HTTP_CF_VISITOR']);
		if (isset($cfo->scheme) && 'https' === $cfo->scheme) {
			return true;
		}
	}

	// other proxy.
	if (! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']) {
		return true;
	}

	return function_exists('is_ssl') ? is_ssl() : false;
}

/**
 * Sanitize the data.
 *
 * @param mixed $raw_data Raw data to be sanitized.
 * @return mixed
 */
function sbtt_sanitize_data($raw_data)
{
	if (is_string($raw_data) && is_array(json_decode(wp_unslash($raw_data), true)) && (json_last_error() == JSON_ERROR_NONE)) {
		$raw_data = json_decode(wp_unslash($raw_data), true);
	}

	if (is_string($raw_data) && filter_var($raw_data, FILTER_VALIDATE_URL)) {
		$raw_data = esc_url_raw($raw_data);
	} elseif (is_bool($raw_data)) {
		$raw_data = (bool) $raw_data;
	} elseif (is_numeric($raw_data)) {
		$raw_data = absint($raw_data);
	} elseif (is_array($raw_data)) {
		foreach ($raw_data as $key => $value) {
			$raw_data[$key] = sbtt_sanitize_data($value);
		}
	} else {
		$raw_data = sanitize_text_field(wp_unslash($raw_data));
	}

	return $raw_data;
}

/**
 * Get Whitespace
 *
 * @param int $times Number of times to repeat the whitespace.
 * @return string
 */
function sbtt_get_whitespace($times)
{
	return str_repeat('&nbsp;', $times);
}

/**
 * Get Site and Server Info
 *
 * @return string
 */
function sbtt_get_site_n_server_info()
{
	$allow_url_fopen = ini_get('allow_url_fopen') ? "Yes" : "No";
	$php_curl = is_callable('curl_init') ? "Yes" : "No";
	$php_json_decode = function_exists("json_decode") ? "Yes" : "No";
	$php_ssl = in_array('https', stream_get_wrappers()) ? "Yes" : "No";

	$output = '## SITE/SERVER INFO: ##' . "</br>";
	$output .= 'Plugin Version:' . sbtt_get_whitespace(11) . SBTT_PLUGIN_NAME . "</br>";
	$output .= 'Site URL:' . sbtt_get_whitespace(17) . site_url() . "</br>";
	$output .= 'Home URL:' . sbtt_get_whitespace(17) . home_url() . "</br>";
	$output .= 'WordPress Version:' . sbtt_get_whitespace(8) . get_bloginfo('version') . "</br>";
	$output .= 'PHP Version:' . sbtt_get_whitespace(14) . PHP_VERSION . "</br>";
	$output .= 'Web Server Info:' . sbtt_get_whitespace(10) . esc_html(sanitize_text_field($_SERVER['SERVER_SOFTWARE'])) . "</br>";
	$output .= 'PHP allow_url_fopen:' . sbtt_get_whitespace(6) . $allow_url_fopen . "</br>";
	$output .= 'PHP cURL:' . sbtt_get_whitespace(17) . $php_curl . "</br>";
	$output .= 'JSON:' . sbtt_get_whitespace(21) . $php_json_decode . "</br>";
	$output .= 'SSL Stream:' . sbtt_get_whitespace(15) . $php_ssl . "</br>";
	$output .= "</br>";

	return $output;
}

/**
 * Get Active Plugins
 *
 * @return string
 */
function sbtt_get_active_plugins_info()
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
 * @return string
 */
function sbtt_get_global_settings_info()
{
	$output = '## GLOBAL SETTINGS: ## </br>';
	$global_settings = get_option('sbtt_global_settings', array());

	$plugin_status = new AuthorizationStatusCheck();

	$statuses = $plugin_status->get_statuses();

	if (Utils::sbtt_is_pro()) {
		$output .= 'License key: ';
		if (isset($global_settings['license_key'])) {
			$output .= esc_html($global_settings['license_key']);
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
		if (isset($global_settings['license_status'])) {
			$output .= $global_settings['license_status'];
		} else {
			$output .= ' Inactive';
		}
		$output .= '</br>';
	}
	$output .= 'Preserve settings if plugin is removed: ';
	$output .= isset($global_settings['preserve_settings']) && ($global_settings['preserve_settings']) ? 'Yes' : 'No';
	$output .= '</br>';
	$output .= 'Caching: ';
	$output .= $statuses['license_tier'] === 'pro' ? 'Twice daily' : 'daily';

	$output .= '</br>';
	$output .= 'GDPR: ';
	$output .= isset($global_settings['gdpr']) ? $global_settings['gdpr'] : ' Not setup';
	$output .= '</br>';
	$output .= 'Optimize Images: ';
	$output .= isset($global_settings['optimize_images']) && $global_settings['optimize_images'] === true ? 'Enabled' : 'Disabled';
	$output .= '</br>';
	$output .= 'Usage Tracking: ';
	$output .= isset($global_settings['usagetracking']) && $global_settings['usagetracking'] === true ? 'Enabled' : 'Disabled';
	$output .= '</br>';
	$output .= '</br>';
	$output .= '</br>';

	return $output;
}

/**
 * Get Sources Settings
 *
 * @return string
 */
function sbtt_get_sources_settings_info()
{
	$output = '## SOURCES: ## </br>';
	$source_list = Utils::get_sources_list();

	if (!$source_list) {
		$output .= 'No sources found';
		$output .= '</br>';

		return $output;
	}

	foreach ($source_list as $source) {
		$output .= $source['display_name'] . ' ( Open ID => ' . $source['open_id'] . ' )';
		$output .= '</br>';
	}
	$output .= '</br>';

	return $output;
}

/**
 * Get known error messages and directions to resolve for the given error message.
 *
 * @param string $message Error message.
 * @return array
 */
function sbtt_get_error_message_and_directions($message)
{
	$error_messages = [
		'The payload is invalid.' => [
			'message' => __('Invalid Access Token. Please reconnect the source.', 'feeds-for-tiktok'),
			'directions' => wp_sprintf(__('Please go to %1$sTikTok Feeds%2$s settings page to reconnect the source.', 'feeds-for-tiktok'), '<a href="' . esc_url(admin_url('admin.php?page=sbtt-settings')) . '" target="_blank" rel="noopener noreferrer">', '</a>'),
		],
		'access_token_invalid' => [
			'message' => __('Invalid Access Token. Please reconnect the source.', 'feeds-for-tiktok'),
			'directions' => wp_sprintf(__('Please go to %1$sTikTok Feeds%2$s settings page to reconnect the source.', 'feeds-for-tiktok'), '<a href="' . esc_url(admin_url('admin.php?page=sbtt-settings')) . '" target="_blank" rel="noopener noreferrer">', '</a>'),
		]
	];

	if (isset($error_messages[$message])) {
		return $error_messages[$message];
	}

	return $message;
}
