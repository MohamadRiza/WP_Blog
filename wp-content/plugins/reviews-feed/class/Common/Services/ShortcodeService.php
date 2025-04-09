<?php

namespace SmashBalloon\Reviews\Common\Services;

use SmashBalloon\Reviews\Common\Feed;
use SmashBalloon\Reviews\Common\FeedCache;
use SmashBalloon\Reviews\Common\FeedDisplay;
use SmashBalloon\Reviews\Common\Parser;
use SmashBalloon\Reviews\Common\SBR_Settings;
use Smashballoon\Stubs\Services\ServiceProvider;

class ShortcodeService extends ServiceProvider {

	public function register() {
		add_shortcode('reviews-feed', array( $this, 'render' ) );
	}

	public function render( $atts = array() ) {
		$feed_id = ! empty( $atts['feed'] ) ? $atts['feed'] : 0;
		$is_single_manual_review = isset( $atts['name'] ) && !empty( $atts['name'] ) ? true : false;

		$settings = SBR_Settings::get_settings_by_feed_id( $feed_id, false, $is_single_manual_review );

		if( $is_single_manual_review ){
			$settings = array_merge(
				$settings,
				$this->get_single_manual_review_content( $atts )
			);
		}

		do_action( 'sbr_before_shortcode_render', $settings );

		$feed = new Feed( $settings, $feed_id, new FeedCache( $feed_id, 2 * DAY_IN_SECONDS ) );

		$feed->init();
		if ( ! empty( $feed->get_errors() ) ) {
			$feed_display = new FeedDisplay( $feed, new Parser() );
			return $feed_display->error_html();
		}
		$feed->get_set_cache();

		$feed_display = new FeedDisplay( $feed, new Parser() );

		return $feed_display->with_wrap();
	}

	/**
	 * @param $settings
	 */
	public function get_single_manual_review_content( $atts ) {
		$settings = [];
		$settings['singleManualReview'] = true;
		$settings['singleManualReviewContent'] = [
			'name' 		=> isset( $atts['name'] ) && !empty( $atts['name'] ) ? $atts['name'] : false,
			'content' 	=> isset( $atts['content'] ) && !empty( $atts['content'] ) ? $atts['content'] : false,
			'rating' 	=> isset( $atts['rating'] ) && !empty( $atts['rating'] ) ? $atts['rating'] : false,
			'avatar' 	=> isset( $atts['avatar'] ) && !empty( $atts['avatar'] ) ? $atts['avatar'] : false,
			'time' 		=> isset( $atts['time'] ) && !empty( $atts['time'] ) ? $atts['time'] : false,
			'provider' 	=> isset( $atts['provider'] ) && !empty( $atts['provider'] ) ? $atts['provider'] : false
		];
		$settings['showHeader'] = false;
		$settings['showLoadButton'] = false;

		//This to remove the multiple columns since we are only showing one Review
		$settings['gridDesktopColumns'] = 1;
		$settings['gridTabletColumns'] = 1;
		$settings['gridMobileColumns'] = 1;
		$settings['masonryDesktopColumns'] = 1;
		$settings['masonryTabletColumns'] = 1;
		$settings['masonryMobileColumns'] = 1;
		$settings['carouselDesktopColumns'] = 1;
		$settings['carouselTabletColumns'] = 1;
		$settings['carouselMobileColumns'] = 1;

		return $settings;
	}

}
