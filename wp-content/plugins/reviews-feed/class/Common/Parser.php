<?php

namespace SmashBalloon\Reviews\Common;

class Parser {

	public function __construct() {

	}

	public function get_id( $post ) {
		if ( ! empty( $post['review_id'] ) ) {
			return (string) $post['review_id'];
		}
		if ( ! empty( $post['id'] ) ) {
			return (string) $post['id'];
		}
		return '';
	}

	public function get_text( $post ) {
		if ( ! empty( $post['text'] ) ) {
			return (string) $post['text'];
		}
		return '';
	}


	public function get_rating( $post ) {
		if (!empty($post['rating'])) {
			if ($post['rating'] === 'positive'){
				return 5;
			} else if ($post['rating'] === 'negative'){
				return 1;
			} else {
				return (int) $post['rating'];
			}
		}
		return 1;
	}

	public function get_time( $post ) {
		if ( ! empty( $post['time'] ) ) {
			return $post['time'];
		}
		return 0;
	}

	public function get_reviewer_name( $post ) {
		if ( ! empty( $post['reviewer']['name'] ) ) {
			return $post['reviewer']['name'];
		}
		return '';
	}



	public function get_provider_name( $post_or_business ) {
		if ( ! empty( $post_or_business['provider']['name'] ) ) {
			return $post_or_business['provider']['name'];
		}
		return '';
	}

	public function get_business_id( $post_or_business ) {
		if ( ! empty( $post_or_business['business']['id'] ) ) {
			return $post_or_business['business']['id'];
		} elseif ( ! empty( $post_or_business['id'] ) ) {
			return $post_or_business['id'];
		}
		return '';
	}

	public function get_business_name( $post_or_business ) {
		if ( ! empty( $post_or_business['business']['name'] ) ) {
			return $post_or_business['business']['name'];
		} elseif ( ! empty( $post_or_business['name'] ) && is_string( $post_or_business['name'] ) ) {
			return $post_or_business['name'];
		}
		return '';
	}

	public function get_average_rating($businesses) {
        if(is_array($businesses)){
            $average_rating = 0;
            $number = 0;
            foreach ($businesses as $business) {
                if ( ! empty( $business['info']['rating'] ) ) {
                    $average_rating += floatval($business['info']['rating']);
                    $number+=1;
                }
            }
            $number = $number === 0 ? 1 : $number;
            return round($average_rating/$number, 1);
        }
		return '';
	}

	public function get_num_ratings( $businesses ) {
        if(is_array($businesses)){
            $total_rating = 0;
            foreach ($businesses as $business) {
                if ( ! empty( $business['info']['total_rating'] ) ) {
                    $total_rating += intval($business['info']['total_rating']);
                }
            }
            return $total_rating;
        }
		return '';
	}

	public function get_max_rating( $business ) {
		if ( ! empty( $business['max'] ) ) {
			return $business['max'];
		}
		return '';
	}

	public function get_rating_type( $business ) {
		if ( ! empty( $business['type'] ) ) {
			return $business['type'];
		}
		return '';
	}

	public function get_business_image( $business ) {
		if ( ! empty( $business['avatar'] ) ) {
			return $business['avatar'];
		}
		return '';
	}

	public function get_review_url( $business, $source ) {

		if ( ! empty( $business['review_url'] ) ) {
			return $business['review_url'];
		}
		if ( ! empty( $business['info']['url'] ) ) {

			if ( strpos( $business['info']['url'], 'https://www.facebook.com' ) === 0) {
				return $this->convert_to_fb_review_url( $business['info']['url'] );
			} elseif ( strpos( $business['info']['url'], 'https://www.yelp.com' ) === 0) {
				return $this->convert_to_yelp_review_url( $business['info']['url'] );
			} else if ( strpos( $business['info']['url'], 'https://www.tripadvisor.com' ) === 0) {
				return $this->convert_to_tripadvisor_review_url( $business['info']['url'] );
			}
			return $business['info']['url'];
		}else{
			if ( $source['provider'] === 'google' ) {
				return $this->convert_to_google_review_url( $source['account_id'] );
			}
		}

		return '';
	}

	public function convert_to_google_review_url( $account_id ) {
		return "https://search.google.com/local/writereview?placeid=" .  $account_id;
	}

	public function convert_to_fb_review_url( $url ) {
		if ( strpos( $url, 'reviews') === false ) {
			return trailingslashit( $url ) . 'reviews';
		}

		return $url;
	}

	public function convert_to_yelp_review_url( $url ) {
		if ( strpos( $url, 'writeareview' ) === false ) {
			return str_replace( 'biz/', 'writeareview/biz/', $url );
		}

		return $url;
	}

	public function convert_to_tripadvisor_review_url( $url ) {
		if ( strpos( $url, 'UserReview') === false ) {
			$url_parts = explode( '/', $url );

			$last_url_part = end( $url_parts );

			$dashes_parts = explode( '-', $last_url_part );

			if ( ! empty( $dashes_parts ) ) {
				return str_replace( $dashes_parts[0], 'UserReviewEdit', $url );
			}
		}

		return $url;
	}

	public function get_location_url( $business ) {
		if ( ! empty( $business['location_url'] ) ) {
			return $business['location_url'];
		}
		return '';
	}
}
