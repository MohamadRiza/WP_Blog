<?php SmashBalloon\Customizer\Builder_Customizer::register_controls(); ?>
<div class="sb-customizer-sidebar" v-bind:class="{ 'sb-onboarding-highlight' : viewsActive.onboardingStep === 2 || viewsActive.onboardingStep === 3 }">
    <div class="sb-customizer-sidebar-sec1 sbc-yt-fs">
        <div class="sb-customizer-sidebar-tab-ctn sbc-yt-fs" v-if="customizerScreens.activeSection == null">
            <div class="sb-customizer-sidebar-tab" v-for="tab in customizerSidebarBuilder" :data-active="customizerScreens.activeTab == tab.id"  @click.prevent.default="switchCustomizerTab(tab.id)"><span class="sb-standard-p sb-bold">{{tab.heading}}</span></div>
        </div>
        <div class="sb-customizer-sidebar-sec-ctn sbc-yt-fs" v-if="customizerScreens.activeSection == null">
            <div v-for="(section, sectionId) in customizerSidebarBuilder[customizerScreens.activeTab].sections">
                <div class="sb-customizer-sidebar-sec-el sbc-yt-fs" v-if="!section.isHeader" @click.prevent.default="switchCustomizerSection(sectionId, section)">
                    <div class="sb-customizer-sidebar-sec-el-icon" v-html="svgIcons[section.icon]"></div>
                    <span class="sb-small-p sb-bold sb-dark-text" v-html="section.heading"></span>
                    <div class="sb-customizer-chevron">
                        <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.66656 0L0.726562 0.94L3.7799 4L0.726562 7.06L1.66656 8L5.66656 4L1.66656 0Z" fill="#141B38"/>
                        </svg>
                    </div>
                </div>
                <div class="sb-customizer-sidebar-sec-elhead sbc-yt-fs" v-if="section.isHeader">
                    {{section.heading}}
                </div>
            </div>
            <div class="sb-customizer-sidebar-cache-wrapper sbc-yt-fs">
                <button class="sb-control-action-button sb-btn sbc-yt-fs sb-btn-grey" v-if="customizerScreens.activeTab == 'settings'" @click.prevent.default="clearSingleFeedCache()">
                    <div v-html="svgIcons['update']"></div>
                    <span>{{genericText.clearFeedCache}}</span>
                </button>
            </div>
        </div>
        <div class="sbc-yt-fs" v-if="customizerScreens.activeSection != null">
            <div class="sb-customizer-sidebar-header sbc-yt-fs" :data-separator="customizerScreens.activeSectionData.separator ? customizerScreens.activeSectionData.separator : ''">
                <div class="sb-customizer-sidebar-breadcrumb sbc-yt-fs">
                    <a @click.prevent.default="switchCustomizerTab(customizerScreens.activeTab)">
                        <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#434960"/>
                        </svg>{{customizerScreens.activeTab}}
                    </a>
                    <a v-if="customizerScreens.parentActiveSection != null" @click.prevent.default="switchCustomizerSection(customizerScreens.parentActiveSection, customizerScreens.parentActiveSectionData)" class="sbi-child-breadcrumb">
                        <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#434960"/>
                        </svg>{{customizerScreens.parentActiveSectionData.heading}}
                    </a>
                    <a v-if="customizerScreens.parentActiveSection == 'customize_videos' && nestedStylingSection.includes(customizerScreens.activeSection)" @click.prevent.default="backToPostElements()" class="sbi-child-breadcrumb">
                        <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#434960"/>
                        </svg>Elements
                    </a>
                    <a v-if="customizerScreens.activeSection == 'lightbox_call_to_action'" @click.prevent.default="backToLightboxExperience()" class="sbi-child-breadcrumb">
                        <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.27203 0.94L4.33203 0L0.332031 4L4.33203 8L5.27203 7.06L2.2187 4L5.27203 0.94Z" fill="#434960"/>
                        </svg>Player Experience
                    </a>
                </div>
                <h3 v-html="customizerScreens.activeSectionData.heading"></h3>
                <span class="sb-customizer-sidebar-intro" v-html="customizerScreens.activeSectionData.description" :class="{'no-intro': !customizerScreens.activeSectionData.description}"></span>
            </div>
            <div class="sb-customizer-sidebar-controls-ctn sbc-yt-fs">
                <div class="sb-control-ctn sbc-yt-fs" v-for="(control, ctlIndex) in customizerScreens.activeSectionData.controls">
					<?php \Smashballoon\Customizer\Builder_Customizer::get_controls_templates('settings'); ?>
                </div>
                <div class="sbc-sidebar-video-sections-link sb-control-elem-ctn sbc-yt-fs" v-if="customizerScreens.activeSection == 'customize_feedlayout'" @click.prevent.default="switchToVideosSection">
                    <div class="sbc-video-sections-link-inner">
                        <span class="sbc-vsl-icon" v-html="svgIcons.tweakVideo"></span>
                        <div class="sbc-vsl-text">
                            <p class="sbc-header">{{genericText.tweakVideoStyles}}</p>
                            <p>{{genericText.changeVideoStyle}}</p>
                        </div>
                        <span class="sbc-icon-right" v-html="svgIcons.chevronRight"></span>
                    </div>
                </div>
                <div class="sbc-sidebar-video-sections-link sb-control-elem-ctn sbc-yt-fs sbc-video-lightbox-info" v-if="customizerScreens.activeSection == 'lightbox_call_to_action' && customizerFeedData.settings.cta == 'link'">
                    <div class="sbc-video-sections-link-inner">
                        <span class="sbc-vsl-icon" v-html="svgIcons.tweakVideo"></span>
                        <div class="sbc-vsl-text">
                            <p class="sbc-header">{{genericText.setVideoLink}}</p>
                            <p v-html="genericText.setVideoLinkDesc"></p>
                        </div>
                    </div>
                </div>
                <div class="sb-customizer-sidebar-sec-el sbc-yt-fs" v-if="customizerScreens.activeSectionData.nested_sections && ((nesetdSection.condition != undefined ? checkControlCondition(nesetdSection.condition) : false) || (nesetdSection.condition == undefined ))" v-for="(nesetdSection, nesetdSectionId) in customizerScreens.activeSectionData.nested_sections" @click.prevent.default="switchCustomizerSection(nesetdSectionId, nesetdSection, true)">
                    <div class="sb-customizer-sidebar-sec-el-icon" v-html="svgIcons[nesetdSection.icon]"></div>
                    <strong>{{nesetdSection.heading}}</strong>
                    <div class="sb-customizer-chevron">
                        <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.66656 0L0.726562 0.94L3.7799 4L0.726562 7.06L1.66656 8L5.66656 4L1.66656 0Z" fill="#141B38"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>