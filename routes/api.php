<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\TransactionController;
// use App\Http\Controllers\OrderController;
// use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::get('users', [AuthController::class, 'get_all_user']);
    Route::get('user/id', [AuthController::class, 'get_user_by_id']);
    
    Route::get('user/downloadPhoto', [AuthController::class, 'download_profilePicture']);
    Route::get('user/uploadPhoto', [AuthController::class, 'upload_profilePicture']);
    
    Route::get('checkout', [CheckoutController::class, 'checkout']);
    Route::get('province', [CheckoutController::class, 'get_province']);
    Route::get('city', [CheckoutController::class, 'get_city']);
    Route::get('ongkir', [CheckoutController::class, 'get_ongkir']);
    
    Route::resource('product', ProductController::class);
    Route::post('prod/uploadPhoto', [ProductController::class, 'upload_productPicture']);
    Route::get('prod/downloadPhoto', [ProductController::class, 'download_productPicture']);
    Route::delete('prod/deletePhoto', [ProductController::class, 'delete_productPicture']);

    
    Route::post('cart/store', [CartController::class, 'store_cart']);
    Route::get('cart/delete', [CartController::class, 'delete_cart']);
    Route::get('carts', [CartController::class, 'cart']);
    
    Route::delete('catalog', [CatalogController::class, 'delete_catalog']);
    Route::post('catalog', [CatalogController::class, 'store_catalog']);
    // Route::put('product', [ProductController::class, 'edit']);
    // Route::delete('product', [ProductController::class, 'delete']);
    // Route::post('product', [ProductController::class, 'store_product']);
    // Route::post('orders', [OrderController::class, 'create']);
    // Route::resource('orders', OrderController::class)->only(['show']);
    
    Route::get('transaction', [TransactionController::class, 'snapPage']);
    Route::get('transaction/status', [TransactionController::class, 'status']);
    Route::get('transactions', [TransactionController::class, 'get_transaction']);
    Route::get('transaction/id', [TransactionController::class, 'get_transaction_by_id']);
    Route::get('transactions/all', [TransactionController::class, 'get_transaction_all']);
});

Route::get('prod/downloadPhoto', [ProductController::class, 'download_productPicture']);

Route::resource('product', ProductController::class)->only([ 'index', 'show']);

Route::get('catalogs', [CatalogController::class, 'get_catalog']);
Route::get('catalog/product', [CatalogController::class, 'catalog_product']);

// Route::get('product', [ProductController::class, 'show_by_id']);
// Route::get('products', [ProductController::class, 'show_all']);
