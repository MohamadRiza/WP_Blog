<div class="sbc-feedtemplates-pp-ctn sbc-feedtemplates-ctn sb-fs-boss sbc-center-boss" v-if="viewsActive.feedtemplatesPopup">
	<div class="sbc-feedtemplates-popup sbc-popup-inside">
		<div class="sbc-popup-cls" @click.prevent.default="activateView('feedtemplatesPopup')"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg>
        </div>
        <div class="sbc-source-top sbc-fs">
            <h2>{{selectFeedTemplateScreen.updateHeading}}</h2>
            <p class="sbc-feedtemplate-alert sbc-fs">
                <span v-html="svgIcons['info']"></span>
                {{selectFeedTemplateScreen.updateHeadingWarning}}
            </p>
            <div class="sbc-feedtemplates sbc-fs">
                <div class="sbc-feedtemplates-list">
                    <div :class="['sbc-feedtemplate-el', 'sbc-feed-template-' + feedTemplateEl.type]" v-for="(feedTemplateEl, feedTemplateIn) in feedTemplates" :data-active="selectedFeedTemplateCustomizer(feedTemplateEl.type)" @click.prevent.default="chooseFeedTemplate(feedTemplateEl, true)">
                        <div class="sbc-feedtemplate-el-img sbc-fs" v-html="svgIcons[feedTemplateEl.icon]"></div>
                        <div class="sbc-feedtemplate-el-info sbc-fs">
                            <p class="sb-small-p sb-bold sb-dark-text" v-html="feedTemplateEl.title"></p>
                            <span class="sb-caption sb-lightest">{{feedTemplateEl.description}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sbc-srcs-popup-btns sbc-fs">
                <button class="sbc-srcs-update sbc-btn sbc-fs sb-btn-orange" @click.prevent.default="updateFeedTemplateCustomizer()">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.08058 8.36133L14.0355 0.406383L15.8033 2.17415L6.08058 11.8969L0.777281 6.59357L2.54505 4.8258L6.08058 8.36133Z" fill="white"/>
                    </svg>
                    <span>{{genericText.update}}</span>
                </button>
                <button class="ctf-fb-source-btn sbc-fs sb-btn-grey" @click.prevent.default="viewsActive.feedtemplatesPopup = false">
                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.3337 5.34163L15.1587 4.16663L10.5003 8.82496L5.84199 4.16663L4.66699 5.34163L9.32533 9.99996L4.66699 14.6583L5.84199 15.8333L10.5003 11.175L15.1587 15.8333L16.3337 14.6583L11.6753 9.99996L16.3337 5.34163Z" fill="#141B38"/>
                    </svg>
                    {{genericText.cancel}}
                </button>
            </div>
        </div>
	</div>
</div>