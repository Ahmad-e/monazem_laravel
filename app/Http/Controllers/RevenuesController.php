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
    public function showRevenuesByBusiness(){
        $user = Auth::user();
        $bus = Business::find($user->business_id);
        $data = Revenues::where('revenues.business_id',$bus->id)
            ->leftJoin("branches" , 'branches.id' ,'revenues.branch_id' )
            ->leftJoin("currencies" , 'currencies.id' ,'revenues.currency_id' )
            ->get([
                "revenues.id",
                "branches.name as branches_name",
                "revenues.name as revenue_name",
                "date",
                "remaining",
                "note",
                "value",
                "revenues.business_id",
                "revenues.currency_id",
                "branch_id",
                "creator_id",
                "revenues.created_at",
                "revenues.updated_at",
                'code_en',
                'code_ar',
                'symbol',
                'name_en',
                'name_ar',
                'exchange_rate_to_dollar',
                'blocked_currency',
            ]);

        return response()->json([
            'state' => 200,
            'business'=>$bus,
            'data' => $data,
        ], 200);
    }


    public function showRevenuesByBranches($id){
        $branch = Branches::find($id);
        $data = Revenues::where('branch_id',$id)
            ->join("currencies" , 'currencies.id' ,'revenues.currency_id' )
            ->get([
                "revenues.id",
                "revenues.name as revenue_name",
                "date",
                "remaining",
                "note",
                "value",
                "revenues.business_id",
                "revenues.currency_id",
                "branch_id",
                "creator_id",
                "revenues.created_at",
                "revenues.updated_at",
                'code_en',
                'code_ar',
                'symbol',
                'name_en',
                'name_ar',
                'exchange_rate_to_dollar',
                'blocked_currency',
            ]);

        return response()->json([
            'state' => 200,
            'branch'=>$branch,
            'data' => $data,
        ], 200);
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

        if($request->branch_id == null)
            return $this->showRevenuesByBusiness();
        else
            return $this->showRevenuesByBranches($request->branch_id);
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

        if($Expenses->branch_id == null)
            return $this->showRevenuesByBusiness($Expenses->business_id);
        else
            return $this->showRevenuesByBranches($Expenses->branch_id);
    }

    //************************************************
    // start  payments controller
    //************************************************

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

        $data = Revenues_payments::where('revenues_id',$request->revenues_id)
            ->get();

        return response()->json([
            'state' => 200,
            'revenue'=>$revenue,
            'data' => $data,
        ], 200);

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

        $data = Revenues_payments::where('revenues_id',$RevenuePay->revenues_id)
            ->get();

        return response()->json([
            'state' => 200,
            'revenue'=>$revenue,
            'data' => $data,
        ], 200);
    }


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

        $data = Revenues_payments::where('revenues_id',$id)->get();

        return response()->json([
            'state' => 200,
            'revenue'=>$revenue,
            'data' => $data,
        ], 200);
    }

}
