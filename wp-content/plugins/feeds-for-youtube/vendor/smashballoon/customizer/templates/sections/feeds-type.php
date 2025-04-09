<div class="sbc-feedtypes-ctn sbc-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'feedsType' && !iscustomizerScreen">
	<div class="sbc-feedtypes sbc-fs">
		<h4>{{selectFeedTypeScreen.feedTypeHeading}}</h4>
		<span class="sbc-feedtypes-desc">{{selectFeedTypeScreen.mainDescription}}</span>
		<div class="sbc-feedtypes-list">
			<div class="sbc-feedtype-el" v-for="(feedTypeEl, feedTypeIn) in feedTypes" :data-active="selectedFeed == feedTypeEl.type && feedTypeEl.type != 'socialwall'" :data-type="feedTypeEl.type" @click.prevent.default="chooseFeedType(feedTypeEl)">
				<div class="sbc-feedtype-el-img sbc-fs" v-html="svgIcons[feedTypeEl.icon]" :data-feed-type="feedTypeEl.type"></div>
				<div class="sbc-feedtype-el-info sbc-fs">
					<span class="sb-small-p sb-bold sb-dark-text" v-html="feedTypeEl.title"></span>
					<span class="sb-caption sb-lightest sb-small-text">{{feedTypeEl.description}}</span>
				</div>
			</div>
		</div>
	</div>
</div>