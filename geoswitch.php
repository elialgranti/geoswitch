<?php
/*
Plugin Name: GeoSwitch
Plugin URI: 
Tags: geocode, geocode switch, geocode filter, geotag, geomarketing, geomarking, geolocation, geofilter, location, local marketing
Description: 
Version: 1.0.0
Author: elialgranti
*/

if ( ! defined( 'ABSPATH' ) )
        die( 'This is just a Wordpress plugin.' );
 
if ( ! defined( 'GEOSWITCH_PLUGIN_DIR' ) )
    define( 'GEOSWITCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( GEOSWITCH_PLUGIN_DIR . 'class.geoswitch.php' );

register_activation_hook( __FILE__, array( 'GeoSwitch', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'GeoSwitch', 'deactivation' ) );
add_action( 'init', array( 'GeoSwitch', 'init' ) );

if ( is_admin() ) {
	require_once( GEOSWITCH_PLUGIN_DIR . 'class.geoswitch_admin.php' );
	add_action( 'init', array( 'GeoSwitchAdmin', 'init' ) );
}

?>