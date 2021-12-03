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
        $file_name = photoproduct::find($request->id_product);
        return response()->download(public_path($file_name->path), "Product Image");
    }

    public function upload_productPicture(Request $request)
    {
        // $path = $request->file('photo')->move(public_path('/'), $request->file_name);
        // $photoURL = url('/' . $request->file_name);
        
        if ($request->hasFile('file')) {
            $destinationPath = public_path('/');
            $files = $request->file('file'); // will get all files
            
            foreach ($files as $file) {//this statement will loop through all files.
                $file_name = $file->getClientOriginalName(); //Get file original name
                $file->move($destinationPath , $file_name); // move files to destination folder
            }
            
            $photoURL = url('/' . $request->file_name);

            $photo = photoproduct::create([
                'id_product' => $request->id_product,
                'path' => $photoURL
            ]);
    
            return  response(['message' => 'Success upload image', 'photo' => $photo])->json(['url' => $photoURL], 200);
        }
         return response(['message'=> 'no file']);

    }
}
