<?php

namespace SmashBalloon\TikTokFeeds\Common;

use SmashBalloon\TikTokFeeds\Common\Relay\Relay;
use SmashBalloon\TikTokFeeds\Common\Database\PostsTable;
use SmashBalloon\TikTokFeeds\Common\Database\SourcesTable;
use SmashBalloon\TikTokFeeds\Common\Services\SettingsManagerService;

class Feed
{
	/**
	 * Max posts to fetch and cache.
	 */
	protected const MAX_POSTS = 200;

	/**
	 * Posts
	 *
	 * @var array
	 */
	protected $posts = array();

	/**
	 * Header data.
	 *
	 * @var array
	 */
	protected $header_data = array();

	/**
	 * Feed Cache
	 *
	 * @var FeedCache
	 */
	protected $feed_cache;

	/**
	 * Statuses
	 *
	 * @var array
	 */
	protected $statuses = array();

	/**
	 * Posts cursor for each source.
	 *
	 * @var array
	 */
	protected $posts_cursor = array();

	/**
	 * Feed ID.
	 *
	 * @var int
	 */
	private $feed_id;

	/**
	 * Feed Settings
	 *
	 * @var array
	 */
	private $feed_settings;

	/**
	 * Uploads directory.
	 *
	 * @var string
	 */
	private $upload_dir;

	/**
	 * Uploads URL.
	 *
	 * @var string
	 */
	private $upload_url;

	/**
	 * Constructor.
	 *
	 * @param array     $feed_settings Feed settings.
	 * @param int       $feed_id     Feed ID.
	 * @param FeedCache $feed_cache Feed cache.
	 *
	 * @return void
	 */
	public function __construct($feed_settings, $feed_id, FeedCache $feed_cache)
	{
		$this->feed_cache    = $feed_cache;
		$this->feed_id       = $feed_id;
		$this->feed_settings = $feed_settings;

		$this->statuses = array(
			'from_cache'               => false,
			'from_backup'              => false,
			'post_found_before_filter' => false,
			'errors'                   => array(),
		);

		$upload     = wp_upload_dir();
		$upload_dir = trailingslashit($upload['basedir']) . SBTT_UPLOAD_FOLDER_NAME;
		$upload_url = trailingslashit($upload['baseurl']) . SBTT_UPLOAD_FOLDER_NAME;
		$this->upload_dir = $upload_dir;
		$this->upload_url = $upload_url;
	}

