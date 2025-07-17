<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\External_depts;
use App\Models\External_depts_payments;
use Illuminate\Support\Facades\Auth;


class ExternalDeptsController extends Controller
{
    public function showExternalDeptByBusinessId($id){
        $data = External_depts::where('business_id', $id)
            ->with(['Payment','currency'])->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 200);
    }

    public function showExternalDept(){
        $user = Auth::user();
        return $this->showExternalDeptByBusinessId($user->business_id);
    }
    public function addExternalDept(Request $request){

        $request->validate([
            'total' => 'required',
            'paid' => 'required',
            'remaining' => 'required',
            'type' => 'required',
            'state' => 'required',
            'currency_id' => 'required',
        ]);

        $user = Auth::user();

        External_depts::create([
            'total' => $request->total,
            'paid' => $request->paid,
            'remaining' => $request->remaining,
            'type' => $request->type,
            'state' => $request->state,
            'note' => $request->note,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'employee_id' => $request->employee_id,
            'user_id' => $request->user_id,
            'business_id' => $user->business_id,
            'currency_id'=>$request->currency_id,
            'creator_id' => $user->id
        ]);

//        $transaction = Transactions::create([
//            'description' => $request->note ,
//            'reference_number_type' => 'external_debt',
//            'branch_id' => $request->branch_id,
//            'currency_id' => $request->currency_id,
//            'business_id' =>$user->business_id,
//            'creator_id' => $user->id,
//        ]);

        return $this->showExternalDeptByBusinessId($user->business_id);
    }
    public function changeExternalDept(Request $request , $id){
        $dept = External_depts::find($id);

        if(!$dept){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no dept id found",
            ], 404);
        }

        $user = Auth::user();
        if($dept->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This dept not related to your business",
            ], 402);

        $dept->update($request->only([
            'note',
            'total',
            'paid',
            'remaining',
            'start_date',
            'end_date',
            'type',
            'state',
            'user_id',
            'employee_id',
            'currency_id',
        ]));
        return $this->showExternalDeptByBusinessId($dept->business_id);
    }
    public function deleteExternalDept($id){
        $dept = External_depts::find($id);

        if(!$dept){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no dept id found",
            ], 404);
        }

        $user = Auth::user();
        if($dept->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This dept not related to your business",
            ], 402);

        $dept->delete();

        return $this->showExternalDeptByBusinessId($dept->business_id);
    }

    //************************************************
    // start  payments controller
    //************************************************

    public function addExternalDeptPayment(Request $request){
        $request->validate([
            'total' => 'required',
            'external_debt_id' => 'required',
            'currency_id' => 'required',
        ]);

        $dept = External_depts::find($request->external_debt_id);

        if(!$dept){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no dept id found",
            ], 404);
        }

        $user = Auth::user();
        if($dept->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This dept not related to your business",
            ], 402);

        External_depts_payments::create([
            'total' => $request->total,
            'note' => $request->note,
            'date' => $request->date,
            'external_debt_id' => $request->external_debt_id,
            'currency_id'=>$request->currency_id,
            'creator_id' => $user->id
        ]);
        $dept->paid = $dept->paid + $request->total ;
        $dept->remaining = $dept->remaining - $request->total ;

        $dept->save();


        return $this->showExternalDeptByBusinessId($dept->business_id);
    }

    public function deleteExternalDeptPayment($id){
        $dept_pay = External_depts_payments::find($id);
        if(!$dept_pay){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no dept_payment id found",
            ], 404);
        }

        $dept = External_depts::find($dept_pay->external_debt_id);
        $user = Auth::user();

        if($dept->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This dept not related to your business",
            ], 402);

        $dept_pay->delete();

        $dept->paid = $dept->paid - $dept_pay->total ;
        $dept->remaining = $dept->remaining + $dept_pay->total ;

        $dept->save();

        return $this->showExternalDeptByBusinessId($dept->business_id);
    }

    public function changeExternalDeptPayment(){

    }

}
