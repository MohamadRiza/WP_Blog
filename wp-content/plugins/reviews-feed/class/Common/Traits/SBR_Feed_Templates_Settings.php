<?php

namespace SmashBalloon\Reviews\Common\Traits;

/**
 * Trait SBR_Feed_Templates_Settings
 *
 * Holds the settings for the feed templates.
 *
 * @since 1.0
 */
trait SBR_Feed_Templates_Settings {

    /**
	 * Get feed settings depending on feed templates.
	 *
	 * @since 1.0
	 */
	public static function get_feed_settings_by_feed_templates( $settings ) {
		if ( empty( $settings['feedTemplate'] ) ) {
			return self::get_default_template_settings( $settings );
		}

        switch ( $settings['feedTemplate'] ) {
            case 'simplecards':
                return self::get_simplecards_template_settings( $settings );
            case 'masonry':
                return self::get_masonry_template_settings( $settings );
            case 'grid':
                return self::get_grid_template_settings( $settings );
            case 'singlereview':
                return self::get_singlereview_template_settings( $settings );
            case 'showcasecarousel':
                return self::get_showcasecarousel_template_settings( $settings );
            case 'carousel':
                return self::get_carousel_template_settings( $settings );
            case 'gridcarousel':
                return self::get_gridcarousel_template_settings( $settings );
            default:
				return self::get_default_template_settings( $settings );
        }
    }

    /**
     * default Template
     *
     * @since 1.0
     */
    public static function get_default_template_settings( $settings ){
        //Header
        $settings['showHeader']    = true;
        $settings['headerContent'] = ['heading', 'button', 'averagereview'];
        $settings['headerMargin'] = ['bottom' => 20];

        //Layout
        $settings['layout'] = 'list';
        $settings['verticalSpacing'] = 20;
        $settings['numPostDesktop'] = 4;
        $settings['numPostTablet'] = 4;
        $settings['numPostMobile'] = 3;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'regular';
        $settings['postStroke'] = [
            'enabled' => true,
            'thickness' => '1',
            'color' => '#eee'
        ];

        //Load More Button
        $settings['showLoadButton'] = true;
        $settings['loadButtonFont'] = [
            'weight' => 600,
            'size' => 16,
            'height' => '1em'
        ];
        $settings['loadButtonColor'] = '#141B38';
        $settings['loadButtonHoverColor'] = '#ffffff';
        $settings['loadButtonBg'] = '#E6E6EB';
        $settings['loadButtonHoverBg'] = '#0096CC';

        //Colors
        $settings['headerButtonBg'] = '#0096CC';
        $settings['headerAvReviewIconColor'] = '#0096CC';
        $settings['ratingIconColor'] = '#0096CC';

        return $settings;
    }

    /**
     * simplecards Template
     *
     * @since 1.0
     */
    public static function get_simplecards_template_settings( $settings ){
        //Header
        $settings['showHeader'] = true;
        $settings['headerContent'] = ['heading', 'button', 'averagereview'];
        $settings['headerMargin'] = ['bottom' => 20];

        //Layout
        $settings['layout'] = 'list';
        $settings['verticalSpacing'] = 20;
        $settings['numPostDesktop'] = 4;
        $settings['numPostTablet'] = 4;
        $settings['numPostMobile'] = 3;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'boxed';
        $settings['postPadding'] = [
            'left' => 15,
            'top' => 15,
            'right' => 15,
            'bottom' => 15
        ];
        $settings['boxedBackgroundColor'] = '#ffffff';
        $settings['boxedBoxShadow'] = [
            'enabled' => true,
            'x' => '0',
            'y' => '1',
            'blur' => '10',
            'spread' => '1',
            'color' => 'rgba(0, 0, 0,0.11)'
        ];
        $settings['boxedBorderRadius'] = [
            'enabled' => true,
            'radius' => '4'
        ];
        //Load More Button
        $settings['showLoadButton'] = true;
        $settings['loadButtonFont'] = [
            'weight' => 600,
            'size' => 16,
            'height' => '1em'
        ];
        $settings['loadButtonColor'] = '#141B38';
        $settings['loadButtonHoverColor'] = '#ffffff';
        $settings['loadButtonBg'] = '#E6E6EB';
        $settings['loadButtonHoverBg'] = '#0096CC';

        //Colors
        $settings['headerButtonBg'] = '#0096CC';
        $settings['headerAvReviewIconColor'] = '#0096CC';
        $settings['ratingIconColor'] = '#0096CC';
        return $settings;
    }

