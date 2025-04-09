<?php

namespace SmashBalloon\Reviews\Common;

class DisplayElements {
	public static function get_style_for_setting( $feed_id, $item, $settings ) {
		$return = '#' . sbr_container_id( $feed_id );
		$additional = '';
		switch ( $item ) {
			case '.sb-post-item' :
				$additional = ' ' . self::sb_post_item( $settings );
				break;
			case '.sb-post-item-wrap' :
				$additional = ' ' . self::sb_post_item_wrap( $settings );
				break;
			case '.sb-feed-header-heading' :
				$additional = ' ' . self::sb_feed_header_heading( $settings );
				break;
			case '.sb-feed-header-btn' :
				$additional = ' ' . self::sb_feed_header_btn( $settings );
				break;
			case '.sb-feed-header-btn:hover' :
				$additional = ' ' . self::sb_feed_header_btn_hover( $settings );
				break;
			case '.sb-feed-header-rating' :
				$additional = ' ' . self::sb_feed_header_rating( $settings );
				break;
			case '.sb-feed-header-rating-subtext' :
				$additional = ' ' . self::sb_feed_header_rating_subtext( $settings );
				break;
			case '.sb-feed-header-rating-icons' :
				$additional = ' ' . self::sb_feed_header_rating_icons( $settings );
				break;
			case '.sb-feed-header-bottom' :
				$additional = ' ' . self::sb_feed_header_bottom( $settings );
				break;
			case '.sb-feed-header' :
				$additional = ' ' . self::sb_feed_header( $settings );
				break;
			case '.sb-item-rating' :
				$additional = ' ' . self::sb_item_rating( $settings );
				break;
			case '.sb-item-text' :
				$additional = ' ' . self::sb_item_text( $settings );
				break;
			case '.sb-item-author-name' :
				$additional = ' ' . self::sb_item_author_name( $settings );
				break;
			case '.sb-item-author-date' :
				$additional = ' ' . self::sb_item_author_date( $settings );
				break;
			case '.sb-item-author-img' :
				$additional = ' ' . self::sb_item_author_img( $settings );
				break;
			case '.sb-item-author-ctn' :
				$additional = ' ' . self::sb_item_author_ctn( $settings );
				break;
			case '.sb-load-button' :
				$additional = ' ' . self::sb_load_button( $settings );
				break;
			case '.sb-load-button:hover' :
				$additional = ' ' . self::sb_load_button_hover( $settings );
				break;
		}

		if ( ! empty( trim( $additional )  ) ) {
			return $return . $additional . "\n";
		}

		return '';
	}

