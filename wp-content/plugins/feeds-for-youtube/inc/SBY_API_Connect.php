<?php

namespace SmashBalloon\YouTubeFeed;
class SBY_API_Connect
{
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var object
	 */
	protected $response;

	/**
	 * @var array
	 */
	private $args;

	public function __construct( $connected_account_or_url = '', $endpoint = '', $params = array() ) {
		if ( is_array( $connected_account_or_url ) && isset( $connected_account_or_url['access_token'] ) ) {
			$this->set_url( $connected_account_or_url, $endpoint, $params );
		} elseif ( is_array( $connected_account_or_url ) ) {
			$this->set_url( $connected_account_or_url, $endpoint, $params );
		} elseif ( strpos( $connected_account_or_url, 'https' ) !== false ) {
			$this->url = $connected_account_or_url;
		} else {
			$this->url = '';
		}
		$this->set_args();
	}

	public function get_data() {
		if (!is_wp_error($this->response) && !empty($this->response['data'])) {
			return $this->response['data'];
		} else {
			return $this->response;
		}
	}

	public function get_wp_error() {
		if ( $this->is_wp_error() ) {
			return array( 'response' => $this->response, 'url' => $this->url );
		} else {
			return false;
		}
	}

	public function get_next_page( $params = false ) {
		if ( ! empty( $this->response['nextPageToken'] ) ) {
			return $this->response['nextPageToken'];
		} else {
			return '';
		}
	}

	public function set_url_from_args( $url ) {
		$this->url = $url;
	}

	public function set_args( $args = array() ) {
		if ( empty( $args ) ) {
			$this->args = array(
				'timeout' => 60,
				'headers' => array(
					'referer' => home_url()
				)
			);
		} else {
			$this->args = $args;
		}
	}

	public function get_url() {
		return $this->url;
	}

	public function get_args() {
		return $this->args;
	}

	public function is_wp_error() {
		return is_wp_error( $this->response );
	}


	public function is_youtube_error() {
		return (is_wp_error( $this->response ) || isset( $this->response['error'] ));
	}

	public function connect() {
		$response = wp_remote_get( esc_url_raw( $this->url ), $this->get_args() );

		if ( ! is_wp_error( $response ) ) {
			// certain ways of representing the html for double quotes causes errors so replaced here.
			$response = json_decode( str_replace( '%22', '&rdquo;', $response['body'] ), true );
		}

		$this->response = $response;
	}

	public static function handle_youtube_error( $response, $error_connected_account, $request_type = '' ) {
		//
		if ( isset( $response['error'] ) ) {
			if ( isset( $response['error']['errors'][0]['reason'] ) && $response['error']['errors'][0]['message'] === 'Invalid Credentials' ) {
				$error_message = '<p><b>' . __( 'Reconnect to YouTube to show this feed.', 'feeds-for-youtube' ) . '</b></p>';
				$error_message .= '<p>' . __( 'To create a new feed, first connect to YouTube using the "Connect to YouTube to Create a Feed" button on the settings page and connect any account.', 'feeds-for-youtube' ) . '</p>';

				if ( current_user_can( 'manage_youtube_feed_options' ) ) {
					$error_message .= '<a href="' . admin_url( 'admin.php?page=youtube-feed-settings' ) . '" target="blank" rel="noopener nofollow">' . __( 'Reconnect in the YouTube Feeds Settings Area', 'feeds-for-youtube' ) . '</a>';
				}
				global $sby_posts_manager;

				$sby_posts_manager->add_frontend_error( 'accesstoken', $error_message );
				$sby_posts_manager->add_error( 'accesstoken', array( 'Trying to connect a new account', $error_message ) );

				return false;
			} elseif ( isset( $response['error']['errors'][0]['reason'] ) ) {
				$error = $response['error']['errors'][0]['message'];

				$error_message = '<p><b>'. sprintf( __( 'Error %s: %s.', 'feeds-for-youtube' ), $response['error']['code'], $error )  .'</b></p>';
				$error_message .= '<p>Domain code: ' . $response['error']['errors'][0]['domain'];
				$error_message .= '<br>Reason code: ' . $response['error']['errors'][0]['reason'];
				if ( current_user_can( 'manage_youtube_feed_options' ) ) {
					if ( isset( $response['error']['errors'][0]['extendedHelp'] ) ) {
						$error_message .= '<br>Extended Help Link: ' . $response['error']['errors'][0]['extendedHelp'];
					}
					$error_message .= '</p>';

					$error_message .= '<a href="https://smashballoon.com/youtube-feed/docs/errors/" target="blank" rel="noopener nofollow">' . __( 'Directions on how to resolve this issue', 'feeds-for-youtube' ) . '</a>';
				} else {
					$error_message .= '</p>';
				}

				global $sby_posts_manager;

				$sby_posts_manager->add_frontend_error( 'api', $error_message );
				$sby_posts_manager->add_error( 'api', array( 'Error connecting', $error_message ) );

				$sby_posts_manager->add_api_request_delay( 300 );
			}
		}
	}

	public static function handle_wp_remote_get_error( $response ) {
		$response_array = array();
		if ( is_wp_error( $response ) ) {
			$response_array = array(
				'url' => '',
				'response' => $response
			);
		} else {
			$response_array = $response;
		}

		$message = sprintf( __( 'Error connecting to %s.', 'feeds-for-youtube' ), $response_array['url'] ). ' ';
		if ( isset( $response_array['response'] ) && isset( $response_array['response']->errors ) ) {
			foreach ( $response_array['response']->errors as $key => $item ) {
				$message .= ' '.$key . ' - ' . $item[0] . ' |';
			}
		}

		global $sby_posts_manager;

		$sby_posts_manager->add_api_request_delay( 300 );

		$sby_posts_manager->add_error( 'connection', array( 'Error connecting', $message ) );
	}

