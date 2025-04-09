<?php

namespace SmashBalloon\YouTubeFeed;

use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\Services\AdminAjaxService;

class SBY_Feed
{
	/**
	 * @var string
	 */
	protected $regular_feed_transient_name;

	/**
	 * @var string
	 */
	private $header_transient_name;

	/**
	 * @var string
	 */
	private $backup_feed_transient_name;

	/**
	 * @var string
	 */
	private $backup_header_transient_name;

	protected $channels_data;

	/**
	 * @var array
	 */
	private $post_data;

	/**
	 * @var
	 */
	private $header_data;

	/**
	 * @var array
	 */
	protected $next_pages;

	/**
	 * @var array
	 */
	private $transient_atts;

	/**
	 * @var int
	 */
	private $last_retrieve;

	/**
	 * @var bool
	 */
	private $should_paginate;

	/**
	 * @var int
	 */
	private $num_api_calls;

	/**
	 * @var int
	 */
	private $max_api_calls;

	/**
	 * @var array
	 */
	protected $image_ids_post_set;

	/**
	 * @var array
	 */
	public $post_ids_with_no_details;

	/**
	 * @var bool
	 */
	private $should_use_backup;

	/**
	 * @var array
	 */
	private $report;

	private $successful_video_api_request_made;

	private $do_page_cache_all;

	protected $channel_id_avatars;
	/**
	 * SBY_Feed constructor.
	 *
	 * @param string $transient_name ID of this feed
	 *  generated in the SBY_Settings class
	 */
	public function __construct( $transient_name ) {
		$this->regular_feed_transient_name = $transient_name;
		$this->backup_feed_transient_name = SBY_BACKUP_PREFIX . $transient_name;

		$sby_header_transient_name = str_replace( 'sby_', 'sby_header_', $transient_name );
		$sby_header_transient_name = substr($sby_header_transient_name, 0, 44);
		$this->header_transient_name = $sby_header_transient_name;
		$this->backup_header_transient_name = SBY_BACKUP_PREFIX . $sby_header_transient_name;

		$this->channels_data = array();

		$this->post_data = array();
		$this->next_pages = array();
		$this->should_paginate = true;

		// this is a count of how many api calls have been made for each feed
		// type and term.
		// By default the limit is 10
		$this->num_api_calls = 0;
		$this->max_api_calls = apply_filters( 'sby_max_concurrent_api_calls', 10 );
		$this->should_use_backup = false;

		// used for errors and the sby_debug report
		$this->report = array();
		$this->successful_video_api_request_made = false;
		$this->post_ids_with_no_details = array();
		$this->do_page_cache_all = false;
	}

