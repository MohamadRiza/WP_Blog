<?php

namespace SmashBalloon\YouTubeFeed;

use SmashBalloon\YouTubeFeed\SBY_Parse;
use SmashBalloon\YouTubeFeed\Pro\SBY_Parse_Pro;
use Smashballoon\Customizer\Feed_Builder;

class SBY_Display_Elements
{
	/**
	 * Images are hidden initially with the new/transition classes
	 * except if the js image loading is disabled using the plugin
	 * settings
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_item_classes( $settings ) {
		$classes = array( 'sby_new' );
		if ( empty( $settings['global_settings'] ) ) {
			$settings['global_settings'] = $settings;
		}

		if ( !$settings['global_settings']['disable_js_image_loading'] ) {
			if ( !sby_doing_customizer( $settings ) ) {
				$classes[] = 'sby_transition';
			}
		} else {
			$classes[] = 'sby_no_js';

			$classes[] = 'sby_no_resraise';
			$classes[] = 'sby_js_load_disabled';
		}

		return ' ' . implode( ' ', $classes );
	}

	/**
	 * Returns the list of CSS classes
	 *
	 * @param array $settings
	 * @param array $additional_classes
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_feed_container_css_classes( $settings, $additional_classes ) {
		$customizer = sby_doing_customizer( $settings );
		$classes = '';
		$classes_array = array(
			'sb_youtube',
			'sby_layout_' . esc_attr( $settings['layout'] ),
			'sby_col_' . esc_attr( self::get_cols( $settings ) ),
			'sby_mob_col_' . esc_attr( self::get_cols_mobile( $settings ) ),
			'sby_palette_' . esc_attr( $settings['colorpalette'] ),
		);

		if ( $customizer ) {
			return ' :class="$parent.getFeedContainerClasses()" ';
		} else {
			$classes = ' class="'. implode(" ", $classes_array ) .' ' . $additional_classes .'"';
		}

		return $classes;
	}

	/**
	 * Get feed container main attributes
	 *
	 * @since 2.0
	 */
	public static function get_feed_container_main_attributes( $settings ) {
		$customizer = sby_doing_customizer( $settings );

		$atts = self::print_element_attribute(
			$customizer,
			array(
				'attr'        	=> 'data-card-boxshadow',
				'vue_content' 	=> ' $parent.customizerFeedData.settings.enableboxshadow',
				'php_condition' => $settings['enableboxshadow'] == true,
				'php_content' 	=> $settings['enableboxshadow'],
			)
		);

		$atts .= self::print_element_attribute(
			$customizer,
			array(
				'attr'        	=> 'data-videostyle',
				'vue_content' 	=> ' $parent.customizerFeedData.settings.videocardstyle',
				'php_condition' => $settings['videocardstyle'] === 'boxed',
				'php_content' 	=> $settings['videocardstyle'],
			)
		);

		$atts .= self::print_element_attribute(
			$customizer,
			array(
				'attr'        	=> 'data-videocardlayout',
				'vue_content' 	=> '$parent.customizerFeedData.settings.videocardlayout',
				'php_condition' => isset($settings['videocardlayout']),
				'php_content' 	=> $settings['videocardlayout'],
			)
		);

		$atts .= self::print_element_attribute(
			$customizer,
			array(
				'attr'        	=> 'data-cardboxshadow',
				'vue_content' 	=> '$parent.customizerFeedData.settings.enableboxshadow',
				'php_condition' => isset($settings['enableboxshadow']),
				'php_content' 	=> $settings['enableboxshadow'],
			)
		);

		return $atts;
	}

	/**
	 * Get text header attributes
	 *
	 * @since 2.0
	 */
	public static function get_text_header_attributes( $settings ) {
		$customizer = sby_doing_customizer( $settings );

		$atts = self::print_element_attribute(
			$customizer,
			array(
				'attr'        	=> 'data-header-size',
				'vue_content' 	=> '$parent.customizerFeedData.settings.customheadersize',
				'php_condition' => isset($settings['customheadersize']),
				'php_content' 	=> $settings['customheadersize'],
			)
		);

		return $atts;
	}

