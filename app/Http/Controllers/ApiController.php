<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Soap\SoapClientController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Session;

class ApiController extends Controller
{

	public function create_client(Request $request) {		
		$request->validate([
	        'document' => 'required|unique:clients',
	        'name' => 'required',
	        'email' => 'required|unique:clients',
	        'phone' => 'required'
	    ]);
        $data_client = $request->all();
        $soapClient = SoapClientController::soapClient();
        [$client, $wallet] = $soapClient->create_client($data_client);
		return response()->json(['data' => ['client' => $client->original, 'wallet' => $wallet->original], 'message' => 'Client has been created'], 200);
	}

    public function get_clients(Request $request) {
    	$soapClient = SoapClientController::soapClient();
    	$data = json_decode($soapClient->get_clients([]));
    	return response()->json(['data' => $data], 200);
    }

    public function get_client(Request $request, int $id) {
    	$soapClient = SoapClientController::soapClient();
    	$data = $soapClient->get_client(['id' => $id]);
    	return response()->json($data);
    }

    public function login(Request $request) {
        $request->validate([
            'document' => 'required',
            'email' => 'required|email'
        ]);
        $data = $request->all();
        $soapClient = SoapClientController::soapClient();
        $dataLogin = $soapClient->login($data);
        return $dataLogin;
    }

    public function recharge_balance(Request $request) {
    	$request->validate([
	        'document' => 'required',
	        'phone' => 'required',
	        'amount' => 'required|numeric|between:0.01,1000000.00'
	    ]);
	    $data = $request->all();
	    $soapClient = SoapClientController::soapClient();
	    $dataBalance = $soapClient->recharge_balance($data);
	    return response()->json($dataBalance);
    }

    public function get_payment(Request $request, $number) {
        $request->validate([
            'client_id' => 'required',
        ]);
        $data = ['client_id' => $request->client_id, 'number' => $number];
        $soapClient = SoapClientController::soapClient();
        $dataPayment = $soapClient->get_payment($data);
        return $dataPayment;
    }

    public function payment(Request $request) {
        $request->validate([
            'client_id' => 'required|numeric',
            'order_amount' => 'required|numeric',
            'order_description' => 'required'
        ]);        
        $data = $request->all();
        $soapClient = SoapClientController::soapClient();
        $soap_payment = $soapClient->payment($data);
        return response()->json($soap_payment);

    }

    public function confirm_payment(Request $request, $number) {
        $request->validate([
            'token' => 'required|min:6'
        ]);
        $soapClient = SoapClientController::soapClient();
        $data = $request->all();
        $data['number'] = $number;
        $confirm_data = $soapClient->confirm_payment($data);
        return $confirm_data;
    }

    public function get_client_historial(Request $request, $client) {
        $soapClient = SoapClientController::soapClient();
        $dataHistory = $soapClient->get_client_historial(['client_id' => $client]);
        return response()->json($dataHistory);
    }

    public function send_email(Request $request) {
    	$details = [
    		'title' => 'Correo Prueba',
    		'body' => 'Simple correo laravel'
    	];
    	Mail::to('bersnardc@gmail.com')->send(new TestMail($details));
    	return response()->json(['message' => 'Correo enviado']);
    }
}