    /**
     * masonry Template
     *
     * @since 1.0
     */
    public static function get_masonry_template_settings( $settings ){
        //Header
        $settings['showHeader'] = true;
        $settings['headerContent'] = ['heading', 'button', 'averagereview'];
        $settings['headerMargin'] = [
            'bottom' => 20
        ];

        //Layout
        $settings['layout'] = 'masonry';
        $settings['verticalSpacing'] = 20;
        $settings['horizontalSpacing'] = 20;
        $settings['numPostDesktop'] = 6;
        $settings['numPostTablet'] = 6;
        $settings['numPostMobile'] = 4;
        $settings['masonryDesktopColumns'] = 3;
        $settings['masonryTabletColumns'] = 2;
        $settings['masonryMobileColumns'] = 1;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'boxed';
        $settings['postPadding'] = [
            'left' => 15,
            'top' => 15,
            'right' => 15,
            'bottom' => 15
        ];
        $settings['boxedBackgroundColor'] = '#ffffff';
        $settings['boxedBoxShadow'] = [
            'enabled' => true,
            'x' => '0',
            'y' => '1',
            'blur' => '10',
            'spread' => '1',
            'color' => 'rgba(0, 0, 0,0.11)'
        ];
        $settings['boxedBorderRadius'] = [
            'enabled' => true,
            'radius' => '4'
        ];
        //Load More Button
        $settings['showLoadButton'] = true;
        $settings['loadButtonFont'] = [
            'weight' => 600,
            'size' => 16,
            'height' => '1em'
        ];
        $settings['loadButtonColor'] = '#141B38';
        $settings['loadButtonHoverColor'] = '#ffffff';
        $settings['loadButtonBg'] = '#E6E6EB';
        $settings['loadButtonHoverBg'] = '#0096CC';

        //Colors
        $settings['headerButtonBg'] = '#0096CC';
        $settings['headerAvReviewIconColor'] = '#0096CC';
        $settings['ratingIconColor'] = '#0096CC';
        return $settings;
    }

    /**
     * grid Template
     *
     * @since 1.0
     */
    public static function get_grid_template_settings( $settings ){
        //Header
        $settings['showHeader'] = true;
        $settings['headerContent'] = ['heading', 'button', 'averagereview'];
        $settings['headerMargin'] = ['bottom' => 20];

        //Layout
        $settings['layout'] = 'grid';
        $settings['verticalSpacing'] = 20;
        $settings['horizontalSpacing'] = 20;
        $settings['numPostDesktop'] = 6;
        $settings['numPostTablet'] = 6;
        $settings['numPostMobile'] = 4;
        $settings['gridDesktopColumns'] = 3;
        $settings['gridTabletColumns'] = 2;
        $settings['gridMobileColumns'] = 1;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'boxed';
        $settings['postPadding'] = [
            'left' => 15,
            'top' => 15,
            'right' => 15,
            'bottom' => 15
        ];
        $settings['boxedBackgroundColor'] = '#ffffff';
        $settings['boxedBoxShadow'] = [
            'enabled' => true,
            'x' => '0',
            'y' => '1',
            'blur' => '10',
            'spread' => '1',
            'color' => 'rgba(0, 0, 0,0.11)'
        ];
        $settings['boxedBorderRadius'] = [
            'enabled' => true,
            'radius' => '4'
        ];
        //Load More Button
        $settings['showLoadButton'] = true;
        $settings['loadButtonFont'] = [
            'weight' => 600,
            'size' => 16,
            'height' => '1em'
        ];
        $settings['loadButtonColor'] = '#141B38';
        $settings['loadButtonHoverColor'] = '#ffffff';
        $settings['loadButtonBg'] = '#E6E6EB';
        $settings['loadButtonHoverBg'] = '#0096CC';

        //Colors
        $settings['headerButtonBg'] = '#0096CC';
        $settings['headerAvReviewIconColor'] = '#0096CC';
        $settings['ratingIconColor'] = '#0096CC';
        return $settings;
    }

    /**
     * singlereview Template
     *
     * @since 1.0
     */
    public static function get_singlereview_template_settings( $settings ){
        //Header
        $settings['showHeader'] = false;

        //Layout
        $settings['layout'] = 'list';
        $settings['numPostDesktop'] = 1;
        $settings['numPostTablet'] = 1;
        $settings['numPostMobile'] = 1;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'regular';
        $settings['postStroke'] = [
            'enabled' => true,
            'thickness' => '1',
            'color' => '#eee'
        ];

        //Load More Button
        $settings['showLoadButton'] = false;

        return $settings;
    }

