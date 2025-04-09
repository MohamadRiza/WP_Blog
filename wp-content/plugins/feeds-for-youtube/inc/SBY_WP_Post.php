<?php
namespace SmashBalloon\YouTubeFeed;
use SmashBalloon\YouTubeFeed\Pro\SBY_Parse_Pro;
use Smashballoon\Customizer\YouTube_License_Tier;

class SBY_WP_Post
{
	private $youtube_api_data;

	private $feed_id;

	private $wp_post_id;

	public function __construct( $json_or_array, $feed_id ) {
		$this->youtube_api_data = $json_or_array;
		$this->feed_id = $feed_id;
	}

	public function update_post( $status = 'draft' ) {
		// YouTube license tier 
		$license_tier = new YouTube_License_Tier;
		$license_tier_features = $license_tier->tier_features();

		// Get global settings
		$sby_settings = sby_get_database_settings();
		$wp_posts_disabled = !empty($sby_settings['disable_wp_posts']);

		// Do not create posts if feature is not available in the tier or WP Posts creation disabled
		if ($wp_posts_disabled) {
			return;
		}

		// Add a filter to get the post type
		$post_type = apply_filters('sby_single_videos_post_type', SBY_CPT);
		$post_status = apply_filters('sby_single_videos_post_status', $status);

		if ( ! $this->get_wp_post_id() ) {
			$postarr = array(
				'post_title' => SBY_Parse::get_video_title( $this->youtube_api_data ),
				'post_content' => $this->get_post_content(),
				'post_type' => $post_type,
				'post_date' => date( 'Y-m-d H:i:s', SBY_Parse::get_timestamp( $this->youtube_api_data ) + sby_get_utc_offset() ),
				'post_date_gmt' => date( 'Y-m-d H:i:s', SBY_Parse::get_timestamp( $this->youtube_api_data ) ),
				'post_status' => $post_status
			);
			$wp_post_id = wp_insert_post( $postarr );

			if ( (int)$wp_post_id > 0 ) {
				$this->wp_post_id = $wp_post_id;

				$success = $this->update_meta();

				do_action( 'sby_after_insert_video_post', $wp_post_id, $this->youtube_api_data );
			}
		} else {
			do_action( 'sby_after_update_video_post', $this->wp_post_id, $this->youtube_api_data );
		}

	}

	public function update_meta() {
		if ( ! isset( $this->wp_post_id ) ) {
			return false;
		}

		$prefix = 'sby_';

		update_post_meta( $this->wp_post_id, $prefix . 'video_id', SBY_Parse::get_video_id( $this->youtube_api_data ) );
		update_post_meta( $this->wp_post_id, $prefix . 'feed_id', sby_strip_after_hash( $this->feed_id ) );
		update_post_meta( $this->wp_post_id, $prefix . 'channel_id', SBY_Parse::get_channel_id( $this->youtube_api_data ) );
		update_post_meta( $this->wp_post_id, $prefix . 'channel_title', SBY_Parse::get_channel_title( $this->youtube_api_data ) );
		update_post_meta( $this->wp_post_id, $prefix . 'description', SBY_Parse::get_caption( $this->youtube_api_data ) );
		update_post_meta( $this->wp_post_id, $prefix . 'last_updated', date( 'Y-m-d H:i:s' ) );
		update_post_meta( $this->wp_post_id, $prefix . 'thumbnails', SBY_Parse::get_media_src_set( $this->youtube_api_data ) );
		update_post_meta( $this->wp_post_id, $prefix . 'youtube_publish_date', date( 'Y-m-d H:i:s', SBY_Parse::get_timestamp( $this->youtube_api_data ) ) );

		if ( sby_is_pro_version() ) {
			update_post_meta( $this->wp_post_id, $prefix . 'live_broadcast_content', SBY_Parse_Pro::get_live_broadcast_content( $this->youtube_api_data ) );
		}

		$post_array = $this->youtube_api_data;

		array_walk_recursive( $post_array, [$this, 'sby_replace_double_quotes'] );

		update_post_meta( $this->wp_post_id, $prefix . 'json', esc_sql( wp_json_encode( $post_array ) ) );

		return true;
	}

