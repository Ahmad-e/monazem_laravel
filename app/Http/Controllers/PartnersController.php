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
    public function showPartners(){
        $user = Auth::user();
        $bus = Business::find($user->business_id);
        $data = Partners::where('business_id',$user->business_id)
            ->join("currencies" , 'currencies.id' ,'partners.currency_id' )
            ->get([
                "partners.id as partners_id",
                "currencies.id as currencies_id",
                "total_capital",
                "ownership_percentage",
                "role",
                "note",
                "join_date",
                "business_id",
                "user_id",
                "currency_id",
                "partners.created_at",
                "partners.updated_at",
                "code_en",
                "code_ar",
                "symbol",
                "name_en",
                "name_ar",
                "exchange_rate_to_dollar",
                "blocked_currency",
                'blocked_partner'
            ]);

        return response()->json([
            'state' => 200,
            'business'=> $bus,
            'data' => $data,
        ], 201);
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

        $data = Partners::where('business_id',$user->business_id)
            ->join("currencies" , 'currencies.id' ,'partners.currency_id' )
            ->get([
                "partners.id as partners_id",
                "currencies.id as currencies_id",
                "total_capital",
                "ownership_percentage",
                "role",
                "note",
                "join_date",
                "business_id",
                "user_id",
                "currency_id",
                "partners.created_at",
                "partners.updated_at",
                "code_en",
                "code_ar",
                "symbol",
                "name_en",
                "name_ar",
                "exchange_rate_to_dollar",
                "blocked_currency",
                'blocked_partner'
            ]);

        return response()->json([
            'state' => 200,
            'message'=>"Added successfully",
            'data' => $data,
        ], 201);
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

        // تحديث البيانات الاختيارية

        $partner->update($request->only([
            'total_capital',
            'ownership_percentage',
            'role',
            'note',
            'join_date',
            'currency_id'
        ]));

        $data = Partners::where('business_id',$user->business_id)
            ->join("currencies" , 'currencies.id' ,'partners.currency_id' )
            ->get([
                "partners.id as partners_id",
                "currencies.id as currencies_id",
                "total_capital",
                "ownership_percentage",
                "role",
                "note",
                "join_date",
                "business_id",
                "user_id",
                "currency_id",
                "partners.created_at",
                "partners.updated_at",
                "code_en",
                "code_ar",
                "symbol",
                "name_en",
                "name_ar",
                "exchange_rate_to_dollar",
                "blocked_currency",
                'blocked_partner'
            ]);

        return response()->json([
            'state' => 200,
            'message'=>"updated successfully",
            'data' => $data,
        ], 201);
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

        $data = Partners::where('business_id',$user->business_id)
            ->join("currencies" , 'currencies.id' ,'partners.currency_id' )
            ->get([
                "partners.id as partners_id",
                "currencies.id as currencies_id",
                "total_capital",
                "ownership_percentage",
                "role",
                "note",
                "join_date",
                "business_id",
                "user_id",
                "currency_id",
                "partners.created_at",
                "partners.updated_at",
                "code_en",
                "code_ar",
                "symbol",
                "name_en",
                "name_ar",
                "exchange_rate_to_dollar",
                "blocked_currency",
                'blocked_partner'
            ]);

        return response()->json([
            'state' => 200,
            'message' => "partner blocked status updated",
            'data' => $data
        ]);
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
            ->join("currencies" , 'currencies.id' ,'partners_payments.currency_id' )
            ->get([
                "partners_payments.id",
            "value",
            "date",
            "partner_id",
            "currency_id",
            "partners_payments.created_at",
            "partners_payments.updated_at",
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
            'currency_id' => $request->currency_id
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
            ->join("currencies" , 'currencies.id' ,'partners_withdrawals.currency_id' )
            ->get([
                "partners_withdrawals.id",
                "value",
                "date",
                "partner_id",
                "currency_id",
                "partners_withdrawals.created_at",
                "partners_withdrawals.updated_at",
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
            'currency_id' => $request->currency_id
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
