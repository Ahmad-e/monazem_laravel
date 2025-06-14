<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products_types;
use App\Models\Products_units;
use App\Models\Products_favorites;
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

}
