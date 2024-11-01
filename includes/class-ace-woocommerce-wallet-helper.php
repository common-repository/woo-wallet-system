<?php

/**
 * Helper class
*/

class Ace_Helper {
    
    static function pr( $arr, $die = false ){
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        if( $die ) wp_die();
    }

    static function acePluginBaseURL(){
        return plugins_url( '/', __DIR__ );
    }

    static function acePluginBasePath(){
        return plugin_dir_path( dirname( __FILE__ ) );
    }

}