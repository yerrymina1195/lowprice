<?php

use App\Http\Controllers\Api\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/cancel', function () {
    return view('cancel');
});


// Route::get('/return_url', [PaymentCallbackController::class, 'handleCallback']);
Route::get('/return_url', [PaymentCallbackController::class, 'handleReturn']);

