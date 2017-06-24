<?php
/*
Plugin Name: GeoSwitch
Plugin URI: https://wordpress.org/plugins/geoswitch/
Tags: geocode, geocode switch, geocode filter, geotag, geomarketing, geomarking, geolocation, geofilter, location, local marketing, GeoIP2, MaxMind
Description: GeoSwitch is a plugin that allows you to change the content of your site based on the location of your client’s IP.
Version: 1.1.3
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