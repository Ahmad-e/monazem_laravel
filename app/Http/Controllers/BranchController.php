<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Branches;
use App\Models\Business;


class BranchController extends Controller
{
    public function showAllBranches(){

        $data = Branches::join('businesses as bus','bus.id','branches.business_id' )
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
                'users.name as manager_id',
        ]);
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function addBranch(Request $request){

        $validatedData = $request->validate([
            'business_id' => 'required',
            'name' => 'required',
            'manager_id' => 'required'
        ]);

        if(!(Business::where('id',$request->business_id)->exists())){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no business id found",
            ], 404);
        }

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
                'business_id'=>$request->business_id,
                'manager_id'=>$request->manager_id,
            ]);

            $data = Branches::where('business_id',$request->business_id)->join('businesses as bus','bus.id','branches.business_id' )
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
                    'users.name as manager_id',
                ]);
            return response()->json([
                'state' => 200,
                'message'=>"Added successfully",
                'data' => $data,
            ], 201);
        }
}
