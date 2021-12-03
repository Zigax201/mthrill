<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();

        return response([
            'message' => 'Success Get All Product',
            'product' => $product
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 1) {
            $product = Product::create([
                'name' => $request->input('name'),
                'desc' => $request->input('desc'),
                'price' => $request->input('price'),
                'tinggi' => $request->input('tinggi'),
                'berat' => $request->input('berat'),
                'warna' => $request->input('warna'),
                'jenis' => $request->input('jenis'),
                'catalog' => $request->input('catalog')
            ]);

            return response([
                'message' => 'Success input product',
                'product' => $product
            ]);
        }
        return response([
            'message' => 'Only Admin can do this'
        ]);
    }

    public function show(Request $request)
    {
        $product = Product::where('id', $request->id)->get();

        return response([
            'message' => 'Success Get Product',
            'product' => $product
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role == 1) {

            $product = Product::where('id', $request->id)->get();

            $product->toQuery()->update([
                'name' => $request->input('name'),
                'desc' => $request->input('desc'),
                'price' => $request->input('price'),
                'tinggi' => $request->input('tinggi'),
                'berat' => $request->input('berat'),
                'warna' => $request->input('warna'),
                'jenis' => $request->input('jenis'),
                'catalog' => $request->input('catalog')
            ]);

            if ($product) {
                return response([
                    'message' => 'Success Edit',
                    'product' => Product::where('id', $request->id)->get()
                ]);
            }
        }

        return response([
            'message' => 'Only Admin can do this'
        ]);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 1) {
            $product = Product::find($request->id);
            $product->delete();
            return response(['message' => 'Success deleted']);
        }
        return response([
            'message' => 'Only Admin can do this'
        ]);
    }

}
