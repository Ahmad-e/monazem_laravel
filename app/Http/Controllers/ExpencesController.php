<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Business;
use App\Models\Clients;
use Illuminate\Http\Request;
use App\Models\Expenses;
use Illuminate\Support\Facades\Auth;
use App\Models\Expenses_payments;

use mysql_xdevapi\Expression;

class ExpencesController extends Controller
{
    public function showExpensesByBusiness(){
        $user = Auth::user();
        $bus = Business::find($user->business_id);
        $data = Expenses::
        leftJoin("branches", 'branches.id', '=', 'expenses.branch_id') // استخدم leftJoin هنا
        ->where('expenses.business_id', $user->business_id)
            ->leftJoin("currencies", 'currencies.id', '=', 'expenses.currency_id') // استخدم leftJoin هنا أيضًا
            ->get([
                "expenses.id",
                "branches.name as branches_name",
                "expenses.name as expense_name",
                "date",
                "remaining",
                "note",
                "value",
                "expenses.business_id",
                "expenses.currency_id",
                "branch_id",
                "creator_id",
                "expenses.created_at",
                "expenses.updated_at",
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
            'business' => $bus,
            'data' => $data,
        ], 200);
    }

    public function showExpensesByBranches($id){
        $branch = Branches::find($id);
        $data = Expenses::where('branch_id',$id)
            ->join("currencies" , 'currencies.id' ,'expenses.currency_id' )
            ->get([
                "expenses.id",
                "expenses.name as expense_name",
                "date",
                "remaining",
                "note",
                "value",
                "expenses.business_id",
                "expenses.currency_id",
                "branch_id",
                "creator_id",
                "expenses.created_at",
                "expenses.updated_at",
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


    public function addExpense(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'value' => 'required',
            'currency_id' => 'required'
        ]);

        Expenses::create([
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
            return $this->showExpensesByBusiness();
        else
            return $this->showExpensesByBranches($request->branch_id);
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

        if($Expenses->branch_id == null)
            return $this->showExpensesByBusiness();
        else
            return $this->showExpensesByBranches($Expenses->branch_id);
    }

    //************************************************
    // start  payments controller
    //************************************************

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

        $data = Expenses_payments::where('expenses_id',$request->expenses_id)
            ->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 200);

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

        $data = Expenses_payments::where('expenses_id',$ExpensesPay->expenses_id)
            ->get();

        return response()->json([
            'state' => 200,
            'Expense'=>$Expenses,
            'data' => $data,
        ], 200);
    }
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

        $data = Expenses_payments::where('expenses_id',$id)->get();

        return response()->json([
            'state' => 200,
            'Expense'=>$Expenses,
            'data' => $data,
        ], 200);
    }
}
