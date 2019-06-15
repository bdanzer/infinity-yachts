<?php 
namespace IYC;

final class API 
{
    private static $user_id = '128';
    private static $domain = 'infinityyachts.com';
    private static $apicode = '128Sx%$yerO9s3';

    public function __construct() {}

    public static function url_builder(string $url, array $query_data = []) 
    {
        if (empty($query_data)) {
            return $url;
        }

        return $url . '&' . build_query($query_data);
    }

    public static function get_xml_locations_url() 
    {
        return 'https://www.centralyachtagent.com/snapins/xmllocations2.php';
    }

    public static function get_xml_snyachts_url($query_data = []) 
    {
        $defaults = [
            'ylocations' => '', //can pass array of locations like src35=1, src36=1 for multiple
            'boattype' => '',
            'guests' => '',
            'startdate' => '',
            'enddate' => '',
            'yachtname' => '',
            'pricefrom' => '',
            'priceto' => '',
            'captainonly' => '',
            'sailinstructions' => '',
            'deckjacuzzi' => '',
            'helipad' => '',
            'scubadet' => ''
        ];

        return self::url_builder('https://www.centralyachtagent.com/snapins/snyachts-xml.php?user=' . self::$user_id, $query_data);
    }

    public static function get_xml_ebrochure_url($yacht_id = NULL) 
    {
        if (!$yacht_id) {
            $yacht_id = get_the_ID();
        }
        
        return 'https://www.centralyachtagent.com/snapins/ebrochure-xml.php?user=' . self::$user_id . '&idin=' . $yacht_id . '&act=' . self::$domain . '&apicode=' . self::$apicode . '';
    }

    public static function get_xml_carates_url($yacht_id = NULL) 
    {
        if (!$yacht_id) {
            $yacht_id = get_the_ID();
        }

        return 'https://www.centralyachtagent.com/snapins/carates-xml.php?idin='. $yacht_id . '&user=' . self::$user_id;
    }

    public static function get_xml_locations() 
    {
        if ($locations = get_transient('IYC_locations_transient')) {
            return $locations;
        }

        $locations = self::xml_array(self::get_xml_locations_url())['locations'];

        set_transient( 'IYC_locations_transient', $locations, 3600);

        return $locations;
    }

    public static function get_xml_snyachts($query_data = []) 
    {
        return self::xml_array(self::get_xml_snyachts_url($query_data));
    }

    public static function get_xml_ebrochure($yacht_id = NULL) 
    {
        return self::xml_array(self::get_xml_ebrochure_url($yacht_id));
    }

    public static function get_xml_carates($yacht_id = NULL)
    {
        return self::xml_array(self::get_xml_carates_url($yacht_id));
    }
    
    public static function xml_array($url)
    {
        $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xml_array = json_decode(json_encode((array) $xml), true);
        return $xml_array;
    }    
}