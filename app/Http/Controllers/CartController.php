<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function store_cart(Request $request)
    {
        $user = Auth::user();
        $product = Product::find($request->id_product);

        $cart = Cart::create([
            'id_user' => $user->id,
            'id_product' => $product->id,
            'qty' => $request->qty 
        ]);

        return response([
            'message' => 'Success input product',
            'cart' => $cart
        ]);
    }
    
    public function cart(){
        $user = Auth::user();
        $cart = Cart::where('id_user', $user->id)->get();
        
        $list_product=array();
        
        foreach ($cart as $value) {
            array_push($list_product,Product::where('id', $value->id_product));
          }

        return response([
            'message' => 'Success get cart',
            'cart' => $list_product
        ]);
    }

    public function delete_cart(Request $request)
    {
        $cart = Cart::where('id_product', $request->id_product);
        $cart->delete();
        return response(['message' => 'Success deleted']);
    }
}