	/**
	 * Print Element HTML Attribute
	 *
	 * @param bool $customizer
	 * @param array $args
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function print_element_attribute( $customizer, $args ) {
		if ( $customizer ) {
			return ' :' . $args['attr'] . '="' . $args['vue_content'] . '"';
		}
		if( ( isset( $args['php_condition'] ) && $args['php_condition'] ) || !isset( $args['php_condition'] ) ){
			if ( isset( $args['php_content'] ) && !empty( $args['php_content'] ) ) {
				return ' ' . $args['attr'] . '="' . esc_attr($args['php_content']) . '"';
			}
		}
	}

	public static function get_element_attribute( $element, $settings ) {
		$customizer = sby_doing_customizer( $settings );
		if ( $customizer ) {
			switch ($element) {
				case 'icon':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'icon\'))');
				break;
				case 'title':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'title\'))');
				break;
				case 'user':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'user\'))');
				break;
				case 'views':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'views\'))');
				break;
				case 'date':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'date\'))');
				break;
				case 'countdown':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'countdown\'))');
				break;
				case 'stats':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'stats\'))');
				break;
				case 'description':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'description\'))');
				break;
				case 'duration':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.include.includes(\'duration\'))');
				break;
				case 'hover_title':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'title\'))');
				break;
				case 'hover_user':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'user\'))');
				break;
				case 'hover_countdown':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'countdown\'))');
				break;
				case 'hover_description':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'description\'))');
				break;
				case 'hover_date':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'date\'))');
				break;
				case 'hover_views':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'views\'))');
				break;
				case 'hover_stats':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.hoverinclude.includes(\'stats\'))');
				break;
				case 'show_gallery_player':
					return self::create_condition_show_vue( $customizer,  '$parent.valueIsEnabled($parent.customizerFeedData.settings.layout == \'gallery\')');
				break;
			}
		}
	}

	/**
	 * Should Show Print HTML
	 *
	 * @param bool $customizer
	 * @param string $condition
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function create_condition_show_vue( $customizer, $condition ) {
		if ( $customizer ) {
			return ' v-show="' . $condition . '" ';
		}
		return '';
	}

	public static function full_date( $timestamp, $settings, $include_time = false ) {
		return date_i18n( self::date_format_setting( $settings['dateformat'], $settings['customdate'] ), $timestamp + sby_get_utc_offset() );
	}

	public static function date_format_setting( $date_format, $custom_format ) {
		if ( empty( $date_format ) ) {
			$date_format = get_option( 'date_format' );
			if ( ! $date_format ) {
				$date_format = 'F j, Y';
			}
			$time_format = get_option( 'time_format' );
			if ( ! $time_format ) {
				$date_format .= ' g:i a';
			} else {
				$date_format .= ' ' . $time_format;
			}
			return $date_format;
		} else {
			switch ( $date_format ) {
				case '1':
					return 'F jS, g:i a';
				case '2':
					return 'F jS';
				case '3':
					return 'D F jS';
				case '4':
					return 'l F jS';
				case '5':
					return 'D M jS, Y';
				case '6':
					return 'l F jS, Y - g:i a';
				case '7':
					return 'm.d.y';
				case '8':
					return'm.d.y - G:i';
				case '9':
					return'm/d/y';
					break;
				case '10':
					return 'd.m.y';
				case '11':
					return 'd/m/y';
				case '12':
					return 'l jS F Y, G:i';
				case 'custom':
					if ( empty( $custom_format ) ) {
						return 'F jS, g:i a';
					}
					return $custom_format;
				default:
					// if the value is not one of the number options and not "custom" then it's likely
					// the person set their own custom format in the shortcode
					return $date_format;
			}
		}
	}

	public static function escaped_formatted_count_string( $count, $type, $no_spinner = false ) {
		global $sby_settings;

		$type_text = isset( $sby_settings[ $type . 'text' ] ) ? ' ' . esc_attr($sby_settings[ $type . 'text' ]) : '';

		if ( '' === $count && false === $no_spinner ) { 
			return SBY_Display_Elements::spinner() . $type_text;
		}

		$count = (int)$count;

		if ( $count < 1000 ) {
			$text = esc_html( number_format_i18n( (float)$count ) );
		} elseif ( $count < 1000000 ) {
			$thousands = $count/1000;
			$num_text = round( $thousands, 1 );
			$text = esc_html( $num_text . $sby_settings['thousandstext'] );
		} else {
			$millions = $count/1000000;
			$num_text = round( $millions, 1 );
			$text = esc_html( $num_text . $sby_settings['millionstext'] );
		}


		return $text . $type_text;
	}

	public static function spinner() {
		return '<span class="sby_loader" style="background-color: rgb(255, 255, 255);"></span>';
	}

	/**
	 * Get the item media URL
	 *
	 * @since 2.0
	 */
	public static function get_media_url( $settings, $media_url, $media_full_res ) {
		return $media_full_res;
	}

