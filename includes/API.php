<?php 
namespace IYC;

final class API 
{
    private $user_id = '128';
    private $domain = 'infinityyachts.com';
    private $apicode = '128Sx%$yerO9s3';

    public function __construct() {}

    public function url_builder(string $url, array $data) 
    {
        return $url . '&' . build_query($data);
    }

    public function get_cya_locations() 
    {
        return xml_array('http://www.centralyachtagent.com/snapins/xmllocations2.php');
    }

    public function get_xml_snyachts_url() 
    {
        return 'http://www.centralyachtagent.com/snapins/snyachts-xml.php?user=' . $this->user_id . '';
    }

    public function get_xml_ebrochure_url($yacht_id = NULL) 
    {
        if ($yacht_id === NULL) {
            $id = get_the_ID();
        }
        
        return 'https://www.centralyachtagent.com/snapins/ebrochure-xml.php?user=' . $this->user_id . '&idin=' . $yacht_id . '&act=' . $this->domain . '&apicode=' . $this->apicode . '';
    }
    
    public function xml_array($url, $yacht_id = NULL) 
    {
        $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xml_array = json_decode(json_encode((array) $xml), true);
        return $xml_array;
    }    
}