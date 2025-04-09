<?php

namespace SmashBalloon\YouTubeFeed\Blocks;

use Smashballoon\Customizer\DB;
use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\Services\AssetsService;
use SmashBalloon\YouTubeFeed\Services\LicenseNotification;
use Smashballoon\Framework\Packages\Blocks\RecommendedBlocks;

/**
 * Instagram Feed block with live preview.
 *
 * @since 1.7.1
 */
class SBY_Blocks {

	protected $db;
	protected $feed_builder;
	protected $license_service;

	public function __construct( Feed_Builder $feed_builder, DB $db ) {
		$this->db = $db;
		$this->feed_builder = $feed_builder;
		$this->license_service = new LicenseNotification();
	}

	/**
	 * Indicates if current integration is allowed to load.
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function allow_load() {
		return function_exists( 'register_block_type' );
	}

	/**
	 * Loads an integration.
	 *
	 * @since 1.7.1
	 */
	public function load() {
		$this->hooks();
		$recommended_blocks = new RecommendedBlocks();
		$recommended_blocks->setup();
	}

	/**
	 * Integration hooks.
	 *
	 * @since 1.7.1
	 */
	protected function hooks() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register Instagram Feed Gutenberg block on the backend.
	 *
	 * @since 1.7.1
	 */
	public function register_block() {

		wp_register_style(
			'sby-blocks-styles',
			trailingslashit( SBY_PLUGIN_URL ) . 'css/sby-blocks.css',
			array( 'wp-edit-blocks' ),
			SBYVER
		);

		$attributes = array(
			'shortcodeSettings' => array(
				'type' => 'string',
			),
			'noNewChanges' => array(
				'type' => 'boolean',
			),
			'executed' => array(
				'type' => 'boolean',
			)
		);

		register_block_type(
			'sby/sby-feed-block',
			array(
				'attributes'      => $attributes,
				'render_callback' => array( $this, 'get_feed_html' ),
			)
		);
	}

	/**
	 * Load Instagram Feed Gutenberg block scripts.
	 *
	 * @since 1.7.1
	 */
	public function enqueue_block_editor_assets() {
		do_action('sby_enqueue_scripts', true);

		wp_enqueue_style( 'sby-blocks-styles' );
		wp_enqueue_script(
			'sby-feed-block',
			trailingslashit( SBY_PLUGIN_URL ) . 'js/sby-blocks.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			SBYVER,
			true
		);

		$shortcode_settings = '';

		$i18n = array(
			'addSettings'         => esc_html__( 'Add Settings', 'feeds-for-youtube' ),
			'shortcodeSettings'   => esc_html__( 'Shortcode Settings', 'feeds-for-youtube' ),
			'example'             => esc_html__( 'Example', 'feeds-for-youtube' ),
			'preview'             => esc_html__( 'Apply Changes', 'feeds-for-youtube' ),

		);

		if ( ! empty( $_GET['sby_wizard'] ) ) {
			$shortcode_settings = 'feed="' . (int) $_GET['sby_wizard'] . '"';
		}

		wp_localize_script(
			'sby-feed-block',
			'sby_block_editor',
			array(
				'wpnonce'  => wp_create_nonce( 'sby-blocks' ),
				'canShowFeed' => true,
				'shortcodeSettings'    => $shortcode_settings,
				'i18n'     => $i18n,
			)
		);
	}

	/**
	 * Get form HTML to display in a Instagram Feed Gutenberg block.
	 *
	 * @param array $attr Attributes passed by Instagram Feed Gutenberg block.
	 *
	 * @since 1.7.1
	 *
	 * @return string
	 */
	public function get_feed_html( $attr ) {
		$feeds_count = $this->db->feeds_count();
		$shortcode_settings = isset( $attr['shortcodeSettings'] ) ? $attr['shortcodeSettings'] : '';

		if ( $feeds_count <= 0 ) {
			return $this->plain_block_design( empty( Util::get_license_key() ) ? 'inactive' : 'expired' );
		}

		$return = '';
		$return .= $this->get_license_expired_notice();

		$statuses = get_option( 'sby_statuses', array() );

		if ( empty( $statuses['support_legacy_shortcode'] ) ) {
			if ( empty( $shortcode_settings ) || strpos( $shortcode_settings, 'feed=' ) === false ) {
				$feeds = $this->feed_builder->get_feed_list();
				if ( ! empty( $feeds[0]['id'] ) ) {
					$shortcode_settings = 'feed="' . (int) $feeds[0]['id'] . '"';
				}
			}
		}

		$shortcode_settings = str_replace(array( '[youtube-feed', ']' ), '', $shortcode_settings );

		$return .= do_shortcode( '[youtube-feed '.$shortcode_settings.']' );

		return $return;

	}

