<div class="sbc-fd-lst-bigctn sbc-fb-fs">
    <div class="sbc-fd-lst-bulk-ctn sbc-fb-fs">
        <select class="sbc-fd-lst-bulk-select sbc-fb-select sb-caption" v-model="selectedBulkAction">
            <option value="false">{{allFeedsScreen.bulkActions}}</option>
            <option value="delete">{{genericText.delete}}</option>
        </select>
        <button class="sbc-fd-lst-bulk-btn sbc-btn-grey sb-button-small sb-button" @click.prevent.default="bulkActionClick()">{{genericText.apply}}</button>
        <div class="sbc-fd-lst-pagination-ctn" v-if="feedPagination.feedsCount != null && feedPagination.feedsCount > 0">
			<span class="sbc-fd-lst-count sb-caption">{{feedPagination.feedsCount +' '+ (feedPagination.feedsCount > 1 ? genericText.items : genericText.item)}}</span>
			<div class="sbc-fd-lst-pagination" v-if="feedPagination.pagesNumber != null && feedPagination.pagesNumber > 1">
				<button class="sbc-fd-lst-pgnt-btn sbc-fd-pgnt-prev sb-btn-grey" :data-active="feedPagination.currentPage == 1 ? 'false' : 'true'" :disabled="feedPagination.currentPage == 1" @click.prevent.default="feedListPagination('prev')"><</button>
				<span class="sbc-fd-lst-pgnt-info">
					{{feedPagination.currentPage}} of {{feedPagination.pagesNumber}}
				</span>
				<button class="sbc-fd-lst-pgnt-btn sbc-fd-pgnt-next sb-btn-grey" :data-active="feedPagination.currentPage == feedPagination.pagesNumber ? 'false' : 'true'" :disabled="feedPagination.currentPage == feedPagination.pagesNumber" @click.prevent.default="feedListPagination('next')">></button>
			</div>
		</div>
    </div>
    <div class="sbc-table-wrap">
        <table>
            <thead class="sbc-fd-lst-thtf sbc-fd-lst-thead">
                <tr>
                    <th>
                        <div class="sbc-fd-lst-chkbx" @click.prevent.default="selectAllFeedCheckBox()" :data-active="checkAllFeedsActive()"></div>
                    </th>
                    <th><span class="sb-caption sb-lighter">{{allFeedsScreen.columns.nameText}}</span></th>
                    <th><span class="sb-caption sb-lighter">{{allFeedsScreen.columns.shortcodeText}}</span></th>
                    <th><span class="sb-caption sb-lighter">{{allFeedsScreen.columns.instancesText}}</span></th>
                    <th class="sbc-fd-lst-act-th"><span class="sb-caption sb-lighter">{{allFeedsScreen.columns.actionsText}}</span></th>
                </tr>
            </thead>
            <tbody class="sbc-fd-lst-tbody">
                <tr v-for="(feed, feedIndex) in feedsList">
                    <td>
                        <div class="sbc-fd-lst-chkbx" @click.prevent.default="selectFeedCheckBox(feed.id)" :data-active="feedsSelected.includes(feed.id)"></div>
                    </td>
                    <td>
                        <a :href="builderUrl+'&feed_id='+feed.id" class="sby-fd-lst-name sb-small-p sb-bold">{{feed.feed_name}}</a>
                        <span class="sby-fd-lst-type sb-caption sb-lighter">{{feed.settings.type}}</span>
                    </td>
                    <td>
                        <div class="sb-flex-center">
                            <span class="sby-fd-lst-shortcode sb-caption sb-lighter">[youtube-feed feed={{feed.id}}]</span>
                            <div class="sbc-fd-lst-shortcode-cp sbc-fd-lst-btn sbc-fb-tltp-parent" @click.prevent.default="copyToClipBoard('[youtube-feed feed='+feed.id+']')">
                                <div class="sbc-fb-tltp-elem"><span>{{(genericText.copy +' '+ genericText.shortcode).replace(/ /g,"&nbsp;")}}</span></div>
                                <div v-html="svgIcons['copy']"></div>
                            </div>
                        </div>
                    </td>
                    <td class="sb-caption sb-lighter">
                        <div class="sb-instances-cell">
                            <span>
                                Used in 
                                <span data-active="false" class="sbc-fb-view-instances sbc-fb-tltp-parent"  :data-active="feed.instance_count < 1 ? 'false' : 'true'" @click.prevent.default="feed.instance_count > 0 ? viewFeedInstances(feed) : checkAllFeedsActive()">
                                    {{feed.instance_count}} places
                                </span>
                            </span>
                        </div>
                    </td>
                    <td class="sbc-fd-lst-actions">
                        <div class="sb-flex-center">
                            <a :href="builderUrl+'&feed_id='+feed.id" class="sbc-fd-lst-btn sbc-fb-tltp-parent">
                                <div class="sbc-fb-tltp-elem"><span>Edit</span></div>
                                <div>
                                    <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.25 9.06241V11.2499H2.4375L8.88917 4.79824L6.70167 2.61074L0.25 9.06241ZM10.9892 2.69824L8.80167 0.510742L7.32583 1.99241L9.51333 4.17991L10.9892 2.69824Z" fill="currentColor"></path>
                                    </svg>
                                </div>
                            </a>
                            <button class="sbc-fd-lst-btn sbc-fb-tltp-parent" @click.prevent.default="feedActionDuplicate(feed)">
                                <div class="sbc-fb-tltp-elem"><span>{{genericText.duplicate.replace(/ /g,"&nbsp;")}}</span></div>
                                <div v-html="svgIcons['duplicate']"></div>
                            </button>
                            <button class="sbc-fd-lst-btn sbc-fd-lst-btn-delete sbc-fb-tltp-parent" @click.prevent.default="openDialogBox('deleteSingleFeed', feed)">
                                <div class="sbc-fb-tltp-elem"><span>{{genericText.delete.replace(/ /g,"&nbsp;")}}</span></div>
                                <div v-html="svgIcons['delete']"></div>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot class="sbc-fd-lst-thtf sbc-fd-lst-tfoot">
                <tr>
                    <td>
                    <div class="sbc-fd-lst-chkbx" @click.prevent.default="selectAllFeedCheckBox()" :data-active="checkAllFeedsActive()"></div>
                    </td>
                    <td><span>Name</span></td>
                    <td><span>Shortcode</span></td>
                    <td><span>Instances</span></td>
                    <td><span>Actions</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>