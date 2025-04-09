<?php

namespace SmashBalloon\YouTubeFeed\Admin;

use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_GDPR_Integrations;

class SBY_Admin extends SBY_Admin_Abstract {

	public function additional_settings_init() {
		$defaults = sby_settings_defaults();

		$args = array(
			'name' => 'num',
			'default' => $defaults['num'],
			'section' => 'sbspf_layout',
			'callback' => 'text',
			'min' => 1,
			'max' => 50,
			'size' => 4,
			'title' => __( 'Number of Videos', 'feeds-for-youtube' ),
			'additional' => '<span class="sby_note">' . __( 'Number of videos to show initially.', 'feeds-for-youtube' ) . '</span>',
			'shortcode' => array(
				'key' => 'num',
				'example' => 5,
				'description' => __( 'The number of videos in the feed', 'feeds-for-youtube' ),
				'display_section' => 'layout'
			)
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => 'px',
				'value' => 'px'
			),
			array(
				'label' => '%',
				'value' => '%'
			)
		);
		$args = array(
			'name' => 'itemspacing',
			'default' => $defaults['itemspacing'],
			'section' => 'sbspf_layout',
			'callback' => 'text',
			'min' => 0,
			'size' => 4,
			'title' => __( 'Spacing between videos', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'itemspacing',
				'example' => '5px',
				'description' => __( 'The spacing/padding around the videos in the feed. Any number with a unit like "px" or "em".', 'feeds-for-youtube' ),
				'display_section' => 'layout'
			),
			'select_name' => 'itemspacingunit',
			'select_options' => $select_options,
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Info Display', 'feeds-for-youtube' ),
			'id' => 'sbspf_info_display',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$select_options = array(
			array(
				'label' => __( 'Below video thumbnail', 'feeds-for-youtube' ),
				'value' => 'below'
			),
			array(
				'label' => __( 'Next to video thumbnail', 'feeds-for-youtube' ),
				'value' => 'side'
			)
		);
		$args = array(
			'name' => 'infoposition',
			'default' => 'below',
			'section' => 'sbspf_info_display',
			'callback' => 'select',
			'title' => __( 'Position', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'infoposition',
				'example' => 'side',
				'description' => __( 'Where the information (title, description, stats) will display. eg.', 'feeds-for-youtube' ) . ' below, side, none',
				'display_section' => 'customize'
			),
			'options' => $select_options,
		);
		$this->add_settings_field( $args );

		$api_key_not_entered = empty( $this->settings['api_key'] ) ? ' sby_api_key_needed' : false;

		$include_options = array(
			array(
				'label' => __( 'Play Icon', 'feeds-for-youtube' ),
				'value' => 'icon',
                'class' => false
			),
			array(
				'label' => __( 'Title', 'feeds-for-youtube' ),
				'value' => 'title',
				'class' => false
			),
			array(
				'label' => __( 'User Name', 'feeds-for-youtube' ),
				'value' => 'user',
				'class' => false
			),
			array(
				'label' => __( 'Views', 'feeds-for-youtube' ),
				'value' => 'views',
				'class' => $api_key_not_entered
			),
			array(
				'label' => __( 'Date', 'feeds-for-youtube' ),
				'value' => 'date',
				'class' => false
			),
			array(
				'label' => __( 'Live Stream Countdown (when applies)', 'feeds-for-youtube' ),
				'value' => 'countdown',
				'class' => false
			),
			array(
				'label' => __( 'Stats (like and comment counts)', 'feeds-for-youtube' ),
				'value' => 'stats',
				'class' => $api_key_not_entered
			),
			array(
				'label' => __( 'Description', 'feeds-for-youtube' ),
				'value' => 'description',
				'class' => false
			),
		);
		$args = array(
			'name' => 'include',
			'default' => $defaults['include'],
			'section' => 'sbspf_info_display',
			'callback' => 'multi_checkbox',
			'title' => __( 'Show/Hide', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'include',
				'example' => '"title, description, date"',
				'description' => __( 'Comma separated list of what video information (title, description, stats) will display in the feed. eg.', 'feeds-for-youtube' ) . ' title, description ',
				'display_section' => 'customize'
			),
			'select_options' => $include_options,
		);
		$this->add_settings_field( $args );

		$include_options = array(
			array(
				'label' => __( 'Title', 'feeds-for-youtube' ),
				'value' => 'title',
				'class' => false
			),
			array(
				'label' => __( 'User Name', 'feeds-for-youtube' ),
				'value' => 'user',
				'class' => false
			),
			array(
				'label' => __( 'Views', 'feeds-for-youtube' ),
				'value' => 'views',
				'class' => $api_key_not_entered
			),
			array(
				'label' => __( 'Date', 'feeds-for-youtube' ),
				'value' => 'date',
				'class' => false
			),
			array(
				'label' => __( 'Live Stream Countdown (when applies)', 'feeds-for-youtube' ),
				'value' => 'countdown',
				'class' => false
			),
			array(
				'label' => __( 'Description', 'feeds-for-youtube' ),
				'value' => 'description',
				'class' => false
			),
			array(
				'label' => __( 'Stats (like and comment counts)', 'feeds-for-youtube' ),
				'value' => 'stats',
				'class' => $api_key_not_entered
			),
		);
		$args = array(
			'name' => 'hoverinclude',
			'default' => $defaults['hoverinclude'],
			'section' => 'sbspf_info_display',
			'callback' => 'multi_checkbox',
			'title' => __( 'Hover Show/Hide', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'hoverinclude',
				'example' => '"title, stats, date"',
				'description' => __( 'Comma separated list of what video information (title, description, stats) will display when hovering over the video thumbnail. eg.', 'feeds-for-youtube' ) . ' title, stats ',
				'display_section' => 'customize'
			),
			'select_options' => $include_options,
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'descriptionlength',
			'default' => $defaults['descriptionlength'],
			'section' => 'sbspf_info_display',
			'callback' => 'text',
			'min' => 5,
			'max' => 1000,
			'size' => 4,
			'title' => __( 'Description Character Length', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'descriptionlength',
				'example' => 300,
				'description' => __( 'Maximum length of the description', 'feeds-for-youtube' ),
				'display_section' => 'customize'
			)
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => __( 'inherit', 'feeds-for-youtube' ),
				'value' => 'inherit'
			),
			array(
				'label' => __( '20px', 'feeds-for-youtube' ),
				'value' => '20px'
			),
			array(
				'label' => __( '18px', 'feeds-for-youtube' ),
				'value' => '18px'
			),
			array(
				'label' => __( '16px', 'feeds-for-youtube' ),
				'value' => '16px'
			),
			array(
				'label' => __( '15px', 'feeds-for-youtube' ),
				'value' => '15px'
			),
			array(
				'label' => __( '14px', 'feeds-for-youtube' ),
				'value' => '14px'
			),
			array(
				'label' => __( '13px', 'feeds-for-youtube' ),
				'value' => '13px'
			),
			array(
				'label' => __( '12px', 'feeds-for-youtube' ),
				'value' => '12px'
			),
		);
		$args = array(
			'name' => 'descriptiontextsize',
			'default' => '13px',
			'section' => 'sbspf_info_display',
			'callback' => 'select',
			'title' => __( 'Description Text Size', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'descriptiontextsize',
				'example' => 'inherit',
				'description' => __( 'Size of description text, size of other text will be relative to this size.', 'feeds-for-youtube' ) . ' 13px, 14px, inherit',
				'display_section' => 'customize'
			),
			'tooltip_info' => __( 'Size of video description text, size of other text in the info display will be relative to this size.', 'feeds-for-youtube' ),
			'options' => $select_options,
		);
		$this->add_settings_field( $args );

