<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Business;
use App\Models\Expenses;
use App\Models\Expenses_payments;
use App\Models\Revenues;
use App\Models\Revenues_payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevenuesController extends Controller
{
    public function showRevenuesByBusiness($id){
        $data = Revenues::where('business_id',$id)
            ->with(['payments', 'currency'])->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 200);
    }

    public function showRevenues(){
        $user = Auth::user();
        return $this->showRevenuesByBusiness($user->business_id);
    }

    public function addRevenue(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'value' => 'required',
            'currency_id' => 'required'
        ]);

        Revenues::create([
            'name' => $request->name,
            'value' => $request->value,
            'remaining' => $request->remaining ? ($request->remaining) : (0),
            'date' => $request->date,
            'note' => $request->note,
            'business_id' => $user->business_id,
            'branch_id' => $request->branch_id,
            'currency_id'=>$request->currency_id,
            'creator_id' => $user->id
        ]);

        return $this->showRevenuesByBusiness($user->business_id);
    }

    public function deleteRevenue ($id){
        $Expenses = Revenues::find($id);

        if (!$Expenses) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Revenues id not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $Expenses->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This revenues not related to your business",
            ], 402);
        // حذف العطلة
        $Expenses->delete();

        return $this->showRevenuesByBusiness($Expenses->business_id);
    }

    public function changeRevenue (Request $request , $id){
        $Expenses = Revenues::find($id);

        if (!$Expenses) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Revenues id not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $Expenses->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This revenues not related to your business",
            ], 402);

        $Expenses->update($request->only([
            'name',
            'note',
            'value',
            'remaining',
            'date',
            'branch_id',
            'currency_id',
        ]));

        return $this->showRevenuesByBusiness($Expenses->business_id);
    }

    //************************************************
    // start  payments controller
    //************************************************

    public function showRevenuePayment ($id){
        $revenue = Revenues::find($id);
        if (!$revenue) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Revenue id not found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $revenue->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This revenue not related to your business",
            ], 402);

        $data = Revenues::where('id',$id)
            ->with(['payments', 'currency'])->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 200);
    }

    public function addRevenuePayment (Request $request){

        $request->validate([
            'note' => 'required',
            'value' => 'required',
            'revenues_id' => 'required',
            'currency_id' => 'required'
        ]);

        $revenue = Revenues::find($request->revenues_id);
        if(!$revenue){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no revenue id found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $revenue->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This revenue not related to your business",
            ], 402);

        Revenues_payments::create([
            'value' => $request->value,
            'date' => $request->date,
            'note' => $request->note,
            'revenues_id' => $request->revenues_id,
            'currency_id'=>$request->currency_id,
            'creator_id' => $user->id
        ]);

        $revenue->remaining = $revenue->remaining - $request->value;
        $revenue->save();

        return $this->showRevenuePayment($request->revenues_id);

    }

    public function deleteRevenuePayment ($id){
        $RevenuePay = Revenues_payments::find($id);

        if (!$RevenuePay) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Revenue Payment id not found",
            ], 404);
        }
        $revenue = Revenues::find($RevenuePay->revenues_id);
        $user = Auth::user();
        if($user->business_id != $revenue->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This revenue not related to your business",
            ], 402);

        // حذف العطلة
        $RevenuePay->delete();

        $revenue->remaining = $revenue->remaining + $RevenuePay->value;
        $revenue->save();

        return $this->showRevenuePayment($RevenuePay->revenues_id);
    }
}
