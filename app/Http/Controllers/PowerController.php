<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Powers;
use App\Models\Powers_users;
use Illuminate\Support\Facades\Auth;


class PowerController extends Controller
{
    public function addPower(Request $request){
        $request->validate([
            'ar_name' => 'required',
            'en_name' => 'required',
            'level' => 'required'
        ]);

        Powers::create([
            'ar_name' => $request->ar_name,
            'en_name' => $request->en_name,
            'level' => $request->level
        ]);

         $data = Powers::get();

        return response()->json([
            'state' => 200,
            'message'=>"Added successfully",
            'data' => $data,
        ], 201);
    }
    public function getPowers(){
        $data = Powers::get();

        return response()->json([
            'state' => 200,
            'message'=>"Added successfully",
            'data' => $data,
        ], 201);
    }
    public function updatePower(Request $request, $id){

        $power = Powers::find($id); // العثور على السجل بناءً على id

        if (!$power) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Power id not found",
            ], 404);
        }

        $power->update($request->only([
            'ar_name',
            'en_name',
            'level',
        ]));

        $data = Powers::get();

        return response()->json([
            'state' => 200,
            'message' => "Updated successfully",
            'data' => $data,
        ], 200);
    }
    public function toggleBlocked($id){
        $power = Powers::findOrFail($id); // العثور على السجل بناءً على id
        $power->blocked = !$power->blocked; // عكس القيمة
        $power->save(); // حفظ التغييرات

        return response()->json([
            'state' => 200,
            'message' => "Blocked state toggled successfully",
            'data' => $power,
        ], 200);
    }


    //*******************************
    //  start user _ powers
    //*******************************

    public function addUserPower (Request $request){
        $request->validate([
            'user_id' => 'required',
            'power_id' => 'required'
        ]);
        $user = Auth::user();
        $user_power = User::find($request->user_id);

        if(!$user_power){
            return response()->json([
                'state' => 404,
                'error'=> 1,
                'message'=>"no user id found",
            ], 404);
        }
        if($user->business_id != $user_power->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This user not related to your business",
            ], 402);

        if(!(Powers::where('id',$request->power_id)->exists())){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no power id found",
            ], 404);
        }

        if(Powers_users::where('user_id',$request->user_id)-> where('power_id',$request->power_id)->exists()){
            return response()->json([
                'state' => 201,
                'error'=> 0 ,
                'message'=>"this power is exists",
            ], 201);
        }

        Powers_users::create([
            'user_id' => $request->user_id,
            'power_id' => $request->power_id,
        ]);

        $data = Powers_users:: where('user_id',$request->user_id)
            ->join( 'powers' , 'powers.id' , 'powers_users.power_id'  )
            ->join( 'users' , 'users.id' , 'powers_users.user_id'  )
            ->get([
                'powers.en_name as power_en_name',
                'powers.ar_name as power_ar_name',
                'powers.level',
                'powers.id as powers_id',
                'users.id as user_id',
                'users.name as user_name'
            ]);
        return response()->json([
            'state' => 200,
            'data'=>$data
        ], 200);
    }

    public function deleteUserPower (Request $request){
        $request->validate([
            'user_id' => 'required',
            'power_id' => 'required'
        ]);

        $user = Auth::user();
        $user_power = User::find($request->user_id);

        if(!$user_power){
            return response()->json([
                'state' => 404,
                'error'=> 1,
                'message'=>"no user id found",
            ], 404);
        }
        if($user->business_id != $user_power->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This user not related to your business",
            ], 402);


        if(!(Powers::where('id',$request->power_id)->exists())){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no power id found",
            ], 404);
        }

        Powers_users::where('user_id',$request->user_id)-> where('power_id',$request->power_id)->delete();

        $data = Powers_users:: where('user_id',$request->user_id)
            ->join( 'powers' , 'powers.id' , 'powers_users.power_id'  )
            ->join( 'users' , 'users.id' , 'powers_users.user_id'  )
            ->get([
                'powers.en_name as power_en_name',
                'powers.ar_name as power_ar_name',
                'powers.level',
                'powers.id as powers_id',
                'users.id as user_id',
                'users.name as user_name'
            ]);
        return response()->json([
            'state' => 200,
            'message'=>'deleted',
            'data'=>$data
        ], 200);
    }

    public function showUser_Power ($id){
        $data = Powers_users:: where('user_id',$id)
            ->join( 'powers' , 'powers.id' , 'powers_users.power_id'  )
            ->join( 'users' , 'users.id' , 'powers_users.user_id'  )
            ->get([
                'powers.en_name as power_en_name',
                'powers.ar_name as power_ar_name',
                'powers.level',
                'powers.id as powers_id',
                'users.id as user_id',
                'users.name as user_name'
            ]);
        return response()->json([
            'state' => 200,
            'data'=>$data
        ], 200);
    }
}
