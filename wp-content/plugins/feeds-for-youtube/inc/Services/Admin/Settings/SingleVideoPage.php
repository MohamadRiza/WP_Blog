<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin\Settings;

use Smashballoon\Customizer\Feed_Builder;
use Smashballoon\Customizer\Feed_Saver;
use SmashBalloon\YouTubeFeed\Helpers\Util;
use SmashBalloon\YouTubeFeed\SBY_Settings;
use Smashballoon\Customizer\YouTube_License_Tier;

class SingleVideoPage extends BaseSettingPage {
	protected $has_assets = true;
	protected $has_menu = true;
	protected $menu_slug = 'single-videos';
	protected $template_file = 'settings.index';
	protected $menu_position = 2;
	protected $menu_position_free_version = 2;

	private $posts_table;
	private $meta_table;

	public function __construct() {
		$this->page_title = __('Single Videos', 'feeds-for-youtube');
		$this->menu_title = __('Single Videos', 'feeds-for-youtube');
	
		// YouTube license tier 
		$license_tier = new YouTube_License_Tier;
		$license_tier_features = $license_tier->tier_features();
		// Hide Single Videos page if feature is not available in the tier
		if (!in_array('convert_videos_to_cpt', $license_tier_features)) {
			$this->has_page_restriction = true;
			$this->menu_title = '<span class="sby-single-videos-upsell">' . __('Single Videos', 'feeds-for-youtube') . '</span>';
		}
	}

	public function register() {
		global $wpdb; 
		parent::register();
		$this->posts_table = $wpdb->prefix . 'posts';
		$this->meta_table  = $wpdb->prefix . 'postmeta';
		add_action( 'wp_ajax_sby_get_single_videos', [ $this, 'ajax_query_single_videos' ] );
		add_action( 'wp_ajax_sby_all_videos_action', [ $this, 'ajax_all_video_action' ] );
		add_action( 'pre_get_posts', [ $this, 'filter_single_videos' ] );
		add_action( 'sby_localized_settings', [ $this, 'localize_listing_url' ] );
	}

	public function ajax_query_single_videos() {
		Util::ajaxPreflightChecks();
		$page = esc_sql($_POST['page']);

		wp_send_json_success(['total_result' => $this->single_videos_total(), 'results' => $this->query_single_videos($page)]);
	}

	public function ajax_all_video_action() {
		Util::ajaxPreflightChecks();
		$meta_query = [
			'relation' => 'OR'
		];
		$exploded_channels = explode(',', $_POST['channel']);
		if ( is_array( $exploded_channels ) ) {
			foreach ( $exploded_channels as $channel_id ) {
				$meta_query[] = [
					'key'     => 'sby_channel_title',
					'value'   => esc_sql( $channel_id ),
					'compare' => '=',
				];
			}
		} else {
			$meta_query[] = [
				'key'     => 'sby_channel_title',
				'value'   => esc_sql( $exploded_channels ),
				'compare' => '=',
			];
		}

		$args = array(
			'post_type' => SBY_CPT,
			'meta_key' => 'sby_channel_title',
			'posts_per_page' => -1,
			'meta_query' => $meta_query
		);

		$query = new \WP_Query($args);

		if($_POST['perform'] === 'publish') {
			foreach ( $query->get_posts() as $post ) {
				$postData = [ 'ID' => $post->ID, 'post_status' => 'publish' ];
				wp_update_post( $postData );
			}
		}

		if($_POST['perform'] === 'delete') {
			foreach ( $query->get_posts() as $post ) {
				wp_delete_post( $post->ID, true );
			}
		}

		wp_send_json_success();
	}

	public function filter_single_videos($query) {
		global $pagenow;

		if ( isset($_GET['post_type'], $_GET['sby_channel_filter']) && $_GET['post_type'] === SBY_CPT &&  $pagenow === 'edit.php' && is_admin() && $query->is_main_query() ) {
			$query->set( 'meta_key', 'sby_channel_title' );
			$query->set( 'meta_value', $_GET['sby_channel_filter'] );
		}

		return $query;
	}

	public function localize_listing_url($settings) {
		$settings['single_videos_list'] = admin_url(sprintf('edit.php?post_type=%s', SBY_CPT));
		return $settings;
	}

	private function query_single_videos($page, $limit = 5) {
		global $wpdb;
		$query = sprintf( 'SELECT SUM(%1$s.post_status="publish") AS publish,SUM(%1$s.post_status="draft") AS draft, %2$s.meta_value as title FROM `%2$s` 
JOIN %1$s ON %2$s.post_id=%1$s.ID 
WHERE %2$s.meta_key="sby_channel_title" 
GROUP BY %2$s.meta_value ORDER BY %1$s.ID DESC LIMIT %3$s, %4$s', $this->posts_table, $this->meta_table, ($page * $limit), $limit );



		$query = $wpdb->prepare($query);
		return $wpdb->get_results($query);
	}

	private function single_videos_total() {
		global $wpdb;

		$total_query = sprintf( 'SELECT SUM(%1$s.post_status="publish") AS publish,SUM(%1$s.post_status="draft") AS draft, %2$s.meta_value as title FROM `%2$s` 
JOIN %1$s ON %2$s.post_id=%1$s.ID 
WHERE %2$s.meta_key="sby_channel_title" 
GROUP BY %2$s.meta_value', $this->posts_table, $this->meta_table );

		$total_query = $wpdb->prepare($total_query);

		$wpdb->get_results($total_query);

		return $wpdb->num_rows;
	}
}