	public static function escaped_live_streaming_time_string( $post, $misc_data = array() ) {
		$live_broadcast_type = SBY_Parse::get_live_broadcast_content( $post ); // 'none', 'upcoming', 'live', 'completed'
		if ( $live_broadcast_type === 'none' ) {
			return '';
		}

		$scheduled_start_timestamp = SBY_Parse::get_scheduled_start_timestamp( $post, $misc_data );
		$actual_start_timestamp = SBY_Parse::get_actual_start_timestamp( $post, $misc_data );
		$actual_end_timestamp = SBY_Parse_Pro::get_actual_end_timestamp( $post, $misc_data );

		global $sby_settings;

		if ( $actual_end_timestamp !== 0
		     || $actual_start_timestamp !== 0
		     || $live_broadcast_type === 'live'
			 || $live_broadcast_type === 'completed' ) {
			return esc_html( $sby_settings['watchnowtext'] );
		}

		if ( $scheduled_start_timestamp === 0 ) {
			return esc_html( $sby_settings['beforedatetext'] ) . ' ' . '<span class="sby_loader sby_hidden" style="background-color: rgb(255, 255, 255);"></span>';
		}

		$now = time();
		$difference = $scheduled_start_timestamp - $now;

		if ( $difference < 0 ) {
			$text = esc_html( $sby_settings['watchnowtext'] );
		} elseif ( $difference < 2 * HOUR_IN_SECONDS ) {
			$unit_text = $difference < 60 ? $sby_settings['minutetext'] : $sby_settings['minutestext'];
			$num_text = round( $difference / 60, 0 );
			$text = esc_html( $sby_settings['beforestreamtimetext'] ) . ' ' . $num_text . ' ' . esc_html( $unit_text );
		} elseif ( $difference < DAY_IN_SECONDS ) {
			$unit_text = $sby_settings['hourstext'];
			$num_text = round( $difference / HOUR_IN_SECONDS, 0 );
			$text = esc_html( $sby_settings['beforestreamtimetext'] ) . ' ' . $num_text . ' ' . esc_html( $unit_text );
		} else  {
			$text = esc_html( $sby_settings['beforedatetext'] ) . ' ' . esc_html( self::format_date( $scheduled_start_timestamp, $sby_settings, true ) );
		}

		return $text;
	}

	public static function format_date( $timestamp, $settings = false, $include_time = false ) {
		if ( ! $settings ) {
			global $sby_settings;
			$settings = $sby_settings;
		}

		$now = time();
		$difference = $now - $timestamp;
		$do_not_use_relative = ! $settings['userelative'];

		// future date, is a live stream
		if ( $difference < 0 || $do_not_use_relative ) {
			return self::full_date( $timestamp, $settings, $include_time );
		} elseif ( $difference < 2 * HOUR_IN_SECONDS ) {
			$unit_text = $difference < 60 ? $settings['minutetext'] : $settings['minutestext'];
			$num_text = round( $difference / 60, 0 );
			return $num_text . ' ' . esc_html( $unit_text ) . ' ' . esc_html( $settings['agotext'] );
		} elseif ( $difference < DAY_IN_SECONDS ) {
			$unit_text = $settings['hourstext'];
			$num_text = round( $difference / HOUR_IN_SECONDS, 0 );
			return $num_text . ' ' . esc_html( $unit_text ) . ' ' . esc_html( $settings['agotext'] );
		} else  {
			return self::full_date( $timestamp, $settings, $include_time );
		}

	}

