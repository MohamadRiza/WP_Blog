<?php

namespace SmashBalloon\YouTubeFeed\Services\Integrations\Divi;

use ET_Builder_Module;
use SmashBalloon\YouTubeFeed\Builder\SBY_Db;

class SB_YouTube_Feed extends ET_Builder_Module {
    /**
	 * Module slug.
	 *
	 * @var string
	 */
	public $slug       = 'sb_youtube_feed';

    /**
	 * VB support.
	 *
	 * @var string
	 */
	public $vb_support = 'on';


    /**
	 * Init module.
	 *
	 * @since 2.3
	 */
	public function init() {
		$this->name = esc_html__('YouTube Feed', 'feeds-for-youtube');
	}

    /**
	 * Get list of settings.
	 *
	 * @since 2.3
	 *
	 * @return array
	 */
	public function get_fields() {
		$feeds_divi  = array();
		$feeds_list = SBY_Db::elementor_feeds_query();

		if ( ! empty( $feeds_list ) ) {
			$feeds_divi[ 'sby-0' ] = esc_html__('Select Youtube Feed', 'feeds-for-youtube');
			foreach ( $feeds_list as $key => $feed ) {
				$feeds_divi[ 'sby-' . $key ] = $feed;
			}
		}

        return [
            'feed_id'    => [
				'label'           => esc_html__('Feed', 'feeds-for-youtube'),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => $feeds_divi,
			]
        ];
    }

    /**
	 * Disable advanced fields configuration.
	 *
	 * @since 2.3
	 *
	 * @return array
	 */
	public function get_advanced_fields_config() {
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
	 * @since 2.3
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render($attrs, $content = null, $render_slug = '') {
		if (empty($this->props['feed_id'])) {
			return '';
		}
		
		$feed_id = str_replace('sby-', '', $this->props['feed_id']);

		return do_shortcode(
			sprintf(
				'[youtube-feed feed="%1$s"]',
				absint($feed_id)
			)
		);
	}
}
