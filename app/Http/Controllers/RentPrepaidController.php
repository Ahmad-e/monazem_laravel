<?php

namespace App\Http\Controllers;

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

        Rent_prepaid_expenses::create([
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
            ->join("currencies", 'currencies.id', 'rent_prepaid_revenues.currency_id')
            ->get([
                "currencies.id as currency_id",
                "rent_prepaid_revenues.id as rent_prepaid_expenses_id",
                "amount",
                "book_value",
                "amount_in_base",
                "month_count",
                "name",
                "note",
                "start_date",
                "end_date",
                "account_id",
                "business_id",
                "branch_id",
                "creator_id",
                "rent_prepaid_revenues.created_at",
                "rent_prepaid_revenues.updated_at",
                "code_en",
                "code_ar",
                "symbol",
                "name_en",
                "name_ar",
                "exchange_rate_to_dollar",
                "blocked_currency"
            ]);
        return response()->json([
            'state' => 200,
            'data' => $data
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

        Rent_prepaid_revenues::create([
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
