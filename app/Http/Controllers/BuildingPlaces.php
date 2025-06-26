<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Branches;
use Illuminate\Http\Request;
use App\Models\Buildings;
use App\Models\Places;
use Illuminate\Support\Facades\Auth;


class BuildingPlaces extends Controller
{
    public function showBuildingsByBranchId($id){
        $branch = Branches::find($id);
        $data = Buildings::where('branch_id',$id)->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
            'branch' =>$branch
        ], 201);
    }
    public function showBuildings($id){
        $branch = Branches::find($id);
        if(!$branch)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This Products not related to your business",
            ], 402);

        return $this->showBuildingsByBranchId($id);

    }

    public function addBuildings(Request $request){
        $request->validate([
            'type' => 'required',
            'branch_id' => 'required'
        ]);
        $branch = Branches::find($request->branch_id);
        if(!$branch)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No branch by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This branch not related to your business",
            ], 402);

        Buildings::create([
            'name' => $request->name,
            'type' => $request->type,
            'branch_id' => $request->branch_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'creator_id' => $user->id,
        ]);

        return $this->showBuildingsByBranchId($request->branch_id);
    }
    public function changeBuildings(Request $request , $id){
        $building = Buildings::find($id);
        $branch = Branches::find($building->branch_id);
        if (!$building) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No building by this ID",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This building not related to your business",
            ], 402);

        $building->update($request->only([
            'name',
            'type' ,
            'branch_id',
            'latitude',
            'longitude'
        ]));

        return $this->showBuildingsByBranchId($branch->id);
    }
    public function toggleBlockBuildings($id){

        $building = Buildings::find($id);

        if(!$building){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no product id found",
            ], 404);
        }

        $user = Auth::user();
        $branch = Branches::find($building->branch_id);
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This product not related to your business",
            ], 402);

        $building->blocked = !$building->blocked;
        $building->save();

        return $this->showBuildingsByBranchId($building->branch_id);
    }

    //******************************
    //   start places
    //******************************

    public function showPlacesByBuildingId($id){
        $buildings = Buildings::find($id);
        $data = Places::where('building_id',$id)->get();

        return response()->json([
            'state' => 200,
            'data' => $data,
            'building' =>$buildings
        ], 201);
    }
    public function showPlaces($id){
        $building = Buildings::find($id);

        if(!$building)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No Products by this ID",
            ], 404);
        $branch = Branches::find($building->branch_id);
        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This place not related to your business",
            ], 402);

        return $this->showPlacesByBuildingId($id);

    }

    public function addPlace(Request $request){
        $request->validate([
            'building_id' => 'required'
        ]);

        $building = Buildings::find($request->building_id);
        $branch = Branches::find($building->branch_id);


        if(!$building)
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No building by this ID",
            ], 404);

        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This building not related to your business",
            ], 402);

        Places::create([
            'name' => $request->name,
            'floor_number' => $request->floor_number,
            'room_number' => $request->room_number,
            'shelves_alphabet' => $request->shelves_alphabet,
            'building_id' => $request->building_id,
            'creator_id' => $user->id,
        ]);

        return $this->showPlacesByBuildingId($request->building_id);
    }
    public function changePlace(Request $request , $id){
        $place = Places::find($id);

        if (!$place) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No place by this ID",
            ], 404);
        }

        $building = Buildings::find($place->building_id);
        $branch = Branches::find($building->branch_id);
        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This place not related to your business",
            ], 402);

        $place->update($request->only([
            'name',
            'floor_number',
            'room_number',
            'shelves_alphabet',
            'building_id' ,
        ]));

        return $this->showPlacesByBuildingId($building->id);
    }
    public function toggleBlockPlaces($id){

        $place = Places::find($id);

        if (!$place) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No place by this ID",
            ], 404);
        }

        $building = Buildings::find($place->building_id);
        $branch = Branches::find($building->branch_id);
        $user = Auth::user();
        if($user->business_id != $branch->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 3 ,
                'message'=>"This place not related to your business",
            ], 402);

        $place->blocked = !$place->blocked;
        $place->save();

        return $this->showPlacesByBuildingId($place->building_id);
    }

}
