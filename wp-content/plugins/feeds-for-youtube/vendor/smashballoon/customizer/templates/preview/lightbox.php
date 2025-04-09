<script type="text/x-template" id="sby-dummy-lightbox-component">
<div id="sbc_lightbox" class="ctf-lightbox-dummy-ctn  sbc_lightbox ctf-lightbox-transitioning" :data-visibility="dummyLightBoxScreen" :class="[(!$parent.valueIsEnabled(customizerFeedData.settings.disablelightbox) ? 'sbc_lightbox-active' : 'sbc_lightbox-disabled')]" :data-playerratio="customizerFeedData.settings.playerratio">
        <div class="sbc_lb-outerContainer">
            <div class="sbc-lb-player-img">
                <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/sby_lightbox_player.png'; ?>" alt="">
            </div>
            <div class="sby_lb-dataContainer">
                <div class="sby_lb-data">
                    <div class="sby_lb-caption" :class="{'sby-lb-dark-scheme': 'dark' === customizerFeedData.settings.colorpalette}">
                        <div class="sby_lb-caption-inner" :class="{'sby_lb-no-channel-info': !customizerFeedData.settings.enablesubscriberlink}">
                            <div class="sby_lb-video-heading">
                                <h3>GoPro: Skate Queens of Barcelona | MACBA Life</h3>
                                <div class="sby_lb-video-info">
                                    <span>11.2M views</span>
                                    <span class="sby_lb-spacer">·</span>
                                    <span>8 day ago</span>
                                </div>
                            </div>
                            <div class="sby-lb-channel-header" v-if="customizerFeedData.settings.enablesubscriberlink">
                                <a class="sby_lightbox_username" href="https://www.youtube.com/watch?v=J2SvnnVBDk0" target="_blank" rel="noopener">
                                    <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/sby_channel_logo.png'; ?>" alt="">
                                    <p class="sby-lb-channel-name-with-subs">
                                        <span>@GoPro</span>
                                        <span>11.2M subscribers</span>
                                    </p>
                                </a>
                                <div class="sbc-channel-subscribe-btn">
                                    <button class="sby-lb-subscribe-btn">
                                        <svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.66671 7.5L9.12671 5.5L5.66671 3.5V7.5ZM13.3734 2.28C13.46 2.59334 13.52 3.01334 13.56 3.54667C13.6067 4.08 13.6267 4.54 13.6267 4.94L13.6667 5.5C13.6667 6.96 13.56 8.03334 13.3734 8.72C13.2067 9.32 12.82 9.70667 12.22 9.87334C11.9067 9.96 11.3334 10.02 10.4534 10.06C9.58671 10.1067 8.79337 10.1267 8.06004 10.1267L7.00004 10.1667C4.20671 10.1667 2.46671 10.06 1.78004 9.87334C1.18004 9.70667 0.793374 9.32 0.626707 8.72C0.540041 8.40667 0.480041 7.98667 0.440041 7.45334C0.393374 6.92 0.373374 6.46 0.373374 6.06L0.333374 5.5C0.333374 4.04 0.440041 2.96667 0.626707 2.28C0.793374 1.68 1.18004 1.29334 1.78004 1.12667C2.09337 1.04 2.66671 0.980002 3.54671 0.940002C4.41337 0.893336 5.20671 0.873336 5.94004 0.873336L7.00004 0.833336C9.79337 0.833336 11.5334 0.940003 12.22 1.12667C12.82 1.29334 13.2067 1.68 13.3734 2.28Z" fill="currentColor"></path>
                                        </svg>
                                        <p>Subscribe</p>
                                    </button>
                                </div>
                            </div>
                            <div class="sby_lb-video-description-wrap">
                                <div class="sby_lb-description">
                                    he MACBA Girls take on the iconic plaza 💪 "Girls with Attitude" filmed and edited by GoPro Family member Gonzalo Gonzalez De Vega with GoPro HERO9 Black and MAX. ‪@MacbaLife‬
                                </div>
                                <button class="sby_lb-more-info-btn">
                                    Description
                                    <svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.94 0.726654L4 3.77999L7.06 0.726654L8 1.66665L4 5.66665L0 1.66665L0.94 0.726654Z" fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="sby-comments-wrap" v-if="customizerFeedData.settings.enablecomments">
                                <h5 class="sby-comments-sub-heading">Comments ( 10 )</h5>
                                <ul class="sby-comments">
                                    <li class="sby-comment">
                                        <div class="sby-comment-profile-pic">
                                            <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/profile_pic_1.png'; ?>">
                                        </div>
                                        <div class="sby-comment-heading"> 
                                            <span class="sby-comment-user-name">@taylor_davis</span>
                                            <span>4 Months ago</span>
                                        </div>
                                        <div class="sby-comment-text">
                                            <p>This video was so insightful! Could you elaborate on what drove you to explore this topic in such depth? I found it really unique and captivating</p>
                                        </div>
                                        <div class="sby-comment-bottom">
                                            <span class="sby-comment-likes">
                                                <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.4159 4.18027C13.761 4.18027 14.0778 4.32177 14.3664 4.60477C14.6549 4.88777 14.7992 5.20738 14.7992 5.5636V6.2706C14.7992 6.36471 14.7902 6.45188 14.7722 6.5321C14.7542 6.61232 14.7272 6.69266 14.6912 6.7731L12.684 11.4908C12.5845 11.7449 12.4181 11.9486 12.1849 12.1019C11.9517 12.2552 11.69 12.3318 11.3999 12.3318H5.15938C4.77282 12.3318 4.44566 12.2006 4.17788 11.9383C3.90999 11.6759 3.77604 11.346 3.77604 10.9484V4.7561C3.77604 4.56277 3.81332 4.38049 3.88788 4.20927C3.96254 4.03804 4.06477 3.88754 4.19454 3.75777L7.28938 0.662932C7.5186 0.431043 7.79427 0.281321 8.11638 0.213765C8.43849 0.146321 8.71416 0.178988 8.94338 0.311765C9.22549 0.46421 9.40932 0.695932 9.49488 1.00693C9.58032 1.31793 9.58999 1.62804 9.52388 1.93727L9.09554 4.18027H13.4159ZM1.34404 12.3318C1.01393 12.3318 0.726767 12.2097 0.482544 11.9654C0.238322 11.7212 0.116211 11.434 0.116211 11.1039V5.40827C0.116211 5.07804 0.236989 4.79082 0.478544 4.5466C0.7201 4.30238 1.00466 4.18027 1.33221 4.18027H1.34804C1.67827 4.18027 1.96549 4.30238 2.20971 4.5466C2.45393 4.79082 2.57604 5.07804 2.57604 5.40827V11.1039C2.57604 11.434 2.45393 11.7212 2.20971 11.9654C1.96549 12.2097 1.67827 12.3318 1.34804 12.3318H1.34404Z" fill="currentColor"></path>
                                                </svg>
                                                144
                                            </span>
                                            <span class="sby-replies sby-active-trigger">
                                                2 Replies
                                                <svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.94 0.726654L4 3.77999L7.06 0.726654L8 1.66665L4 5.66665L0 1.66665L0.94 0.726654Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <ul class="sby-reply-comments">
                                            <li class="sby-reply-comment">
                                                <div class="sby-comment-profile-pic">
                                                    <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/profile_pic_2.png'; ?>">
                                                </div>
                                                <div class="sby-comment-heading">
                                                    <span class="sby-comment-user-name">@casey_wilson</span>
                                                    <span>4 Months ago</span>
                                                </div>
                                                <div class="sby-comment-text">
                                                    <p>I was wondering the same thing! The topic is so unique and refreshing. Would love to hear more about your thought process.</p>
                                                </div>
                                                <div class="sby-comment-bottom">
                                                    <span class="sby-comment-likes">
                                                        <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.4159 4.18027C13.761 4.18027 14.0778 4.32177 14.3664 4.60477C14.6549 4.88777 14.7992 5.20738 14.7992 5.5636V6.2706C14.7992 6.36471 14.7902 6.45188 14.7722 6.5321C14.7542 6.61232 14.7272 6.69266 14.6912 6.7731L12.684 11.4908C12.5845 11.7449 12.4181 11.9486 12.1849 12.1019C11.9517 12.2552 11.69 12.3318 11.3999 12.3318H5.15938C4.77282 12.3318 4.44566 12.2006 4.17788 11.9383C3.90999 11.6759 3.77604 11.346 3.77604 10.9484V4.7561C3.77604 4.56277 3.81332 4.38049 3.88788 4.20927C3.96254 4.03804 4.06477 3.88754 4.19454 3.75777L7.28938 0.662932C7.5186 0.431043 7.79427 0.281321 8.11638 0.213765C8.43849 0.146321 8.71416 0.178988 8.94338 0.311765C9.22549 0.46421 9.40932 0.695932 9.49488 1.00693C9.58032 1.31793 9.58999 1.62804 9.52388 1.93727L9.09554 4.18027H13.4159ZM1.34404 12.3318C1.01393 12.3318 0.726767 12.2097 0.482544 11.9654C0.238322 11.7212 0.116211 11.434 0.116211 11.1039V5.40827C0.116211 5.07804 0.236989 4.79082 0.478544 4.5466C0.7201 4.30238 1.00466 4.18027 1.33221 4.18027H1.34804C1.67827 4.18027 1.96549 4.30238 2.20971 4.5466C2.45393 4.79082 2.57604 5.07804 2.57604 5.40827V11.1039C2.57604 11.434 2.45393 11.7212 2.20971 11.9654C1.96549 12.2097 1.67827 12.3318 1.34804 12.3318H1.34404Z" fill="currentColor"></path>
                                                        </svg>
                                                        120
                                                    </span>
                                                </div>
                                            </li>
                                            <li class="sby-reply-comment">
                                                <div class="sby-comment-profile-pic">
                                                    <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/profile_pic_3.png'; ?>">
                                                </div>
                                                <div class="sby-comment-heading">
                                                    <span class="sby-comment-user-name">@alex_smith</span>
                                                    <span>4 Months ago</span>
                                                </div>
                                                <div class="sby-comment-text">
                                                    <p>Yes, please share more about your inspiration! It's always fascinating to learn about the creative process behind such interesting content.</p>
                                                </div>
                                                <div class="sby-comment-bottom">
                                                    <span class="sby-comment-likes">
                                                        <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.4159 4.18027C13.761 4.18027 14.0778 4.32177 14.3664 4.60477C14.6549 4.88777 14.7992 5.20738 14.7992 5.5636V6.2706C14.7992 6.36471 14.7902 6.45188 14.7722 6.5321C14.7542 6.61232 14.7272 6.69266 14.6912 6.7731L12.684 11.4908C12.5845 11.7449 12.4181 11.9486 12.1849 12.1019C11.9517 12.2552 11.69 12.3318 11.3999 12.3318H5.15938C4.77282 12.3318 4.44566 12.2006 4.17788 11.9383C3.90999 11.6759 3.77604 11.346 3.77604 10.9484V4.7561C3.77604 4.56277 3.81332 4.38049 3.88788 4.20927C3.96254 4.03804 4.06477 3.88754 4.19454 3.75777L7.28938 0.662932C7.5186 0.431043 7.79427 0.281321 8.11638 0.213765C8.43849 0.146321 8.71416 0.178988 8.94338 0.311765C9.22549 0.46421 9.40932 0.695932 9.49488 1.00693C9.58032 1.31793 9.58999 1.62804 9.52388 1.93727L9.09554 4.18027H13.4159ZM1.34404 12.3318C1.01393 12.3318 0.726767 12.2097 0.482544 11.9654C0.238322 11.7212 0.116211 11.434 0.116211 11.1039V5.40827C0.116211 5.07804 0.236989 4.79082 0.478544 4.5466C0.7201 4.30238 1.00466 4.18027 1.33221 4.18027H1.34804C1.67827 4.18027 1.96549 4.30238 2.20971 4.5466C2.45393 4.79082 2.57604 5.07804 2.57604 5.40827V11.1039C2.57604 11.434 2.45393 11.7212 2.20971 11.9654C1.96549 12.2097 1.67827 12.3318 1.34804 12.3318H1.34404Z" fill="currentColor"></path>
                                                        </svg>
                                                        144
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="sby-comment">
                                        <div class="sby-comment-profile-pic">
                                            <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/profile_pic_4.png'; ?>">
                                        </div>
                                        <div class="sby-comment-heading">
                                            <span class="sby-comment-user-name">@morgan_lee</span>
                                            <span>4 Months ago</span>
                                        </div>
                                        <div class="sby-comment-text">
                                            <p>Incredible video! I'm curious, what inspired you to choose this topic? It’s not something you see every day and it was very engaging!</p>
                                        </div>
                                        <div class="sby-comment-bottom">
                                            <span class="sby-comment-likes">
                                                <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.4159 4.18027C13.761 4.18027 14.0778 4.32177 14.3664 4.60477C14.6549 4.88777 14.7992 5.20738 14.7992 5.5636V6.2706C14.7992 6.36471 14.7902 6.45188 14.7722 6.5321C14.7542 6.61232 14.7272 6.69266 14.6912 6.7731L12.684 11.4908C12.5845 11.7449 12.4181 11.9486 12.1849 12.1019C11.9517 12.2552 11.69 12.3318 11.3999 12.3318H5.15938C4.77282 12.3318 4.44566 12.2006 4.17788 11.9383C3.90999 11.6759 3.77604 11.346 3.77604 10.9484V4.7561C3.77604 4.56277 3.81332 4.38049 3.88788 4.20927C3.96254 4.03804 4.06477 3.88754 4.19454 3.75777L7.28938 0.662932C7.5186 0.431043 7.79427 0.281321 8.11638 0.213765C8.43849 0.146321 8.71416 0.178988 8.94338 0.311765C9.22549 0.46421 9.40932 0.695932 9.49488 1.00693C9.58032 1.31793 9.58999 1.62804 9.52388 1.93727L9.09554 4.18027H13.4159ZM1.34404 12.3318C1.01393 12.3318 0.726767 12.2097 0.482544 11.9654C0.238322 11.7212 0.116211 11.434 0.116211 11.1039V5.40827C0.116211 5.07804 0.236989 4.79082 0.478544 4.5466C0.7201 4.30238 1.00466 4.18027 1.33221 4.18027H1.34804C1.67827 4.18027 1.96549 4.30238 2.20971 4.5466C2.45393 4.79082 2.57604 5.07804 2.57604 5.40827V11.1039C2.57604 11.434 2.45393 11.7212 2.20971 11.9654C1.96549 12.2097 1.67827 12.3318 1.34804 12.3318H1.34404Z" fill="currentColor"></path>
                                                </svg>
                                                145
                                            </span>
                                            <span class="sby-replies">
                                                2 Replies
                                                <svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.94 0.726654L4 3.77999L7.06 0.726654L8 1.66665L4 5.66665L0 1.66665L0.94 0.726654Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="sby-comment">
                                        <div class="sby-comment-profile-pic">
                                            <img src="<?php echo CUSTOMIZER_PLUGIN_URL . 'assets/img/profile_pic_5.png'; ?>">
                                        </div>
                                        <div class="sby-comment-heading">
                                            <span class="sby-comment-user-name">@jordan_brown</span>
                                            <span>4 Months ago</span>
                                        </div>
                                        <div class="sby-comment-text">
                                            <p>Incredible video! I'm curious, what inspired you to choose this topic? It’s not something you see every day and it was very engaging!</p>
                                        </div>
                                        <div class="sby-comment-bottom">
                                            <span class="sby-comment-likes">
                                                <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.4159 4.18027C13.761 4.18027 14.0778 4.32177 14.3664 4.60477C14.6549 4.88777 14.7992 5.20738 14.7992 5.5636V6.2706C14.7992 6.36471 14.7902 6.45188 14.7722 6.5321C14.7542 6.61232 14.7272 6.69266 14.6912 6.7731L12.684 11.4908C12.5845 11.7449 12.4181 11.9486 12.1849 12.1019C11.9517 12.2552 11.69 12.3318 11.3999 12.3318H5.15938C4.77282 12.3318 4.44566 12.2006 4.17788 11.9383C3.90999 11.6759 3.77604 11.346 3.77604 10.9484V4.7561C3.77604 4.56277 3.81332 4.38049 3.88788 4.20927C3.96254 4.03804 4.06477 3.88754 4.19454 3.75777L7.28938 0.662932C7.5186 0.431043 7.79427 0.281321 8.11638 0.213765C8.43849 0.146321 8.71416 0.178988 8.94338 0.311765C9.22549 0.46421 9.40932 0.695932 9.49488 1.00693C9.58032 1.31793 9.58999 1.62804 9.52388 1.93727L9.09554 4.18027H13.4159ZM1.34404 12.3318C1.01393 12.3318 0.726767 12.2097 0.482544 11.9654C0.238322 11.7212 0.116211 11.434 0.116211 11.1039V5.40827C0.116211 5.07804 0.236989 4.79082 0.478544 4.5466C0.7201 4.30238 1.00466 4.18027 1.33221 4.18027H1.34804C1.67827 4.18027 1.96549 4.30238 2.20971 4.5466C2.45393 4.79082 2.57604 5.07804 2.57604 5.40827V11.1039C2.57604 11.434 2.45393 11.7212 2.20971 11.9654C1.96549 12.2097 1.67827 12.3318 1.34804 12.3318H1.34404Z" fill="currentColor"></path>
                                                </svg>
                                                130
                                            </span>
                                            <span class="sby-replies">
                                                3 Replies
                                                <svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.94 0.726654L4 3.77999L7.06 0.726654L8 1.66665L4 5.66665L0 1.66665L0.94 0.726654Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <span href="#" class="sby-view-all-button ">View all comments on YouTube</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>