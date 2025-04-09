<script type="text/x-template" id="cff-dummy-lightbox">
    <div v-if="customizerScreens.activeSection == 'customize_lightbox'" class="cff-dummy-lightbox-container">
        <div class="cff-dummy-lightbox-modal">
            <div class="cff-dummy-lightbox-modal-content">
                <div class="cff-dlm-left">
                    <img src="<?php echo CFF_BUILDER_URL . 'assets/img/cff-dummy-lightbox-preview-img.png' ?>" alt="dummy lightbox preview">
                </div>
                <div class="cff-dlm-right">
                    <div class="cff-dlm-header-data">
                        <div class="sb-img">
                            <img :src="customizerFeedData.header.picture.data.url" alt="profile picture">
                        </div>
                        <div class="sb-dlm-header-title">
                            <h6>{{customizerFeedData.header.name}}</h6>
                            <span>1 week ago</span>
                        </div>
                    </div>
                    <div class="cff-dlm-content-text">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim <a href="#">veniam</a>, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea...</p>
                    </div>
                    <div class="cff-dlm-reactions-bar">
                        <img src="<?php echo CFF_BUILDER_URL . 'assets/img/cff-lightbox-reactions-bar.png' ?>" alt="dummy lightbox preview">
                    </div>
                </div>
            </div>
            <div class="cff-dlm-arrow-left">
                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26.9675 12.9675L24.5 10.5L14 21L24.5 31.5L26.9675 29.0325L18.9525 21L26.9675 12.9675Z" fill="white"/>
                </svg>
            </div>
            <div class="cff-dlm-arrow-right">
                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.4987 10.5L15.0312 12.9675L23.0462 21L15.0312 29.0325L17.4987 31.5L27.9987 21L17.4987 10.5Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>
</script>