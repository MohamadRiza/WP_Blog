<?php

namespace SmashBalloon\TikTokFeeds\Common;

use SmashBalloon\TikTokFeeds\Common\Database\FeedsTable;

class FeedSettings
{
	/**
	 * Feed ID.
	 *
	 * @var int
	 */
	protected $feed_id;

	/**
	 * Feed data.
	 *
	 * @var array
	 */
	protected $feed_data;

	/**
	 * Feed settings.
	 *
	 * @var array
	 */
	protected $feed_settings;

	/**
	 * Constructor.
	 *
	 * @param int $feed_id Feed ID.
	 */
	public function __construct($feed_id)
	{
		$this->feed_id = $feed_id;
		$this->set_feed_data();
	}

	/**
	 * Set feed data from database.
	 *
	 * @return void
	 */
	public function set_feed_data()
	{
		if (! $this->feed_id) {
			$this->feed_data = [];
			$this->feed_settings = [];
			return;
		}

		$feeds_table = new FeedsTable();
		$feed_data = $feeds_table->get_feed($this->feed_id);

		if (! $feed_data) {
			$this->feed_data = [];
			$this->feed_settings = [];
			return;
		}

		$this->feed_data = $feed_data;

		$feed_settings = isset($feed_data['settings']) ? json_decode($feed_data['settings'], true) : [];
		$feed_settings['feed_name'] = isset($feed_data['feed_name']) ? $feed_data['feed_name'] : '';
		$feed_settings = wp_parse_args($feed_settings, sbtt_feed_settings_defaults());

		$this->feed_settings = $feed_settings;
		return;
	}

	/**
	 * Get feed ID.
	 *
	 * @return int
	 */
	public function get_feed_id()
	{
		return $this->feed_id;
	}

	/**
	 * Get feed data.
	 *
	 * @return array
	 */
	public function get_feed_data()
	{
		return $this->feed_data;
	}

	/**
	 * Get feed settings.
	 *
	 * @return array
	 */
	public function get_feed_settings()
	{
		return $this->feed_settings;
	}

	/**
	 * Get feed style.
	 *
	 * @return string
	 */
	public function get_feed_style()
	{
		$feed_data = $this->get_feed_data();

		$feed_style = isset($feed_data['feed_style']) && ! empty($feed_data['feed_style']) ? $feed_data['feed_style'] : '';

		return $feed_style;
	}

	/**
	 * Get feed info.
	 *
	 * @return array
	 */
	public function get_feed_info()
	{
		// feed info data has all the feed data without settings.
		$feed_info = $this->get_feed_data();

		if (isset($feed_info['settings'])) {
			unset($feed_info['settings']);
		}

		return $feed_info;
	}

	/**
	 * Get connected feed sources.
	 *
	 * @return array
	 */
	public function get_connected_feed_sources()
	{
		$sources = [];

		$feed_settings = $this->get_feed_settings();

		if (isset($feed_settings['sources']) && !empty($feed_settings['sources'])) {
			$sources = Utils::get_sources_list(['open_id' => $feed_settings['sources']]);
		}

		return $sources;
	}
}
