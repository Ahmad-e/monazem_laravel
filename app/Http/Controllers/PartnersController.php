<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Business;
use App\Models\Employees;
use App\Models\Powers;
use App\Models\Transactions;
use App\Models\Transactions_lines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partners;
use App\Models\Partners_payments;
use App\Models\Partners_withdrawals;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use function Illuminate\Queue\retrieveNextJob;

class PartnersController extends Controller
{
    public function addPartnerTransaction(
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
            'reference_number' => '211_' . $id,
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
        $request->validate([
            'user_id' => 'required',
            'currency_id' => 'required'
        ]);

        return DB::transaction(function () use ($request) {
            $user = Auth::user();


            $partner = Partners::create([
                'total_capital' => $request->total_capital ? $request->total_capital : 0,
                'ownership_percentage' => $request->ownership_percentage ? $request->ownership_percentage : 0,
                'role' => $request->role,
                'note' => $request->note,
                'join_date' => $request->join_date,
                'business_id' => $user->business_id,
                'user_id' => $request->user_id,
                'currency_id' => $request->currency_id
            ]);

            $t_text='إضافة شريك رقم : '.$partner->id;
            $transaction_result = $this->addPartnerTransaction(
                $partner->business_id,
                $partner->branch_id,
                $partner->id,
                $user->id,
                $request->total_capital ? $request->total_capital : 0,
                $partner->currency_id,
                $t_text,
                $request->debitAccount_number ? $request->debitAccount_number : '121000' ,
                $request->creditAccount_number ? $request->debitAccount_number : '211000'
            );

            return $this->showPartnersByBusinessId($user->business_id);
        });
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
//            'total_capital',
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

        return DB::transaction(function () use ($request) {

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

            $payment = Partners_payments::create([
                'value' => $request->value,
                'date' => $request->date,
                'partner_id' => $request->partner_id,
                'currency_id' => $request->currency_id,
                'creator_id' => $user->id
            ]);

            $partner -> total_capital = $partner -> total_capital + $request->value ;
            $partner -> save();

            $t_text='إضافة دفعة للشريك برقم : '.$payment->id;
            $transaction_result = $this->addPartnerTransaction(
                $partner->business_id,
                $partner->branch_id,
                $payment->id,
                $user->id,
                $request->value,
                $partner->currency_id,
                $t_text,
                $request->debitAccount_number ? $request->debitAccount_number : '121000' ,
                $request->creditAccount_number ? $request->debitAccount_number : '211000'
            );

            return $this -> showPartnerPayment($request->partner_id);
        });
    }
    public function deletePartnerPayment($id){
        return DB::transaction(function () use ($id) {
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

            $t_text='حذف دفعة للشريك رقم : '.$partnerPay->id;
            $transaction_result = $this->addPartnerTransaction(
                $partner->business_id,
                $partner->branch_id,
                $partnerPay->id,
                $user->id,
                $partnerPay->value,
                $partnerPay->currency_id,
                $t_text,
                '211000' ,
                '121000'
            );

            return $this -> showPartnerPayment($partnerPay->partner_id);
        });
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
        return DB::transaction(function () use ($request) {
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

            $payment = Partners_withdrawals::create([
                'value' => $request->value,
                'date' => $request->date,
                'partner_id' => $request->partner_id,
                'currency_id' => $request->currency_id,
                'creator_id' => $user->id
            ]);

            $partner -> total_capital = $partner -> total_capital - $request->value ;
            $partner -> save();

            $t_text='إضافة مسحوبات للشريك برقم : '.$payment->id;
            $transaction_result = $this->addPartnerTransaction(
                $partner->business_id,
                $partner->branch_id,
                $payment->id,
                $user->id,
                $payment->value,
                $payment->currency_id,
                $t_text,
                '211000' ,
                '121000'
            );

            return $this -> showWithdrawalsPayment($request->partner_id);
        });
    }
    public function deleteWithdrawalsPayment($id){
        return DB::transaction(function () use ($id) {
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

            $t_text='حذف مسحوبات للشريك برقم : '.$partnerPay->id;
            $transaction_result = $this->addPartnerTransaction(
                $partner->business_id,
                $partner->branch_id,
                $partnerPay->id,
                $user->id,
                $partnerPay->value,
                $partnerPay->currency_id,
                $t_text,
                '121000' ,
                '211000'
            );

            return $this -> showWithdrawalsPayment($partnerPay->partner_id);
        });
    }
}
