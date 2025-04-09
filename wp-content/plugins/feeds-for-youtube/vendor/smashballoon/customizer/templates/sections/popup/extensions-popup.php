<div class="sbc-extensions-pp-ctn sb-fs-boss sbc-center-boss" v-if="viewsActive.extensionsPopupElement != null && viewsActive.extensionsPopupElement != false">
	<div 
        class="sbc-extensions-popup sbc-popup-inside" 
        v-if="viewsActive.extensionsPopupElement != null && viewsActive.extensionsPopupElement != false" 
        :data-getext-view="viewsActive.extensionsPopupElement"
        :class="{'sbc-extpp-license-expired-border': sbyLicenseNoticeActive}"
    >
        <div class="sbc-popup-cls" @click.prevent.default="activateView('extensionsPopupElement')">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg>
        </div>
        <div>
            <div class="sbc-extpp-top sbc-fs">
                <div class="sbc-extpp-info">
                    <div class="sbc-extpp-license-notice sbc-fs" v-if="sbyLicenseNoticeActive">
                        <span v-html="genericText.licenseInactive" v-if="sbyLicenseInactiveState"></span>
                        <span v-html="genericText.licenseExpired"  v-if="!sbyLicenseInactiveState"></span>
                    </div>
                    <div class="sbc-extpp-head sbc-fs"><h2 v-html="extensionsPopup[viewsActive.extensionsPopupElement].heading"></h2></div>
                    <div class="sbc-extpp-desc sbc-fs sb-caption" v-html="extensionsPopup[viewsActive.extensionsPopupElement].description"></div>
                    <div v-if="extensionsPopup[viewsActive.extensionsPopupElement].popupContentBtn && !sbyIsPro" v-html="extensionsPopup[viewsActive.extensionsPopupElement].popupContentBtn"></div>
                </div>
                <div class="sbc-extpp-img" v-html="extensionsPopup[viewsActive.extensionsPopupElement].img">
                </div>
            </div>
            <div class="sbc-extpp-bottom sbc-fs">
                <div v-if="typeof extensionsPopup[viewsActive.extensionsPopupElement].bullets !== 'undefined'" class="ctf-extension-bullets">
                    <h4>{{extensionsPopup[viewsActive.extensionsPopupElement].bullets.heading}}</h4>
                    <div class="ctf-extension-bullet-list">
                        <div class="ctf-extension-single-bullet" v-for="bullet in extensionsPopup[viewsActive.extensionsPopupElement].bullets.content">
                            <svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="4" height="4" fill="#0096CC"/>
                            </svg>
                            <span class="sb-small-p">{{bullet}}</span>
                        </div>
                    </div>
                </div>
                <div class="sbc-extpp-btns sbc-fs">
                    <a class="sbc-extpp-get-btn sbc-btn sbc-btn-green" :href="extensionsPopup[viewsActive.extensionsPopupElement].buyUrl" target="_blank">
                        <div class="sbc-cta-icon" v-if="!sbyIsPro" v-html="svgIcons['whiteBalloon']"></div>
                        {{ sbyIsPro ? (sbyLicenseInactiveState ? genericText.activateLicense : sbyLicenseNoticeActive ? genericText.renew : genericText.upgrade) : genericText.upgrade }}
                    </a>
                    <a class="sbc-btn-grey sbc-btn" href="https://smashballoon.com/youtube-feed/?utm_campaign=youtube-free&utm_source=feed-type&utm_medium=playlist&utm_content=LearnMore" target="_blank">
                        {{ genericText.learnMore }}
                    </a>
                </div>
            </div>
        </div>
	</div>
</div>