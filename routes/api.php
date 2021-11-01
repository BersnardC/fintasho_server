<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/clients', [ApiController::class, 'create_client']);
Route::get('/clients', [ApiController::class, 'get_clients']);
Route::get('/clients/{int:id}', [ApiController::class, 'get_client']);
Route::post('/auth/login', [ApiController::class, 'login']);
Route::post('/wallet/balance', [ApiController::class, 'recharge_balance']);
Route::post('/payment', [ApiController::class, 'payment']);
Route::post('/payment/{any:number_payment}', [ApiController::class, 'get_payment']);
Route::post('/confirm_payment/{any:number_payment}', [ApiController::class, 'confirm_payment']);
Route::get('/clients/{int:id}/historial', [ApiController::class, 'get_client_historial']);
Route::get('/send_email', [ApiController::class, 'send_email']);
