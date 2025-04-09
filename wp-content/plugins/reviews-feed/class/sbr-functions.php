<?php
use SmashBalloon\Reviews\Common\Util;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function sbr_json_encode( $thing ){
	if (function_exists('wp_json_encode')) {
		return wp_json_encode($thing);
	}
	else {
		return json_encode($thing);
	}
}


/**
 * Reviews Currect User Capability Check
 *
 * @since 1.0
 */
function sbr_current_user_can( $cap ){
	if ($cap === 'manage_reviews_feed_options') {
		$cap = current_user_can('manage_reviews_feed_options') ? 'manage_reviews_feed_options' : 'manage_options';
	}
	$cap = apply_filters('sbr_settings_pages_capability', $cap);

	return current_user_can($cap);
}


/**
 * Get the settings in the database with defaults
 *
 * @return array
 */
function sbr_get_database_settings(){
	global $sbr_settings;

	$defaults = sbr_settings_defaults();

	if ($sbr_settings === null) {
		$sbr_settings = get_option('sbr_settings', []);
	}

	return array_merge($defaults, $sbr_settings);
}

/**
 * Get the settings default settins
 *
 * @return array
 */
function sbr_settings_defaults(){
	return [
		//Template
		'feedTemplate' => 'default',
		//Sources
		'sources'   => [],
		//Layout Settings
		'layout' => 'list',
		'verticalSpacing' => 20,
		'horizontalSpacing' => 10,
		'contentAlignment' => 'left',
		'contentLength' => 320,
		'numPostDesktop' => 10,
		'numPostTablet' => 8,
		'numPostMobile' => 6,
		'gridDesktopColumns' => 3,
		'gridTabletColumns' => 2,
		'gridMobileColumns' => 1,
		'masonryDesktopColumns' => 3,
		'masonryTabletColumns' => 2,
		'masonryMobileColumns' => 1,
		'carouselDesktopColumns' => 3,
		'carouselTabletColumns' => 2,
		'carouselMobileColumns' => 1,
		'carouselDesktopRows' => 1,
		'carouselTabletRows' => 1,
		'carouselMobileRows' => 1,
		'carouselLoopType' => 'infinity',
		'carouselIntervalTime' => 5000,
		'carouselShowArrows' => false,
		'carouselShowPagination' => true,
		'carouselEnableAutoplay' => true,

		//Header
		'showHeader'    => true,
		'headerContent' => ['heading', 'button', 'averagereview'],
		'headerPadding' => [],
		'headerMargin'  => ['bottom' => 20],
		//Heading
		'headerHeadingContent'  => 'Reviews',
		'headingFont'  => [
			'weight' => 700,
			'size' => 36,
			'height' => '100%'
		],
		'headingColor' => '#141B38',
		'headerHeadingPadding' => [],
		'headerHeadingMargin' => ['bottom' => 10],
		//Button
		'headerButtonLinkTo'    => 'google',
		'headerButtonIcon'  => '',
		'headerButtonExternalLink'  => '',
		'headerButtonFont'  => [
			'weight' => 600,
			'size' => 14,
			'height' => '22px'
		],
		'headerButtonColor' => '#ffffff',
		'headerButtonBg' => '#ED4944',
		'headerButtonHoverColor' => '#ffffff',
		'headerButtonHoverBg' => '#CC3F3A',
		'headerButtonPadding' => [
			'top'   => 8,
			'right' => 20,
			'bottom' => 8,
			'left' => 12,
		],
		'headerButtonMargin'  => [],
		//AverageReview
		'headerAvReviewFont' =>  [
			'weight' => 600,
			'size' => 20,
			'height' => '1.5em'
		],
		'headerAvSubtextReviewFont' =>   [
			'weight' => 400,
			'size' => 12,
			'height' => '1.5em'
		],
		'headerAvReviewIconColor' => '#ED4944',
		'headerAvReviewColor' => '#141B38',
		'headerAvReviewSubtextColor' => '#434960',
		'headerAvReviewMargin' => '',
		'headerAvReviewPadding' => '',

		//Post Style
		'postStyle' => 'regular',
		'boxedBackgroundColor'  => '#ffffff',
		'boxedBoxShadow'     => [],
		'boxedBorderRadius'     => [],
		'postStroke'     => [],
		'postPadding' => [
			'bottom' => 20
		],

		'postElements' => ['author', 'rating', 'text', 'media'],
		'ratingIconSize'    => 'small',
		'ratingIconColor' => '#ED4944',
		'ratingIconPadding' => [],
		'ratingIconMargin' => [
			'top' => 15,
			'bottom' => 15,
		],
		'paragraphFont' =>   [
			'weight' => 400,
			'size' => 16,
			'height' => '1.5em'
		],
		'paragraphColor' => '#434960',
		'paragraphPadding' => [],
		'paragraphMargin' => [],

		'authorContent' => ['name', 'image', 'date'],
		'authorPadding' => [],
		'authorMargin' => [],

		'authorNameFont' => [
			'weight' => 600,
			'size' => 14,
			'height' => '1.5em'
		],
		'authorNameColor'   => '#141B38',
		'authorNamePadding' => [],
		'authorNameMargin' => [],

		'dateFont' => [
			'weight' => 400,
			'size' => 13,
			'height' => '1.5em'
		],
		'dateColor'   => '#434960',

		'dateFormat'    => '1',
		'dateCustomFormat'  =>'',
		'dateBeforeText'  =>'',
		'dateAfterText'  =>'',
		'datePadding' => [],
		'dateMargin' => [],
		'authorImageBorderRadius' => 50,
		'authorImageMargin' => [
			'right' => 10
		],

		'showLoadButton' => true,
		'loadButtonText'    => 'Load More',
		'loadButtonFont'  => [
			'weight' => 600,
			'size' => 16,
			'height' => '1em'
		],
		'loadButtonColor'   => '#141B38',
		'loadButtonHoverColor'  => '#ffffff',
		'loadButtonBg'  => '#E6E6EB',
		'loadButtonHoverBg' => '#FE544F',

		'loadButtonPadding' => [
			'top' => 15,
			'bottom' => 15
		],
		'loadButtonMargin' => [
			'top' => 20
		],

		//Filters
		'includedStarFilters' => [],
		'includeWords' => '',
		'excludeWords' => '',
		'filterByImage' => false,
		'filterByVideos' => true,

		//Sort
		'sortByDateEnabled' => true,
		'sortByDate' => 'latest',

		'sortByRatingEnabled'  => false,
		'sortByRating' => '',

		'sortRandomEnabled' => false,

		//ColorScheme
		'colorScheme' => 'inherit',


		//Moderation Mode
		'moderationEnabled' => false,
		'moderationType' => 'allow',
		'moderationAllowList' => [],
		'moderationBlockList' => [],

        //Translation
        'localization'=> 'default',

		//Filter By Length
        'filterCharCountMin'=> 28,
        'filterCharCountMax'=> '',
	];
}


