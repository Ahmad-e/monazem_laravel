<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Transactions;
use App\Models\Transactions_lines;
use Illuminate\Http\Request;
use App\Models\External_depts;
use App\Models\External_depts_payments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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

    public function addExternalDeptTransaction(
        $bus_id ,
        $branch_id ,
        $id,
        $user_id ,
        $amount,
        $currency_id,
        $t_description,
        $debitAccount_number,
        $creditAccount_number
    )
    {
        $transaction = Transactions::create([
            'description' => $t_description,
            'reference_number_type' => 'transaction',
            'reference_number' => '125_' . $id,
            'branch_id' => $branch_id,
            'currency_id' => $currency_id,
            'business_id' => $bus_id,
            'creator_id' => $user_id,
        ]);

        $debit_account = Accounts::where('business_id', $bus_id)
            ->where('code', $debitAccount_number)->first();


        Transactions_lines::create([
            'description' => $t_description,
            'debit_credit' => 'Debit',
            'amount' => $amount,
            'account_id' => $debit_account->id,
            'transaction_id' => $transaction->id,
            'currency_id' => $currency_id
        ]);
        $credit_account = Accounts::where('business_id', $bus_id)
            ->where('code', $creditAccount_number)->first();

        Transactions_lines::create([
            'description' => $t_description,
            'debit_credit' => 'Credit',
            'amount' => $amount,
            'account_id' => $credit_account->id,
            'transaction_id' => $transaction->id,
            'currency_id' => $currency_id
        ]);
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
        return DB::transaction(function () use ($request) {
            $user = Auth::user();

            $external_dept = External_depts::create([
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

            $t_text='إضافة دين خارجي رقم : '.$external_dept->id;
            $transaction_result = $this->addExternalDeptTransaction(
                $external_dept->business_id,
                $external_dept->branch_id,
                $external_dept->id,
                $user->id,
                $external_dept->total,
                $external_dept->currency_id,
                $t_text,
                $request->debitAccount_number ? $request->debitAccount_number : '125004',
                $request->creditAccount_number ? $request->creditAccount_number : '121000'
            );

            return $this->showExternalDeptByBusinessId($user->business_id);
        });

    }
    public function changeExternalDept(Request $request , $id){
        return DB::transaction(function () use ($request , $id) {
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
            'start_date',
            'end_date',
            'type',
            'state',
            'user_id',
            'employee_id',
            'currency_id',
        ]));

        if($request->total){
            $t_text='تعديل دين خارجي رقم : '.$dept->id;
            if($request->total > $dept->total ){

                $transaction_result = $this->addExternalDeptTransaction(
                    $dept->business_id,
                    $dept->branch_id,
                    $dept->id,
                    $user->id,
                    $request->total - $dept->total,
                    $dept->currency_id,
                    $t_text,
                    '125004',
                    '121000'
                );
            }
            else{
                $transaction_result = $this->addExternalDeptTransaction(
                    $dept->business_id,
                    $dept->branch_id,
                    $dept->id,
                    $user->id,
                    $dept->total - $request->total,
                    $dept->currency_id,
                    $t_text,
                    '121000',
                    '125004'
                );
            }
        }

        return $this->showExternalDeptByBusinessId($dept->business_id);
        });
    }
    public function deleteExternalDept($id){
        return DB::transaction(function () use ($id) {
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

            $t_text='حذف دين خارجي رقم : '.$dept->id;
            $transaction_result = $this->addExternalDeptTransaction(
                $dept->business_id,
                $dept->branch_id,
                $dept->id,
                $user->id,
                $dept->total,
                $dept->currency_id,
                $t_text,
                '121000',
                '125004'
            );

            return $this->showExternalDeptByBusinessId($dept->business_id);
        });
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
        return DB::transaction(function () use ($request) {
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

            $t_text='إضافة دفعة دين خارجي رقم : '.$dept->id;
            $transaction_result = $this->addExternalDeptTransaction(
                $dept->business_id,
                $dept->branch_id,
                $dept->id,
                $user->id,
                $request->total,
                $request->currency_id,
                $t_text,
                $request->debitAccount_number ? $request->debitAccount_number : '121000',
                $request->creditAccount_number ? $request->creditAccount_number : '125004'
            );

            return $this->showExternalDeptByBusinessId($dept->business_id);
        });
    }

    public function deleteExternalDeptPayment($id){
        return DB::transaction(function () use ($id) {
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

            $t_text='حذف دين خارجي رقم : '.$dept->id;
            $transaction_result = $this->addExternalDeptTransaction(
                $dept->business_id,
                $dept->branch_id,
                $dept->id,
                $user->id,
                $dept_pay->total,
                $dept_pay->currency_id,
                $t_text,
                 '125004',
                 '121000'
            );
            return $this->showExternalDeptByBusinessId($dept->business_id);
        });
    }

    public function changeExternalDeptPayment(){

    }

}
