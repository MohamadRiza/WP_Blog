<div class="sbc-upsell-banner sbc-feed-plugins-upsell-banner" v-if="viewsActive.pageScreen == 'selectFeed' && !iscustomizerScreen && !fullScreenLoader && viewsActive.selectedFeedSection == 'selectSource'">
    <div class="sbc-row">
        <div class="sbc-col-left">
        <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/upsell/all-plugins-upsell.jpg'; ?>" alt="click-social-upsell">
        </div>
        <div class="sbc-col-right">
            <div class="sbc-upsell-banner-title">{{genericText.feedPluginUpSellTitleFirstPart}} <span>{{genericText.feedPluginUpSellTitleSecondPart}}</span></div>
            <div class="sbc-upsell-banner-cta">
                <button class="sbc-upsell-banner-single-cta" v-for="(plugin, pluginName, platIndex) in plugins" @click.prevent.default="TriggerInstallLightbox(plugin)">
                    <div class="sbc-upsell-banner-single-cta-svg" v-html="plugin['svgIcon']"></div>{{plugin['displayName']}}
                </button>
            </div>
        </div>
    </div> 
</div>