	/**
	 * Plain block design when theres no feeds.
	 * 
	 * @since 2.0.2
	 */
	public function plain_block_design( $license_state = 'expired' ) {
		if ( !is_admin() && !defined( 'REST_REQUEST' ) ) {
			return;
		}
		$other_plugins = $this->get_others_plugins();

		$icons = sby_builder_pro()->builder_svg_icons();
		$output = '<div class="sby-license-expired-plain-block-wrapper '. $license_state .'">
			<div class="sby-lepb-header">
				<div class="sb-left">';
		$output .= $icons['info'];

		if ( $license_state == 'expired' ) {
			$output .= sprintf('<p>%s</p>', __('Your license has expired! Renew it to reactivate Pro features.', 'feeds-for-youtube'));
		} else {
			$output .= sprintf('<p>%s</p>', __('Your license key is inactive. Activate it to enable Pro features.', 'feeds-for-youtube'));
		}
		
		$output .= '</div>
				<div class="sb-right">
					<a href="'. $this->license_service->get_renew_url() .'">
						Resolve Now
						'. $icons['chevronRight'] .'
					</a>
				</div>
			</div>
			<div class="sby-lepb-body">
				'. $icons['blockEditorSBYLogo'] .'
				<p class="sby-block-body-title">Get started with your first feed from <br/> your YouTube Channel</p>';
		
		$output .= sprintf(
					'<a href="%s" class="sby-btn sby-btn-blue">%s '. $icons['chevronRight'] .'</a>', 
					admin_url('admin.php?page=sby-feed-builder'), 
					__('Create a YouTube Feeds', 'feeds-for-youtube')
				);
		$output .= '</div>
			<div class="sby-lepd-footer">
				<p class="sby-lepd-footer-title">Did you know? </p>
				<p>You can add posts from '. $other_plugins .' using our free plugins</p>
			</div>
		</div>';

		return $output;
	}

	/**
	 * Get other Smash Balloon plugins list
	 * 
	 * @since 2.0.2
	 */
	public function get_others_plugins() {
		$active_plugins = sby_get_active_plugins_info();

		$other_plugins = array(
			'is_instagram_installed' => array(
				'title' => 'Instagram',
				'url'	=> 'https://smashballoon.com/instagram-feed/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
			),
			'is_facebook_installed' => array(
				'title' => 'Facebook',
				'url'	=> 'https://smashballoon.com/custom-facebook-feed/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
			),
			'is_twitter_installed' => array(
				'title' => 'Twitter',
				'url'	=> 'https://smashballoon.com/custom-twitter-feeds/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
			),
			'is_youtube_installed' => array(
				'title' => 'YouTube',
				'url'	=> 'https://smashballoon.com/youtube-feed/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
			),
		);

		if ( ! empty( $active_plugins ) ) {
			foreach ( $active_plugins as $name => $plugin ) {
				if ( $plugin != false ) {
					unset( $other_plugins[$name] );
				}
			}
		}

		$other_plugins_html = array();
		foreach( $other_plugins as $plugin ) {
			$other_plugins_html[] = '<a href="'. $plugin['url'] .'">'. $plugin['title'] .'</a>';
		}
		
		return \implode(", ", $other_plugins_html);
	}

	public function get_license_expired_notice() {
		// Check that the license exists and the user hasn't already clicked to ignore the message
		if ( empty( Util::get_license_key() ) ) {
			return $this->get_license_expired_notice_content( 'inactive' );
		}
		// If license not expired then return;
		if ( !Util::is_license_expired() ) {
			return;
		}
		// Grace period ended?
		if ( !Util::is_license_grace_period_ended( true ) ) {
			return;
		}
		
		return $this->get_license_expired_notice_content();
	}

	/**
	 * Output the license expired notice content on top of the embed block 
	 * 
	 * @since 2.0.2
	 */
	public function get_license_expired_notice_content( $license_state = 'expired' ) {
		if ( !is_admin() && !defined( 'REST_REQUEST' ) ) {
			return;
		}
		$icons = sby_builder_pro()->builder_svg_icons(); 

		$output = '<div class="sby-block-license-expired-notice-ctn sby-bln-license-state-'. $license_state .'">';
			$output .= '<div class="sby-blen-header">';
				$output .= $icons['eye2'];
				$output .= '<span>' . __('Only Visible to WordPress Admins', 'feeds-for-youtube') . '</span>';
			$output .= '</div>';
			$output .= '<div class="sby-blen-resolve">';
				$output .= '<div class="sby-left">';
					$output .= $icons['info'];
					if ( $license_state == 'inactive' ) {
						$output .= '<span>' . __('Your license key is inactive. Activate it to enable Pro features.', 'feeds-for-youtube') . '</span>';
					} else {
						$output .= '<span>' . __('Your license has expired! Renew it to reactivate Pro features.', 'feeds-for-youtube') . '</span>';
					}
				$output .= '</div>';
				$output .= '<div class="sby-right">';
					$output .= '<a href="'. $this->license_service->get_renew_url() .'" target="_blank">'. __('Resolve Now', 'feeds-for-youtube') .'</a>';
					$output .= $icons['chevronRight'];
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Checking if is Gutenberg REST API call.
	 *
	 * @since 1.7.1
	 *
	 * @return bool True if is Gutenberg REST API call.
	 */
	public static function is_gb_editor() {

		// TODO: Find a better way to check if is GB editor API call.
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context']; // phpcs:ignore
	}

}
