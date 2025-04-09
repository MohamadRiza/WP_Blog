<?php
/**
 * Reviews Feed Saver
 *
 * @since 1.0
 */

namespace SmashBalloon\Reviews\Common\Builder;

use SmashBalloon\Reviews\Common\Customizer\ProxyProvider;
use SmashBalloon\Reviews\Common\Customizer\DB;

class SBR_Feed_Saver{

    /**
     * @var int
     *
     * @since 2.0
     */
    private $insert_id;

    /**
     * @var array
     *
     * @since 2.0
     */
    private $data;

    /**
     * @var array
     *
     * @since 2.0
     */
    private $sanitized_and_sorted_data;

    /**
     * @var array
     *
     * @since 2.0
     */
    private $feed_db_data;


    /**
     * @var string
     *
     * @since 2.0
     */
    private $feed_name;

    /**
     * @var string
     *
     * @since 2.0
     */
    private $feed_style;


    /**
     * @var bool
     *
     * @since 2.0
     */
    private $is_legacy;

    /**
     * @var ProxyProvider
     */
    private $proxy_provider;

    /**
     * @var DB
     *
     * @since 2.0
     */
    private $db;


    /**
     * SBY_Feed_Saver constructor.
     *
     * @param int $insert_id
     *
     * @since 1.0
     */
    public function __construct($insert_id){
        $this->proxy_provider = new ProxyProvider;
        $this->db = new DB();
        if ($insert_id === 'legacy') {
            $this->is_legacy = true;
            $this->insert_id = 0;
        }
        else {
            $this->is_legacy = false;
            $this->insert_id = $insert_id;
        }
    }

    /**
     * Feed insert ID if it exists
     *
     * @return bool|int
     *
     * @since 2.0
     */
    public function get_feed_id()
    {
        if ($this->is_legacy) {
            return 'legacy';
        }
        if (!empty($this->insert_id)) {
            return $this->insert_id;
        }
        else {
            return false;
        }
    }

    /**
     * @param array $data
     *
     * @since 2.0
     */
    public function set_data($data){
        $this->data = $data;
    }

    /**
     * @param string $feed_name
     *
     * @since 2.0
     */
    public function set_feed_name($feed_name)
    {
        $this->feed_name = $feed_name;
    }

    /**
     * @param string $feed_style
     *
     * @since 2.0
     */
    public function set_feed_style($feed_style)
    {
        $this->feed_style = $feed_style;
    }

    /**
     *
     * @return array
     *
     * @since 2.0
     */
    public function get_feed_db_data()
    {
        return $this->feed_db_data;
    }

    /**
     * Adds a new feed if there is no associated feed
     * found. Otherwise updates the exiting feed.
     *
     * @return false|int
     *
     * @since 2.0
     */
    public function update_or_insert()
    {
        $this->sanitize_and_sort_data();

        if ($this->exists_in_database()) {
            return $this->update();
        }
        else {
            return $this->insert();
        }
    }

    /**
     * Whether or not a feed exists with the
     * associated insert ID
     *
     * @return bool
     *
     * @since 2.0
     */
    public function exists_in_database(){
        if ($this->is_legacy) {
            return true;
        }

        if ($this->insert_id === false) {
            return false;
        }

        $args = array(
            'id' => $this->insert_id,
        );
        $results = $this->db->feeds_query($args);

        return isset($results[0]);
    }

    /**
     * Inserts a new feed from sanitized and sorted data.
     * Some data is saved in the sbi_feeds table and some is
     * saved in the sbi_feed_settings table.
     *
     * @return false|int
     *
     * @since 2.0
     */
    public function insert(){

        if (!isset($this->sanitized_and_sorted_data)) {
            return false;
        }

        $settings_array = self::format_settings($this->sanitized_and_sorted_data['feed_settings']);

        $this->sanitized_and_sorted_data['feeds'][] = array(
            'key' => 'settings',
            'values' => array(json_encode($settings_array)),
        );

        if (!empty($this->feed_name)) {
            $this->sanitized_and_sorted_data['feeds'][] = array(
                'key' => 'feed_name',
                'values' => array($this->feed_name),
            );
        }

        $this->sanitized_and_sorted_data['feeds'][] = array(
            'key' => 'status',
            'values' => array('publish'),
        );
        $insert_id = $this->db->feeds_insert($this->sanitized_and_sorted_data['feeds']);

        if ($insert_id) {
            $this->insert_id = $insert_id;

            return $insert_id;
        }

        return false;
    }

