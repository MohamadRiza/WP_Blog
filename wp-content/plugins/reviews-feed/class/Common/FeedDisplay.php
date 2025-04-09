<?php
/**
 * Class FeedDisplay
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;


class FeedDisplay {
	protected $feed;
	protected $parser;
	protected $translations;

	public function __construct( Feed $feed, Parser $parser ) {
		$this->feed = $feed;
		$this->parser = $parser;
		$settings = sbr_recursive_parse_args(get_option('sbr_settings', []), sbr_plugin_settings_defaults());
		$this->translations = $settings['translations'];
	}

	public function with_wrap() {
		$header_data = ! $this->feed->is_single_manual_review() ? $this->feed->get_header_data() : [];
		$header_data = is_array( $header_data ) ? $header_data : [];
		$posts = $this->feed->get_post_set_page();
		$feed_id = $this->feed->get_feed_id();

		$shortcode_atts = '{}';
		$settings = $this->feed->get_settings();
		$parser = $this->parser;

		wp_enqueue_script( 'sbr_scripts' );

		ob_start();
		echo $this->custom_styles();

		include sbr_get_feed_template_part( 'feed', $settings );
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	public function items_only( $page = 1 ) {
		$posts = $this->feed->get_post_set_page( $page );
		$feed_id = $this->feed->get_feed_id();
		$shortcode_atts = '{}';
		$settings = $this->feed->get_settings();
		$parser = $this->parser;

		ob_start();
		$this->posts_loop( $posts, $settings );
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	public function custom_styles() {
		$settings = $this->feed->get_settings();
		$feed_id = $this->feed->get_feed_id();

		ob_start();
		?>
        <style>
            <?php echo '#' . sbr_container_id($feed_id); ?>{
                --column-gutter : <?php echo isset( $settings['horizontalSpacing'] ) ? $settings['horizontalSpacing'] : 0  ?>px;
            }
            <?php echo $this->feed->get_feed_style(); ?>
        </style>
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}
	public function render_post_elements( $post ) {
		$settings = $this->feed->get_settings();
		$allowed_files = array( 'author', 'text', 'rating' );

		foreach ( $settings['postElements'] as $file_name ) {
			if ( in_array( $file_name, $allowed_files, true ) ) {
				include sbr_get_feed_template_part( 'post-elements/' . $file_name, $settings );
			}
		}
	}

	public function should_show( $element, $item = '' ) {
		$settings = $this->feed->get_settings();
		if ( $element === 'header' ) {
			if ( empty( $item ) ) {
				return ! empty( $settings['showHeader'] );
			}
			if ( in_array( $item, (array) $settings['headerContent'], true ) ) {
				return true;
			}
		} elseif ( $element === 'author' ) {
			if ( in_array( $item, (array) $settings['authorContent'], true ) ) {
				return true;
			}
		}

        return false;
	}

	public function get_header_heading_content() {
		if( $this->feed->is_init_wpml() ){
			return __( 'Reviews', 'reviews-feed' );
		}

		$settings = $this->feed->get_settings();
		if ( ! empty( $settings['headerHeadingContent'] ) ) {
			return $settings['headerHeadingContent'];
		}

		return __( 'Reviews', 'reviews-feed' );
	}

	public function get_header_button_text( ) {
		return isset( $this->translations['writeReview'] ) && !$this->feed->is_init_wpml() ? $this->translations['writeReview'] : __( 'Write a Review', 'reviews-feed' );
	}


	public function get_review_link( $header_data ) {
		$settings = $this->feed->get_settings();
		if ( ! empty( $settings['headerButtonLinkTo'] ) && $settings['headerButtonLinkTo'] === 'external' && ! empty( $settings['headerButtonExternalLink'] ) ) {
			$link = str_replace( array( 'https://', 'http://' ), '', $settings['headerButtonExternalLink'] );
			return 'https://' . $link;
		}
		$header_data = isset( $header_data[0] ) ? $header_data[0] : $header_data;
		$source_data = isset( $settings['sources'][0] ) ? $settings['sources'][0] : [];

		return $this->parser->get_review_url(
			$header_data,
			$source_data
		);
	}

	public function is_truncated_yelp_review( $post ) {
		$text = $this->parser->get_text( $post );

		if ( ! empty( $post['provider']['name'] ) && $post['provider']['name'] === 'yelp' ) {

			if ( substr( $text, -3 ) === '...' ) {
				return true;
			}
		}

		return false;
	}

	public function more_link( $post ) {
		if ( ! $this->is_truncated_yelp_review( $post ) ) {
			return '';
		}

		if ( ! empty( $post['source']['url'] ) ) {
			return $post['source']['url'];
		}

	}

	public function get_review_text( $post ) {
		if ( ! $this->is_truncated_yelp_review( $post ) ) {
			return $this->parser->get_text( $post );
		}
		$text = $this->parser->get_text( $post );

		// remove ellipsis
		return substr( $text, 0, -3 );
	}

	public function posts_loop( $posts, $settings ) {
		if (isset($settings['sortRandomEnabled']) && $settings['sortRandomEnabled'] === true) {
			shuffle($posts);
		}
		foreach ( $posts as $post ) {
			include sbr_get_feed_template_part( 'item', $settings );
		}
	}

	public function star_rating_display( $post, $settings ) {
		$star_rating = intval( $this->parser->get_rating($post) );
		$return = '';
		for ($i = 0; $i < 5; $i++) {
			$iconClass = $star_rating - $i < 1 ? ' sb-item-rating-icon-dimmed' : '';
			if ($star_rating - $i === 0.5) {
				$return .= '<span class="sb-item-rating-icon sb-feed-item-icon-half">
                    <span class="sb-item-rating-icon sb-item-rating-icon-dimmed">' . DisplayElements::get_star_icon() . '</span>
                    <span class="sb-item-rating-icon-halfdimmed">' . DisplayElements::get_star_icon() . '</span>
                </span>';
			} else {
				$return .= '<span class="sb-item-rating-icon ' . $iconClass . '">' . DisplayElements::get_star_icon() . '</span>';
			}

		}

		return $return;
	}

	public function overall_star_rating_display( $business, $settings ) {
		$star_rating = floatval( $this->parser->get_average_rating( $business ) );
		$return = '';
		for ( $i = 0; $i < 5 ; $i++ ) {
			$iconClass = $star_rating - $i < 1 ? ' sb-item-rating-icon-dimmed' : '';
			if($star_rating - $i < 1 && $star_rating - $i >= 0.5){
				$return .= '<span class="sb-feed-item-icon sb-feed-item-icon-half">
                    <span class="sb-feed-header-icon sb-item-rating-icon-dimmed">' . DisplayElements::get_star_icon() . '</span>
                    <span class="sb-item-rating-icon-halfdimmed">' . DisplayElements::get_star_icon() . '</span>
                </span>';
			}else{
				$return .= '<span class="sb-feed-header-icon ' . $iconClass . '">' . DisplayElements::get_star_icon() . '</span>';
			}

		}


		return $return;
	}

	public function provider_icon_url( $post, $settings ) {
		$provider = $this->parser->get_provider_name( $post ) ;
		if ( ! empty( $provider ) ) {
			return trailingslashit( SBR_PLUGIN_URL ) . 'assets/icons/' . $provider . '-provider.svg';
		}
	}

	public function date( $post, $translations ) {

		if( ! isset( $post['time'] ) || $post['time'] === null || !$post['time'] ){
			return '';
		}

		$settings = $this->feed->get_settings();
		$timestamp = $this->parser->get_time( $post );

		$now = time();
		$date_formats = self::get_date_formats();

		if ( intval( $settings['dateFormat'] ) === 1 ) {
			$second 	= $this->feed->is_init_wpml() ? __('second', 'reviews-feed') : $translations['second'];
			$seconds 	= $this->feed->is_init_wpml() ? __('seconds', 'reviews-feed') : $translations['seconds'];
			$minute 	= $this->feed->is_init_wpml() ? __('minute', 'reviews-feed') : $translations['minute'];
			$minutes 	= $this->feed->is_init_wpml() ? __('minutes', 'reviews-feed') : $translations['minutes'];
			$hour 		= $this->feed->is_init_wpml() ? __('hour', 'reviews-feed') : $translations['hour'];
			$hours 		= $this->feed->is_init_wpml() ? __('hours', 'reviews-feed') : $translations['hours'];
			$day 		= $this->feed->is_init_wpml() ? __('day', 'reviews-feed') : $translations['day'];
			$days 		= $this->feed->is_init_wpml() ? __('days', 'reviews-feed') : $translations['days'];
			$week 		= $this->feed->is_init_wpml() ? __('week', 'reviews-feed') : $translations['week'];
			$weeks 		= $this->feed->is_init_wpml() ? __('weeks', 'reviews-feed') : $translations['weeks'];
			$month 		= $this->feed->is_init_wpml() ? __('month', 'reviews-feed') : $translations['month'];
			$months 	= $this->feed->is_init_wpml() ? __('months', 'reviews-feed') : $translations['months'];
			$year 		= $this->feed->is_init_wpml() ? __('years', 'reviews-feed') : $translations['years'];
			$years 		= $this->feed->is_init_wpml() ? __('years', 'reviews-feed') : $translations['years'];
			$ago 		= $this->feed->is_init_wpml() ? __('ago', 'reviews-feed') : $translations['ago'];

			$lengths = array("60", "60", "24", "7", "4.35", "12", "10");
			$periods = array($second, $minute, $hour, $day, $week, $month, $year, "decade");
			$periods_plural = array($seconds, $minutes, $hours, $days, $weeks, $months, $years, "decade");

			// is it future date or past date
			if ($now > $timestamp) {
				$difference = $now - $timestamp;
				$tense = $ago;
			} else {
				$difference = $timestamp - $now;
				$tense = $ago;
			}
			for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
				$difference /= $lengths[$j];
			}

			$difference = round($difference);

			if ($difference != 1) {
				$periods[$j] = $periods_plural[$j];
			}
			$date_str = "$difference $periods[$j] {$tense}";

		} else {
            if( $settings['dateFormat'] !== 'custom'  ){
                $date_str = date_i18n( $date_formats[ intval( $settings['dateFormat']) ], $timestamp );
            }
		}
		$before_padding = '';
		if ( ! empty( $settings['dateBeforeText'] ) ) {
			$before_padding = ' ';
		}
		$after_padding = '';
		if ( ! empty( $settings['dateAfterText'] ) ) {
			$after_padding = ' ';
		}

		if ( $settings['dateFormat'] === 'custom'  && !empty( $settings['dateCustomFormat'] ) ) {
			$custom_date = $settings['dateCustomFormat'];
			$custom_date = str_replace("{hide-start}", "<k>", $custom_date);
			$custom_date = str_replace("{hide-end}", "</k>", $custom_date);
			$date_str = date_i18n($custom_date, $timestamp);
		}

		return $settings['dateBeforeText'] . $before_padding . $date_str . $after_padding . $settings['dateAfterText'];
	}

	public static function get_date_formats(){
		return [
			2 => 'F jS, g:i a',
			3 => 'F jS',
			4=> 'D F jS',
			5 => 'l F jS',
			6 => 'D M jS, Y',
			7=> 'l F jS, Y',
			8=> 'l F jS, Y - g:i a',
			9=> "l M jS, 'y",
			10 => 'm.d.y',
			18 => 'm.d.y - G:i',
			11 => 'm/d/y',
			12 => 'd.m.y',
			19 => 'd.m.y - G:i',
			13 => 'd/m/y',
			14 => 'd-m-Y, G:i',
			15 => 'jS F Y, G:i',
			16 => 'd M Y, G:i',
			17 => 'l jS F Y, G:i',
			18 => 'Y-m-d',
		];
	}

	public function feed_classes() {
		$settings = $this->feed->get_settings();
		$classes = array();
		if ( $settings['layout'] === 'masonry' ) {
			$classes[] = 'sb-cols-' . absint( $settings[ $settings['layout'] . "DesktopColumns"] );
			$classes[] = 'sb-colstablet-' . absint( $settings[ $settings['layout'] . "TabletColumns"] );
			$classes[] = 'sb-colsmobile-' . absint( $settings[ $settings['layout'] . "MobileColumns"] );
		}

		if ($settings['layout'] === 'grid') {
			$classes[] = 'sb-grid-wrapper';
		}

		return implode( ' ', $classes );
	}

	public function item_classes( $post ) {
		$classes = array();

		$id = $this->parser->get_id( $post );
		$classes[] = 'sbr-item-' . $id;

		if ( ! empty( $post['provider']['name'] ) ) {
			$classes[] = 'sbr-provider-' . $post['provider']['name'];
		}

		return implode( ' ', $classes );
	}

	public function should_show_header() {
		$settings = $this->feed->get_settings();

	}

	public function misc_atts() {
		$atts = '';
		$settings = $this->feed->get_settings();
		$misc_atts = array();

		$misc_atts['num'] = array(
			'desktop' => absint( $settings['numPostDesktop'] ),
			'tablet' => absint( $settings['numPostTablet'] ),
			'mobile' => absint( $settings['numPostMobile'] ),
		);

		$misc_atts['flagLastPage'] = $this->feed->is_last_page( 1 );
		$misc_atts['contentLength'] = $settings['contentLength'];

		$atts = ' data-misc="' . esc_attr( wp_json_encode( $misc_atts ) ) . '"';

		if ($settings['layout'] === 'grid') {
			$atts .= ' data-grid-columns="'.esc_attr( $settings['gridDesktopColumns'] ).'" data-grid-tablet-columns="' . esc_attr($settings['gridTabletColumns']) . '" data-grid-mobile-columns="' . esc_attr($settings['gridMobileColumns']) . '" ';
		}

		return $atts;
	}

	public function error_html() {
		if ( ! sbr_current_user_can( 'manage_reviews_feed_options' ) ) {
			return '';
		}
		$errors = $this->feed->get_errors();
		if ( empty( $errors ) ) {
			return '';
		}
		ob_start();
		?>
        <div class="sbr-feed-error">
            <span><?php _e('This error message is only visible to WordPress admins', 'reviews-feed' ); ?></span><br />
			<?php foreach ( $errors as $error ) : ?>
                <p><strong><?php echo wp_kses_post( $error['message'] ); ?></strong>
                <p><?php echo wp_kses_post( $error['directions'] ); ?></p>
			<?php endforeach; ?>
        </div>
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	public function get_header_reviews_number( $number )
	{
		$the_text = isset($this->translations['reviewsHeader']) && !$this->feed->is_init_wpml() ? $this->translations['reviewsHeader'] : __('Over %s Reviews', 'sb-customizer');
		return !empty($number) ? str_replace('%s', $number, $the_text) : '';
	}
}