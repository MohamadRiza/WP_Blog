<?php

namespace SmashBalloon\YouTubeFeed\Services\Integrations;

use SmashBalloon\YouTubeFeed\Builder\SBY_Db;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * SBY_Integration Class
 * Common funcions for Elementor/Divi/Gutenberg
 *
 * @since 2.3
 *
 * @return array
*/

class SBY_Integration {


	/**
	 * Generate link
	 *
	 * @since 2.3
	 *
	 * @return HTML
	 */

    public static function linkGenerator($link, $link_text) {

        ob_start(); ?>
            <a href="<?php echo esc_url( $link ) ?>" class="sby-feed-block-link">
                <?php echo esc_html($link_text); ?>
            </a>
        <?php
        $html = ob_get_contents();
        ob_get_clean();

        return $html;
    }

	/**
	 * Get Widget/Module/Block Info
	 *
	 * @since 2.3
	 *
	 * @return array
	 */
	public static function get_widget_info()
    {
        return [
            'plugin'                            => 'youtube',
            'cta_header'                        => esc_html__('Get started with your first feed from your YouTube Channel', 'feeds-for-youtube'),
            'cta_header2'                       => esc_html__('Select a YouTube feed to embed', 'feeds-for-youtube'),
            'cta_description_free'              => esc_html__('You can display feeds for your YouTube channel, playlist, live streams and more using the ', 'feeds-for-youtube') . self::linkGenerator('https://smashballoon.com/youtube-feed/?utm_campaign=' . sby_utm_campaign() . '&utm_source=elementor&utm_medium=widget&utm_content=proversion', 'Pro version'),
            'cta_description_pro'               => esc_html__('You can also add Instagram, Facebook, and Twitter posts into your feed using our ', 'feeds-for-youtube') . self::linkGenerator('https://smashballoon.com/social-wall/?utm_campaign=' . sby_utm_campaign() . '&utm_source=elementor&utm_medium=widget&utm_content=socialwall', 'Social Wall plugin'),
            'plugins'                           => sby_get_installed_plugin_info()
        ];
    }

    /**
	 * Widget CTA
	 *
	 * @since 2.3
	 *
	 * @return HTML
	*/
    public static function get_widget_cta( $type = 'dropdown' )
    {
        $widget_cta_html = '';
        $feeds_list = SBY_Db::elementor_feeds_query();
        ob_start();
        self::get_widget_cta_html( $feeds_list, $type );
        $widget_cta_html .= ob_get_contents();
        ob_get_clean();
        return $widget_cta_html;
    }

    public static function get_widget_cta_html( $feeds_list, $type )
    {
        $info = self::get_widget_info();

        $feeds_exist = is_array( $feeds_list ) && sizeof( $feeds_list ) > 0;
        ?>
        <div class="sby-feed-block-cta">
            <div class="sby-feed-block-cta-img-ctn">
                <div class="sby-feed-block-cta-img">
                    <span><?php echo $info['plugins'][$info['plugin']]['icon']; ?></span>
                    <svg class="sby-feed-block-cta-logo" width="31" height="39" viewBox="0 0 31 39" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.62525 18.4447C1.62525 26.7883 6.60827 33.9305 13.3915 35.171L12.9954 36.4252L12.5937 37.6973L13.923 37.5843L18.4105 37.2026L20.0997 37.0589L19.0261 35.7468L18.4015 34.9834C24.7525 33.3286 29.3269 26.4321 29.3269 18.4447C29.3269 9.29016 23.2952 1.53113 15.4774 1.53113C7.65975 1.53113 1.62525 9.2899 1.62525 18.4447Z" fill="#FE544F" stroke="white" stroke-width="1.78661"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.5669 8.05676L19.1904 14.4905L25.6512 14.6761L20.9776 19.0216L24.6689 22.3606L18.4503 23.1916L16.5651 29.4104L13.7026 23.8415L7.92284 26.4899L10.1462 20.5199L4.50931 17.6767L10.5435 15.7361L8.8784 9.79176L14.5871 13.0464L18.5669 8.05676Z" fill="white"/></svg>
                </div>
            </div>
            <h3 class="sby-feed-block-cta-heading"><?php echo $feeds_exist ? $info['cta_header2'] : $info['cta_header'] ?></h3>

            <?php if($feeds_exist): ?>
                <div class="sby-feed-block-cta-selector">
                    <?php if( $type == 'dropdown' ): ?>
                        <select class="sby-feed-block-cta-feedselector">
                            <option><?php echo __('Select', 'feeds-for-youtube') . ' ' . ucfirst($info['plugin']) . ' '. __('Feed', 'feeds-for-youtube')?> </option>
                            <?php foreach ($feeds_list as $feed_id => $feed_name): ?>
                                <option value="<?php echo $feed_id ?>"><?php echo $feed_name ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php elseif( $type == 'button' ): ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sby-feed-builder' ) ) ?>" rel="noopener noreferrer" class="sby-feed-block-cta-btn">
                            <?php echo esc_html__('Create YouTube Feed', 'feeds-for-youtube'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if( $type == 'dropdown' ): ?>   
                    <span class="sby-feed-block-create-with">
                        <?php
                            echo esc_html__('Or create a Feed for', 'feeds-for-youtube');
                            unset( $info['plugins'][$info['plugin']] );
                            foreach ($info['plugins'] as $name => $plugin):
                            $dashboard_permalink = !empty($plugin['dashboard_permalink']) ? $plugin['dashboard_permalink'] : '';
                            $installed = !empty($plugin['installed']) ? $plugin['installed'] : '';
                            $activated = !empty($plugin['activated']) ? $plugin['activated'] : '';
                            $website_link = !empty($plugin['website_link']) ? $plugin['website_link'] : '';
                            $link = $installed && $activated ? $dashboard_permalink : $website_link;
                        ?>
                            <a href="<?php echo esc_attr($link); ?>" target="_blank" class="sby-feed-block-link"><?php echo $name ?></a>
                        <?php endforeach ?>
                    </span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=sby-feed-builder' ) ) ?>" class="sby-feed-block-cta-btn"><?php echo esc_html__('Create', 'feeds-for-youtube') . ' ' . ucfirst($info['plugin']) . ' ' . esc_html__('Feed', 'feeds-for-youtube') ?></a>
            <?php endif; ?>

            <div class="sby-feed-block-cta-desc">
                <strong><?php echo esc_html__('Did you Know?', 'feeds-for-youtube') ?></strong>
                <span>
                    <?php echo \sby_is_pro() ? $info['cta_description_pro'] : $info['cta_description_free']; ?>
                </span>
            </div>
        </div>
        <?php
    }
}