    /**
     * Updates an existing feed and related settings from
     * sanitized and sorted data.
     *
     * @return false|int
     *
     * @since 2.0
     */
    public function update(){
        if (!isset($this->sanitized_and_sorted_data)) {
            return false;
        }

        $args = array(
            'id' => $this->insert_id,
        );

        $settings_array = self::format_settings($this->sanitized_and_sorted_data['feed_settings']);

        $this->sanitized_and_sorted_data['feeds'][] = array(
            'key' => 'settings',
            'values' => array(json_encode($settings_array)),
        );

        $this->sanitized_and_sorted_data['feeds'][] = array(
            'key' => 'feed_name',
            'values' => array(sanitize_text_field($this->feed_name)),
        );

        $this->sanitized_and_sorted_data['feeds'][] = array(
            'key' => 'feed_style',
            'values' => array(sanitize_text_field($this->feed_style)),
        );

        $success = $this->db->feeds_update($this->sanitized_and_sorted_data['feeds'], $args);

        return $success;
    }

    /**
     * Converts settings that have been sanitized into an associative array
     * that can be saved as JSON in the database
     *
     * @param $raw_settings
     *
     * @return array
     *
     * @since 2.0
     */
    public static function format_settings( $raw_settings ){
        $settings_array = array();
        foreach ($raw_settings as $single_setting) {
            if (count($single_setting['values']) > 1) {
                $settings_array[$single_setting['key']] = $single_setting['values'];

            }
            else {
                $settings_array[$single_setting['key']] = isset($single_setting['values'][0]) ? $single_setting['values'][0] : '';
            }
        }

        return $settings_array;
    }

    /**
     * Gets the Preview Settings
     * for the Feed Fly Preview
     *
     * @return bool
     *
     * @since 2.0
     */
    public function get_feed_preview_settings( $preview_settings ){
        return false;
    }

    /**
     * Retrieves and organizes feed setting data for easy use in
     * the builder
     *
     * @return array|bool
     *
     * @since 1.0
     */
    public function get_feed_settings(){
        if (empty($this->insert_id)) {
            return false;
        }
        else {
            $args = array(
                'id' => $this->insert_id,
            );
            $settings_db_data = $this->db->feeds_query($args);
            if (false === $settings_db_data || sizeof($settings_db_data) === 0) {
                return false;
            }
            $this->feed_db_data = array(
                'id' => $settings_db_data[0]['id'],
                'feed_name' => $settings_db_data[0]['feed_name'],
                'feed_style' => isset( $settings_db_data[0]['feed_style'] ) ? $settings_db_data[0]['feed_style'] : '',
                'feed_title' => $settings_db_data[0]['feed_title'],
                'status' => $settings_db_data[0]['status'],
                'last_modified' => $settings_db_data[0]['last_modified'],
            );

            $return = json_decode($settings_db_data[0]['settings'], true);
            $return['feed_name'] = $settings_db_data[0]['feed_name'];
        }
        $return = wp_parse_args( $return, sbr_settings_defaults() );

        if (empty($return['id'])) {
            return $return;
        }


        return $return;
    }









    /**
     * Used for taking raw post data related to settings
     * an sanitizing it and sorting it to easily use in
     * the database tables
     *
     * @since 2.0
     */
    private function sanitize_and_sort_data(){
        $data = $this->data;

        $sanitized_and_sorted = array(
            'feeds' => array(),
            'feed_settings' => array(),
        );

        foreach ($data as $key => $value) {

            $data_type = SBR_Feed_Saver_Manager::get_data_type($key);
            $sanitized_values = array();
            if ( is_array( $value ) ) {
                //foreach ($value as $item) {
                //    $type = SBR_Feed_Saver_Manager::is_boolean($item) ? 'boolean' : $data_type['sanitization'];
                //    $sanitized_values[] = SBR_Feed_Saver_Manager::sanitize($type, $item);
                //}
                $sanitized_values[] = $value;
            }
            elseif( is_object( $value ) ){
                $sanitized_values[] = $value;
            }
            else {
                $type = SBR_Feed_Saver_Manager::is_boolean($value) ? 'boolean' : $data_type['sanitization'];
                $sanitized_values[] = SBR_Feed_Saver_Manager::sanitize($type, $value);
            }

            $single_sanitized = array(
                'key' => $key,
                'values' => $sanitized_values,
            );

            $sanitized_and_sorted[$data_type['table']][] = $single_sanitized;
        }

        $this->sanitized_and_sorted_data = $sanitized_and_sorted;
    }
}