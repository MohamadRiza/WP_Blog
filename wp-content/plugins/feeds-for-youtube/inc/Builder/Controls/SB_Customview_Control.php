<?php
/**
 * Customizer Builder
 * Custom View
 *  This control will used for custom HTMlL controls like (source, feed type...)
 * @since 2.0
 */
namespace SmashBalloon\YouTubeFeed\Builder\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SB_Customview_Control extends SB_Controls_Base {

	/**
	 * Get control type.
	 *
	 * Getting the Control Type
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return string
	*/
	public function get_type() {
		return 'customview';
	}

	/**
	 * Output Control
	 *
	 *
	 * @since 2.0
	 * @access public
	*/
	public function get_control_output( $controlEditingTypeModel ) {
		$this->get_control_sources_output( $controlEditingTypeModel );
		$this->get_control_shoppable_disabled_output( $controlEditingTypeModel );
		$this->get_control_shoppable_enabled_output( $controlEditingTypeModel );
		$this->get_control_shoppable_selected_post_output( $controlEditingTypeModel );
		$this->get_control_moderation_mode_output( $controlEditingTypeModel );

	}


	/**
	 * Shoppable Feed Disabled Output Control
	 *
	 *
	 * @since 2.0
	 * @access public
	*/
	public function get_control_shoppable_disabled_output( $controlEditingTypeModel ) {
		?>
	<div class="sb-control-shoppbale-disabled-ctn sb-control-imginfo-ctn" v-if="control.viewId == 'shoppabledisabled'">
		<div class="sb-control-imginfo-elem sby-yt-fs">
			<div class="sb-control-imginfo-icon sby-yt-fs" v-html="svgIcons['shoppableDisabled']"></div>
			<div class="sb-control-imginfo-text sby-yt-fs" data-textalign="left">
				<strong class="sb-bold sb-dark-text " v-html="customizeScreensText.shoppableFeedScreen.heading1"></strong>
				<span v-html="customizeScreensText.shoppableFeedScreen.description1"></span>
			</div>
		</div>
	</div>
		<?php
	}


	/**
	 * Shoppable Feed Enabled Output Control
	 *
	 *
	 * @since 2.0
	 * @access public
	*/
	public function get_control_shoppable_enabled_output( $controlEditingTypeModel ) {
		?>
	<div class="sb-control-shoppbale-enbaled-ctn sb-control-imginfo-ctn" v-if="control.viewId == 'shoppableenabled'">
		<div class="sb-control-imginfo-elem sby-yt-fs">
			<div class="sb-control-imginfo-icon sby-yt-fs" v-html="svgIcons['shoppableEnabled']"></div>
			<div class="sb-control-imginfo-text sby-yt-fs" data-textalign="center">
				<strong class="sb-bold sb-dark-text " v-html="customizeScreensText.shoppableFeedScreen.heading2"></strong>
			</div>
		</div>
	</div>
		<?php
	}


	/**
	 * Shoppable Feed Selected Post
	 *
	 *
	 * @since 2.0
	 * @access public
	*/
	public function get_control_shoppable_selected_post_output( $controlEditingTypeModel ) {
		?>
	<div class="sb-control-shoppbale-selectedpost-ctn" v-if="control.viewId == 'shoppableselectedpost' && shoppableFeed.postId != null">
		<strong v-html="genericText.selectedPost"></strong>
		<div class="sb-control-selectedpost-info sby-yt-fs">
			<img :src="shoppableFeed.postMedia" alt="Selected Shoppable">
			<span v-html="shoppableFeed.postCaption"></span>
		</div>
		<div class="sb-control-selectedpost-input sby-yt-fs">
			<span class="sby-yt-fs" v-html="genericText.productLink"></span>
			<input type="text" class="sb-control-input sby-yt-fs" v-model="shoppableFeed.postShoppableUrl" :placeholder="genericText.enterProductLink">
		</div>
		<div class="sb-control-selectedpost-btns sby-yt-fs">
			<button class="sb-shoppable-selectedpost-btn sbi-btn-grey" @click.prevent.default="addPostShoppableFeed()">
				<div v-html="svgIcons['checkmark']"></div>
				<span v-html="genericText.add"></span>
			</button>
			<button class="sb-shoppable-selectedpost-btn sbi-btn-grey" @click.prevent.default="cancelPostShoppableFeed()">
				<span v-html="genericText.cancel"></span>
			</button>
		</div>

	</div>
		<?php
	}



