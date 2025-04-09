<div class="sb-onboarding-wizard-step-wrapper sb-onboarding-wizard-step-addsource sb-fs">
	<div class="sb-onboarding-wizard-step-top sb-fs">
		<strong v-html="onboardingWizardStepContent['add-source'].smallHeading"></strong>
		<h4 v-html="onboardingWizardStepContent['add-source'].heading"></h4>
	</div>
	<div class="sb-onboarding-wizard-sources-list sb-fs">

		<div class="sb-onboarding-wizard-source-newitem sb-fs" @click.prevent.default="activateView('sourcePopup', 'creationRedirect')">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            	<path d="M9.66634 5.66634H5.66634V9.66634H4.33301V5.66634H0.333008V4.33301H4.33301V0.333008H5.66634V4.33301H9.66634V5.66634Z" fill="#0096CC"/>
            </svg>
            <span class="sb-small-p sb-bold">{{genericText.addNew}}</span>
		</div>

		<div class="sb-onboarding-wizard-source-item sb-fs" v-for="source in sourcesList">
			<div class="sb-onboarding-wizard-source-item-avatar">
				<img :src="getSourceListAvatar(source)" :alt="source.username"/>
				<svg viewBox="0 0 12 13" width="16" fill="#1778F2" style="background: #fff; border-radius: 50px;"><path d="M11.8125 6.5C11.8125 3.28906 9.21094 0.6875 6 0.6875C2.78906 0.6875 0.1875 3.28906 0.1875 6.5C0.1875 9.40625 2.29688 11.8203 5.08594 12.2422V8.1875H3.60938V6.5H5.08594V5.23438C5.08594 3.78125 5.95312 2.96094 7.26562 2.96094C7.92188 2.96094 8.57812 3.07812 8.57812 3.07812V4.50781H7.85156C7.125 4.50781 6.89062 4.95312 6.89062 5.42188V6.5H8.50781L8.25 8.1875H6.89062V12.2422C9.67969 11.8203 11.8125 9.40625 11.8125 6.5Z"/></svg>
			</div>
			<strong v-html="source.username"></strong>
			<div class="sb-onboarding-wizard-source-item-delete" @click.prevent.default="openDialogBox('deleteSource', source)">
				<svg width="10" height="12" viewBox="0 0 10 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.00065 10.6667C1.00065 11.4 1.60065 12 2.33398 12H7.66732C8.40065 12 9.00065 11.4 9.00065 10.6667V2.66667H1.00065V10.6667ZM2.33398 4H7.66732V10.6667H2.33398V4ZM7.33398 0.666667L6.66732 0H3.33398L2.66732 0.666667H0.333984V2H9.66732V0.666667H7.33398Z" fill="#AF2121"/></svg>
			</div>
		</div>

	</div>
</div>

<div class="sb-onboarding-wizard-step-pag-btns sb-fs">
	<button class="sb-btn cff-btn-grey sb-btn-wizard-next" v-if="sourcesList.length === 0" v-html="'Skip this step'" @click.prevent.default="nextWizardStep"></button>
	<button class="sb-btn cff-btn-blue sb-btn-wizard-next" v-if="sourcesList.length >= 1" v-html="'Next'" @click.prevent.default="nextWizardStep"></button>
</div>