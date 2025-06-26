<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assets;
use Illuminate\Support\Facades\Auth;

class AssetsController extends Controller
{
    public function showAssetsByBusinessId($id){
        $data = Assets::where('business_id' , $id)
            ->join("currencies" , 'currencies.id' ,'assets.currency_id' )
            ->get([
                "assets.id as assets_id",
                "currencies.id as currencies_id",
                "name",
                "note",
                "state",
                "amount",
                "count",
                "book_value",
                "date",
                "business_id",
                "branch_id",
                "creator_id",
                "assets.created_at",
                "assets.updated_at",
                "code_en",
                "code_ar",
                "symbol",
                "name_en",
                "name_ar",
                "exchange_rate_to_dollar",
                "blocked_currency"
            ]);
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function showAssets(){
        $user = Auth::user();
        return $this->showAssetsByBusinessId($user->business_id);
    }
    public function addAssets(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'date' => 'required',
            'state' => 'required',
            'amount' => 'required',
            'count' => 'required',
            'book_value' => 'required'
        ]);
        Assets::create([
            'name' => $request->name,
            'note' => $request->note,
            'date' => $request->date,
            'state' => $request->state,
            'amount' => $request->amount,
            'count' => $request->count,
            'book_value' => $request->book_value,
            'creator_id' => $user->id,
            'business_id'=>$user->business_id,
            'branch_id' => $request->branch_id,
            'currency_id'=>$request->currency_id
        ]);
        return $this->showAssetsByBusinessId($user->business_id);
    }
    public function changeAssets(Request $request , $id){
        $assets = Assets::find($id);

        if(!$assets)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no assets id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $assets->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This assets not related to your business",
            ], 402);

        $assets->update($request->only([
            'name',
            'note',
            'date',
            'state',
            'amount',
            'count',
            'book_value',
            'branch_id',
            'currency_id'
        ]));



        return $this->showAssetsByBusinessId($user->business_id);
    }
    public function deleteAssets($id){
        $assets = Assets::find($id);

        if(!$assets)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no assets id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $assets->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This assets not related to your business",
            ], 402);

        $assets->delete();

        return $this->showAssetsByBusinessId($user->business_id);
    }
}
