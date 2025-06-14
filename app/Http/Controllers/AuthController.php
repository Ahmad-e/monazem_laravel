<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

//php artisan make:controller

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'type_id' => 'required',
            'business_name' => 'required'
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type_id'=>$request->type_id,
            'img_url'=>$request->img_url,
            'phone_number'=>$request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $business = Business::create([
            'name' => $request->business_name,
            'description' => $request->business_description,
            'manager_id'=>$user->id,
        ]);

        $branch =  Branches::create([
            'name' => $request->branch_name ? $request->branch_name : $request->business_name,
            'description' => $request->branch_description,
            'contact_info' => $request->branch_contact_info,
            'business_id'=>$business->id,
            'manager_id'=>$user->id,
        ]);

        $user->business_id = $business->id ;
        $user->branch_id = $branch->id ;
        $user->save();

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
            'business' => $business,
            'branch' => $branch
        ], 201);
    }

    public function create_account(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $creator = Auth::user();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type_id'=>$request->type_id,
            'img_url'=>$request->img_url,
            'phone_number'=>$request->phone_number,
            'password' => Hash::make($request->password),
            'business_id' => $creator->business_id,
            'branch_id' => $request->branch_id
        ]);
        $users = User::where('business_id',$creator->business_id)->get();
        return response()->json([
            'user' => $user,
            'users' => $users
        ], 201);
    }

    public function showAccounts(){
        $creator = Auth::user();
        $users = User::where('business_id',$creator->business_id)->get();
        return response()->json([
            'users' => $users
        ], 201);
    }

    public function updateAccount(Request $request,$id)
    {

        $user = User::find($id);
        if(!$user){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no user id found",
            ], 404);
        }

        $creator = Auth::user();
        if($creator->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);




        try {
            $user->update($request->only([
                'name',
                'email',
                'phone_number',
                'branch_id'
            ]));
            $users = User::where('business_id',$creator->business_id)->get();
            return response()->json([
                'user'=>$user,
                'users' => $users
            ], 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to update Account'], 500);
        }
    }
    public function toggleOverPowerUser($id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no user id found",
            ], 404);
        }

        $creator = Auth::user();
        if($creator->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);

        $user->overPower = !$user->overPower;
        $user->save();

        $users = User::where('business_id',$creator->business_id)->get();
        return response()->json([
            'user'=>$user,
            'users' => $users
        ], 201);
    }

    public function toggleBlockUser($id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no user id found",
            ], 404);
        }

        $creator = Auth::user();
        if($creator->business_id != $user->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This account not related to your business",
            ], 402);

        $user->blocked = !$user->blocked;
        $user->save();

        $users = User::where('business_id',$creator->business_id)->get();
        return response()->json([
            'user'=>$user,
            'users' => $users
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'token' => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getUser()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to fetch user profile'], 500);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $user = Auth::user();
            $user->update($request->only([
                'name',
                'email',
                'phone_number',
                'branch_id'
            ]));
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

    public function test()
    {
        return response()->json(['message' => 'Successfully test']);
    }

}
