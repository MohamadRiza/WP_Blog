<div class="sbc-upsell-banner sbc-click-social-upsell-banner" v-if="(viewsActive.pageScreen == 'welcome' && feedsList != null && feedsList.length != 0) && !iscustomizerScreen">
    <div class="sbc-row">
        <div class="sbc-col-left">
        <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/upsell/click-social-upsell.jpg'; ?>" alt="click-social-upsell">
        </div>
        <div class="sbc-col-right">
            <div class="sbc-upsell-banner-title">{{genericText.clickSocialUpSellTitle}}</div>
            <p>{{genericText.clickSocialUpSellDescription}}</p>
            <div class="sbc-upsell-banner-cta">
                <a :href="genericLink.clickSocialUpSellCTALink" target="_blank" class="sbc-cta-link">
                    {{genericText.clickSocialUpSellCTAText}}
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.333496 8.72659L7.3935 1.66659H3.00016V0.333252H9.66683V6.99992H8.3335V2.60659L1.2735 9.66659L0.333496 8.72659Z" fill="white"/>
                    </svg>
                </a>
            </div>
        </div>
    </div> 
</div>