<?php

/**
 * Table responsible for storing sources settings.
 *
 * @package tiktok-feeds
 */

namespace SmashBalloon\TikTokFeeds\Common\Database;

use SmashBalloon\TikTokFeeds\Common\Database\Migrations\SourcesScopeFieldUpdate;

class SourcesTable extends Table
{
	/**
	 * Custom table name.
	 */
	protected const TABLE_NAME = SBTT_SOURCES_TABLE;

	/**
	 * Table version.
	 */
	protected const VERSION = 2;

	/**
	 * Set a default values for the table columns.
	 *
	 * @var array
	 */
	protected function defaults()
	{
		return [
			'open_id'         => '',
			'access_token'    => '',
			'refresh_token'   => '',
			'display_name'    => '',
			'info'            => '',
			'expires'         => 0,
			'refresh_expires' => 0,
			'scope'           => '',
			'last_updated'    => date('Y-m-d H:i:s'),
			'author'          => get_current_user_id(),
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
			'open_id'         => '%s',
			'access_token'    => '%s',
			'refresh_token'   => '%s',
			'display_name'    => '%s',
			'info'            => '%s',
			'expires'         => '%s',
			'refresh_expires' => '%s',
			'scope'           => '%s',
			'last_updated'    => '%s',
			'author'          => '%d',
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
            open_id varchar(255) NOT NULL DEFAULT '',
            access_token varchar(1000) NOT NULL DEFAULT '',
            refresh_token varchar(1000) NOT NULL DEFAULT '',
            display_name varchar(255) NOT NULL DEFAULT '',
            info longtext NOT NULL DEFAULT '',
            expires datetime NOT NULL,
			refresh_expires datetime NOT NULL,
			scope varchar(255) NOT NULL DEFAULT '',
            last_updated datetime NOT NULL,
            author bigint(20) unsigned NOT NULL DEFAULT '1',
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

		if (! empty($data['info'])) {
			$info = sbtt_sanitize_data($data['info']);
			$data['info'] = sbtt_json_encode($info);
		}

		$result = $wpdb->insert($table_name, $data, $this->get_columns_format());

		if (! $result) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Get data from the table.
	 *
	 * @param array $args Query arguments.
	 * @return array|false Array of results or false on failure.
	 */
	public function get_sources($args = array())
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		if (! empty($args['open_id'])) {
			if (is_array($args['open_id'])) {
				$open_id = array_map('sanitize_text_field', $args['open_id']);
				$open_id = implode("','", $open_id);
				$sql     = "SELECT * FROM $table_name WHERE open_id IN ('$open_id')";
			} else {
				$open_id = sanitize_text_field($args['open_id']);
				$sql     = $wpdb->prepare(
					"SELECT * FROM $table_name WHERE open_id = %s",
					$open_id
				);
			}
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
	 * Get single source from the table.
	 *
	 * @param int $open_id Source ID.
	 * @return array|false Array of results or false on failure.
	 */
	public function get_source($open_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$open_id       = sanitize_text_field($open_id);
		$cache_key     = 'get_source_by_id' . $open_id;
		$cached_result = wp_cache_get($cache_key, 'tiktok-feeds');

		if (false !== $cached_result) {
			return $cached_result;
		}

		$sql = $wpdb->prepare(
			"SELECT * FROM $table_name WHERE open_id = %s",
			$open_id
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
		$format     = $this->get_columns_format();

		if (! empty($data['info'])) {
			$info = sbtt_sanitize_data($data['info']);
			$data['info'] = sbtt_json_encode($info);
		}
		$data['last_updated'] = date('Y-m-d H:i:s');

		// filter the format to match the data keys to prevent mismatched types.
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
	 * @return int|false Number of rows updated or false on failure.
	 */
	public function update_or_insert($data)
	{
		$open_id = isset($data['open_id']) ? sanitize_text_field($data['open_id']) : false;

		if ($open_id) {
			$source = $this->get_source($open_id);
		} else {
			$source = false;
		}

		if ($source) {
			return $this->update($data, array('open_id' => $open_id));
		}

		return $this->insert($data);
	}

	/**
	 * Delete data from the table.
	 *
	 * @param int $source_id Source ID to delete.
	 * @return int|false Number of rows deleted or false on failure.
	 */
	public function delete_source($source_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$source_id = absint($source_id);

		$result = $wpdb->delete($table_name, array( 'id' => $source_id ), array( '%d' ));

		if (! $result) {
			return false;
		}

		return $result;
	}

	/**
	 * Get migrations.
	 */
	public function get_migrations()
	{
		return array(
			2 => SourcesScopeFieldUpdate::class
		);
	}
}
