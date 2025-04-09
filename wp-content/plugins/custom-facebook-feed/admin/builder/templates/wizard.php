<div id="cff-builder-app" class="cff-fb-fs cff-builder-app"  :class="dismissLite == false ? 'cff-builder-app-lite-dismiss' : '' " :data-app-loaded="appLoaded ? 'true' : 'false'">
	<?php
		$icons = CustomFacebookFeed\Builder\CFF_Feed_Builder::builder_svg_icons();

		include_once CFF_BUILDER_DIR . 'templates/sections/header.php';
		include_once CFF_BUILDER_DIR . 'templates/sections/footer.php';
		//Onboarding Wizard
		include_once CFF_BUILDER_DIR . 'templates/screens/onboarding-wizard.php';
	?>
	<div class="sb-control-elem-tltp-content" v-show="tooltip.hover" @mouseover.prevent.default="hoverTooltip(true, 'inside')" @mouseleave.prevent.default="hoverTooltip(false, 'outside')">
		<div class="sb-control-elem-tltp-txt" v-html="tooltip.text" :data-align="tooltip.align"></div>
	</div>
</div>
