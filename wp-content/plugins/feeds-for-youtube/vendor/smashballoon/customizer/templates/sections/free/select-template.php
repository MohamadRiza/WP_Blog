<div class="sbc-feedtypes-ctn sbc-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'selectTemplate' && !iscustomizerScreen">
    <div class="sbc-select-template-content">
        <h4>{{selectTemplate.title}}</h4>
        <p>{{selectTemplate.description}}</p>
        <div class="sbc-feedtemplates-list">
            <div :class="['sbc-feedtemplate-el', 'sbc-feed-template-' + feedTemplateEl.type]" v-for="(feedTemplateEl, feedTemplateIn) in feedTemplates" @click.prevent.default="chooseFeedTemplate(feedTemplateEl)" :data-active="selectedFeedTemplate == feedTemplateEl.type">
                <div class="sbc-feedtemplate-el-img sbc-fs" v-html="svgIcons[feedTemplateEl.icon]"></div>
                <div class="sbc-feedtemplate-el-info sbc-fs">
                    <p class="sb-small-p sb-bold sb-dark-text" v-html="feedTemplateEl.title"></p>
                    <span class="sb-caption sb-lightest">{{feedTemplateEl.description}}</span>
                </div>
            </div>
        </div>
    </div>
</div>