	protected function formatted_param_string( $params ) {
		$param_string= '';
		foreach ( $params as $param => $value ) {
			if ( $param !== 'part' && $param !== 'num' ) {
				$param_string .= '&' . $param . '=' . $value;
			}
		}

		return $param_string;
	}

	protected function formatted_part_param_string( $part ) {
		if ( is_array( $part ) ) {
			return implode(',', str_replace( ' ', '', $part ) );
		}
		return str_replace( ' ', '', $part );
	}
	
	public function set_url( $connected_account, $endpoint_slug, $params = [], $api_key = null ) {
		$num = ! empty( $params['num'] ) ? (int)$params['num'] : 50;

		if ( empty( $connected_account ) && ! empty( $api_key ) ) {
			$connected_account = array(
				'api_key' => $api_key
			);
		}

		$access_credentials = isset( $connected_account['api_key'] ) ? 'key=' . $connected_account['api_key'] : 'access_token=' . $connected_account['access_token'];
		$next_page = '';
		if ( isset( $params['nextPageToken'] ) && ! is_array( $params['nextPageToken'] ) ) {
			$next_page = '&pageToken=' . $params['nextPageToken'];
		}

		if ( $endpoint_slug === 'tokeninfo' ) {
			$url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $connected_account['access_token'];
		} elseif ( $endpoint_slug === 'channels' ) {
			$channel_param = 'mine=true';
			if ( isset( $params['channel_name'] ) ) {
				$channel_param = 'forUsername=' . $params['channel_name'];
			} elseif ( !empty( $params['channel_handle']  ) ){
				$channel_param = 'forHandle=' . $params['channel_handle'];
			} elseif ( isset( $params['channel_id'] ) ) {
				$channel_param = 'id=' . $params['channel_id'];
			}

			$url = 'https://www.googleapis.com/youtube/v3/channels?part=id,snippet,statistics,contentDetails&'.$channel_param.'&' . $access_credentials . $next_page;
		} elseif ( $endpoint_slug === 'live' ) {
			$url = 'iframe_'.$params['channelId'];
		} elseif ( $endpoint_slug === 'search' ) {
			$part = 'snippet';
			if ( isset( $params['part'] ) ) {
				$part = $this->formatted_part_param_string( $params['part'] );
			}
			if ( ! isset( $params['isCustom'] ) ) {
				if ( isset( $params['eventType'] ) && $params['eventType'] === 'upcoming' ) {
					$num = 50; // get max videos so we can reverse sort them to show soonest playing live streams first, default order is by publish date
				}
				$params_string = $this->formatted_param_string( $params );

			} else {
				$params_string = $params['customSearch'];

				if ( isset( $params['nextPageToken'] ) ) {
					$params_string .= '&pageToken=' . $params['nextPageToken'];
				}
			}
			$num = max( 10, $num );

			$query_var_string= 'type=video&part='.$part.'&maxResults=' . $num . $params_string;

			$url = 'https://www.googleapis.com/youtube/v3/search?'.$query_var_string.'&'.$access_credentials.$next_page;
		} elseif ( $endpoint_slug === 'playlistItems' ) {
			$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=id,snippet,contentDetails,status&maxResults='.$num.'&playlistId='.$params['playlist_id'].'&' . $access_credentials.$next_page;
		} elseif ( $endpoint_slug === 'single' ) {
			$part = 'id,statistics,snippet,liveStreamingDetails';

			$vid_ids = empty( $params['nextPageToken'] ) ? $params['video_ids'] : $params['nextPageToken'];
			$vid_ids = array_slice( $vid_ids, 0, SBY_MAX_SINGLE_PAGE );
			$vid_id_string  = implode( ',', $vid_ids );

			$url = 'https://www.googleapis.com/youtube/v3/videos?part='.$part.'&id='.$vid_id_string.'&maxResults=50&' . $access_credentials;

		} elseif ( $endpoint_slug === 'videos' ) {
			$params_string = $this->formatted_param_string( $params );
			$part = 'id,statistics';
			if ( isset( $params['part'] ) ) {
				$part = $this->formatted_part_param_string( $params['part'] );
			}

			$url = 'https://www.googleapis.com/youtube/v3/videos?part='.$part.$params_string.'&maxResults='.$num.'&' . $access_credentials;
		} else {
			$channel_param = 'mine=true';
			if ( isset( $params['username'] ) ) {
				$channel_param = 'forUsername=' . $params['username'];
			} elseif ( isset( $params['channel_id'] ) ) {
				$channel_param = 'id=' . $params['channel_id'];
			}

			$url = 'https://www.googleapis.com/youtube/v3/channels?part=id,snippet&'.$channel_param.'&' . $access_credentials.$next_page;
		}

		$this->set_url_from_args( $url );
	}

	public static function refresh_token( $client_id, $refresh_token, $client_secret ) {
		$response = wp_remote_post( 'https://www.googleapis.com/oauth2/v4/token/?client_id=' . $client_id . '&client_secret=' . $client_secret . '&refresh_token='. $refresh_token . '&grant_type=refresh_token' );

		if ( $response['response']['code'] === 200 ) {
			$return = json_decode( $response['body'], true );
		} else {
			$return = array();
		}

		return $return;
	}

}
