<div class="sb-onboarding-overlay sb-fs-boss sbc-center-boss" v-if="viewsActive.onboardingCustomizerPopup && iscustomizerScreen">
</div>
<div v-for="tooltip in customizeScreensText.onboarding.tooltips" 
    v-if="viewsActive.onboardingCustomizerPopup && iscustomizerScreen"
    v-bind:id="'sb-onboarding-tooltip-' + customizeScreensText.onboarding.type + '-' + tooltip.step" class="sbc-popup-inside sb-onboarding-tooltip sbc-source-top" v-bind:class="'sb-onboarding-tooltip-' + tooltip.step" v-bind:data-step="tooltip.step"
>
    <div class="sbc-popup-cls" @click.prevent.default="onboardingClose()">
		<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
		</svg>
	</div>
    <div class="sb-onboarding-top-row">
		<p class="sb-standard-p sb-bold">{{tooltip.heading}}</p>
		<div class="wp-clearfix"></div>
		<p class="sb-onboarding-step">{{tooltip.p}}</p>
	</div>
    <div class="sb-onboarding-bottom-row">
		<div class="sb-step-counter-wrap">
			<span>{{tooltip.step}}/{{customizeScreensText.onboarding.tooltips.length}}</span>
		</div>
		<div class="sb-previous-next-wrap">
			<div class="sbc-btn-wrapper">
				<div class="sbc-onboarding-previous sbc-hd-btn sbc-btn-grey sb-button-small sb-button-left-icon" v-bind:data-active="tooltip.step === 1 ? 'false' : 'true'" @click.prevent.default="onboardingPrev">
					<svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#141B38"/>
					</svg>{{genericText.previous}}
				</div>
				<div v-if="customizeScreensText.onboarding.tooltips.length > tooltip.step" class="sbc-onboarding-next sbc-hd-btn sbc-btn-grey sb-button-small sb-button-right-icon" @click.prevent.default="onboardingNext">{{genericText.next}}
					<svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1.66656 0L0.726562 0.94L3.7799 4L0.726562 7.06L1.66656 8L5.66656 4L1.66656 0Z" fill="#141B38"/>
					</svg>
				</div>
				<div v-if="customizeScreensText.onboarding.tooltips.length === tooltip.step" class="sbc-onboarding-finish sb-button-small ctf-btn-grey sbc-hd-btn" @click.prevent.default="onboardingClose">{{genericText.finish}}</div>
			</div>
		</div>
	</div>
</div>