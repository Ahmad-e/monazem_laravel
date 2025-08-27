<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use  App\Models\Transactions_lines;
use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\Rent_prepaid_expenses;
use App\Models\Rent_prepaid_revenues;
use Illuminate\Support\Facades\Auth;

class RentPrepaidController extends Controller
{
    public function showRentPreExpensesByBusId($id){
        $data = Rent_prepaid_expenses::where('business_id',$id)
            ->with('currency')->get();
        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }

    public function addRentPreExpensesTransaction(
        $bus_id ,
        $branch_id ,
        $id,
        $user_id ,
        $amount,
        $currency_id,
        $debitAccount_id,
        $creditAccount_id
    )
    {
        $transaction = Transactions::create([
            'description' => 'إضافة دفعة إيجار رقم : '.$id ,
            'reference_number_type'=>'transaction',
            'reference_number' => '326000'.$id,
            'branch_id' => $branch_id,
            'currency_id' => $currency_id,
            'business_id' =>$bus_id,
            'creator_id' => $user_id,
        ]);

        $debit_account=null;
        if( $debitAccount_id ){
            $debit_account=Accounts::find($debitAccount_id);
            if(!$debit_account)
                return -1;
        }
        else
            $debit_account = Accounts::where('business_id',$bus_id)
                ->where('code','326000')->first();


        Transactions_lines::create([
            'description' => 'إضافة دفعة إيجار رقم : '.$id ,
            'debit_credit' => 'Debit',
            'amount' => $amount,
            'account_id' => $debitAccount_id ? $debitAccount_id : $debit_account->id,
            'transaction_id' => $transaction->id,
            'currency_id' => $currency_id
        ]);
        $credit_account = null;
        if($creditAccount_id){
            $credit_account = Accounts::find($creditAccount_id);
            if(!$credit_account)
                return -2;
        }
        else
            $credit_account = Accounts::where('business_id',$bus_id)
                ->where('code','121000')->first();

        Transactions_lines::create([
            'description' => 'إضافة دفعة إيجار رقم : '.$id ,
            'debit_credit' => 'Credit',
            'amount' => $amount,
            'account_id' => $creditAccount_id ? $creditAccount_id : $credit_account->id,
            'transaction_id' => $transaction->id,
            'currency_id' => $currency_id
        ]);


        return response()->json([
            'state' => 200,
            'transaction' => $transaction
        ], 201);
    }

    public function showRentPreExpenses(){
        $user = Auth::user();
        return $this->showRentPreExpensesByBusId($user->business_id);
    }
    public function addRentPreExpenses(Request $request){
        $user = Auth::user();
        $request->validate([
            'account_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'amount' => 'required',
            'book_value' => 'required',
            'month_count' => 'required',
            'name' => 'required',
            'currency_id' => 'required'
        ]);

        $rent = Rent_prepaid_expenses::create([
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'book_value' => $request->book_value,
            'month_count' => $request->month_count,
            'name' => $request->name,
            'note' => $request->note,
            'amount_in_base' => $request->amount_in_base,
            'currency_id' => $request->currency_id,
            'account_id' => $request->account_id,
            'branch_id' => $request->branch_id,
            'business_id' => $user->business_id,
            'creator_id' => $user->id,
        ]);

        $transaction_result = $this->addRentPreExpensesTransaction(
            $user->business_id,
            $request->branch_id,
            $rent->id,
            $user->id,
            $request->amount,
            $request->currency_id,
            $request->debitAccount_id,
            $request->creditAccount_id
        );

        return $this->showRentPreExpensesByBusId($user->business_id);
    }
    public function deleteRentPreExpenses($id){
        $user = Auth::user();
        $rent = Rent_prepaid_expenses::find($id);

        if (!$rent) {
            return response()->json([
                'state' => 404,
                'error' => 2,
                'message' => "No rent by this ID",
            ], 404);
        }

        if($user->business_id != $rent->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This rent not related to your business",
            ], 402);