	/**
	 * Initialize the feed.
	 *
	 * @return void
	 */
	public function init()
	{
		$feed_settings = $this->get_feed_settings();

		if (empty($feed_settings)) {
			$this->add_error(
				sprintf(__('No feed with the ID %d found.', 'feeds-for-tiktok'), $this->get_feed_id()),
				sprintf(__('Please go to %1$sTikTok Feeds%2$s settings page to create a feed.', 'feeds-for-tiktok'), '<a href="' . esc_url(admin_url('admin.php?page=sbtt')) . '" target="_blank" rel="noopener noreferrer">', '</a>')
			);
			return;
		}

		if (! isset($feed_settings['sources']) || empty($feed_settings['sources'])) {
			$this->add_error(
				sprintf(__('No sources available for this feed.', 'feeds-for-tiktok'), $this->get_feed_id()),
				sprintf(__('Please go to %1$sTikTok Feeds%2$s settings page and add sources for this feed to use.', 'feeds-for-tiktok'), '<a href="' . esc_url(admin_url('admin.php?page=sbtt')) . '" target="_blank" rel="noopener noreferrer">', '</a>')
			);
			return;
		}

		$this->hydrate_sources();
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
	 * Set the feed settings
	 *
	 * @param array $feed_settings Feed settings.
	 */
	public function set_feed_settings($feed_settings)
	{
		return $this->feed_settings = $feed_settings;
	}

	/**
	 * Get the feed settings.
	 *
	 * @return array
	 */
	public function get_feed_settings()
	{
		return $this->feed_settings;
	}

	/**
	 * Set the feed posts.
	 *
	 * @param array $posts Feed posts.
	 */
	public function set_posts($posts)
	{
		$this->posts = $posts;
	}

	/**
	 * Get the feed posts.
	 *
	 * @return array
	 */
	public function get_posts()
	{
		return $this->posts;
	}

	/**
	 * Set the header data.
	 *
	 * @param array $header_data Header data.
	 */
	public function set_header_data($header_data)
	{
		$this->header_data = $header_data;
	}

	/**
	 * Get the header data.
	 *
	 * @return array
	 */
	public function get_header_data()
	{
		return $this->header_data;
	}

	/**
	 * Get the errors data
	 *
	 * @return array
	 */
	public function get_errors()
	{
		return $this->statuses['errors'];
	}

	/**
	 * Set the errors data
	 *
	 * @param array $errors_array Errors array.
	 * @return void
	 */
	public function set_errors($errors_array)
	{
		$this->statuses['errors'] = $errors_array;
	}

	/**
	 * Add error message and directions to resolve
	 *
	 * @param string $message Error message.
	 * @param string $instructions Error instructions.
	 * @return void
	 */
	public function add_error($message, $instructions)
	{
		// check if error already exists.
		foreach ($this->statuses['errors'] as $error) {
			if ($error['message'] === $message) {
				return;
			}
		}

		$this->statuses['errors'][] = array(
			'message'    => $message,
			'directions' => $instructions,
		);
	}

	/**
	 * Get the feed header and posts data from cache or remote and set it.
	 *
	 * @return void
	 */
	public function get_set_cache()
	{
		$this->feed_cache->retrieve_and_set_feed_cache();

		if ($this->feed_cache->is_expired_with_no_errors()) {
			$header_data = $this->update_header_cache();
			$posts       = $this->update_posts_cache();
		} else {
			$this->statuses['from_cache'] = true;

			$posts       = $this->feed_cache->get('posts') !== null ? json_decode($this->feed_cache->get('posts'), true) : array();
			$header_data = $this->feed_cache->get('header') !== null ? json_decode($this->feed_cache->get('header'), true) : array();

			$error_cache = $this->feed_cache->get('errors');
			if (is_string($error_cache)) {
				$error_cache = json_decode($error_cache, true);
			}
			$this->set_errors($error_cache);
		}

		// if posts empty, try backup.
		if (empty($posts) && $this->feed_cache->backup_exists()) {
			$this->statuses['from_cache'] = false;
			$this->statuses['from_backup'] = true;

			$posts       = json_decode($this->feed_cache->get('posts_backup'), true);
			$header_data = json_decode($this->feed_cache->get('header_backup'), true);
		}

		// check if posts are found before filter.
		if (! empty($posts)) {
			$this->statuses['post_found_before_filter'] = true;

			if (($this->statuses['from_cache'] || $this->statuses['from_backup']) && !empty($this->get_errors())) {
				$this->add_error(
					__('Source Error.', 'feeds-for-tiktok'),
					__('This is a saved backup feed. Please try reconnecting the source.', 'feeds-for-tiktok')
				);
			}
		}

		$posts = $this->filter_posts($posts);

		// check if posts are found after filter.
		if (empty($posts)) {
			if ($this->statuses['post_found_before_filter']) {
				$this->add_error(
					__('No posts found.', 'feeds-for-tiktok'),
					__('There were no posts that fit your filters. Please check your filters and try again.', 'feeds-for-tiktok')
				);
			}

			if ($this->statuses['from_cache']) {
				$this->add_error(
					__('No posts found.', 'feeds-for-tiktok'),
					__('There is no saved backup feed. Please try reconnecting the source.', 'feeds-for-tiktok')
				);
			} else {
				$this->add_error(
					__('No posts found.', 'feeds-for-tiktok'),
					__('There were no posts found for the sources selected. Please make sure there are posts for the sources added and try again.', 'feeds-for-tiktok')
				);
			}
		}

		$this->set_header_data($header_data);
		$this->set_posts($posts);
	}

	/**
	 * Filter the feed posts.
	 *
	 * @param array $posts Feed posts.
	 * @return array
	 */
	public function filter_posts($posts)
	{
		if (empty($posts)) {
			return array();
		}

		// Filter out posts that don't have a video.
		$posts = array_filter(
			$posts,
			function ($post) {
				return isset($post['duration']) && absint($post['duration']) > 0;
			}
		);

		$feed_settings = $this->get_feed_settings();

		$include_words = !empty($feed_settings['includeWords']) ? explode(',', $feed_settings['includeWords']) : false;
		$exclude_words = !empty($feed_settings['excludeWords']) ? explode(',', $feed_settings['excludeWords']) : false;

		// Filter by words.
		$filtered_posts = array_filter($posts, function ($post) use ($include_words, $exclude_words) {
			$video_description = strtolower($post['video_description']);

			if (!empty($include_words) && is_array($include_words)) {
				$include = false;
				foreach ($include_words as $word) {
					if (stripos($video_description, trim($word)) !== false) {
						$include = true;
						break;
					}
				}
				if (!$include) {
					return false;
				}
			}

			if (!empty($exclude_words) && is_array($exclude_words)) {
				foreach ($exclude_words as $word) {
					if (stripos($video_description, trim($word)) !== false) {
						return false;
					}
				}
			}

			return true;
		});

		// Sort the posts.
		return $this->sort_posts($filtered_posts);
	}

	/**
	 * Sort the feed posts.
	 *
	 * @param array $posts Feed posts.
	 * @return array
	 */
	public function sort_posts($posts)
	{
		if (empty($posts)) {
			return array();
		}

		$feed_settings = $this->get_feed_settings();

		// if sortRandomEnabled is true, return. Shuffle is done after the posts are sorted.
		if (isset($feed_settings['sortRandomEnabled']) && $feed_settings['sortRandomEnabled'] === true) {
			return $posts;
		}

		// sort by latest date.
		if (isset($feed_settings['sortFeedsBy']) && $feed_settings['sortFeedsBy'] === 'latest') {
			usort(
				$posts,
				function ($a, $b) {
					return $b['create_time'] <=> $a['create_time'];
				}
			);
		}

		// sort by oldest date.
		if (isset($feed_settings['sortFeedsBy']) && $feed_settings['sortFeedsBy'] === 'oldest') {
			usort(
				$posts,
				function ($a, $b) {
					return $a['create_time'] <=> $b['create_time'];
				}
			);
		}

		// sort by likes.
		if (isset($feed_settings['sortFeedsBy']) && $feed_settings['sortFeedsBy'] === 'likes') {
			usort(
				$posts,
				function ($a, $b) {
					return $b['like_count'] <=> $a['like_count'];
				}
			);
		}

		// sort by views.
		if (isset($feed_settings['sortFeedsBy']) && $feed_settings['sortFeedsBy'] === 'views') {
			usort(
				$posts,
				function ($a, $b) {
					return $b['view_count'] <=> $a['view_count'];
				}
			);
		}

		return $posts;
	}

	/**
	 * Update the feed header cache.
	 *
	 * @return array
	 */
	public function update_header_cache()
	{
		$feed_settings = $this->get_feed_settings();

		if (empty($feed_settings['sources'])) {
			return array();
		}

		$remote_header_data = array();

		foreach ($feed_settings['sources'] as $feed_source) {
			$header_data = $this->get_remote_header_data($feed_source);

			if (!empty($header_data)) {
				$open_id = isset($header_data['open_id']) ? sanitize_text_field($header_data['open_id']) : '';
				$source_table = new SourcesTable();
				$source = !empty($open_id) ? $source_table->get_source($open_id) : false;

				if ($source) {
					$header_data = $this->resize_avatar($header_data);

					$source['display_name'] = !empty($header_data['display_name']) ? sanitize_text_field(wp_unslash($header_data['display_name'])) : '';
					$source['info']         = sbtt_sanitize_data($header_data);

					// Update or insert the source.
					$source_table->update_or_insert($source);

					$remote_header_data[] = $header_data;
				}
			}
		}

		// Update the cache.
		if (!empty($remote_header_data)) {
			$this->feed_cache->update_or_insert('header', \json_encode($remote_header_data));
		}

		return $remote_header_data;
	}

	/**
	 * Get remote header data.
	 *
	 * @param array $source Feed source.
	 * @return array
	 */
	public function get_remote_header_data($source)
	{
		if (! isset($source) || empty($source)) {
			return array();
		}

		$args = [
			'access_token' => $source['access_token'],
			'open_id'      => $source['open_id'],
			'username'     => $source['info']['username'],
		];

		$relay    = new Relay();
		$response = $relay->call('user/info', $args);

		if (isset($response['success']) && $response['success'] === false) {
			$this->add_error($response['data']['error'], $response['data']['directions']);
			$this->feed_cache->update_or_insert('errors', json_encode($this->get_errors()));
			return array();
		}

		if (isset($response['data']['user_data'])) {
			$header_data = $response['data']['user_data'];
		} else {
			$header_data = array();
		}

		return $header_data;
	}

	/**
	 * Update the feed posts cache.
	 *
	 * @return array
	 */
	public function update_posts_cache()
	{
		$feed_settings = $this->get_feed_settings();

		if (empty($feed_settings['sources'])) {
			return array();
		}

		$remote_posts = array();

		foreach ($feed_settings['sources'] as $source) {
			$remote_posts = array_merge($remote_posts, $this->get_remote_posts($source));
		}

		if (empty($remote_posts)) {
			return array();
		}

		while (!empty($this->posts_cursor) && count($remote_posts) < self::MAX_POSTS) {
			$next_posts = array();

			foreach ($this->posts_cursor as $cursor) {
				$source = array_values(array_filter(
					$feed_settings['sources'],
					function ($source) use ($cursor) {
						return $source['open_id'] === $cursor['open_id'];
					}
				))[0] ?? null;

				if ($source) {
					$next_posts = array_merge($next_posts, $this->get_remote_posts($source, $cursor['cursor']));
				}
			}

			if (!empty($next_posts)) {
				$remote_posts = array_merge($remote_posts, $next_posts);
			}
		}

		if (!empty($this->posts_cursor)) {
			$this->feed_cache->update_or_insert('posts_cursor', \json_encode($this->posts_cursor));
		} else {
			$this->feed_cache->update_or_insert('posts_cursor', '');
		}

		// Update or insert the posts into the database.
		$this->update_posts_to_database($remote_posts);

		return $remote_posts;
	}

	/**
	 * Get remote posts.
	 *
	 * @param array  $source Feed source.
	 * @param string $cursor  Cursor.
	 * @return array
	 */
	public function get_remote_posts($source, $cursor = '')
	{
		if (!isset($source) || empty($source)) {
			return array();
		}

		$args = [
			'access_token' => $source['access_token'],
			'open_id'      => $source['open_id'],
			'username'     => $source['info']['username'],
		];

		if (!empty($cursor)) {
			$args['cursor'] = $cursor;
		}

		$relay    = new Relay();
		$response = $relay->call('list/videos', $args);

		if (isset($response['success']) && $response['success'] === false) {
			$this->add_error($response['data']['error'], $response['data']['directions']);
			$this->feed_cache->update_or_insert('errors', json_encode($this->get_errors()));
			return array();
		}

		if (isset($response['data']['video_data'])) {
			$posts = $response['data']['video_data'];
			$posts = array_map(
				function ($post) use ($source) {
					$post['open_id'] = $source['open_id'];
					return $post;
				},
				$posts
			);
		} else {
			$posts = array();
		}

		if (isset($response['data']['cursor']) && !empty($response['data']['cursor'])) {
			$cursor = ['open_id' => $source['open_id'], 'cursor' => $response['data']['cursor']];
			$this->posts_cursor[$source['open_id']] = $cursor;
		} else {
			unset($this->posts_cursor[$source['open_id']]);
		}

		return $posts;
	}

	/**
	 * Get posts from the set page.
	 *
	 * @param int $page Page number.
	 *
	 * @return array
	 */
	public function get_post_set_page($page = 1)
	{
		$posts = $this->get_posts();

		$feed_settings = $this->get_feed_settings();
		$max           = max(absint($feed_settings['numPostDesktop']), absint($feed_settings['numPostTablet']), absint($feed_settings['numPostMobile']));

		$offset         = ($page - 1) * $max;
		$set_page_posts = is_array($posts) ? array_slice($posts, $offset, $max) : [];

		return $set_page_posts;
	}

	/**
	 * Check if there is a next page.
	 *
	 * @param int $page Page number.
	 * @return bool|string
	 */
	public function has_next_page($page = 1)
	{
		$posts = $this->get_posts();

		$feed_settings = $this->get_feed_settings();
		$max           = max(absint($feed_settings['numPostDesktop']), absint($feed_settings['numPostTablet']), absint($feed_settings['numPostMobile']));

		if (count($posts) > (int) $page * (int) $max) {
			return true;
		}

		return false;
	}

	/**
	 * Get next page cursor
	 *
	 * @return mixed string || bool || array
	 */
	public function get_next_page_cursor()
	{
		$cursor = $this->feed_cache->get('posts_cursor');

		if (is_string($cursor)) {
			$cursor = json_decode($cursor, true);
		}

		if (isset($cursor) && ! empty($cursor)) {
			return $cursor;
		}

		return false;
	}

	/**
	 * Update the posts to the database and cache.
	 *
	 * @param array $posts Posts.
	 * @return void
	 */
	public function update_posts_to_database($posts)
	{
		if (empty($posts)) {
			return;
		}

		$posts_table = new PostsTable();

		// Update or insert the posts into the database.
		foreach ($posts as $post) {
			$video_id   = isset($post['id']) ? sanitize_text_field($post['id']) : '';
			$time_stamp = isset($post['create_time']) ? date('Y-m-d H:i:s', $post['create_time']) : date('Y-m-d H:i:s');
			$open_id    = isset($post['open_id']) ? sanitize_text_field($post['open_id']) : '';
			$views      = isset($post['view_count']) ? absint($post['view_count']) : 0;
			$likes      = isset($post['like_count']) ? absint($post['like_count']) : 0;
			$json_data  = sbtt_sanitize_data($post);

			$single_post = array(
				'video_id'       => $video_id,
				'json_data'      => $json_data,
				'open_id'        => $open_id,
				'views'          => $views,
				'likes'          => $likes,
				'time_stamp'     => $time_stamp,
				'created_on'     => date('Y-m-d H:i:s'),
				'last_requested' => date('Y-m-d H:i:s'),
			);

			$posts_table->update_or_insert($single_post);
		}

		// Save posts and feed ID to resize the images.
		$resize_data = get_option('sbtt_resize_images_data', array());
		$resize_data[] = array('posts' => $posts, 'feed_id' => $this->feed_cache->get_feed_id());
		update_option('sbtt_resize_images_data', $resize_data);

		// Update the cache.
		$this->feed_cache->update_or_insert('posts', \json_encode($posts));
		$this->feed_cache->clear('errors');
		$this->feed_cache->update_or_insert('errors', json_encode($this->get_errors()));
	}

	/**
	 * Hydrate sources.
	 *
	 * @return void
	 */
	public function hydrate_sources()
	{
		$sources       = Utils::get_sources_list();
		$feed_settings = $this->get_feed_settings();

		if (! $sources) {
			$this->add_error(
				sprintf(__('No sources available for this feed.', 'feeds-for-tiktok'), $this->get_feed_id()),
				sprintf(__('Please go to %1$sTikTok Feeds%2$s settings page and add sources for this feed to use.', 'feeds-for-tiktok'), '<a href="' . esc_url(admin_url('admin.php?page=sbtt')) . '" target="_blank" rel="noopener noreferrer">', '</a>')
			);
			$this->feed_cache->update_or_insert('errors', json_encode($this->get_errors()));
			return;
		}

		if (! is_array($feed_settings['sources'])) {
			$feed_settings['sources'] = explode(',', $feed_settings['sources']);
		}

		$hydrated_sources = array();
		foreach ($feed_settings['sources'] as $single_source) {
			// if single source is already hydrated, continue.
			if (isset($single_source['open_id'])) {
				$hydrated_sources[] = $single_source;
				continue;
			}

			$single_source = str_replace(array('"', '\\'), '', $single_source);
			$single_source = sanitize_text_field($single_source);

			// filter out the source from the sources list.
			$source = array_filter(
				$sources,
				function ($source) use ($single_source) {
					return $source['open_id'] === $single_source;
				}
			);

			if ($source) {
				$source             = array_shift($source);
				$source['info']     = json_decode($source['info'], true);
				$hydrated_sources[] = $source;
			}
		}

		$feed_settings['sources'] = $hydrated_sources;
		$this->set_feed_settings($feed_settings);
	}

	/**
	 * Resize and save the images from TikTok posts to uploads folder.
	 *
	 * @param array $post Post.
	 * @return array
	 */
	public function resize_images($post)
	{
		$video_id = isset($post['id']) ? sanitize_text_field($post['id']) : '';
		$cover_image_url = isset($post['cover_image_url']) ? esc_url_raw($post['cover_image_url']) : '';

		if (empty($video_id) || empty($cover_image_url)) {
			return $post;
		}

		$jpg_exists = file_exists($this->upload_dir . '/' . $video_id . '-full.jpg');
		$webp_exists = file_exists($this->upload_dir . '/' . $video_id . '-full.webp');
		$image_exists = $jpg_exists || $webp_exists;

		if ($image_exists) {
			$extension = $webp_exists ? '.webp' : '.jpg';
			$cover_image_url = $this->upload_url . '/' . $video_id . '-full' . $extension;
			$post['local_cover_image_url'] = $cover_image_url;

			return $post;
		}

		$image_sizes = array(
			'full' => array(
				'width'  => 720,
				'height' => 1280,
			),
		);

		$resized_image = false;

		$webp_supported = wp_image_editor_supports(array('mime_type' => 'image/webp'));
		$webp_supported = apply_filters('sbtt_webp_supported', $webp_supported);
		$extension 	    = $webp_supported ? '.webp' : '.jpg';

		foreach ($image_sizes as $size => $dimensions) {
			$filename = $video_id . '-' . $size . $extension;
			$image_editor = wp_get_image_editor($cover_image_url);

			if (is_wp_error($image_editor)) {
				continue;
			}

			$image_editor->resize($dimensions['width'], $dimensions['height'], null);
			$resized_image = $image_editor->save($this->upload_dir . '/' . $filename);
		}

		if ($resized_image) {
			$cover_image_url = $this->upload_url . '/' . $video_id . '-full' . $extension;
			$post['local_cover_image_url'] = $cover_image_url;
			$json_data  = sbtt_sanitize_data($post);

			$posts_table = new PostsTable();
			$posts_table->update(
				array(
					'json_data' => $json_data,
					'images_done'  => 1,
				),
				array(
					'video_id' => $video_id,
				)
			);
		}

		return $post;
	}

	/**
	 * Resize avatar image for header.
	 *
	 * @param array $header_data Header data.
	 * @return array $header_data Header data.
	 */
	public function resize_avatar($header_data)
	{
		$global_settings = new SettingsManagerService();
		$global_settings = $global_settings->get_global_settings();

		if (! isset($global_settings['optimize_images']) || $global_settings['optimize_images'] !== true) {
			return $header_data;
		}

		$open_id = isset($header_data['open_id']) ? sanitize_text_field($header_data['open_id']) : '';
		$avatar_url = isset($header_data['avatar_url']) ? esc_url_raw($header_data['avatar_url']) : '';

		if (empty($avatar_url) || empty($open_id)) {
			return $header_data;
		}

		$jpg_exists = file_exists($this->upload_dir . '/' . $open_id . '-thumbnail.jpg');
		$webp_exists = file_exists($this->upload_dir . '/' . $open_id . '-thumbnail.webp');
		$avatar_exists = $jpg_exists || $webp_exists;

		if ($avatar_exists) {
			$extension = $webp_exists ? '.webp' : '.jpg';
			$avatar_url = $this->upload_url . '/' . $open_id . '-thumbnail' . $extension;
			$header_data['local_avatar_url'] = $avatar_url;

			return $header_data;
		}

		$image_sizes = array(
			'thumbnail' => array(
				'width'  => 150,
				'height' => 150,
			),
		);

		$resized_image = false;

		$webp_supported = wp_image_editor_supports(array('mime_type' => 'image/webp'));
		$webp_supported = apply_filters('sbtt_webp_supported', $webp_supported);
		$extension 	    = $webp_supported ? '.webp' : '.jpg';

		foreach ($image_sizes as $size => $dimensions) {
			$filename = $open_id . '-' . $size . $extension;
			$image_editor = wp_get_image_editor($avatar_url);

			if (is_wp_error($image_editor)) {
				continue;
			}

			$image_editor->resize($dimensions['width'], $dimensions['height'], null);
			$resized_image = $image_editor->save($this->upload_dir . '/' . $filename);
		}

		if ($resized_image) {
			$avatar_url = $this->upload_url . '/' . $open_id . '-thumbnail' . $extension;
			$header_data['local_avatar_url'] = $avatar_url;
		}

		return $header_data;
	}

	/**
	 * Update the posts cache.
	 *
	 * @param array $posts Posts.
	 * @return void
	 */
	public function update_posts_cache_from_resize($posts)
	{
		$this->feed_cache->update_or_insert('posts', \json_encode($posts));
		$this->feed_cache->clear('errors');
		$this->feed_cache->update_or_insert('errors', json_encode($this->get_errors()));
	}
}
