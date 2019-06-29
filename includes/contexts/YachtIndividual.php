<?php
namespace IYC\contexts;

use IYC\API;
use IYC\contexts\IYC_Context;
use Timber\Timber;

class YachtIndividual 
{
    public $context;

    public function __construct($context) 
    {
        $this->context = $context;
        $this->yacht_id = $yacht_id = get_post_meta(get_the_ID(), 'yacht_id', true);
        //echo '<h2 class="">' . get_the_title() . '</h2>';
        $this->xml_ebrochure_array = ($yacht_id) ? API::get_xml_ebrochure($yacht_id)['yacht'] : [];
    }

    public function get_yacht_layout($xml_ebrochure_array) 
    {
        if ( isset($xml_ebrochure_array['yachtLayout']) && $xml_ebrochure_array['yachtLayout'] != '') {
            $yacht_layout = $xml_ebrochure_array['yachtLayout'];
        } elseif (get_field('yacht_layout')) {
            $yacht_layout = get_field('yacht_layout');
        }

        return $yacht_layout;
    }

    public function get_crew_info($xml_ebrochure_array) 
    {
        if (get_field('crew_description')) {
            $crew_profile = get_field('crew_description');
        } else {
            $crew_profile = (isset($xml_ebrochure_array['yachtCrewProfile'])) ? $xml_ebrochure_array['yachtCrewProfile'] : '';
        }

        if (get_field('crew_photo')) {
            $crew_photo = get_field('crew_photo');
        } else {
            $crew_photo = (isset($xml_ebrochure_array['yachtCrewPhoto']) && $xml_ebrochure_array['yachtCrewPhoto'] != '' && !is_array($xml_ebrochure_array['yachtCrewPhoto'])) ? $xml_ebrochure_array['yachtCrewPhoto'] : false;
        }

        return [
            'crew_photo' => $crew_photo,
            'crew_profile' => $crew_profile
        ];
    }

    public function get_units($xml_ebrochure_array) 
    {
        $units = get_field('units');
                                    
        //Check units
        if (isset($xml_ebrochure_array['yachtUnits']) && $xml_ebrochure_array['yachtUnits'] == 'Metres' || $units == 'Metres') {
            $unit = 'm';
        } else {
            $unit = 'ft';
        }

        return $unit;
    }

    public function get_images($xml_ebrochure_array) 
    {
        $images = get_field('iyc_gallery');

        if (!$images && !empty($xml_ebrochure_array)) {
            $name = 'yachtPic';
            $name_large = 'Large';

            $images = [];

            foreach ($xml_ebrochure_array as $key => $value) {
                if((strpos($key, $name) !== FALSE && strpos($key, $name_large) !== FALSE && $value != '')) {
                    $images[]['sizes']['large'] = $value;
                }
            } 
        }

        return $images;
    }

    public function get_video_html() 
    {
        $video = '';
        if (get_field('iyc_video')) {
            $iframe = get_field('iyc_video');

            // use preg_match to find iframe src
            preg_match('/src="(.+?)"/', $iframe, $matches);
            $src = $matches[1];

            // add extra params to iframe src
            $params = array(
                'controls' => 1,
                'hd'       => 1,
                'autohide' => 1,
            );

            $new_src = add_query_arg($params, $src);

            $iframe = str_replace($src, $new_src, $iframe);

            // add extra attributes to iframe html
            $attributes = 'frameborder="0"';

            $iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);

            $iframe = str_replace('<iframe', ' ' . '<iframe class="danzerpress-ab-items"' , $iframe);


            // echo $iframe
            $video .= '<div class="danzerpress-rectangle">';
            $video .= $iframe;
            $video .= '</div><br>';
        }

        return $video;
    }

    public function price_details($xml_ebrochure_array, $yacht_id) {
        $html =  '';
        $html .= '<h4 class="yacht-title">Pricing Details</h4>';
        $url_rates = 'https://www.centralyachtagent.com/snapins/carates-xml.php?idin=' . $yacht_id . '&user=128';
        $rates_xml = simplexml_load_file($url_rates, 'SimpleXMLElement', LIBXML_NOCDATA);
    
        $rates_array = json_decode(json_encode($rates_xml), true);
    
        if (isset($rates_array['yacht']['season'][0])) {
            foreach ($rates_array['yacht']['season'] as $key => $rates_value) {
                $html .= '<span class="season-name">' . $rates_value['seasonName'] . '</span>';
                $html .= '<table style="max-width:400px;">';
                    
                    foreach ($rates_value as $key => $value) {
    
                        if (strpos($key, 'Pax') !== FALSE && $value !== '&#36;0') {
                            $html .= '
                            <tr>
                            <th>' . $key . '</th>
                            <td>' . $value . '</td>
                            </tr>
                            ';
                        }
    
                    }
        
                $html .= '</table>';
                $html .= '<br>';
            }
        } elseif (isset($xml_ebrochure_array['yachtPriceDetails']) && $xml_ebrochure_array['yachtPriceDetails'] != '') {
            $html .= '<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">';
            //take a look at this
            $price_details = nl2br($xml_ebrochure_array['yachtPriceDetails'][0]);
                $html .= '<p>' . $price_details . '</p>';
            $html .= '</div>';
        } elseif (get_field('price_from')) {
            $price = get_field('price_from');
            $nights_option = get_field('nights_option');
            $currency = get_field('currency_type');
    
            if ($currency == 'usd') {
                $sign = '$';
            } else {
                $sign = '€';
            }
    
            if ($nights_option == 6) {
                $nights = 6;
                $html .= '<table class="wow fadeIn" style="max-width: 400px;">';
                    while ($nights <= 10) {
                        $price_new = ($price / 7) * $nights;
                        $html .= '
                            <tr>
                            <th>' . $nights . ' Nights</th>
                            <td>' . $sign . ' ' . number_format($price_new) . '</td>
                            </tr>
                            ';
                        $nights++;
                    }
                $html .= '</table><br>';
            } else {
                $nights = 7;
                $html .= '<table class="wow fadeIn" style="max-width: 400px;">';
                    while ($nights <= 10) {
                        $price_new = ($price / 7) * $nights;
                        $html .= '
                            <tr>
                            <th>' . $nights . ' Nights</th>
                            <td>' . $sign . ' ' . number_format($price_new) . '</td>
                            </tr>
                            ';
                        $nights++;
                    }
                $html .= '</table><br>';
            }
        } else {
            $html .= '
            <div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">
                <p>Contact Us to get more details</p>
            </div>';
        }
    
        return $html;
    }

    public function context() 
    {
        $context = $this->context + [
            'post' => Timber::get_post(),
            'images' => $this->get_images($this->xml_ebrochure_array),
            'sidebar' => Timber::compile('parts/content-sidebar.twig'),
            'video' => $this->get_video_html(),
            'xml_ebrocure_array' => $this->xml_ebrochure_array,
            'boat_description' => get_field('boat_description'),
            'unit' => $this->get_units($this->xml_ebrochure_array),
            'crew_info' => $this->get_crew_info($this->xml_ebrochure_array),
            'yacht_locations' => get_post_meta(get_the_ID(), 'dp_metabox_ylocations', true),
            'price_details' => $this->price_details($this->xml_ebrochure_array, $this->yacht_id),
            'iyc' => new IYC_Context
        ];
        
        return $context;
    }
}