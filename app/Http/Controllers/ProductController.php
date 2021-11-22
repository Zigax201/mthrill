<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{


    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->input('name'),
            'desc' => $request->input('desc'),
            'price' => $request->input('price'),
            'catalog' => $request->input('catalog')
        ]);

        return response([
            'message' => 'Success input product',
            'product' => $product
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::where('id', $id)->get();

        return response([
            'message' => 'Success Get Product ' . $product->name,
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)->get();

        $product->toQuery()->update([
            'name' => $request->input('name'),
            'desc' => $request->input('desc'),
            'price' => $request->input('price'),
            'catalog' => $request->input('catalog')
        ]);

        if ($product) {
            return response([
                'message' => 'Success Edit',
                'product' => $product
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response(['message' => 'Success deleted']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::all();

        return response([
            'message' => 'Success Get All Product',
            'product' => $product
        ]);
    }
}