	/**
	 * The sby_link element for each item has different styles applied if
	 * the lightbox is disabled.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function get_sby_link_classes( $settings ) {
		if ( ! empty( $settings['disablelightbox'] ) && ($settings['disablelightbox'] === 'on' || $settings['disablelightbox'] === 'true' || $settings['disablelightbox'] === true) ) {
			return ' sby_disable_lightbox';
		}
		return '';
	}

	/**
	 * Get gallery layout player attributes
	 *
	 * @since 2.0
	 */
	public static function get_player_attributes( $settings ) {
		$customizer = sby_doing_customizer( $settings );
		if ( ! $customizer ) {
			return;
		}

		return self::create_condition_show_vue( $customizer,  '$parent.shouldShowPlayer()');
	}


	/**
	 * Custom background color for the hover element. Slightly opaque.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function get_sby_link_styles( $settings ) {
		if ( ! empty( $settings['hovercolor'] ) && $settings['hovercolor'] !== '#000' ) {
			return 'style="background: rgba(' . esc_attr( sby_hextorgb( $settings['hovercolor'] ) ) . ',0.85)"';
		}
		return '';
	}

	/**
	 * Text color for the hover element.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function get_hover_styles( $settings ) {
		if ( ! empty( $settings['hovertextcolor'] ) && $settings['hovertextcolor'] !== '#000' ) {
			return 'style="color: rgba(' . esc_attr( sby_hextorgb( $settings['hovertextcolor'] ) ) . ',1)"';
		}
		return '';
	}

	/**
	 * Inline styles applied to the caption/like count/comment count information appearing
	 * underneath each post by default.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function get_sby_info_styles( $settings ) {
		$styles = '';
		if ( (! empty( $settings['captionsize'] ) && $settings['captionsize'] !== 'inherit') || ! empty( $settings['captioncolor'] ) ) {
			$styles = 'style="';
			if ( ! empty( $settings['captionsize'] ) && $settings['captionsize'] !== 'inherit' ) {
				$styles .= 'font-size: '. esc_attr( $settings['captionsize'] ) . 'px;';
			}
			if ( ! empty( $settings['captioncolor'] ) ) {
				$styles .= 'color: rgb(' . esc_attr( sby_hextorgb( $settings['captioncolor'] ) ). ');';
			}
			$styles .= '"';
		}
		return $styles;
	}

	/**
	 * Color of the likes heart icon and the comment voice box icon in the
	 * sby_info area.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function get_sby_meta_color_styles( $settings ) {
		if ( ! empty( $settings['likescolor'] ) ) {
			return 'style="color: rgb(' . esc_attr( sby_hextorgb( $settings['likescolor'] ) ). ');"';
		}
		return '';
	}

	/**
	 * Size of the likes heart icon and the comment voice box icon in the
	 * sby_info area.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function get_sby_meta_size_color_styles( $settings ) {
		$styles = '';
		if ( (! empty( $settings['likessize'] ) && $settings['likessize'] !== 'inherit') || ! empty( $settings['likescolor'] ) ) {
			$styles = 'style="';
			if ( ! empty( $settings['likessize'] ) && $settings['likessize'] !== 'inherit' ) {
				$styles .= 'font-size: '. esc_attr( $settings['likessize'] ) . 'px;';
			}
			if ( ! empty( $settings['likescolor'] ) ) {
				$styles .= 'color: rgb(' . esc_attr( sby_hextorgb( $settings['likescolor'] ) ). ');';
			}
			$styles .= '"';
		}
		return $styles;
	}

	/**
	 * Display header
	 *
	 * @since 2.0
	 */
	public static function display_header( $header_data, $settings ) {
		if ( sby_doing_customizer( $settings ) ) {
			include sby_get_feed_template_part( 'header', $settings );
			include sby_get_feed_template_part( 'header-text', $settings );
		} else {
			if ( $settings['headerstyle'] == 'standard' ) {
				include sby_get_feed_template_part( 'header', $settings );
			} else if ( $settings['headerstyle'] == 'text' ) {
				include sby_get_feed_template_part( 'header-text', $settings );
			}
		}
	}

