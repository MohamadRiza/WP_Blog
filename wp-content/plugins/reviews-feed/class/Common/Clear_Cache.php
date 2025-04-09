<?php
/**
 * Clear Cache data in the DB
 *
 */

namespace SmashBalloon\Reviews\Common;

use Smashballoon\Stubs\Services\ServiceProvider;

class Clear_Cache extends ServiceProvider
{
	public function register()
	{
		add_action( 'wp_ajax_sbr_clear_post_header_cache', [ $this , 'sbr_clear_post_header_cache' ] );
		add_action( 'wp_ajax_sbr_reset_posts', [ $this, 'sbr_reset_posts' ] );
	}

	private static function clear_data($table_name, $column, $where_clause)
	{
		global $wpdb;
		$table_full_name = $wpdb->prefix . $table_name;

		$sql = "
		UPDATE $table_full_name
		SET $column = ''
		$where_clause";
		$wpdb->query( $sql );
	}

	private static function drop_table($table_name)
	{
		global $wpdb;
		$table_full_name = $wpdb->prefix . $table_name;

		$sql = "DROP TABLE IF EXISTS $table_full_name";
		$wpdb->query( $sql );
	}

	public function sbr_clear_post_header_cache()
	{
        check_ajax_referer('sbr-admin', 'nonce');
        if (!sbr_current_user_can('manage_reviews_feed_options')) {
            wp_send_json_error();
        }

		$this->clear_post_header_cache();

		wp_send_json( ['success' => true], 200 );
		wp_die();
	}

	public function sbr_reset_posts()
	{
        check_ajax_referer('sbr-admin', 'nonce');

        if (!sbr_current_user_can('manage_reviews_feed_options')) {
            wp_send_json_error();
        }


        SinglePostCache::delete_resizing_table_and_images();
		SinglePostCache::create_resizing_table_and_uploads_folder();
		$this->sbr_clear_post_header_cache();
		wp_send_json( ['success' => true], 200 );
		wp_die();
	}

	public function clear_post_header_cache()
	{
		static::clear_data(
			"sbr_feed_caches",
			"cache_value",
			"WHERE cache_key NOT IN ( 'posts_backup', 'header_backup' );"
		);
	}


	public static function clear_feed_caches_by_id($feeds_ids)
	{
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbr_feed_caches';
		$feeds_ids_string = "'" . implode('\', \'', $feeds_ids) . "'";
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE cache_key NOT IN ( 'posts_backup', 'header_backup' )
				AND feed_id IN ($feeds_ids_string)
				"
			)
		);

	}
}
