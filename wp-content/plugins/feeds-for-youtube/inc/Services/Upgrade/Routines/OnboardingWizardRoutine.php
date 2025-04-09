<?php
namespace SmashBalloon\YouTubeFeed\Services\Upgrade\Routines;

class OnboardingWizardRoutine extends UpgradeRoutine {
	protected $target_version = 2.3;

	public function run() {
		$this->set_onboarding_wizard_flag();
	}

	private function set_onboarding_wizard_flag() {
		if($this->is_fresh_install) {
			return;
		}

        $sby_statuses_option = get_option( 'sby_statuses', array() );
        if( !isset( $sby_statuses_option['wizard_dismissed'] ) || $sby_statuses_option['wizard_dismissed'] === false){
		    $sby_statuses_option['wizard_dismissed'] = true;
		    update_option( 'sby_statuses', $sby_statuses_option );
        }
	}
}
