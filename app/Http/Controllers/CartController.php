<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\photoproduct;
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

            $photo = photoproduct::where('id_product', $product->id)->get();
            $list_picture = array();

            foreach ($photo as $val) {
                if (file_exists(public_path('photoproduct/' . $val->path))) {
                    $product_picture = $val->path;
                    $photoURL = url('/photoproduct' . '/' . $product_picture);
                    array_push($list_picture, ['id_picture' => $val->id, 'url' => $photoURL]);
                } else {
                    $photo = photoproduct::find($val->id);
                    $photo->delete();
                }
            }

            $product->price = (int)$product->price;

            // $value = (object) array_merge( (array)$value, array( 'list_picture' => $list_picture ) );
            $product->list_picture = $list_picture;

            array_push($list_product, $product);
        }

        return response([
            'message' => 'Success get cart',
            'cart' => $list_product
        ]);
    }

    public function delete_cart(Request $request)
    {
        $cart = Cart::where('id_product', $request->id_product)->where('id_user', $request->id_user);
        $cart->delete();
        return response(['message' => 'Success deleted cart']);
    }
}
