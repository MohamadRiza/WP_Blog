<?php
/**
 * YouTube Feeds Cache
 *
 * For the new feed builder
 *
 * @since 2.0
 */

namespace SmashBalloon\YouTubeFeed;

 class SBY_Cache {

	/**
	 * @var int
	 */
	private $feed_id;

	/**
	 * @var int
	 */
	private $page;

	/**
	 * @var string
	 */
	private $suffix;

	/**
	 * @var bool
	 */
	private $is_legacy;

	/**
	 * @var int
	 */
	private $cache_time;

	/**
	 * @var array
	 */
	private $posts;

	/**
	 * @var array
	 */
	private $posts_page;

	/**
	 * @var bool
	 */
	private $is_expired;

	/**
	 * @var array
	 */
	private $header;

	/**
	 * @var array
	 */
	private $resized_images;

	/**
	 * @var array
	 */
	private $meta;

	/**
	 * @var array
	 */
	private $posts_backup;

	/**
	 * @var array
	 */
	private $header_backup;

	/**
	 * @var object|SB_Instagram_Data_Encryption
	 */
	protected $encryption;

	/**
	 * SBY_Cache constructor. Set the feed id, cache key, legacy
	 *
	 * @param string $feed_id
	 * @param int $page
	 * @param int $cache_time
	 *
	 * @since 2.0
	 */
	public function __construct( $feed_id, $page = 1, $cache_time = 0 ) {
		$this->cache_time = (int) $cache_time;
		$this->is_legacy  = strpos( $feed_id, '*' ) !== 0;
		$this->page       = $page;

		if ( $this->page === 1 ) {
			$this->suffix = '';
		} else {
			$this->suffix = '_' . $this->page;
		}

		$this->feed_id = str_replace( '*', '', $feed_id );

		if ( is_admin() ) {
			$this->feed_id .= $this->maybe_customizer_suffix();
		}
	}


	/**
	 * Add suffix to cache keys used in the customizer
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	private function maybe_customizer_suffix() {
		$additional_suffix = '';
		$in_customizer     = ! empty( $_POST['previewSettings'] ) || ( isset( $_GET['page'] ) && $_GET['page'] === 'sby-feed-builder' );
		if ( $in_customizer ) {
			$additional_suffix .= '_CUSTOMIZER';

			if ( ! empty( $_POST['moderationShoppableMode'] ) ) {
				$additional_suffix .= '_MODMODE';
				$offset             = ! empty( $_POST['moderationShoppableModeOffset'] ) ? intval( $_POST['moderationShoppableModeOffset'] ) : '';
				$additional_suffix .= $offset;
			}
		}

		return $additional_suffix;
	}

	/**
	 * Resets caches after they expire
	 *
	 * @param string $type
	 *
	 * @return bool|false|int
	 *
	 * @since 2.0
	 */
	public function clear( $type ) {
		$this->clear_wp_cache();

		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sby_feed_caches';

		$feed_id = str_replace( array( '_CUSTOMIZER', '_CUSTOMIZER_MODMODE' ), '', $this->feed_id );

		if ( $type === 'all' ) {
			$affected = $wpdb->query(
				$wpdb->prepare(
					"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE feed_id = %s
				AND cache_key NOT IN ( 'posts', 'posts_backup', 'header_backup' );",
					$feed_id
				)
			);

			$affected = $wpdb->query(
				$wpdb->prepare(
					"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE feed_id = %s",
					$feed_id . '_CUSTOMIZER'
				)
			);

			$mod_mode_where = esc_sql( $feed_id ) . '_CUSTOMIZER_MODMODE%';
			$affected       = $wpdb->query(
				$wpdb->prepare(
					"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE feed_id like %s",
					$mod_mode_where
				)
			);
		} else {

			$data   = array( 'cache_value' => '' );
			$format = array( '%s' );

			$where['feed_id'] = $feed_id;
			$where_format[]   = '%s';

			$where['cache_key'] = $type . $this->suffix;
			$where_format[]     = '%s';

			$affected = $wpdb->update( $cache_table_name, $data, $where, $format, $where_format );

			$where['feed_id'] = $feed_id . '_CUSTOMIZER';

			$affected = $wpdb->update( $cache_table_name, $data, $where, $format, $where_format );

			$where['feed_id'] = $feed_id . '_CUSTOMIZER_MODMODE';

			$affected = $wpdb->update( $cache_table_name, $data, $where, $format, $where_format );
		}

		return $affected;
	}

	 /**
	  * Get active/all cache count.
	  *
	  * @param bool $active when set to true only items updated in the last months are returned.
	  *
	  * @return int
	  */
	 public function get_cache_count($active = false) {
		 global $wpdb;
		 $cache_table_name = $wpdb->prefix . 'sby_feed_caches';
		 $query = "SELECT COUNT(DISTINCT feed_id, cache_key) as cache_count FROM $cache_table_name WHERE feed_id Not Like '%_CUSTOMIZER%'";

		 if($active === true) {
			 $query .= " AND feed_id Not Like '%_MODMODE%' AND last_updated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
		 }

		 $sql = $wpdb->prepare($query);
		 $caches = $wpdb->get_results( $sql );

		 if(!empty($caches)) {
			 return $caches[0]->cache_count;
		 }

		 return 0;
	 }

    /**
	 * Delete the wp_cache
	 *
	 * @since 2.0
	 */
	private function clear_wp_cache() {
		wp_cache_delete( $this->get_wp_cache_key() );
	}

	/**
	 * Key used to get the wp cache key
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	private function get_wp_cache_key() {
		return 'sby_feed_' . $this->feed_id . '_' . $this->page;
	}
 }
