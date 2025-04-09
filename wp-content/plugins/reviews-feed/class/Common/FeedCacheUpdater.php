<?php
/**
 * Class FeedCacheUpdater
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;

class FeedCacheUpdater {

	/**
	 * @var array
	 */
	private $batch;

	public function __construct( $batch ) {
		$this->batch = $batch;
	}

	public function do_updates() {
		foreach ($this->batch as $single_feed) {
			$atts = !empty($single_feed['feed_id']) ? array('feed' => $single_feed['feed_id']) : array();
			$feed_id = !empty($single_feed['feed_id']) ? $single_feed['feed_id'] : 0;
			$feed = Util::sbr_is_pro()
			? new \SmashBalloon\Reviews\Pro\Feed($atts, $feed_id, new FeedCache($feed_id, 0))
			: new \SmashBalloon\Reviews\Common\Feed($atts, $feed_id, new FeedCache($feed_id, 0));
			$feed->get_set_cache();
		}
	}
}
