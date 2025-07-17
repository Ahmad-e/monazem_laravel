<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    public function showAccountByBusinessId($id){
        $data = Accounts::where('business_id',$id)
            ->with('partner')->get();
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

    public function importAccountTree($businessId)
    {
        if (!$businessId) {
            return response()->json(['error' => 'يجب توفير business_id'], 400);
        }

        // تحميل ملف JSON من مجلد التخزين أو resources (هنا نستخدم public_path لسهولة العرض)
        $jsonPath = public_path('data\accounts_tree.json');

        if (!file_exists($jsonPath)) {
            return response()->json(['error' => 'الملف غير موجود'], 404);
        }

        $json = file_get_contents($jsonPath);
        $accounts = json_decode($json, true);

        DB::beginTransaction();
        try {
            foreach ($accounts as $item) {
                Accounts::create([
                    'business_id' => $businessId,
                    'name'        => $item['accounts_name'],
                    'code'        => (string)$item['accounts_code'],
                    'nature'      => $item['accounts_nature'],
                    'statement'   => $item['accounts_statement'],
                    'level'       => $item['accounts_level'],
                    'is_sub'      => $item['accounts_is_sub'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'تم استيراد شجرة الحسابات بنجاح.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'حدث خطأ أثناء الاستيراد', 'details' => $e->getMessage()], 500);
        }

    }
}
