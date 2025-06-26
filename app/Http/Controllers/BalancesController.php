<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Clients;
use App\Models\Products_types;
use Illuminate\Http\Request;
use App\Models\Trial_balances;
use App\Models\Clients_balances;
use Illuminate\Support\Facades\Auth;


class BalancesController extends Controller
{
    public function showTrialBalanceByAccount_id($id){
        $account = Accounts::find($id);
        $data = Trial_balances::where('account_id',$id)
            ->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
            'account' => $account
        ], 201);
    }
    public function showTrialBalance($id){
        $account = Accounts::find($id);
        if(!$account){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no account id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $account->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);
        return $this->showTrialBalanceByAccount_id($id);
    }
    public function addTrialBalance(Request $request){
        $request->validate([
            'account_id' => 'required',
            'opening' => 'required',
            'current' => 'required',
            'closing' => 'required'
        ]);

        $account = Accounts::find($request->account_id);
        if(!$account){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no account id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $account->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);

        Trial_balances::create([
            'account_id' => $request->account_id,
            'opening' => $request->opening,
            'current' => $request->current,
            'closing' => $request->closing,
            'creator_id' => $user->id,
        ]);

        return $this->showTrialBalanceByAccount_id($request->account_id);
    }
    public function changeTrialBalance(Request $request , $id){
        $balance = Trial_balances::find($id);
        if(!$balance){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no trial balance id found",
            ], 404);
        }

        $account = Accounts::find($balance->account_id);
        $user = Auth::user();
        if($user->business_id != $account->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);

        $balance->update($request->only([
            'opening',
            'current',
            'closing',
        ]));
        return $this->showTrialBalanceByAccount_id($account->id);
    }
    public function deleteTrialBalance($id){
        $balance = Trial_balances::find($id);
        if(!$balance){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no trial balance id found",
            ], 404);
        }

        $account = Accounts::find($balance->account_id);
        $user = Auth::user();
        if($user->business_id != $account->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);

        $balance->delete();

        return $this->showTrialBalanceByAccount_id($account->id);
    }

    //******************************
    //   start clients balance
    //******************************

    public function showClientsBalanceByClientId($id){
        $account = Clients::find($id);
        $data = Clients_balances::where('clients_balance.client_id',$id)
            ->join("currencies" , 'currencies.id' ,'clients_balance.currency_id' )
            ->get([
                "currencies.id as currencies_id",
                "clients_balance.id as clients_balance_id",
                "opening",
                "current",
                "closing",
                "trial_balance_id",
                "client_id",
                "creator_id",
                "clients_balance.created_at",
                "clients_balance.updated_at",
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
            'data' => $data,
            'client' => $account
        ], 201);
    }
    public function showClientsBalance($id){
        $client = Clients::find($id);
        if(!$client){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no Clients id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $client->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Clients not related to your business",
            ], 402);

        return $this->showClientsBalanceByClientId($id);
    }
    public function addClientsBalance(Request $request){
        $request->validate([
            'client_id' => 'required',
            'opening' => 'required',
            'current' => 'required',
            'closing' => 'required',
            'currency_id' => 'required'
        ]);

        $client = Clients::find($request->client_id);
        if(!$client){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no Clients id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $client->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Clients not related to your business",
            ], 402);

        Clients_balances::create([
            'client_id' => $request->client_id,
            'opening' => $request->opening,
            'current' => $request->current,
            'closing' => $request->closing,
            'currency_id' => $request->currency_id,
            'creator_id' => $user->id,
            'trial_balance_id' => $request->trial_balance_id
        ]);

        return $this->showClientsBalanceByClientId($request->client_id);
    }
    public function changeClientsBalance(Request $request , $id){

        $balance = Clients_balances::find($id);
        if(!$balance){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no balance id found",
            ], 404);
        }

        $client = Clients::find($balance->client_id);
        $user = Auth::user();
        if($user->business_id != $client->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Clients not related to your business",
            ], 402);

        $balance->update($request->only([
            'opening',
            'current',
            'closing',
            'trial_balance_id',
            'currency_id',
        ]));

        return $this->showClientsBalanceByClientId($balance->client_id);
    }

    public function deleteClientsBalance($id){

        $balance = Clients_balances::find($id);
        if(!$balance){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no balance id found",
            ], 404);
        }

        $client = Clients::find($balance->client_id);
        $user = Auth::user();
        if($user->business_id != $client->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Clients not related to your business",
            ], 402);
        $balance->trial_balance_id = null;
        $balance->save();
        $balance->delete();

        return $this->showClientsBalanceByClientId($balance->client_id);
    }

}
