<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Internal_depts;
use App\Models\Internal_depts_paymentes;
use App\Models\Transactions;
use App\Models\Transactions_lines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function addInternalDeptTransaction(
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
            'reference_number' => '232_' . $id,
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
        return DB::transaction(function () use ($request) {
            $user = Auth::user();

            $internal_dept = Internal_depts::create([
                'note' => $request->note,
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

            $t_text='إضافة دين داخلي رقم : '.$internal_dept->id;
            $transaction_result = $this->addInternalDeptTransaction(
                $internal_dept->business_id,
                $internal_dept->branch_id,
                $internal_dept->id,
                $user->id,
                $internal_dept->total,
                $internal_dept->currency_id,
                $t_text,
                $request->debitAccount_number ? $request->debitAccount_number : '232000' ,
                $request->creditAccount_number ? $request->debitAccount_number : '121000'
            );

            return $this->showInternalDeptByBusinessId($user->business_id);
        });
    }
    public function changeInternalDept(Request $request , $id){
        return DB::transaction(function () use ($request , $id) {
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
                'start_date',
                'end_date',
                'type',
                'state',
                'invoice_id',
                'currency_id',
                'client_id',
                'branch_id',
            ]));

            if($request->total){
                $t_text='تعديل دين خارجي رقم : '.$dept->id;
                if($request->total > $dept->total){
                    $transaction_result = $this->addInternalDeptTransaction(
                        $dept->business_id,
                        $dept->branch_id,
                        $dept->id,
                        $user->id,
                        $request->total - $dept->total,
                        $dept->currency_id,
                        $t_text,
                        '232000' ,
                         '121000'
                    );
                }
                else{
                    $transaction_result = $this->addInternalDeptTransaction(
                        $dept->business_id,
                        $dept->branch_id,
                        $dept->id,
                        $user->id,
                        $dept->total - $request->total ,
                        $dept->currency_id,
                        $t_text,
                        '121000' ,
                        '232000'
                    );
                }
            }

            return $this->showInternalDeptByBusinessId($user->business_id);
        });
    }
    public function deleteInternalDept($id){
        return DB::transaction(function () use ($id) {
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

            $t_text='حذف دين داخلي رقم : '.$dept->id;
            $transaction_result = $this->addInternalDeptTransaction(
                $dept->business_id,
                $dept->branch_id,
                $dept->id,
                $user->id,
                $dept->total,
                $dept->currency_id,
                $t_text,
                '121000',
                '232000'
            );

            return $this->showInternalDeptByBusinessId($user->business_id);
        });
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
        return DB::transaction(function () use ($request) {

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

            $payment = Internal_depts_paymentes::create([
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

            $t_text='إضافة دفعة دين داخلي رقم : '.$dept->id;
            $transaction_result = $this->addInternalDeptTransaction(
                $user->business_id,
                $request->branch_id,
                $payment->id,
                $user->id,
                $request->total,
                $request->currency_id,
                $t_text,
                $request->debitAccount_number ? $request->debitAccount_number : '121000' ,
                $request->creditAccount_number ? $request->debitAccount_number : '232000'
            );

            return $this->showInternalDeptByBusinessId($dept->business_id);
        });
    }

    public function deleteInternalDeptPayment($id){
        return DB::transaction(function () use ($id) {
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

            $t_text='حذف دفعة دين داخلي رقم : '.$dept->id;
            $transaction_result = $this->addInternalDeptTransaction(
                $user->business_id,
                $user->branch_id,
                $dept_pay->id,
                $user->id,
                $dept_pay->total,
                $dept_pay->currency_id,
                $t_text,
                '232000',
                '121000'
            );

            return $this->showInternalDeptByBusinessId($dept->business_id);
        });
    }

}
