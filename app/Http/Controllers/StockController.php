<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function showStocksByProductId($id){
        $data = Stock::where('product_id',$id)->get();
        $product = Products::find($id);
        return response()->json([
            'state' => 200,
            'data' => $data,
            'product' =>$product
        ], 201);
    }

    public function showStocks($id){
        $product = Products::find($id);
        if(!$product){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no product id found",
            ], 404);
        }

        $user = Auth::user();
        if($product->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This product not related to your business",
            ], 402);

        return $this->showStocksByProductId($id);

    }
    public function addStocks(Request $request){

        $request->validate([
            'count' => 'required',
            'date' => 'required',
            'building_id' => 'required',
            'product_id' => 'required',
        ]);
        $product = Products::find($request->product_id);
        if(!$product){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no product id found",
            ], 404);
        }

        $user = Auth::user();
        if($product->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This product not related to your business",
            ], 402);

        Stock::create([
            'name' => $request->name,
            'count' => $request->count,
            'date' => $request->date,
            'building_id' => $request->building_id,
            'place_id' => $request->place_id,
            'manager_id' => $request->manager_id,
            'product_id' => $request->product_id,
            'products_price_id' => $request->products_price_id,
        ]);

        return $this->showStocksByProductId($request->product_id);
    }
    public function changeStocks(Request $request , $id){
        $stock = Stock::find($id);
        if(!$stock){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no stock id found",
            ], 404);
        }

        $product = Products::find($stock->product_id);
        $user = Auth::user();
        if($product->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This product not related to your business",
            ], 402);

        $stock->update($request->only([
            'name',
            'count',
            'date',
            'building_id',
            'place_id',
            'product_id',
            'products_price_id',
            'manager_id'
        ]));

        return $this->showStocksByProductId($stock->product_id);
    }
    public function deleteStocks($id){
        $stock = Stock::find($id);
        if(!$stock){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no stock id found",
            ], 404);
        }

        $product = Products::find($stock->product_id);
        $user = Auth::user();
        if($product->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This product not related to your business",
            ], 402);

        $stock->delete();

        return $this->showStocksByProductId($stock->product_id);
    }
}
