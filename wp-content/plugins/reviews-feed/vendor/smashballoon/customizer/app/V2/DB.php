<?php

/**
 * Customizer Database
 *
 * @since 2.0
 */
namespace Smashballoon\Customizer\V2;

/** @internal */
class DB
{
    const RESULTS_PER_PAGE = 20;
    protected $sources_table = 'sb_sources';
    protected $feeds_table = 'sb_feeds';
    protected $caches_table = 'sb_feed_caches';
    protected $custom_source_table = \false;
    public function __construct()
    {
        global $wpdb;
        $this->sources_table = apply_filters('sb_customizer_sources_table', $wpdb->prefix . $this->sources_table);
        $this->feeds_table = apply_filters('sb_customizer_feeds_table', $wpdb->prefix . $this->feeds_table);
        $this->caches_table = apply_filters('sb_customizer_feeds_caches_table', $wpdb->prefix . $this->caches_table);
        $this->custom_source_table = apply_filters('sb_customizer_custom_source_table', $this->custom_source_table);
    }
    /**
     * Set Feeds Table
     *
     * @since 2.0
     */
    public function set_feeds_table($name)
    {
        if (!$name) {
            return;
        }
        $this->feeds_table = $name;
    }
    /**
     * Query the sbi_sources table
     *
     * @param array $args
     *
     * @return array|bool
     *
     * @since 6.0
     */
    public function source_query($args = array())
    {
        global $wpdb;
        $sources_table_name = $wpdb->prefix . $this->sources_table;
        $feeds_table_name = $wpdb->prefix . $this->feeds_table;
        if ($wpdb->get_var("show tables like '{$sources_table_name}'") !== $sources_table_name) {
            return [];
        }
        $page = 0;
        if (isset($args['page'])) {
            $page = (int) $args['page'] - 1;
            unset($args['page']);
        }
        $offset = \max(0, $page * self::RESULTS_PER_PAGE);
        if (empty($args)) {
            $limit = (int) self::RESULTS_PER_PAGE;
            $sql = "SELECT s.id, s.account_id, s.account_type, s.privilege, s.access_token, s.username, s.info, s.error, s.expires, count(f.id) as used_in\n\t\t\t\tFROM {$sources_table_name} s\n\t\t\t\tLEFT JOIN {$feeds_table_name} f ON f.settings LIKE CONCAT('%', s.account_id, '%')\n\t\t\t\tGROUP BY s.id, s.account_id\n\t\t\t\tLIMIT {$limit}\n\t\t\t\tOFFSET {$offset};\n\t\t\t\t";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (empty($results)) {
                return array();
            }
            $i = 0;
            foreach ($results as $result) {
                if ((int) $result['used_in'] > 0) {
                    $account_id = sanitize_key($result['account_id']);
                    $sql = "SELECT *\n\t\t\t\t\t\tFROM {$feeds_table_name}\n\t\t\t\t\t\tWHERE settings LIKE CONCAT('%', {$account_id}, '%')\n\t\t\t\t\t\tGROUP BY id\n\t\t\t\t\t\tLIMIT 100;\n\t\t\t\t\t\t";
                    $results[$i]['instances'] = $wpdb->get_results($sql, ARRAY_A);
                }
                $i++;
            }
            return $results;
        }
        if (!empty($args['expiring'])) {
            $sql = $wpdb->prepare("\n\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\tWHERE account_type = 'personal'\n\t\t\tAND expires < %s\n\t\t\tAND last_updated < %s\n\t\t\tORDER BY expires ASC\n\t\t\tLIMIT 5;\n\t\t ", \gmdate('Y-m-d H:i:s', \time() + SBI_REFRESH_THRESHOLD_OFFSET), \gmdate('Y-m-d H:i:s', \time() - SBI_MINIMUM_INTERVAL));
            return $wpdb->get_results($sql, ARRAY_A);
        }
        if (!empty($args['username'])) {
            return $wpdb->get_results($wpdb->prepare("\n\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\tWHERE username = %s;\n\t\t ", $args['username']), ARRAY_A);
        }
        if (isset($args['access_token']) && !isset($args['id'])) {
            return $wpdb->get_results($wpdb->prepare("\n\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\tWHERE access_token = %s;\n\t\t ", $args['access_token']), ARRAY_A);
        }
        if (!isset($args['id'])) {
            return \false;
        }
        if (\is_array($args['id'])) {
            $id_array = array();
            foreach ($args['id'] as $id) {
                $id_array[] = esc_sql($id);
            }
        } elseif (\strpos($args['id'], ',') !== \false) {
            $id_array = \explode(',', \str_replace(' ', '', esc_sql($args['id'])));
        }
        if (isset($id_array)) {
            $id_string = "'" . \implode("' , '", \array_map('esc_sql', $id_array)) . "'";
        }
        if (!empty($args['all_business'])) {
            $id_string = empty($id_string) ? '0' : $id_string;
            $sql = "\n\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\tWHERE account_id IN ({$id_string})\n\t\t\tOR account_type = 'business'\n\t\t ";
            return $wpdb->get_results($sql, ARRAY_A);
        }
        $privilege = '';
        if (!empty($privilege)) {
            if (isset($id_string)) {
                $sql = $wpdb->prepare("\n\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\tWHERE account_id IN ({$id_string})\n\t\t\tAND privilege = %s;\n\t\t ", $privilege);
            } else {
                $sql = $wpdb->prepare("\n\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\tWHERE account_id = %s\n\t\t\tAND privilege = %s;\n\t\t ", $args['id'], $privilege);
            }
        } else {
            if (isset($id_string)) {
                $sql = "\n\t\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\t\tWHERE account_id IN ({$id_string});\n\t\t\t\t";
            } else {
                $sql = $wpdb->prepare("\n\t\t\t\tSELECT * FROM {$sources_table_name}\n\t\t\t\tWHERE account_id = %s;\n\t\t\t    ", $args['id']);
            }
        }
        return $wpdb->get_results($sql, ARRAY_A);
    }
    /**
     * Update a source (connected account)
     *
     * @param array $to_update
     * @param array $where_data
     *
     * @return false|int
     *
     * @since 6.0
     */
    public function source_update($to_update, $where_data)
    {
    }
    /**
     * New source (connected account) data is added to the
     * sbi_sources table and the new insert ID is returned
     *
     * @param array $to_insert
     *
     * @return false|int
     *
     * @since 6.0
     */
    public function source_insert($to_insert)
    {
    }
    /**
     * Count the sby_feeds table
     *
     * @return int
     *
     * @since 6.0
     */
    public function feeds_count()
    {
        global $wpdb;
        $feeds_table_name = $this->feeds_table;
        $results = $wpdb->get_results("SELECT COUNT(*) AS num_entries FROM {$feeds_table_name}", ARRAY_A);
        return isset($results[0]['num_entries']) ? (int) $results[0]['num_entries'] : 0;
    }
    /**
     * Query the sby_feeds table
     *
     * @param array $args
     *
     * @return array|bool
     *
     * @since 6.0
     */
    public function feeds_query($args = array())
    {
        global $wpdb;
        $feeds_table_name = $this->feeds_table;
        $page = 0;
        if (isset($args['page'])) {
            $page = (int) $args['page'] - 1;
            unset($args['page']);
        }
        $offset = \max(0, $page * self::RESULTS_PER_PAGE);
        if (isset($args['id'])) {
            $sql = $wpdb->prepare("\n\t\t\tSELECT * FROM {$feeds_table_name}\n\t\t\tWHERE id = %d;\n\t\t ", $args['id']);
        } else {
            $sql = $wpdb->prepare("\n\t\t\tSELECT * FROM {$feeds_table_name}\n\t\t\tLIMIT %d\n\t\t\tOFFSET %d;", self::RESULTS_PER_PAGE, $offset);
        }
        return $wpdb->get_results($sql, ARRAY_A);
    }
    /**
     * Update feed data in the sbi_feed table
     *
     * @param array $to_update
     * @param array $where_data
     *
     * @return false|int
     *
     * @since 6.0
     */
    public function feeds_update($to_update, $where_data)
    {
        global $wpdb;
        $feeds_table_name = $this->feeds_table;
        $data = array();
        $where = array();
        $format = array();
        foreach ($to_update as $single_insert) {
            if ($single_insert['key']) {
                $data[$single_insert['key']] = $single_insert['values'][0];
                $format[] = '%s';
            }
        }
        if (isset($where_data['id'])) {
            $where['id'] = $where_data['id'];
            $where_format = array('%d');
        } elseif (isset($where_data['feed_name'])) {
            $where['feed_name'] = $where_data['feed_name'];
            $where_format = array('%s');
        } else {
            return \false;
        }
        $data['last_modified'] = \gmdate('Y-m-d H:i:s');
        $format[] = '%s';
        $affected = $wpdb->update($feeds_table_name, $data, $where, $format, $where_format);
        return $affected;
    }
    /**
     * New feed data is added to the sby_feeds table and
     * the new insert ID is returned
     *
     * @param array $to_insert
     *
     * @return false|int
     *
     * @since 6.0
     */
    public function feeds_insert($to_insert)
    {
        global $wpdb;
        $feeds_table_name = $this->feeds_table;
        $data = array();
        $format = array();
        foreach ($to_insert as $single_insert) {
            if ($single_insert['key']) {
                $data[$single_insert['key']] = $single_insert['values'][0];
                $format[] = '%s';
            }
        }
        $data['last_modified'] = \gmdate('Y-m-d H:i:s');
        $format[] = '%s';
        $data['author'] = get_current_user_id();
        $format[] = '%d';
        $wpdb->insert($feeds_table_name, $data, $format);
        return $wpdb->insert_id;
    }
    /**
     * Creates all database tables used in the new admin area in
     * the 6.0 update.
     *
     * TODO: Add error reporting
     *
     * @since 1.0
     */
    public function create_tables($include_charset_collate = \true, $skip_sources = \false)
    {
        if (!\function_exists('SmashBalloon\\Reviews\\Vendor\\dbDelta')) {
            require_once ABSPATH . '/wp-admin/includes/upgrade.php';
        }
        global $wpdb;
        $max_index_length = 191;
        $charset_collate = '';
        if ($include_charset_collate && \method_exists($wpdb, 'get_charset_collate')) {
            // get_charset_collate introduced in WP 3.5
            $charset_collate = $wpdb->get_charset_collate();
        }
        $feeds_table_name = $this->feeds_table;
        if ($wpdb->get_var("show tables like '{$feeds_table_name}'") !== $feeds_table_name) {
            $sql = "\n\t\t\tCREATE TABLE {$feeds_table_name} (\n\t\t\t id bigint(20) unsigned NOT NULL auto_increment,\n\t\t\t feed_name text NOT NULL default '',\n\t\t\t feed_title text NOT NULL default '',\n\t\t\t settings longtext NOT NULL default '',\n\t\t\t author bigint(20) unsigned NOT NULL default '1',\n\t\t\t status varchar(255) NOT NULL default '',\n\t\t\t last_modified datetime NOT NULL,\n\t\t\t feed_style LONGTEXT NOT NULL default '',\n\t\t\t PRIMARY KEY  (id),\n\t\t\t KEY author (author)\n\t\t\t) {$charset_collate};\n\t\t\t";
            $wpdb->query($sql);
        }
        $error = $wpdb->last_error;
        $query = $wpdb->last_query;
        $had_error = \false;
        if ($wpdb->get_var("show tables like '{$feeds_table_name}'") !== $feeds_table_name) {
            $had_error = \true;
        }
        if (!$had_error) {
        }
        $feed_caches_table_name = $this->caches_table;
        if ($wpdb->get_var("show tables like '{$feed_caches_table_name}'") !== $feed_caches_table_name) {
            $sql = '
				CREATE TABLE ' . $feed_caches_table_name . " (\n\t\t\t\tid bigint(20) unsigned NOT NULL auto_increment,\n\t\t\t\tfeed_id varchar(255) NOT NULL default '',\n                cache_key varchar(255) NOT NULL default '',\n                cache_value longtext NOT NULL default '',\n                cron_update varchar(20) NOT NULL default 'yes',\n                last_updated datetime NOT NULL,\n                PRIMARY KEY  (id),\n                KEY feed_id (feed_id({$max_index_length}))\n            ) {$charset_collate};";
            $wpdb->query($sql);
        }
        $error = $wpdb->last_error;
        $query = $wpdb->last_query;
        $had_error = \false;
        if ($wpdb->get_var("show tables like '{$feed_caches_table_name}'") !== $feed_caches_table_name) {
            $had_error = \true;
        }
        if (!$had_error) {
        }
        $sources_table_name = $this->sources_table;
        if ($skip_sources === \false && $wpdb->get_var("show tables like '{$sources_table_name}'") !== $sources_table_name) {
            $sql = '
			CREATE TABLE ' . $sources_table_name . " (\n\t\t\t\tid bigint(20) unsigned NOT NULL auto_increment,\n\t\t\t\taccount_id varchar(255) NOT NULL default '',\n                account_type varchar(255) NOT NULL default '',\n                privilege varchar(255) NOT NULL default '',\n                access_token varchar(1000) NOT NULL default '',\n                username varchar(255) NOT NULL default '',\n                info text NOT NULL default '',\n                error text NOT NULL default '',\n                expires datetime NOT NULL,\n                last_updated datetime NOT NULL,\n                author bigint(20) unsigned NOT NULL default '1',\n                PRIMARY KEY  (id),\n                KEY account_type (account_type({$max_index_length})),\n                KEY author (author)\n            ) {$charset_collate};";
            $wpdb->query($sql);
        }
        $error = $wpdb->last_error;
        $query = $wpdb->last_query;
        $had_error = \false;
        if ($wpdb->get_var("show tables like '{$sources_table_name}'") !== $sources_table_name) {
            $had_error = \true;
        }
        if (!$had_error) {
        }
        //Detect if there is custom sources table
        if ($this->custom_source_table === \true) {
            $this->create_sources_table();
        }
    }
    public function reset_tables()
    {
        global $wpdb;
        $feeds_table_name = $this->feeds_table;
        $wpdb->query("DROP TABLE IF EXISTS {$feeds_table_name}");
        $feed_caches_table_name = $wpdb->prefix . 'sbi_feed_caches';
        $wpdb->query("DROP TABLE IF EXISTS {$feed_caches_table_name}");
        $sources_table_name = $this->sources_table;
        $wpdb->query("DROP TABLE IF EXISTS {$sources_table_name}");
    }
    public function get_results_per_page()
    {
        return self::RESULTS_PER_PAGE;
    }
}
