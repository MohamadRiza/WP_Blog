<?php
namespace SmashBalloon\YouTubeFeed;

class SBY_Vars {
	public function version() { return SBYVER; }

	public function plugin_dir() { return SBY_PLUGIN_DIR; }

	public function plugin_url() { return SBY_PLUGIN_URL; }

	public function plugin_basename() { return SBY_PLUGIN_BASENAME; }

	public function cron_update_cache_time() { return SBY_CRON_UPDATE_CACHE_TIME; }

	public function max_records() { return SBY_MAX_RECORDS; }

	public function text_domain() { return SBY_TEXT_DOMAIN; }

	public function slug() { return SBY_SLUG; }

	public function plugin_name( $with_a_an = false ) { if ( $with_a_an ) { return SBY_INDEF_ART . ' ' . SBY_PLUGIN_NAME; } return SBY_PLUGIN_NAME; }

	public function social_network() { return SBY_SOCIAL_NETWORK; }

	public function setup_url() { return SBY_SETUP_URL; }

	public function support_url() { return SBY_SUPPORT_URL; }

	public function oauth_processor_url() { return SBY_OAUTH_PROCESSOR_URL; }

	public function demo_url() { return SBY_DEMO_URL; }

	public function pro_logo() { return SBY_PRO_LOGO; }
}