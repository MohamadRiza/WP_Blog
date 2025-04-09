<?php

/**
 * Customizer Builder
 * Custom View
 *  This control will used for custom HTMlL controls like (source, feed type...)
 * @since 6.0
 */
namespace Smashballoon\Customizer\Controls;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class SB_Customview_Control extends \Smashballoon\Customizer\Controls\SB_Controls_Base
{
    /**
     * Get control type.
     *
     * Getting the Control Type
     *
     * @since 6.0
     * @access public
     *
     * @return string
     */
    public function get_type()
    {
        return 'customview';
    }
    /**
     * Output Control
     *
     *
     * @since 6.0
     * @access public
     */
    public function get_control_output($controlEditingTypeModel)
    {
        $this->get_control_feedtype_output($controlEditingTypeModel);
        $this->get_control_feedtemplate_output($controlEditingTypeModel);
    }
    /**
     * Feed Templates Output Control
     *
     *
     * @since 4.0
     * @access public
     *
     * @return HTML
     */
    public function get_control_feedtype_output($controlEditingTypeModel)
    {
        ?>
			<div :class="['sb-control-feedtype-ctn sb-control-feedtemplate-ctn', 'sbc-feedtemplate-' + customizerScreens.printedTemplate.type]" v-if="control.viewId == 'feedtype'">
				<div class="sbc-feedtemplate-el" v-if="customizerFeedTypePrint()"  @click.prevent.default="activateView('feedtypesPopup')">
					<div class="sbc-feedtemplate-el-img sbc-fs" v-html="svgIcons[customizerScreens.printedTemplate.icon]"></div>
					<div class="sbc-feedtemplate-el-info sbc-fs">
						<strong class="sbc-fs" v-html="customizerScreens.printedTemplate.title"></strong>
					</div>
				</div>
				<button class="sb-control-action-button sb-btn sbc-fs sb-btn-grey" @click.prevent.default="activateView('feedtypesPopup')">
					<div v-html="svgIcons['edit']"></div>
					<span>{{genericText.change}}</span>
				</button>
			</div>

			<!-- For Channel feed type -->
			<div class="sbc-control-feedtype-source sbc-fs" v-if="control.viewId == 'feedtype' && customizerFeedData.settings.type == 'channel'">
				<div class="sbc-feedtype-label-wrap">
					<strong>{{genericText.channelOrUsername}}</strong>
					<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(tooltipContent[customizerFeedData.settings.type], 'show', 'left' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
						<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
					</div>
				</div>
				<div class="sb-control-feedtype-source-input cff-fb-fs">
					<input class="sb-control-input" type="text" v-model="customizerFeedData.settings.channel">
					<button class="sb-control-action-button sbc-btn sbc-btn-default" @click.prevent.default="customizerControlAjaxAction('feedHandleFlyPreview')">
						<span>{{genericText.update}}</span>
					</button>
				</div>
			</div>

			<!-- For Playlist feed type -->
			<div class="sbc-control-feedtype-source sbc-fs" v-if="control.viewId == 'feedtype' && customizerFeedData.settings.type == 'playlist'">
				<div class="sbc-feedtype-label-wrap">
					<strong>{{genericText.playlistId}}</strong>
					<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(tooltipContent[customizerFeedData.settings.type], 'show', 'left' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
						<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
					</div>
				</div>
				<div class="sb-control-feedtype-source-input cff-fb-fs">
					<input class="sb-control-input" type="text" v-model="customizerFeedData.settings.playlist">
					<button class="sb-control-action-button sbc-btn sbc-btn-default" @click.prevent.default="customizerControlAjaxAction('feedFlyPreview')">
						<span>{{genericText.update}}</span>
					</button>
				</div>
			</div>

			<!-- For Favorites feed type -->
			<div class="sbc-control-feedtype-source sbc-fs" v-if="control.viewId == 'feedtype' && customizerFeedData.settings.type == 'favorites'">
				<div class="sbc-feedtype-label-wrap">
					<strong>{{genericText.channelOrUsername}}</strong>
					<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(tooltipContent[customizerFeedData.settings.type], 'show', 'left' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
						<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
					</div>
				</div>
				<div class="sb-control-feedtype-source-input cff-fb-fs">
					<input class="sb-control-input" type="text" v-model="customizerFeedData.settings.favorites">
					<button class="sb-control-action-button sbc-btn sbc-btn-default" @click.prevent.default="customizerControlAjaxAction('feedHandleFlyPreview')">
						<span>{{genericText.update}}</span>
					</button>
				</div>
			</div>

			<!-- For Search feed type -->
			<div class="sbc-control-feedtype-source sbc-fs" v-if="control.viewId == 'feedtype' && customizerFeedData.settings.type == 'search'">
				<div class="sbc-feedtype-label-wrap">
					<strong>{{genericText.searchTerm}}</strong>
					<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(tooltipContent[customizerFeedData.settings.type], 'show', 'left' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
						<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
					</div>
				</div>
				<div class="sb-control-feedtype-source-input cff-fb-fs">
					<input class="sb-control-input" type="text" v-model="customizerFeedData.settings.search">
					<button class="sb-control-action-button sbc-btn sbc-btn-default" @click.prevent.default="customizerControlAjaxAction('feedFlyPreview')">
						<span>{{genericText.update}}</span>
					</button>
				</div>
			</div>

			<!-- For Livestream feed type -->
			<div class="sbc-control-feedtype-source sbc-fs" v-if="control.viewId == 'feedtype' && customizerFeedData.settings.type == 'live'">
				<div class="sbc-feedtype-label-wrap">
					<strong>{{genericText.channelOrUsername}}</strong>
					<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(tooltipContent[customizerFeedData.settings.type], 'show', 'left' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
						<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
					</div>
				</div>
				<div class="sb-control-feedtype-source-input cff-fb-fs">
					<input class="sb-control-input" type="text" v-model="customizerFeedData.settings.live">
					<button class="sb-control-action-button sbc-btn sbc-btn-default" @click.prevent.default="customizerControlAjaxAction('feedHandleFlyPreview')">
						<span>{{genericText.update}}</span>
					</button>
				</div>
			</div>

			<!-- For Single Videos feed type -->
			<div class="sbc-control-feedtype-source sbc-fs" v-if="control.viewId == 'feedtype' && customizerFeedData.settings.type == 'single'">
				<div class="sbc-feedtype-label-wrap">
					<strong>{{genericText.singleVideosId}}</strong>
					<div class="sb-control-elem-tltp" @mouseover.prevent.default="toggleElementTooltip(tooltipContent[customizerFeedData.settings.type], 'show', 'left' )" @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
						<div class="sb-control-elem-tltp-icon" v-html="svgIcons['info']"></div>
					</div>
				</div>
				<div class="sb-control-feedtype-source-input cff-fb-fs">
					<input class="sb-control-input" type="text" v-model="customizerFeedData.settings.single">
					<button class="sb-control-action-button sbc-btn sbc-btn-default" @click.prevent.default="customizerControlAjaxAction('feedFlyPreview')">
						<span>{{genericText.update}}</span>
					</button>
				</div>
			</div>
		<?php 
    }
    /**
     * Feed Templates Output Control
     *
     *
     * @since 4.0
     * @access public
     *
     * @return HTML
     */
    public function get_control_feedtemplate_output($controlEditingTypeModel)
    {
        ?>
			<div :class="['sb-control-feedtype-ctn sb-control-feedtemplate-ctn', 'sbc-feedtemplate-' + customizerScreens.printedTemplate.type]" v-if="control.viewId == 'feedtemplate'">
				<div class="sbc-feedtemplate-el" v-if="customizerFeedTemplatePrint()"  @click.prevent.default="activateView('feedtemplatesPopup')">
					<div class="sbc-feedtemplate-el-img sbc-fs" v-html="svgIcons[customizerScreens.printedTemplate.icon]"></div>
					<div class="sbc-feedtemplate-el-info sbc-fs">
						<strong class="sbc-fs" v-html="customizerScreens.printedTemplate.title"></strong>
					</div>
				</div>
				<button class="sb-control-action-button sb-btn sbc-fs sb-btn-grey" @click.prevent.default="activateView('feedtemplatesPopup')">
					<div v-html="svgIcons['edit']"></div>
					<span>{{genericText.change}}</span>
				</button>
			</div>

			<!-- For Feed type -->
			<div class="sbc-customview-alert sbc-fs" v-if="control.viewId == 'feedtemplate'">
				<span>
					<span v-html="svgIcons.info" class="sbc-alert-icon"></span>
					<span v-html="genericText.feedTemplateAlert"></span>
				</span>
			</div>
		<?php 
    }
}
