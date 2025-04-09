<?php

namespace Smashballoon\Customizer\V2;

/** @internal */
class Feed_Locator
{
    /**
     * @var Config
     */
    private $config;
    private $table;
    public function __construct(\Smashballoon\Customizer\V2\Config $config)
    {
        $this->config = $config;
        $this->table = $this->config->plugin_slug . '_feed_locator';
    }
    public function legacy_feed_locator_query($args)
    {
        global $wpdb;
        $feed_locator_table_name = $wpdb->prefix . $this->table;
        $group_by = '';
        if (isset($args['group_by'])) {
            $group_by = 'GROUP BY ' . esc_sql($args['group_by']);
        }
        $location_string = 'content';
        $by_feed_id = '';
        $by_shortcode_atts = '';
        if (isset($args['html_location'])) {
            $locations = \array_map('esc_sql', $args['html_location']);
            $location_string = \implode("', '", $locations);
        }
        if (isset($args['feed_id'])) {
            $by_feed_id = \sprintf("AND feed_id = '%s'", $args['feed_id']);
        }
        if (isset($args['shortcode_atts'])) {
            $by_feed_id = \sprintf("AND shortcode_atts = '%s'", $args['shortcode_atts']);
        }
        $page = 0;
        if (isset($args['page'])) {
            $page = (int) $args['page'] - 1;
            unset($args['page']);
        }
        $offset = \max(0, $page * \Smashballoon\Customizer\V2\DB::RESULTS_PER_PAGE);
        $limit = \Smashballoon\Customizer\V2\DB::RESULTS_PER_PAGE;
        $results = $wpdb->get_results("\n\t\t\tSELECT *\n\t\t\tFROM {$feed_locator_table_name}\n\t\t\tWHERE feed_id NOT LIKE '*%'\n\t\t  \tAND html_location IN ( '{$location_string}' )\n\t\t  \t{$by_feed_id}\n\t\t  \t{$group_by}\n\t\t  \tLIMIT {$limit}\n\t\t\tOFFSET {$offset};", ARRAY_A);
        return $results;
    }
    public function count($args)
    {
        global $wpdb;
        $feed_locator_table_name = $wpdb->prefix . $this->table;
        if (isset($args['shortcode_atts'])) {
            $results = $wpdb->get_results($wpdb->prepare("\n\t\t\tSELECT COUNT(*) AS num_entries\n            FROM {$feed_locator_table_name}\n            WHERE shortcode_atts = %s\n            ", $args['shortcode_atts']), ARRAY_A);
        } else {
            $results = $wpdb->get_results($wpdb->prepare("\n\t\t\tSELECT COUNT(*) AS num_entries\n            FROM {$feed_locator_table_name}\n            WHERE feed_id = %s\n            ", $args['feed_id']), ARRAY_A);
        }
        if (isset($results[0]['num_entries'])) {
            return (int) $results[0]['num_entries'];
        }
        return 0;
    }
    public function feed_locator_query($args)
    {
        global $wpdb;
        $feed_locator_table_name = $wpdb->prefix . $this->table;
        $group_by = '';
        if (isset($args['group_by'])) {
            $group_by = 'GROUP BY ' . esc_sql($args['group_by']);
        }
        $location_string = 'content';
        if (isset($args['html_location'])) {
            $locations = \array_map('esc_sql', $args['html_location']);
            $location_string = \implode("', '", $locations);
        }
        $page = 0;
        if (isset($args['page'])) {
            $page = (int) $args['page'] - 1;
            unset($args['page']);
        }
        $offset = \max(0, $page * \Smashballoon\Customizer\V2\DB::RESULTS_PER_PAGE);
        if (isset($args['shortcode_atts'])) {
            $results = $wpdb->get_results($wpdb->prepare("\n\t\t\tSELECT *\n\t\t\tFROM {$feed_locator_table_name}\n\t\t\tWHERE shortcode_atts = %s\n\t\t  \tAND html_location IN ( '{$location_string}' )\n\t\t  \t{$group_by}\n\t\t  \tLIMIT %d\n\t\t\tOFFSET %d;", $args['shortcode_atts'], \Smashballoon\Customizer\V2\DB::RESULTS_PER_PAGE, $offset), ARRAY_A);
        } else {
            $results = $wpdb->get_results($wpdb->prepare("\n\t\t\tSELECT *\n\t\t\tFROM {$feed_locator_table_name}\n\t\t\tWHERE feed_id = %s\n\t\t  \tAND html_location IN ( '{$location_string}' )\n\t\t  \t{$group_by}\n\t\t  \tLIMIT %d\n\t\t\tOFFSET %d;", $args['feed_id'], \Smashballoon\Customizer\V2\DB::RESULTS_PER_PAGE, $offset), ARRAY_A);
        }
        return $results;
    }
    /**
     * A custom table stores locations
     */
    public function create_table()
    {
        global $wpdb;
        $feed_locator_table_name = $wpdb->prefix . $this->table;
        if ($wpdb->get_var("show tables like '{$feed_locator_table_name}'") != $feed_locator_table_name) {
            $sql = "CREATE TABLE " . $feed_locator_table_name . " (\n\t\t\t\tid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,\n                feed_id VARCHAR(50) DEFAULT '' NOT NULL,\n                post_id BIGINT(20) UNSIGNED NOT NULL,\n                html_location VARCHAR(50) DEFAULT 'unknown' NOT NULL,\n                shortcode_atts LONGTEXT NOT NULL,\n                last_update DATETIME\n            );";
            $wpdb->query($sql);
        }
        $error = $wpdb->last_error;
        $query = $wpdb->last_query;
        $had_error = \false;
        if ($wpdb->get_var("show tables like '{$feed_locator_table_name}'") != $feed_locator_table_name) {
            $had_error = \true;
        }
        if (!$had_error) {
            $wpdb->query("ALTER TABLE {$feed_locator_table_name} ADD INDEX feed_id (feed_id)");
            $wpdb->query("ALTER TABLE {$feed_locator_table_name} ADD INDEX post_id (post_id)");
        }
    }
    public function update_legacy_to_builder($args)
    {
        global $wpdb;
        $data = array('feed_id' => '*' . $args['new_feed_id'], 'shortcode_atts' => '{"feed":"' . $args['new_feed_id'] . '"}');
        $affected = $wpdb->query($wpdb->prepare("UPDATE {$this->table}\n         \t\t\t\tSET feed_id = %s, shortcode_atts = %s", $data['feed_id'], $data['shortcode_atts']));
        return $affected;
    }
}
