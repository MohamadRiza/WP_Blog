<?php

namespace SmashBalloon\Reviews\Common\Services;

use SmashBalloon\Reviews\Common\Builder\SBR_Feed_Saver_Manager;
use SmashBalloon\Reviews\Common\Utils\AjaxUtil;
use Smashballoon\Stubs\Services\ServiceProvider;

class SettingsManagerService extends ServiceProvider
{
    private $settings_options = 'sbr_settings';

    public function register(): void
    {
        add_action('wp_ajax_sbr_update_settings', [$this, 'ajax_update_settings']);
    }

    public function ajax_update_settings(): void
    {
        check_ajax_referer('sbr-admin', 'nonce');

        if (!sbr_current_user_can('manage_reviews_feed_options')) {
            wp_send_json_error();
        }

        unset($_POST['nonce'], $_POST['action']);

        $settings = json_decode(stripslashes($_POST['settings']), true);

        $update_array = $this->sanitize_settings( $settings ) ;

        foreach ($settings['translations'] as $key => $value) {
            $update_array['translations'][$key] = sanitize_text_field( $value );
        }


        if ($this->update_settings($update_array)) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
       }
    }

    public function update_settings($settings): bool
    {
        $current_settings = $this->get_settings();
        $updated_settings = array_merge($current_settings, $settings);

        return update_option($this->settings_options, $updated_settings);
    }

    public function get_settings(): array
    {
        return get_option($this->settings_options, []);
    }

    public function sanitize_settings( $data )
    {
        $sanitized_and_sorted= [];

        foreach ($data as $key => $value) {
            $data_type = SBR_Feed_Saver_Manager::get_data_type($key);
            $sanitized_values = array();
            if (is_array($value)) {
                $sanitized_values[] = $value;
            } elseif (is_object($value)) {
                $sanitized_values[] = $value;
            } else {
                $type = SBR_Feed_Saver_Manager::is_boolean($value) ? 'boolean' : $data_type['sanitization'];
                $sanitized_values = SBR_Feed_Saver_Manager::sanitize($type, $value);
            }

            $sanitized_and_sorted[$key] = $sanitized_values;

        }
        return $sanitized_and_sorted;
    }
}