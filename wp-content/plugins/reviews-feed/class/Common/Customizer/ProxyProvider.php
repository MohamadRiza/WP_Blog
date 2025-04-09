<?php

namespace SmashBalloon\Reviews\Common\Customizer;

use SmashBalloon\Reviews\Common\SBR_Settings;


class ProxyProvider extends \Smashballoon\Customizer\V2\ProxyProvider{

    public function get_settings_class(){
       # if ( ! \SmashBalloon\Reviews\Common\Util::sbr_is_pro() ) {
            return new SBR_Settings( [], sbr_get_database_settings() );
        #}
       # return new SBR_Settings_Pro( [], sbr_get_database_settings() );
    }

    public function get_db_settings(){
        return sbr_get_database_settings();
    }
}