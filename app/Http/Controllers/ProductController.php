<?php

namespace App\Http\Controllers;

use App\Models\photoproduct;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $all_product = Product::all();

        $list_product = array();

        foreach ($all_product as $value) {

            $photo = photoproduct::where('id_product', $value->id)->get();
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

            $value->price = filter_var($value->price, FILTER_SANITIZE_NUMBER_INT);

            // $value = (object) array_merge( (array)$value, array( 'list_picture' => $list_picture ) );
            $value->list_picture = $list_picture;

            array_push($list_product, $value);
        }

        return response([
            'message' => 'Success Get All Product',
            'product' => $list_product
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

        $photo = photoproduct::where('id_product', $request->id)->get();

        $list_picture = array();

        foreach ($photo as $value) {
            if (file_exists(public_path('photoproduct/' . $value->path))) {
                $product_picture = $value->path;
                $photoURL = url('/photoproduct' . '/' . $product_picture);
                array_push($list_picture, ['id_picture' => $value->id, 'url' => $photoURL]);
            } else {
                $photo = photoproduct::find($value->id);
                $photo->delete();
            }
        }

        $product->list_picture = $list_picture;

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

            $photo = photoproduct::where('id_product', $request->id)->get();

            foreach ($photo as $value) {
                File::delete(public_path('photoproduct/' . $value->path));
            }

            photoproduct::where('id_product', $request->id)->delete();

            return response(['message' => 'Success deleted']);
        }
        return response([
            'message' => 'Only Admin can do this'
        ]);
    }

    public function download_productPicture(Request $request)
    {
        $file_name = photoproduct::where('id_product', $request->id_product)->get();

        $list_picture = array();

        foreach ($file_name as $value) {
            if (file_exists(public_path('photoproduct/' . $value->path))) {
                $product_picture = $value->path;
                $photoURL = url('/photoproduct' . '/' . $product_picture);
                array_push($list_picture, ['id_picture' => $value->id, 'url' => $photoURL]);
            } else {
                $photo = photoproduct::find($value->id);
                $photo->delete();
            }
        }

        return response([
            'message' => 'Success get all picture for this product',
            'list_picture' => $list_picture
        ]);
        // return response()->download(public_path('photoproduct/anggrek_pink.jpg'));
    }

    public function upload_productPicture(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 1) {

            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = $request->image->getClientOriginalName();

            $imageName = preg_replace('/\s+/', '_', $imageName);


            $i = true;
            $j = 0;
            while ($i == true) {
                $picture = photoproduct::where('path', $imageName)->count();
                if ($picture > 0) {
                    $j++;
                    $imageName = basename(
                        $request->image->getClientOriginalName(),
                        '.' . $request->image->getClientOriginalExtension()
                    )
                        . ' ' . ($picture + $j) . '.' . $request->image->getClientOriginalExtension();

                    $imageName = preg_replace('/\s+/', '_', $imageName);
                } else {
                    $i = false;
                }
            }

            $request->image->move(public_path('photoproduct'), $imageName);

            $photo = photoproduct::create([
                'id_product' => $request->id_product,
                'path' => $imageName
            ]);

            $photo->save();

            $photoURL = url('/photoproduct' . '/' . $imageName);

            return response(['fileName' => $imageName, 'url' => $photoURL]);
        } else {
            return response(['message' => 'Only admins can do this']);
        }
    }

    public function delete_productPicture(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 1) {
            $photo = photoproduct::find($request->id_picture)->first();

            File::delete(public_path('photoproduct/' . $photo->path));

            $photo->delete();

            return response(['message' => 'Success deleting picture']);
        } else {
            return response(['message' => 'Only admins can do this']);
        }
    }

    // function string_between_two_string($str, $starting_word, $ending_word)
    // {
    //     $subtring_start = strpos($str, $starting_word);

    //     $subtring_start += strlen($starting_word);

    //     $size = strpos($str, $ending_word, $subtring_start) - $subtring_start;

    //     return substr($str, $subtring_start, $size);
    // }
}
