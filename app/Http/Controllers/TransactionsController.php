<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;
use  App\Models\Transactions_lines;
use Illuminate\Support\Facades\Auth;


class TransactionsController extends Controller
{
    public function showTransactionByBusinessId($id){

        $transactions = Transactions::with(['lines', 'currency'])
            ->where('business_id', $id)
            ->get();

        return response()->json([
            'state' => 200,
            'data' => $transactions,
        ], 201);
    }
    public function showTransaction(){
        $user = Auth::user();
        return $this->showTransactionByBusinessId($user->business_id);
    }
    public function addTransaction(Request $request){
        $user = Auth::user();

        $request->validate([
            'reference_number_type' => 'required',
            'currency_id' => 'required'
        ]);

        Transactions::create([
            'description' => $request->description ,
            'reference_number_type' => $request->reference_number_type,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'business_id' =>$user->business_id,
            'creator_id' => $user->id,
        ]);

        return $this->showTransactionByBusinessId($user->business_id);
    }
    public function changeTransaction(Request $request , $id){
        $transactions = Transactions::find($id);

        if(!$transactions){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no transactions id found",
            ], 404);
        }

        $user = Auth::user();
        if($transactions->business_id != $user->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This transactions not related to your business",
            ], 402);

        $transactions->update($request->only([
            'description',
            'reference_number',
            'reference_number_type',
            'number',
            'branch_id',
            'currency_id',
        ]));

        return $this->showTransactionByBusinessId($user->business_id);
    }
}
