<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Branches;
use App\Models\Business;
use App\Models\Clients;
use App\Models\Transactions;
use App\Models\Transactions_lines;
use Illuminate\Http\Request;
use App\Models\Expenses;
use Illuminate\Support\Facades\Auth;
use App\Models\Expenses_payments;

use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Expression;

class ExpencesController extends Controller
{
    public function showExpensesByBusiness($id){
        $data = Expenses::where('expenses.business_id', $id)
            ->with(['payments', 'currency'])->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 200);
    }

    public function showExpenses(){
        $user = Auth::user();
        return $this->showExpensesByBusiness($user->business_id);
    }

    public function addExpensesTransaction(
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
            'reference_number' => $debitAccount_number. '_' . $id,
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

    public function addExpense(Request $request){
        $request->validate([
            'name' => 'required',
            'value' => 'required',
            'currency_id' => 'required',
            'debitAccount_number' => 'required'
        ]);
        return DB::transaction(function () use ($request) {
            $user = Auth::user();
            $expenses = Expenses::create([
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

            $t_text='إضافة مصروفات باسم : '.$request->name;
            $transaction_result = $this->addExpensesTransaction(
                $expenses->business_id,
                $expenses->branch_id,
                $expenses->id,
                $user->id,
                $request->value ,
                $expenses->currency_id,
                $t_text,
                $request->debitAccount_number  ,
                $request->creditAccount_number ? $request->debitAccount_number : '121000'
            );

            return $this->showExpensesByBusiness($user->business_id);
        });
    }

    public function changeExpense (Request $request,$id){
        $Expenses = Expenses::find($id);

        if (!$Expenses) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Expenses id not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $Expenses->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This expenses not related to your business",
            ], 402);

        $Expenses->update($request->only([
            'name',
            'note',
            'date',
            'branch_id',
            'currency_id',
        ]));

        return $this->showExpensesByBusiness($user->business_id);
    }

    public function deleteExpense ($id){
        $Expenses = Expenses::find($id);

        if (!$Expenses) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Expenses id not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $Expenses->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This expenses not related to your business",
            ], 402);

        // حذف العطلة
        $Expenses->delete();

        return $this->showExpensesByBusiness($user->business_id);
    }

    //************************************************
    // start  payments controller
    //************************************************

    public function showExpensePayment ($id){

        $Expenses = Expenses::find($id);

        if (!$Expenses) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Expenses id not found",
            ], 404);
        }
        $user = Auth::user();
        if($Expenses->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This expenses not related to your business",
            ], 402);

        $data = Expenses::where('id',$id)
            ->with(['payments', 'currency'])->get();

        return response()->json([
            'state' => 200,
            'Expense'=>$Expenses,
            'data' => $data,
        ], 200);
    }

    public function addExpensePayment (Request $request){
        $request->validate([
            'note' => 'required',
            'value' => 'required',
            'expenses_id' => 'required',
            'currency_id' => 'required'
        ]);

        $expenses = Expenses::find($request->expenses_id);
        if(!$expenses){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no expense id found",
            ], 404);
        }
        $user = Auth::user();
        if($expenses->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This expenses not related to your business",
            ], 402);

        $Expenses = Expenses::find($request->expenses_id);
        Expenses_payments::create([
                'value' => $request->value,
                'date' => $request->date,
                'note' => $request->note,
                'expenses_id' => $request->expenses_id,
                'currency_id'=>$request->currency_id,
                'creator_id' => $user->id
            ]);

        $Expenses->remaining = $Expenses->remaining - $request->value;
        $Expenses->save();

        return $this->showExpensePayment($request->expenses_id);

    }

    public function deleteExpensePayment ($id){
        $ExpensesPay = Expenses_payments::find($id);
        $Expenses = Expenses::find($ExpensesPay->expenses_id);
        if (!$ExpensesPay) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Expenses id not found",
            ], 404);
        }
        $user = Auth::user();
        if($Expenses->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This expenses not related to your business",
            ], 402);

        $Expenses->remaining = $Expenses->remaining + $ExpensesPay->value;
        $Expenses->save();
        // حذف العطلة
        $ExpensesPay->delete();

        return $this->showExpensePayment($ExpensesPay->expenses_id);
    }
}
