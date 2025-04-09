<div class="sbc-fs sbc-builder-footer-free-wrapper" v-if="(viewsActive.pageScreen == 'welcome' && feedsList != null && feedsList.length != 0) && !iscustomizerScreen">
    <div class="sbc-settings-cta" :class="{'sbc-show-features': freeCtaShowFeatures}" v-if="feedsList.length > 0 || legacyFeedsList.length > 0">
        <div class="sbc-cta-head-inner">
            <div class="sbc-cta-title">
                <div class="sbc-plugin-logo">
                    <svg width="36" height="37" viewBox="0 0 36 37" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 23L22.785 18.5L15 14V23ZM32.34 11.255C32.535 11.96 32.67 12.905 32.76 14.105C32.865 15.305 32.91 16.34 32.91 17.24L33 18.5C33 21.785 32.76 24.2 32.34 25.745C31.965 27.095 31.095 27.965 29.745 28.34C29.04 28.535 27.75 28.67 25.77 28.76C23.82 28.865 22.035 28.91 20.385 28.91L18 29C11.715 29 7.8 28.76 6.255 28.34C4.905 27.965 4.035 27.095 3.66 25.745C3.465 25.04 3.33 24.095 3.24 22.895C3.135 21.695 3.09 20.66 3.09 19.76L3 18.5C3 15.215 3.24 12.8 3.66 11.255C4.035 9.905 4.905 9.035 6.255 8.66C6.96 8.465 8.25 8.33 10.23 8.24C12.18 8.135 13.965 8.09 15.615 8.09L18 8C24.285 8 28.2 8.24 29.745 8.66C31.095 9.035 31.965 9.905 32.34 11.255Z" fill="#EB2121"/></svg>
                </div>
                <div class="sbc-plugin-title">
                    <h3>{{genericText.getMoreFeatures}}</h3>
                    <div class="sbc-plugin-title-bt">
                        <span class="sbc-cta-discount-label">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.841 9.65008L10.341 2.15008C10.0285 1.84015 9.60614 1.6664 9.16602 1.66675H3.33268C2.89066 1.66675 2.46673 1.84234 2.15417 2.1549C1.84161 2.46746 1.66602 2.89139 1.66602 3.33342V9.16675C1.66584 9.38668 1.7092 9.60446 1.79358 9.80756C1.87796 10.0106 2.00171 10.195 2.15768 10.3501L9.65768 17.8501C9.97017 18.16 10.3926 18.3338 10.8327 18.3334C11.274 18.3316 11.6966 18.1547 12.0077 17.8417L17.841 12.0084C18.154 11.6973 18.3308 11.2747 18.3327 10.8334C18.3329 10.6135 18.2895 10.3957 18.2051 10.1926C18.1207 9.98952 17.997 9.80513 17.841 9.65008ZM10.8327 16.6667L3.33268 9.16675V3.33342H9.16602L16.666 10.8334L10.8327 16.6667ZM5.41602 4.16675C5.66324 4.16675 5.90492 4.24006 6.11048 4.37741C6.31604 4.51476 6.47626 4.70999 6.57087 4.93839C6.66548 5.1668 6.69023 5.41814 6.642 5.66061C6.59377 5.90309 6.47472 6.12582 6.2999 6.30063C6.12508 6.47545 5.90236 6.5945 5.65988 6.64273C5.4174 6.69096 5.16607 6.66621 4.93766 6.5716C4.70925 6.47699 4.51403 6.31677 4.37668 6.11121C4.23933 5.90565 4.16602 5.66398 4.16602 5.41675C4.16602 5.08523 4.29771 4.76729 4.53213 4.53287C4.76655 4.29844 5.0845 4.16675 5.41602 4.16675Z" fill="#663D00"/>
                            </svg>
                            {{genericText.liteFeedUsers}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="sbc-cta-btn">
                <a :href="upgradeUrl" class="sbc-btn-blue" target="_blank">
                    {{genericText.tryDemo}}
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.166016 10.6584L8.99102 1.83341H3.49935V0.166748H11.8327V8.50008H10.166V3.00841L1.34102 11.8334L0.166016 10.6584Z" fill="white"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="sbc-cta-boxes" v-if="freeCtaShowFeatures">
            <div class="sbc-cta-box">
                <span class="sbc-cta-box-icon" v-html="svgIcons.ctaBoxes.live"></span>
                <span class="sbc-cta-box-title">{{genericText.ctaLive}}</span>
            </div>
            <div class="sbc-cta-box">
                <span class="sbc-cta-box-icon" v-html="svgIcons.ctaBoxes.feeds"></span>
                <span class="sbc-cta-box-title">{{genericText.ctaFeeds}}</span>
            </div>
            <div class="sbc-cta-box">
                <span class="sbc-cta-box-icon" v-html="svgIcons.ctaBoxes.customActions"></span>
                <span class="sbc-cta-box-title">{{genericText.ctaCustomActions}}</span>
            </div>
            <div class="sbc-cta-box">
                <span class="sbc-cta-box-icon" v-html="svgIcons.ctaBoxes.convertVideos"></span>
                <span class="sbc-cta-box-title">{{genericText.ctaConvertVideos}}</span>
            </div>
        </div>
        <div class="sbc-cta-much-more" v-if="freeCtaShowFeatures">
            <div class="sbc-cta-mm-left">
                <h4>{{genericText.andMuchMore}}</h4>
            </div>
            <div class="sbc-cta-mm-right">
                <ul>
                    <li v-for="item in genericText.sbyFreeCTAFeatures">{{item}}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="sbc-cta-toggle-features">
        <button class="sbc-cta-toggle-btn" @click="ctaToggleFeatures">
            <span v-if="!freeCtaShowFeatures">{{genericText.ctaShowFeatures}}</span>
            <span v-if="freeCtaShowFeatures">{{genericText.ctaHideFeatures}}</span>

            <svg v-if="freeCtaShowFeatures" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.91 15.41L12.5 10.83L17.09 15.41L18.5 14L12.5 8L6.5 14L7.91 15.41Z" fill="#141B38"/>
            </svg>
            <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#141B38"/>
            </svg>
        </button>
    </div>
</div>