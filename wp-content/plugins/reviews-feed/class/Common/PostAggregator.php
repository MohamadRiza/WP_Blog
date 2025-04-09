<?php
/**
 * Class SinglePostCache
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;
use SmashBalloon\Reviews\Common\Helpers\Data_Encryption;
use SmashBalloon\Reviews\Pro\Integrations\Forms\ReviewsSubmissionsRules;

class PostAggregator {

	public const UPLOAD_FOLDER_NAME = 'sbr-feed-images';
	public const POSTS_TABLE_NAME = SBR_POSTS_TABLE;

	/**
	 * @var array
	 */
	private $post_set = array();

	private $upload_dir;

	private $upload_url;

	private $missing_media_found;

	public function __construct() {
		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . self::UPLOAD_FOLDER_NAME;
		$this->upload_dir = $upload_dir;

		$upload_url = trailingslashit( $upload['baseurl'] ) . self::UPLOAD_FOLDER_NAME;
		$this->upload_url = $upload_url;
		$this->missing_media_found = false;
	}

	public function missing_media_found() {
		return $this->missing_media_found;
	}

	public function add( $post_set ) {
		return $this->post_set = array_merge( $this->post_set, $post_set );
	}

	public function db_post_set( $requests_needed ) {
		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$where_clause = $this->build_provider_business_where_clause( $requests_needed );

		$results = $wpdb->get_results(
			"SELECT * FROM $table_name
					WHERE $where_clause ORDER BY time_stamp DESC LIMIT 150", ARRAY_A );

        $results  =  self::remove_duplicated_posts_list( $results  );
        return $results;
	}

    public static function remove_duplicated_posts_list( $posts_list, $type = 'db' ){
        $posts_db = [];
        $posts_list_result = [];
		$encryption = new Data_Encryption();
		foreach ($posts_list as $postKey => $post) {
			$post = is_array($post) ? $post : json_decode($encryption->maybe_decrypt($post), true);
			if(  $type === 'db' ){
				$post['post_content'] = $post['provider'] === 'facebook' ? $encryption->maybe_decrypt($post['post_content']) : $post['post_content'];
				$post['json_data'] = $post['provider'] === 'facebook' ? $encryption->maybe_decrypt($post['json_data']) : $post['json_data'];
                $post_json =  isset($post['json_data']) ? json_decode($post['json_data'], true) : null;
                $post_json['source'] = ['id' => $post['provider_id']];
            } else {
				$post_json = $post;
            }

            if( $post_json !== null ){
				$time = date('m_d_Y', $post_json['time'] );
                $search_value = $post_json['source']['id'] . '-' . $post_json['rating'] . '-' . $time . '-' . $post_json['reviewer']['name']. '-' . $post_json['provider']['name'];
                if( !in_array( $search_value, $posts_db ) ){
                    array_push($posts_db, $search_value );
                    array_push($posts_list_result, $post );
                }
            }
        }
        return $posts_list_result;
    }

    public static function remove_duplicated_posts_routine(){
        global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
        $posts_db = [];
        $posts_id_todelete = [];

		$results = $wpdb->get_results(
			"SELECT * FROM $table_name", ARRAY_A );

        foreach ($results  as $post) {
            $post_json =  isset($post['json_data']) ? json_decode( $post['json_data'], true ) : null;
            if( $post_json !== null ){
				$time = date('m_d_Y', $post_json['time'] );
                $search_value = $post['provider_id']  . '-' . $post_json['rating'] . '-' . $time . '-' . $post_json['reviewer']['name']. '-' . $post_json['provider']['name'];
                if( in_array( $search_value, $posts_db ) ){
                    array_push($posts_id_todelete, $post['id'] );
                }else{
                    array_push($posts_db, $search_value );
                }
            }
        }

        if( sizeof($posts_id_todelete) > 0 )  {
            $posts_ids = implode(',', $posts_id_todelete);
			$wpdb->query(
                "DELETE FROM $table_name WHERE id IN ($posts_ids)"
            );
        }


    }


	public function db_posts_for_media_finding_and_resizing_set( $requests_needed ) {
		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );

		$where_clause = $this->build_provider_business_where_clause( $requests_needed );

		$results = $wpdb->get_results(
			"SELECT * FROM $table_name
					WHERE $where_clause
					AND images_done = 0
					ORDER BY time_stamp DESC LIMIT 150", ARRAY_A );

		return $results;
	}



	public function normalize_db_post_set( $results ) {
		$normalized_set = array();
		foreach ( $results as $result ) {
			if ( ! empty( $result['json_data'] ) ) {
				$post = json_decode( $result['json_data'], true );
				if ( ! empty( $post ) ) {
					$post = self::add_local_image_urls( $post, $result );
				}
				if ( (int)$result['images_done'] === 0 ) {
					$this->missing_media_found = true;
				}
				$normalized_set[] = $post;
			}
		}

		return $normalized_set;
	}

	public function add_local_image_urls( $post, $result ) {
		$return     = $post;
		$base_url   = $this->upload_url;
		$resize_url = apply_filters( 'sbr_resize_url', trailingslashit( $base_url ) );

		if ( ! empty( $post['reviewer']['avatar'] ) ) {
			if ( ! empty( $result['avatar_id'] ) && $result['avatar_id'] !== 'error' ) {
				$return['reviewer']['avatar_local'] = $resize_url . $result['avatar_id']. '.png';
			}
		}

		if ( ! empty( $post['media'] ) ) {
			$sizes = ! empty( $result['sizes'] ) ? json_decode( $result['sizes'] ) : array();
			$i     = 0;
			foreach ( $post['media'] as $single_image ) {
				if ( ! empty( $result['media_id'] ) && $result['media_id'] !== 'error' ) {
					$return['media'][ $i ]['local'] = array();
					$media_id                       = $result['media_id'];
					foreach ( $sizes as $size ) {
						$local_url_for_size = $resize_url . $media_id . '-' . $i . '-' .  $size . '.jpg';
						$return['media'][ $i ]['local'][ $size ] = $local_url_for_size;
					}
				}
				$i ++;
			}
		}

		return $return;
	}

	public function update_last_requested( $requests_needed ) {
		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$where_clause = $this->build_provider_business_where_clause( $requests_needed );
		$query = $wpdb->query(
			$wpdb->prepare(
				"UPDATE $table_name
					SET last_requested = %s
					WHERE $where_clause;",
				date( 'Y-m-d' )

			)
		);

	}

	private function build_provider_business_where_clause( $requests_needed ) {

		$i            = 1;
		$where_clause = '';
		foreach ( $requests_needed as $request ) {
			$single_clause = sprintf( 'provider_id = "%s"', esc_sql( $request['account_id'] ) );
			if ( isset( $request['lang'] ) ) {
				$single_clause.= sprintf( ' AND lang = "%s"', esc_sql( $request['lang'] ) );
			}
			$where_clause .= '(' . $single_clause . ')';
			if ( $i <  count( $requests_needed ) ) {
				$where_clause .= ' OR ';
			}
			$i ++;
		}

		return $where_clause;
	}


	/**
	 * Get List Provider/Source Posts
	 *
	 * @param string $provider_id | int $page
	 *
	 * @return array
	 *
	 * @since 1.4
	 */
	public static function get_source_posts_list($provider_id, $page = 1)
	{
		global $wpdb;
		$table_name = esc_sql($wpdb->prefix . self::POSTS_TABLE_NAME);
		$limit = 40;
		$offset = ($page - 1) * $limit;
		$encryption = new Data_Encryption();

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM $table_name
				WHERE provider_id = %s
				ORDER BY id DESC
				LIMIT %d
				OFFSET %d
				",
				$provider_id,
				$limit,
				$offset
			),
			ARRAY_A
		);

		$decrypted_results = [];
		foreach ($results as $s_post) {
			if ($s_post['provider'] === 'facebook') {
				$s_post['post_content'] = $encryption->maybe_decrypt($s_post['post_content']);
				$s_post['json_data'] = $encryption->maybe_decrypt($s_post['json_data']);
				$json_data = json_decode($s_post['json_data'], true);

				if (isset($json_data['rating']) && !is_numeric($json_data['rating'])) {
					$rating = isset($json_data['rating']) && $json_data['rating'] === 'negative' ? 1 : 5;
					$json_data['rating'] = $rating;
				}

				$s_post['json_data'] = wp_json_encode($json_data);
			}
			array_push($decrypted_results,$s_post);
		}
		return $decrypted_results;
	}

	/**
	 * Delete Review from Collection
	 *
	 * @param string $provider_id | string $provider
	 *
	 * @return void
	 *
	 * @since 1.4
	 */
	public static function delete_review($provider_id, $review_id, $provider)
	{
		global $wpdb;
		$table_name = esc_sql($wpdb->prefix . self::POSTS_TABLE_NAME);
		$provider_value = $provider === 'collection' ? 'none' : $provider;

		ReviewsSubmissionsRules::remove_auto_approved($review_id, $provider_id, '', 'single', false);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name
				WHERE provider_id = %s AND provider = %s AND post_id = %s
				",
				$provider_id,
				$provider_value,
				$review_id
			)
        );
	}


	/**
	 * Select a List of reviews By Review ID
	 *
	 * @param array $reviews_ids
	 *
	 * @return array|boolean
	 *
	 * @since 1.4
	 */
	public static function get_list_reviews_by_ids($reviews_ids)
	{
		global $wpdb;
		$table_name  = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$reviews_ids = esc_sql( $reviews_ids );

		if (empty($reviews_ids)) {
			return false;
		}
		$reviews_ids_string = "'" . implode('\', \'', $reviews_ids) . "'";
		$results =
		$wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM $table_name
				WHERE post_id IN ($reviews_ids_string)
				"
			),
			ARRAY_A
		);
		return $results;
	}

	/**
	 * Select a List of reviews By Review ID
	 *
	 * @param string $provider_id | array $reviews_ids
	 *
	 * @return void
	 *
	 * @since 1.4
	 */
	public static function insert_multiple_reviews($provider_id, $reviews_ids)
	{
		$reviews_to_insert = PostAggregator::get_list_reviews_by_ids($reviews_ids);
		$encryption = new Data_Encryption();
		if (is_array($reviews_to_insert)) {
			foreach ($reviews_to_insert as $rev) {
				$review_id = $provider_id . time() . wp_rand();
				$json_data = $encryption->maybe_decrypt($rev['json_data']);
				$review = json_decode($json_data, true);
				$review_store = Util::parse_single_review($review, $provider_id, $review_id);
				$review_store['json_data'] = $review_store;

				$single_post_cache = new \SmashBalloon\Reviews\Pro\SinglePostCache($review_store, new \SmashBalloon\Reviews\Pro\MediaFinder($review_store['source']));
				$single_post_cache->set_provider_id($provider_id);
				$single_post_cache->store();
			}
		}
	}

	/**
	 * Delete All Reviews from Provider/Source ID
	 *
	 * @param string $provider_id
	 *
	 * @return void
	 *
	 * @since 1.4
	 */
	public static function delete_reviews_by_provide_id($provider_id)
	{
		global $wpdb;
		$table_name = esc_sql($wpdb->prefix . self::POSTS_TABLE_NAME);
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name
				WHERE provider_id = %s",
				$provider_id
			)
        );
	}

	/**
	 * Get List Provider/Source Posts
	 *
	 * @param string $provider_id
	 *
	 * @return array
	 *
	 * @since 1.4
	 */
	public static function get_source_all_posts($provider_id)
	{
		global $wpdb;
		$table_name = esc_sql($wpdb->prefix . self::POSTS_TABLE_NAME);
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM $table_name
				WHERE provider_id = %s
				",
				$provider_id
			),
			ARRAY_A
		);
		return $results;
	}


	/**
	 * Search Reviews of a specific Provider
	 *
	 * @param string $provider_id | string $search_text
	 *
	 * @return array
	 *
	 * @since 1.4
	 */
	public static function search_source_posts_list($provider_id, $search_text)
	{
		global $wpdb;
		$table_name = esc_sql($wpdb->prefix . self::POSTS_TABLE_NAME);
		$search_text_array = explode(' ', $search_text);
		$search_string = implode('.*', $search_text_array);
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM $table_name
				WHERE provider_id = %s
				AND json_data REGEXP '%s'
				",
				$provider_id,
				$search_string
			),
			ARRAY_A
		);
		return $results;
	}
}
