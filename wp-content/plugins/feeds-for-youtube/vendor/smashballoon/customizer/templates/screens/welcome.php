<div class="sbc-yt-full-wrapper sbc-yt-fs" v-if="viewsActive.pageScreen == 'welcome' && !iscustomizerScreen">
    <?php
        /**
         * YouTubeFeed Admin Notices
         *
         * @since 2.0
         */
        do_action('sby_admin_notices');
    ?>
    
	<div class="sbc-yt-wlcm-header sbc-yt-fs">
		<h2>{{welcomeScreen.mainHeading}}</h2>
        <div class="sb-positioning-wrap" >
            <div class="sbc-yt-btn sbc-yt-btn-new sbc-btn-orange" @click.prevent.default="switchScreen('pageScreen', 'selectFeed')">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.66537 5.66659H5.66536V9.66659H4.33203V5.66659H0.332031V4.33325H4.33203V0.333252H5.66536V4.33325H9.66537V5.66659Z" fill="white"/>
                </svg>
                <span>{{genericText.addNew}}</span>
            </div>
        </div>
	</div>
	<?php
        include_once CUSTOMIZER_ABSPATH . 'templates/sections/empty-state.php';
		include_once CUSTOMIZER_ABSPATH . 'templates/sections/feeds-list.php';
	?>
</div>