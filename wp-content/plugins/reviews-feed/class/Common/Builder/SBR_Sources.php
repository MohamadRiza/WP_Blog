<?php
/**
 * Reviews Sources Management
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Builder;

use SmashBalloon\Reviews\Common\Clear_Cache;
use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\PostAggregator;

class SBR_Sources{

	/**
	 * Add a new source as a row in the sbi_sources table
	 *
	 * @param array $source_data
	 *
	 * @return false|int
	 *
	 * @since 1.0
	 */
	public static function insert($source_data){
		$db = new DB();
		if (isset($source_data['id'])) {
			$source_data['account_id'] = $source_data['id'];
			unset($source_data['id']);
		}
		$data = $source_data;

		return $db->source_insert($data);
	}

	/**
	 * Whether or not the source exists in the database
	 *
	 * @param array $args
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function exists_in_database($args){
		$db = new DB();
		$results = $db->get_single_source($args);
		return isset($results[0]);
	}


     /**
     * Get Single Source
     *
     * @param array $args
     *
     * @return array
     *
     * @since 6.0
     */
    public static function get_single_source_info($args)
    {
        $db = new DB();
        $results = $db->get_single_source($args);
        return isset($results[0]) ? $results[0] : [];
    }

    /**
	 * Used to update or insert connected accounts (sources)
	 *
	 * @param array $source_data
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public static function update_or_insert( $source_data ) {
		if ( ! isset( $source_data['id'] ) ) {
			return false;
		}

		if ( isset( $source_data ) ) {
			// data from an API request related to the source is saved as a JSON string
			if ( is_object( $source_data ) || is_array( $source_data ) ) {
				$source_data['info'] = sbr_json_encode( $source_data );
			}
		}

		if ( self::exists_in_database( $source_data ) ) {
			$source_data['last_updated'] = date( 'Y-m-d H:i:s' );
			self::update( $source_data, false );
		} else {
			self::insert( $source_data );
		}

		return true;
	}

	/**
	 * Update info in rows that match the source data
	 *
	 * @param array $source_data
	 *
	 * @return false|int
	 *
	 * @since 6.0
	 */
	public static function update( $source_data) {
		$where = array(
			'id' => $source_data['id'],
			'provider' => $source_data['provider']
		 );

		$data = $source_data;
		$db = new DB();
		return $db->source_update( $data, $where );
	}


	/**
	 * Update info in rows that match the source data
	 *
	 * @param array $source_data
	 *
	 * @return false|int
	 *
	 * @since 6.0
	 */
	public static function delete_source( $source_id) {
		$db = new DB();
		return $db->delete_source( $source_id );
	}

	/**
	 * Get Sources
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public static function get_sources_list( $args = [] ){
		$db = new DB();
		return $db->source_query( $args );
	}

	/**
	 * Get Sources Count
	 *
	 * @return int
	 *
	 * @since 1.0
	 */
	public static function get_sources_count()
	{
		$db = new DB();
		return $db->source_query_count();
	}




	/**
	 * Update Collection Info
	 * Number of Reviews and Ratings average
	 *
	 * @return array|boolean
	 *
	 * @since 1.0
	 */
	public static function update_collection_ratings($account_id)
	{
		$db = new DB();
		$args = [
			'id' => $account_id,
			'provider' => 'collection'
		];
		$results = $db->get_single_source($args);
		if (!isset($results[0])) {
			return false;
		}
		$collection = $results[0];

		if (isset($collection['instances']) && sizeof($collection['instances']) > 0) {
			$feed_ids = [];
			foreach ($collection['instances'] as $instance) {
				array_push(
					$feed_ids,
					$instance['id'], $instance['id'] . '_CUSTOMIZER'
				);
			}
			unset($collection['instances']);
			unset($collection['used_in']);
			Clear_Cache::clear_feed_caches_by_id($feed_ids);
		}

		$reviews_list = PostAggregator::get_source_all_posts($account_id);
		$rating = 0;
		$total_rating = sizeof($reviews_list);

		foreach ($reviews_list as $review) {
			$rating += isset($review['rating']) ? intval($review['rating']) : 0;
		}

		$info_collection = json_decode($collection['info'], true);
		$info_collection['total_rating'] = $total_rating;
		$info_collection['rating'] = $total_rating > 0 ? ceil($rating / $total_rating) : 0;


		$collection['last_updated'] = date( 'Y-m-d H:i:s' );
		$collection['id'] = $account_id;
		$collection['info'] = json_encode($info_collection);
		SBR_Sources::update($collection);

		return $collection;

	}

	/**
	 * Get Sources List By Provider
	 *
	 * @param array $providers
	 *
	 * @return array
	 *
	 * @since 2.0
	 */
	public static function sources_by_providers($providers = [])
	{
		global $wpdb;
		$source_table = $wpdb->prefix . SBR_SOURCES_TABLE;
		$provider_list = implode(',', $providers);

		$sql = $wpdb->prepare("
			SELECT * FROM $source_table
			WHERE provider IN (%d);",
			$provider_list
		);
		return $wpdb->get_results($sql, ARRAY_A);
	}

	/**
	 * Check Source reviews were already retrieved
	 *
	 * @param string $provider
	 * @param string $provider_id
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public static function already_fetched($provider, $provider_id)
	{
		global $wpdb;
		$posts_table = $wpdb->prefix . SBR_POSTS_TABLE;
		$sql = $wpdb->prepare(
			"SELECT COUNT(*) FROM $posts_table
			WHERE provider = %s AND provider_id = %s",
			esc_sql($provider),
			esc_sql($provider_id)
		);

		return $wpdb->get_var($sql) > 0;
	}

	/**
	 * Check Source reviews were already retrieved
	 *	The Current Week
	 *
	 * @param string $provider
	 * @param string $provider_id
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public static function already_fetched_week($provider, $provider_id)
	{
		global $wpdb;
		$posts_table = $wpdb->prefix . SBR_POSTS_TABLE;

		$sql = $wpdb->prepare(
			"SELECT COUNT(*) FROM $posts_table
			WHERE provider = %s
			AND provider_id = %s
			AND created_on > %s
			",
			esc_sql($provider),
			esc_sql($provider_id),
			date('Y-m-d', strtotime('-1 week', time()))
		);
		return $wpdb->get_var($sql) > 0;
	}
}