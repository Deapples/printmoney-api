<?php

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
//test connection route
Route::get('/test', function(){
    return response(['message'=> 'Connected to server Successfully',
'status' => 'ok'], 200);
});
//make payment route 
Route::match(['get', 'put'], '/paymoney', 'PayFundsController@makePayment');

//withdraw money route

Route::match(['get', 'put'], '/withdraw', 'WithdrawFundsController@withdrawPayment');