<?php
namespace CustomFacebookFeed;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CFF_Parse{
	public static function get_link( $header_data ) {
		$link = isset( $header_data->link) ? $header_data->link : "https://facebook.com";
		return $link;
	}

	public static function get_cover_source( $header_data ) {
		$url = isset( $header_data->cover->source ) ? $header_data->cover->source : '';
		return $url;
	}

	public static function get_avatar( $header_data ) {
		$avatar = isset( $header_data->picture->data->url ) ? $header_data->picture->data->url : '';
		return $avatar;
	}

	public static function get_name( $header_data ) {
		$name = isset( $header_data->name ) ? $header_data->name : '';
		return $name;
	}

	public static function get_bio( $header_data ) {
		$about = isset( $header_data->about ) ? $header_data->about : '';
		return $about;
	}

	public static function get_likes( $header_data ) {
		$likes = isset( $header_data->fan_count ) ? $header_data->fan_count : '';
		return $likes;
	}

	public static function get_post_id( $post ) {
		if ( isset( $post->id ) ) {
			return $post->id;
		} elseif ( ! is_object( $post ) && isset( $post['id'] ) ) {
			return $post['id'];
		}
		return '';
	}

	public static function get_timestamp( $post ) {
		if ( isset( $post->start_time ) ) {
			return strtotime( $post->start_time );
		} elseif ( ! is_object( $post ) && isset( $post['start_time'] ) ) {
			return strtotime( $post['start_time'] );
		} elseif ( isset( $post->created_time ) ) {
			return strtotime( $post->created_time );
		} elseif ( ! is_object( $post ) && isset( $post['created_time'] ) ) {
			return strtotime( $post['created_time'] );
		}
		return '';
	}
	public static function get_message( $post ) {

		if ( isset( $post->message ) ) {
			return $post->message;
		} elseif ( ! is_object( $post ) && isset( $post['message'] ) ) {
			return $post['message'];
		} elseif ( isset( $post->description ) ) {
			return $post->description;
		} elseif ( ! is_object( $post ) && isset( $post['description'] ) ) {
			return $post['description'];
		}
		return '';
	}

	public static function get_status_type( $post ) {

		if ( isset( $post->status_type ) ) {
			return $post->status_type;
		} elseif ( ! is_object( $post ) && isset( $post['status_type'] ) ) {
			return $post['status_type'];
		} elseif ( isset( $post->start_time )
		           || (! is_object( $post ) && isset( $post['start_time'] )) ) {
			return 'event';
		} elseif ( isset( $post->images )
		           || (! is_object( $post ) && isset( $post['images'] ))) {
			return 'photo';
		} elseif ( isset( $post->format )
		           || (! is_object( $post ) && isset( $post['format'] ))) {
			return 'video';
		} elseif ( isset( $post->cover_photo )
		           || (! is_object( $post ) && isset( $post['cover_photo'] ))) {
			return 'album';
		}
		return '';
	}

	public static function get_event_name( $post ) {

		if ( isset( $post->name ) ) {
			return $post->name;
		} elseif ( ! is_object( $post ) && isset( $post['name'] ) ) {
			return $post['name'];
		}
		return '';
	}



	public static function get_permalink( $post ) {
		if ( isset( $post->start_time ) ) {
			return 'https://www.facebook.com/events/' . $post->id;
		} elseif ( ! is_object( $post ) && isset( $post['start_time'] ) ) {
			return 'https://www.facebook.com/events/' . $post['id'];
		} elseif ( isset( $post->id ) ) {
			return 'https://www.facebook.com/' . $post->id;
		} elseif ( ! is_object( $post ) && isset( $post['id'] ) ) {
			return 'https://www.facebook.com/' . $post['id'];
		} elseif ( isset( $post->link ) ) {
			return $post->link;
		} elseif ( ! is_object( $post ) && isset( $post['link'] ) ) {
			return $post['link'];
		}
		return 'https://www.facebook.com/';
	}

	public static function get_from_link( $post ) {
		if ( isset( $post->from->link ) ) {
			return $post->from->link;
		} elseif ( ! is_object( $post ) && isset( $post['from']['link'] ) ) {
			return $post['from']['link'];
		} elseif ( isset( $post->owner->link ) ) {
			return $post->owner->link;
		} elseif ( ! is_object( $post ) && isset( $post['owner']['link'] ) ) {
			return $post['owner']['link'];
		}
		return 'https://www.facebook.com/';
	}

	public static function get_item_title( $data ) {
		$title = '';
		if ( isset( $data->name ) ) {
			$title = $data->name;
		} elseif ( ! is_object( $data ) && isset( $data['name'] ) ) {
			$title = $data['name'];
		}
		return $title;
	}

