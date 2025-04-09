<?php
/**
 * Custom Reviews Feed block with live preview.
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;

use SmashBalloon\Reviews\Common\Customizer\DB;
use SmashBalloon\Reviews\Common\Util;
use Smashballoon\Stubs\Services\ServiceProvider;

class SBR_Blocks extends ServiceProvider
{

    /**
     * Indicates if current integration is allowed to load.
     *
     * @since 1.0
     *
     * @return bool
     */
    public function allow_load()
    {
        return function_exists('register_block_type');
    }

    public function register()
    {
        if( $this->allow_load() )
        $this->load();
    }
    /**
     * Loads an integration.
     *
     * @since 1.0
     */
    public function load()
    {
        $this->hooks();
    }

    /**
     * Integration hooks.
     *
     * @since 1.0
     */
    protected function hooks()
    {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    /**
     * Register Custom Reviews Feed Gutenberg block on the backend.
     *
     * @since 1.0
     */
    public function register_block()
    {
        /*
        wp_register_style(
            'sbr-blocks-styles',
            trailingslashit(CFF_PLUGIN_URL) . 'assets/css/sbr-blocks.css',
            array('wp-edit-blocks'),
            CFFVER
        );
        */

        $attributes = array(
            'shortcodeSettings' => array(
                'type' => 'string',
            ),
            'noNewChanges' => array(
                'type' => 'boolean',
            ),
            'executed' => array(
                'type' => 'boolean',
            )
        );

        register_block_type(
            'sbr/sbr-feed-block',
            array(
                'attributes' => $attributes,
                'render_callback' => array($this, 'get_feed_html'),
            )
        );
    }

    /**
     * Load Custom Reviews Feed Gutenberg block scripts.
     *
     * @since 1.0
     */
    public function enqueue_block_editor_assets()
    {

        wp_enqueue_style('sbr-blocks-styles');
        wp_enqueue_script(
            'sbr-feed-block',
            trailingslashit( SBR_PLUGIN_URL ) . 'assets/js/sbr-blocks.js',
            array('wp-blocks', 'wp-i18n', 'wp-element'),
            SBRVER,
            true
        );

        $shortcodeSettings = '';

        $i18n = array(
            'addSettings' => esc_html__('Add Settings', 'reviews-feed'),
            'shortcodeSettings' => esc_html__('Shortcode Settings', 'reviews-feed'),
            'example' => esc_html__('Example', 'reviews-feed'),
            'preview' => esc_html__('Apply Changes', 'reviews-feed'),

        );

        if (!empty($_GET['sbr_wizard'])) {
            $shortcodeSettings = 'feed="' . (int) sanitize_text_field(wp_unslash($_GET['sbr_wizard'])) . '"';
        }

        wp_localize_script(
            'sbr-feed-block',
            'sbr_block_editor',
            array(
                'wpnonce' => wp_create_nonce('reviews-blocks'),
                'canShowFeed' => true,
                'configureLink' => get_admin_url() . '?page=sbr',
                'shortcodeSettings' => $shortcodeSettings,
                'i18n' => $i18n,
            )
        );


        sbr_scripts_enqueue();

    }

    /**
     * Get form HTML to display in a Custom Reviews Feed Gutenberg block.
     *
     * @param array $attr Attributes passed by Custom Reviews Feed Gutenberg block.
     *
     * @since 1.0
     *
     * @return string
     */
    public function get_feed_html($attr)
    {
        $feeds_count = DB::feeds_list_count();
        $shortcode_settings = isset($attr['shortcodeSettings']) ? $attr['shortcodeSettings'] : '';
        $settings = get_option('sbr_settings', []);
        if ($feeds_count <= 0) {
            //return $this->plain_block_design(empty( $settings['license_key'] ) ? 'inactive' : 'expired');
        }

        $return = '';
        //$return .= $this->get_license_expired_notice();

        if (empty($shortcode_settings) || strpos($shortcode_settings, 'feed=') === false) {
            $feeds = DB::get_feeds_list();
            if (!empty($feeds[0]['id'])) {
                $shortcode_settings = 'feed="' . (int) $feeds[0]['id'] . '"';
            }
        }

        $shortcode_settings = str_replace(array('[reviews-feed', ']'), '', $shortcode_settings);

        $return .= do_shortcode('[reviews-feed ' . $shortcode_settings . ']');

        return $return;

    }

    public function get_license_expired_notice()
    {
        // Check that the license exists and the user hasn't already clicked to ignore the message
        $settings = get_option('sbr_settings', []);

        if (empty( $settings['license_key'] )) {
            return $this->get_license_expired_notice_content('inactive');
        }
        // If license not expired then return;
        if (!empty($settings['license_key']) && isset($settings['license_status'] ) && $settings['license_status'] === 'active' ) {
            return;
        }

        return $this->get_license_expired_notice_content();
    }

    /**
     * Output the license expired notice content on top of the embed block
     *
     * @since 4.4.0
     */
    public function get_license_expired_notice_content($license_state = 'expired')
    {
        if (!is_admin() && !defined('REST_REQUEST')) {
            return;
        }



        $output = '<div class="sbr-block-license-expired-notice-ctn sbr-bln-license-state-' . $license_state . '">';
        $output .= '<div class="sbr-blen-header">';
        #$output .= $icons['eye2'];
        $output .= '<span>' . __('Only Visible to WordPress Admins', 'reviews-feed') . '</span>';
        $output .= '</div>';
        $output .= '<div class="sbr-blen-resolve">';
        $output .= '<div class="sbr-left">';
        #$output .= $icons['info'];
        if ($license_state == 'inactive') {
            $output .= '<span>' . __('Your license key is inactive. Activate it to enable Pro features.', 'reviews-feed') . '</span>';
        } else {
            $output .= '<span>' . __('Your license has expired! Renew it to reactivate Pro features.', 'reviews-feed') . '</span>';
        }
        $output .= '</div>';
        $output .= '<div class="sbr-right">';
        $output .= '<a href="" target="_blank">' . __('Resolve Now', 'reviews-feed') . '</a>';
       # $output .= $icons['chevronRight'];
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Plain block design when theres no feeds.
     *
     * @since 4.4.0
     */
    public function plain_block_design($license_state = 'expired')
    {
        if (!is_admin() && !defined('REST_REQUEST')) {
            return;
        }
        $other_plugins = $this->get_others_plugins();
        $should_display_license_notice = cff_main_pro()->cff_license_handler->should_disable_pro_features;
        $icons = CFF_Feed_Builder::builder_svg_icons();
        $output = '<div class="sbr-license-expired-plain-block-wrapper ' . $license_state . '">';
        /*
        if ($should_display_license_notice):
            $output .= '<div class="sbr-lepb-header">
				<div class="sb-left">';
            $output .= $icons['info'];
            if ($license_state == 'expired') {
                $output .= sprintf('<p>%s</p>', __('Your license has expired! Renew it to reactivate Pro features.', 'reviews-feed'));
            } else {
                $output .= sprintf('<p>%s</p>', __('Your license key is inactive. Activate it to enable Pro features.', 'reviews-feed'));
            }
            $output .= '</div>
				<div class="sb-right">
					<a href="' . cff_main_pro()->cff_license_handler->get_renew_url($license_state) . '">
						Resolve Now
						' . $icons['chevronRight'] . '
					</a>
				</div>
			</div>';
        endif;
        */
        $output .= '<div class="sbr-lepb-body">
				' . $icons['blockEditorCFFLogo'] . '
				<p class="sbr-block-body-title">Get started with your first feed from <br/> your Instagram profile</p>';

        $output .= sprintf(
            '<a href="%s" class="sbr-btn sbr-btn-blue">%s ' . $icons['chevronRight'] . '</a>',
            admin_url('admin.php?page=sbr-feed-builder'),
            __('Create a Reviews Feed', 'reviews-feed')
        );
        $output .= '</div>
			<div class="sbr-lepd-footer">
				<p class="sbr-lepd-footer-title">Did you know? </p>
				<p>You can add posts from ' . $other_plugins . ' using our free plugins</p>
			</div>
		</div>';

        return $output;
    }


    /**
     * Get other Smash Balloon plugins list
     *
     * @since 4.4.0
     */
    public function get_others_plugins()
    {
        $active_plugins = Util::get_sb_active_plugins_info();

        $other_plugins = array(
            'is_instagram_installed' => array(
                'title' => 'Instagram',
                'url' => 'https://smashballoon.com/instagram-feed/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
            ),
            'is_facebook_installed' => array(
                'title' => 'Facebook',
                'url' => 'https://smashballoon.com/reviews-feed/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
            ),
            'is_twitter_installed' => array(
                'title' => 'Twitter',
                'url' => 'https://smashballoon.com/custom-twitter-feeds/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
            ),
            'is_youtube_installed' => array(
                'title' => 'YouTube',
                'url' => 'https://smashballoon.com/youtube-feed/?utm_campaign=youtube-pro&utm_source=block-feed-embed&utm_medium=did-you-know',
            ),
        );

        if (!empty($active_plugins)) {
            foreach ($active_plugins as $name => $plugin) {
                if ($plugin != false) {
                    unset($other_plugins[$name]);
                }
            }
        }

        $other_plugins_html = array();
        foreach ($other_plugins as $plugin) {
            $other_plugins_html[] = '<a href="' . $plugin['url'] . '">' . $plugin['title'] . '</a>';
        }

        return \implode(", ", $other_plugins_html);
    }

    /**
     * Checking if is Gutenberg REST API call.
     *
     * @since 1.0
     *
     * @return bool True if is Gutenberg REST API call.
     */
    public static function is_gb_editor()
    {

        // TODO: Find a better way to check if is GB editor API call.
        return defined('REST_REQUEST') && REST_REQUEST && !empty($_REQUEST['context']) && 'edit' === $_REQUEST['context']; // phpcs:ignore
    }

}