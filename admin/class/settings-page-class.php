<?php 

class xmlBuilder {

	private $xml = '';
	private $yacht_id;
	private $api_key = '&act=infinityyachts.com&apicode=128Sx%$yerO9s3';
	private $array = array();

    function __construct($url) {
    	$parameters = simplexml_load_file($url);
        foreach($parameters as $key => $value) {
            $this->array = $value;
        }
    }

    function get_xml() {
    	echo $this->xml;
    }

    function get_array() {
    	echo $this->array;
    }

}

class person {
		var $name;
		function __construct($persons_name) {		
			$this->name = $persons_name;		
		}		

	
 
	}	