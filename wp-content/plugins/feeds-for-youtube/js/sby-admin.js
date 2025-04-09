(function($) {
    jQuery(document).ready(function($){

    // Add class and open link in new tab for the try upgrade from Free menu
    $('.sby_get_pro').parent().attr({'class':'sby_get_pro_highlight', 'target':'_blank'});


    /**
     * Dismiss header notice for YouTube Feed Lite
     * 
     * @since 2.0
     */
    $(document).on('click', '#sbc-dismiss-header-notice', function() {
      $.ajax({
        url : sby_admin.ajaxUrl,
        type : 'post',
        data : {
        action : 'sby_dismiss_upgrade_notice',
        nonce: sby_admin.nonce
      },
      success : function(data) {
        if ( data.success ) {
          $('#sbc-notice-bar').slideUp();
        }
      },
      error : function(e)  {
        console.log(e);
      }
      });
    });

    /**
     * Remove the other plugin installer modal
     * 
     * @since 2.0
     */
     $('body').on('click', '.sby-ip-popup-cls', function(e){
        $('#sby-op-modals').remove();
    });

    /**
     * Single Video Upsell Modal
     * 
     * @since 2.3
     */
    $('.sby-single-videos-upsell').parent('a').on('click', function(e) {
        e.preventDefault();
        // remove the already opened modal
        $('#sby-single-videos-upsell-modal').remove();

        $('#wpbody-content').prepend('<div class="sbc-extensions-pp-ctn sb-fs-boss sbc-center-boss" id="sby-single-videos-upsell-modal"><i class="fa fa-spinner fa-spin sby-loader" aria-hidden="true"></i></div>');

        // send the ajax request to load plugin name and others data
        $.ajax({
          url: ajaxurl,
          type: 'post',
          data: {
            action: 'sby_single_videos_upsell_modal',
            nonce : sby_admin.nonce,
          },
          success: function (data) {
            if (data.success == true) {
                $('#sby-single-videos-upsell-modal').html(data.data);
                $('body').on('click', '#sby-single-videos-upsell-modal', function(e){
                  if (e.target !== this) return;
                  $('#sby-single-videos-upsell-modal').remove();
                });
                $('body').on('click', '.sbc-popup-cls', function(e){
                  $('#sby-single-videos-upsell-modal').remove();
                });
            }
          },
          error: function (e) {
            console.log(e);
          }
        });
    });

    /**
     * Open other plugin installer modal from admin sidebar menu
     * 
     * @since 2.0
     */
    $('.sby_get_cff, .sby_get_sbi, .sby_get_ctf, .sby_get_sbtt, .sby_get_sbr').parent('a').on('click', function(e) {
        e.preventDefault();
        // remove the already opened modal
        $('#sby-op-modals').remove();

        // prepend the modal wrapper
        $('#wpbody-content').prepend('<div class="sby-install-plugin-popup-outer sb-fs-boss" id="sby-op-modals"><i class="fa fa-spinner fa-spin sby-loader" aria-hidden="true"></i></div>');

        // determine the plugin name
        var $self = $(this).find('span'),
          sb_get_plugin = 'twitter';

        if ($self.hasClass('sby_get_cff')) {
          sb_get_plugin = 'facebook';
        }

        if ($self.hasClass('sby_get_sbi')) {
          sb_get_plugin = 'instagram';
        }

        if ($self.hasClass('sby_get_sby')) {
          sb_get_plugin = 'twitter';
        }

        if ($self.hasClass('sby_get_sbtt')) {
          sb_get_plugin = 'tiktok';
        }

        if ($self.hasClass('sby_get_sbr')) {
          sb_get_plugin = 'reviews';
        }

        // send the ajax request to load plugin name and others data
        $.ajax({
          url: ajaxurl,
          type: 'post',
          data: {
            action: 'sby_other_plugins_modal',
            plugin: sb_get_plugin,
            nonce : sby_admin.nonce,
          },
          success: function (data) {
            if (data.success == true) {
                $('#sby-op-modals').html(data.data);
                $('body').on('click', '#sby-op-modals', function(e){
                    if (e.target !== this) return;
                    $('#sby-op-modals').remove();
                  });
                  $('body').on('click', '.sby-fb-popup-cls', function(e){
                    $('#sby-op-modals').remove();
                  });
            }
          },
          error: function (e) {
            console.log(e);
          }
        });
    });

    /**
     * Install other plugins from the modal
     * 
     * @since 2.0
     */
    $(document).on('click', '#sby_install_op_plugin', function() {
        let self = $(this);
        let pluginAtts = self.data('plugin-atts');
        if ( pluginAtts.step == 'install' ) {
          pluginAtts.plugin = pluginAtts.download_plugin
        }
        let loader = '<span class="ctf-btn-spinner"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform></path></svg></span>';
        self.prepend(loader);
    
        // send the ajax request to install or activate the plugin
        $.ajax({
          url : ajaxurl,
          type : 'post',
          data : {
            action : pluginAtts.action,
            nonce : sby_admin.nonce,
            plugin : pluginAtts.plugin,
            download_plugin : pluginAtts.download_plugin,
            type : 'plugin',
          },
          success : function(data) {
            if ( data.success == true ) {
              self.find('.ctf-btn-spinner').remove();
              self.attr('disabled', 'disabled');
    
              if ( pluginAtts.step == 'install' ) {
                self.html( data.data.msg );
              } else {
                self.html( data.data );
              }
            }
          },
          error : function(e)  {
            console.log(e);
          }
        });
      });
});
})(jQuery)