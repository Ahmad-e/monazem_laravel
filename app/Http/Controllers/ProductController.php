<?php

namespace App\Http\Controllers;

use App\Models\Places;
use App\Models\Product_places;
use App\Models\Products_codes;
use App\Models\Products_moves;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products_types;
use App\Models\Products_units;
use App\Models\Product_favorites;
use App\Models\Products_prices;
use App\Models\Products;
use function Carbon\this;

class ProductController extends Controller
{
    public function showProduct_Types(){
        $creator = Auth::user();
        $data = Products_types::where('business_id',$creator->business_id)->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function showProductType($id){

        $data = Products_types::where('business_id',$id)
            ->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function addProductType(Request $request){
        $request->validate([
            'name' => 'required',
        ]);

        $creator = Auth::user();

        Products_types::create([
            'name' => $request->name,
            'business_id' => $creator->business_id,
            'branch_id' => $request->branch_id
        ]);

        return $this->showProductType($creator->business_id);
    }
    public function changeProductType(Request $request , $id){
        $productType = Products_types::find($id);

        if (!$productType) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products types this ID",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $productType->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products types not related to your business",
            ], 402);

        $productType->update($request->only([
            'name'
        ]));
        return $this->showProductType($productType->business_id);
    }

    public function toggleBlockProductType($id)
    {
        $user = Products_types::find($id);
        if(!$user){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no type id found",
            ], 404);
        }

        $creator = Auth::user();
        if($creator->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This type not related to your business",
            ], 402);

        $user->blocked_type = !$user->blocked_type;
        $user->save();

        return $this -> showProductType($id);
    }
    //******************************
    //   start products units
    //******************************

    public function showProduct_Unit(){
        $creator = Auth::user();
        $data = Products_units::where('business_id',$creator->business_id)->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function showProductUnit($id){

        $data = Products_units::where('business_id',$id)->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function addProductUnit(Request $request){
        $request->validate([
            'name' => 'required',
        ]);

        $creator = Auth::user();

        Products_units::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'group_ar' => $request->group_ar,
            'group_en' => $request->group_en,
            'conversion_factor' => $request->conversion_factor,
            'business_id' => $creator->business_id,
            'branch_id' => $request->branch_id
        ]);

        return $this->showProductUnit($creator->business_id);
    }
    public function changeProductUnit(Request $request , $id){
        $productType = Products_units::find($id);

        if (!$productType) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products unite this ID",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $productType->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products unit not related to your business",
            ], 402);

        $productType->update($request->only([
            'name',
            'symbol',
            'group_ar',
            'group_en',
            'conversion_factor',
        ]));
        return $this->showProductUnit($productType->business_id);
    }
    public function toggleBlockProductUnit($id)
    {
        $user = Products_types::find($id);
        if(!$user){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no type id found",
            ], 404);
        }

        $creator = Auth::user();
        if($creator->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This type not related to your business",
            ], 402);

        $user->blocked_type = !$user->blocked_type;
        $user->save();

        return $this -> showProductUnit($id);
    }

    //******************************
    //   start products
    //******************************


    public function showProductById($id){

        $data = Products::where('products.business_id', $id)
            ->with(['type', 'unite'])->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function showProduct(){
        $creator = Auth::user();
        return $this->showProductById($creator->business_id);
    }
    public function addProduct(Request $request){
        $request->validate([
            'name' => 'required',
            'categories' => 'required',
            'type_id' => 'required'
        ]);

        $creator = Auth::user();

        Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'categories' => $request->categories,
            'code' => $request->code,
            'img_url' => $request->img_url,
            'type_id' => $request->type_id,
            'unit_id' => $request->unit_id,
            'business_id' => $creator->business_id,
            'branch_id' => $request->branch_id,
            'creator_id' => $creator->id,
        ]);

        return $this->showProductById($creator->business_id);
    }
    public function changeProduct(Request $request,$id){

        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $product->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        $product->update($request->only([
            'name',
            'description',
            'categories',
            'code',
            'img_url',
            'type_id',
            'unit_id',
            'branch_id',
        ]));

        return $this->showProductById($user->business_id);
    }
    public function toggleBlockProduct($id){

        $product = Products::find($id);
        if(!$product){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no product id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $product->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This product not related to your business",
            ], 402);

        $product->blocked_product = !$product->blocked_product;
        $product->save();

        return $this->showProductById($user->business_id);
    }


    //******************************
    //   start products prices
    //******************************

    public function showPricesByID($id){
        $product = Products::find($id);
        $data = Products_prices::where('product_id',$id)
            ->with('currency')->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
            'product'=>$product
        ], 201);
    }

    public function showProductsPrices($id){
        $user = Auth::user();
        $product = Products::find($id);

        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);

        if($product->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        return $this->showPricesByID($product->id);
    }

    public function addProductsPrices(Request $request){
        $request->validate([
            'price' => 'required',
            'categories' => 'required',
            'product_id' => 'required',
            'currency_id' => 'required',
        ]);

        $product = Products::find($request->product_id);
        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);

        $creator = Auth::user();
        if($product->business_id != $creator->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);


        Products_prices::create([
            'price' => $request->price,
            'note' => $request->note,
            'categories' => $request->categories,
            'product_id' => $request->product_id,
            'partner_ar' => $request->partner_ar,
            'partner_en' => $request->partner_en,
            'unit_id' => $request->unit_id,
            'currency_id' => $request->currency_id,
            'creator_id' => $creator->id,
        ]);

        return $this->showPricesByID($request->product_id);
    }

    public function changeProductsPrices(Request $request , $id){
        $price = Products_prices::find($id);
        if(!$price)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Product price by this ID",
            ], 404);

        $creator = Auth::user();
        $product = Products::find($price->product_id);
        if($product->business_id != $creator->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        $price->update($request->only([
            'price',
            'note',
            'categories',
            'partner_ar',
            'partner_en',
            'currency_id',
        ]));
        return $this->showPricesByID($price->product_id);
    }

    //******************************
    //   start products codes
    //******************************

    public function showProductCodeByProductId($id){
        $data = Products::where('id',$id)
            ->with('codes')->get();

        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }
    public function showProductCode($id){
        $product = Products::find($id);
        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);

        $user = Auth::user();
        if($product->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        return $this->showProductCodeByProductId($id);
    }
    public function addProductCode(Request $request){
        $request->validate([
            'value' => 'required',
            'product_id' => 'required'
        ]);

        $product = Products::find($request->product_id);
        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);

        $user = Auth::user();
        if($product->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        Products_codes::create([
            'value' => $request->value,
            'date' => $request->date,
            'product_id' => $request->product_id,
            'creator_id' => $user->id,
        ]);

        return $this->showProductCodeByProductId($request->product_id);
    }
    public function changeProductCode(Request $request , $id){
        $code = Products_codes::find($id);
        if(!$code)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No code by this ID",
            ], 404);

        $product = Products::find($id);
        $user = Auth::user();
        if($product->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        $code->update($request->only([
            'value',
            'date',
        ]));

        return $this->showProductCodeByProductId($code->product_id);
    }

    //******************************
    //   start products favorite
    //******************************


    public function showProductFavoriteById($id){
        $data = Product_favorites::where('user_id',$id)
            ->join("products" , 'products.id' ,'product_favorites.product_id' )
            ->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function showProductFavorite(){
        $user = Auth::user();
        return $this->showProductFavoriteById($user->id);
    }

    public function addProductFavorite($id){
        $user = Auth::user();
        $product = Products::find($id);
        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);


        if($user->business_id != $product->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        $exists = Product_favorites::where('user_id',$user->id)->where('product_id',$product->id)->exists();

        if(!$exists)
            Product_favorites::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);

        return $this->showProductFavoriteById($user->id);
    }

    public function deleteProductFavorite($id){

        $product_favorite = Product_favorites::find($id);
        if(!$product_favorite)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No product favorite by this ID",
            ], 404);

        $user = Auth::user();

        if($user->id != $product_favorite->user_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your account",
            ], 402);

        $product_favorite->delete();

        return $this->showProductFavoriteById($user->id);
    }

    public function showProductMovesPlaces($id){
        $product = Products::find($id);

        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No product by this ID",
            ], 404);
        $user = Auth::user();

        if($user->business_id != $product->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your account",
            ], 402);


        $places = Product_places::where('product_id',$product->id)->get();
        $moves = Products_moves::where('product_id',$product->id)->get();
        return response()->json([
            'state' => 200,
            'product' => $product,
            'places' => $places,
            'moves' => $moves,
        ], 201);

    }
    public function AddMoveProduct(Request $request){
        $request->validate([
            'old_place_id' => 'required',
            'new_place_id' => 'required',
            'date' => 'required',
            'move_amount' => 'required',
            'currency_id' => 'required',
            'product_id' => 'required',
            'count' => 'required',
        ]);

        $product = Products::find($request->product_id);

        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No product by this ID",
            ], 404);
        $user = Auth::user();

        if($user->business_id != $product->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your account",
            ], 402);


        $old_place = Product_places::find($request->old_place_id);
