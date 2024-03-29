<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceivedController;
use App\Http\Controllers\RequiredController;
use App\Http\Controllers\ProductController;
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

// Route::post('/',"hola");

Route::prefix('Received')->group(function(){
    Route::post('/c',[ReceivedController::class, 'required']);
});

Route::prefix('Required')->group(function(){
    Route::post('/c',[RequiredController::class, 'received']);
});

// Route::prefix('Product')->group(function(){
//     Route::get('/fam',[ProductController::class, 'familiarizacion']);
//     Route::get('/wor',[ProductController::class, 'wor']);
// });

Route::prefix('product')->group(function(){
    Route::get('/fam',[ProductController::class, 'familiarizacion']);
    Route::get('/wor',[ProductController::class, 'wor']);
    Route::get('/insart',[ProductController::class, 'productInsert']);
    Route::get('/celler',[ProductController::class, 'replyCeller']);
    Route::get('/minmax',[ProductController::class, 'minmax']);
    Route::get('/compareprices',[ProductController::class, 'compareprices']);
    


});