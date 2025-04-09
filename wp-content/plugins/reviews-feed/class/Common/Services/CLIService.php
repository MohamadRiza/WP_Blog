<?php

namespace SmashBalloon\Reviews\Common\Services;

use SmashBalloon\Reviews\Common\Exceptions\RelayResponseException;
use SmashBalloon\Reviews\Common\Integrations\SBRelay;
use Smashballoon\Stubs\Services\ServiceProvider;

class CLIService extends ServiceProvider
{
    private $relay;

    public function __construct(SBRelay $relay)
    {
        $this->relay = $relay;
    }

    public function register()
    {
        if (defined('WP_CLI') && WP_CLI) {
            \WP_CLI::add_command('sbr_update', [$this, 'update_site_setting']);
            \WP_CLI::add_command('sbr_get', [$this, 'get_site_setting']);
        }
    }

    public function update_site_setting($args, $assoc_args)
    {
        try {
            $response = $this->relay->call('settings', $assoc_args, 'POST', true);
            if (isset($response['data'])) {
                \WP_CLI::success(json_encode($response['data']));
            }
        } catch (RelayResponseException $exception) {
            \WP_CLI::error($exception->getMessage());
        }
    }

    public function get_site_setting($args, $assoc_args)
    {
        try {
            $response = $this->relay->call('settings', [], 'GET', true);
            if (isset($response['data'])) {
                \WP_CLI::success(json_encode($response['data']));
            }
        } catch (RelayResponseException $exception) {
            \WP_CLI::error($exception->getMessage());
        }
    }
}