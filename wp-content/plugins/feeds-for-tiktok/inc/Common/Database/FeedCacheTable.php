<?php

/**
 * Table responsible for storing feed caches.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database;

class FeedCacheTable extends Table
{
	/**
	 * Custom table name.
	 *
	 * @var string
	 */
	protected const TABLE_NAME = SBTT_FEED_CACHES_TABLE;

	/**
	 * Table version.
	 *
	 * @var int
	 */
	protected const VERSION = 1;

	/**
	 * Set a default values for the table columns.
	 *
	 * @var array
	 */
	protected function defaults()
	{
		return [
			'feed_id'      => '',
			'cache_key'    => '',
			'cache_value'  => '',
			'cron_update'  => 'yes',
			'last_updated' => date('Y-m-d H:i:s'),
		];
	}

	/**
	 * Get the column formats.
	 *
	 * @return array
	 */
	protected function get_columns_format()
	{
		return array(
			'feed_id'      => '%s',
			'cache_key'    => '%s',
			'cache_value'  => '%s',
			'cron_update'  => '%s',
			'last_updated' => '%s',
		);
	}

	/**
	 * Create custom table.
	 *
	 * @return bool True if table exists if created.
	 */
	public function create_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$max_index_length = 191;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            feed_id varchar(255) NOT NULL DEFAULT '',
            cache_key varchar(255) NOT NULL DEFAULT '',
            cache_value longtext NOT NULL DEFAULT '',
            cron_update varchar(20) NOT NULL DEFAULT 'yes',
            last_updated datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY feed_id (feed_id($max_index_length))
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		return $this->table_exists();
	}

	/**
	 * Insert data into the table.
	 *
	 * @param array $data Data to insert.
	 *
	 * @return int|false
	 */
	public function insert($data)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$data = wp_parse_args($data, $this->defaults());

		$result = $wpdb->insert(
			$table_name,
			$data,
			$this->get_columns_format()
		);

		if (! $result) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Update data in the table.
	 *
	 * @param array $data Data to update.
	 * @param array $where Where clause.
	 *
	 * @return int|false
	 */
	public function update($data, $where)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$format       = $this->get_columns_format();
		$where_format = array();
		if (is_array($where)) {
			$sanitized_where = array();
			foreach ($where as $key => $value) {
				$sanitized_where[ $key ] = sanitize_text_field($value);
				$where_format[ $key ]    = isset($format[ $key ]) ? $format[ $key ] : '%s';
			}
			$where = $sanitized_where;
		} else {
			return false;
		}

		$result = $wpdb->update(
			$table_name,
			$data,
			$where,
			$format,
			$where_format
		);

		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Get data from the table.
	 *
	 * @param array $args Arguments to get data.
	 *
	 * @return array|false
	 */
	public function get_feed_cache($args = array())
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		if (! empty($args['feed_id']) && ! empty($args['cache_key'])) {
			$feed_id   = sanitize_text_field($args['feed_id']);
			$cache_key = sanitize_text_field($args['cache_key']);

			$sql = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE feed_id = %s AND cache_key = %s",
				$feed_id,
				$cache_key
			);
		} elseif (! empty($args['feed_id'])) {
			$feed_id = sanitize_text_field($args['feed_id']);

			$sql = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE feed_id = %s",
				$feed_id
			);
		} else {
			$sql = "SELECT * FROM $table_name";
		}

		$results = $wpdb->get_results($sql, ARRAY_A);

		if (! $results) {
			return false;
		}

		return $results;
	}

	/**
	 * Update or insert data into the table.
	 *
	 * @param array $data Data to update or insert.
	 */
	public function update_or_insert($data)
	{
		$feed_id   = isset($data['feed_id']) ? $data['feed_id'] : '';
		$cache_key = isset($data['cache_key']) ? $data['cache_key'] : '';
		$cache_value = isset($data['cache_value']) ? $data['cache_value'] : '';

		$existing_cache = $this->get_feed_cache(
			array(
				'feed_id'   => $feed_id,
				'cache_key' => $cache_key,
			)
		);

		if (! $existing_cache) {
			$this->insert($data);
		} else {
			// only update last updated and cache value.
			$data = array(
				'cache_value'  => $cache_value,
				'last_updated' => date('Y-m-d H:i:s'),
			);

			$this->update(
				$data,
				array(
					'feed_id'   => $feed_id,
					'cache_key' => $cache_key,
				)
			);
		}

		$this->maybe_create_backup($feed_id, $cache_key, $cache_value);
	}

	/**
	 * Check if backup should be created and create it.
	 *
	 * @param string $feed_id Feed ID.
	 * @param string $cache_key Cache key.
	 * @param string $cache_value Cache value.
	 * @return void
	 */
	public function maybe_create_backup($feed_id, $cache_key, $cache_value)
	{
		if ($feed_id === false || empty($cache_key) || empty($cache_value)) {
			return;
		}

		// if cache key is posts or header, create a backup.
		if (! in_array($cache_key, array( 'posts', 'header' ), true)) {
			return;
		}

		$backup_cache_key = $cache_key . '_backup';
		$existing_cache   = $this->get_feed_cache(
			array(
				'feed_id'   => $feed_id,
				'cache_key' => $backup_cache_key,
			)
		);

		if (!$existing_cache) {
			$data = array(
				'cache_value'  => $cache_value,
				'cache_key'    => $backup_cache_key,
				'last_updated' => date('Y-m-d H:i:s'),
				'cron_update'  => '',
				'feed_id'      => $feed_id,
			);

			$this->insert($data);
		} else {
			// only update last updated and cache value.
			$data = array(
				'cache_value'  => $cache_value,
				'last_updated' => date('Y-m-d H:i:s'),
			);

			$this->update(
				$data,
				array(
					'feed_id'   => $feed_id,
					'cache_key' => $backup_cache_key,
				)
			);
		}
	}

	/**
	 * Clear the cache for all feeds.
	 *
	 * @return void
	 */
	public function clear_feed_cache()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$result = $wpdb->query(
			"UPDATE $table_name SET cache_value = '', last_updated = '" . date('Y-m-d H:i:s') . "'
			WHERE cache_key NOT IN ('posts_backup', 'header_backup')"
		);

		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Get feeds for cron update.
	 *
	 * @return array|false
	 */
	public function get_feeds_to_update()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$sql = "SELECT DISTINCT feed_id FROM $table_name 
			WHERE cron_update = 'yes' 
			AND feed_id NOT LIKE '%_CUSTOMIZER'
			AND feed_id NOT LIKE '%_CUSTOMIZER_MODMODE'
			AND last_updated > DATE_SUB(NOW(), INTERVAL 1 MONTH)
			ORDER BY last_updated ASC";

		$results = $wpdb->get_results($sql, ARRAY_A);

		if (! $results) {
			return false;
		}

		return $results;
	}
}