	public static function sb_post_item( $settings ) {
		$return = '.sb-post-item {';
		$extra = '';
		$props = array();
		if ( ! empty( $settings['verticalSpacing'] ) ) {
			$props[] = 'margin-bottom: ' . (int) $settings['verticalSpacing'] . 'px;';
		}
		if ( ! empty( $settings['postStyle'] ) && $settings['postStyle']  === 'boxed' ) {

			if ( ! empty(  $settings['boxedBoxShadow'] ) && ! empty(  $settings['boxedBoxShadow']['enabled'] ) ) {
				$props[] = 'box-shadow: ' . (int) $settings['boxedBoxShadow']['x'] . 'px '. (int) $settings['boxedBoxShadow']['y'] . 'px '. (int) $settings['boxedBoxShadow']['blur'] . 'px '. (int) $settings['boxedBoxShadow']['spread'] . 'px ' . self::sanitize_color( $settings['boxedBoxShadow']['color'] ) . ';';
			}
			if ( ! empty(  $settings['boxedBorderRadius'] ) && ! empty(  $settings['boxedBorderRadius']['enabled'] ) ) {
				$props[] = 'border-radius: ' . (int) $settings['boxedBorderRadius']['radius'] . 'px;';
			}
			if ( ! empty(  $settings['boxedBackgroundColor'] )  ) {
				$props[] = 'background: ' . self::sanitize_color( $settings['boxedBackgroundColor'] ) . ';';
			}
			if ( ! empty(  $settings['boxedBackgroundColor'] )  ) {
				$props[] = 'background: ' . self::sanitize_color( $settings['boxedBackgroundColor'] ) . ';';
			}
			if ( ! empty(  $settings['postStroke'] ) && ! empty(  $settings['postStroke']['enabled'] )  ) {
				$extra  .= ' [data-post-style="boxed"] .sb-post-item { border: ' . (int) $settings['postStroke']['thickness'] . 'px solid ' . self::sanitize_color( $settings['postStroke']['color'] ) . '; }' ;
			}
		} else {
			if ( ! empty(  $settings['postStroke'] ) && ! empty(  $settings['postStroke']['enabled'] )  ) {
				$extra  .= ' [data-post-style="regular"] .sb-post-item { border-bottom: ' . (int) $settings['postStroke']['thickness'] . 'px solid ' . self::sanitize_color( $settings['postStroke']['color'] ) . '; }' ;
			}
		}

		if ( ! empty( $settings['postPadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['postPadding'] );
			$props[] = 'padding: ' . $prop_string;
		} elseif ( ! empty( $settings['layout'] ) && $settings['layout'] !== 'list' ) {
			if ( ! empty( $settings['horizontalSpacing'] ) ) {
				$prop_string = floor( $settings['horizontalSpacing'] / 2 ) . 'px';

				$props[] = 'padding: 0 ' . $prop_string . ' 0 ' . $prop_string;
			}
		}


		$return .= implode( '; ', $props ) . '}' . $extra;
		return $return;
	}

	public static function sb_post_item_wrap( $settings ) {
		$return = '.sb-post-item-wrap {';

		$props = array();
		if ( ! empty( $settings['postStyle'] ) && $settings['postStyle']  === 'boxed' ) {
			$props[] = 'padding: 0 5px';
		} else {
			return '';
		}


		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_heading( $settings ) {
		$return = '.sb-feed-header-heading {';
		$props = array();
		if ( ! empty( $settings['headingFont'] ) ) {
			if ( ! empty( $settings['headingFont']['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $settings['headingFont']['weight'];
			}
			if ( ! empty( $settings['headingFont']['size'] ) ) {
				$props[] = 'font-size: ' . (int) $settings['headingFont']['size'] . 'px';
			}
			if ( ! empty( $settings['headingFont']['height'] ) && $settings['headingFont']['height'] !== '100%' ) {
				$props[] = 'height: ' . wp_strip_all_tags( $settings['headingFont']['height'] );
			}
		}
		if ( ! empty( $settings['headingColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['headingColor'] );
		}
		if ( ! empty( $settings['headerHeadingPadding'] ) ) {
			$props[] = 'padding: ' . (int)$settings['headerHeadingPadding'] . 'px';
		}
		if ( ! empty( $settings['headerHeadingMargin']['bottom'] ) ) {
			$props[] = 'margin-bottom: ' . (int) $settings['headerHeadingMargin']['bottom'] . 'px;';
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_btn( $settings ) {
		$return = '.sb-feed-header-btn {';

		$props = array();
		if ( ! empty( $settings['headerButtonFont'] ) ) {
			$this_setting = $settings['headerButtonFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] )  ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
			if ( ! empty( $settings['headerButtonColor'] ) ) {
				$props[] = 'color: ' . self::sanitize_color( $settings['headerButtonColor'] );
			}
			if ( ! empty( $settings['headerButtonBg'] ) ) {
				$props[] = 'background-color: ' . self::sanitize_color( $settings['headerButtonBg'] );
			}
			if ( ! empty( $settings['headerButtonPadding'] ) ) {
				$prop_string = self::padding_and_margin_helper( $settings['headerButtonPadding'] );

				$props[] = 'padding: ' . $prop_string;
			}
			if ( ! empty( $settings['headerButtonMargin'] ) ) {
				$prop_string = self::padding_and_margin_helper( $settings['headerButtonMargin'] );

				$props[] = 'margin: ' . $prop_string;
			}
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_btn_hover( $settings ) {
		$return = '.sb-feed-header-btn:hover {';

		$props = array();
		if ( ! empty( $settings['headerButtonFont'] ) ) {
			if ( ! empty( $settings['headerButtonHoverColor'] ) ) {
				$props[] = 'color: ' . self::sanitize_color( $settings['headerButtonHoverColor'] );
			}
			if ( ! empty( $settings['headerButtonHoverBg'] ) ) {
				$props[] = 'background-color: ' . self::sanitize_color( $settings['headerButtonHoverBg'] );
			}
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_rating( $settings ) {
		$return = '.sb-feed-header-rating {';

		$props = array();
		if ( ! empty( $settings['headerAvReviewColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['headerAvReviewColor'] );
		}
		if ( ! empty( $settings['headerAvReviewFont'] ) ) {
			$this_setting = $settings['headerAvReviewFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] ) ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_rating_subtext( $settings ) {
		$return = '.sb-feed-header-rating-subtext {';

		$props = array();
		if ( ! empty( $settings['headerAvReviewSubtextColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['headerAvReviewSubtextColor'] );
		}
		if ( ! empty( $settings['headerAvSubtextReviewFont'] ) ) {
			$this_setting = $settings['headerAvSubtextReviewFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] ) ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_rating_icons( $settings ) {
		$return = '.sb-feed-header-rating-icons {';

		$props = array();
		if ( ! empty( $settings['headerAvReviewIconColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['headerAvReviewIconColor'] );
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header_bottom( $settings ) {
		$return = '.sb-feed-header-bottom {';

		$props = array();
		if ( ! empty( $settings['headerAvReviewPadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['headerAvReviewPadding'] );

			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['headerAvReviewMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['headerAvReviewMargin'] );

			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_feed_header( $settings ) {
		$return = '.sb-feed-header {';

		$props = array();
		if ( ! empty( $settings['headerPadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['headerPadding'] );

			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['headerMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['headerMargin'] );

			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}
	public static function sb_item_rating( $settings ) {
		$return = '.sb-item-rating {';

		$props = array();
		if ( ! empty( $settings['ratingIconColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['ratingIconColor'] );
		}
		if ( ! empty( $settings['ratingIconPadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['ratingIconPadding'] );
			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['ratingIconMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['ratingIconMargin'] );
			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_item_text( $settings ) {
		$return = '.sb-item-text {';

		$props = array();
		if ( ! empty( $settings['paragraphColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['paragraphColor'] );
		}
		if ( ! empty( $settings['paragraphFont'] ) ) {
			$this_setting = $settings['paragraphFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] ) ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
		}
		if ( ! empty( $settings['paragraphPadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['paragraphPadding'] );
			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['paragraphMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['paragraphMargin'] );
			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_item_author_name( $settings ) {
		$return = '.sb-item-author-name {';

		$props = array();
		if ( ! empty( $settings['authorNameColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['authorNameColor'] );
		}
		if ( ! empty( $settings['authorNameFont'] ) ) {
			$this_setting = $settings['authorNameFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] ) ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
		}
		if ( ! empty( $settings['authorNamePadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['authorNamePadding'] );
			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['authorNameMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['authorNameMargin'] );
			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}
	public static function sb_item_author_date( $settings ) {
		$return = '.sb-item-author-date {';

		$props = array();
		if ( ! empty( $settings['dateColor'] ) ) {
			$props[] = 'color: ' . self::sanitize_color( $settings['dateColor'] );
		}
		if ( ! empty( $settings['dateFont'] ) ) {
			$this_setting = $settings['dateFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] ) ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
		}
		if ( ! empty( $settings['datePadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['datePadding'] );
			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['dateMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['dateMargin'] );
			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_item_author_img( $settings ) {
		$return = '.sb-item-author-img {';

		$props = array();
		if ( ! empty( $settings['authorImageMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['authorImageMargin'] );
			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';

		if ( ! empty( $settings['authorImageBorderRadius'] ) ) {
			$return .= '.sb-item-author-img img {';

			$return .= 'border-radius: ' . (int) $settings['authorImageBorderRadius'] . 'px;';
			$return .= '}';
		}
		return $return;
	}

	public static function sb_item_author_ctn( $settings ) {
		$return = '.sb-item-author-ctn {';

		$props = array();
		if ( ! empty( $settings['authorPadding'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['authorPadding'] );
			$props[] = 'padding: ' . $prop_string;
		}
		if ( ! empty( $settings['authorMargin'] ) ) {
			$prop_string = self::padding_and_margin_helper( $settings['authorMargin'] );
			$props[] = 'margin: ' . $prop_string;
		}
		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_load_button( $settings ) {
		$return = '.sb-load-button {';

		$props = array();
		if ( ! empty( $settings['loadButtonFont'] ) ) {
			$this_setting = $settings['loadButtonFont'];
			if ( ! empty( $this_setting['weight'] ) ) {
				$props[] = 'font-weight: ' . (int) $this_setting['weight'];
			}
			if ( ! empty( $this_setting['size'] ) ) {
				$props[] = 'font-size: ' . (int) $this_setting['size'] . 'px';
			}
			if ( ! empty( $this_setting['height'] ) ) {
				$props[] = 'line-height: ' . wp_strip_all_tags( $this_setting['height'] );
			}
			if ( ! empty( $settings['loadButtonColor'] ) ) {
				$props[] = 'color: ' . self::sanitize_color( $settings['loadButtonColor'] );
			}
			if ( ! empty( $settings['loadButtonBg'] ) ) {
				$props[] = 'background-color: ' . self::sanitize_color( $settings['loadButtonBg'] );
			}
			if ( ! empty( $settings['loadButtonPadding'] ) ) {
				$prop_string = self::padding_and_margin_helper( $settings['loadButtonPadding'] );

				$props[] = 'padding: ' . $prop_string;
			}
			if ( ! empty( $settings['loadButtonMargin'] ) ) {
				$prop_string = self::padding_and_margin_helper( $settings['loadButtonMargin'] );

				$props[] = 'margin: ' . $prop_string;
			}
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function sb_load_button_hover( $settings ) {
		$return = '.sb-load-button:hover {';

		$props = array();
		if ( ! empty( $settings['loadButtonFont'] ) ) {
			if ( ! empty( $settings['loadButtonHoverColor'] ) ) {
				$props[] = 'color: ' . self::sanitize_color( $settings['loadButtonHoverColor'] );
			}
			if ( ! empty( $settings['loadButtonHoverBg'] ) ) {
				$props[] = 'background-color: ' . self::sanitize_color( $settings['loadButtonHoverBg'] );
			}
		}

		$return .= implode( '; ', $props ) . '}';
		return $return;
	}

	public static function padding_and_margin_helper( $setting ) {
		$prop_string = '';

		$keys = array( 'top', 'right', 'bottom', 'left' );
		foreach ( $keys  as $key ) {
			if ( ! empty( $setting[ $key ] ) ) {
				$prop_string .= intval( $setting[ $key ] ) . 'px ';
			} else {
				$prop_string .= '0 ';
			}
		}
		return $prop_string;
	}

	public static function sanitize_color( $color ) {
		if ( empty( $color ) || is_array( $color ) )
			return 'rgba(0,0,0,0)';

		if ( false === strpos( $color, 'rgba' ) ) {
			return sanitize_hex_color( $color );
		}

		$color = str_replace( ' ', '', $color );
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		return 'rgba('.$red.','.$green.','.$blue.','.$alpha.')';
	}

    public static function get_star_icon(){
        return '<svg viewBox="0 0 20 20">
					<path d="M10.0001 16.0074L14.8499 18.9407C15.7381 19.4783 16.8249 18.6836 16.5912 17.6786L15.3057 12.1626L19.5946 8.44634C20.3776 7.76853 19.9569 6.48303 18.9285 6.40122L13.2839 5.92208L11.0752 0.709949C10.6779 -0.236649 9.32225 -0.236649 8.92491 0.709949L6.71618 5.91039L1.07165 6.38954C0.043251 6.47134 -0.377459 7.75685 0.405529 8.43466L4.69444 12.1509L3.40893 17.6669C3.17521 18.6719 4.26204 19.4666 5.15021 18.929L10.0001 16.0074V16.0074Z" ></path>
				</svg>';
    }
}
