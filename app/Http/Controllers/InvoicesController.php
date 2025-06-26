<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoices;
use App\Models\Internal_depts;
use App\Models\Invoices_products;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;


class InvoicesController extends Controller
{
    public function showInvoicesByBusiness_id($id){
        $data=Invoices::where('business_id',$id)->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function showInvoices(){
        $user = Auth::user();
        return $this->showInvoicesByBusiness_id($user->business_id);
    }
    public function addInvoices(Request $request){
        $request->validate([
            'type' => 'required',
            'payment_status' => 'required',
            'unDiscounted_amount' => 'required',
            'discounted_amount' => 'required',
            'tax_amount' => 'required',
            'shipping_cost' => 'required',
            'refunded_amount' => 'required',
            'affect_refund' => 'required',
            'paid_amount' => 'required',
            'currency_id' => 'required',
            'invoices_items' => 'required',
            'invoices_items.*.products_id' => 'required|integer',
            'invoices_items.*.products_count' => 'required|numeric',
            'invoices_items.*.products_total_price' => 'required|numeric',
            'invoices_items.*.count_unit_id' => 'required|integer',
            'invoices_items.*.count_for_each_place' => 'required|numeric',
            'invoices_items.*.product_places_id' => 'required',
            'invoices_items.*.tax_amount' => 'required|numeric',
        ]);

        $user = Auth::user();

        $lastInvoice = Invoices::where('creator_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastInvoice && $lastInvoice->created_at->gt(Carbon::now()->subSeconds(5))) {
            // إذا أنشأ فاتورة قبل أقل من 5 ثوان
            return $this->showInvoicesByBusiness_id($user->business_id);
        }

        $invoice= Invoices::create([
            'type' => $request->type,
            'payment_status' => $request->payment_status,
            'unDiscounted_amount' => $request->unDiscounted_amount,
            'discounted_amount' => $request->discounted_amount,
            'paid_amount' => $request->paid_amount,
            'shipping_cost' => $request->shipping_cost,
            'refunded_amount' => $request->refunded_amount,
            'affect_refund' => $request->affect_refund,
            'tax_amount' => $request->tax_amount,
            'note' => $request->note,
            'number' => $request->number,
            'amount_in_base' => $request->amount_in_base,
            'shipping_cost_in_base' => $request->shipping_cost_in_base,
            'date' => $request->date,
            'branch_id' => $request->branch_id,
            'partner_id' =>$request->partner_id,
            'client_id' =>$request->client_id,
            'currency_id' => $request->currency_id,
            'business_id' =>$user->business_id,
            'creator_id' => $user->id,
        ]);

        if($request->payment_status=="unpaid"){
            Internal_depts::create([
                'total' => $request->discounted_amount,
                'paid' => $request->debts_paid_amount ? $request->debts_paid_amount : 0,
                'remaining' => $request->discounted_amount - $request->debts_paid_amount,
                'type' => 'Debit',
                'state' => 'unpaid',
                'invoice_id' => $invoice->id,
                'client_id' => $request->client_id,
                'business_id' => $user->business_id,
                'branch_id' => $request->branch_id,
                'currency_id'=>$request->currency_id,
                'creator_id' => $user->id
            ]);
        }

        foreach ($request->invoices_items as $line) {

            if($line['prices_id']==0){
//                create price
            }

//            Invoices::create([
//                'ar_name' => $powerData['ar_name'],
//                'en_name' => $powerData['en_name'],
//                'level' => $powerData['level']
//            ]);
        }

    return $this->showInvoicesByBusiness_id($user->business_id);

    }

    public function changInvoices(Request $request){

    }
}
