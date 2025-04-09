<?php

namespace CustomFacebookFeed\Integrations\Divi;

use ET_Builder_Module;
use CustomFacebookFeed\Builder\CFF_Db;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SBFacebookFeed extends ET_Builder_Module
{
    /**
     * Module slug.
     *
     * @var string
     */
    public $slug       = 'sb_facebook_feed';

    /**
     * VB support.
     *
     * @var string
     */
    public $vb_support = 'on';


    /**
     * Init module.
     *
     * @since 4.3
     */
    public function init()
    {
        $this->name = esc_html__('Custom Facebook Feed', 'custom-facebook-feed');
    }


    /**
     * Get list of settings.
     *
     * @since 4.3
     *
     * @return array
     */
    public function get_fields()
    {
        $feeds_list = CFF_Db::elementor_feeds_query();


        return [
            'feed_id'    => [
                'label'           => esc_html__('Feed', 'custom-facebook-feed'),
                'type'            => 'select',
                'option_category' => 'basic_option',
                'toggle_slug'     => 'main_content',
                'options'         => $feeds_list,
            ]
        ];
    }

    /**
     * Disable advanced fields configuration.
     *
     * @since 4.3
     *
     * @return array
     */
    public function get_advanced_fields_config()
    {

        return [
            'link_options' => false,
            'text'         => false,
            'background'   => false,
            'borders'      => false,
            'box_shadow'   => false,
            'button'       => false,
            'filters'      => false,
            'fonts'        => false,
        ];
    }

    /**
     * Render module on the frontend.
     *
     * @since 4.3
     *
     * @param array  $attrs       List of unprocessed attributes.
     * @param string $content     Content being processed.
     * @param string $render_slug Slug of module that is used for rendering output.
     *
     * @return string
     */
    public function render($attrs, $content = null, $render_slug = '')
    {

        if (empty($this->props['feed_id'])) {
            return '';
        }

        return do_shortcode(
            sprintf(
                '[custom-facebook-feed feed="%1$s"]',
                absint($this->props['feed_id'])
            )
        );
    }
}