        $rent->delete();
        return $this->showRentPreExpensesByBusId($user->business_id);
    }


//******************************************************
//******************************************************


    public function showRentPreRevenuesByBusId($id){
        $data = Rent_prepaid_revenues::where('business_id',$id)
            ->with('currency')->get();
        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }

    public function addRentPreRevenuesTransaction(
        $bus_id ,
        $branch_id ,
        $id,
        $user_id ,
        $amount,
        $currency_id,
        $debitAccount_id,
        $creditAccount_id
    )
    {
        $transaction = Transactions::create([
            'description' => 'إضافة دفعة تأجير رقم : '.$id ,
            'reference_number_type'=>'transaction',
            'reference_number' => '414'.$id,
            'branch_id' => $branch_id,
            'currency_id' => $currency_id,
            'business_id' =>$bus_id,
            'creator_id' => $user_id,
        ]);

        $debit_account=null;
        if( $debitAccount_id ){
            $debit_account=Accounts::find($debitAccount_id);
            if(!$debit_account)
                return -1;
        }
        else
            $debit_account = Accounts::where('business_id',$bus_id)
                ->where('code','121000')->first();


        Transactions_lines::create([
            'description' => 'إضافة دفعة تأجير رقم : '.$id ,
            'debit_credit' => 'Debit',
            'amount' => $amount,
            'account_id' => $debitAccount_id ? $debitAccount_id : $debit_account->id,
            'transaction_id' => $transaction->id,
            'currency_id' => $currency_id
        ]);
        $credit_account = null;
        if($creditAccount_id){
            $credit_account = Accounts::find($creditAccount_id);
            if(!$credit_account)
                return -2;
        }
        else
            $credit_account = Accounts::where('business_id',$bus_id)
                ->where('code','414')->first();

        Transactions_lines::create([
            'description' => 'إضافة دفعة تأجير رقم : '.$id ,
            'debit_credit' => 'Credit',
            'amount' => $amount,
            'account_id' => $creditAccount_id ? $creditAccount_id : $credit_account->id,
            'transaction_id' => $transaction->id,
            'currency_id' => $currency_id
        ]);


        return response()->json([
            'state' => 200,
            'transaction' => $transaction
        ], 201);
    }

    public function showRentPreRevenues(){
        $user = Auth::user();

        return $this->showRentPreRevenuesByBusId($user->business_id);
    }
    public function addRentPreRevenues(Request $request){
        $user = Auth::user();
        $request->validate([
            'account_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'amount' => 'required',
            'book_value' => 'required',
            'month_count' => 'required',
            'name' => 'required',
            'currency_id' => 'required'
        ]);

        $rent = Rent_prepaid_revenues::create([
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'book_value' => $request->book_value,
            'month_count' => $request->month_count,
            'name' => $request->name,
            'note' => $request->note,
            'amount_in_base' => $request->amount_in_base,
            'currency_id' => $request->currency_id,
            'account_id' => $request->account_id,
            'branch_id' => $request->branch_id,
            'business_id' => $user->business_id,
            'creator_id' => $user->id,
        ]);

        $transaction_result = $this->addRentPreRevenuesTransaction(
            $user->business_id,
            $request->branch_id,
            $rent->id,
            $user->id,
            $request->amount,
            $request->currency_id,
            $request->debitAccount_id,
            $request->creditAccount_id
        );

        return $this->showRentPreRevenuesByBusId($user->business_id);
    }
    public function deleteRentPreRevenues($id){
        $user = Auth::user();
        $rent = Rent_prepaid_revenues::find($id);

        if (!$rent) {
            return response()->json([
                'state' => 404,
                'error' => 2,
                'message' => "No rent by this ID",
            ], 404);
        }

        if($user->business_id != $rent->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This rent not related to your business",
            ], 402);

        $rent->delete();
        return $this->showRentPreRevenuesByBusId($user->business_id);
    }
}
