<?php

namespace SmashBalloon\YouTubeFeed\Customizer\Tabs;

use Smashballoon\Customizer\Tabs\Manager;
use Smashballoon\Stubs\Services\ServiceProvider;

class TabsService extends ServiceProvider {

	/**
	 * @var Customize_Tab
	 */
	private $customize_tab;
	/**
	 * @var Settings_Tab
	 */
	private $settings_tab;

	public function __construct(Customize_Tab $customize_tab, Settings_Tab $settings_tab) {
		$this->customize_tab = $customize_tab;
		$this->settings_tab = $settings_tab;
	}

	public function register() {
		$tabs_manager = Manager::getInstance();
		$tabs_manager->register_tab($this->customize_tab);
		$tabs_manager->register_tab($this->settings_tab);
	}
}