		$full_date = SBY_Display_Elements::full_date( strtotime( 'July 25th, 5:30 pm' ), array( 'dateformat' => '0', 'customdate' => '' ), $include_time = true );
		$date_format_options = array(
			array(
				'label' => sprintf( __( 'WordPress Default (%s)', 'feeds-for-youtube' ), $full_date ),
				'value' => '0'
			),
			array(
				'label' => __( 'July 25th, 5:30 pm', 'feeds-for-youtube' ),
				'value' => '1'
			),
			array(
				'label' => __( 'July 25th', 'feeds-for-youtube' ),
				'value' => '2'
			),
            array(
				'label' => __( 'Mon July 25th', 'feeds-for-youtube' ),
				'value' => '3'
			),
            array(
				'label' => __( 'Monday July 25th', 'feeds-for-youtube' ),
				'value' => '4'
			),
			array(
				'label' => __( 'Mon Jul 25th, 2020', 'feeds-for-youtube' ),
				'value' => '5'
			),
			array(
				'label' => __( 'Monday July 25th, 2020 - 5:30 pm', 'feeds-for-youtube' ),
				'value' => '6'
			),
			array(
				'label' => __( '07.25.20', 'feeds-for-youtube' ),
				'value' => '7'
			),
			array(
				'label' => __( '07.25.20 - 17:30', 'feeds-for-youtube' ),
				'value' => '8'
			),
			array(
				'label' => __( '07/25/20', 'feeds-for-youtube' ),
				'value' => '9'
			),
			array(
				'label' => __( '25.07.20', 'feeds-for-youtube' ),
				'value' => '10'
			),
			array(
				'label' => __( '25/07/20', 'feeds-for-youtube' ),
				'value' => '11'
			),
			array(
				'label' => __( '25th July 2020, 17:30', 'feeds-for-youtube' ),
				'value' => '12'
			),
			array(
				'label' => __( 'Custom (Enter Below)', 'feeds-for-youtube' ),
				'value' => 'custom'
			)
        );
		$args = array(
			'name' => 'dateformat',
			'default' => '',
			'section' => 'sbspf_info_display',
			'date_formats' => $date_format_options,
			'callback' => 'date_format',
			'title' => __( 'Date Format', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );
		$this->add_false_field( 'userelative', 'customize' );
		$this->add_false_field( 'disablecdn', 'customize' );

		$args = array(
			'title' => __( 'Info Text/Translations', 'feeds-for-youtube' ),
			'id' => 'sbspf_info_text',
			'tab' => 'customize',
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'viewstext',
			'default' => __( 'views', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Views" Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'viewstext',
				'example' => '"times viewed"',
				'description' => __( 'The text that appears after the number of views.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'agotext',
			'default' => __( 'ago', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Ago" Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'agotext',
				'example' => '"prior"',
				'description' => __( 'The text that appears after relative times in the past.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'beforedatetext',
			'default' => __( 'Streaming live', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( 'Before Date Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'beforedatetext',
				'example' => '"Watch Live"',
				'description' => __( 'The text that appears before live stream dates.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'beforestreamtimetext',
			'default' => __( 'Streaming live in', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( 'Before Live Stream Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'beforestreamtimetext',
				'example' => '"Starting in"',
				'description' => __( 'The text that appears before relative live stream times.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );
		
		$args = array(
			'name' => 'minutetext',
			'default' => __( 'minute', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Minute" text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'minutetext',
				'example' => '"minuto"',
				'description' => __( 'Translation for singular "minute".', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'minutestext',
			'default' => __( 'minute', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Minutes" text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'minutestext',
				'example' => '"minuten"',
				'description' => __( 'Translation for plural "minutes".', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'hourstext',
			'default' => __( 'hours', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Hours" text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'hourstext',
				'example' => '"minuten"',
				'description' => __( 'Translation for "hours".', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'watchnowtext',
			'default' => __( 'Watch Now', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Watch Now" Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'watchnowtext',
				'example' => '"Now Playing"',
				'description' => __( 'The text that appears when video is currently streaming live.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'thousandstext',
			'default' => __( 'K', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Thousands" text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'thousandstext',
				'example' => '" thousand"',
				'description' => __( 'Text after statistics if over 1 thousand.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'millionstext',
			'default' => __( 'M', 'feeds-for-youtube' ),
			'section' => 'sbspf_info_text',
			'callback' => 'text',
			'title' => __( '"Millions" text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'millionstext',
				'example' => '" million"',
				'description' => __( 'Text after statistics if over 1 million.', 'feeds-for-youtube' ),
				'display_section' => 'text'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Header', 'feeds-for-youtube' ),
			'id' => 'sbspf_header',
			'tab' => 'customize',
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'showheader',
			'section' => 'sbspf_header',
			'callback' => 'checkbox',
			'title' => __( 'Show Header', 'feeds-for-youtube' ),
			'default' => true,
			'shortcode' => array(
				'key' => 'showheader',
				'example' => 'false',
				'description' => __( 'Include a header for this feed.', 'feeds-for-youtube' ),
				'display_section' => 'header'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'showdescription',
			'section' => 'sbspf_header',
			'callback' => 'checkbox',
			'title' => __( 'Show Channel Description', 'feeds-for-youtube' ),
			'default' => true,
			'shortcode' => array(
				'key' => 'showdescription',
				'example' => 'false',
				'description' => __( 'Include the channel description in the header.', 'feeds-for-youtube' ),
				'display_section' => 'header'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'showsubscribers',
			'section' => 'sbspf_header',
			'callback' => 'checkbox',
			'title' => __( 'Show Subscribers', 'feeds-for-youtube' ),
			'default' => true,
			'shortcode' => array(
				'key' => 'showsubscribers',
				'example' => 'false',
				'description' => __( 'Include the number of subscribers in the header.', 'feeds-for-youtube' ),
				'display_section' => 'header'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'subscriberstext',
			'default' => __( 'subscribers', 'feeds-for-youtube' ),
			'section' => 'sbspf_header',
			'callback' => 'text',
			'title' => __( '"Subscribers" Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'subscriberstext',
				'example' => '"followers"',
				'description' => __( 'The text that appears after the number of subscribers.', 'feeds-for-youtube' ),
				'display_section' => 'header'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( '"Load More" Button', 'feeds-for-youtube' ),
			'id' => 'sbspf_loadmore',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'showbutton',
			'section' => 'sbspf_loadmore',
			'callback' => 'checkbox',
			'title' => __( 'Show "Load More" Button', 'feeds-for-youtube' ),
			'default' => true,
			'shortcode' => array(
				'key' => 'showbutton',
				'example' => 'false',
				'description' => __( 'Include a "Load More" button at the bottom of the feed to load more videos.', 'feeds-for-youtube' ),
				'display_section' => 'button'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'buttoncolor',
			'default' => '',
			'section' => 'sbspf_loadmore',
			'callback' => 'color',
			'title' => __( 'Button Background Color', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'buttoncolor',
				'example' => '#0f0',
				'description' => __( 'Background color for the "Load More" button. Any hex color code.', 'feeds-for-youtube' ),
				'display_section' => 'button'
			),
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'buttontextcolor',
			'default' => '',
			'section' => 'sbspf_loadmore',
			'callback' => 'color',
			'title' => __( 'Button Text Color', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'buttontextcolor',
				'example' => '#00f',
				'description' => __( 'Text color for the "Load More" button. Any hex color code.', 'feeds-for-youtube' ),
				'display_section' => 'button'
			),
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'buttontext',
			'default' => __( 'Load More...', 'feeds-for-youtube' ),
			'section' => 'sbspf_loadmore',
			'callback' => 'text',
			'title' => __( 'Button Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'buttontext',
				'example' => '"More Videos"',
				'description' => __( 'The text that appers on the "Load More" button.', 'feeds-for-youtube' ),
				'display_section' => 'button'
			)
		);
		$this->add_settings_field( $args );

		/* Subscribe button */
		$args = array(
			'title' => __( '"Subscribe" Button', 'feeds-for-youtube' ),
			'id' => 'sbspf_subscribe',
			'tab' => 'customize',
			'save_after' => true
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'showsubscribe',
			'section' => 'sbspf_subscribe',
			'callback' => 'checkbox',
			'title' => __( 'Show "Subscribe" Button', 'feeds-for-youtube' ),
			'default' => true,
			'shortcode' => array(
				'key' => 'showsubscribe',
				'example' => 'false',
				'description' => __( 'Include a "Subscribe" button at the bottom of the feed to load more videos.', 'feeds-for-youtube' ),
				'display_section' => 'subscribe'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'subscribecolor',
			'default' => '',
			'section' => 'sbspf_subscribe',
			'callback' => 'color',
			'title' => __( 'Subscribe Background Color', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'subscribecolor',
				'example' => '#0f0',
				'description' => __( 'Background color for the "Subscribe" button. Any hex color code.', 'feeds-for-youtube' ),
				'display_section' => 'subscribe'
			),
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'subscribetextcolor',
			'default' => '',
			'section' => 'sbspf_subscribe',
			'callback' => 'color',
			'title' => __( 'Subscribe Text Color', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'subscribetextcolor',
				'example' => '#00f',
				'description' => __( 'Text color for the "Subscribe" button. Any hex color code.', 'feeds-for-youtube' ),
				'display_section' => 'subscribe'
			),
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'subscribetext',
			'default' => __( 'Subscribe', 'feeds-for-youtube' ),
			'section' => 'sbspf_subscribe',
			'callback' => 'text',
			'title' => __( 'Subscribe Text', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'subscribetext',
				'example' => '"Subscribe to My Channel"',
				'description' => __( 'The text that appers on the "Subscribe" button.', 'feeds-for-youtube' ),
				'display_section' => 'subscribe'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Video Experience', 'feeds-for-youtube' ),
			'id' => 'sbspf_experience',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$select_options = array(
			array(
				'label' => '9:16',
				'value' => '9:16'
			),
			array(
				'label' => '3:4',
				'value' => '3:4'
			),
		);
		$args = array(
			'name' => 'playerratio',
			'default' => '9:16',
			'section' => 'sbspf_experience',
			'callback' => 'select',
			'title' => __( 'Player Size Ratio', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'playerratio',
				'example' => '9:16',
				'description' => __( 'Player height relative to width e.g.', 'feeds-for-youtube' ) . ' 9:16, 3:4',
				'display_section' => 'experience'
			),
			'options' => $select_options,
			'tooltip_info' => __( 'A 9:16 ratio does not leave room for video title and playback tools while a 3:4 ratio does.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => __( 'Play when clicked', 'feeds-for-youtube' ),
				'value' => 'onclick'
			),
			array(
				'label' => 'Play automatically (desktop only)',
				'value' => 'automatically'
			)
		);
		$args = array(
			'name' => 'playvideo',
			'default' => 'onclick',
			'section' => 'sbspf_experience',
			'callback' => 'select',
			'title' => __( 'When does video play?', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'playvideo',
				'example' => 'onclick',
				'description' => __( 'What the user needs to do to play a video. eg.', 'feeds-for-youtube' ) . ' onclick, automatically',
				'display_section' => 'experience'
			),
			'options' => $select_options,
			'tooltip_info' => __( 'List layout will not play automatically. Choose whether to play the video automatically in the player or wait until the user clicks the play button after the video is loaded.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$cta_options = array(
			array(
				'label' => __( 'Related Videos', 'feeds-for-youtube' ),
				'slug' => 'related',
				'note' => __( 'Display video thumbnails from the feed that play on your site when clicked.', 'feeds-for-youtube' )
			),
			array(
				'label' => 'Custom Link',
				'slug' => 'link',
				'note' => __( 'Display a button link to a custom URL.', 'feeds-for-youtube' ),
				'options' => array(
					array(
						'name' => 'instructions',
						'callback' => 'instructions',
						'instructions' => __( 'To set a link for each video individually, add the link and button text in the video description on YouTube in this format:', 'feeds-for-youtube' ) . '<br><br><code>{Link: Button Text https://my-site.com/buy-now/my-product/}</code>',
						'label' => __( 'Custom link for each video', 'feeds-for-youtube' ),
					),
					array(
						'name' => 'url',
						'callback' => 'text',
						'label' => __( 'Default Link', 'feeds-for-youtube' ),
						'class' => 'large-text',
						'default' => '',
						'shortcode' => array(
							'example' => 'https://my-site.com/buy-now/my-product/',
							'description' => __( 'URL for viewer to visit for the call to action.', 'feeds-for-youtube' ),
						)
					),
					array(
						'name' => 'opentype',
						'callback' => 'select',
						'options' => array(
							array(
								'label' => __( 'Same window', 'feeds-for-youtube' ),
								'value' => 'same'
							),
							array(
								'label' => __( 'New window', 'feeds-for-youtube' ),
								'value' => 'newwindow'
							)
						),
						'label' => __( 'Link Open Type', 'feeds-for-youtube' ),
						'default' => 'same',
						'shortcode' => array(
							'example' => 'newwindow',
							'description' => __( 'Whether to open the page in a new window or the same window.', 'feeds-for-youtube' ),
						)
					),
					array(
						'name' => 'text',
						'callback' => 'text',
						'label' => __( 'Default Button Text', 'feeds-for-youtube' ),
						'default' => __( 'Learn More', 'feeds-for-youtube' ),
						'shortcode' => array(
							'example' => 'Buy Now',
							'description' => __( 'Text that appears on the call-to-action button.', 'feeds-for-youtube' ),
						)
					),
					array(
						'name' => 'color',
						'default' => '',
						'callback' => 'color',
						'label' => __( 'Button Background Color', 'feeds-for-youtube' ),
						'shortcode' => array(
							'example' => '#0f0',
							'description' => __( 'Button background. Turns opaque on hover.', 'feeds-for-youtube' ),
						)
					),
					array(
						'name' => 'textcolor',
						'default' => '',
						'callback' => 'color',
						'label' => __( 'Button Text Color', 'feeds-for-youtube' ),
						'shortcode' => array(
							'example' => '#0f0',
							'description' => __( 'Color of the text on the call-to-action-button', 'feeds-for-youtube' ),
						)
					)
				)
			),
			array(
				'label' => __( 'YouTube Default', 'feeds-for-youtube' ),
				'slug' => 'default',
				'note' => __( 'YouTube suggested videos from your channel that play on YouTube when clicked.', 'feeds-for-youtube' )
			),
		);

		$args = array(
			'name' => 'cta',
			'default' => 'related',
			'section' => 'sbspf_experience',
			'callback' => 'sub_option',
			'sub_options' => $cta_options,
			'title' => __( 'Call to Action', 'feeds-for-youtube' ),
			'before' => '<p style="margin-bottom: 10px">' . __( 'What the user sees when a video pauses or ends.', 'feeds-for-youtube' ) . '</p>',
			'shortcode' => array(
				'key' => 'cta',
				'example' => 'link',
				'description' => __( 'What the user sees when a video pauses or ends. eg.', 'feeds-for-youtube' ) . ' related, link',
				'display_section' => 'experience'
			),
			'tooltip_info' => __( 'Choose what will happen after a video is paused or completes.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Moderation', 'feeds-for-youtube' ),
			'id' => 'sbspf_moderation',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'includewords',
			'default' => '',
			'section' => 'sbspf_moderation',
			'callback' => 'text',
			'class' => 'large-text',
			'title' => __( 'Show videos containing these words or hashtags', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'includewords',
				'example' => '#filter',
				'description' => __( 'Show videos that have specific text in the title or description.', 'feeds-for-youtube' ),
				'display_section' => 'customize'
			),
			'additional' => __( '"includewords" separate multiple words with commas, include "#" for hashtags', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'excludewords',
			'default' => '',
			'section' => 'sbspf_moderation',
			'callback' => 'text',
			'class' => 'large-text',
			'title' => __( 'Remove videos containing these words or hashtags', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'excludewords',
				'example' => '#filter',
				'description' => __( 'Remove videos that have specific text in the title or description.', 'feeds-for-youtube' ),
				'display_section' => 'customize'
			),
			'additional' => __( '"excludewords" separate multiple words with commas, include "#" for hashtags', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'hidevideos',
			'default' => '',
			'section' => 'sbspf_moderation',
			'callback' => 'textarea',
			'title' => __( 'Hide Specific Videos', 'feeds-for-youtube' ),
			'options' => $select_options,
			'tooltip_info' => __( 'Separate IDs with commas.', 'feeds-for-youtube' ) . '<a class="sbspf_tooltip_link" href="JavaScript:void(0);">'.$this->default_tooltip_text().'</a>
            <p class="sbspf_tooltip sbspf_more_info">' . __( 'These are the specific ID numbers associated with a video or with a post. You can find the ID of a video by viewing the video on YouTube and copy/pasting the ID number from the end of the URL. ex. <code>https://www.youtube.com/watch?v=<span class="sbspf-highlight">Ij1KvL8eN</span></code>', 'feeds-for-youtube' ) . '</p>'
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Custom Code Snippets', 'feeds-for-youtube' ),
			'id' => 'sbspf_custom_snippets',
			'tab' => 'customize'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'custom_css',
			'default' => '',
			'section' => 'sbspf_custom_snippets',
			'callback' => 'textarea',
			'title' => __( 'Custom CSS', 'feeds-for-youtube' ),
			'options' => $select_options,
			'tooltip_info' => __( 'Enter your own custom CSS in the box below', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'custom_js',
			'default' => '',
			'section' => 'sbspf_custom_snippets',
			'callback' => 'textarea',
			'title' => __( 'Custom JavaScript', 'feeds-for-youtube' ),
			'options' => $select_options,
			'tooltip_info' => __( 'Enter your own custom JavaScript/jQuery in the box below', 'feeds-for-youtube' ),
			'note' => __( 'Note: Custom JavaScript reruns every time more videos are loaded into the feed', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'GDPR', 'feeds-for-youtube' ),
			'id' => 'sbspf_gdpr',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$this->add_settings_field( array(
			'name' => 'gdpr',
			'title' => __( 'Enable GDPR Settings', 'feeds-for-youtube' ),
			'callback'  => 'gdpr', // name of the function that outputs the html
			'section' => 'sbspf_gdpr', // matches the section name
		));

		$args = array(
			'title' => __( 'Advanced', 'feeds-for-youtube' ),
			'id' => 'sbspf_advanced',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'preserve_settings',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Preserve settings when plugin is removed', 'feeds-for-youtube' ),
			'default' => false,
			'tooltip_info' => __( 'When removing the plugin your settings are automatically erased. Checking this box will prevent any settings from being deleted. This means that you can uninstall and reinstall the plugin without losing your settings.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => __( 'Background', 'feeds-for-youtube' ),
				'value' => 'background'
			),
			array(
				'label' => __( 'Page', 'feeds-for-youtube' ),
				'value' => 'page'
			),
			array(
				'label' => __( 'None', 'feeds-for-youtube' ),
				'value' => 'none'
			)
		);
		$additional = '<input id="sby-clear-cache" class="button-secondary sbspf-button-action" data-sby-action="sby_delete_wp_posts" data-sby-confirm="'.esc_attr( 'This will permanently delete all YouTube posts from the wp_posts table and the related data in the postmeta table. Existing feeds will only have 15 or fewer videos available initially. Continue?', 'feeds-for-youtube' ).'" style="margin-top: 1px;" type="submit" value="'.esc_attr( 'Clear YouTube Posts', 'feeds-for-youtube' ).'">';
		$args = array(
			'name' => 'storage_process',
			'default' => '',
			'section' => 'sbspf_advanced',
			'callback' => 'select',
			'title' => __( 'Local storage process', 'feeds-for-youtube' ),
			'options' => $select_options,
			'additional' => $additional,
			'tooltip_info' => __( 'To preserve your feeds and videos even if the YouTube API is unavailable, a record of each video is added to the wp_posts table in the WordPress database. Please note that changing this setting to "none" will limit the number of posts available in the feed to 15 or less.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'ajaxtheme',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Are you using an AJAX theme?', 'feeds-for-youtube' ),
			'default' => false,
			'tooltip_info' => __( 'When navigating your site, if your theme uses Ajax to load content into your pages (meaning your page doesn\'t refresh) then check this setting. If you\'re not sure then it\'s best to leave this setting unchecked while checking with your theme author, otherwise checking it may cause a problem.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'ajax_post_load',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Load initial posts with AJAX', 'feeds-for-youtube' ),
			'default' => false,
			'tooltip_info' => __( 'Initial videos will be loaded using AJAX instead of added to the page directly. If you use page caching, this will allow the feed to update according to the "Check for new videos every" setting on the "Configure" tab.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'customtemplates',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Enable Custom Templates', 'feeds-for-youtube' ),
			'default' => false,
			'tooltip_info' => __( 'The default HTML for the feed can be replaced with custom templates added to your theme\'s folder. Enable this setting to use these templates. See <a href="https://smashballoon.com/youtube-custom-templates/" target="_blank">this guide</a>', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'eagerload',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Load Iframes on Page Load', 'feeds-for-youtube' ),
			'default' => false,
			'tooltip_info' => __( 'To optimize the performance of your site and feeds, the plugin loads iframes only after a visitor interacts with the feed. Enabling this setting will cause YouTube player iframes to load when the page loads. Some features may work differently when this is enabled.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'enqueue_js_in_head',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Enqueue JS file in head', 'feeds-for-youtube' ),
			'default' => false,
			'tooltip_info' => __( 'Check this box if you\'d like to enqueue the JavaScript file for the plugin in the head instead of the footer.', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );
	}

	public function instructions( $args ) {
	    ?>
        <div class="sbspf_instructions_wrap">
            <?php echo $args['instructions']?>
        </div>
        <?php
    }

	public function cache( $args ) {
		$social_network = $this->vars->social_network();
		$type_selected = isset( $this->settings['caching_type'] ) ? $this->settings['caching_type'] : 'page';
		$caching_time = isset( $this->settings['caching_time'] ) ? $this->settings['caching_time'] : 1;
		$cache_time_unit_selected = isset( $this->settings['caching_time_unit'] ) ? $this->settings['caching_time_unit'] : 'hours';
		$cache_cron_interval_selected = isset( $this->settings['cache_cron_interval'] ) ? $this->settings['cache_cron_interval'] : '';
		$cache_cron_time = isset( $this->settings['cache_cron_time'] ) ? $this->settings['cache_cron_time'] : '';
		$cache_cron_am_pm = isset( $this->settings['cache_cron_am_pm'] ) ? $this->settings['cache_cron_am_pm'] : '';
		?>
        <div class="sbspf_cache_settings_wrap">
            <div class="sbspf_row">
                <input type="radio" name="<?php echo $this->option_name.'[caching_type]'; ?>" class="sbspf_caching_type_input" id="sbspf_caching_type_page" value="page"<?php if ( $type_selected === 'page' ) echo ' checked'?>>
                <label class="sbspf_radio_label" for="sbspf_caching_type_page"><?php _e ( 'When the page loads', 'feeds-for-youtube' ); ?></label>
                <a class="sbspf_tooltip_link" href="JavaScript:void(0);" style="position: relative; top: 2px;"><?php echo $this->default_tooltip_text(); ?></a>
                <p class="sbspf_tooltip sbspf_more_info"><?php echo sprintf( __( "Your %s data is temporarily cached by the plugin in your WordPress database. There are two ways that you can set the plugin to check for new data:<br><br>
                <b>1. When the page loads</b><br>Selecting this option means that when the cache expires then the plugin will check %s for new posts the next time that the feed is loaded. You can choose how long this data should be cached for. If you set the time to 60 minutes then the plugin will clear the cached data after that length of time, and the next time the page is viewed it will check for new data. <b>Tip:</b> If you're experiencing an issue with the plugin not updating automatically then try enabling the setting labeled <b>'Cron Clear Cache'</b> which is located on the 'Customize' tab.<br><br>
                <b>2. In the background</b><br>Selecting this option means that the plugin will check for new data in the background so that the feed is updated behind the scenes. You can select at what time and how often the plugin should check for new data using the settings below. <b>Please note</b> that the plugin will initially check for data from YouTube when the page first loads, but then after that will check in the background on the schedule selected - unless the cache is cleared.", 'feeds-for-youtube' ), $social_network, $social_network ); ?>
                </p>
            </div>
            <div class="sbspf_row sbspf-caching-page-options" style="display: none;">
				<?php _e ( 'Every', 'feeds-for-youtube' ); ?>:
                <input name="<?php echo $this->option_name.'[caching_time]'; ?>" type="text" value="<?php echo esc_attr( $caching_time ); ?>" size="4">
                <select name="<?php echo $this->option_name.'[caching_time_unit]'; ?>">
                    <option value="minutes"<?php if ( $cache_time_unit_selected === 'minutes' ) echo ' selected'?>><?php _e ( 'Minutes', 'feeds-for-youtube' ); ?></option>
                    <option value="hours"<?php if ( $cache_time_unit_selected === 'hours' ) echo ' selected'?>><?php _e ( 'Hours', 'feeds-for-youtube' ); ?></option>
                    <option value="days"<?php if ( $cache_time_unit_selected === 'days' ) echo ' selected'?>><?php _e ( 'Days', 'feeds-for-youtube' ); ?></option>
                </select>
                <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php _e ( 'What does this mean?', 'feeds-for-youtube' ); ?></a>
                <p class="sbspf_tooltip sbspf_more_info"><?php echo sprintf( __("Your %s posts are temporarily cached by the plugin in your WordPress database. You can choose how long the posts should be cached for. If you set the time to 1 hour then the plugin will clear the cache after that length of time and check %s for posts again.", 'feeds-for-youtube' ), $social_network, $social_network ); ?></p>
            </div>

            <div class="sbspf_row">
                <input type="radio" name="<?php echo $this->option_name.'[caching_type]'; ?>" id="sbspf_caching_type_cron" class="sbspf_caching_type_input" value="background" <?php if ( $type_selected === 'background' ) echo ' checked'?>>
                <label class="sbspf_radio_label" for="sbspf_caching_type_cron"><?php _e ( 'In the background', 'feeds-for-youtube' ); ?></label>
            </div>
            <div class="sbspf_row sbspf-caching-cron-options" style="display: block;">

                <select name="<?php echo $this->option_name.'[cache_cron_interval]'; ?>" id="sbspf_cache_cron_interval">
                    <option value="30mins"<?php if ( $cache_cron_interval_selected === '30mins' ) echo ' selected'?>><?php _e ( 'Every 30 minutes', 'feeds-for-youtube' ); ?></option>
                    <option value="1hour"<?php if ( $cache_cron_interval_selected === '1hour' ) echo ' selected'?>><?php _e ( 'Every hour', 'feeds-for-youtube' ); ?></option>
                    <option value="12hours"<?php if ( $cache_cron_interval_selected === '12hours' ) echo ' selected'?>><?php _e ( 'Every 12 hours', 'feeds-for-youtube' ); ?></option>
                    <option value="24hours"<?php if ( $cache_cron_interval_selected === '24hours' ) echo ' selected'?>><?php _e ( 'Every 24 hours', 'feeds-for-youtube' ); ?></option>
                </select>

                <div id="sbspf-caching-time-settings" style="">
					<?php _e ( 'at', 'feeds-for-youtube' ); ?>
                    <select name="<?php echo $this->option_name.'[cache_cron_time]'; ?>" style="width: 80px">
                        <option value="1"<?php if ( (int)$cache_cron_time === 1 ) echo ' selected'?>>1:00</option>
                        <option value="2"<?php if ( (int)$cache_cron_time === 2 ) echo ' selected'?>>2:00</option>
                        <option value="3"<?php if ( (int)$cache_cron_time === 3 ) echo ' selected'?>>3:00</option>
                        <option value="4"<?php if ( (int)$cache_cron_time === 4 ) echo ' selected'?>>4:00</option>
                        <option value="5"<?php if ( (int)$cache_cron_time === 5 ) echo ' selected'?>>5:00</option>
                        <option value="6"<?php if ( (int)$cache_cron_time === 6 ) echo ' selected'?>>6:00</option>
                        <option value="7"<?php if ( (int)$cache_cron_time === 7 ) echo ' selected'?>>7:00</option>
                        <option value="8"<?php if ( (int)$cache_cron_time === 8 ) echo ' selected'?>>8:00</option>
                        <option value="9"<?php if ( (int)$cache_cron_time === 9 ) echo ' selected'?>>9:00</option>
                        <option value="10"<?php if ( (int)$cache_cron_time === 10 ) echo ' selected'?>>10:00</option>
                        <option value="11"<?php if ( (int)$cache_cron_time === 11 ) echo ' selected'?>>11:00</option>
                        <option value="0"<?php if ( (int)$cache_cron_time === 0 ) echo ' selected'?>>12:00</option>
                    </select>

                    <select name="<?php echo $this->option_name.'[cache_cron_am_pm]'; ?>" style="width: 50px">
                        <option value="am"<?php if ( $cache_cron_am_pm === 'am' ) echo ' selected'?>><?php _e ( 'AM', 'feeds-for-youtube' ); ?></option>
                        <option value="pm"<?php if ( $cache_cron_am_pm === 'pm' ) echo ' selected'?>><?php _e ( 'PM', 'feeds-for-youtube' ); ?></option>
                    </select>
                </div>

				<?php
				if ( wp_next_scheduled( 'sby_feed_update' ) ) {
					$time_format = get_option( 'time_format' );
					if ( ! $time_format ) {
						$time_format = 'g:i a';
					}
					//
					$schedule = wp_get_schedule( 'sby_feed_update' );
					if ( $schedule == '30mins' ) $schedule = __( 'every 30 minutes', 'feeds-for-youtube' );
					if ( $schedule == 'twicedaily' ) $schedule = __( 'every 12 hours', 'feeds-for-youtube' );
					$sbspf_next_cron_event = wp_next_scheduled( 'sby_feed_update' );
					echo '<p class="sbspf-caching-sched-notice"><span><b>' . __( 'Next check', 'feeds-for-youtube' ) . ': ' . date( $time_format, $sbspf_next_cron_event + sby_get_utc_offset() ) . ' (' . $schedule . ')</b> - ' . __( 'Note: Saving the settings on this page will clear the cache and reset this schedule', 'feeds-for-youtube' ) . '</span></p>';
				} else {
					echo '<p style="font-size: 11px; color: #666;">' . __( 'Nothing currently scheduled', 'feeds-for-youtube' ) . '</p>';
				}
				?>
            </div>
        </div>
		<?php
	}

	public function gdpr( $args ) {
		$gdpr = ( isset( $this->settings['gdpr'] ) ) ? $this->settings['gdpr'] : 'auto';
		$select_options = array(
			array(
				'label' => __( 'Automatic', 'feeds-for-youtube' ),
				'value' => 'auto'
			),
			array(
				'label' => __( 'Yes', 'feeds-for-youtube' ),
				'value' => 'yes'
			),
			array(
				'label' => __( 'No', 'feeds-for-youtube' ),
				'value' => 'no'
			)
		)
		?>
		<?php
		$gdpr_list = "<ul class='sby-list'>
                            	<li>" . __('YouTube Player API will not be loaded.', 'feeds-for-youtube') . "</li>
                            	<li>" . __('Thumbnail images for videos will be displayed instead of the actual video.', 'feeds-for-youtube') . "</li>
                            	<li>" . __('To view videos, visitors will click on links to view the video on youtube.com.', 'feeds-for-youtube') . "</li>
                            </ul>";
		?>
        <div>
            <select name="<?php echo $this->option_name.'[gdpr]'; ?>" id="sbspf_gdpr_setting">
				<?php foreach ( $select_options as $select_option ) :
					$selected = $select_option['value'] === $gdpr ? ' selected' : '';
					?>
                    <option value="<?php echo esc_attr( $select_option['value'] ); ?>"<?php echo $selected; ?> ><?php echo esc_html( $select_option['label'] ); ?></option>
				<?php endforeach; ?>
            </select>
            <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $this->default_tooltip_text(); ?></a>
            <div class="sbspf_tooltip sbspf_more_info gdpr_tooltip">

                <p><span><?php _e("Yes", 'feeds-for-youtube' ); ?>:</span> <?php _e("Enabling this setting prevents all videos and external code from loading on your website. To accommodate this, some features of the plugin will be disabled or limited.", 'feeds-for-youtube' ); ?> <a href="JavaScript:void(0);" class="sbspf_show_gdpr_list"><?php _e( 'What will be limited?', 'feeds-for-youtube' ); ?></a></p>

				<?php echo "<div class='sbspf_gdpr_list'>" . $gdpr_list . '</div>'; ?>


                <p><span><?php _e("No", 'feeds-for-youtube' ); ?>:</span> <?php _e("The plugin will still make some requests to display and play videos directly from YouTube.", 'feeds-for-youtube' ); ?></p>


                <p><span><?php _e("Automatic", 'feeds-for-youtube' ); ?>:</span> <?php echo sprintf( __( 'The plugin will only videos if consent has been given by one of these integrated %s', 'feeds-for-youtube' ), '<a href="https://smashballoon.com/doc/gdpr-plugin-list/?youtube" target="_blank" rel="noopener">' . __( 'GDPR cookie plugins', 'feeds-for-youtube' ) . '</a>' ); ?></p>

                <p><?php echo sprintf( __( '%s to learn more about GDPR compliance in the YouTube Feeds plugin.', 'feeds-for-youtube' ), '<a href="https://smashballoon.com/doc/feeds-for-youtube-gdpr-compliance/?youtube" target="_blank" rel="noopener">'. __( 'Click here', 'feeds-for-youtube' ).'</a>' ); ?></p>
            </div>
        </div>

        <div id="sbspf_images_options" class="sbspf_box">
            <div class="sbspf_box_setting">
                    <?php
                    $checked = isset( $this->settings['disablecdn'] ) && $this->settings['disablecdn'] ? ' checked' : false;
                    ?>
                    <input name="<?php echo $this->option_name.'[disablecdn]'; ?>" id="sbspf_disablecdn" class="sbspf_single_checkbox" type="checkbox"<?php echo $checked; ?>>
                    <label for="sbspf_disablecdn"><?php _e("Block CDN Images", 'feeds-for-youtube' ); ?></label>
                    <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $this->default_tooltip_text(); ?></a>
                    <div class="sbspf_tooltip sbspf_more_info">
	                    <?php _e("Images in the feed are loaded from the YouTube CDN. If you want to avoid these images being loaded until consent is given then enabling this setting will show a blank placeholder image instead.", 'feeds-for-youtube' ); ?>
                    </div>
            </div>
        </div>

		<?php if ( ! SBY_GDPR_Integrations::gdpr_tests_successful( isset( $_GET['retest'] ) ) ) :
			$errors = SBY_GDPR_Integrations::gdpr_tests_error_message();
			?>
            <div class="sbspf_box sbspf_gdpr_error">
                <div class="sbspf_box_setting">
                    <p>
                        <strong><?php _e( 'Error:', 'feeds-for-youtube' ); ?></strong> <?php _e("Due to a configuration issue on your web server, the GDPR setting is unable to be enabled. Please see below for more information.", 'feeds-for-youtube' ); ?></p>
                    <p>
						<?php echo $errors; ?>
                    </p>
                </div>
            </div>
		<?php else: ?>

            <div class="sbspf_gdpr_auto">
				<?php if ( SBY_GDPR_Integrations::gdpr_plugins_active() ) :
					$active_plugin = SBY_GDPR_Integrations::gdpr_plugins_active();
					?>
                    <div class="sbspf_gdpr_plugin_active">
                        <div class="sbspf_active">
                            <p>
                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-check-circle fa-w-16 fa-2x"><path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" class=""></path></svg>
                                <b><?php echo sprintf( __( '%s detected', 'feeds-for-youtube' ), $active_plugin ); ?></b>
                                <br />
								<?php _e( 'Some YouTube Feeds features will be limited for visitors to ensure GDPR compliance until they give consent.', 'feeds-for-youtube' ); ?>
                                <a href="JavaScript:void(0);" class="sbspf_show_gdpr_list"><?php _e( 'What will be limited?', 'feeds-for-youtube' ); ?></a>
                            </p>
							<?php echo "<div class='sbspf_gdpr_list'>" . $gdpr_list . '</div>'; ?>
                        </div>

                    </div>
				<?php else: ?>
                    <div class="sbspf_box">
                        <div class="sbspf_box_setting">
                            <p><?php _e( 'No GDPR consent plugin detected. Install a compatible <a href="https://smashballoon.com/doc/gdpr-plugin-list/?youtube" target="_blank">GDPR consent plugin</a>, or manually enable the setting above to display a GDPR compliant version of the feed to all visitors.', 'feeds-for-youtube' ); ?></p>
                        </div>
                    </div>
				<?php endif; ?>
            </div>

            <div class="sbspf_box sbspf_gdpr_yes">
                <div class="sbspf_box_setting">
                    <p><?php _e( "No requests will be made to third-party websites. To accommodate this, some features of the plugin will be limited:", 'feeds-for-youtube' ); ?></p>
					<?php echo $gdpr_list; ?>
                </div>
            </div>

            <div class="sbspf_box sbspf_gdpr_no">
                <div class="sbspf_box_setting">
                    <p><?php _e( "The plugin will function as normal and load images and videos directly from YouTube.", 'feeds-for-youtube' ); ?></p>
                </div>
            </div>

		<?php endif;
	}

	public function search_query_string( $args ){
	    $checked = $this->settings['usecustomsearch'] ? ' checked' : '';
	    $custom_search_string = $this->settings['customsearch'];
	    ?>
        <div class="sbspf_row" style="min-height: 29px;">
            <div class="sbspf_col sbspf_one">&nbsp;
            </div>
            <div class="sbspf_col sbspf_two sbspf_custom_search_wrap">
                <input id="sbspf_usecustomsearch" type="checkbox" name="sby_settings[usecustomsearch]"<?php echo $checked; ?>><label for="sbspf_usecustomsearch">use a custom search</label> <a href="https://smashballoon.com/youtube-feed/custom-search-guide/" target="_blank" rel="noopener">Custom Search Guide</a>
                <div id="sbspf_usecustomsearch_reveal">
                    <label>Custom Search</label><br>
                    <textarea name="sby_settings[customsearch]" id="sbspf_customsearch" type="text" style="width: 100%;"><?php echo esc_attr( $custom_search_string ); ?></textarea>
                </div>
            </div>

        </div>
    <?php
    }

    public function live_options( $args ) {
	    $checked = $this->settings['showpast'] ? ' checked' : '';
	    ?>
        <div class="sbspf_row" style="min-height: 29px;">
            <div class="sbspf_col sbspf_one">&nbsp;
            </div>
            <div class="sbspf_col sbspf_two sbspf_live_options_wrap sbspf_onselect_reveal">
                <input id="sbspf_showpast" type="checkbox" name="sby_settings[showpast]"<?php echo $checked; ?>><label for="sbspf_showpast"><?php _e( 'Show past live streams', 'feeds-for-youtube' ); ?></label>
            </div>

        </div>
        <?php
    }

    public function sub_option( $args ) {
	    $value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : 'related';

	    $cta_options = $args['sub_options'];
	    ?>
	    <?php if ( ! empty( $args['before'] ) ) {
	        echo $args['before'];
        }?>

                <div class="sbspf_sub_options">
			<?php foreach ( $cta_options as $sub_option ) : ?>
                <div class="sbspf_sub_option_cell">
                    <input class="sbspf_sub_option_type" id="sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>" name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" type="radio" value="<?php echo esc_attr( $sub_option['slug'] ); ?>"<?php if ( $sub_option['slug'] === $value ) echo ' checked'?>><label for="sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>"><span class="sbspf_label"><?php echo $sub_option['label']; ?></span></label>
                </div>
			<?php endforeach; ?>

            <div class="sbspf_box_setting">
				<?php if ( isset( $cta_options ) ) : foreach ( $cta_options as $sub_option ) : ?>
                    <div class="sbspf_sub_option_settings sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>">

                        <div class="sbspf_sub_option_setting">
							<?php echo sby_admin_icon( 'info-circle', 'sbspf_small_svg' ); ?>&nbsp;&nbsp;&nbsp;<span class="sbspf_note" style="margin-left: 0;"><?php echo $sub_option['note']; ?></span>
                        </div>
						<?php if ( ! empty( $sub_option['options'] ) ) : ?>
                            <?php foreach ( $sub_option['options'] as $option ) :
                                $option['name'] = $sub_option['slug'].$option['name'];
                                ?>
                                <div class="sbspf_sub_option_setting">
                                    <?php if ( $option['callback'] !== 'checkbox' ) :
                                        if ( isset( $option['shortcode'] ) ) : ?>
                                            <label title="<?php echo __( 'Click for shortcode option', 'feeds-for-youtube' ); ?>"><?php echo $option['label']; ?></label><code class="sbspf_shortcode"> <?php echo $option['name'] . "\n"; ?>
                                                Eg: <?php echo $option['name']; ?>=<?php echo $option['shortcode']['example']; ?></code><br>
                                        <?php else: ?>
                                            <label><?php echo $option['label']; ?></label><br>
                                        <?php endif; ?>
                                    <?php else:
                                        $option['shortcode_example'] = $option['shortcode']['example'];
                                        $option['has_shortcode'] = true;
                                    endif; ?>
                                    <?php call_user_func_array( array( $this, $option['callback'] ), array( $option ) ); ?>

                                </div>

                            <?php endforeach; ?>
						<?php endif; ?>

                    </div>

				<?php endforeach; endif; ?>
            </div>
        </div>
<?php
    }

    public function date_format( $args ) {
	    $checkbox_args = array(
	        'name' => 'userelative',
		    'callback' => 'checkbox',
		    'label' => __( 'Use relative times when less than 2 days', 'feeds-for-youtube' ),
		    'default' => true,
		    //'shortcode_example' => 'false',
		    //'has_shortcode' => '1',
		    'tooltip_info' => __( 'For times that are within 2 days of the video playing time, relative times are displayed rather than the date.  e.g. "5 hours ago"', 'feeds-for-youtube' )
	    );
	    ?>
        <div class="sbspf_sub_option_setting">
        <?php
	    $this->checkbox( $checkbox_args );
        ?>
        </div>
        <div class="sbspf_sub_option_setting sbspf_box_setting">
            <label><?php _e( 'Full Date Format', 'feeds-for-youtube' ); ?></label><code class="sbspf_shortcode" style="display: none; float: none; position: relative; max-width: 300px"> dateformat Eg: dateformat="F j, Y g:i a"</code><br>
	    <?php
	    $args['options'] = $args['date_formats'];
	    $this->select( $args );
	    $value = isset( $this->settings['customdate'] ) ? stripslashes( $this->settings['customdate'] ) : '';
	    ?>

        </div>
        <div class="sbspf_sub_option_setting sby_customdate_wrap">
            <label><?php _e( 'Custom Format', 'feeds-for-youtube' ); ?></label><br>
            <input name="sby_settings[customdate]" id="sby_settings_customdate" type="text" placeholder="F j, Y g:i a" value="<?php echo esc_attr( $value ); ?>"><a href="https://smashballoon.com/youtube-feed/docs/date/" class="sbspf-external-link sbspf_note" target="_blank" rel="noopener"><?php _e( 'Examples', 'feeds-for-youtube' ); ?></a>
        </div>
	    <?php
    }

	public function get_connected_accounts() {
		global $sby_settings;

		if ( isset( $sby_settings['connected_accounts'] ) ) {
			return $sby_settings['connected_accounts'];
		}
		return array();
	}

	public function access_token_listener() {
		if ( isset( $_GET['page'], $_GET['sby_access_token'] ) && 
			( $_GET['page'] === 'youtube-feed-settings' || $_GET['page'] === 'sby-feed-builder' ) 
		) {
			sby_attempt_connection();
		}
	}

	public static function connect_account( $args ) {
		sby_update_or_connect_account( $args );
	}

	public function after_create_menues() {
		remove_menu_page( 'edit.php?post_type=' . SBY_CPT );
	}
}
