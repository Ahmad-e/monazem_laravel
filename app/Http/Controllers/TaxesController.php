<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Models\Branches;
use App\Models\Products;
use App\Models\Taxes;
use App\Models\Taxes_products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TaxesController extends Controller
{
    public function showTaxesByBusinessId($id){
        $data = Taxes::where('business_id' , $id)->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function showTaxes(){
        $user = Auth::user();
        return $this->showTaxesByBusinessId($user->business_id);
    }
    public function addTax(Request $request){
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'level' => 'required',
            'rate' => 'required'
        ]);
        Taxes::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'level' => $request->level,
            'rate' => $request->rate,
            'creator_id'=>$user->id,
            'business_id'=>$user->business_id,
            'branch_id' => $request->branch_id,
        ]);

        return $this->showTaxesByBusinessId($user->business_id);
    }
    public function changeTax(Request $request , $id){
        $tax = Taxes::find($id);

        if(!$tax)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no tax id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $tax->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This tax not related to your business",
            ], 402);

        $tax->update($request->only([
            'name',
            'description',
            'type',
            'level',
            'rate',
            'branch_id',
        ]));


        return $this->showTaxesByBusinessId($user->business_id);
    }
    public function taggleblockTax($id){
        $tax = Taxes::find($id);

        if(!$tax)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no tax id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $tax->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This tax not related to your business",
            ], 402);

        $tax->blocked = !$tax->blocked;
        $tax->save();

        return $this->showTaxesByBusinessId($user->business_id);
    }

    public function showProductTaxesByBranchId($id){
        $data = Taxes_products::where('branch_id' , $id)
            ->with(['taxes','products'])
            ->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function showProductTaxes($id){
        $branch = Branches::find($id);
        if(!$branch)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no tax id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $branch->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This tax not related to your branch",
            ], 402);
        return $this->showProductTaxesByBranchId($id);
    }
    public function addProductTaxes(Request $request){

        $request->validate([
            'tax_id' => 'required',
            'product_id' => 'required',
            'branch_id' => 'required',
        ]);
        $tax = Taxes::find($request->tax_id);
        if(!$tax)
            return response()->json([
                'state' => 404,
                'error'=> 6 ,
                'message'=>"no tax id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $tax->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 1 ,
                'message'=>"This tax not related to your business",
            ], 402);

        $product = Products::find($request->product_id);
        if(!$product)
            return response()->json([
                'state' => 404,
                'error'=> 5 ,
                'message'=>"no product id found",
            ], 404);

        if($user->business_id != $product->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This product not related to your business",
            ], 402);

        $branch = Branches::find($request->branch_id);
        if(!$branch)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no branch id found",
            ], 404);

        if($user->business_id != $branch->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This branch not related to your business",
            ], 402);

        Taxes_products::create([
            'tax_id' => $request->tax_id,
            'product_id' => $request->product_id,
            'branch_id' => $request->branch_id,
            'creator_id'=>$user->id,
        ]);

        return $this->showProductTaxesByBranchId($request->branch_id);
    }
    public function deleteProductTaxes($id){

        $productTax= Taxes_products::find($id);
        if(!$productTax)
            return response()->json([
                'state' => 404,
                'error'=> 4 ,
                'message'=>"no productTax id found",
            ], 404);

        $branch = Branches::find($productTax->branch_id);
        if(!$branch)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no branch id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $branch->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This branch not related to your business",
            ], 402);

        $productTax->delete();
        return $this->showProductTaxesByBranchId($productTax->branch_id);
    }
}