	/**
	 * Overwritten in the Pro version.
	 *
	 * @param string $type key of the kind of icon needed
	 * @param string $icon_type svg or font
	 *
	 * @return string the complete html for the icon
	 *
	 * @since 1.0
	 */
	public static function get_icon( $type, $icon_type ) {
		return self::get_basic_icons( $type, $icon_type );
	}

	public static function get_cols( $settings ) {
		if( $settings['layout'] == 'list' ) {
			return 0;
		}
		if ( isset( $settings['cols'] ) ) {
			return $settings['cols'];
		}
		return 0;
	}

	public static function get_cols_mobile( $settings ) {
		if( $settings['layout'] == 'list' ) {
			return 0;
		}
		if ( isset( $settings['colsmobile'] ) ) {
			return $settings['colsmobile'];
		}
		return 0;
	}

	public static function get_style_att( $context, $settings, $pos = false ) {
		$style_settings = array();
		$item_spacing_setting = $settings['itemspacing'];
		if ( ! preg_match("/(px)|(%)/", $item_spacing_setting ) ) {
			$item_spacing_setting = $item_spacing_setting . $settings['itemspacingunit'];
		}
		if ( $context === 'player' ) {
			$style_settings['margin-bottom'] = $item_spacing_setting;
		} elseif ( $context === 'item' ) {
			if ( $settings['layout'] === 'list' ) {
				$style_settings['margin-bottom'] = $item_spacing_setting;
			}
		} elseif ( $context === 'items_wrap' || $context === 'player_outer_wrap' ) {
			if ( $settings['layout'] !== 'list' ) {
				$style_settings['padding'] = $item_spacing_setting;
			}
		} elseif ( $context === 'header' ) {
			if ( $settings['itemspacing'] > 0 ) {
				$style_settings['padding'] = $item_spacing_setting;
			}
			if ( $settings['itemspacing'] < 10 ) {
				$style_settings['margin-bottom'] = '10px';
			}
			$style_settings['padding-bottom'] = '0';
		}

		if ( $context === 'player_outer_wrap' ) {
			$style_settings['padding-bottom'] = 0;
		}

		$style_att = '';
		if ( ! empty( $style_settings ) ) {
			$style_att = ' style="';
			foreach ( $style_settings as $prop => $val ) {
				$style_att .= esc_attr( $prop . ': '. $val . ';' );
			}
			$style_att .= '"';
		}

		return $style_att;
	}

	public static function get_display_avatar( $header_data, $settings ) {
		if ( SBY_GDPR_Integrations::doing_gdpr( $settings ) && $settings['global_settings']['disablecdn'] ) {
			return trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png';
		}
		return SBY_Parse::get_avatar( $header_data, $settings );
	}

	/**
	 * Get Text Header Attributes
	 *
	 * @since 2.0
	 */
	public static function get_header_text_attr( $settings ) {
		if ( sby_doing_customizer( $settings ) ) {
			return ' :data-header-subscriber="$parent.checkShouldShowSubscribers()"';
		}

		// If not doing customizer
		$subscribe_shown = $settings['showsubscribe'] == true ? 'shown' : '';
		return ' data-header-subscriber="'. $subscribe_shown .'"';
	}

