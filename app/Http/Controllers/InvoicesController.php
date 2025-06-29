<?php

namespace App\Http\Controllers;

use App\Models\Places;
use App\Models\Product_places;
use App\Models\Products_prices;
use App\Models\Stock;
use App\Models\Transactions;
use App\Models\Transactions_lines;
use Illuminate\Http\Request;
use App\Models\Invoices;
use App\Models\Internal_depts;
use App\Models\Invoices_products;
use App\Models\Batches;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;


class InvoicesController extends Controller
{
    public function showInvoicesByBusiness_id($id){
        $data=Invoices::where('business_id',$id)->get();
        $invoices = Invoices::where('business_id',$id)
            ->with(['products'])
            ->get();
        return response()->json([
            'state' => 200,
            'data' => $invoices,
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
            'invoices_items.*.product_id' => 'required|integer',
            'invoices_items.*.products_count' => 'required|numeric',
            'invoices_items.*.total_product_price' => 'required|numeric',
            'invoices_items.*.products_places_id' => 'required',
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
            'original_invoice_id'=>$request->original_invoice_id,
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

        $transaction = Transactions::create([
            'description' => ($request->type == "sell" ? " فاتورة بيع " : " فاتورة شراء ") . " _ رقم " . $invoice->id  ,
            'reference_number_type' => 'invoice',
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'business_id' =>$user->business_id,
            'creator_id' => $user->id,
        ]);

        if($request->payment_status=="unpaid" || $request->payment_status=="partial"){
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

            // إنشاء السعر في حال لم يكن موجود
            $price = null;
            if($line['prices_id']==0){
                $price = Products_prices::create([
                    'price' => $line['price_data']['price'],
                    'categories' => $line['price_data']['prices_type'],
                    'product_id' => $line['product_id'],
                    'partner_ar' => $line['price_data']['prices_partner_ar'],
                    'partner_en' => $line['price_data']['prices_partner_en'],
                    'currency_id' => $request->currency_id,
                    'creator_id' => $user->id,
                ]);
            }
            // إنشاء أقلام الفاتورة
            $new_line_data = Invoices_products::create([
                'product_id' => $line['product_id'],
                'invoice_id' => $invoice->id,
                'products_count' => $line['products_count'],
                'total_product_price' => $line['total_product_price'],
                'tax_amount' => $line['tax_amount'],
                'products_price_id' => $line['prices_id'] == 0 ? $price->id : $line['prices_id'] ,
                'place_id' => $line['products_places_id'],
                'currency_id' => $request->currency_id
            ]);

            Transactions_lines::create([
                'description' => ($request->type == "sell" ? " فاتورة بيع " : " فاتورة شراء ") . " _ رقم " . $invoice->id  ,
                'debit_credit' => $line['debit_credit'],
                'amount' => $line['total_product_price'],
                'account_id' => $line['account_id'],
                'client_id' => $request->client_id,
                'partner_id' => $request->partner_id,
                'transaction_id' => $transaction->id,
                'currency_id' => $request->currency_id
            ]);

            $baches = Batches::create([
                'invoices_products_id' => $new_line_data->id,
                'expiration_date' => $request->date,
                'unit_cost' => $line['total_product_price']/$line['products_count'],
                'products_prices_id' => $line['prices_id'] == 0 ? $price->id : $line['prices_id'] ,
                'currency_id' => $request->currency_id
            ]);

            Product_places::create([
                'count' => $line['products_count'],
                'place_id' => $line['products_places_id'],
                'batches_id' => $baches->id,
                'product_id' => $line['product_id'],
                'unit_id' => $line['unit_id'],
            ]);

            $stock = Stock::where('product_id',$line['product_id'])->first();
            if(!$stock){
                $place = Places::find($line['products_places_id']);
                Stock::create([
                    'count' => $line['products_count'],
                    'building_id' => $place->building_id,
                    'place_id' => $line['products_places_id'],
                    'product_id' => $line['product_id'],
                    'products_price_id' => $line['prices_id'] == 0 ? $price->id : $line['prices_id'] ,
                    'unit_id' => $line['unit_id'],
                    'date' => $request->date,
                ]);
            }
            else{
                if($request->type == "sell" || $request->type == "buyRefund" )
                    $stock->count = $stock->count - $line['products_count'] ;
                else
                    $stock->count = $stock->count + $line['products_count'] ;

                $stock->save();
            }
        }

        return $this->showInvoicesByBusiness_id($user->business_id);

    }

    public function changInvoices(Request $request){

    }
}
