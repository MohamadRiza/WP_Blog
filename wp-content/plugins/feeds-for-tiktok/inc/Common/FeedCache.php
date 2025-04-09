<?php

namespace SmashBalloon\TikTokFeeds\Common;

use SmashBalloon\TikTokFeeds\Common\Database\FeedCacheTable;

class FeedCache
{
	/**
	 * Cache key.
	 */
	protected const CACHE_KEY = 'sbtt_feed_cache_';

	/**
	 * Feed ID.
	 *
	 * @var int
	 */
	protected $feed_id;

	/**
	 * Page number.
	 *
	 * @var int
	 */
	protected $page;

	/**
	 * Suffix.
	 *
	 * @var string
	 */
	protected $suffix;

	/**
	 * Cache time.
	 *
	 * @var int
	 */
	private $cache_time;

	/**
	 * Whether or not the cache is expired.
	 *
	 * @var bool
	 */
	private $is_expired = true;

	/**
	 * Posts.
	 *
	 * @var array
	 */
	private $posts;

	/**
	 * Posts page.
	 *
	 * @var array
	 */
	private $posts_page;

	/**
	 * Header.
	 *
	 * @var array
	 */
	private $header;

	/**
	 * Posts backup.
	 *
	 * @var array
	 */
	private $posts_backup;

	/**
	 * Header backup.
	 *
	 * @var array
	 */
	private $header_backup;

	/**
	 * Errors.
	 *
	 * @var array
	 */
	private $errors;

	/**
	 * Posts cursor.
	 *
	 * @var string
	 */
	private $posts_cursor;

	/**
	 * Constructor.
	 *
	 * @param int $feed_id Feed ID.
	 * @param int $cache_time Cache time.
	 *
	 * @return void
	 */
	public function __construct($feed_id, $cache_time = 0)
	{
		$this->feed_id    = str_replace('*', '', $feed_id);
		$this->cache_time = (int) $cache_time;
		$this->page       = 1;
		$this->suffix     = $this->page !== 1 ? '_' . $this->page : '';

		if (is_admin()) {
			$this->feed_id .= $this->maybe_customizer_suffix();
		}
	}

	/**
	 * Set all caches based on available data.
	 *
	 * @return void
	 */
	public function retrieve_and_set_feed_cache()
	{
		$expired         = true;
		$existing_caches = $this->get_existing_cache();

		if ($existing_caches === false) {
			$this->is_expired = true;
			return;
		}

		foreach ($existing_caches as $cache) {
			switch ($cache['cache_key']) {
				case 'posts':
					$this->posts = $cache['cache_value'];

					if (strtotime($cache['last_updated']) > time() - $this->cache_time) {
						$expired = false;
					}

					if (empty($cache['cache_value'])) {
						$expired = true;
					}
					break;

				case 'posts' . $this->suffix:
					$this->posts_page = $cache['cache_value'];
					break;

				case 'header':
					$this->header = $cache['cache_value'];

					if (empty($cache['cache_value'])) {
						$expired = true;
					}
					break;

				case 'errors' . $this->suffix:
					$this->errors = $cache['cache_value'];
					break;

				case 'posts_cursor':
					$this->posts_cursor = $cache['cache_value'];
					break;

				case 'posts_backup' . $this->suffix:
					$this->posts_backup = $cache['cache_value'];
					break;

				case 'header_backup' . $this->suffix:
					$this->header_backup = $cache['cache_value'];
					break;
			}
		}

		$this->is_expired = $expired;

		if ($this->cache_time < 1) {
			$this->is_expired = true;
		}
	}

	/**
	 * Whether or not the cache needs to be refreshed
	 *
	 * @param string $cache_type Cache type.
	 *
	 * @return bool
	 */
	public function is_expired($cache_type = 'posts')
	{
		if ($cache_type !== 'posts') {
			$cache = $this->get($cache_type);

			return ( empty($cache) || $this->is_expired );
		}

		if ($this->page > 1 && empty($this->posts_page)) {
			return true;
		}

		return $this->is_expired;
	}

