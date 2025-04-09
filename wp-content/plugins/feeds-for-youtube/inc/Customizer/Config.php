<?php

namespace SmashBalloon\YouTubeFeed\Customizer;

class Config extends \Smashballoon\Customizer\Config {
	public $plugin_slug = 'sby';
	public $statuses_option = 'sby_statuses';

	public function isPro() {
		return sby_is_pro_version();
	}
}