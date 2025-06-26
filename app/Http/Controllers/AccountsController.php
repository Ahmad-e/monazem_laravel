<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use Illuminate\Support\Facades\Auth;


class AccountsController extends Controller
{
    public function showAccountByBusinessId($id){
        $data = Accounts::where('business_id',$id)->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function showAccounts(){
        $user = Auth::user();
        return $this->showAccountByBusinessId($user->business_id);
    }

    public function addAccount(Request $request){
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
            'nature' => 'required',
            'statement' => 'required',
            'level' =>  'required',
            'code' =>  'required',
        ]);

        Accounts::create([
            'name' => $request->name,
            'nature' => $request->nature,
            'statement' => $request->statement,
            'level' => $request->level,
            'code' => $request->code,
            'business_id' => $user->business_id,
            'branch_id' => $request->branch_id,
            'partner_id' => $request->partner_id,
        ]);
        return $this->showAccountByBusinessId($user->business_id);
    }
    public function changeAccounts(Request $request , $id){

        $accounts = Accounts::find($id);
        if(!$accounts)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No accounts by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $accounts->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This accounts not related to your business",
            ], 402);

        $accounts->update($request->only([
            'name',
            'nature',
            'statement',
            'level',
            'code',
            'branch_id',
            'partner_id',
        ]));
        return $this->showAccountByBusinessId($user->business_id);
    }

    public function deleteAccounts($id){

        $accounts = Accounts::find($id);
        if(!$accounts)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No accounts by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $accounts->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This accounts not related to your business",
            ], 402);
        $accounts->delete();

        return $this->showAccountByBusinessId($user->business_id);
    }
}
