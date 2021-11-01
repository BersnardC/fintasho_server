<?php 
	namespace App\Http\Controllers\Soap;

	class SoapClientController {
		
		public function __construct()
		{
			$params = array('location' => 'http://localhost:8080/fintasho/public/soap', 'uri' => 'urn://localhost:8080/fintasho/public/soap', 'trace' => 1);
			$this->instance = new \SoapClient(null, $params);

			# Set the Header
			$authParams = new \stdClass();
			$authParams->username = 'root';
			$authParams->password = 'root';
			$headers_params = new \SoapVar($authParams, SOAP_ENC_OBJECT);
			$header = new \SoapHeader('fintasho', 'authenticate', $headers_params, false);
			$this->instance->__setSoapHeaders(array($header));
		}

		public function cliente_soap2() {
			$cliente = new \SoapClient(null, array('location' => 'http://localhost:8080/fintasho/public/soap', 'uri' => 'urn://localhost:8080/fintasho/public/soap', 'trace' => 1));
	        $result = $cliente->getStudentName(['id' => 6]);
	        dd($result);
		}

		public static function soapClient() {
			$soapClient = $cliente = new \SoapClient(null, array('location' => 'http://localhost:8080/fintasho/public/soap', 'uri' => 'urn://localhost:8080/fintasho/public/soap', 'trace' => 1));
			return $soapClient;
		}
	}
 ?>