	/**
	 * Palette class
	 *
	 * @param array $settings
	 * @param string $context
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_palette_class( $settings, $context = '' ) {
		$customizer = sby_doing_customizer( $settings );
		if ( $customizer ) {
			return ' $parent.getPaletteClass() ';
		} else {
			$feed_id_addition = ! empty( $settings['colorpalette'] ) && $settings['colorpalette'] === 'custom' ? '_' . $settings['feed'] : '';
			$palette_class    = ! empty( $settings['colorpalette'] ) && $settings['colorpalette'] !== 'inherit' ? ' sby' . $context . '_palette_' . $settings['colorpalette'] . $feed_id_addition : '';
			return $palette_class;
		}
	}

	/**
	 * Palette type
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function palette_type( $settings ) {
		return ! empty( $settings['colorpalette'] ) ? $settings['colorpalette'] : 'inherit';
	}
	/**
	 * Returns the best media url for an image based on settings.
	 * By default a white placeholder image is loaded and replaced
	 * with the most optimal image size based on the actual dimensions
	 * of the image element in the feed.
	 *
	 * @param array $post data for an individual post
	 * @param array $settings
	 * @param array $resized_images (optional) not yet used but
	 *  can pass in existing resized images to use in the source
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_optimum_media_url( $post, $settings, $resized_images = array() ) {
		$media_url = '';
		$optimum_res = $settings['imageres'];
		$account_type = isset( $post['images'] ) ? 'personal' : 'business';
		if ( empty( $settings['global_settings'] ) ) {
			$settings['global_settings'] = $settings;
		}

		// only use the placeholder if it will be replaced using JS
		if ( ! $settings['global_settings']['disable_js_image_loading'] || ($settings['global_settings']['disablecdn']  && $settings['global_settings']['gdpr'] === 'yes')) {
			return trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png';
		} else {
			$optimum_res = 'full';
			$settings['imageres'] = 'full';
		}

		$media_url = SBY_Parse::get_media_url( $post, 'lightbox' );

		return $media_url;
	}

	/**
	 * Creates a style attribute that contains all of the styles for
	 * the main feed div.
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function get_feed_style( $settings ) {

		$styles = '';
		$bg_color = str_replace( '#', '', $settings['background'] );

		if ( ! empty( $settings['imagepadding'] )
		     || ! empty( $bg_color )
		     || ! empty( $settings['width'] )
		     || ! empty( $settings['height'] ) ) {
			$styles = ' style="';
			if ( ! empty( $settings['imagepadding'] ) ) {
				$styles .= 'padding-bottom: ' . ((int)$settings['imagepadding'] * 2) . esc_attr( $settings['imagepaddingunit'] ) . ';';
			}
			if ( ! empty( $bg_color ) ) {
				$styles .= 'background-color: rgb(' . esc_attr( sby_hextorgb( $bg_color ) ). ');';
			}
			if ( ! empty( $settings['width'] ) ) {
				$styles .= 'width: ' . (int)$settings['width'] . esc_attr( $settings['widthunit'] ) . ';';
			}
			if ( ! empty( $settings['height'] ) ) {
				$styles .= 'height: ' . (int)$settings['height'] . esc_attr( $settings['heightunit'] ) . ';';
			}
			$styles .= '"';
		}
		return $styles;
	}

	/**
	 * Creates a style attribute for the sby_images div
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_items_wrap_style( $settings ) {
		if ( ! empty ( $settings['imagepadding'] ) ) {
			return 'style="padding: '.(int)$settings['imagepadding'] . esc_attr( $settings['imagepaddingunit'] ) . ';"';
		}
		return '';
	}

	/**
	 * Creates a style attribute for the header. Can be used in
	 * several places based on the header style
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_header_text_color_styles( $settings ) {
		$header_color = str_replace( '#', '', $settings['headercolor'] );

		if ( ! empty( $header_color ) ) {
			return 'style="color: rgb(' . esc_attr( sby_hextorgb( $header_color ) ). ');"';
		}
		return '';
	}

	/**
	 * Header icon and text size is styled using the class added here.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_header_size_class( $settings ) {
		$header_size_class = in_array( strtolower( $settings['headersize'] ), array( 'medium', 'large' ) ) ? ' sby_'.strtolower( $settings['headersize'] ) : '';
		return $header_size_class;
	}

	/**
	 * Creates a style attribute for the subscribe button. Can be in
	 * the feed footer or in a boxed header.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_subscribe_styles( $settings ) {
		$styles = '';
		$subscribe_color = str_replace( '#', '', $settings['subscribecolor'] );
		$subscribe_text_color = str_replace( '#', '', $settings['subscribetextcolor'] );

		if ( ! empty( $subscribe_color ) || ! empty( $subscribe_text_color ) ) {
			$styles = 'style="';
			if ( ! empty( $subscribe_color ) ) {
				$styles .= 'background: rgb(' . esc_attr( sby_hextorgb( $subscribe_color ) ) . ');';
			}
			if ( ! empty( $subscribe_text_color ) ) {
				$styles .= 'color: rgb(' . esc_attr( sby_hextorgb( $subscribe_text_color ) ). ');';
			}
			$styles .= '"';
		}
		return $styles;
	}

	/**
	 * Creates a style attribute for styling the load more button.
	 *
	 * @param $settings
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function get_load_button_styles( $settings ) {
		$styles = '';
		$button_color = str_replace( '#', '', $settings['buttoncolor'] );
		$button_text_color = str_replace( '#', '', $settings['buttontextcolor'] );

		if ( ! empty( $button_color ) || ! empty( $button_text_color ) ) {
			$styles = 'style="';
			if ( ! empty( $button_color ) ) {
				$styles .= 'background: rgb(' . esc_attr( sby_hextorgb( $button_color ) ) . ');';
			}
			if ( ! empty( $button_text_color ) ) {
				$styles .= 'color: rgb(' . esc_attr( sby_hextorgb( $button_text_color ) ). ');';
			}
			$styles .= '"';
		}
		return $styles;
	}

	/**
	 * Get load more button attributes
	 *
	 * @since 2.0
	 */
	public static function get_load_button_attribute( $settings ) {
		return self::should_print_element_vue( sby_doing_customizer( $settings ), '$parent.customizerFeedData.settings.buttontext' );
	}

