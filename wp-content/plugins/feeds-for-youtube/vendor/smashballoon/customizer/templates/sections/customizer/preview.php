<div class="sb-customizer-preview" :data-preview-device="customizerScreens.previewScreen">
	<?php
	/**
	 * YouTube Admin Notices
	 *
	 * @since 2.0
	 */
	do_action('sbc_admin_notices');

	$feed_id = ! empty( $_GET['feed_id'] ) ? (int)$_GET['feed_id'] : 0;
	?>

    <div class="sb-preview-ctn sb-tr-2">
        <div class="sb-preview-top-chooser sby-yt-fs">
            <strong v-html="genericText.preview"></strong>
            <div class="sb-preview-chooser">
                <button class="sb-preview-chooser-btn" v-for="device in previewScreens" v-bind:class="'sb-' + device" v-html="svgIcons[device]" @click.prevent.default="switchCustomizerPreviewDevice(device)" :data-active="customizerScreens.previewScreen == device"></button>
            </div>
        </div>

        <div class="sby-preview-ctn sby-yt-fs" :data-color-scheme="customizerFeedData.settings.colorpalette" :data-preview-screen="customizerScreens.previewScreen">
            <div>
                <component :is="{template}"></component>
            </div>
			<?php
				include_once CUSTOMIZER_ABSPATH . 'templates/preview/lightbox.php';
			?>
        </div>
    </div>
	<sby-dummy-lightbox-component :dummy-light-box-screen="dummyLightBoxScreen" :customizer-feed-data="customizerFeedData" :customizer-header-data="customizerHeaderData"></sby-dummy-lightbox-component>
</div>