function sbr_plugin_settings_defaults(){
	return [
        'localization' => '',
        'optimize_images' => true,
		'usagetracking' => true,
		'enqueue_js_in_header' => false,
		'admin_error_notices' => true,
		'feed_issue_reports' => true,
        'translations' => [
            'second' => __('second', 'reviews-feed'),
            'seconds' => __('seconds', 'reviews-feed'),
            'minute' => __('minute', 'reviews-feed'),
            'minutes' => __('minutes', 'reviews-feed'),
            'hour' => __('hour', 'reviews-feed'),
            'hours' => __('hours', 'reviews-feed'),
            'day' => __('day', 'reviews-feed'),
            'days' => __('days', 'reviews-feed'),
            'week' => __('week', 'reviews-feed'),
            'weeks' => __('weeks', 'reviews-feed'),
            'month' => __('month', 'reviews-feed'),
            'months' => __('months', 'reviews-feed'),
            'year' => __('year', 'reviews-feed'),
            'years' => __('year', 'reviews-feed'),
            'ago' => __('ago', 'reviews-feed'),
            'writeReview' => __('Write a Review', 'reviews-feed'),
            'reviewsHeader' => __('Over %s Reviews', 'reviews-feed'),
        ]
	];
}
function sbr_activate( $network_wide ) {
	global $wp_roles;
	$wp_roles->add_cap('administrator', 'manage_reviews_feed_options');
}

register_activation_hook( __FILE__, 'sby_activate' );


