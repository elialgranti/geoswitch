<?php
if ( ! defined( 'ABSPATH' ) )
        die( 'This is just a Wordpress plugin.' );
 
if ( ! defined( 'GEOSWITCH_PLUGIN_DIR' ) )
    define( 'GEOSWITCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once GEOSWITCH_PLUGIN_DIR . 'vendor/autoload.php';

class GeoSwitch {
    private static $initialized = false;
    private static $user_ip = null;
    private static $record = null;
	
	public static function init() {
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;
        
        self::$user_ip = self::get_user_ip();

        try {
            $opt = get_option('geoswitch_options');
            
            //require_once($gs_path . 'geoip2.phar');
    
            $database = GEOSWITCH_PLUGIN_DIR . 'database/' . $opt['database_name'];
            $reader = new GeoIp2\Database\Reader($database);

            self::$record = $reader->city('58.166.111.213'); //self::$user_ip);
        } catch (Exception $e) {
            self::$record = null;
        }

        add_shortcode('geoswitch', array( 'GeoSwitch', 'switch_block' ));
        add_shortcode('geoswitch_case', array( 'GeoSwitch', 'switch_case' ));
        
        add_shortcode('geoswitch_ip', array( 'GeoSwitch', 'get_ip' ));
        add_shortcode('geoswitch_city', array( 'GeoSwitch', 'get_city' ));
        add_shortcode('geoswitch_state', array( 'GeoSwitch', 'get_state' ));
        add_shortcode('geoswitch_state_code', array( 'GeoSwitch', 'get_state_code' ));
        add_shortcode('geoswitch_country', array( 'GeoSwitch', 'get_country' ));
        add_shortcode('geoswitch_country_code', array( 'GeoSwitch', 'get_country_code' ));
    }

	public static function switch_block($atts, $content) {
		$str = do_shortcode($content);
        $arr = explode('#', $str, 3);
        
        return count($arr) == 3  
            ? substr($arr[2], 0, intval($arr[1]))
            : '';
    }

	public static function switch_case($atts, $content) {
        $expandedContent = do_shortcode($content);
        
        if (is_null(self::$record)) {
            if (!empty($atts['city']) ||
                !empty($atts['state']) || 
                !empty($atts['state_code']) || 
                !empty($atts['country'] || 
                !empty($atts['country_code']))) {
                    return '';
            }
            return '#'.strlen($expandedContent).'#'.$expandedContent;
        }
        
        
        if ((empty($atts['city']) || strcasecmp($atts['city'], self::$record->city->name) == 0)
            &&
            (empty($atts['state']) || strcasecmp($atts['state'], self::$record->mostSpecificSubdivision->name) == 0)
            &&
            (empty($atts['state_code']) || strcasecmp($atts['state_code'], self::$record->mostSpecificSubdivision->isoCode) == 0)
            &&
            (empty($atts['country']) || strcasecmp($atts['country'], self::$record->country->name) == 0)
            &&
            (empty($atts['country_code']) || strcasecmp($atts['country_code'], self::$record->country->isoCode) == 0)) {
            return '#'.strlen($expandedContent).'#'.$expandedContent;
        }
        return '';
    }

    public static function get_ip($atts, $content) {
        return self::$user_ip;
    }

    public static function get_city($atts, $content) {
        if (is_null(self::$record)) {
            return '~';
        }
        return self::$record->city->name;
    }

    public static function get_state($atts, $content) {
        if (is_null(self::$record)) {
            return '~';
        }
        return self::$record->mostSpecificSubdivision->name;
    }

    public static function get_state_code($atts, $content) {
        if (is_null(self::$record)) {
            return '~';
        }
        return self::$record->mostSpecificSubdivision->isoCode;
    }
    
    public static function get_country($atts, $content) {
        if (is_null(self::$record)) {
            return '~';
        }
        return self::$record->country->name;
    }

    public static function get_country_code($atts, $content) {
        if (is_null(self::$record)) {
            return '~';
        }
        return self::$record->country->isoCode;
    }

    public static function activation() {
        $default_options=array(
            'database_name'=>'GeoLite2-City.mmdb'
         );
        add_option('geoswitch_options',$default_options);
    }
    
    public static function deactivation() {
        unregister_setting('geoswitch_options', 'geoswitch_options');
        delete_option('geoswitch_options');
    }
    
    private static function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
