<div v-if="viewsActive.pageScreen == 'selectFeed' && !iscustomizerScreen && !fullScreenLoader" class="sbc-fs">
	<div class="sbc-create-ctn sbc-fs">
		<div class="sbc-feedtype-heading">
			<h1 v-if="viewsActive.selectedFeedSection !== 'selectSource'">{{selectFeedTypeScreen.mainHeading}}</h1>
			<h1 v-if="viewsActive.selectedFeedSection == 'selectSource'">{{selectFeedTypeScreen.addSource}}</h1>

			<div class="sbc-btn sbc-slctf-nxt sbc-btn-ac sbc-btn-orange" @click.prevent.default="creationProcessNext()">
				<span>{{genericText.next}}</span>
				<svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.3332 0.00683594L0.158203 1.18184L3.97487 5.00684L0.158203 8.83184L1.3332 10.0068L6.3332 5.00684L1.3332 0.00683594Z" fill="white"/>
				</svg>
			</div>
		</div>
		<?php
			$file_path = sby_is_pro() && !sby_license_notices_active() ? CUSTOMIZER_ABSPATH . 'templates/sections/' : CUSTOMIZER_ABSPATH . 'templates/sections/free/';
			$feed_plugin_upsell = CUSTOMIZER_ABSPATH . 'templates/sections/footer-banner/feed-plugins-upsell.php';

			include_once $file_path . 'feeds-type.php';
			include_once $file_path . 'select-source.php';
			include_once $file_path . 'select-template.php';
			include_once $feed_plugin_upsell;
			?>
	</div>

	<div class="sbc-ft-action sbc-slctfd-action sbc-fs">
		<div class="sbc-wrapper">
			<div class="sbc-slctf-back sbc-hd-btn sbc-btn-grey" @click.default.prevent="creationProcessBack()">
				<svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6.3415 1.18184L5.1665 0.00683594L0.166504 5.00684L5.1665 10.0068L6.3415 8.83184L2.52484 5.00684L6.3415 1.18184Z" fill="#141B38"/>
				</svg>
				<span>{{genericText.back}}</span>
			</div>
			<div class="sbc-btn sbc-slctf-nxt sbc-btn-ac sbc-btn-orange" @click.prevent.default="creationProcessNext()">
				<span>{{genericText.next}}</span>
				<svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.3332 0.00683594L0.158203 1.18184L3.97487 5.00684L0.158203 8.83184L1.3332 10.0068L6.3332 5.00684L1.3332 0.00683594Z" fill="white"/>
				</svg>
			</div>
		</div>
	</div>

</div>