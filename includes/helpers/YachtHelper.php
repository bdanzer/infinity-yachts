<?php
namespace IYC\helpers;

class YachtHelper 
{
    public function __construct() 
    {

    }

    public function get_yacht_price($case) 
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
    
    public function get_yacht_length($case) 
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

    public function get_locations() 
    {
        $specific_locations = array(
            'src13' => 'alaska',
            'src28' => 'antarctica',
            'src29' => 'arctic',
            'src21' => 'australia',
            'src5' => 'bahamas',
            //IYC Location SRC50
            'src50' => 'british colombia',
            'src17' => 'california',
            'src34' => 'canary islands',
            'src7' => 'caribbean leewards',
            'src3' => 'caribbean virgin islands',
            'src8' => 'caribbean windwards',
            'src20' => 'central america',
            'src16' => 'croatia',
            'src32' => 'cuba',
            'src26' => 'dubai',
            'src10' => 'florida',
            'src30' => 'french polynesia',
            'src31' => 'galapagos',
            'src18' => 'great lakes',
            'src4' => 'greece',
            'src12' => 'indian ocean and se asia',
            //IYC Location SRC50
            'src51' => 'indonesia',
            'src19' => 'mexico',
            'src9' => 'new england',
            'src22' => 'new zealand',
            'src24' => 'northern europe',
            'src14' => 'pacific nw',
            'src25' => 'red sea',
            'src2' => 'south america',
            'src33' => 'south china sea',
            'src23' => 'south pacific',
            'src11' => 'turkey',
            'src27' => 'united arab emirates',
            'src15' => 'w. med - spain/balearics',
            'src1' => 'w. med -naples/sicily',
            'src6' => 'w. med -riviera/cors/sard.',
        );
    
        return $specific_locations;
    }

    public function format_shitty_cya_feed_locations() 
    {
        global $locations;
    
        $not_shitty_locations_array = [];
    
        foreach ($locations as $location) {
            $not_shitty_locations_array[$location['locationCode']] = strtolower($location['locationName']);
        }
    
        return $not_shitty_locations_array;
    }

    public function get_location_codes($summer_locations, $winter_locations) 
    {
        global $formatted_shitty_cya_feed_locations;
    
        $summer_locations = apply_filters('iyc_summer_locations', explode(", ", $summer_locations));
        $winter_locations = apply_filters('iyc_winter_locations', explode(", ", $winter_locations));
    
        $winter_locations = [];
    
        $locations = apply_filters('iyc_locations', array_unique(array_merge($summer_locations, $winter_locations)));
    
        foreach ($locations as $location) {
            $location_codes[] = array_search($location, $formatted_shitty_cya_feed_locations);
        }
    
        return apply_filters('iyc_location_codes', $location_codes);
    }

    public function get_pricing_options() 
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
    
    public function get_boat_types() 
    {
        $boat_types = array(
            'all' => 'All Types',
            'power' => 'Power Yacht',
            'motor' => 'Motor Yacht',
            'sail' => 'Sailing Yacht',
            'cat' => 'Sailing Catamaran',
        );
    
        return $boat_types;
    }
    
    public function get_boat_lengths() 
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