	public function post_content_description_is_incomplete() {
		$content = get_the_content( '', false, $this->get_wp_post_id() );

		return (strpos( $content, '<!-- sby:description-incomplete -->' ) !== false);
	}

	public function update_video_details() {
		if ( empty( $this->youtube_api_data['statistics'] ) ) {
			return false;
		}

		$post_id = $this->get_wp_post_id();

		if ( $post_id ) {
			$prefix = 'sby_';

			if ( sby_is_pro_version() ) {
				update_post_meta( $post_id, $prefix . 'comment_count', SBY_Parse_Pro::get_comment_count( $this->youtube_api_data ) );
				update_post_meta( $post_id, $prefix . 'view_count', SBY_Parse_Pro::get_view_count( $this->youtube_api_data ) );
				update_post_meta( $post_id, $prefix . 'like_count', SBY_Parse_Pro::get_like_count( $this->youtube_api_data ) );
				update_post_meta( $post_id, $prefix . 'scheduled_start_time', date( 'Y-m-d H:i:s', SBY_Parse_Pro::get_scheduled_start_timestamp( $this->youtube_api_data ) )  );
				update_post_meta( $post_id, $prefix . 'actual_start_time', date( 'Y-m-d H:i:s', SBY_Parse_Pro::get_actual_start_timestamp( $this->youtube_api_data ) )  );
				update_post_meta( $post_id, $prefix . 'actual_end_time', date( 'Y-m-d H:i:s', SBY_Parse_Pro::get_actual_end_timestamp( $this->youtube_api_data ) )  );
			}

			update_post_meta( $post_id, $prefix . 'last_details_check_time', date( 'Y-m-d H:i:s' ) );
			$description = SBY_Parse::get_caption( $this->youtube_api_data );
			if ( ! empty( $description ) ) {
				update_post_meta( $post_id, $prefix . 'description', $description );
				if ( $this->post_content_description_is_incomplete() ) {
					$this->update_content();
				}
			}
			return true;
		}

		return false;
	}
	public function update_content() {
		$my_post = array();
		$my_post['ID'] = $this->get_wp_post_id();
		$my_post['post_content'] = $this->get_post_content( true );

		wp_update_post( $my_post );
	}

	public static function maybe_get_channel_id_for_name( $name ) {

	}

	public static function maybe_get_channel_id_for_channel_title( $title ) {
		global $wpdb;

		$channel_id = $wpdb->get_col( $wpdb->prepare( "
        SELECT Max(CASE
			WHEN m.meta_key = 'sby_channel_id' THEN m.meta_value
			ELSE NULL
			END) AS sby_channel_id
        FROM $wpdb->postmeta as m
        WHERE m.post_id IN (SELECT m2.post_id FROM $wpdb->postmeta as m2 WHERE m2.meta_key = 'sby_channel_title' AND m2.meta_value = %s )
        GROUP BY m.post_id
        LIMIT 1", $title ) );

		if ( isset( $channel_id[0] ) ) {
			return $channel_id[0];
		}
		return false;
	}

	public function get_wp_post_id() {
		if ( isset( $this->wp_post_id ) ) {
			return $this->wp_post_id;
		}

		$video_id = SBY_Parse::get_video_id( $this->youtube_api_data );

		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = %s", $video_id, 'sby_video_id' ), ARRAY_A );

		if ( isset( $results[0] ) ) {
			$this->wp_post_id = $results[0]['post_id'];

			return $results[0]['post_id'];
		} else {
			return false;
		}
	}

	protected function get_post_content( $override = false ) {
		$description = SBY_Parse::get_caption( $this->youtube_api_data );
		$flag = '';
		if ( ! $override && substr( $description, -3 ) === '...' ) {
			$flag = '<!-- sby:description-incomplete -->';
		}

		$content = '['.SBY_SLUG.'-single]<!-- sby:description-start -->' . wp_kses_post( make_clickable( wp_encode_emoji( $description ) ) ) . '<!-- sby:description-end -->' . $flag;

		$content = apply_filters( 'sby_wp_post_content', $content, $this->youtube_api_data, $this->wp_post_id );

		return $content;
	}

	public function sby_replace_double_quotes( &$element, $index ) {
		$element = str_replace( array( '"', "\nn", "\n" ), array( "&quot;", '<br />', '<br />' ), $element );
	}
}
