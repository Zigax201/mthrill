<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
// Route::resource('product', ProductController::class, ['only' => 'index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::get('checkout', [CheckoutController::class, 'checkout']);
    Route::get('province', [CheckoutController::class, 'get_province']);
    Route::get('city', [CheckoutController::class, 'get_city']);
    Route::get('ongkir', [CheckoutController::class, 'get_ongkir']);
    
    // Route::resource('product', ProductController::class);
    Route::put('productedit', [ProductController::class, 'edit']);
    Route::delete('productdelete', [ProductController::class, 'delete']);
    Route::post('product', [ProductController::class, 'store_product']);
    Route::get('product', [ProductController::class, 'show_by_id']);
    Route::get('products', [ProductController::class, 'show_all']);
});


// Route::post('orders', [OrderController::class, 'create']);
// Route::resource('orders', OrderController::class)->only(['index', 'show']);