<?php

namespace SmashBalloon\YouTubeFeed;

class SBY_RSS_Connect
{
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var object
	 */
	private $response;

	private $is_live_stream;

	public function __construct( $endpoint = '', $params = array() ) {
		$this->is_live_stream = false;
		if ( isset( $params['livestream'] ) ) {
			$this->is_live_stream = true;
		}
		$this->set_url( $endpoint, $params );
	}

	public function get_data() {
		return $this->response;
	}

	public function set_url_from_args( $url ) {
		$this->url = $url;
	}

	public function get_url() {
		return $this->url;
	}

	public function connect() {

		if ( ! ini_get( 'allow_url_fopen' ) ) {
			if ( ! $this->is_live_stream ) {
				$error_message = '<p><b>'. __( 'Unable to retrieve new videos without an API key.', 'feeds-for-youtube' ) .'</b></p>';
				if ( current_user_can( 'manage_youtube_feed_options' ) ) {
					$error_message .= '<p>' . sprintf( __( 'Due to your server configuration, an API key is required to update your feed. See %sthis FAQ%s to set up an API key.', 'feeds-for-youtube' ), '<a href="https://smashballoon.com/youtube-api-key/" target="_blank" rel="noopener nofollow">', '</a>' ) . '</p>';
				}
			} else {
				$error_message = '<p><b>'. __( 'Unable to retrieve new videos due to server configuration.', 'feeds-for-youtube' ) .'</b></p>';
				if ( current_user_can( 'manage_youtube_feed_options' ) ) {
					$error_message .= '<p>' . sprintf( __( 'You must have the allow_url_fopen directive enabled in your server\'s php.ini file to retrieve live streams.', 'feeds-for-youtube' ), '<a href="https://smashballoon.com/youtube-api-key/" target="_blank" rel="noopener nofollow">', '</a>' ) . '</p>';
				}
			}

			global $sby_posts_manager;

			$sby_posts_manager->add_frontend_error( 'api', $error_message );
			$sby_posts_manager->add_error( 'api', array( 'Error connecting', $error_message ) );

			$sby_posts_manager->add_api_request_delay( 300 );

			return array();
		}

		if ( wp_remote_retrieve_response_code( wp_remote_get( $this->url ) ) === 404 ) {
			$error_message = '<p><b>'. __( 'Cannot collect videos from this channel. Please make sure this is a valid channel ID.', 'feeds-for-youtube' ) .'</b></p>';

			global $sby_posts_manager;

			$sby_posts_manager->add_frontend_error( 'api', $error_message );
			$sby_posts_manager->add_error( 'api', array( 'Error connecting', $error_message ) );

			$sby_posts_manager->add_api_request_delay( 300 );

			return array();
		}

		$parsed_obj = new \SimpleXMLElement( $this->url, null, true );

		$items_array = array();
		if ( isset( $parsed_obj->entry ) ) {
			foreach ( $parsed_obj->entry as $video_xml ) {

				$this_item_array = array();

				$high_thumbnail_url = (string) $video_xml->children( 'http://search.yahoo.com/mrss/' )->group->thumbnail->attributes()->url;

				$this_item_array['snippet']    = array(
					'publishedAt'  => (string) $video_xml->published,
					'channelId'    => (string) $video_xml->children( 'http://www.youtube.com/xml/schemas/2015' )->channelId,
					'title'        => (string) $video_xml->title,
					'description'  => (string) $video_xml->children( 'http://search.yahoo.com/mrss/' )->group->description,
					'thumbnails'   => array(
						'default'  => array(
							'url' => (string) str_replace( 'hqdefault.jpg', 'default.jpg', $high_thumbnail_url ),
						),
						'medium'   => array(
							'url' => str_replace( 'hqdefault.jpg', 'mqdefault.jpg', $high_thumbnail_url ),
						),
						'high'     => array(
							'url'    => $high_thumbnail_url,
							'width'  => (string) $video_xml->children( 'http://search.yahoo.com/mrss/' )->group->thumbnail->attributes()->width,
							'height' => (string) $video_xml->children( 'http://search.yahoo.com/mrss/' )->group->thumbnail->attributes()->height,
						),
						'standard' => array(
							'url' => str_replace( 'hqdefault.jpg', 'sddefault.jpg', $high_thumbnail_url ),
						),
						'maxres'   => array(
							'url' => str_replace( 'hqdefault.jpg', 'maxresdefault.jpg', $high_thumbnail_url ),
						),
					),
					'channelTitle' => (string) $video_xml->author->name,
					'resourceId'   => array(
						'videoId' => (string) $video_xml->children( 'http://www.youtube.com/xml/schemas/2015' )->videoId
					),
				);
				$this_item_array['statistics'] = array(
					'viewCount'  => (int) $video_xml->children( 'http://search.yahoo.com/mrss/' )->group->community->statistics->attributes()->views,
					'starRating' => (float) $video_xml->children( 'http://search.yahoo.com/mrss/' )->group->community->starRating->attributes()->average,
				);
				$items_array[]                 = $this_item_array;

			}
		}


		$this->response = $items_array;

	}

	protected function set_url( $endpoint_slug, $params ) {
		$url = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $params['channel_id'];

		$this->set_url_from_args( $url );
	}

}