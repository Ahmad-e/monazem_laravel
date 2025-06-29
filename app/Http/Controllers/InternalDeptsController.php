<?php

namespace App\Http\Controllers;

use App\Models\Internal_depts;
use App\Models\Internal_depts_paymentes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalDeptsController extends Controller
{
    public function showInternalDeptByBusinessId($id){
        $debts = Internal_depts::where('business_id',$id)
        ->with(['payments.currency', 'currency'])->get();

        return response()->json([
            'state' => 200,
            'data' => $debts,
        ], 200);
    }
    public function showInternalDept(){
        $user = Auth::user();
        return $this->showInternalDeptByBusinessId($user->business_id);
    }
    public function addInternalDept(Request $request){
        $request->validate([
            'total' => 'required',
            'paid' => 'required',
            'remaining' => 'required',
            'type' => 'required',
            'state' => 'required',
            'currency_id' => 'required',
        ]);

        $user = Auth::user();

        Internal_depts::create([
            'total' => $request->total,
            'paid' => $request->paid,
            'remaining' => $request->remaining ,
            'type' => $request->type,
            'state' => $request->state,
            'invoice_id' => $request->invoice_id,
            'client_id' => $request->client_id,
            'branch_id' => $request->branch_id,
            'currency_id'=>$request->currency_id,
            'creator_id' => $user->id,
            'business_id' => $user->business_id

        ]);

        return $this->showInternalDeptByBusinessId($user->business_id);
    }
    public function changeInternalDept(Request $request , $id){
        $dept = Internal_depts::find($id);
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
            'invoice_id',
            'currency_id',
            'client_id',
            'branch_id',
        ]));


        return $this->showInternalDeptByBusinessId($user->business_id);
    }
    public function deleteInternalDept($id){
        $dept = Internal_depts::find($id);
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

        return $this->showInternalDeptByBusinessId($user->business_id);
    }

    //************************************************
    // start  payments controller
    //************************************************

    public function addInternalDeptPayment(Request $request){
        $request->validate([
            'total' => 'required',
            'internal_dept_id' => 'required',
            'currency_id' => 'required',
        ]);

        $dept = Internal_depts::find($request->internal_dept_id);

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

        Internal_depts_paymentes::create([
            'total' => $request->total,
            'note' => $request->note,
            'date' => $request->date,
            'internal_dept_id' => $request->internal_dept_id,
            'currency_id'=>$request->currency_id,
            'creator_id' => $user->id
        ]);
        $dept->paid = $dept->paid + $request->total ;
        $dept->remaining = $dept->remaining - $request->total ;

        $dept->save();


        return $this->showInternalDeptByBusinessId($dept->business_id);
    }

    public function deleteInternalDeptPayment($id){
        $dept_pay = Internal_depts_paymentes::find($id);
        if(!$dept_pay){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no dept_payment id found",
            ], 404);
        }

        $dept = Internal_depts::find($dept_pay->internal_dept_id);
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

        return $this->showInternalDeptByBusinessId($dept->business_id);
    }

}
