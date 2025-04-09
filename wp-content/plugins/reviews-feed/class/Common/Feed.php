<?php
/**
 * Class Feed
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;

use SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager;
use SmashBalloon\Reviews\Common\Builder\SBR_Sources;
use SmashBalloon\Reviews\Common\Helpers\Data_Encryption;

class Feed
{
	protected $posts = array();

	protected $header_data = array();

	/**
	 * @var FeedCache
	 */
	protected $feed_cache;

	protected $statuses = array();

	protected $settings;

	protected $feed_id;
	private $feed_style;

	private $flag_media_check;
    private $providers_languages;

	/**
	 * Data_Encryption
	 */
	private $encryption;

	/**
	 * @var array|string[]
	 */
	protected $providers_no_media = [];


	public function __construct($settings, $feed_id, FeedCache $feed_cache)
	{
		$this->feed_cache = $feed_cache;
		$this->feed_id = $feed_id;
		$this->settings = $settings;
		$this->settings['apiCallLanguage'] = Util::get_api_call_language($settings);
		$this->feed_style = is_array($settings) && isset($settings['feed_style']) ? $settings['feed_style'] : '';
		$this->statuses = array(
			'from_cache' => false,
			'post_found_before_filter' => false,
			'errors' => array()
		);

		$this->flag_media_check = false;

        $this->providers_languages = [
            'facebook',
            'google'
        ];

		$this->providers_no_media = sbr_get_no_media_providers();
		$this->encryption = new Data_Encryption();
	}

	public function init()
	{
		if ( empty( $this->settings ) ) {
			$this->add_error( sprintf( __( 'No feed with the ID %d found.', 'reviews-feed' ), $this->feed_id ), sprintf( __( 'Please go to the %sReviews Feed%s settings page to create a feed.', 'reviews-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sbr' ) ) . '" target="_blank" rel="noopener noreferrer">', '</a>' ) );
			return;
		}
		if ( empty( $this->settings['sources'] ) && ! $this->is_single_manual_review() ) {
			$this->add_error( sprintf( __( 'No sources available for this feed.', 'reviews-feed' ), $this->feed_id ), sprintf( __( 'Please go to the %sReviews Feed%s settings page add sources for this feed to use.', 'reviews-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sbr' ) ) . '" target="_blank" rel="noopener noreferrer">', '</a>' ) );
			return;
		}
		if( ! $this->is_single_manual_review() ){
			$this->hydrate_sources();
		}
	}

	public function get_settings()
	{
		return $this->settings;
	}

	public function get_errors()
	{
		return $this->statuses['errors'];
	}

	public function set_errors( $errors_array )
	{
		$this->statuses['errors'] = $errors_array;
	}

	public function add_error( $message, $instructions )
	{
		$this->statuses['errors'][] = array(
			'message' => $message,
			'directions' => $instructions
		);
	}

	public function get_feed_id()
	{
		return $this->feed_id;
	}

	public function get_feed_style()
	{
		return $this->feed_style;
	}

	public function set_posts($posts)
	{
		$this->posts = $posts;
	}

	public function get_posts()
	{
		return $this->posts;
	}

	public function should_check_media()
	{
		return $this->flag_media_check;
	}

	public function set_header_data($header_data)
	{
		$this->header_data = $header_data;
	}

	public function get_header_data()
	{
		return $this->header_data;
	}

	public function is_single_manual_review()
	{
		return isset( $this->settings['singleManualReview'] ) && $this->settings['singleManualReview'] === true;
	}

	public function get_set_cache()
	{
		if( ! $this->is_single_manual_review() ){
			$this->feed_cache->retrieve_and_set();

			if ( $this->feed_cache->is_expired() ) {
				$posts = $this->update_posts_cache();
				$header_data = $this->update_header_cache();
			} else {
				$this->statuses['from_cache'] = true;
				$posts = json_decode($this->feed_cache->get('posts'), true);
				$header_data = $this->feed_cache->get('header') !== null ? json_decode($this->feed_cache->get('header'), true) : $this->update_header_cache_from_source();
				$error_cache = $this->feed_cache->get('errors');
				if ( is_string( $error_cache ) ) {
					$error_cache = json_decode($error_cache, true);
				}
				$this->set_errors( $error_cache );
			}

			$posts = PostAggregator::remove_duplicated_posts_list($posts, 'json');

			if (empty($header_data)) {
				$header_data = $this->update_header_cache_from_source();
			}


			$this->set_posts($posts);
			$this->set_header_data($header_data);
		}
	}

	public function update_posts_cache()
	{
		$settings = $this->get_settings();

		if (empty($settings['sources'])) {
			return array();
		}
		$remote_posts = $this->get_remote_posts($settings);

        foreach ($remote_posts as $provider_remote_posts) {
			if (isset($provider_remote_posts['data']['reviews'])) {
				$this->cache_single_posts_from_set($provider_remote_posts['data']['reviews'], $provider_remote_posts['provider_id']);
            }
		}


		$posts = $this->posts_from_db();
		if ( empty( $posts ) ) {
			$no_posts_found =__( 'No Posts Found.', 'reviews-feed' );
			if ( $this->statuses['post_found_before_filter'] ) {
				$this->add_error( $no_posts_found, sprintf( __( 'There were no posts that fit your filters. Try modifying the filters set or add more sources with reviews that fit the filter by %sediting your feed%s', 'reviews-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sbr' ) ) . '" target="_blank" rel="noopener noreferrer">', '</a>' ) );
			} else {
				$this->add_error( $no_posts_found, sprintf( __( 'There were no posts found for the sources selected. Make sure reviews are available for this source or change the source by %sediting your feed%s', 'reviews-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sbr' ) ) . '" target="_blank" rel="noopener noreferrer">', '</a>' ) );
			}
		}

		$posts = $this->maybe_encrypt_cached_posts($posts);
		$this->update_cache($posts);

		return $posts;
	}


	/**
	 * Used to filter Posts and check Facebook that should be encrypted
	 *
	 * @param $posts posts list
	 *
	 *  @return array
	 *
	 */
	public function maybe_encrypt_cached_posts($posts)
	{
		foreach ($posts as $key => $s_post) {
			if (isset($s_post['provider']['name']) && $s_post['provider']['name'] === 'facebook') {
				$posts[$key] = $this->encryption->maybe_encrypt(wp_json_encode($s_post));
			}
		}
		return $posts;
	}

	public function posts_from_db() {
		$settings = $this->get_settings();
		$aggregator = new PostAggregator();
		$posts = $aggregator->db_post_set( $settings['sources'], $this->settings['apiCallLanguage'] );
		$posts = $aggregator->normalize_db_post_set( $posts );
		if ( $aggregator->missing_media_found() ) {
			$this->flag_media_check = true;
		}

		$aggregator->update_last_requested( $settings['sources'] );

		if ( ! empty( $posts ) ) {
			$this->statuses['post_found_before_filter'] = true;
		}

		return $this->filter_posts( $posts, $settings, true );
	}

	public function update_cache( $posts ) {
		$this->feed_cache->update_or_insert( 'posts', json_encode( $posts ) );
		$this->feed_cache->clear( 'errors' );
		$this->feed_cache->update_or_insert( 'errors', json_encode( $this->get_errors() ) );
	}

	public function update_header_cache()
	{
		$settings = $this->get_settings();
		if (empty($settings['sources'])) {
			return array();
		}
		$remote_header_data = $this->get_remote_header_data($settings);

		if (!empty($remote_header_data) && isset($remote_header_data[0]) && isset($remote_header_data[0]['info']) && isset($remote_header_data[0]['info']['id'])) {
			$persistent_business_data_cache = new BusinessDataCache();
			$persistent_business_data_cache->update_data($settings['sources'][0]['provider'], $remote_header_data[0]['info']['id'], $remote_header_data);
			$this->feed_cache->update_or_insert('header', json_encode($remote_header_data));
			$source_to_update = [
				'id' 			=> $remote_header_data[0]['info']['id'],
				'provider' 			=> $settings['sources'][0]['provider'],
				'last_updated' => date('Y-m-d H:i:s'),
				'info' 		=> json_encode($remote_header_data[0]['info'])
			];
			SBR_Sources::update($source_to_update);
        }
		return $remote_header_data;
	}

	public function update_header_cache_from_source()
	{
		$settings = $this->get_settings();

		if (empty($settings['sources'])) {
			return array();
		}
		$total_rating = 0;
		$rating = 0;
		foreach ($settings['sources'] as $s_source) {
			$total_rating += isset($s_source['info']['total_rating']) ? intval($s_source['info']['total_rating']) : 0;
			$rating += isset($s_source['info']['rating']) ? $s_source['info']['rating'] : 0;
		}
		$rating_average = $rating > 0 ? $rating / sizeof($settings['sources']) : 0;

		$remote_header_data = [
			[
				'info' => [
					'id' => $settings['sources'][0]['info']['id'],
					'name' => $settings['sources'][0]['info']['name'],
					'rating' => $rating_average,
					'total_rating' => $total_rating,
					'url' => $settings['sources'][0]['info']['url']
				]
		]
		];

		$persistent_business_data_cache = new BusinessDataCache();
		$persistent_business_data_cache->update_data($settings['sources'][0]['provider'], $settings['sources'][0]['info']['id'], $remote_header_data );
		$this->feed_cache->update_or_insert('header', json_encode( $remote_header_data ));

		return $remote_header_data ;
	}




	public function get_remote_posts($settings)
	{
		if (empty($settings['sources'])) {
			return array();
		}
		return $this->api_request($settings['sources']);
	}

	public function get_remote_header_data_old($settings)
	{
		if (empty($settings['sources'])) {
			return array();
		}
		$needed = array($settings['sources'][0]);
		return $this->api_request($needed, 'sources');
	}

    public function get_remote_header_data($settings)
	{
		if (empty($settings['sources'])) {
			return array();
		}
		$needed = $settings['sources'];
		return $this->api_request($needed, 'sources');
	}

	public function cache_single_posts_from_set( $posts, $provider_id )
	{
		foreach ( $posts as $single_review ) {
			$single_post_cache = new SinglePostCache( $single_review );
			$single_post_cache->set_provider_id( $provider_id );

			$single_post_cache->set_lang( $this->get_db_lang( $provider_id ) );

			if ( ! $single_post_cache->db_record_exists() ) {
				$single_post_cache->resize_avatar( 150 );
				if ( in_array( $this->provider_for_provider_id( $provider_id ), $this->providers_no_media,  true ) ) {
					$single_post_cache->set_storage_data( 'images_done', 1 );
				}
				$single_post_cache->store();
			} else {
				$single_post_cache->update_single();
            }
		}
	}



	public function api_request($requests_needed, $type = 'reviews')
	{
		$data = array();
		foreach ($requests_needed as $request) {
			if ($request['provider'] === 'collection' && $type === 'sources') {
				$collection = SBR_Sources::update_collection_ratings($request['account_id']);
				$info = isset($collection['info']) ? json_decode($collection['info'], true) : [];
				$data[] = [
					'info' => $info
				];
			}

			if( ! SBR_Feed_Saver_Manager::check_api_limit( $request['provider'] ) && $request['provider'] !== 'collection'){

                if(in_array($request['provider'] , $this->providers_languages) ){
                    $request['language'] = Util::get_api_call_language($this->settings);
                }

				if( ! SBR_Feed_Saver_Manager::limit_provider_api_calls( $request['provider'], $request['account_id'] ) ){
					if ($request['provider'] === 'facebook') {
						$new_data = \SmashBalloon\Reviews\Pro\Integrations\Providers\Facebook::get_facebook_info($type, $request);
					} else {
						$remote_request = new RemoteRequest($request['provider'], $request, $type);
						$new_data = $remote_request->fetch();
					}
				}


				if (isset($new_data['data'])) {
					if ( ! empty( $new_data['data']['error'] ) ) {
						$message = ! empty( ( $new_data['message'] ) ) ? wp_strip_all_tags( $new_data['message'] ) : 'An error has occurred when fetching new reviews';
						if ( is_array( $new_data['data']['error'] ) ) {
							$message .= '<br>';
							foreach ( $new_data['data']['error'] as $key => $value ) {
								$message .= '<br>' . $key . ': ' . wp_strip_all_tags( $value );
							}
						}
						$message .= '<br><br>';
						$message .= sprintf( __( 'This is affecting the source %s for %s. New reviews will not be fetched until this is resolved.', 'reviews-feed' ), wp_strip_all_tags( $request['name'] ), wp_strip_all_tags( $request['provider'] ) );
						$message .= '<br><br>';
						$this->add_error( $message, sprintf( __( 'Troubleshoot by visiting %serror message reference page%s.', 'reviews-feed' ), '<a href="https://smashballoon.com/doc/reviews-feed-error-message-reference/?reviews&utm_campaign=reviews-pro&utm_source=feed&utm_medium=apierror&utm_content=Error%20Message%20Reference" target="_blank" rel="noopener noreferrer">', '</a>' ) );
					}
					$new_data = $this->add_source_to_post_set( $request, $new_data );
					$to_push = $type === 'reviews' ? [
						'provider_id' => $request['account_id'],
						'data' => $new_data['data']
					] : $new_data['data'];
					array_push($data, $to_push);
				}
			}
		}

		return $data;
	}

	public function add_source_to_post_set( $source, $post_set ) {
		if ( ! isset( $post_set['data']['reviews'][0] ) ) {
			return $post_set;
		}
		foreach ( $post_set['data']['reviews'] as $index => $review ) {
			$post_set['data']['reviews'][ $index ]['source'] = array(
				'id' => $source['info']['id'],
				'url' => $source['info']['url'],
			);
		}

		return $post_set;
	}

	public function get_post_set_page($page = 1)
	{
		if( $this->is_single_manual_review() )
		{
			return [
				$this->hydrate_single_manual_review( $this->settings['singleManualReviewContent'] )
			];
		}

		$posts = $this->get_posts();
		$max = $this->settings['numPostDesktop'];
		if ($this->settings['numPostTablet'] > $this->settings['numPostDesktop']) {
			$max = $this->settings['numPostTablet'];
		}
		if ($this->settings['numPostMobile'] > $this->settings['numPostTablet']) {
			$max = $this->settings['numPostMobile'];
		}

		$offset = ($page - 1) * $max;
		return is_array( $posts ) ? array_slice($posts, $offset, $max) : [];
	}

	public function is_last_page($page)
	{
		$posts = $this->get_posts();
		$posts_counts = count($posts);
		$posts_per_page = $this->settings['numPostDesktop'];
		if ($this->settings['numPostTablet'] > $this->settings['numPostDesktop']) {
			$posts_per_page = $this->settings['numPostTablet'];
		}
		if ($this->settings['numPostMobile'] > $this->settings['numPostTablet']) {
			$posts_per_page = $this->settings['numPostMobile'];
		}
		$posts_per_page = (int) $posts_per_page;
		return $posts_counts <= ($page * $posts_per_page);
	}

    public function hydrate_sources()
    {

        if (!is_array($this->settings['sources'])) {
            $this->settings['sources'] = explode(',', $this->settings['sources']);
        }

        $db_sources = SBR_Sources::get_sources_list([
          'id' => $this->settings['sources']
        ]);

        $hydrated_sources = array();
        foreach ($this->settings['sources'] as $single_source) {
            foreach ($db_sources as $db_source) {
                if (
                    !is_array($single_source)
                    && !empty($db_source['account_id'])
                    && (string) $db_source['account_id'] === $single_source
                ) {
                    $final_source = $db_source;
                    $final_source['business'] = $db_source['account_id'];
                    if (!empty($final_source['info'])) {
                        $decoded = json_decode($final_source['info'], true);
                        if ($decoded) {
                            $final_source['info'] = $decoded;
                        }
                    }
                    if ($final_source['provider'] === 'google') {
                        $final_source['lang'] = $this->settings['apiCallLanguage'];

                    }
                    $hydrated_sources[] = $final_source;
                }
            }
        }

        $this->settings['sources'] = $hydrated_sources;
    }

    protected function get_db_lang($provider_id)
    {
        $settings = $this->get_settings();
        if ('google' === $this->provider_for_provider_id($provider_id)) {
            return $settings['apiCallLanguage'];
        }

        return '';
    }


	protected function provider_for_provider_id( $provider_id ) {
		foreach ( $this->settings['sources'] as $single_source ) {
			if ( $provider_id === $single_source['account_id'] ) {
				return $single_source['provider'];
			}
		}

		return '';
	}

	public function filter_posts($posts, $settings, $moderatePosts = false )
	{

		$filtered_posts = [];

		$is_star_filters = isset($settings['includedStarFilters']) && sizeof($settings['includedStarFilters']) > 0 ? true : false;
		$is_includeword = isset($settings['includeWords']) && !empty($settings['includeWords']) ? true : false;
		$is_excludeword = isset($settings['excludeWords']) && !empty($settings['excludeWords']) ? true : false;

		$is_sortbydate = isset($settings['sortByDateEnabled']) && !empty($settings['sortByDateEnabled']) && $settings['sortByDateEnabled'] == true ? true : false;
		$is_sortbyrating = isset($settings['sortByRatingEnabled']) && !empty($settings['sortByRatingEnabled']) && $settings['sortByRatingEnabled'] == true ? true : false;
		$is_randomize = isset($settings['sortRandomEnabled']) && !empty($settings['sortRandomEnabled']) && $settings['sortRandomEnabled'] == true ? true : false;

		$is_minchar = isset($settings['filterCharCountMin']) && !empty($settings['filterCharCountMin']) ? true : false;
		$is_maxchar = isset($settings['filterCharCountMax']) && !empty($settings['filterCharCountMax']) ? true : false;

		$sort_by_date = $settings['sortByDate'];
		$sort_by_rating = $settings['sortByRating'];

		$includewords = $is_includeword ? explode(',', $settings['includeWords']) : [];
		$excludewords = $is_excludeword ? explode(',', $settings['excludeWords']) : [];


		foreach ($posts as $post) {

			if (!is_null($post)) {
				$keep_post = false;
				//Work Around for facebook Positive / Negative Reviews
				if ( !empty($post['provider']['name']) && $post['provider']['name'] === 'facebook' ) {
					if( in_array( $post['rating'], [ 'positive', 'negative' ] ) ){
						$post['rating'] = $post['rating'] === 'positive' ? 5 : 1;
					}
				}

				$passes_star_filter = !$is_star_filters || ($is_star_filters && (isset($post['rating']) && in_array($post['rating'], $settings['includedStarFilters']))) ? true : false;
				$has_includeword = false;
				$has_excludeword = false;

				$passes_word_filter = false;
				$passes_moderation = true;


				if ($is_includeword && !empty($includewords)) {
					foreach ($includewords as $includeword) {
						if (strpos(strtolower($post['text']), strtolower($includeword)) !== false) {
							$has_includeword = true;
						}
					}
				}

				if ($is_excludeword && !empty($excludewords)) {
					foreach ($excludewords as $excludeword) {
						if (strpos(strtolower($post['text']), strtolower($excludeword)) !== false) {
							$has_excludeword = true;
						}
					}
				}

				if (!empty($excludewords) && !empty($includewords)) {
					$passes_word_filter = $has_includeword && !$has_excludeword;
				} elseif (!empty($includewords)) {
					$passes_word_filter = $has_includeword;
				} else {
					$passes_word_filter = !$has_excludeword;
				}


				if( $moderatePosts === true && isset( $settings['moderationEnabled'] ) && $settings['moderationEnabled'] === true){
					$moderation_ids = isset( $settings['moderationType'] ) && $settings['moderationType'] === 'allow' ? $settings['moderationAllowList'] : $settings['moderationBlockList'];
					if( $settings['moderationType'] === 'allow' ){
						$passes_moderation = in_array( $post['review_id'], $moderation_ids );
					}
					if ($settings['moderationType'] === 'block') {
						$passes_moderation = !in_array($post['review_id'], $moderation_ids);
					}
				}

				//Max Length and Min Length checking
				$text_length = strlen($post['text']);
				$passes_minchar_filter = ( !$is_minchar || ( $is_minchar && $text_length >= intval($settings['filterCharCountMin']) ) )? true : false;
				$passes_maxchar_filter = ( !$is_maxchar || ( $is_maxchar && $text_length <= intval($settings['filterCharCountMax']) ) )? true : false;


				if ($passes_star_filter === true && $passes_word_filter && $passes_moderation && $passes_minchar_filter && $passes_maxchar_filter) {
					$keep_post = true;
				}

				// $keep_post = apply_filters( 'sbr_passes_filter', $keep_post, $post, $settings );
				if ( $keep_post ) {
					$filtered_posts[] = $post;
				}
			}
		}

		if (!$is_randomize) {
			if ($is_sortbydate && !$is_sortbyrating) {
				$filtered_posts = $this->sort_array_bydate($filtered_posts, $sort_by_date);
			}

			if ($is_sortbyrating && !$is_sortbyrating) {
				$filtered_posts = $this->sort_array_byrating($filtered_posts, $sort_by_rating);
			}

			if ($is_sortbyrating && $is_sortbyrating) {
				$filtered_posts = $this->sort_array_byrating_and_date($filtered_posts, $sort_by_rating, $sort_by_date);
			}
		}

		return $filtered_posts;

	}


	public function sort_array_bydate($posts, $type = 'latest')
	{
		usort($posts, function ($a, $b) use ($type) {
			return $type == 'latest' ? $b['time'] <=> $a['time'] : $a['time'] <=> $b['time'];
		});
		return $posts;
	}

	public function sort_array_byrating($posts, $type = 'lowest')
	{
		usort($posts, function ($a, $b) use ($type) {
			return $type == 'highest' ? $b['rating'] <=> $a['rating'] : $a['rating'] <=> $b['rating'];
		});
		return $posts;
	}

	public function sort_array_byrating_and_date($posts, $rating_type = 'lowest', $date_type = 'latest')
	{
		usort($posts, function ($a, $b) use ($rating_type, $date_type) {
			if ($a['rating'] === $b['rating']) {
				return $date_type == 'latest' ? $b['time'] <=> $a['time'] : $a['time'] <=> $b['time'];
			}
			return $rating_type == 'highest' ? $b['rating'] <=> $a['rating'] : $a['rating'] <=> $b['rating'];
		});
		return $posts;
	}

	public function get_posts_for_moderation()
	{
		$settings = $this->get_settings();
		$aggregator = new PostAggregator();
		$posts = $aggregator->db_post_set($settings['sources']);
		$posts = $aggregator->normalize_db_post_set($posts);
		$post_set = $this->filter_posts($posts, $settings);
		return $post_set;
	}

	public function hydrate_single_manual_review( $review )
	{
		return [
			'review_id'		=> uniqid(),
			'text' 			=> $review['content'],
			'rating' 		=> $review['rating'],
			'time' 			=> $review['time'],
			'reviewer' 		=> [
					'name' 		=> $review['name'],
					'avatar' 	=> $review['avatar']
			],
			'provider'		=> [
				'name' => $review['provider']
			]
		];

	}

	public function is_init_wpml(){
        return false;
    }

}
