<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Employees;
use App\Models\Powers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partners;
use App\Models\Partners_payments;
use App\Models\Partners_withdrawals;
use Illuminate\Support\Facades\Bus;
use function Illuminate\Queue\retrieveNextJob;

class PartnersController extends Controller
{
    public function showPartnersByBusinessId($id){
        $data = Partners::where('business_id',$id)
            ->with('currency')->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function showPartners(){
        $user = Auth::user();
        return $this->showPartnersByBusinessId($user->business_id);
    }

    public function addPartner(Request $request){
        $user = Auth::user();
        $request->validate([
            'user_id' => 'required',
            'currency_id' => 'required'
        ]);

        Partners::create([
            'total_capital' => $request->total_capital ? $request->total_capital : 0,
            'ownership_percentage' => $request->ownership_percentage ? $request->ownership_percentage : 0,
            'role' => $request->role,
            'note' => $request->note,
            'join_date' => $request->join_date,
            'business_id' => $user->business_id,
            'user_id' => $request->user_id,
            'currency_id' => $request->currency_id
        ]);

        return $this->showPartnersByBusinessId($user->business_id);
    }

    public function updatePartner(Request $request, $id)
    {
        $partner= Partners::find($id);

        if (!$partner) {
            return response()->json([
                'state' => 404,
                'error'=> 3 ,
                'message' => "Partner not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);


        $partner->update($request->only([
            'total_capital',
            'ownership_percentage',
            'role',
            'note',
            'join_date',
            'currency_id'
        ]));

        return $this->showPartnersByBusinessId($user->business_id);
    }
    public function toggleBlockPartner($id){
        $partner= Partners::find($id);

        if (!$partner) {
            return response()->json([
                'state' => 404,
                'error'=> 3 ,
                'message' => "Partner not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        // عكس القيمة
        $partner->blocked_partner = !$partner->blocked_partner;
        $partner->save();

        return $this->showPartnersByBusinessId($user->business_id);
    }

    //************************************************
    // start  payments controller
    //************************************************


    public function showPartnerPayment($id){
        $partner = Partners::find($id);
        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        $data = Partners_payments::where('partner_id',$id)
            ->with('currency')->get();

        return response()->json([
            'state' => 200,
            'partner'=> $partner ,
            'data' => $data,
        ], 201);
    }
    public function addPartnerPayment(Request $request){
        $request->validate([
            'partner_id' => 'required',
            'value' => 'required',
            'currency_id' => 'required'
        ]);

        $partner = Partners::find($request->partner_id);

        if(!$partner) {
            return response()->json([
                'state' => 404,
                'error' => 2,
                'message' => "no partner id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        Partners_payments::create([
            'value' => $request->value,
            'date' => $request->date,
            'partner_id' => $request->partner_id,
            'currency_id' => $request->currency_id,
            'creator_id' => $user->id
        ]);

        $partner -> total_capital = $partner -> total_capital + $request->value ;
        $partner -> save();
        return $this -> showPartnerPayment($request->partner_id);
    }
    public function deletePartnerPayment($id){
        $partnerPay = Partners_payments::find($id);

        if(!$partnerPay) {
            return response()->json([
                'state' => 404,
                'error' => 3,
                'message' => "no partners payments id found",
            ], 404);
        }

        $partner = Partners::find($partnerPay->partner_id);
        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        $partnerPay-> delete();
        $partner -> total_capital = $partner -> total_capital - $partnerPay->value ;
        $partner -> save();
        return $this -> showPartnerPayment($partnerPay->partner_id);
    }

    //************************************************
    // start  withdrawals controller
    //************************************************

    public function showWithdrawalsPayment($id){
        $partner = Partners::find($id);
        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        $data = Partners_withdrawals::where('partner_id',$id)
            ->with('currency')->get();

        return response()->json([
            'state' => 200,
            'partner'=> $partner ,
            'data' => $data,
        ], 201);
    }
    public function addWithdrawalsPayment(Request $request){
        $request->validate([
            'partner_id' => 'required',
            'value' => 'required',
            'currency_id' => 'required'
        ]);

        $partner = Partners::find($request->partner_id);

        if(!$partner) {
            return response()->json([
                'state' => 404,
                'error' => 2,
                'message' => "no partner id found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        Partners_withdrawals::create([
            'value' => $request->value,
            'date' => $request->date,
            'partner_id' => $request->partner_id,
            'currency_id' => $request->currency_id,
            'creator_id' => $user->id
        ]);

        $partner -> total_capital = $partner -> total_capital - $request->value ;
        $partner -> save();
        return $this -> showWithdrawalsPayment($request->partner_id);
    }
    public function deleteWithdrawalsPayment($id){
        $partnerPay = Partners_withdrawals::find($id);

        if(!$partnerPay) {
            return response()->json([
                'state' => 404,
                'error' => 3,
                'message' => "no partners withdrawals id found",
            ], 404);
        }
        $partner = Partners::find($partnerPay->partner_id);
        $user = Auth::user();
        if($user->business_id != $partner->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This partner not related to your business",
            ], 402);

        $partnerPay-> delete();
        $partner -> total_capital = $partner -> total_capital + $partnerPay->value ;
        $partner -> save();
        return $this -> showWithdrawalsPayment($partnerPay->partner_id);

    }
}
