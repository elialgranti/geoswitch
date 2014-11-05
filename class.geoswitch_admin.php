<?php
if ( ! defined( 'ABSPATH' ) )
        die( 'This is just a Wordpress plugin.' );
 
if ( ! defined( 'GEOSWITCH_PLUGIN_DIR' ) )
    define( 'GEOSWITCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

class GeoSwitchAdmin {
    private static $initialized = false;
    private static $user_ip = null;
    private static $record = null;
	
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
        add_settings_section('geoswitch_main', 'General Settings', array('GeoSwitchAdmin', 'main_section_text'), 'geoswitch_options_main_page');
        add_settings_field('geoswitch_database_name', 'MaxMind Database Name', array('GeoSwitchAdmin', 'database_name'), 'geoswitch_options_main_page', 'geoswitch_main');
    }

    public static function add_menu() {
        add_options_page('GeoSwitch Plugin Options Page', 'GeoSwitch', 'manage_options', __FILE__, array('GeoSwitchAdmin', 'display_menu'));
    }
    
    public static function display_menu() {
?>
<div>
<h2>GeoSwitch Plugin Options</h2>
<p>If you find this plugin useful consider a donation.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="C7QAD2M3L5T6E">
<input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
</form>

<form method="post" action="options.php">
<?php settings_fields('geoswitch_options'); ?>
<?php do_settings_sections('geoswitch_options_main_page'); ?>
 
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
<?php
    }

    public static function main_section_text() {
    }
    
    public static function database_name() {
        $options = get_option('geoswitch_options');
?>
<input id='geoswitch_database_name' name='geoswitch_options[database_name]' size='100' type='text' value='<?= $options['database_name']?>' />
<?php
    }

    public static function validate($input)
    {
        $newinput['database_name'] = trim($input['database_name']);
        if(!preg_match('/^[a-z0-9._]+/i', $newinput['text_string'])) {
            $newinput['text_string'] = 'GeoLite2-City.mmdb';
        }
        return $newinput;
    }
}