	/**
	 * Sources Output Control
	 *
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return HTML
	*/
	public function get_control_sources_output( $controlEditingTypeModel ) {
		?>
	<div class="sb-control-feedtype-ctn" v-if="control.viewId == 'sources'">

		<div class="sb-control-feedtype-item sby-yt-fs" v-for="(feedType, feedTypeID) in selectSourceScreen.multipleTypes" v-if="checkMultipleFeedTypeActiveCustomizer(feedTypeID)">

			<div class="sb-control-elem-label-title sby-yt-fs">
				<div class="sb-control-elem-heading sb-small-p sb-dark-text" v-html="feedType.heading"></div>
				<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(feedType.description, 'show', 'center' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
					<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
				</div>
			</div>

			<div class="sb-control-feedtype-list sby-yt-fs">
				<div class="sb-control-feedtype-list-item" v-for="selectedSource in returnSelectedSourcesByTypeCustomizer(feedTypeID)">
					<div class="sb-control-feedtype-list-item-icon" v-html="feedTypeID == 'hashtag' ? svgIcons['hashtag'] : svgIcons['user']"></div>
					<span v-html="feedTypeID == 'hashtag' ? selectedSource : selectedSource.username"></span>
				</div>
			</div>

		</div>

		<button class="sb-control-action-button sb-btn sb-btn-grey sby-yt-fs" @click.prevent.default="openFeedTypesPopupCustomizer()">
			<div v-html="svgIcons['edit']"></div>
			<span>{{genericText.editSources}}</span>
		</button>

	</div>
		<?php
	}


	/**
	 * Moderation Mode Ouptut
	 *
	 *
	 * @since 2.0
	 * @access public
	 */
	public function get_control_moderation_mode_output( $controlEditingTypeModel ) {
		?>
	<div class="sb-control-moderationmode-ctn" v-if="control.viewId == 'moderationmode'">
		<button class="sb-control-moderationmode-btn sb-btn sb-btn-right-icon sb-btn-grey sby-yt-fs" v-if="!viewsActive.moderationMode" @click.prevent.default="openModerationMode()">
			<div class="sb-btn-right-txt">
				<div v-html="svgIcons['eye1']"></div>
				<span>{{genericText.moderateFeed}}</span>
			</div>
			<div class="sb-btn-right-chevron"></div>
		</button>

		<div class="sb-control-moderationmode-elements sby-yt-fs" v-if="viewsActive.moderationMode">

			<div class="sb-control-switcher-ctn" :data-active="<?php echo $controlEditingTypeModel; ?>[control.switcher.id] === control.switcher.options.enabled" @click.prevent.default="changeSwitcherSettingValue(control.switcher.id, control.switcher.options.enabled, control.switcher.options.disabled, control.switcher.ajaxAction ? control.switcher.ajaxAction : false)">
				<div class="sb-control-switcher sb-tr-2"></div>
				<div class="sb-control-label" v-if="control.switcher.label" :data-title="control.switcher.labelStrong ? 'true' : false">{{control.switcher.label}}</div>
			</div>

			<div v-if="<?php echo $controlEditingTypeModel; ?>[control.switcher.id] == control.switcher.options.enabled">
				<div class="sb-control-moderatiomode-selement sby-yt-fs sb-control-before-brd">
					<div class="sb-control-elem-label-title sby-yt-fs">
						<div class="sb-control-elem-heading sb-small-p sb-dark-text">{{genericText.moderationMode}}</div>
					</div>
					<div class="sb-control-toggle-set-ctn sb-control-toggle-set-desc-ctn sby-yt-fs">
						<div class="sb-control-toggle-elm sby-yt-fs sb-tr-2" v-for="(moderationItem, moderationId) in control.moderationTypes " @click.prevent.default="switchModerationListType(moderationId)" :data-active="moderationSettings.list_type_selected == moderationId">
							<div class="sb-control-toggle-deco sb-tr-1"></div>
							<div class="sb-control-content">
								<div class="sb-control-label">{{moderationItem.label}}</div>
								<div class="sb-control-toggle-description">{{moderationItem.description}}</div>
							</div>
						</div>
					</div>
				</div>

				<div class="sb-control-moderatiomode-selement sby-yt-fs sb-control-before-brd">
					<div class="sb-control-elem-label-title sby-yt-fs">
						<div class="sb-control-elem-heading sb-small-p sb-dark-text">{{genericText.moderationModeEnterPostId}}</div>
					</div>
					<div class="sby-yt-fs">
						<textarea class="sb-control-input-textrea sby-yt-fs" v-model="customBlockModerationlistTemp" :placeholder="genericText.moderationModeTextareaPlaceholder"></textarea>
					</div>
				</div>

				<div class="sb-control-moderationmode-action-btns sb-control-before-brd sby-yt-fs">
					<button class="sb-btn sb-btn-blue sby-yt-fs" @click.prevent.default="saveModerationSettings()">
						<div class="sbi-fb-icon-success"></div>
						{{genericText.moderateFeedSaveExit}}
					</button>
					<button class="sb-btn sb-btn-grey sby-yt-fs" @click.prevent.default="activateView('moderationMode'); moderationShoppableMode = false;">
						<div class="sbi-fb-icon-cancel"></div>
						{{genericText.cancel}}
					</button>
				</div>
			</div>


		</div>
	</div>
		<?php
	}

}
