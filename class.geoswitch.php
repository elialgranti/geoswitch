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
    private static $data_source = null;
    private static $useKm = true;

	public static function init() {
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;

        self::$user_ip = self::get_user_ip();

        try {
            $opt = get_option('geoswitch_options');
            $useKM = ($opt['units'] == 'km');
            self::$data_source = request_record($opts);
            self::$record = $data_source->city(self::$user_ip);
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
        add_shortcode('geoswitch_latitude', array( 'GeoSwitch', 'get_latitude' ));
        add_shortcode('geoswitch_longitude', array( 'GeoSwitch', 'get_longitude' ));
    }

    public static function request_record($opts){
        $data_source = (is_null($opts['data_source'])) ? 'localdb' : $opts['data_source'];
        return ($data_source == 'webservice') ? build_client($opts) : build_reader($opts);
    }

    public static function build_client($opts){
        return new GeoIp2\WebService\Client($opts['service_user_name'], $opts['service_license_key']);
    }

    public static function build_reader($opts){
        $database = GEOSWITCH_PLUGIN_DIR . 'database/' . $opts['database_name'];
        return new GeoIp2\Database\Reader($database);
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
                !empty($atts['country']) ||
                !empty($atts['country_code']) ||
                !empty($atts['within']) ||
                !empty($atts['from'])) {
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
            (empty($atts['country_code']) || strcasecmp($atts['country_code'], self::$record->country->isoCode) == 0)
            &&
            (empty($atts['within']) || self::within($atts['within'], $atts['from']))) {
            return '#'.strlen($expandedContent).'#'.$expandedContent;
        }
        return '';
    }

    public static function get_ip($atts, $content) {
        return self::$user_ip;
    }

    public static function get_city($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->city->name;
    }

    public static function get_state($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->mostSpecificSubdivision->name;
    }

    public static function get_state_code($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->mostSpecificSubdivision->isoCode;
    }

    public static function get_country($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->country->name;
    }

    public static function get_country_code($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->country->isoCode;
    }

    public static function get_latitude($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->location->latitude;
    }

    public static function get_longitude($atts, $content) {
        if (is_null(self::$record)) {
            return '?';
        }
        return self::$record->location->longitude;
    }


    public static function activation() {
        $default_options=array(
            'database_name'=>'GeoLite2-City.mmdb',
            'units'=>'km'
         );
        add_option('geoswitch_options',$default_options);
    }

    public static function deactivation() {
        unregister_setting('geoswitch_options', 'geoswitch_options');
        delete_option('geoswitch_options');
    }

    private static function within($within, $from) {
        $within = 0.0 + $within;
        $from = explode(',', $from, 2);

        $pi80 = M_PI / 180;
        $lat1 = ($from[0] + 0.0) * $pi80;
        $lng1 = ($from[1] + 0.0) * $pi80;
        $lat2 = self::$record->location->latitude * $pi80;
        $lng2 = self::$record->location->longitude * $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        return self::$useKm ? ($km <= $within) : (($km * 0.621371192) <= $within);
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
