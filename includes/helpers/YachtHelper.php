<?php
namespace IYC\helpers;

use IYC\API;

class YachtHelper 
{
    public function __construct() {}

    public static function get_yacht_price($case) 
    {
        $price_arr = [];
        
        switch ($case) {
            case 0:
                $price_arr['price_from'] = 'all';
                $price_arr['price_to'] = 'all';
                break;
            case 1:
                $price_arr['price_from'] = 1;
                $price_arr['price_to'] = 9999;
                break;
            case 2:
                $price_arr['price_from'] = 10000;
                $price_arr['price_to'] = 19999;
                break;
            case 3:
                $price_arr['price_from'] = 20000;
                $price_arr['price_to'] = 29999;
                break;
            case 4:
                $price_arr['price_from'] = 30000;
                $price_arr['price_to'] = 49999;
                break;
            case 5:
                $price_arr['price_from'] = 50000;
                $price_arr['price_to'] = 100000;
                break;
            case 6:
                $price_arr['price_from'] = 100001;
                $price_arr['price_to'] = 999999;
                break;
        }
    
        return $price_arr;
    }
    
    public static function get_yacht_length($case) 
    {
        $length_arr = [];
        switch ($case) {
            case 0:
                $length_arr['length_from'] = 'all';
                $length_arr['length_to'] = 'all';
                break;
            case 1:
                $length_arr['length_from'] = 1;
                $length_arr['length_to'] = 49;
                break;
            case 2:
                $length_arr['length_from'] = 50;
                $length_arr['length_to'] = 59;
                break;
            case 3:
                $length_arr['length_from'] = 60;
                $length_arr['length_to'] = 79;
                break;
            case 4:
                $length_arr['length_from'] = 80;
                $length_arr['length_to'] = 99;
                break;
            case 5:
                $length_arr['length_from'] = 100;
                $length_arr['length_to'] = 119;
                break;  
            case 6:
                $length_arr['length_from'] = 120;
                $length_arr['length_to'] = 139;
                break;
            case 7:
                $length_arr['length_from'] = 140;
                $length_arr['length_to'] = 169;
                break;
            case 8:
                $length_arr['length_from'] = 170;
                $length_arr['length_to'] = 199;
                break;
            case 9:
                $length_arr['length_from'] = 200;
                $length_arr['length_to'] = 50000;
                break;    
        }
    
        return $length_arr;
    }

    public static function get_locations() 
    {
        $locations = (get_option('IYC_cya_locations')) ?: [];
        return $locations;
    }

    public static function format_shitty_cya_feed_locations() 
    {
        $locations = API::get_xml_locations();
    
        $not_shitty_locations_array = [];
    
        foreach ($locations as $location) {
            $not_shitty_locations_array[$location['locationCode']] = strtolower($location['locationName']);
        }

        return $not_shitty_locations_array;
    }

    public static function get_location_codes($summer_locations, $winter_locations) 
    {   
        $formatted_shitty_cya_feed_locations = self::format_shitty_cya_feed_locations();

        $summer_locations = (empty($summer_locations)) ? [] : apply_filters('iyc_summer_locations', explode(", ", $summer_locations));
        $winter_locations = (empty($winter_locations)) ? [] : apply_filters('iyc_winter_locations', explode(", ", $winter_locations));

        $locations = apply_filters('iyc_locations', array_unique(array_merge($summer_locations, $winter_locations)));

        foreach ($locations as $location) {
            $location_codes[] = array_search(strtolower($location), $formatted_shitty_cya_feed_locations);
        }
    
        return apply_filters('iyc_location_codes', $location_codes);
    }

    public static function get_pricing_options() 
    {
        $pricing_options = array(
            '0' => 'All',
            '1' => '$9,999 and less',
            '2' => '$10,000 - $19,999',
            '3' => '$20,000 - $29,999',
            '4' => '$30,000 - $49,999',
            '5' => '$50,000 - $99,999',
            '6' => '$100,000 and above'
        );
    
        return $pricing_options;
    }
    
    public static function get_boat_types()
    {
        $boat_types = array(
            'all'   => 'All Types',
            'power' => 'Power Yacht',
            'motor' => 'Motor Yacht',
            'sail'  => 'Sailing Yacht',
            'cat'   => 'Sailing Catamaran',
        );
    
        return $boat_types;
    }
    
    public static function get_boat_lengths() 
    {
        $boat_lengths = array(
            '0' => 'All Lengths',
            '1' => 'Under 50 feet',
            '2' => '50 to 59 feet',
            '3' => '60 - 79 feet',
            '4' => '80 - 99 feet',
            '5' => '100 - 119 feet',
            '6' => '120 - 139 feet',
            '7' => '140 - 169 feet',
            '8' => '170 - 199 feet',
            '9' => '200 feet and larger',
        );
    
        return $boat_lengths;
    }
}