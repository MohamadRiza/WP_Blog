<?php
/**
 * The Feed Templates Settings Class
 * 
 * It has the default settings for the feed templates for various feed types
 *
 * @since 2.0
 */

namespace SmashBalloon\YouTubeFeed\Builder;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SBY_Feed_Templates_Settings {

	/**
	 * Add feed settings depending on feed templates
	 * 
	 * @since 4.2.0
	 */
	public static function get_feed_settings_by_feed_templates( $settings ) {
        // Check if the feedtype is timelime/posts
		if ( $settings['feedtemplate'] == 'default' ) {
			$settings = self::get_default_feedtemplate_settings( $settings );
		}
        
		if ( $settings['feedtemplate'] == 'carousel' ) {
			$settings = self::get_carousel_feedtemplate_settings( $settings );
		}

        if ( $settings['feedtemplate'] == 'cards' ) {
			$settings = self::get_cards_feedtemplate_settings( $settings );
		}
 
        if ( $settings['feedtemplate'] == 'list' ) {
			$settings = self::get_list_feedtemplate_settings( $settings );
		}
        
        if ( $settings['feedtemplate'] == 'gallery' ) {
			$settings = self::get_gallery_feedtemplate_settings( $settings );
		}

        if ( $settings['feedtemplate'] == 'latest_video' ) {
			$settings = self::get_latest_video_feedtemplate_settings( $settings );
		}
        
        if ( $settings['feedtemplate'] == 'showcase_carousel' ) {
			$settings = self::get_showcase_carousel_feedtemplate_settings( $settings );
		}
        
        if ( $settings['feedtemplate'] == 'widget' ) {
			$settings = self::get_widget_feedtemplate_settings( $settings );
		}
        
		return $settings;
	}
    
    /**
     * Feed settings for default template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_default_feedtemplate_settings( $settings ) {
        $settings['layout'] = 'grid';
        $settings['cols'] = '3';
        $settings['colsmobile'] = '1';
        $settings['num'] = '9';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = false;
        $settings['videocardstyle'] = 'regular';
        
        $settings = self::enable_comments_for_template( $settings, true );
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        return $settings;
    }


    /**
     * Feed settings for carousel template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */    
    public static function get_carousel_feedtemplate_settings( $settings ) {
        $settings = self::get_default_carousel_settings( $settings );

        $settings['cols'] = '3';
        $settings['colsmobile'] = '1';

        $settings['num'] = '9';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = true;
        $settings['headerstyle'] = 'text';
        $settings['customheadertext'] = 'We are on YouTube';
        $settings['customheadersize'] = 'medium';
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        $settings['videocardstyle'] = 'regular';

        return $settings;
    }
    
    /**
     * Feed settings for cards template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_cards_feedtemplate_settings( $settings ) {
        $settings['layout'] = 'grid';
        $settings['cols'] = '3';
        $settings['colsmobile'] = '1';

        $settings['num'] = '9';
        $settings['itemspacing'] = '8';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = true;
        $settings['headerstyle'] = 'text';
        $settings['customheadertext'] = 'We are on YouTube';
        $settings['customheadersize'] = 'medium';

        $settings['videocardstyle'] = 'boxed';
        $settings['boxedbgcolor'] = '#ffffff';
        $settings['boxborderradius'] = '4';
        $settings['enableboxshadow'] = true;

       $settings = self::enable_comments_for_template( $settings, true );
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        return $settings;
    }

    /**
     * Feed settings for lists template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_list_feedtemplate_settings( $settings ) {
        $settings['layout'] = 'list';

        $settings['num'] = '2';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = true;
        $settings['headerstyle'] = 'standard';

        $settings['videocardstyle'] = 'regular';

        $settings = self::enable_comments_for_template( $settings, false );
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        return $settings;
    }

    /**
     * Feed settings for gallery template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_gallery_feedtemplate_settings( $settings ) {
        $settings['layout'] = 'gallery';
        $settings['cols'] = '3';
        $settings['colsmobile'] = '1';
        $settings['num'] = '6';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = false;
        $settings['videocardstyle'] = 'regular';
        
        $settings = self::enable_comments_for_template( $settings, false );
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        return $settings;
    }
    
    /**
     * Feed settings for latest_video template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_latest_video_feedtemplate_settings( $settings ) {
        $settings['layout'] = 'list';

        $settings['num'] = '1';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = true;

        $settings['videocardstyle'] = 'regular';
        
        $settings = self::enable_comments_for_template( $settings, true );
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        return $settings;
    }

    /**
     * Feed settings for showcase carousel template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */    
    public static function get_showcase_carousel_feedtemplate_settings( $settings ) {
        $settings = self::get_default_carousel_settings( $settings );

        $settings['cols'] = '1';
        $settings['colsmobile'] = '1';

        $settings['num'] = '3';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = true;

        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        
        $settings['videocardstyle'] = 'regular';
        return $settings;
    }
    
    /**
     * Feed settings for widget template type
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_widget_feedtemplate_settings( $settings ) {
        $settings['layout'] = 'list';

        $settings['num'] = '2';
        $settings['itemspacing'] = '5';
        
        $settings['colorpalette'] = 'inherit';

        $settings['showheader'] = false;

        $settings['videocardstyle'] = 'regular';
        
        $settings = self::get_default_loadmore_button_settings( $settings );
        $settings = self::get_default_subscribe_button_settings( $settings );
        $settings = self::enable_comments_for_template( $settings, false );

        return $settings;
    }

    /**
     * Default settings for carousel settings
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_default_carousel_settings( $settings ) {
        $settings['layout'] = 'carousel';
        $settings['carouselrows'] = 1;
        $settings['carouselloop'] = 'rewind';
        $settings['carouseltime'] = '3000';
        $settings['carouselarrows'] = true;
        $settings['carouselpag'] = true;
        $settings['carouselautoplay'] = true;
        $settings = self::enable_comments_for_template( $settings, true );
        
        return $settings;
    }

    /**
     * Default settings for load more button
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_default_loadmore_button_settings( $settings ) {
        $settings['showbutton'] = true;
        $settings['buttoncolor'] = '';
        $settings['buttonhovercolor'] = '';
        $settings['buttontextcolor'] = '';
        
        return $settings;
    }

    /**
     * Default settings for subscribe button
     * 
     * @since 2.0
     * 
     * @param array $settings
     * @return array $settings
     */
    public static function get_default_subscribe_button_settings( $settings ) {
        $settings['showsubscribe'] = true;
        $settings['subscribecolor'] = '';
        $settings['subscribehovercolor'] = 'rgb(255, 255, 255, .25)';
        $settings['subscribetextcolor'] = '';
        
        return $settings;
    }

      /**
     * Enables comments for the template settings if the conditions are met.
     * 
     * @since 2.3.3
     * 
     * @param array $settings
     * @param bool $flag
     * @return array $settings
     */
    private static function enable_comments_for_template($settings, $flag) {
        if( sby_is_pro() && !sby_license_notices_active()) {
            $settings['enablecomments'] = $flag;
        }
        return $settings;
}
}