	/**
	 * @return string
	 *
	 * @since 2.0
	 */
	public function get_feed_id() {
		return str_replace( '*', '', $this->regular_feed_transient_name );
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_post_data() {
		return $this->post_data;
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function set_post_data( $post_data ) {
		$this->post_data = $post_data;
	}

	public function get_misc_data( $feed_id, $posts ) {
		return array();
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_next_pages() {
		return $this->next_pages;
	}

	public function are_posts_with_no_details() {
		return (! empty( $this->post_ids_with_no_details ));
	}

	/**
	 * Uses the settings to determine if avatars are going to be used.
	 * Can make feed creation faster if not.
	 *
	 * @param $settings
	 *
	 * @return bool
	 *
	 * @since 2.0
	 */
	public function need_avatars( $settings ) {
		if ( isset( $settings['type'] ) && $settings['type'] === 'hashtag' ) {
			return false;
		} elseif ( isset( $settings['disablelightbox'] ) && ($settings['disablelightbox'] === 'true' || $settings['disablelightbox'] === 'on') ) {
			return false;
		} else {
			return true;
		}
	}

	public function maybe_add_live_html( $post ) {
		if ( ! isset( $post['iframe'] ) ) {
			return;
		}

		echo '<iframe  id="sby_live_player'. esc_attr( $post['iframe'] ) .'" width="640" height="360"  data-origwidth="640" data-origheight="360"  data-relstop="1"  src="https://www.youtube.com/embed/live_stream?enablejsapi=1&#038;channel='. esc_attr( $post['iframe'] ) .'&#038;rel=0&#038;modestbranding=1&#038;autoplay=0&#038;cc_load_policy=0&#038;iv_load_policy=1&#038;loop=0&#038;fs=1&#038;playsinline=0&#038;autohide=2&#038;theme=dark&#038;color=red&#038;controls=1&#038;" class="sby_live_player" title="YouTube player"  allow="autoplay; encrypted-media" allowfullscreen data-no-lazy="1" data-skipgform_ajax_framebjll=""></iframe>';
	}
	/**
	 * Available avatars are added to the feed as an attribute so they can be used in the lightbox
	 *
	 * @param $connected_accounts_in_feed
	 * @param $feed_types_and_terms
	 *
	 * @since 2.0
	 */
	public function set_up_feed_avatars( $connected_accounts_in_feed, $feed_types_and_terms ) {
		foreach ( $feed_types_and_terms as $type => $terms ) {
			foreach ( $terms as $term_and_params ) {
				$existing_channel_cache = $this->get_channel_cache( $term_and_params['term'], true );
				$avatar = SBY_Parse::get_avatar( $existing_channel_cache );
				$channel_id = SBY_Parse::get_channel_id( $existing_channel_cache );
				$this->set_avatar( $channel_id, $avatar );
			}
		}
	}

	/**
	 * Creates a key value pair of the username and the url of
	 * the avatar image
	 *
	 * @param $name
	 * @param $url
	 *
	 * @since 2.0
	 */
	public function set_avatar( $channel_id, $url ) {
		$this->channel_id_avatars[ $channel_id ] = $url;
	}

	/**
	 * @return array
	 */
	public function get_channel_id_avatars() {
		return $this->channel_id_avatars;
	}

	/**
	 * Checks the database option related the transient expiration
	 * to ensure it will be available when the page loads
	 *
	 * @return bool
	 *
	 * @since 2.0/4.0
	 */
	public function regular_cache_exists() {
		//Check whether the cache transient exists in the database and is available for more than one more minute
		$transient_exists = get_transient( $this->regular_feed_transient_name );

		return $transient_exists;
	}

	/**
	 * Checks the database option related the header transient
	 * expiration to ensure it will be available when the page loads
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function regular_header_cache_exists() {
		$header_transient = get_transient( $this->header_transient_name );

		return $header_transient;
	}

	public function get_channel_cache( $channel, $force_get_cache = false ) {
		if ( $this->is_pageable() && ! $force_get_cache ) {
			return false;
		}

		$maybe_cache = get_option( SBY_CHANNEL_CACHE_PREFIX . $channel );
		if ( $maybe_cache !== false ) {
			$maybe_cache = json_decode( $maybe_cache, true );
		}

		return $maybe_cache;
	}

	public function set_channel_cache( $channel, $cache ) {
		if ( is_array( $cache ) ) {
			$cache = wp_json_encode( $cache );
		}

		update_option( SBY_CHANNEL_CACHE_PREFIX . $channel, $cache, false );
	}

	/**
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function should_use_backup() {
		return $this->should_use_backup || empty( $this->post_data );
	}

	/**
	 * The header is only displayed when the setting is enabled and
	 * an account has been connected
	 *
	 * Overwritten in the Pro version
	 *
	 * @param array $settings settings specific to this feed
	 * @param array $feed_types_and_terms organized settings related to feed data
	 *  (ex. 'user' => array( 'smashballoon', 'customyoutubefeed' )
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function need_header( $settings, $feed_types_and_terms ) {
		if ( ! empty( $settings['headerchannel'] ) || isset( $feed_types_and_terms['channels'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Use the transient name to retrieve cached data for header
	 *
	 * @since 1.0
	 */
	public function set_header_data_from_cache() {
		$header_cache = get_transient( $this->header_transient_name );

		$header_cache = json_decode( $header_cache, true );

		if ( ! empty( $header_cache ) ) {
			$this->header_data = $header_cache;
		}
	}

	public function set_header_data( $header_data ) {
		$this->header_data = $header_data;
	}

	/**
	 * @since 1.0
	 */
	public function get_header_data() {
		return $this->header_data;
	}

	/**
	 * Sets the post data, pagination data, shortcode atts used (cron cache),
	 * and timestamp of last retrieval from transient (cron cache)
	 *
	 * @param array $atts available for cron caching
	 *
	 * @since 1.0
	 */
	public function set_post_data_from_cache( $atts = array() ) {
		$transient_data = get_transient( $this->regular_feed_transient_name );
		$transient_data = json_decode( $transient_data, true );

		if ( $transient_data ) {
			$post_data = isset( $transient_data['data'] ) ? $transient_data['data'] : array();
			$this->post_data = $post_data;
			$this->next_pages = isset( $transient_data['pagination'] ) ? $transient_data['pagination'] : array();

			if ( isset( $transient_data['atts'] ) ) {
				$this->transient_atts = $transient_data['atts'];
				$this->last_retrieve = $transient_data['last_retrieve'];
			}
		}
	}

	/**
	 * Sets post data from a permanent database backup of feed
	 * if it was created
	 *
	 * @since 1.0
	 */
	public function maybe_set_post_data_from_backup() {
		$args = array(
			'post_type'		=>	SBY_CPT,
			'post_status' => array( 'publish', 'pending', 'draft' ),
			'orderby' => 'date',
			'order'   => 'DESC',
			'posts_per_page' => 50,
			'meta_query'	=>	array(
				array(
					'value'	=>	sby_strip_after_hash( $this->regular_feed_transient_name ),
					'key'	=>	'sby_feed_id'
				)
			)
		);
		$feed_videos = new \WP_Query( $args );

		if ( $feed_videos->have_posts() ) {
			$posts = array();
			while ( $feed_videos->have_posts() ) {
				$feed_videos->the_post();
				$json = get_post_meta( get_the_ID(), 'sby_json', true );

				if ( $json ) {
					$posts[] = json_decode( $json, true );
				}
			}

			wp_reset_postdata();

			$this->post_data = $posts;
			return true;
		} else {
			$this->add_report( 'no backup post data found' );
			wp_reset_postdata();

			return false;
		}
	}

	/**
	 * Sets header data from a permanent database backup of feed
	 * if it was created
	 *
	 * @since 1.0
	 */
	public function maybe_set_header_data_from_backup() {
		$backup_header_data = get_option( $this->backup_header_transient_name, false );

		if ( ! empty( $backup_header_data ) ) {
			$backup_header_data = json_decode( $backup_header_data, true );
			$this->header_data = $backup_header_data;

			return true;
		} else {
			$this->add_report( 'no backup header data found' );

			return false;
		}
	}

	/**
	 * Returns recorded image IDs for this post set
	 * for use with image resizing
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_image_ids_post_set() {
		return $this->image_ids_post_set;
	}

	/**
	 * Cron caching needs additional data saved in the transient
	 * to work properly. This function checks to make sure it's present
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function need_to_start_cron_job() {
		return (( ! empty( $this->post_data ) && ! isset( $this->transient_atts )) || empty( $this->post_data ));
	}

	/**
	 * Checks to see if there are enough posts available to create
	 * the current page of the feed
	 *
	 * @param int $num
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function need_posts( $num, $offset = 0 ) {
		$num_existing_posts = is_array( $this->post_data ) ? count( $this->post_data ) : 0;
		$num_needed_for_page = (int)$num + (int)$offset;

		($num_existing_posts < $num_needed_for_page) ? $this->add_report( 'need more posts' ) : $this->add_report( 'have enough posts' );

		return ($num_existing_posts < $num_needed_for_page);
	}

	/**
	 * Checks to see if there are additional pages available for any of the
	 * accounts in the feed and that the max conccurrent api request limit
	 * has not been reached
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function can_get_more_posts() {
		$one_type_and_term_has_more_ages = $this->next_pages !== false;
		$max_concurrent_api_calls_not_met = $this->num_api_calls < $this->max_api_calls;
		$max_concurrent_api_calls_not_met ? $this->add_report( 'max conccurrent requests not met' ) : $this->add_report( 'max concurrent met' );
		$one_type_and_term_has_more_ages ? $this->add_report( 'more pages available' ) : $this->add_report( 'no next page' );

		return ($one_type_and_term_has_more_ages && $max_concurrent_api_calls_not_met);
	}

	public function get_play_list_for_term( $type, $term, $connected_account_for_term, $params ) {

		if ( $type === 'search' || $type === 'live' ) {
			return false;
		}
		$existing_channel_cache = $this->get_channel_cache( $term );

		if ( $existing_channel_cache ) {
			$this->channels_data[ $term ] = $existing_channel_cache;
		}

		if ( empty( $this->channels_data[ $term ] ) ) {
			if ( $connected_account_for_term['expires'] < time() + 5 ) {
				$error_message = '<p><b>' . __( 'Reconnect to YouTube to show this feed.', 'feeds-for-youtube' ) . '</b></p>';
				$error_message .= '<p>' . __( 'To create a new feed, first connect to YouTube using the "Connect to YouTube to Create a Feed" button on the settings page and connect any account.', 'feeds-for-youtube' ) . '</p>';

				if ( current_user_can( 'manage_youtube_feed_options' ) ) {
					$error_message .= '<a href="' . admin_url( 'admin.php?page=youtube-feed-settings' ) . '" target="blank" rel="noopener nofollow">' . __( 'Reconnect in the YouTube Feeds Settings Area', 'feeds-for-youtube' ) . '</a>';
				}
				global $sby_posts_manager;

				$sby_posts_manager->add_frontend_error( 'accesstoken', $error_message );
				$sby_posts_manager->add_error( 'accesstoken', array( 'Trying to connect a new account', $error_message ) );

				return false;
			}
			$channel_data         = array();
			$api_connect_channels = $this->make_api_connection( $connected_account_for_term, 'channels', $params );

			$this->add_report( 'channel api call made for ' . $term . ' - ' . $type );

			$api_connect_channels->connect();
			if ( ! $api_connect_channels->is_wp_error() && ! $api_connect_channels->is_youtube_error() ) {
				$channel_data = $api_connect_channels->get_data();
				$channel_id = SBY_Parse::get_channel_id( $channel_data );
				$this->set_channel_cache( $channel_id, $channel_data );

				if ( isset( $params['channel_name'] ) ) {
					sby_set_channel_id_from_channel_name( $params['channel_name'], $channel_id );
					$this->set_channel_cache( $params['channel_name'], $channel_data );
				}

				$params = array( 'channel_id' => $channel_id );
				$this->channels_data[ $channel_id ] = $channel_data;
				$this->channels_data[ $term ] = $channel_data;
			} else {
				if ( ! $api_connect_channels->is_wp_error() ) {
					$return = SBY_API_Connect::handle_youtube_error( $api_connect_channels->get_data(), $connected_account_for_term );
					if ( $return && isset( $return['access_token'] ) ) {
						$connected_account_for_term['access_token'] = $return['access_token'];
						$connected_accounts_for_feed[ $term ]['access_token'] = $return['access_token'];
						$connected_account_for_term['expires'] = $return['expires_in'] + time();
						$connected_accounts_for_feed[ $term ]['expires'] = $return['expires_in'] + time();

						sby_update_or_connect_account( $connected_account_for_term );
						$this->add_report( 'refreshing access token for ' . $connected_account_for_term['channel_id'] );

						$sby_api_connect_channel = $this->make_api_connection( $connected_account_for_term, 'channels', $params );
						$sby_api_connect_channel->connect();
						if ( ! $sby_api_connect_channel->is_youtube_error() ) {
							$channel_data = $sby_api_connect_channel->get_data();
							$channel_id = SBY_Parse::get_channel_id( $channel_data );
							$this->set_channel_cache( $channel_id, $channel_data );

							if ( isset( $params['channel_name'] ) ) {
								sby_set_channel_id_from_channel_name( $params['channel_name'], $channel_id );
								$this->set_channel_cache( $params['channel_name'], $channel_data );
							}

							$this->channels_data[ $channel_id ] = $channel_data;
							$this->channels_data[ $term ] = $channel_data;

						}
					} else {
						$this->add_report( 'error connecting to channel' );
					}
				} else {
					$api_connect_channels->handle_wp_remote_get_error( $api_connect_channels->get_data() );
				}
			}
		}

		$playlist = isset( $this->channels_data[ $term ]['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ) ? $this->channels_data[ $term ]['items'][0]['contentDetails']['relatedPlaylists']['uploads'] : false;

		return $playlist;
	}

	public function maybe_refresh_token( $term, $connected_account_for_term ) {
		return $connected_account_for_term;
	}

	public function is_efficient_type( $type ) {
		return in_array( $type, array( 'playlist', 'channel' ), true );
	}

	public function requires_workaround_connection( $type ) {
		return false;
	}

	public function make_workaround_connection( $connected_account_for_term, $type, $params, $feed_id = '' ) {
		return $this->make_api_connection( $connected_account_for_term, $type, $params );
	}


	/**
	 * Appends one filtered API request worth of posts for each feed term
	 *
	 * @param $settings
	 * @param array $feed_types_and_terms organized settings related to feed data
	 *  (ex. 'user' => array( 'smashballoon', 'customyoutubefeed' )
	 * @param array $connected_accounts_for_feed connected account data for the
	 *  feed types and terms
	 *
	 * @since 1.0
	 */

	public function add_remote_posts( $settings, $feed_types_and_terms, $connected_accounts_for_feed ) {
		$new_post_sets = array();
		$next_pages = $this->next_pages;
		global $sby_posts_manager;
		$api_requests_delayed = $sby_posts_manager->are_current_api_request_delays();

		/**
		 * Number of posts to retrieve in each API call
		 *
		 * @param int               Minimum number of posts needed in each API request
		 * @param array $settings   Settings for this feed
		 *
		 * @since 1.0
		 */
		$num = apply_filters( 'sby_num_in_request', (int)$settings['num'], $settings );

		$params = array(
			'num' => $num
		);

		$one_successful_connection = false;
		$next_page_found = false;
		$one_api_request_delayed = false;



		foreach ( $feed_types_and_terms as $type => $terms ) {
			if ( is_array( $terms ) && count( $terms ) > 5 ) {
				shuffle( $terms );
			}
			foreach ( $terms as $term_and_params ) {

				$term = $term_and_params['term'];
				$params = array_merge( $params, $term_and_params['params'] );

				$connected_accounts_for_feed[ $term ] = $this->maybe_refresh_token( $term, $connected_accounts_for_feed[ $term ] );
				$connected_account_for_term = $connected_accounts_for_feed[ $term ];

				$play_list = $this->get_play_list_for_term( $type, $term, $connected_account_for_term, $params );

				if ( ! empty( $next_pages[ $term . '_' . $type ] ) ) {
					$params['nextPageToken'] = $next_pages[ $term . '_' . $type ];
				}

				if ( $this->is_pageable() ) {
					$this->add_remote_pageable_posts( $settings, $feed_types_and_terms, $connected_accounts_for_feed );
				} else {
					$this->add_remote_non_pageable( $settings, $feed_types_and_terms, $connected_accounts_for_feed );
				}

				if ( ! $this->is_efficient_type( $type ) && $this->is_pageable() ) {

					if ( $this->requires_workaround_connection( $type ) ) {
						$feed_id  = !empty($settings['feed']) ? $settings['feed'] : '';

						$api_connect_playlist_items = $this->make_workaround_connection( $connected_account_for_term, $type, $params, $feed_id );
						$this->add_report( 'Workaround API call made for ' . $term );

					} else {
						if ( $play_list ) {
							$params['playlist_id'] = $play_list;
							$api_connect_playlist_items = $this->make_api_connection( $connected_account_for_term, 'playlistItems', $params );
						} else {
							$api_connect_playlist_items = $this->make_api_connection( $connected_account_for_term, $type, $params );
						}

						$api_connect_playlist_items->connect();
						$this->add_report( 'API call made for ' . $term );
					}


					if ( ! $api_connect_playlist_items->is_wp_error() && ! $api_connect_playlist_items->is_youtube_error() ) {
						$one_successful_connection = true;
						$data = $api_connect_playlist_items->get_data();

						if ( isset( $data['items'][0] ) ) {
							// clear object cache after successful API connection
							sby_clear_object_cache();
							$post_set = $this->filter_posts( $data['items'], $settings );

							$this->successful_video_api_request_made = true;

							$new_post_sets[] = $post_set;
						}

						$next_page = $api_connect_playlist_items->get_next_page( $params );
						$report = is_array( $next_page ) ? implode( ',', $next_page ) : $next_page;
						$this->add_report( 'Next Page ' . $report );


						if ( ! empty( $next_page ) ) {
							$next_pages[ $term . '_' . $type ] = $next_page;
							$next_page_found = true;
						} else {
							$next_pages[ $term . '_' . $type ] = false;
						}
					}


					$this->num_api_calls++;

				} else {

					if ( ! $this->is_pageable() && (int)self::get_channel_status( $term ) !== 1 ) {
						self::update_channel_status( $term, 1 );
						$this->do_page_cache_all = true;
						$play_list = $this->get_play_list_for_term( $type, $term, $connected_account_for_term, $params );

						$channel_id = isset( $this->channels_data[ $term ] ) ? SBY_Parse::get_channel_id( $this->channels_data[ $term ] ) : '';
						$params['channel_id'] = $channel_id;
						$params['num'] = 50;

						if ( $play_list ) {

							$params['playlist_id'] = $play_list;
							if ( ! empty( $next_pages[ $term . '_' . $type ] ) && $next_pages[ $term . '_' . $type ] !== 'rss' ) {
								$params['nextPageToken'] = $next_pages[ $term . '_' . $type ];
							}

							self::update_channel_status( $term, 1 );
							$this->do_page_cache_all = true;
							$this->add_report( 'using API request to get first 50 videos' );

							$api_connect_playlist_items = $this->make_api_connection( $connected_account_for_term, 'playlistItems', $params );

							$api_connect_playlist_items->connect();

							if ( ! $api_connect_playlist_items->is_wp_error() && ! $api_connect_playlist_items->is_youtube_error() ) {
								$one_successful_connection = true;
								$data = $api_connect_playlist_items->get_data();

								if ( isset( $data['items'][0] ) ) {
									$post_set = $this->filter_posts( $data['items'], $settings );

									$this->successful_video_api_request_made = true;

									$new_post_sets[] = $post_set;
								}

								$next_pages[ $term . '_' . $type ] = false;
							}
						} else {
							$this->add_report( 'no first playlist' );
						}

					} elseif ( ! $this->is_pageable()
					           && isset( $params['channel_id'] )
					           && (! isset( $next_pages[ $term . '_' . $type ] ) || $next_pages[ $term . '_' . $type ] !== 'rss') ) {

						$rss_connect_playlist_items = new SBY_RSS_Connect( 'playlistItems', $params );
						$this->add_report( 'RSS call made for ' . $term );

						$rss_connect_playlist_items->connect();

						$one_successful_connection = true;

						$data = $rss_connect_playlist_items->get_data();

						if ( isset( $data[0] ) ) {
							$data = array(
								'items' => $data
							);
							$post_set = $this->filter_posts( $data['items'], $settings );

							$this->successful_video_api_request_made = true;

							if ( count( $post_set ) > 14 ) {
								if ( (int)self::get_channel_status( $term ) !== 1 ) {
									$next_pages[ $term . '_' . $type ] = 'rss';
									$next_page_found = true;
								} else {
									$this->add_report( 'RSS update only for ' . $term );
									$post_set = $this->merge_cached_posts( $post_set, $term );
									$next_pages[ $term . '_' . $type ] = false;
								}
							} else {
								$next_pages[ $term . '_' . $type ] = false;
							}

							$new_post_sets[] = $post_set;

						}
					} elseif ( isset( $connected_account_for_term['rss_only'] ) ) {
						$rss_connect_playlist_items = new SBY_RSS_Connect( 'playlistItems', $params );
						$this->add_report( 'RSS Only call made for ' . $term );

						$rss_connect_playlist_items->connect();

						$one_successful_connection = true;

						$data = $rss_connect_playlist_items->get_data();
						$next_pages[ $term . '_' . $type ] = false;

						if ( isset( $data[0] ) ) {
							$data = array(
								'items' => $data
							);
							$post_set = $this->filter_posts( $data['items'], $settings );

							$this->successful_video_api_request_made = true;
							$new_post_sets[] = $post_set;
						}
					} else {

						if ( ! $api_requests_delayed
						     && (! isset( $next_pages[ $term . '_' . $type ] ) || $next_pages[ $term . '_' . $type ] !== false) ) {

							$play_list = $this->get_play_list_for_term( $type, $term, $connected_account_for_term, $params );

							$channel_id = isset( $this->channels_data[ $term ] ) ? SBY_Parse::get_channel_id( $this->channels_data[ $term ] ) : '';
							$params['channel_id'] = $channel_id;

							if ( $play_list ) {

								$params['playlist_id'] = $play_list;
								if ( ! empty( $next_pages[ $term . '_' . $type ] ) && $next_pages[ $term . '_' . $type ] !== 'rss' ) {
									$params['nextPageToken'] = $next_pages[ $term . '_' . $type ];
								}

								if ( isset( $next_pages[ $term . '_' . $type ] ) && $next_pages[ $term . '_' . $type ] === 'rss' ) {
									self::update_channel_status( $term, 1 );
									$this->do_page_cache_all = true;
									$this->add_report( 'using API request to get first 50 videos' );
								} else {
									$this->add_report( 'using API request to get more videos' );
								}

								$api_connect_playlist_items = $this->make_api_connection( $connected_account_for_term, 'playlistItems', $params );

								$api_connect_playlist_items->connect();

								if ( ! $api_connect_playlist_items->is_wp_error() && ! $api_connect_playlist_items->is_youtube_error() ) {
									$one_successful_connection = true;
									$data = $api_connect_playlist_items->get_data();

									if ( isset( $data['items'][0] ) ) {
										$post_set = $this->filter_posts( $data['items'], $settings );

										$this->successful_video_api_request_made = true;

										$new_post_sets[] = $post_set;
									}

									$next_page = $this->is_pageable() ? $api_connect_playlist_items->get_next_page() : false;
									if ( ! empty( $next_page ) ) {
										$next_pages[ $term . '_' . $type ] = $next_page;
										$next_page_found = true;
									} else {
										$next_pages[ $term . '_' . $type ] = false;
									}
								}
							} else {
								$this->add_report( 'no first playlist' );
							}

							if ( ! $this->is_pageable()
							     && empty( $next_pages[ $term . '_' . $type ] )
							     && ! empty( $params['channel_id'] ) ) {
								$this->add_report( 'using RSS to get first 15' );

								$rss_connect_playlist_items = new SBY_RSS_Connect( 'playlistItems', $params );

								$rss_connect_playlist_items->connect();

								$one_successful_connection = true;

								$data = $rss_connect_playlist_items->get_data();

								if ( isset( $data[0] ) ) {
									$data = array(
										'items' => $data
									);
									$post_set = $this->filter_posts( $data['items'], $settings );

									$this->successful_video_api_request_made = true;


									if ( count( $post_set ) > 14 ) {
										if ( (int)self::get_channel_status( $term ) !== 1 ) {
											$next_pages[ $term . '_' . $type ] = 'rss';
											$next_page_found = true;
										} else {
											$this->add_report( 'RSS Only' . $term );
											$post_set = $this->merge_cached_posts( $post_set, $term );
											$next_pages[ $term . '_' . $type ] = false;
										}
									} else {
										$next_pages[ $term . '_' . $type ] = false;
									}

									$new_post_sets[] = $post_set;

								}
							} else {
								$this->num_api_calls++;
							}

						}
					}
				}

			}

			if ( sby_is_pro() ) {
				// Make another API request to add video duration to existing videos
				$new_post_sets = $this->add_video_duration( $type, $new_post_sets, $connected_account_for_term, $params );
			}
		}

		if ( ! $one_successful_connection || ($one_api_request_delayed && empty( $new_post_sets )) ) {
			$this->should_use_backup = true;
		}
		$posts = $this->merge_posts( $new_post_sets, $settings );

		$posts = $this->sort_posts( $posts, $settings );

		if ( ! empty( $this->post_data ) && is_array( $this->post_data ) ) {
			$posts = array_merge( $this->post_data, $posts );
		}

		$this->post_data = $posts;

		if ( isset( $next_page_found ) && $next_page_found ) {
			$this->next_pages = $next_pages;
		} else {
			$this->next_pages = false;
		}
	}

	/**
	 * Add video durations to each videos
	 * 
	 * @since 2.1
	 */
	public function add_video_duration( $type, $posts, $connected_account_for_term, $params ) {
		if ( count( $posts ) < 1 ) {
			return $posts;
		}
		$videos_id = array();
		if ( $type == 'single' ) {
			return $posts;
		}
		if ( $type == 'channels' ||  $type == 'playlist' ) {
			foreach( $posts[0] as $post ) {
				$videos_id[] = $post['snippet']['resourceId']['videoId'];
			}
		}
		if ( $type == 'search' ) {
			foreach( $posts[0] as $post ) {
				$videos_id[] = $post['id']['videoId'];
			}
		}
		$params['ids'] = implode( '&id=', $videos_id );
		$connection = $this->make_api_connection( $connected_account_for_term, 'videosDuration', $params );
		$connection->connect();
		$posts_with_duration = $connection->get_data();

		$posts[0] = $this->marge_duration_data_to_original_posts( $posts[0], $posts_with_duration['items'], $type );
		
		return $posts;
	}

	/**
	 * Merge duration data to orignal posts 
	 * 
	 * @since 2.1
	 */
	public function marge_duration_data_to_original_posts( $original_posts, $new_posts, $type ) {
		// map over the origional posts and add video duration to contentDetails element
		$updated_posts = array_map(function( $original_post ) use ($new_posts, $type) {
			if ( $type == 'search' ) {
				$original_post_video_id = isset( $original_post['id']['videoId'] ) ? $original_post['id']['videoId'] : null;
			} else {
				$original_post_video_id = isset( $original_post['contentDetails']['videoId'] ) ? $original_post['contentDetails']['videoId'] : null;
			}
			if ( $original_post_video_id ) {
				foreach( $new_posts as $video ) {
					if ( $video['id'] == $original_post_video_id ) {
						$video_duration = isset( $video['contentDetails']['duration'] ) ? $video['contentDetails']['duration'] : null;
						$original_post['snippet']['videoDuration'] = $video_duration;
					}
				}
			}
			return $original_post;
		}, $original_posts );
		
		return $updated_posts;
	}

	public function add_remote_pageable_posts() {

	}

	public function add_remote_non_pageable() {

	}

	private function is_pageable() {
		global $sby_settings;

		return ! empty( $sby_settings['api_key'] );
	}

	public function merge_cached_posts( $current_posts, $channel_id ) {
		$args = array(
			'post_type'		=>	SBY_CPT,
			'post_status' => array( 'publish', 'pending', 'draft' ),
			'orderby' => 'date',
			'order'   => 'DESC',
			'posts_per_page' => 80,
			'meta_query'	=>	array(
				array(
					'value'	=>	$channel_id,
					'key'	=>	'sby_channel_id'
				)
			)
		);
		$feed_videos = new \WP_Query( $args );

		if ( $feed_videos->have_posts() ) {
			$posts = array();
			while ( $feed_videos->have_posts() ) {
				$feed_videos->the_post();
				$json = get_post_meta( get_the_ID(), 'sby_json', true );
				if ( $json ) {
					$posts[] = json_decode( $json, true );
				}
			}

			wp_reset_postdata();
			$this->add_report( 'merging cached posts' );

			$posts = array_merge( $current_posts, $posts );

			return $posts;
		} else {
			$this->add_report( 'no cached posts found' );
			wp_reset_postdata();

			return $current_posts;
		}
	}

	/**
	 * Connects to the YouTube API and records returned data. Will use channel data if already
	 * set by the regular feed
	 *
	 * @param $settings
	 * @param array $feed_types_and_terms organized settings related to feed data
	 *  (ex. 'user' => array( 'smashballoon', 'customyoutubefeed' )
	 * @param array $connected_accounts_for_feed connected account data for the
	 *  feed types and terms
	 *
	 * @since 1.0
	 */
	public function set_remote_header_data( $settings, $feed_types_and_terms, $connected_accounts_for_feed ) {
		if ( is_array( $feed_types_and_terms['channels'] ) && count($feed_types_and_terms['channels']) > 1 ) {
			$this->header_data = $this->process_multi_channel_header_data( $settings, $feed_types_and_terms, $connected_accounts_for_feed );
			return;
		}
		$first_user = $this->get_first_user( $feed_types_and_terms, $settings );
		$this->header_data = false;
		$existing_channel_cache = $this->get_channel_cache( $first_user );

		if ( $existing_channel_cache ) {
			$this->channels_data[ $first_user ] = $existing_channel_cache;
			$this->add_report( 'header data for ' . $first_user . ' exists in cache' );
		}

		if ( isset( $this->channels_data[ $first_user ] ) && ! $this->is_pageable() ) {
			$this->header_data = $this->channels_data[ $first_user ];
		} elseif ( ! empty( $first_user ) ) {
			$connected_account_for_term = sby_get_first_connected_account();
			if ( $connected_account_for_term['expires'] < time() + 5 ) {
				$error_message = '<p><b>' . __( 'Reconnect to YouTube to show this feed.', 'feeds-for-youtube' ) . '</b></p>';
				$error_message .= '<p>' . __( 'To create a new feed, first connect to YouTube using the "Connect to YouTube to Create a Feed" button on the settings page and connect any account.', 'feeds-for-youtube' ) . '</p>';

				if ( current_user_can( 'manage_youtube_feed_options' ) ) {
					$error_message .= '<a href="' . admin_url( 'admin.php?page=youtube-feed-settings' ) . '" target="blank" rel="noopener nofollow">' . __( 'Reconnect in the YouTube Feeds Settings Area', 'feeds-for-youtube' ) . '</a>';
				}
				global $sby_posts_manager;

				$sby_posts_manager->add_frontend_error( 'accesstoken', $error_message );
				$sby_posts_manager->add_error( 'accesstoken', array( 'Trying to connect a new account', $error_message ) );
			} else {
				$channel_params_type = strpos( $first_user, 'UC' ) !== 0 ? 'channel_name' : 'channel_id';
				$params[ $channel_params_type ] = $first_user;
				$connection = $this->make_api_connection( $connected_account_for_term, 'channels', $params );

				$connection->connect();
				$this->add_report( 'api call made for header - ' . $first_user );

				if ( ! $connection->is_wp_error() && ! $connection->is_youtube_error() ) {
					$this->header_data = $connection->get_data();
					$channel_id = SBY_Parse::get_channel_id( $this->header_data );
					$this->set_channel_cache( $channel_id, $this->header_data );
					$this->channels_data[ $channel_id ] = $this->header_data;
					$this->channels_data[ $first_user ] = $this->header_data;

					if ( isset( $connected_accounts_for_feed[ $first_user ]['local_avatar'] ) && $connected_accounts_for_feed[ $first_user ]['local_avatar'] ) {
						$upload = wp_upload_dir();
						$resized_url = trailingslashit( $upload['baseurl'] ) . trailingslashit( SBY_UPLOADS_NAME );

						$full_file_name = $resized_url . $this->header_data['username']  . '.jpg';
						$this->header_data['local_avatar'] = $full_file_name;
					}
				} else {
					if ( $connection->is_wp_error() ) {
						SBY_API_Connect::handle_wp_remote_get_error( $connection->get_wp_error() );
					} else {
						SBY_API_Connect::handle_youtube_error( $connection->get_data(), $connected_accounts_for_feed[ $first_user ], 'header' );
					}
				}
			}
		}
	}

	/**
	 * Process header data when a feed has multiple channel sources
	 * 
	 * @since 2.0.8
	 */
	public function process_multi_channel_header_data( $settings, $feed_types_and_terms, $connected_accounts_for_feed ) {
		$multiple_header_data = array();
		foreach( $feed_types_and_terms['channels'] as $channel ) {
			if ( isset( $channel['term'] ) ) {
				$channel_term = $channel['term'];
				$existing_channel_cache = $this->get_channel_cache( $channel_term );

				if ( $existing_channel_cache ) {
					$this->channels_data[ $channel_term ] = $existing_channel_cache;
					$this->add_report( 'header data for ' . $channel_term . ' exists in cache' );
				}

				if ( isset( $this->channels_data[ $channel_term ] ) && ! $this->is_pageable() ) {
					$multiple_header_data[$channel_term] = $this->channels_data[ $channel_term ];
				} elseif ( ! empty( $channel_term ) ) {
					$connected_account_for_term = sby_get_first_connected_account();
					if ( $connected_account_for_term['expires'] < time() + 5 ) {
						$error_message = '<p><b>' . __( 'Reconnect to YouTube to show this feed.', 'feeds-for-youtube' ) . '</b></p>';
						$error_message .= '<p>' . __( 'To create a new feed, first connect to YouTube using the "Connect to YouTube to Create a Feed" button on the settings page and connect any account.', 'feeds-for-youtube' ) . '</p>';
		
						if ( current_user_can( 'manage_youtube_feed_options' ) ) {
							$error_message .= '<a href="' . admin_url( 'admin.php?page=youtube-feed-settings' ) . '" target="blank" rel="noopener nofollow">' . __( 'Reconnect in the YouTube Feeds Settings Area', 'feeds-for-youtube' ) . '</a>';
						}
						global $sby_posts_manager;
		
						$sby_posts_manager->add_frontend_error( 'accesstoken', $error_message );
						$sby_posts_manager->add_error( 'accesstoken', array( 'Trying to connect a new account', $error_message ) );
					} else {
						$channel_params_type = strpos( $channel_term, 'UC' ) !== 0 ? 'channel_name' : 'channel_id';
						$params[ $channel_params_type ] = $channel_term;
						$connection = $this->make_api_connection( $connected_account_for_term, 'channels', $params );
		
						$connection->connect();
						$this->add_report( 'api call made for header - ' . $channel_term );
		
						if ( ! $connection->is_wp_error() && ! $connection->is_youtube_error() ) {
							$multiple_header_data[$channel_term] = $connection->get_data();
							$channel_id = SBY_Parse::get_channel_id( $this->header_data );
							$this->set_channel_cache( $channel_id, $this->header_data );
							$this->channels_data[ $channel_id ] = $this->header_data;
							$this->channels_data[ $channel_term ] = $this->header_data;
		
							if ( isset( $connected_accounts_for_feed[ $channel_term ]['local_avatar'] ) && $connected_accounts_for_feed[ $channel_term ]['local_avatar'] ) {
								$upload = wp_upload_dir();
								$resized_url = trailingslashit( $upload['baseurl'] ) . trailingslashit( SBY_UPLOADS_NAME );
		
								$full_file_name = $resized_url . $this->header_data['username']  . '.jpg';
								$this->header_data['local_avatar'] = $full_file_name;
							}
						} else {
							if ( $connection->is_wp_error() ) {
								SBY_API_Connect::handle_wp_remote_get_error( $connection->get_wp_error() );
							} else {
								SBY_API_Connect::handle_youtube_error( $connection->get_data(), $connected_accounts_for_feed[ $channel_term ], 'header' );
							}
						}
					}
				}
			} 
		}

		return $multiple_header_data;
	}

	/**
	 * Stores feed data in a transient for a specified time
	 *
	 * @param int $cache_time
	 *
	 * @since 1.0
	 */
	public function cache_feed_data( $cache_time ) {
		if ( ! empty( $this->post_data ) || ! empty( $this->next_pages ) ) {
			$this->remove_duplicate_posts();
			$this->trim_posts_to_max();

			$post_data = $this->post_data;

			if (! isset( $post_data[0]['iframe'] )) {
				$to_cache = array(
					'data' => $this->post_data,
					'pagination' => $this->next_pages
				);

				set_transient( $this->regular_feed_transient_name, wp_json_encode( $to_cache ), $cache_time );
			} else {
				$this->add_report( 'iframe not caching' );
			}


		} else {
			$this->add_report( 'no data not caching' );
		}
	}

	/**
	 * Stores feed data with additional data specifically for cron caching
	 *
	 * @param array $to_cache feed data with additional things like the shortcode
	 *  settings, when the cache was last requested, when new posts were last retrieved
	 * @param int $cache_time how long the cache will last
	 *
	 * @since 1.0
	 */
	public function set_cron_cache( $to_cache, $cache_time ) {
		if ( ! empty( $this->post_data )
		     || ! empty( $this->next_pages )
		     || ! empty( $to_cache['data'] ) ) {
			$this->remove_duplicate_posts();
			$this->trim_posts_to_max();

			$to_cache['data'] = isset( $to_cache['data'] ) ? $to_cache['data'] : $this->post_data;
			$to_cache['pagination'] = isset( $to_cache['next_pages'] ) ? $to_cache['next_pages'] : $this->next_pages;
			$to_cache['atts'] = isset( $to_cache['atts'] ) ? $to_cache['atts'] : $this->transient_atts;
			$to_cache['last_requested'] = isset( $to_cache['last_requested'] ) ? $to_cache['last_requested'] : time();
			$to_cache['last_retrieve'] = isset( $to_cache['last_retrieve'] ) ? $to_cache['last_retrieve'] : $this->last_retrieve;

			set_transient( $this->regular_feed_transient_name, wp_json_encode( $to_cache ), $cache_time );
		} else {
			$this->add_report( 'no data not caching' );
		}

	}

	/**
	 * Stores header data for a specified time as a transient
	 *
	 * @param int $cache_time
	 * @param bool $save_backup
	 *
	 * @since 1.0
	 */
	public function cache_header_data( $cache_time, $save_backup = true ) {
		if ( $this->header_data ) {
			set_transient( $this->header_transient_name, wp_json_encode( $this->header_data ), $cache_time );

			if ( $save_backup ) {
				update_option( $this->backup_header_transient_name, wp_json_encode( $this->header_data ), false );
			}
		}
	}

	/**
	 * Used to randomly trigger an updating of the last requested data for cron caching
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function should_update_last_requested() {
		return (rand( 1, 20 ) === 20);
	}

	/**
	 * Determines if pagination can and should be used based on settings and available feed data
	 *
	 * @param array $settings
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function should_use_pagination( $settings, $offset = 0 ) {
		$posts_available = count( $this->post_data ) - ($offset + $settings['num']);
		$show_loadmore_button_by_settings = ($settings['showbutton'] == 'on' || $settings['showbutton'] == 'true' || $settings['showbutton'] == true ) && $settings['showbutton'] !== 'false';

		if ( $show_loadmore_button_by_settings ) {
			// used for permanent and whitelist feeds
			if ( $this->feed_is_complete( $settings, $offset ) ) {
				$this->add_report( 'no pagination, feed complete' );
				return false;
			}
			if ( $posts_available > 0 ) {
				$this->add_report( 'do pagination, posts available' );
				return true;
			}
			$pages = $this->next_pages;

			if ( $pages && ! $this->should_use_backup() ) {
				foreach ( $pages as $page ) {
					if ( ! empty( $page ) ) {
						return true;
					}
				}
			}

		}


		$this->add_report( 'no pagination, no posts available' );

		return false;
	}

	public static function get_channel_status( $channel ) {
		$channel_setting = get_option( 'sby_channel_status', array() );

		if ( isset( $channel_setting[ $channel ] ) ) {
			return $channel_setting[ $channel ];
		}

		return 0;
	}

	public static function update_channel_status( $channel, $status ) {
		$channel_setting = get_option( 'sby_channel_status', array() );

		$channel_setting[ $channel ] = $status;

		update_option( 'sby_channel_status', $channel_setting, false );
	}

	/**
	 * Generates the HTML for the feed if post data is available. Although it seems
	 * some of the variables ar not used they are set here to hide where they
	 * come from when used in the feed templates.
	 *
	 * @param array $settings
	 * @param array $atts
	 * @param array $feed_types_and_terms organized settings related to feed data
	 *  (ex. 'user' => array( 'smashballoon', 'customyoutubefeed' )
	 * @param array $connected_accounts_for_feed connected account data for the
	 *  feed types and terms
	 *
	 * @return false|string
	 *
	 * @since 1.0
	 */
	public function get_the_feed_html( $settings, $atts, $feed_types_and_terms, $connected_accounts_for_feed ) {
		global $sby_posts_manager;
		if ( empty( $this->post_data ) && ! empty( $connected_accounts_for_feed ) ) {

			$error_template = "<p><b>%s</b><p>%s</p>";
			$error_title = __( 'Error: No videos found.', 'feeds-for-youtube' );
			$error_description = __( 'Make sure this is a valid channel ID and that the channel has videos available on youtube.com.', 'feeds-for-youtube' );

			if ( ! $this->feed_exists( $settings['feed'] ) ) {
				$error_title = sprintf( __( 'Error: No feed found with the ID %s.', 'feeds-for-youtube' ),
					$settings['feed'] );
				$error_description = __( 'Go to the All Feeds page and select an ID from an existing feed.', 'feeds-for-youtube' );
			}

			$sby_posts_manager->add_frontend_error( 'noposts', sprintf($error_template, $error_title, $error_description) );
		}

		$posts = array_slice( $this->post_data, 0, $settings['num'] );
		$header_data = ! empty( $this->header_data ) ? $this->header_data : false;

		$first_username = false;
		if ( $header_data ) {
			$first_username = SBY_Parse::get_channel_id( $header_data );
		} elseif ( isset( $this->post_data[0] ) ) { // in case no connected account for feed
			$first_username = SBY_Parse::get_channel_id( $this->post_data[0] );
		}

		$use_pagination = $this->should_use_pagination( $settings, 0 );

		$feed_id = $this->regular_feed_transient_name;
		$shortcode_atts = ! empty( $atts ) ? wp_json_encode( $atts ) : '{}';

		$settings['header_outside'] = false;
		$settings['header_inside'] = false;
		if ( $header_data && $settings['showheader'] ) {
			$settings['header_inside'] = true;
		}

		$other_atts = '';

        // The plugin settings did not mention heightunit but instead accept px or % in the height option directly. This is a check for this to make sure heightunit is assigned properly.
        $settings['heightunit'] = ( strpos( $settings['height'], 'px' ) === false && strpos( $settings['height'], '%' ) === false ) ? $settings['heightunit'] : (strpos( $settings['height'], 'px' ) !== false ? 'px' : '%' );

		$classes = array();
		if ( empty( $settings['widthresp'] ) || $settings['widthresp'] == 'on' || $settings['widthresp'] == 'true' || $settings['widthresp'] === true ) {
			if ( $settings['widthresp'] !== 'false' ) {
				$classes[] = 'sby_width_resp';
			}
		}
		if ( ! empty( $settings['class'] ) ) {
			$classes[] = esc_attr( $settings['class'] );
		}
		if ( ! empty( $settings['height'] )
		     && (((int)$settings['height'] < 100 && $settings['heightunit'] === '%') || $settings['heightunit'] === 'px') ) {
			$classes[] = 'sby_fixed_height';
		}
		if ( ! empty( $settings['disablemobile'] )
		     && ($settings['disablemobile'] == 'on' || $settings['disablemobile'] == 'true' || $settings['disablemobile'] == true) ) {
			if ( $settings['disablemobile'] !== 'false' ) {
				$classes[] = 'sby_disable_mobile';
			}
		}
		if ( isset( $atts['classname'] ) ) {
			$classes[] = sanitize_text_field( $atts['classname'] );
		}

		$additional_classes = '';
		if ( ! empty( $classes ) ) {
			$additional_classes = ' ' . esc_attr( implode( ' ', $classes ) );
		}

		$other_atts = $this->add_other_atts( $other_atts, $settings );

		$flags = array();

		if ( $this->successful_video_api_request_made && ! empty( $posts ) ) {
			if ( $settings['storage_process'] === 'page' ) {
				$this_posts = $posts;
				if ( $this->do_page_cache_all ) {
					$this_posts = $this->post_data;
				}
				$this->add_report( 'Adding videos to wp_posts ' . count( $this_posts ) );

				AdminAjaxService::sby_process_post_set_caching( $this_posts, $feed_id );
			} elseif ( $settings['storage_process'] === 'background' ) {
				$flags[] = 'checkWPPosts';
				if ( $this->do_page_cache_all ) {
					$this->add_report( 'Flagging videos to wp_posts ' . count( $this->post_data ) );
					$flags[] = 'cacheAll';
				}
			}
		}

		if ( $settings['disable_resize'] ) {
			$flags[] = 'resizeDisable';
		} elseif ( $settings['favor_local'] ) {
			$flags[] = 'favorLocal';
		}

		if ( $settings['global_settings']['disable_js_image_loading'] ) {
			$flags[] = 'imageLoadDisable';
		}
		if ( $settings['ajax_post_load'] ) {
			$flags[] = 'ajaxPostLoad';
		}
		if ( $settings['playerratio'] === '3:4' ) {
			$flags[] = 'narrowPlayer';
		}
		if ( SBY_GDPR_Integrations::doing_gdpr( $settings ) ) {
			$flags[] = 'gdpr';
		}
		if ( ! is_admin()
		     && Feed_Locator::should_do_ajax_locating( $this->regular_feed_transient_name, get_the_ID() ) ) {
			$flags[] = 'locator';
		}
    	if ( $settings['global_settings']['disablecdn'] ) {
			$flags[] = 'disablecdn';
		}
		if ( isset( $_GET['sb_debug'] ) ) {
			$flags[] = 'debug';
		}

        if ( ! empty( $settings['allowcookies'] )
        && ( $settings['allowcookies'] == 'on' || $settings['allowcookies'] == 'true' || $settings['allowcookies'] == true) ) {
			$flags[] = 'allowcookies';
		}
		if ( isset( $atts['allowcookies'] ) && $atts['allowcookies'] == true ) {
			$flags[] = 'allowcookies';
		}

		if ( ! empty( $flags ) ) {
			if ( sby_doing_customizer( $settings ) ) {
				$other_atts .= ' :data-sby-flags="$parent.getFlagsAttr()"';
			} else {
				$other_atts .= ' data-sby-flags="' . esc_attr( implode(',', $flags ) ) . '"';
			}
		}
		$other_atts .= ' data-postid="' . esc_attr( get_the_ID() ) . '"';
		if ( $settings['layout'] === 'grid' || $settings['layout'] === 'carousel' ) {
			$other_atts .= ' data-sby-supports-lightbox="1"';
		}
		$icon_type = $settings['font_method'];

		ob_start();
		include sby_get_feed_template_part( 'feed', $settings );
		$html = ob_get_contents();
		ob_get_clean();

		if ( $settings['ajaxtheme'] ) {
			$html .= $this->get_ajax_page_load_html($settings);
		}

		return $html;
	}

	/**
	 * Generates HTML for individual sby_item elements
	 *
	 * @param array $settings
	 * @param int $offset
	 * @param array $feed_types_and_terms organized settings related to feed data
	 *  (ex. 'user' => array( 'smashballoon', 'customyoutubefeed' )
	 * @param array $connected_accounts_for_feed connected account data for the
	 *  feed types and terms
	 *
	 * @return false|string
	 *
	 * @since 1.0
	 */
	public function get_the_items_html( $settings, $offset, $feed_types_and_terms = array(), $connected_accounts_for_feed = array() ) {
		if ( empty( $this->post_data ) ) {
			ob_start();
			$html = ob_get_contents();
			ob_get_clean();		?>
            <p><?php _e( 'No posts found.', 'feeds-for-youtube' ); ?></p>
			<?php
			$html = ob_get_contents();
			ob_get_clean();
			return $html;
		}

		$posts = array_slice( $this->post_data, $offset, $settings['num'] );

		ob_start();

		$this->posts_loop( $posts, $settings, $offset );

		$html = ob_get_contents();
		ob_get_clean();

		return $html;
	}

	/**
	 * Overwritten in the Pro version
	 *
	 * @return object
	 */
	public function make_api_connection( $connected_account_or_page, $type = NULL, $params = NULL ) {
		return new SBY_API_Connect( $connected_account_or_page, $type, $params );
	}

	/**
	 * When the feed is loaded with AJAX, the JavaScript for the plugin
	 * needs to be triggered again. This function is a workaround that adds
	 * the file and settings to the page whenever the feed is generated.
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function get_ajax_page_load_html($settings) {
		$js_options = array(
			'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
			'placeholder' => trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png',
			'placeholderNarrow' => trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder-narrow.png',
			'lightboxPlaceholder' => trailingslashit( SBY_PLUGIN_URL ) . 'img/lightbox-placeholder.png',
			'lightboxPlaceholderNarrow' => trailingslashit( SBY_PLUGIN_URL ) . 'img/lightbox-placeholder-narrow.png',
			'autoplay' => $settings['playvideo'] === 'automatically',
			'semiEagerload' => $settings['eagerload'],
			'eagerload' => $settings['eagerload']
		);

		$encoded_options = wp_json_encode( $js_options );

		$js_option_html = '<script type="text/javascript">if (typeof sbyOptions === "undefined") var sbyOptions = ' . $encoded_options . ';</script>';
		$js_option_html .= "<script type='text/javascript' src='" . trailingslashit( SBY_PLUGIN_URL ) . 'js/sb-youtube.min.js?ver=' . SBYVER . "'></script>";

		return $js_option_html;
	}

	/**
	 * Overwritten in the Pro version
	 *
	 * @param $feed_types_and_terms
	 *
	 * @return string
	 *
	 * @since 2.1/5.2
	 */
	public function get_first_user( $feed_types_and_terms, $settings = array() ) {
		if ( isset( $feed_types_and_terms['channels'][0] ) ) {
			return $feed_types_and_terms['channels'][0]['term'];
		} else {
			return '';
		}
	}

	public function do_page_cache_all() {
		return $this->do_page_cache_all;
	}

	public function successful_video_api_request_made() {
		return $this->successful_video_api_request_made;
	}

	/**
	 * Adds recorded strings to an array
	 *
	 * @param $to_add
	 *
	 * @since 1.0
	 */
	public function add_report( $to_add ) {
		$this->report[] = $to_add;
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_report() {
		return $this->report;
	}

	/**
	 * Additional options/settings added to the main div
	 * for the feed
	 *
	 * Overwritten in the Pro version
	 *
	 * @param $other_atts
	 * @param $settings
	 *
	 * @return string
	 */
	protected function add_other_atts( $other_atts, $settings ) {
		return '';
	}

	/**
	 * Used for filtering a single API request worth of posts
	 *
	 * Overwritten in the Pro version
	 *
	 * @param array $post_set a single set of post data from the api
	 *
	 * @return mixed|array
	 *
	 * @since 1.0
	 */
	protected function filter_posts( $post_set, $settings = array() ) {
		// array_unique( $post_set, SORT_REGULAR);

		return $post_set;
	}

	protected function remove_duplicate_posts() {
		$posts = $this->post_data;
		$ids_in_feed = array();
		$non_duplicate_posts = array();
		$removed = array();

		foreach ( $posts as $post ) {
			$post_id = SBY_Parse::get_video_id( $post );
			if ( ! in_array( $post_id, $ids_in_feed, true ) ) {
				$ids_in_feed[] = $post_id;
				$non_duplicate_posts[] = $post;
			} else {
				$removed[] = $post_id;
			}
		}

		$this->add_report( 'removed duplicates: ' . implode(', ', $removed ) );
		$this->set_post_data( $non_duplicate_posts );
	}

	/**
	 * Used for limiting the cache size
	 *
	 * @since 2.0/5.1.1
	 */
	protected function trim_posts_to_max() {
		if ( ! is_array( $this->post_data ) ) {
			return;
		}

		$max = apply_filters( 'sby_max_cache_size', 500 );
		$this->set_post_data( array_slice( $this->post_data , 0, $max ) );

	}

	/**
	 * Used for permanent feeds or white list feeds to
	 * stop pagination if all posts are already added
	 *
	 * Overwritten in the Pro version
	 *
	 * @param array $settings
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	protected function feed_is_complete( $settings, $offset = 0 ) {
		return false;
	}

	/**
	 * Iterates through post data and tracks the index of the current post.
	 * The actual post ids of the posts are stored in an array so the plugin
	 * can search for local images that may be available.
	 *
	 * @param array $posts final filtered post data for the feed
	 * @param array $settings
	 * @param int $offset
	 *
	 * @since 1.0
	 */
	protected function posts_loop( $posts, $settings, $offset = 0 ) {
		$header_data = $this->get_header_data();
		$image_ids = array();
		$post_index = $offset;
		if ( ! isset( $settings['feed_id'] ) ) {
			$settings['feed_id'] = $this->regular_feed_transient_name;
		}
		$misc_data = $this->get_misc_data( $settings['feed_id'], $posts );
		$icon_type = $settings['font_method'];

		foreach ( $posts as $post ) {
			$image_ids[] = SBY_Parse::get_post_id( $post );
			include sby_get_feed_template_part( 'item', $settings );
			$post_index++;
		}

		$this->image_ids_post_set = $image_ids;
	}

	/**
	 * Uses array of API request results and merges them based on how
	 * the feed should be sorted. Mixed feeds are always sorted alternating
	 * since there is no post date for hashtag feeds.
	 *
	 * @param array $post_sets an array of single API request worth
	 *  of posts
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	private function merge_posts( $post_sets, $settings ) {

		$merged_posts = array();
		if ( $settings['sortby'] === 'alternate' ) {
			// don't bother merging posts if there is only one post set
			if ( isset( $post_sets[1] ) ) {
				$min_cycles = max( 1, (int)$settings['num'] );
				for( $i = 0; $i <= $min_cycles; $i++ ) {
					foreach ( $post_sets as $post_set ) {
						if ( isset( $post_set[ $i ] ) && isset( $post_set[ $i ]['id'] ) ) {
							$merged_posts[] = $post_set[ $i ];
						}
					}
				}
			} else {
				$merged_posts = isset( $post_sets[0] ) ? $post_sets[0] : array();
			}
		} elseif ( $settings['sortby'] === 'api' ) {
			if ( isset( $post_sets[0] ) ) {
				foreach ( $post_sets as $post_set ) {
					$merged_posts = array_merge( $merged_posts, $post_set );
				}
			}
		} else {
			// don't bother merging posts if there is only one post set
			if ( isset( $post_sets[1] ) ) {
				foreach ( $post_sets as $post_set ) {
					if ( isset( $post_set[0]['id'] ) ) {
						$merged_posts = array_merge( $merged_posts, $post_set );
					}
				}
			} else {
				$merged_posts = isset( $post_sets[0] ) ? $post_sets[0] : array();
			}
		}


		return $merged_posts;
	}

	/**
	 * Sorts a post set based on sorting settings. Sorting by "alternate"
	 * is done when merging posts for efficiency's sake so the post set is
	 * just returned as it is.
	 *
	 * @param array $post_set
	 * @param array $settings
	 *
	 * @return mixed|array
	 *
	 * @since 1.0
	 */
	protected function sort_posts( $post_set, $settings ) {
		if ( empty( $post_set ) ) {
			return $post_set;
		}

		// sorting done with "merge_posts" to be more efficient
		if ( $settings['sortby'] === 'alternate' || $settings['sortby'] === 'api' ) {
			$return_post_set = $post_set;
		} elseif ( $settings['sortby'] === 'random' ) {
			/*
             * randomly selects posts in a random order. Cache saves posts
             * in this random order so paginating does not cause some posts to show up
             * twice or not at all
             */
			usort($post_set, 'sby_rand_sort' );
			$return_post_set = $post_set;

		} else {
			// compares posted on dates of posts
			usort($post_set, 'sby_date_sort' );
			$return_post_set = $post_set;
		}

		/**
		 * Apply a custom sorting of posts
		 *
		 * @param array $return_post_set    Ordered set of filtered posts
		 * @param array $settings           Settings for this feed
		 *
		 * @since 1.0
		 */

		return apply_filters( 'sby_sorted_posts', $return_post_set, $settings );
	}

	/**
	 * Can trigger a second attempt at getting posts from the API
	 *
	 * Overwritten in the Pro version
	 *
	 * @param string $type
	 * @param array $connected_account_with_error
	 * @param int $attempts
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	protected function can_try_another_request( $type, $connected_account_with_error, $attempts = 0 ) {
		return false;
	}

	/**
	 * returns a second connected account if it exists
	 *
	 * Overwritten in the Pro version
	 *
	 * @param string $type
	 * @param array $attempted_connected_accounts
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	protected function get_different_connected_account( $type, $attempted_connected_accounts ) {
		return false;
	}

	private function feed_exists( $id ) {
		$builder    = \Smashballoon\Customizer\Container::getInstance()->get( Feed_Builder::class );
		$feeds_list = $builder->get_feed_list( [ "id" => $id ], true );

		return ! empty( $feeds_list );
	}
}
