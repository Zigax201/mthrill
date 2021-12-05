<?php

namespace App\Http\Controllers;

use App\Models\photoproduct;
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
                'name' => $request->name,
                'desc' => $request->desc,
                'price' => $request->price,
                'tinggi' => $request->tinggi,
                'berat' => $request->berat,
                'warna' => $request->warna,
                'jenis' => $request->jenis,
                'catalog' => $request->catalog
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
        $product = Product::where('id', $request->id)->first();

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

    public function download_productPicture(Request $request)
    {
        $file_name = photoproduct::where('id_product', $request->id_product)->all();
        $list_picture = array();
        foreach ($file_name as $value) {
            $product_picture = $value->path;
            $photoURL = url('/photoproduct' . '/' . $product_picture);
            array_push($list_picture, $photoURL);
        }

        return response([
            'message' => 'Success get all picture for this product',
            'list_picture' => $list_picture
        ]);
        // return response()->download(public_path('photoproduct/'.$file_name->path), "Product Image");
    }

    public function upload_productPicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('photoproduct'), $imageName);

        $photo = photoproduct::create([
            'id_product' => $request->id_product,
            'path' => $imageName
        ]);

        $photo->save();

        $photoURL = url('/photoproduct' . '/' . $imageName);

        return response(['fileName' => $imageName, 'url' => $photoURL]);
    }
}
