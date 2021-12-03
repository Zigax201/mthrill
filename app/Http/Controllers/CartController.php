<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function store_cart(Request $request)
    {
        if (
            Cart::where('id_product', $request->id_product)
            ->where('id_user', $request->id_user)->count() > 0
        ) {
            // $cart = Cart::where('id_product', $request->id_product)->first();
            // $cart->toQuery()->update([
            //     'qty' => ($cart->qty + $request->qty)
            // ]);
            $cart = Cart::where('id_product', $request->id_product)
                ->where('id_user', $request->id_user)->first();

            $new_qty = $cart->qty + $request->qty;

            Cart::where('id_product', $request->id_product)
                ->where('id_user', $request->id_user)
                ->update(['qty' => $new_qty]);
        } else {
            $cart = Cart::create([
                'id_user' => $request->id_user,
                'id_product' => $request->id_product,
                'qty' => $request->qty
            ]);
        }

        return response([
            'message' => 'Success input cart',
            'cart' => $cart
        ]);
    }

    public function cart(Request $request)
    {
        $cart = Cart::where('id_user', $request->id_user)->get();

        $list_product = array();

        foreach ($cart as $value) {
            $product = Product::find($value->id_product);
            $product->qty = Cart::find($value->id)->qty;
            array_push($list_product, $product);
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
        return response(['message' => 'Success deleted cart']);
    }
}