	/**
	 * Get subscribe button attributes
	 *
	 * @since 2.0
	 */
	public static function get_subscribe_button_attribute( $settings ) {
		return self::should_print_element_vue( sby_doing_customizer( $settings ), '$parent.customizerFeedData.settings.subscribetext' );
	}

	/**
	 * Get subscribe button attributes
	 *
	 * @since 2.0
	 */
	public static function get_text_header_content( $settings ) {
		return self::should_print_element_vue( sby_doing_customizer( $settings ), '$parent.getCustomHeaderText()' );
	}

	/**
	 * Channel subscribers data attributes
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_subscribers_data_attributes( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' ' . self::display_vue_condition( 'showsubscribers' );
	}

	/**
	 * Channel description data attributes
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_description_data_attributes( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' ' . self::display_vue_condition( 'showdescription' );
	}

	/**
	 * Load button data attributes
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_button_data_attributes( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' ' . self::display_vue_condition( 'showbutton' );
	}

	/**
	 * Subscribe link bar data attributes
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.1
	 */
	public static function get_subscribe_bar_link_data_attributes( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' ' . self::display_vue_condition( 'enablesubscriberlink' );
	}

	/**
	 * Subscribe button data attributes
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_subscribe_button_data_attributes( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' ' . self::display_vue_condition( 'showsubscribe' );
	}

	/**
	 * Customizer feed Header display conditions
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_header_display_condition( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' v-show="$parent.shouldShowStandardHeader()"';
	}

	/**
	 * Get subscribe button text
	 * 
	 * @since 2.1
	 * 
	 * @return string
	 */
	public static function get_subscribe_btn_text( $settings ) {
		if ( sby_doing_customizer( $settings ) ) {
			return '';
		}
		return isset( $settings['subscribetext'] ) ? $settings['subscribetext'] : '';
	}

	/**
	 * Customizer feed text header data attributes
	 *
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function get_text_header_display_condition( $settings ) {
		if ( ! sby_doing_customizer( $settings ) ) {
			return '';
		}
		return ' v-show="$parent.shouldShowTextHeader()"';
	}

	/**
	 * Display vue condition
	 *
	 * @param array $setting_name
	 *
	 * @return string
	 *
	 * @since 6.0
	 */
	public static function display_vue_condition( $setting_name, $custom_condition = '' ) {
		return self::create_condition_vue( true, '$parent.valueIsEnabled( $parent.customizerFeedData.settings.' . $setting_name . ' ) '. $custom_condition );
	}