	/**
	 * Check if the feed cache is expired and needs to be refreshed, but has no errors.
	 *
	 * @return bool
	 */
	public function is_expired_with_no_errors()
	{
		if ($this->is_expired() === true && empty($this->errors)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if backup exists.
	 *
	 * @return bool
	 */
	public function backup_exists()
	{
		return ! empty($this->posts_backup) && ! empty($this->header_backup);
	}

	/**
	 * Get data currently stored in the database for the type
	 *
	 * @param string $type Type.
	 *
	 * @return string
	 */
	public function get($type)
	{
		$return = array();

		switch ($type) {
			case 'posts':
				$return = $this->posts;
				break;

			case 'posts' . $this->suffix:
				$return = $this->posts_page;
				break;

			case 'header':
				$return = $this->header;
				break;

			case 'posts_backup':
				$return = $this->posts_backup;
				break;

			case 'header_backup':
				$return = $this->header_backup;
				break;

			case 'errors':
				$return = $this->errors;
				break;

			case 'posts_cursor':
				$return = $this->posts_cursor;
				break;
		}

		return $return;
	}

	/**
	 * Set the cache value
	 *
	 * @param string $type 	  Type.
	 * @param array  $cache_value Cache value.
	 */
	public function set($type, $cache_value)
	{
		switch ($type) {
			case 'posts':
				$this->posts = $cache_value;
				break;

			case 'posts' . $this->suffix:
				$this->posts_page = $cache_value;
				break;

			case 'header':
				$this->header = $cache_value;
				break;

			case 'posts_backup':
				$this->posts_backup = $cache_value;
				break;

			case 'header_backup':
				$this->header_backup = $cache_value;
				break;

			case 'errors':
				$this->errors = $cache_value;
				break;

			case 'posts_cursor':
				$this->posts_cursor = $cache_value;
				break;
		}
	}

	/**
	 * Save the cache to the database
	 *
	 * @param string              $cache_type Cache type.
	 * @param array|object|string $cache_value Cache value.
	 * @param bool                $cron_update Whether or not this is a cron update.
	 */
	public function update_or_insert($cache_type, $cache_value, $cron_update = true)
	{
		$this->delete_feed_cache();

		if ($this->page > 1 || ( $cache_type !== 'posts' && $cache_type !== 'header' )) {
			$cron_update = false;
		}

		if (strpos($this->feed_id, '_CUSTOMIZER') !== false) {
			$cron_update = false;
		}

		$cache_key = $cache_type === 'posts' ? $cache_type . $this->suffix : $cache_type;

		$this->set($cache_key, $cache_value);

		if (is_array($cache_value) || is_object($cache_value)) {
			$cache_value = wp_json_encode($cache_value);
		}

		$data = array(
			'cache_key'    => $cache_key,
			'cache_value'  => $cache_value,
			'last_updated' => date('Y-m-d H:i:s'),
			'cron_update'  => $cron_update === true ? 'yes' : '',
			'feed_id'      => $this->feed_id,
		);

		$cache_table = new FeedCacheTable();
		$result      = $cache_table->update_or_insert($data);

		return $result;
	}

	/**
	 * Clear the cache
	 *
	 * @param string $cache_type Cache type.
	 * @return bool
	 */
	public function clear($cache_type)
	{
		$this->delete_feed_cache();

		$feed_id     = str_replace(array( '_CUSTOMIZER', '_CUSTOMIZER_MODMODE' ), '', $this->feed_id);
		$cache_table = new FeedCacheTable();

		$cache_table->update(
			array(
				'cache_value'  => '',
				'last_updated' => date('Y-m-d H:i:s'),
			),
			array(
				'feed_id'   => $feed_id,
				'cache_key' => $cache_type . $this->suffix,
			)
		);

		$cache_table->update(
			array(
				'cache_value'  => '',
				'last_updated' => date('Y-m-d H:i:s'),
			),
			array(
				'feed_id'   => $feed_id . '_CUSTOMIZER',
				'cache_key' => $cache_type . $this->suffix,
			)
		);

		$cache_table->update(
			array(
				'cache_value'  => '',
				'last_updated' => date('Y-m-d H:i:s'),
			),
			array(
				'feed_id'   => $feed_id . '_CUSTOMIZER_MODMODE',
				'cache_key' => $cache_type . $this->suffix,
			)
		);

		return true;
	}

	/**
	 * Get existing caches.
	 *
	 * @return array
	 */
	private function get_existing_cache()
	{
		$feed_cache = $this->get_feed_cache();

		if (false === $feed_cache) {
			$feed_cache_table = new FeedCacheTable();
			$feed_cache       = $feed_cache_table->get_feed_cache(array( 'feed_id' => $this->feed_id ));

			if (false !== $feed_cache) {
				$this->set_feed_cache($feed_cache);
			}
		}

		return $feed_cache;
	}

	/**
	 * Get cache key.
	 *
	 * @return string
	 */
	private function get_cache_key()
	{
		return self::CACHE_KEY . $this->feed_id . '_' . $this->suffix;
	}

	/**
	 * Get feed cache.
	 *
	 * @return array
	 */
	private function get_feed_cache()
	{
		return wp_cache_get($this->get_cache_key());
	}

	/**
	 * Set feed cache.
	 *
	 * @param array $data Data.
	 *
	 * @return void
	 */
	private function set_feed_cache($data)
	{
		wp_cache_set($this->get_cache_key(), $data);
	}

	/**
	 * Delete feed cache.
	 *
	 * @return void
	 */
	public function delete_feed_cache()
	{
		wp_cache_delete($this->get_cache_key());
	}

	/**
	 * Get the page number.
	 *
	 * @return int
	 */
	public function get_page()
	{
		return $this->page;
	}

	/**
	 * Get the feed ID.
	 *
	 * @return int
	 */
	public function get_feed_id()
	{
		return $this->feed_id;
	}

	/**
	 * Add suffix to cache key if in customizer.
	 *
	 * @return string
	 */
	private function maybe_customizer_suffix()
	{
		$additional_suffix = '';

		if (! empty($_POST['previewSettings']) || ( isset($_GET['page']) && $_GET['page'] === 'sbtt' )) {
			$additional_suffix = '_CUSTOMIZER';
		}

		return $additional_suffix;
	}
}
