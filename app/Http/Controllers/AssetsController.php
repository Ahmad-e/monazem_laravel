<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Transactions;
use App\Models\Transactions_lines;
use Illuminate\Http\Request;
use App\Models\Assets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetsController extends Controller
{
    public function showAssetsByBusinessId($id){
        $data = Assets::where('business_id' , $id)
            ->with('currency')->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function addAssetsTransaction(
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
            'reference_number' => '11_' . $id,
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

    public function showAssets(){
        $user = Auth::user();
        return $this->showAssetsByBusinessId($user->business_id);
    }
    public function addAssets(Request $request){

        $request->validate([
            'name' => 'required',
            'date' => 'required',
            'state' => 'required',
            'amount' => 'required',
            'count' => 'required',
            'book_value' => 'required',
            'debitAccount_number' => 'required'
        ]);
        return DB::transaction(function () use ($request) {
            $user = Auth::user();
            $asset = Assets::create([
                'name' => $request->name,
                'note' => $request->note,
                'date' => $request->date,
                'state' => $request->state,
                'amount' => $request->amount,
                'count' => $request->count,
                'book_value' => $request->book_value,
                'creator_id' => $user->id,
                'business_id'=>$user->business_id,
                'branch_id' => $request->branch_id,
                'currency_id'=>$request->currency_id
            ]);

            $t_text='إضافة أصول باسم : '.$asset->name;
            $transaction_result = $this->addAssetsTransaction(
                $user->business_id,
                $request->branch_id,
                $asset->id,
                $user->id,
                $request->amount,
                $request->currency_id,
                $t_text,
                $request->debitAccount_number ,
                $request->creditAccount_number ? $request->debitAccount_number : '121000'
            );

            return $this->showAssetsByBusinessId($user->business_id);
        });
    }
    public function changeAssets(Request $request , $id){
        $assets = Assets::find($id);

        if(!$assets)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no assets id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $assets->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This assets not related to your business",
            ], 402);

        $assets->update($request->only([
            'name',
            'note',
            'date',
            'state',
            'count',
            'book_value',
            'branch_id',
            'currency_id'
        ]));



        return $this->showAssetsByBusinessId($user->business_id);
    }
    public function deleteAssets($id){
        $assets = Assets::find($id);

        if(!$assets)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no assets id found",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $assets->business_id )
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This assets not related to your business",
            ], 402);

        $assets->delete();

        return $this->showAssetsByBusinessId($user->business_id);
    }
}
