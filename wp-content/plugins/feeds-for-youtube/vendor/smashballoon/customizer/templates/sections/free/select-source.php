<div class="sbc-feedtypes-ctn sbc-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'selectSource' && !iscustomizerScreen">
    <div class="sbc-select-source-content">
        <p class="sbc-src-type-title" v-html="sourcesScreenText[selectedFeed].title">Channel ID or Username</p>
        <input class="sbc-input" type="text" :placeholder="sourcesScreenText[selectedFeed].placeholder" v-model="selectedFeedModel[selectedFeed]">
        <p v-html="sourcesScreenText[selectedFeed].description" class="sbc-feedsource-description"></p>
        <ul v-if="sourcesScreenText[selectedFeed].lists">
            <li v-for="list in sourcesScreenText[selectedFeed].lists" v-html="list"></li>
        </ul>
    </div>
    <div class="sbc-select-source-footer" v-if="sourcesScreenText[selectedFeed].footerText">
        <p v-html="sourcesScreenText[selectedFeed].footerText"></p>
        <div class="sbc-select-source-footer-right">
            <a :href="sourcesScreenText[selectedFeed].connectURL ? sourcesScreenText[selectedFeed].connectURL : ''" class="sbc-channel-connect-btn sbc-btn sbc-btn-grey" v-html="sourcesScreenText[selectedFeed].connect"></a>
            <a class="sbc-feedflow-manual-connect" @click.prevent.default="showFeedSourceManualConnect()">Cannot Connect? Try connecting manually</a>
        </div>
    </div>
</div>