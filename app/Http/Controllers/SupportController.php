<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Support;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;


class SupportController extends Controller
{
    public function showUserFeedback(){
        $user = Auth::user();
        $data = Feedback::where('user_id',$user->id)->get();
        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }
    public function showAdminFeedback(){
        $data = Feedback::get();
        Feedback::query()->update(['seen' => true]);

        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }
    public function addFeedback(Request $request){
        $request->validate([
            'text' => 'required'
        ]);

        $user = Auth::user();
        Feedback::create([
            'text' => $request->text,
            'seen' => false,
            'user_id' => $user->id,
        ]);
        return $this->showUserFeedback();
    }
    public function deleteFeedback($id){
        $feedback = Feedback::find($id);
        if (!$feedback) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No place by this ID",
            ], 404);
        }

        $feedback->delete();
        return $this->showAdminFeedback();
    }

    //******************************
    //   start support
    //******************************

    public function showUserSupport(){
        $user = Auth::user();
        $data = Support::where('user_id',$user->id)->get();
        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }

    public function showAdminSupport(){
        $data = Support::get();
        Support::query()->update(['seen' => true]);

        return response()->json([
            'state' => 200,
            'data' => $data
        ], 201);
    }

    public function addSupport(Request $request){
        $request->validate([
            'text' => 'required'
        ]);

        $user = Auth::user();
        Support::create([
            'text' => $request->text,
            'seen' => false,
            'user_id' => $user->id,
        ]);
        return $this->showUserSupport();
    }

    public function addReSupport(Request $request , $id){
        $request->validate([
            'reText' => 'required'
        ]);
        $support = Support::find($id);
        if (!$support) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No support by this ID",
            ], 404);
        }

        $support->reText=$request->reText;
        $support->save();

        return $this->showAdminSupport();

    }

    public function deleteSupport($id){
        $support = Support::find($id);
        if (!$support) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No support by this ID",
            ], 404);
        }

        $support->delete();

        return $this->showAdminSupport();
    }
}