function sbr_get_feed_template_part( $part, $settings = array() ) {
	$file 		= '';

	/**
	 * Whether or not to search for custom templates in theme folder
	 *
	 * @param boolean  Setting from DB or shortcode to use custom templates
	 *
	 * @since 1.0
	 */
	$settings_custom_templates = ! empty( $settings['customtemplates'] ) && $settings['customtemplates'];
	$using_custom_templates_in_theme = apply_filters( 'sbr_use_theme_templates', $settings_custom_templates );
	$generic_path = trailingslashit( SBR_PLUGIN_DIR ) . 'templates/frontend/';

    //For Templates that are different Free Or Pro
    $special_path = $generic_path . ( Util::sbr_is_pro() ? 'pro' : 'lite'  ) . '/';

	if ( $using_custom_templates_in_theme ) {
		$custom_header_template = locate_template( 'sbr/header.php', false, false );
		$custom_item_template = locate_template( 'sbr/item.php', false, false );
		$custom_footer_template = locate_template( 'sbr/footer.php', false, false );
		$custom_feed_template = locate_template( 'sbr/feed.php', false, false );
	} else {
		$custom_header_template = false;
		$custom_item_template = false;
		$custom_footer_template = false;
		$custom_feed_template = false;
	}

	if ( $part === 'header' ) {
		if ( $custom_header_template ) {
			$file = $custom_header_template;
		} else {
			#$file = $generic_path . 'header.php';
            $file = $special_path . 'header.php';
		}
	} elseif ( $part === 'item' ) {
		if ( $custom_item_template ) {
			$file = $custom_item_template;
		} else {
			$file = $generic_path . 'item.php';
		}
	} elseif ( $part === 'footer' ) {
		if ( $custom_footer_template ) {
			$file = $custom_footer_template;
		} else {
			#$file = $generic_path . 'footer.php';
            $file = $special_path . 'footer.php';
        }
	} elseif ( $part === 'feed' ) {
		if ( $custom_feed_template ) {
			$file = $custom_feed_template;
		} else {
			#$file = $generic_path . 'feed.php';
            $file = $special_path . 'feed.php';
        }
	} elseif ( $part === 'post-elements/author' ) {
		if ( $custom_feed_template ) {
			$file = $custom_feed_template;
		} else {
			#$file = $generic_path . 'post-elements/author.php';
            $file = $special_path . 'post-elements/author.php';
        }
	} elseif ( $part === 'post-elements/media' ) {
		if ( $custom_feed_template ) {
			$file = $custom_feed_template;
		} else {
			#$file = $generic_path . 'post-elements/media.php';
            $file = $special_path . 'post-elements/media.php';
		}
	} elseif ( $part === 'post-elements/rating' ) {
		if ( $custom_feed_template ) {
			$file = $custom_feed_template;
		} else {
			$file = $generic_path . 'post-elements/rating.php';
		}
	} elseif ( $part === 'post-elements/text' ) {
		if ( $custom_feed_template ) {
			$file = $custom_feed_template;
		} else {
			$file = $generic_path . 'post-elements/text.php';
		}
	}

	return $file;
}

function sbr_container_id( $feed_id ) {
	return 'sb-reviews-container-' . $feed_id;
}

function sbr_scripts_enqueue() {
	//Register the script to make it available
	$settings = get_option('sbr_settings', []);
    $min = '.min';
    $min = '';
	wp_enqueue_style( 'sbr_styles', trailingslashit( SBR_PLUGIN_URL ) . 'assets/css/sbr-styles'.$min.'.css', array(), SBRVER );
	if ( ! empty( $settings['enqueue_js_in_header'] ) ) {
		wp_enqueue_script( 'sbr_scripts', trailingslashit( SBR_PLUGIN_URL ) . 'assets/js/sbr-feed'.$min.'.js', array( 'jquery' ), SBRVER, false );
	} else {
		wp_register_script( 'sbr_scripts', trailingslashit( SBR_PLUGIN_URL ) . 'assets/js/sbr-feed'.$min.'.js', array( 'jquery' ), SBRVER, true );
	}

	$data = array(
		'adminAjaxUrl'  => admin_url( 'admin-ajax.php' ),
	);
	//Pass option to JS file
	wp_localize_script('sbr_scripts', 'sbrOptions', $data );
}
add_action( 'wp_enqueue_scripts', 'sbr_scripts_enqueue', 2 );

function sbr_esc_html_with_br( $text ) {
	return str_replace( array( '&lt;br /&gt;', '&lt;br&gt;' ), '<br>', esc_html( nl2br( $text ) ) );
}




