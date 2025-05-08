<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MySoapServer extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->model(''); //load your models here

        $this->load->library("Nusoap_lib"); //load the library here
		$this->nusoap_server = new soap_server();
		$this->nusoap_server->configureWSDL("MySoapServer", "urn:MySoapServer");
		
		$this->nusoap_server->wsdl->addComplexType(
			'intArray',
			'complexType',
			'array',
			'',
			'SOAP-ENC:Array',
			array(),
			array(
				array(
					'ref' => 'SOAP-ENC:arrayType',
					'wsdl:arrayType' => 'xsd:integer[]'
				)
			),
			'xsd:integer'
		);
		/* $this->nusoap_server->wsdl->addComplexType(
			"jual",
			"complexType",
			"struct",
			"all",
			"",
			array(
				"kota" => array("name" => "kota", "type" => "xsd:string"),
				"terjual" => array("name" => "terjual", "type" => "xsd:integer")
			)
		);
		 
		$this->nusoap_server->wsdl->addComplexType(
			"jualArray",
			"complexType",
			"array",
			"",
			"SOAP-ENC:Array",
			array(),
			array(
				array(
					"ref"=>"SOAP-ENC:arrayType",
					"wsdl:arrayType"=>"xsd:jual"
				)
			),
			"tns:jual"
		); */
		
		$this->nusoap_server->register(
			"echoTest",
			array("tmp" => "xsd:string"),
			array("return" => "xsd:string"),
			"urn:MySoapServer",
			"urn:MySoapServer#echoTest",
			"rpc",
			"encoded",
			"Echo test"
        );
		
		$this->nusoap_server->register(
			"penjualan",
			array("kota" => "xsd:string"),
			array("jual" => "tns:string"),
			"urn:MySoapServer",
			"urn:MySoapServer#penjualan",
			"rpc",
			"encoded",
			"Belajar 1"
        );
		
		$this->nusoap_server->register(
			"penjualan_per_kota",
			array(), // input parameters
			array("return" => "tns:jualArray"),
			"urn:MySoapServer", // namespace
			"urn:MySoapServer#penjualan_per_kota", // soapaction
			"rpc", // style
			"encoded", // use
			"Fetch array of address book contacts for use in autocomplete"
		); // documentation
		
		$this->nusoap_server->register(
			"intCount",
			array("from" => "xsd:integer", "to" => "xsd:integer"),
			array("return" => "tns:intArray"),
			"urn:MySoapServer",
			"urn:MySoapServer#penjualan",
			"rpc",
			"encoded",
			"Belajar 3"
		);

        /**
         * To test whether SOAP server/client is working properly
         * Just echos the input parameter
         * @param string $tmp anything as input parameter
         * @return string returns the input parameter
         */
        function echoTest($tmp) {
            if (!$tmp) {
                return new soap_fault("-1", "Server", "Parameters missing for echoTest().", "Please refer documentation.");
            } else {
                return "from MySoapServer() : $tmp";
            }
        }

        /**
         * To test whether SOAP server/client is working properly
         * Just echos the input parameter
         * @param string $tmp anything as input parameter
         * @return string returns the input parameter
         */
        function penjualan($kota) {
			//$arr = array("negara"=>"Indonesia","ibukota"=>"Jakarta");
			$penjualan["Jakarta"] = 50;
			/* $arr[] = array("negara"=>"Indonesia","ibukota"=>"Jakarta");
			$arr[] = array("negara"=>"Malaysia","ibukota"=>"Kuala Lumpur");
			$arr[] = array("negara"=>"Thailand","ibukota"=>"Bangkok"); */

            if (empty($kota)) {
                return new soap_fault("-1", "Server", "Parameters missing for penjualan().", "Please refer documentation.");
            } else {
                return $penjualan[$kota];
            }
        }
		
		function penjualan_per_kota() {
			//$arr = array("negara"=>"Indonesia","ibukota"=>"Jakarta");
			$penjualan=array();
			$penjualan[]=array("kota"=>"Jakarta","terjual"=>50);
			$penjualan[]=array("kota"=>"Bandung","terjual"=>30);
			//array_push($penjualan,array("kota"=>"Surabaya","jual"=>80));

			return $penjualan;
        }
		
		function intCount($from, $to) {
			$out = array();
			for ($i = $from; $i <= $to; $i++) {
				$out[] = $i;
			}
			return $out;
		}
		
    }

    function index() {
		//echo "asd";
        $this->nusoap_server->service(file_get_contents("php://input")); //shows the standard info about service
		
		
		/* $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : "";
		$this->nusoap_server->service($HTTP_RAW_POST_DATA); */
    }
}
