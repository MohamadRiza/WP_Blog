<div class="sbc-feeds-list sbc-fb-fs" v-if="(feedsList != null && feedsList.length > 0 ) || (legacyFeedsList != null && legacyFeedsList.length > 0)">
    <?php
		include_once CUSTOMIZER_ABSPATH . 'templates/sections/feeds/legacy-feeds.php';
		include_once CUSTOMIZER_ABSPATH . 'templates/sections/feeds/feeds.php';
	?>
</div>
<?php
include_once CUSTOMIZER_ABSPATH . 'templates/sections/feeds/instances.php';
