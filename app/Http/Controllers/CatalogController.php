<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Product;
use App\Models\photoproduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function store_catalog(Request $request)
    {
        if (Auth::user()->role == 1) {
            $catalog = Catalog::create([
                'name' => $request->name_catalog
            ]);
        }

        return response(['message' => 'Success insert catalog', 'catalog' => $catalog]);
    }

    public function get_catalog()
    {
        $catalog = Catalog::all();

        return response(['message' => 'Success get catalog', 'catalog' => $catalog]);
    }

    public function catalog_product(Request $request)
    {
        $all_product = Product::where('catalog', $request->id_catalog)->get();

        // $list_product = array();

        // foreach ($catalog as $value) {
        //     $product = Product::find($value->id_product);
        //     $product->qty = Cart::find($value->id)->qty;
        //     array_push($list_product, $product);
        // }

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

            $value->price = (int)filter_var($value->price, FILTER_SANITIZE_NUMBER_INT);

            // $value = (object) array_merge( (array)$value, array( 'list_picture' => $list_picture ) );
            $value->list_picture = $list_picture;

            array_push($list_product, $value);
        }

        return response([
            'message' => 'Success get product in catalog ' . Catalog::find($request->id_catalog)->name,
            'product' => $list_product
        ]);
    }

    public function delete_catalog(Request $request)
    {
        $catalog = Catalog::find($request->id_catalog);
        $catalog->delete();
        return response([
            'message' => 'Success delete catalog'
        ]);
    }
}
