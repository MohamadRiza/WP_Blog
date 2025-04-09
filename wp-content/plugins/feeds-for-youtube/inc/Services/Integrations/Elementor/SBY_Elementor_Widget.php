<?php

namespace SmashBalloon\YouTubeFeed\Services\Integrations\Elementor;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use SmashBalloon\YouTubeFeed\Builder\SBY_Db;
use SmashBalloon\YouTubeFeed\Services\Integrations\SBY_Integration;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class SBY_Elementor_Widget extends Widget_Base {

	public function get_name() {
        return 'sby-widget';
    }
    public function get_title() {
        return esc_html__('YouTube Feed', 'feeds-for-youtube');
    }
    public function get_icon() {
        return 'sb-elem-icon sb-elem-youtube';
    }
    public function get_categories() {
        return array('smash-balloon');
    }
    public function get_script_depends() {
        return [
            'sbyscripts',
            'sby-elementor-preview'
        ];
    }

    protected function register_controls() {
    	/********************************************
                    CONTENT SECTION
        ********************************************/
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('YouTube Feed Settings', 'feeds-for-youtube'),
            ]
        );
        $this->add_control(
            'feed_id',
            [
                'label' => esc_html__('Select a Feed', 'feeds-for-youtube'),
                'type' => 'sby_feed_control',
                'label_block' => true,
                'dynamic' => ['active' => true],
                'options' => SBY_Db::elementor_feeds_query(),
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
    	$settings = $this->get_settings_for_display();
    	if (isset($settings['feed_id']) && !empty($settings['feed_id'])) {
            $feed_id = (int) $settings['feed_id'];
    		$output = do_shortcode( shortcode_unautop( '[youtube-feed feed='. $feed_id .']' ) );
    	} else {
            $output = is_admin() ? SBY_Integration::get_widget_cta() : '';
    	}
        echo apply_filters('sby_output', $output, $settings);
    }




}