	/**
	 * Should Print HTML
	 *
	 * @param bool $customizer
	 * @param string $condition
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function create_condition_vue( $customizer, $condition ) {
		if ( $customizer ) {
			return ' v-if="' . $condition . '" ';
		}
		return '';
	}

	/**
	 * A not very elegant but useful method to abstract out how the settings
	 * work for displaying certain elements in the feed.
	 *
	 * @param string $element specific key, view below for supported ones
	 * @param $settings
	 *
	 * @return bool
	 *
	 * @since 5.0
	 */
	public static function should_show_element( $element, $context, $settings ) {
		//user, views, date
		if ( $context === 'item-hover' ) {
			$include_array = is_array( $settings['hoverinclude'] ) ? $settings['hoverinclude'] : explode( ',', str_replace( ' ', '', $settings['hoverinclude'] ) );
		} else {
			$include_array = is_array( $settings['include'] ) ? $settings['include'] : explode( ',', str_replace( ' ', '', $settings['include'] ) );
		}

		if ( in_array( $element, $include_array, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the html for an icon based on the kind requested
	 *
	 * @param string $type kind of icon needed (ex "video" is a play button)
	 * @param string $icon_type svg or font
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected static function get_basic_icons( $type, $icon_type ) {
		if ( $type === 'carousel' ) {
			if ( $icon_type === 'svg' ) {
				return '<svg class="svg-inline--fa fa-clone fa-w-16 sby_lightbox_carousel_icon" aria-hidden="true" data-fa-proƒcessed="" data-prefix="far" data-icon="clone" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
	                <path fill="currentColor" d="M464 0H144c-26.51 0-48 21.49-48 48v48H48c-26.51 0-48 21.49-48 48v320c0 26.51 21.49 48 48 48h320c26.51 0 48-21.49 48-48v-48h48c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM362 464H54a6 6 0 0 1-6-6V150a6 6 0 0 1 6-6h42v224c0 26.51 21.49 48 48 48h224v42a6 6 0 0 1-6 6zm96-96H150a6 6 0 0 1-6-6V54a6 6 0 0 1 6-6h308a6 6 0 0 1 6 6v308a6 6 0 0 1-6 6z"></path>
	            </svg>';
			} else {
				return '<i class="fa fa-clone sby_carousel_icon" aria-hidden="true"></i>';
			}

		} elseif ( $type === 'video' ) {
			if ( $icon_type === 'svg' ) {
				return '<svg style="color: rgba(255,255,255,1)" class="svg-inline--fa fa-play fa-w-14 sby_playbtn" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="play" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg>';
			} else {
				return '<i class="fa fa-play sby_playbtn" aria-hidden="true"></i>';
			}
		} elseif ( $type === 'youtube' ) {
			if ( $icon_type === 'svg' ) {
				return '<svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="youtube" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-youtube fa-w-18"><path fill="currentColor" d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" class=""></path></svg>';
			} else {
				return '<i aria-hidden="true" role="img" class="sby_new_logo fab fa-youtube"></i>';
			}
		} elseif ( $type === 'newlogo' ) {
			if ( $icon_type === 'svg' ) {
				return '<svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="youtube" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="sby_new_logo svg-inline--fa fa-youtube fa-w-18"><path fill="currentColor" d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" class=""></path></svg>';
			} else {
				return '<i aria-hidden="true" role="img" class="sby_new_logo fab fa-youtube"></i>';
			}
		} elseif ( $type === 'play') {
			if ( $icon_type === 'svg' ) {
				return '<svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="youtube" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-youtube fa-w-18"><path fill="currentColor" d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" class=""></path></svg>';
			} else {
				return '<i aria-hidden="true" role="img" class="fa fas fa-play"></i>';
			}
		} else {
			return '';
		}
	}

	public static function escaped_data_att_string( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}
		$string = '';
		foreach ( $atts as $key => $value ) {
			$string .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $string;
	}

	/**
	 * Should Print HTML
	 *
	 * @param bool $customizer
	 * @param string $content
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public static function should_print_element_vue( $customizer, $content ) {
		if ( $customizer ) {
			return ' v-html="' . $content . '" ';
		}
		return '';
	}

}