function sbr_get_fb_connection_urls( $is_settings = false ) {
	$urls            	= array();
	$admin_url_state 	= $is_settings ?
							admin_url('admin.php?page=sbr-settings') :
							admin_url('admin.php?page=sbr');
	$sb_admin_email 	= get_option('admin_email');
	$nonce           	= wp_create_nonce('cff_con');
	$sw_flag         	= !empty($_GET['sw-feed']) ? true : false;

	// If the admin_url isn't returned correctly then use a fallback.
	if (
		$admin_url_state === '/wp-admin/admin.php?page=sbr'
		|| $admin_url_state === '/wp-admin/admin.php?page=sbr&tab=configuration'
	) {
		$admin_url_state = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	$urls['page'] 	= [
		'connect' 			=> SBR_FB_CONNECT_URL,
		'wordpress_user'   => $sb_admin_email,
		'v'                => 'pro',
		'vn'               => SBRVER,
		'cff_con'          => $nonce,
		'sw_feed'          => $sw_flag
	];

	$urls['stateURL'] = $admin_url_state;
	return $urls;
}

function check_license_valid(){
	$sbr_settings = get_option('sbr_settings', []);
	return isset($sbr_settings['license_key'])
	       && !empty($sbr_settings['license_key'])
	       && isset($sbr_settings['license_status'])
	       && !empty($sbr_settings['license_status'])
	       && $sbr_settings['license_status'] !== 'invalid';
}

function sbr_plugin_action_links( $links ){
	$settings_link = check_license_valid() ? admin_url( 'admin.php?page=sbr-settings' ) : admin_url( 'admin.php?page=sbr' );
    $support_link = check_license_valid() ? admin_url('admin.php?page=sbr-support') : admin_url('admin.php?page=sbr');
	$links = array_merge(
		array(
			'<a href="' . esc_url( $settings_link ) . '">' . __('Settings', 'reviews-feed') . '</a>'
		), $links);

    if( !Util::sbr_is_pro() ){
        $links = array_merge(
            array(
                '<a href="https://smashballoon.com/reviews-feed/?utm_campaign=reviews-free&utm_source=plugins-page&utm_medium=upgrade-link&utm_content=UpgradeToPro" target="_blank" style="font-weight:bold; color: #50a56d;">' . __('Upgrade to Pro', 'reviews-feed') . '</a>'
            ),
            $links
        );
    }else{
        $links = array_merge(
            array(
                '<a href="' . esc_url($support_link) . '">' . __('Support', 'reviews-feed') . '</a>'
            ),
            $links
        );
    }

	return $links;

}
add_action('plugin_action_links_' . SBR_PLUGIN_BASENAME, 'sbr_plugin_action_links');


add_action( 'current_screen', 'sbr_check_current_screen' );

function sbr_check_current_screen() {
	if (Util::currentPageIs('sbr')) {
		add_action('admin_enqueue_scripts', 'dequeue_smash_plugins_style');
	}
}

function dequeue_smash_plugins_style() {
	wp_dequeue_style('cff_custom_wp_admin_css');
	wp_deregister_style('cff_custom_wp_admin_css');

    wp_dequeue_style('feed-global-style');
    wp_deregister_style('feed-global-style');

	wp_dequeue_style('sb_instagram_admin_css');
	wp_deregister_style('sb_instagram_admin_css');

    wp_dequeue_style('ctf_admin_styles');
	wp_deregister_style('ctf_admin_styles');

}

function sbr_custom_menu(){
    if(Util::sbr_is_pro() === false){
        $cap = current_user_can('manage_reviews_feed_options') ? 'manage_reviews_feed_options' : 'manage_options';
        $cap = apply_filters('sbr_settings_pages_capability', $cap);
        add_submenu_page(
            'sbr',
            __('Upgrade to Pro', 'reviews-feed'),
            __('<div class="sb-pro-upgradelink-bg"></div><strong class="sb-pro-upgradelink">Upgrade to Pro</strong>', 'reviews-feed'),
            $cap,
            'https://smashballoon.com/reviews-feed/?utm_campaign=reviews-free&utm_source=menu-link&utm_medium=upgrade-link&utm_content=UpgradeToPro',
            ''
        );
    }
}

add_action('admin_menu', 'sbr_custom_menu', 40);


function sbr_text_domain()
{
    load_plugin_textdomain('reviews-feed', false, dirname(SBR_PLUGIN_BASENAME) . '/languages');
}
add_action('init', 'sbr_text_domain');

function sbr_get_current_time() {
    $current_time = time();

    // where to do tests
     //$current_time = strtotime( 'November 25, 2020' );

    return $current_time;
}


function sbr_recursive_parse_args( $args, $defaults ) {
    $new_args = (array) $defaults;

    foreach ( $args as $key => $value ) {
    	if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
        	$new_args[ $key ] = sbr_recursive_parse_args( $value, $new_args[ $key ] );
        }
        else {
        	$new_args[ $key ] = $value;
        }
    }
	return $new_args;
}

function sbr_doing_openssl()
{
	return extension_loaded('openssl');
}

function sbr_encrypt_decrypt($action, $string)
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


/**
 * Returns a list of providers that needs
 * the bulk history update
 *
 * @return array
 *
 * @since 1.5
 */
function sbr_get_bulk_providers()
{
	return [
		'google',
		'yelp'
	];
}

/**
 * Returns a list of providers that
 * have media
 *
 * @return array
 *
 * @since 1.5
 */
function sbr_get_media_providers()
{
	return [
		'yelp',
		'tripadvisor',
		'google'
	];
}

/**
 * Returns a list of providers that doesn't
 * have media
 *
 * @return array
 *
 * @since 1.5
 */
function sbr_get_no_media_providers()
{
	return [
		'facebook'
	];
}

/**
 * Returns a list of providers that have
 * translations / languages Possibility
 *
 * @return array
 *
 * @since 1.5
 */
function sbr_get_lang_providers()
{
	return [
		'google'
	];
}