    /**
     * showcasecarousel Template
     *
     * @since 1.0
     */
    public static function get_showcasecarousel_template_settings( $settings ){
        //Header
        $settings['showHeader'] = false;

        //Layout
        $settings['layout'] = 'carousel';
        $settings['verticalSpacing'] = 0;
        $settings['horizontalSpacing'] =20;
        $settings['numPostDesktop'] = 5;
        $settings['numPostTablet'] = 5;
        $settings['numPostMobile'] = 5;
        $settings['carouselDesktopColumns'] = 1;
        $settings['carouselTabletColumns'] = 1;
        $settings['carouselMobileColumns'] = 1;
        $settings['carouselDesktopRows'] = 1;
        $settings['carouselTabletRows'] = 1;
        $settings['carouselMobileRows'] = 1;
        $settings['carouselLoopType'] = 'infinity';
        $settings['carouselIntervalTime'] = 5000;
        $settings['carouselShowArrows'] = true;
        $settings['carouselShowPagination'] = true;
        $settings['carouselEnableAutoplay'] = true;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'regular';
        $settings['postStroke'] = [
            'enabled' => true,
            'thickness' => '1',
            'color' => '#eee'
        ];

        //Load More Button
        $settings['showLoadButton'] = false;

        return $settings;
    }

    /**
     * carousel Template
     *
     * @since 1.0
     */
    public static function get_carousel_template_settings( $settings ){
        //Header
        $settings['showHeader'] = true;
        $settings['headerContent'] = ['heading', 'button', 'averagereview'];
        $settings['headerMargin'] = ['bottom' => 20];


        //Layout
        $settings['layout'] = 'carousel';
        $settings['verticalSpacing'] = 0;
        $settings['horizontalSpacing'] = 20;
        $settings['numPostDesktop'] = 9;
        $settings['numPostTablet'] = 6;
        $settings['numPostMobile'] = 6;
        $settings['carouselDesktopColumns'] = 3;
        $settings['carouselTabletColumns'] = 2;
        $settings['carouselMobileColumns'] = 1;
        $settings['carouselDesktopRows'] = 1;
        $settings['carouselTabletRows'] = 1;
        $settings['carouselMobileRows'] = 1;
        $settings['carouselLoopType'] = 'infinity';
        $settings['carouselIntervalTime'] = 5000;
        $settings['carouselShowArrows'] = true;
        $settings['carouselShowPagination'] = true;
        $settings['carouselEnableAutoplay'] = true;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'regular';
        $settings['postStroke'] = [
            'enabled' => true,
            'thickness' => '1',
            'color' => '#eee'
        ];

        //Load More Button
        $settings['showLoadButton'] = false;

        return $settings;
    }

    /**
     * gridcarousel Template
     *
     * @since 1.0
     */
    public static function get_gridcarousel_template_settings( $settings ){
        //Header
        $settings['showHeader'] = true;
        $settings['headerContent'] = ['heading', 'button', 'averagereview'];
        $settings['headerMargin'] = ['bottom' => 20];


        //Layout
        $settings['layout'] = 'carousel';
        $settings['verticalSpacing'] = 0;
        $settings['horizontalSpacing'] = 20;
        $settings['numPostDesktop'] = 9;
        $settings['numPostTablet'] = 6;
        $settings['numPostMobile'] = 6;
        $settings['carouselDesktopColumns'] = 3;
        $settings['carouselTabletColumns'] = 2;
        $settings['carouselMobileColumns'] = 1;
        $settings['carouselDesktopRows'] = 2;
        $settings['carouselTabletRows'] = 2;
        $settings['carouselMobileRows'] = 1;
        $settings['carouselLoopType'] = 'infinity';
        $settings['carouselIntervalTime'] = 5000;
        $settings['carouselShowArrows'] = true;
        $settings['carouselShowPagination'] = true;
        $settings['carouselEnableAutoplay'] = true;
        $settings['contentLength'] = 280;

        //Post Style
        $settings['postStyle'] = 'boxed';
        $settings['postPadding'] = [
            'left' => 15,
            'top' => 15,
            'right' => 15,
            'bottom' => 15
        ];
        $settings['boxedBackgroundColor'] = '#ffffff';
        $settings['boxedBoxShadow'] = [
            'enabled' => true,
            'x' => '0',
            'y' => '1',
            'blur' => '10',
            'spread' => '1',
            'color' => 'rgba(0, 0, 0,0.11)'
        ];
        $settings['boxedBorderRadius'] = [
            'enabled' => true,
            'radius' => '4'
        ];

        //Load More Button
        $settings['showLoadButton'] = false;

        return $settings;
    }



}