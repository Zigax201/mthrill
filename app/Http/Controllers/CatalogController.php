<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function store_catalog(Request $request)
    {
        if(Auth::user()->role == 1){
            $catalog = Catalog::create([
                'name' => $request->name_catalog
            ]);
        }

        return response(['message'=>'Success insert catalog', 'catalog'=>$catalog]);
    }

    public function get_catalog()
    {
        $catalog = Catalog::all();

        return response(['message'=>'Success get catalog', 'catalog'=>$catalog]);
    }

    public function catalog_product(Request $request){
        $list_product = Catalog::where('catalog', $request->id_catalog)->get();

        return response([
            'message' => 'Success get cart',
            'cart' => $list_product
        ]);
    }
    
    public function delete_catalog(Request $request){
        $catalog = Catalog::find($request->id);
        $catalog->delete();
        return response([
            'message' => 'Success delete catalog'
        ]); 
    }
}
