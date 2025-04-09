<?php

/**
 * YouTube Feed Saver Manager
 *
 * @since 2.0
 */
namespace Smashballoon\Customizer;

/** @internal */
class Feed_Saver_Manager
{
    private $saver;
    private $preview_provider;
    private $feed_processor;
    public function __construct(\Smashballoon\Customizer\Feed_Saver $saver, \Smashballoon\Customizer\PreviewProvider $preview_provider, \Smashballoon\Customizer\Feed_Processor $feed_processor)
    {
        $this->saver = $saver;
        $this->preview_provider = $preview_provider;
        $this->feed_processor = $feed_processor;
    }
    /**
     * AJAX hooks for various feed data related functionality
     *
     * @since 2.0
     */
    public function hooks()
    {
        \add_action('wp_ajax_sbc_feed_saver_manager_fly_preview', array('Smashballoon\\Customizer\\Feed_Saver_Manager', 'feed_customizer_fly_preview'));
    }
    /**
     * Used To check if it's customizer Screens
     * Returns Feed info or false!
     *
     * @param bool $include_comments
     *
     * @return array|bool
     *
     * @since 2.0
     */
    public function maybe_feed_customizer_data($include_comments = \false)
    {
        if (isset($_GET['feed_id'])) {
            $feed_id = \sanitize_key($_GET['feed_id']);
            $this->saver->set_feed_id($feed_id);
            $settings = $this->saver->get_feed_settings();
            $settings = $this->process_videos_elements_settings($settings);
            $feed_db_data = $this->saver->get_feed_db_data();
            if ($settings !== \false) {
                $return = array('feed_info' => $feed_db_data, 'headerData' => $feed_db_data, 'settings' => $settings, 'posts' => array());
                return $this->feed_processor->process($return);
            }
        }
        return \false;
    }
    /**
     * Process videos elements settings
     * 
     * @since 2.0
     * 
     * @return array $settings
     */
    public function process_videos_elements_settings($settings)
    {
        $api_key_activated = \Smashballoon\Customizer\Feed_Builder::check_api_key_status();
        // if all video elements are disabled then make it as an empty array
        if (empty($settings['include'])) {
            $settings['include'] = array();
        }
        if (empty($settings['hoverinclude'])) {
            $settings['hoverinclude'] = array();
        }
        // if there are only one video element shown as string
        // convert it to an array
        if (!\is_array($settings['include'])) {
            $settings['include'] = array($settings['include']);
        }
        if (!\is_array($settings['hoverinclude'])) {
            $settings['hoverinclude'] = array($settings['hoverinclude']);
        }
        // If api is not added then remove stats and views from settings if were added before
        if (!$api_key_activated) {
            $hover_settings = $settings['hoverinclude'];
            if ($key = \array_search('view', $hover_settings) !== \false) {
                unset($hover_settings[$key]);
            }
            if ($key = \array_search('stats', $hover_settings) !== \false) {
                unset($hover_settings[$key]);
            }
            $settings['hoverinclude'] = $hover_settings;
        }
        return $settings;
    }
    /**
     * Used to retrieve Feed Posts for preview screen
     * Returns Feed info or false!
     *
     * @since 2.0
     */
    public function feed_customizer_fly_preview()
    {
        \check_ajax_referer('sby-admin', 'nonce');
        if (!sby_current_user_can('manage_youtube_feed_options')) {
            \wp_send_json_error();
        }
        if (isset($_POST['feedID']) && isset($_POST['previewSettings'])) {
            $feed_id = $_POST['feedID'];
            $preview_settings = $_POST['previewSettings'];
            $feed_name = $_POST['feedName'];
            $feed_cache = new \Smashballoon\Customizer\SBY_Cache($feed_id);
            $feed_cache->clear('all');
            $feed_cache->clear('posts');
            $this->saver->set_feed_id($feed_id);
            $this->saver->set_feed_name($feed_name);
            $this->saver->set_data($preview_settings);
            $atts = \Smashballoon\Customizer\Feed_Builder::add_customizer_att(array('feed' => $feed_id, 'customizer' => \true));
            $return['feed_html'] = $this->preview_provider->render($atts);
            echo $return['feed_html'];
        }
        \wp_die();
    }
    public function get_source_list($page = 1)
    {
        return $this->saver->get_source_list($page);
    }
    /**
     * Use a JSON string to import a feed with settings and sources. The return
     * is whether or not the import was successful
     *
     * @param string $json
     *
     * @return array
     */
    public function import_feed($json, $name = null)
    {
        $settings_data = \json_decode($json, \true);
        $return = [];
        $this->saver->set_data($settings_data);
        if (!empty($name)) {
            $this->saver->set_feed_name($name);
        }
        $this->saver->set_feed_id(\false);
        if ($this->saver->update_or_insert()) {
            $return = array('success' => \true, 'feed_id' => $this->saver->get_feed_id(), 'message' => \__('Feed imported successfully.', 'feeds-for-youtube'));
            return $return;
        } else {
            $return['message'] = \__('Could not import feed. Please try again', 'feeds-for-youtube');
        }
        return $return;
    }
}