	public static function get_attachments( $post ) {

		if ( isset( $post->attachments ) ) {
			return $post->attachments->data;
		} elseif ( ! is_object( $post ) && isset( $post['attachments'] ) ) {
			return $post['attachments']['data'];
		}
		return '';
	}
	public static function get_sub_attachments( $post ) {

		if ( isset( $post->attachments ) && isset( $post->attachments->data[0]->subattachments ) ) {
			return $post->attachments->data[0]->subattachments->data ;
		} elseif ( ! is_object( $post ) && isset( $post['attachments']['data'][0]['subattachments'] ) ) {
			return $post['attachments']['data'][0]['subattachments']['data'];
		} elseif ( isset( $post->subattachments ) ) {
			return $post->subattachments->data ;
		} elseif ( ! is_object( $post ) && isset( $post['subattachments'] ) ) {
			return $post['subattachments']['data'];
		}else {
			return array();
		}
	}

	public static function get_sub_attachment_type( $sub_attachment ) {

		if ( isset( $sub_attachment->type ) ) {
			return $sub_attachment->type;
		} elseif ( ! is_object( $sub_attachment ) && isset( $sub_attachment['type'] ) ) {
			return $sub_attachment['type'];
		}
		return '';
	}


	public static function get_attachment_title( $attachment ) {

		if ( isset( $attachment->title ) ) {
			return $attachment->title;
		} elseif ( ! is_object( $attachment ) && isset( $attachment['title'] ) ) {
			return $attachment['title'];
		}
		return '';
	}

	public static function get_attachment_description( $attachment ) {

		if ( isset( $attachment->description ) ) {
			return $attachment->description;
		} elseif ( ! is_object( $attachment ) && isset( $attachment['description'] ) ) {
			return $attachment['description'];
		}
		return '';
	}

	public static function get_attachment_unshimmed_url( $attachment ) {

		if ( isset( $attachment->unshimmed_url ) ) {
			return $attachment->unshimmed_url;
		} elseif ( ! is_object( $attachment ) && isset( $attachment['unshimmed_url'] ) ) {
			return $attachment['unshimmed_url'];
		}
		return '';
	}

	public static function get_event_start_time( $event ) {
		$time = '';
		$timezone = 'UTC';

		if ( isset( $event->start_time ) ) {
			$time = $event->start_time;
			$timezone = isset( $event->timezone ) ? $event->timezone : 'UTC';
		} elseif ( ! is_object( $event ) &&  isset( $event['start_time'] ) ) {
			$time = $event['start_time'];
			$timezone = isset( $event['timezone'] ) ? $event['timezone'] : 'UTC';
		}

		$timestamp = CFF_Utils::cff_set_timezone( strtotime( $time ), $timezone );

		return $timestamp;
	}

	public static function get_event_end_time( $event ) {
		$time = '';
		$timezone = 'UTC';

		if ( isset( $event->end_time ) ) {
			$time = $event->end_time;
			$timezone = isset( $event->timezone ) ? $event->timezone : 'UTC';
		} elseif ( ! is_object( $event ) &&  isset( $event['end_time'] ) ) {
			$time = $event['end_time'];
			$timezone = isset( $event['timezone'] ) ? $event['timezone'] : 'UTC';
		}

		$timestamp = CFF_Utils::cff_set_timezone( strtotime( $time ), $timezone );

		return $timestamp;
	}

	public static function get_event_location_name( $event ) {
		if ( isset( $event->place->name ) ) {
			return $event->place->name;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['name'] ) ) {
			return $event['place']['name'];
		}
		return '';
	}

	public static function get_event_street( $event ) {
		if ( isset( $event->place->location->street ) ) {
			return $event->place->location->street;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['street'] ) ) {
			return $event['place']['location']['street'];
		}
		return '';
	}

	public static function get_event_state( $event ) {
		if ( isset( $event->place->location->state ) ) {
			return $event->place->location->state;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['state'] ) ) {
			return $event['place']['location']['state'];
		}
		return '';
	}

	public static function get_event_city( $event ) {
		if ( isset( $event->place->location->city ) ) {
			return $event->place->location->city;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['city'] ) ) {
			return $event['place']['location']['city'];
		}
		return '';
	}

	public static function get_event_zip( $event ) {
		if ( isset( $event->place->location->zip ) ) {
			return $event->place->location->zip;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['zip'] ) ) {
			return $event['place']['location']['zip'];
		}
		return '';
	}

	public static function get_event_strings( $event ) {
		if ( is_array( $event ) ) {
			$event = (object) $event;
		}
		return $event;
	}

	public static function get_from_id( $post ) {
		if ( is_object( $post ) && isset( $post->from->id ) ) {
			return $post->from->id;
		} elseif ( ! is_object( $post ) && isset( $post['from']['id'] ) ) {
			return $post['from']['id'];
		}

		return 0;
	}
}