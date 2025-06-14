<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Business;
use App\Models\Employees;
use App\Models\Powers;
use Illuminate\Http\Request;
use App\Models\Clients;
use Illuminate\Support\Facades\Auth;
use function Carbon\this;

class ClientController extends Controller
{

    public function showClientsByBusiness(){
        $user = Auth::user();
        $bus = Business::find($user->business_id);

        $data = Clients::where('clients.business_id',$bus->id)
//            ->join("branches" , 'branches.id' ,'clients.branch_id' )
            ->get(
//                [
//                "clients.id",
//                "branches.name as branches_name",
//                "clients.name as clients_name",
//                "phone_number",
//                "email",
//                "address",
//                "note",
//                "blocked",
//                "type",
//                "clients.business_id",
//                "branch_id",
//                "creator",
//                "clients.created_at",
//                "clients.updated_at",
//                "description",
//            ]
            );

        return response()->json([
            'state' => 200,
            'business'=>$bus,
            'data' => $data,
        ], 200);
    }

    public function showClientsByBranches($id){
        $branch = Branches::find($id);
        $data = Clients::where('branch_id',$id)
            ->get();

        return response()->json([
            'state' => 200,
            'branch'=>$branch,
            'data' => $data,
        ], 200);
    }


    public function addClient(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        Clients::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address' => $request->address,
            'note' => $request->note,
            'type' => $request->type,
            'business_id' => $user->business_id,
            'branch_id' => $request->branch_id,
            'creator' => $user->id
        ]);
        if($request->branch_id == null)
            return $this->showClientsByBusiness($request->business_id);
        else
            return $this->showClientsByBranches($request->branch_id);
    }

    public function updateClient(Request $request, $id){
        $employee = Clients::find($id);

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 3 ,
                'message' => "Client not found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $employee->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This Client not related to your business",
            ], 402);

        if($request->branch_id != null)
            if(!(Branches::where('id',$request->branch_id)->exists())){
                return response()->json([
                    'state' => 404,
                    'error'=> 2 ,
                    'message'=>"no branch id found",
                ], 404);
            }


        $employee->update($request->only([
            'name',
            'phone_number',
            'email',
            'address',
            'note',
            'type',
            'branch_id'
        ]));

        if($employee->branch_id == null)
            return $this->showClientsByBusiness($employee->business_id);
        else
            return $this->showClientsByBranches($employee->branch_id);
    }

    public function toggleBlockedClient($id)
    {
        $employee = Clients::find($id);
        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 3 ,
                'message' => "Client not found",
            ], 404);
        }
        $user = Auth::user();
        if($user->business_id != $employee->business_id)
            return response()->json([
                'state' => 402,
                'error'=> 4 ,
                'message'=>"This Client not related to your business",
            ], 402);

        // عكس القيمة

        $employee->blocked = !$employee->blocked;
        $employee->save();

        if($employee->branch_id == null)
            return $this->showClientsByBusiness($employee->business_id);
        else
            return $this->showClientsByBranches($employee->branch_id);
    }
}
