<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Employees;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function addBusiness(Request $request){

        $request->validate([
            'name' => 'required',
            'manager_id' => 'required',
        ]);

        if(User::where('id',$request->manager_id)->exists()){

            Business::create([
                'name' => $request->name,
                'description' => $request->description,
                'manager_id'=>$request->manager_id,
            ]);

            $data = Business::get();
            return response()->json([
                'state' => 200,
                'message'=>"Added successfully",
                'data' => $data,
            ], 201);
        }

        return response()->json([
            'state' => 404,
            'message'=>"no user found",
        ], 404);
    }

    public function showAllBusiness(){
        $data = Business::get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function showBusiness(){
        $user = Auth::user();
        $data = Business::where('manager_id',$user->id)-> get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }

    public function updateBusiness(Request $request)
    {
        $user = Auth::user();
        $business = Business::find($user->business_id);

        if (!$business) {
            return response()->json([
                'state' => 404,
                'error'=> 3 ,
                'message' => "Business not found",
            ], 404);
        }

        $business->update($request->only([
            'name',
            'description'
        ]));

        return response()->json([
            'state' => 200,
            'message' => "business updated successfully",
            'data' => $business,
        ]);
    }
}
