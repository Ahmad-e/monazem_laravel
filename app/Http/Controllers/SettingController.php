<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function showSettings(){
        $user = Auth::user();
        $data = Settings::where('business_id',$user->business_id)->get();
        return response()->json([
            'state' => 200,
            'data' => $data,
        ], 201);
    }
    public function addSettings(Request $request){
        $user = Auth::user();
        $request->validate([
            'key' => 'required'
        ]);

        Settings::create([
            'key' => $request->key,
            'value' => $request->value,
            'branch_id' => $request->branch_id,
            'business_id' => $user->business_id
        ]);
        return $this->showSettings();
    }
    public function changeSettings(Request $request,$id){

        $setting = Settings::find($id);
        if(!$setting)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No setting by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $setting->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This setting not related to your business",
            ], 402);

        $setting->update($request->only([
            'key',
            'value',
            'branch_id',
        ]));
        return $this->showSettings();
    }
    public function deleteSettings($id){

        $setting = Settings::find($id);
        if(!$setting)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No setting by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $setting->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This setting not related to your business",
            ], 402);

        $setting->delete();

        return $this->showSettings();
    }
}
