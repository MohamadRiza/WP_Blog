<?php

namespace SmashBalloon\Reviews\Common\Utils;

class AjaxUtil{
    public static function send_json_error($message, $code)
    {
        wp_send_json_error([
            'message' => $message,
        ], $code);
    }

    public static function send_json_success($data, $message = 'Success', $code = 200)
    {
        wp_send_json_success([
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function ajaxPreflightChecks()
    {
        check_ajax_referer('sbr_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(); // This auto-dies.
        }
    }
}