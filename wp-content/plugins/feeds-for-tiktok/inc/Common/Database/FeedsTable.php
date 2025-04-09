<?php

/**
 * Table responsible for storing feeds settings.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database;

class FeedsTable extends Table
{
	/**
	 * Custom table name.
	 *
	 * @var string
	 */
	protected const TABLE_NAME = SBTT_FEEDS_TABLE;

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
			'feed_name'     => '',
			'feed_title'    => '',
			'settings'      => '',
			'feed_style'    => '',
			'author'        => get_current_user_id(),
			'status'        => 'publish',
			'last_modified' => date('Y-m-d H:i:s'),
		];
	}

	/**
	 * Get the column formats.
	 *
	 * @return array
	 */
	protected function get_columns_format()
	{
		return [
			'feed_name'     => '%s',
			'feed_title'    => '%s',
			'settings'      => '%s',
			'feed_style'    => '%s',
			'author'        => '%d',
			'status'        => '%s',
			'last_modified' => '%s',
		];
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

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            feed_name varchar(255) NOT NULL DEFAULT '',
            feed_title varchar(255) NOT NULL DEFAULT '',
            settings longtext NOT NULL DEFAULT '',
            feed_style longtext NOT NULL DEFAULT '',
            author bigint(20) unsigned NOT NULL DEFAULT '1',
            status varchar(255) NOT NULL DEFAULT '',
            last_modified datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY author (author)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		return $this->table_exists();
	}

	/**
	 * Insert data into the table.
	 *
	 * @param array $data Data to insert.
	 * @return int|false Inserted row ID or false on failure.
	 */
	public function insert($data)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$data = wp_parse_args($data, $this->defaults());

		if (! empty($data['settings'])) {
			$data['settings'] = sbtt_json_encode($data['settings']);
		}

		$result = $wpdb->insert($table_name, $data, $this->get_columns_format());

		if (! $result) {
			return false;
		}

		// action hook for when a feed is created.
		do_action('sbtt_feed_created', $wpdb->insert_id);

		return $wpdb->insert_id;
	}

	/**
	 * Get data from the table.
	 *
	 * @param array $args Query arguments.
	 * @return array|false Array of results or false on failure.
	 */
	public function get_feeds($args = array())
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		if (! empty($args['id'])) {
			$args['id'] = absint($args['id']);

			$sql = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$args['id']
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
	 * Get single feed from the table.
	 *
	 * @param int $feed_id Feed ID.
	 * @return array|false Array of results or false on failure.
	 */
	public function get_feed($feed_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$feed_id       = absint($feed_id);
		$cache_key     = 'get_feed_by_id' . $feed_id;
		$cached_result = wp_cache_get($cache_key, 'tiktok-feeds');

		if (false !== $cached_result) {
			return $cached_result;
		}

		$sql = $wpdb->prepare(
			"SELECT * FROM $table_name WHERE id = %d",
			$feed_id
		);

		$results = $wpdb->get_results($sql, ARRAY_A);

		if (! $results) {
			return false;
		}

		wp_cache_set($cache_key, $results[0], 'tiktok-feeds', 60 * 60 * 24);
		return $results[0];
	}


	/**
	 * Update data in the table.
	 *
	 * @param array $data Data to update.
	 * @param array $where Where clause.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public function update($data, $where)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$data = wp_parse_args($data, $this->defaults());

		if (! empty($data['settings'])) {
			$data['settings'] = sbtt_json_encode($data['settings']);
		}

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

		$result = $wpdb->update($table_name, $data, $where, $format, $where_format);

		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Check whether to update or insert data.
	 *
	 * @param array $data Data to update or insert.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public function update_or_insert($data)
	{
		$feed_id = isset($data['id']) ? absint($data['id']) : false;

		if ($feed_id) {
			$feed = $this->get_feed($feed_id);
		} else {
			$feed = false;
		}

		if ($feed) {
			return $this->update($data, array( 'id' => $feed_id ));
		}

		return $this->insert($data);
	}

	/**
	 * Delete data from the table.
	 *
	 * @param array $feed_ids Feed IDs to delete.
	 * @return int|false Number of rows deleted or false on failure.
	 */
	public function delete_feeds($feed_ids)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$feed_ids = implode(',', $feed_ids);

		$sql    = "DELETE FROM $table_name WHERE id IN ($feed_ids)";
		$result = $wpdb->query($sql);

		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Get feeds count.
	 *
	 * @return int|false Number of feeds or false on failure.
	 */
	public function get_feeds_count()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql = "SELECT COUNT(*) FROM $table_name";

		$result = $wpdb->get_var($sql);

		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Duplicate feed.
	 *
	 * @param int $feed_id Feed ID.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public function duplicate_feed($feed_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$feed_id = absint($feed_id);

		$sql = $wpdb->prepare(
			"INSERT INTO $table_name (feed_name, feed_title, settings, feed_style, author, status) 
			SELECT CONCAT(feed_name, ' - Copy'), feed_title, settings, feed_style, author, status 
			FROM $table_name WHERE id = %d",
			$feed_id
		);

		$result = $wpdb->query($sql);

		if (! $result) {
			return false;
		}

		return $wpdb->insert_id;
	}
}
