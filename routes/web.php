<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MySoapController;
use App\Http\Controllers\Soap\SoapClientController;
use App\Http\Controllers\Soap\SoapServerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  	return view('welcome');
});

Route::any('/soap', function() {

	ini_set("soap.wsdl_cache_enabled", "0");
	$params = array('uri' => 'localhost:8080/fintasho/public/soap');
	$server = new SoapServer(null, $params);
	$server->setClass(SoapServerController::class);
	$server->handle();

});

Route::get('/sclient', function () {
  	$cclass = new SoapClientController();
  	$cclass->cliente_soap2();
});