//        if(!$old_place)
//            return response()->json([
//                'state' => 404,
//                'error' => 1,
//                'message' => "No old_place by this ID",
//            ], 404);

        $new_place = Product_places::find($request->new_place_id);
//        if(!$new_place)
//            return response()->json([
//                'state' => 404,
//                'error' => 1,
//                'message' => "No new_place by this ID",
//            ], 404);

        Products_moves::create([
            'name' => $request->name,
            'date'=> $request->date,
            'old_place_id' => $request->old_place_id,
            'new_place_id' => $request->new_place_id,
            'move_amount' => $request->move_amount,
            'currency_id' => $request->currency_id,
            'product_id' => $request->product_id,
            'count' => $request->count,
            'creator_id' => $user->id
        ]);


        $old_place->count = $old_place->count - $request->count ;

        $new_place->count = $new_place->count + $request->count ;

        $old_place->save();
        $new_place->save();

        return $this->showProductMovesPlaces($request->product_id);
    }
    public function addPlaceProduct(Request $request){
        $request->validate([
            'place_id' => 'required',
            'product_id' => 'required',
            'count' => 'required',
        ]);

        $product = Products::find($request->product_id);
        if(!$product)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No product by this ID",
            ], 404);
        $user = Auth::user();

        if($user->business_id != $product->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your account",
            ], 402);

        Product_places::create([
            'count' => $request->count,
            'place_id' => $request->place_id,
            'batches_id' => $request->batches_id,
            'product_id' => $request->product_id,
            'unit_id' => $request->unit_id
        ]);

        return $this->showProductMovesPlaces($request->product_id);
    }

}

