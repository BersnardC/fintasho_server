<?php 
namespace App\Http\Controllers\Soap;

use App\Models\Client;
use App\Models\Wallet;
use App\Models\Payment;
use App\Mail\TestMail;
use App\Models\History_wallet;
use Illuminate\Support\Facades\Mail;

class SoapServerController {
	#http://localhost:8080/fintasho/public/soap?wsdl
	private $user = 'root';
	private $password = 'root';
	public function __construct()
	{
		
	}

	/*
		Método para autenticacion del soap
	*/
	public static function authenticate($headers_params) {
		if ($headers_params->username == $this::$user && $headers_params->password == $this::password) return true;
		else
			throw new \SoapFault("Wrong user/pass combination", 401);
			
	}

	public function get_clients($data) {
		$arr = Client::with('wallets')->get();
		return json_encode($arr);
	}
	public function get_client($data) {
		$arr = Client::with('wallets')->find($data['id']);
		if (!$arr)
			return ['status' => false, 'code' => 404, 'message' => 'Client not found'];
		$client = json_decode(json_encode($arr));
		return ['code' => 200, 'status' => true, 'client' => $client];
	}

	public function login($data) {
		$client = Client::where('document', $data['document'])
			->where('email', $data['email'])->with('wallets')->first();
		if(!$client)
			return ['status' => false, 'code' => 401, 'message' => 'Login failed'];
		$client_parse = json_decode(json_encode($client));
		return ['status' => true, 'client' => $client_parse, 'code' => 200];
	}

	public function create_client($data) {
		$client = new Client();
		$client->document = $data['document'];
		$client->name = $data['name'];
		$client->email = $data['email'];
		$client->phone = $data['phone'];
		$client->save();
		$wallet = new Wallet();
		$wallet->token = md5($client->id);
		$wallet->balance = 0;
		$wallet->client_id = $client->id;
		$wallet->save();
		return [$client, $wallet];
	}

	public function recharge_balance($data) {
		$client = Client::where('document', $data['document'])
			->where('phone', $data['phone'])->first();
		if (!$client)
			return ['status' => false, 'code' => 404, 'message' => 'Client not found'];
		$wallet = Wallet::where('client_id', $client->id)->first();
		if (!$wallet)
			return ['status' => false, 'code' => 404, 'message' => 'Wallet not found'];
		$wallet->balance += $data['amount'];
		$wallet->updated_at = date('Y-m-d H:i:s');
		$wallet->save();
		$nhistory = new History_wallet();
		$nhistory->action =  1;
		$nhistory->wallet_id = $wallet->id;
		$nhistory->amount = $data['amount'];
		$nhistory->save();
		return ['status' => true, 'code' => 201, 'message' => 'balance recharged succesfully', 'balance' => $wallet->balance];
	}

	public function get_payment($data) {
		$payment = Payment::where('client_id', $data['client_id'])
			->where('number', $data['number'])->first();
		if(!$payment)
			return ['status' => false, 'code' => 404, 'message' => 'Payment not found'];
		$parsePayment = json_decode(json_encode($payment));
		return ['status' => true, 'code' => 200, 'payment' => $parsePayment];
	}

	public function payment($data) {
		$client = Client::find($data['client_id']);
		if (!$client)
			return ['status' => false, 'code' => 404, 'message' => 'Client not found'];
		$wallet = Wallet::where('client_id', $client->id)->first();
		if (!$wallet)
			return ['status' => false, 'code' => 404, 'message' => 'Wallet not found'];
		if ($wallet->balance < $data['order_amount'])
			return ['status' => false, 'code' => 401, 'message' => 'Insufficient balance'];
		$token = rand(100000,999999);
		$number_payment = date('ymdhis') . md5($token);
		$payment = new Payment();
		$payment->number = $number_payment;
		$payment->token = $token;
		$payment->description = $data['order_description'];
		$payment->amount = $data['order_amount'];
		$payment->client_id = $client->id;
		$payment->status = 1;
		$payment->save();
		$details = [
    		'title' => 'Payment Confirm',
    		'body' => 'Hi ' . $client->name . ' your has generated a payment request number: '.$number_payment.'. Your token is: '.$token,
    		'url' => url('/confirm_payment/' . $number_payment)
    	];
    	Mail::to($client->email)->send(new TestMail($details));
		return ['status' => true, 'code' => 200, 'number' => $number_payment,'message' => 'Payment created, confirm the payment on email to complete the process'];
	}

	public function confirm_payment($data) {
		$payment = Payment::where('number', $data['number'])->first();
		if(!$payment)
			return ['status' => false, 'code' => 404, 'message' => 'Payment not found'];
		if ($payment->token != $data['token'])
			return ['status' => false, 'message' => 'Invalid payment token'];
		$wallet = Wallet::where('client_id', $payment->client_id)->first();
		if(!$wallet)
			return ['status' => false, 'code' => 404, 'message' => 'Wallet not found'];
		$wallet->balance -= $payment->amount;
		$wallet->save();
		$payment->status = 2;
		$payment->save();
		$nhistory = new History_wallet();
		$nhistory->action = 2;
		$nhistory->wallet_id = $wallet->id;
		$nhistory->amount = $payment->amount;
		$nhistory->save();
		return ['status' => true, 'code' => 200, 'message' => 'Payment complete succesfully'];
	}
	public function get_client_historial($data) {
		$wallet = Wallet::where('client_id', $data['client_id'])->first();
		if (!$wallet) {
			return ['status' => false, 'code' => 404, 'message' => 'Wallet not found'];
		} else {
			$history = History_wallet::where('wallet_id', $wallet->id)->get();
			$historyParse = json_decode(json_encode($history));
			return ['status' => true, 'code' => 200, 'history' => $historyParse];
		}

	}

}

?>