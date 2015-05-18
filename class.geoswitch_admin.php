<?php
if ( ! defined( 'ABSPATH' ) )
        die( 'This is just a Wordpress plugin.' );
 
if ( ! defined( 'GEOSWITCH_PLUGIN_DIR' ) )
    define( 'GEOSWITCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

class GeoSwitchAdmin {
    private static $initialized = false;
    
    public static function init() {
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;
        add_action('admin_menu', array('GeoSwitchAdmin', 'add_menu'));
        add_action('admin_init', array('GeoSwitchAdmin', 'admin_init'));
    }
    
    public static function admin_init() {    
        register_setting( 'geoswitch_options', 'geoswitch_options', array('GeoSwitchAdmin', 'validate') );
		
        add_settings_section('geoswitch_main', 'Geolocation Service Settings', array('GeoSwitchAdmin', 'data_source_section_text'), 'geoswitch_options_data_source_page');
	    add_settings_field('geoswitch_data_source', 'Select Geolocation Service', array('GeoSwitchAdmin', 'data_source'), 'geoswitch_options_data_source_page', 'geoswitch_main');
		
		add_settings_section('geoswitch_main', 'Local DataBase Settings', array('GeoSwitchAdmin', 'localdb_section_text'), 'geoswitch_options_localdb_page');
		add_settings_field('geoswitch_database_name', 'MaxMind Database Name', array('GeoSwitchAdmin', 'database_name'), 'geoswitch_options_localdb_page', 'geoswitch_main');
        
		add_settings_section('geoswitch_main', 'Web Service Settings', array('GeoSwitchAdmin', 'webservice_section_text'), 'geoswitch_options_webservice_page');
		add_settings_field('geoswitch_service_user_name', 'User ID', array('GeoSwitchAdmin', 'service_user_name'), 'geoswitch_options_webservice_page', 'geoswitch_main');
        add_settings_field('geoswitch_service_license_key', 'License key', array('GeoSwitchAdmin', 'service_license_key'), 'geoswitch_options_webservice_page', 'geoswitch_main');

		add_settings_section('geoswitch_main', 'Measurement Settings', array('GeoSwitchAdmin', 'measurement_section_text'), 'geoswitch_options_measurement_page');
        add_settings_field('geoswitch_units', 'Distance Units', array('GeoSwitchAdmin', 'units'), 'geoswitch_options_measurement_page', 'geoswitch_main');
        
		add_settings_section('geoswitch_main', 'Debug Settings', array('GeoSwitchAdmin', 'debug_section_text'), 'geoswitch_options_debug_page');
		add_settings_field('geoswitch_debug_ip', 'User IP', array('GeoSwitchAdmin', 'debug_ip'), 'geoswitch_options_debug_page', 'geoswitch_main');
    }

    public static function add_menu() {
        add_options_page('GeoSwitch Plugin Options Page', 'GeoSwitch', 'manage_options', __FILE__, array('GeoSwitchAdmin', 'display_menu'));
    }
    
    public static function display_menu() {
?>
<div>
<h2>GeoSwitch Plugin Options</h2>
<p>If you find this plugin useful why not</p>
<a href='http://ko-fi.com?i=8d1dbd20374ff5c' target='_blank'><img style='border:0px' src='http://ko-fi.com/img/button-1.png' border='0' alt='Buy Me A Coffee :) @ ko-fi.com' /></a>

<form method="post" action="options.php">
<?php 
    settings_fields('geoswitch_options');
    do_settings_sections('geoswitch_options_data_source_page'); 
    do_settings_sections('geoswitch_options_localdb_page'); 
    do_settings_sections('geoswitch_options_webservice_page'); 
    do_settings_sections('geoswitch_options_measurement_page'); 
	do_settings_sections('geoswitch_options_debug_page'); 
?>
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
<?php
    }

    public static function data_source_section_text() {
    }

    public static function localdb_section_text() {
    }

    public static function webservice_section_text() {
    }

    public static function measurement_section_text() {
    }

	public static function debug_section_text() {
?>
		These settings override the real settings for debugging purposes. Leave empty to disable.
<?php
    }
	
    public static function data_source() {
        $options =  GeoSwitch::get_options();
?>
<select id='geoswitch_data_source' name='geoswitch_options[data_source]'>
  <option value="localdb" <?=selected($options['data_source'], 'localdb', false)?>>Local Database</option>
  <option value="webservice" <?=selected($options['data_source'], 'webservice', false)?>>GeoIP2 Precision Service</option>
</select>

<?php
    }
    public static function database_name() {
        $options =  GeoSwitch::get_options();
        
?>
<input id='geoswitch_database_name' name='geoswitch_options[database_name]' size='64' type='text' value='<?= $options['database_name']?>' />

<?php
    }

    public static function service_user_name() {
        $options =  GeoSwitch::get_options();
?>
<input id='geoswitch_service_user_name', name='geoswitch_options[service_user_name]' size='64' type='text' value='<?= $options['service_user_name']?>' />


<?php
    }

    public static function service_license_key() {
        $options =  GeoSwitch::get_options();
?>
<input id='geoswitch_service_license_key', name='geoswitch_options[service_license_key]' size='64' type='text' value='<?= $options['service_license_key']?>' />


<?php
    }

    public static function units() {
        $options =  GeoSwitch::get_options();
?>
<select id='geoswitch_units' name='geoswitch_options[units]'>
  <option value="km" <?=selected($options['units'], 'km', false)?>>Kilometers</option>
  <option value="m" <?=selected($options['units'], 'm', false)?>>Miles</option>
</select>
<?php
    }
	
	public static function debug_ip() {
        $options =  GeoSwitch::get_options();
        
?>
<input id='geoswitch_debug_ip' name='geoswitch_options[debug_ip]' size='64' type='text' value='<?= $options['debug_ip']?>' />

<?php
    }

    public static function validate($input)
    {
        if (isset($input['database_name'])) {
            $newinput['database_name'] = trim($input['database_name']);
            if(!preg_match('/^[a-z0-9._]+/i', $newinput['database_name'])) {
                $newinput['database_name'] = 'GeoLite2-City.mmdb';
            }
        } else {
            $newinput['database_name'] = 'GeoLite2-City.mmdb';
        }

        if (isset($input['data_source'])) {
            $newinput['data_source'] = ($input['data_source'] == 'localdb' ? 'localdb' : 'webservice');
        } else {
            $newinput['units'] = 'localdb';
        }

        if (isset($input['units'])) {
            $newinput['units'] = ($input['units'] == 'm' ? 'm' : 'km');

        } else {
            $newinput['units'] = 'km';
        }
        if (isset($input['service_user_name'])){
            $newinput['service_user_name'] = trim($input['service_user_name']);
        }

        if (isset($input['service_license_key'])){
            $newinput['service_license_key'] = trim($input['service_license_key']);
        }
		
		 if (isset($input['debug_ip'])) {
            $newinput['debug_ip'] = trim($input['debug_ip']);
            if(!preg_match('/^[a-z0-9.:]+/i', $newinput['debug_ip'])) {
                $newinput['debug_ip'] = '';
            }
        } else {
            $newinput['debug_ip'] = '';
        }

        return $newinput;
    }
}