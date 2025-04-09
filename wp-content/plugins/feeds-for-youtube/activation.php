<?php

if (!function_exists('sby_on_plugin_activation')) {
    function sby_on_plugin_activation($plugin)
    {
        if (basename($plugin) !== 'youtube-feed.php') {
            return;
        }

        $plugin_to_deactivate = 'feeds-for-youtube';
        if (false !== strpos($plugin, $plugin_to_deactivate)) {
            $plugin_to_deactivate = 'youtube-feed-pro';
        }

        foreach (sby_get_active_plugins() as $basename) {
            if ($basename === $plugin_to_deactivate . '/youtube-feed.php') {
                deactivate_plugins($basename);

                return;
            }
        }
    }

    function sby_get_active_plugins()
    {
        if (is_multisite()) {
            $active_plugins = array_keys((array)get_site_option('active_sitewide_plugins', array()));
        } else {
            $active_plugins = (array)get_option('active_plugins', array());
        }

        return $active_plugins;
    }
}

add_action('activated_plugin', 'sby_on_plugin_activation');