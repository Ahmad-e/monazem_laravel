<?php

namespace App\Http\Controllers;

use App\Models\Buildings;
use App\Models\Places;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Branches;
use App\Models\Business;
use App\Models\Main_branch_business;

use App\Models\Cashes;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function showAllBranches(){
        $user = Auth::user();
        $data = Branches::where('branches.business_id',$user->business_id)
            ->join('businesses as bus','bus.id','branches.business_id' )
            ->join( 'users','users.id','branches.manager_id' )
            ->get([
                'bus.name as business_name',
                'bus.id as business_id',
                'branches.name as branches_name',
                'branches.id as branches_id',
                'branches.description',
                'contact_info',
                'blocked_branch',
                'users.name as manager_name',
                'users.id as manager_id',
                "branches.description",
                "blocked_branch",
                "branches.created_at",
                "branches.updated_at",
            ]);
        $main_branch = Main_branch_business::where('business_id',$user->business_id)->first();
        return response()->json([
            'state' => 200,
            'data' => $data,
            'main_branch'=>$main_branch
        ], 201);
    }
    public function addBranch(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'manager_id' => 'required'
        ]);

        if(!(User::where('id',$request->manager_id)->exists())){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no user id found",
            ], 404);
        }


            Branches::create([
                'name' => $request->name,
                'description' => $request->description,
                'contact_info' => $request->contact_info,
                'business_id'=>$user->business_id,
                'manager_id'=>$request->manager_id,
            ]);

            return $this->showAllBranches();
        }
    public function changeBranch(Request $request , $id){
        $user = Auth::user();
        $branch = Branches::find($id);
        if(!$branch)
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no branch id found",
            ], 404);
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This cash not related to your business",
            ], 402);

        $branch->update($request->only([
            'name',
            'description',
            'contact_info',
            'contact_info',
        ]));

        return $this->showAllBranches();
    }

    public function setMainBranch($id){
        $user = Auth::user();
        $branch = Branches::find($id);

        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This branch not related to your business",
            ], 402);

        $main_branch=Main_branch_business::where('business_id',$user->business_id)->first();
        if(!$main_branch){
            $main_branch = Main_branch_business::create([
                'business_id' => $user->business_id,
                'branch_id' => $branch->id
            ]);
        }
        else{
            $main_branch->branch_id = $id;
            $main_branch->save();
        }
        return response()->json([
            'state' => 200,
            'data' => $main_branch
        ], 201);
    }
    //******************************
    //   start cashes
    //******************************

    public function showCashesByBranchId ($id){
        $branch = Branches::find($id);
        $data = Cashes::where('branch_id',$id)
            ->join("currencies" , 'currencies.id' ,'Cashes.currency_id' )
            ->get([
                "cashes.id as cashes_id",
                "Balance",
                "note",
                "branch_id",
                "manager_id",
                "currency_id",
                "cashes.created_at",
                "cashes.updated_at",
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
            'branch' =>$branch
        ], 201);
    }

    public function showCashes ($id){

        $branch = Branches::find($id);
        if (!$branch) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No branch by this ID",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This building not related to your business",
            ], 402);

        return $this->showCashesByBranchId($id);
    }

    public function addCash (Request $request){
        $request->validate([
            'Balance' => 'required',
            'currency_id' => 'required',
            'branch_id' => 'required'
        ]);
        $branch = Branches::find($request->branch_id);
        if (!$branch) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No branch by this ID",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This branch not related to your business",
            ], 402);

        Cashes::create([
            'Balance' => $request->Balance,
            'note' => $request->note,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'manager_id' => $request->manager_id
        ]);

        return $this->showCashesByBranchId($request->branch_id);
    }
    public function ChangeCash (Request $request , $id){
        $cash = Cashes::find($id);

        if (!$cash) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No cash by this ID",
            ], 404);
        }

        $branch = Branches::find($cash->branch_id);
        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This cash not related to your business",
            ], 402);

        $cash->update($request->only([
            'Balance',
            'note',
            'branch_id',
            'currency_id',
            'manager_id'
        ]));

        return $this->showCashesByBranchId($request->branch_id);
    }

    public function DeleteCash ($id){

        $cash = Cashes::find($id);

        if (!$cash) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No cash by this ID",
            ], 404);
        }

        $branch = Branches::find($cash->branch_id);
        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This cash not related to your business",
            ], 402);

        $cash->delete();


        return $this->showCashesByBranchId($branch->id);
    }
}
