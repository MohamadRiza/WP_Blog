<div class="sbc-api-pp-ctn sb-fs-boss sbc-popup-medium sbc-popup" v-if="viewsActive.accountAPIPopup" :class="{'account-connection-warning': showShowYTAccountWarning}">
    <div class="sbc-api-popup sbc-pp-popup-inside">
        <div class="sbc-pp-popup-cls" @click.prevent.default="activateView('accountAPIPopup')">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"></path>
            </svg>
        </div>
        <div>
            <div class="sbc-popup-header">
                <span class="sbc-hide-api-form" @click.prevent.default="hideAPIConnectForm()" v-if="shouldShowFeedAPIBackBtn"><span v-html="svgIcons.chevronLeft"></span> Back</span>
                <h3 v-if="!shouldShowManualConnect && !showShowYTAccountWarning">{{apiKeyPopupScreen.title}}</h3>
                <p v-if="!shouldShowManualConnect && !showShowYTAccountWarning">{{apiKeyPopupScreen.description}}</p>
                
                <h3 v-if="shouldShowManualConnect">{{apiKeyPopupScreen.manualConnectionTitle}}</h3>
                <p v-if="shouldShowManualConnect" v-html="apiKeyPopupScreen.manualConnectionDescription"></p>
                
                <h3 v-if="showShowYTAccountWarning">{{apiKeyPopupScreen.secondModalTitle}}</h3>
                <p v-if="showShowYTAccountWarning" v-html="apiKeyPopupScreen.secondModalDescription"></p>
            </div>
            <div class="sbc-popup-content">
                <div class="sbc-popup-buttons" v-if="!shouldShowFeedAPIForm && !shouldShowManualConnect &&!showShowYTAccountWarning">
                    <button class="sbc-btn sbc-btn-blue sbc-popup-btn" @click.prevent.default="showAPIConnectForm()">
                        <span v-html="svgIcons.plus" class="btn-icon"></span> 
                        {{apiKeyPopupScreen.btnOne}}
                    </button>
                    <button class="sbc-btn sbc-btn-default sbc-popup-btn" @click.prevent.default="showYTAccountLimitations()">
                        {{apiKeyPopupScreen.btnTwo}}
                    </button>
                    <a class="sbc-btn sbc-btn-default sbc-popup-btn sbc-manual-connect-btn" @click.prevent.default="showManualConnect()">{{apiKeyPopupScreen.btnThree}}</a>
                </div>
                <div class="sbc-api-form" v-if="shouldShowFeedAPIForm" :class="{'sbc-api-key-error': apiKeyError}">
                    <div>
                        <input type="text" name="" id="" v-model="selectedFeedModel.apiKey" :placeholder="apiKeyPopupScreen.enterAPIKey">
                        <span class="sbc-api-key-error" v-if="apiKeyError">{{apiKeyPopupScreen.errorMsg}}</span>
                        <span class="sbc-input-error-icon" v-if="apiKeyError">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.99984 1.6665C5.39984 1.6665 1.6665 5.39984 1.6665 9.99984C1.6665 14.5998 5.39984 18.3332 9.99984 18.3332C14.5998 18.3332 18.3332 14.5998 18.3332 9.99984C18.3332 5.39984 14.5998 1.6665 9.99984 1.6665ZM10.8332 14.1665H9.1665V12.4998H10.8332V14.1665ZM10.8332 10.8332H9.1665V5.83317H10.8332V10.8332Z" fill="#D72C2C"/>
                            </svg>
                        </span>
                    </div>
                    <button class="sbc-btn sbc-btn-blue"  @click.prevent.default="addAPIKey()"><span v-html="svgIcons.spinner" class="sbc-spinner" v-if="apiKeyBtnLoader"></span>{{apiKeyPopupScreen.add}}</button>
                </div>
                <div class="sbc-api-form" v-if="showShowYTAccountWarning" :class="{'sbc-api-key-error': apiKeyError}">
                    <button class="sbc-btn sbc-btn-default"  @click.prevent.default="backToApiPopup()"><span v-html="svgIcons.spinner" class="sbc-spinner" v-if="apiKeyBtnLoader"></span>{{apiKeyPopupScreen.back}}</button>
                    <form :action="connectSiteParameters.connect_site_url" method="POST" class="sbs-popoup-account-connect">
                        <input type="hidden" name="return_uri" :value="connectSiteParameters.return_uri">
                        <input type="hidden" name="nonce" :value="connectSiteParameters.nonce">
                        <input type="hidden" name="pro" :value="connectSiteParameters.pro">
                        <input type="hidden" name="version" :value="connectSiteParameters.version">
                        <input type="hidden" name="email" :value="connectSiteParameters.email" v-if="!connectSiteParameters.pro">
                        <button type="submit" class="sbc-btn sbc-btn-blue">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.46447 9.88917L12.8284 3.5252L14.2426 4.93942L6.46447 12.7176L2.22183 8.47495L3.63604 7.06074L6.46447 9.88917Z" fill="white"/>
                            </svg>
                            {{apiKeyPopupScreen.connectYouTubeAccount}}
                        </button>
                    </form>
                </div>
                <div class="sbc-api-form" v-if="shouldShowManualConnect" :class="{'sbc-api-key-error': apiKeyError}">
                    <div>
                        <input type="text" name="" id="" v-model="selectedFeedModel.accessToken" :placeholder="apiKeyPopupScreen.enterAccessToken">
                        <span class="sbc-api-key-error" v-if="accessTokenError" v-html="apiKeyPopupScreen.errorMsgAccessToken"></span>
                        <span class="sbc-input-error-icon" v-if="apiKeyError || accessTokenError">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.99984 1.6665C5.39984 1.6665 1.6665 5.39984 1.6665 9.99984C1.6665 14.5998 5.39984 18.3332 9.99984 18.3332C14.5998 18.3332 18.3332 14.5998 18.3332 9.99984C18.3332 5.39984 14.5998 1.6665 9.99984 1.6665ZM10.8332 14.1665H9.1665V12.4998H10.8332V14.1665ZM10.8332 10.8332H9.1665V5.83317H10.8332V10.8332Z" fill="#D72C2C"/>
                            </svg>
                        </span>
                    </div>
                    <button class="sbc-btn sbc-btn-blue" @click.prevent.default="addAccessToken()"><span v-html="svgIcons.spinner" class="sbc-spinner" v-if="apiKeyBtnLoader"></span>{{apiKeyPopupScreen.add}}</button>
                </div>
            </div>
            <div class="sbc-popup-footer" :data-form-api="shouldShowFeedAPIForm" v-if="!shouldShowManualConnect && !showShowYTAccountWarning">
                <p v-if="!shouldShowFeedAPIForm">{{apiKeyPopupScreen.note}}</p>
                <p v-if="shouldShowFeedAPIForm"><a :href="apiKeyPopupScreen.learnMoreLink" target="_blank">{{apiKeyPopupScreen.learnMore}} <span v-html="svgIcons.chevronRight"></span></a></p>
            </div>
        </div>
    </div>
</div>