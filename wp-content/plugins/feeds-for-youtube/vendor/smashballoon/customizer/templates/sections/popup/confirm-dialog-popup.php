<script type="text/x-template" id="sb-confirm-dialog-component">
	<div class="sb-dialog-ctn sb-fs-boss sbc-center-boss" v-if="dialogBoxElement.active">
		<div class="sb-dialog-popup sbc-fb-popup-inside sbc-delete-feed-popup">
			<div class="sbc-popup-cls" @click.prevent.default="closeConfirmDialog">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
                </svg>
            </div>
			<div class="sb-dialog-popup-content sbc-fs">
				<strong v-html="dialogBoxElement.heading"></strong>
				<span class="sb-dialog-popup-description" v-html="dialogBoxElement.description"></span>
				<div class="sb-dialog-popup-actions sbc-fs">
					<button class="sb-btn " :class="getButtonBackground('confirm',dialogBoxElement)" @click.prevent.default="confirmDialogAction" v-html="getButtonText('confirm',dialogBoxElement)"></button>
					<button class="sb-btn " :class="getButtonBackground('cancel',dialogBoxElement)" @click.prevent.default="closeConfirmDialog" v-html="getButtonText('cancel',dialogBoxElement)"></button>
				</div>
			</div>
		</div>
	</div>
</script>
