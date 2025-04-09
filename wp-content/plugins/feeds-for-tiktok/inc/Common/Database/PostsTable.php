<?php

/**
 * Table responsible for storing posts.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database;

class PostsTable extends Table
{
	/**
	 * Custom table name.
	 *
	 * @var string
	 */
	protected const TABLE_NAME = SBTT_TIKTOK_POSTS_TABLE;

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
			'video_id'       => '',
			'open_id'        => '',
			'created_on'     => date('Y-m-d H:i:s'),
			'time_stamp'     => date('Y-m-d H:i:s'),
			'json_data'      => '',
			'views'          => '',
			'likes'          => '',
			'images_done'    => 0,
			'last_requested' => date('Y-m-d H:i:s'),
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
			'video_id'       => '%s',
			'open_id'        => '%s',
			'created_on'     => '%s',
			'time_stamp'     => '%s',
			'json_data'      => '%s',
			'views'          => '%d',
			'likes'          => '%d',
			'images_done'    => '%d',
			'last_requested' => '%s',
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

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            video_id VARCHAR(1000) DEFAULT '' NOT NULL,
			open_id varchar(255) NOT NULL DEFAULT '',
            created_on DATETIME NOT NULL,
            time_stamp DATETIME NOT NULL,
            json_data LONGTEXT DEFAULT '' NOT NULL,
			views bigint(20) unsigned DEFAULT 0 NOT NULL,
			likes bigint(20) unsigned DEFAULT 0 NOT NULL,
            images_done TINYINT(1) DEFAULT 0 NOT NULL,
            last_requested DATETIME NOT NULL,
            PRIMARY KEY  (id),
            INDEX video_id (video_id),
			INDEX open_id (open_id)
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
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function insert($data)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$data = wp_parse_args($data, $this->defaults());

		if (! empty($data['json_data'])) {
			$data['json_data'] = sbtt_json_encode($data['json_data']);
		}

		$result = $wpdb->insert($table_name, $data, $this->get_columns_format());

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
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function update($data, $where)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		if (! empty($data['json_data'])) {
			$data['json_data'] = sbtt_json_encode($data['json_data']);
		}
		$data['last_requested'] = date('Y-m-d H:i:s');

		$format         = $this->get_columns_format();
		$filtered_format = array_filter(
			$format,
			function ($key) use ($data) {
				return array_key_exists($key, $data);
			},
			ARRAY_FILTER_USE_KEY
		);

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

		$result = $wpdb->update($table_name, $data, $where, $filtered_format, $where_format);
		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Check whether to update or insert data.
	 *
	 * @param array $data Data to update or insert.
	 *
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function update_or_insert($data)
	{
		$video_id = isset($data['video_id']) ? sanitize_text_field($data['video_id']) : false;

		if ($video_id) {
			$post = $this->get_post($video_id);
		} else {
			$post = false;
		}

		if ($post) {
			$result = $this->update($data, array('video_id' => $video_id));
		} else {
			$result = $this->insert($data);
		}

		return $result;
	}

	/**
	 * Get a single post from the database.
	 *
	 * @param string $video_id Video ID.
	 *
	 * @return array|object|null
	 */
	public function get_post($video_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$video_id = sanitize_text_field($video_id);
		$query    = "SELECT * FROM $table_name WHERE video_id = %s";

		return $wpdb->get_row($wpdb->prepare($query, $video_id));
	}

	/**
	 * Get posts from the database.
	 *
	 * @param array $args Arguments to pass to the query.
	 *
	 * @return array|object|null
	 */
	public function get_posts($args = array())
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		if (! empty($args['id'])) {
			$args['id'] = is_array($args['id']) ? $args['id'] : array($args['id']);
			$ids = implode(',', array_map('absint', $args['id']));
			$sql = "SELECT * FROM $table_name WHERE video_id IN ($ids)";
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
	 * Delete all the posts from the database.
	 *
	 * @return bool
	 */
	public function delete_all_posts()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$query = "DELETE FROM $table_name";

		return $wpdb->query($query);
	}

	/**
	 * Update the images_done to 0 for all posts.
	 *
	 * @return bool
	 */
	public function reset_images_done()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$query = "UPDATE $table_name SET images_done = 0";

		return $wpdb->query($query);
	}
}
