<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Owner;

use Carbon\Carbon;

class serviceController extends Controller
{
    //

    
    public function searchbycid($cid)
    {   
        $datelastvisit=Service::select('servicedate')
        ->where('cid',$cid)
        ->orderBy('id', 'desc')
        ->first();
        // ถ้าไม่เคยมา เลยมาครั้งแรก
        
        if (empty((array) $datelastvisit)) {
            return ['status' => true,"message" =>"not visited in 5 day."];
        }

        $currentDate = Carbon::now()->toDateString();
        $datepay = Carbon::parse($datelastvisit['servicedate']);
        $diffInDays = $datepay->diffInDays($currentDate);

        if ($diffInDays >= 5) {
            //"The time difference is more than 5 days.";
            return ['status' => true,"message" =>"not visited in 5 day."];
           
        } else {
            //"The time difference is not more than 5 days.";
            return ['status' => false,"message" =>"visited in 5 day."];
        }


    }
    public function create_by_id($cid,$owner)
    {  
        $checkhastrow=Owner::select('*')
        ->where('owner_cid',$owner)
        ->first();

        if (empty((array) $checkhastrow)) {
            return ['status' => false,"message" =>"unauthorized"];
        } else {

            $currentDate = Carbon::now()->toDateString();

            $result=new Service();
            $result->cid=$cid;
            $result->owner_cid=$owner;
            $result->servicedate=$currentDate;
            $result->create_at=date('Y-m-d H:i:s');
            $result=$result->save();


            return ['status' => false,"message" =>"insert visit success"];
        }

       

        

    }
    

       
        
    
    public function create(Request $request)
    {   
        $user = Auth::user();

        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'plant_detail' => 'required',
            'plant_date' => 'required',//ใส่เอง
        ]);
         if ($validator->fails()) {
            return response()->json([
               'status'=>false,
               'error_message' => $validator->errors()->first(),
           ],422);
        }

        $result=new Plant();
        $result->id=$request->id;
        $result->plant_detail=$request->plant_detail;
        $result->plant_date=$request->plant_date;
        $result=$result->save();

        if($result){
            return response()->json(['success'=>true]);
        }else{
            return response()->json(['success'=>false]);
        }
    }
    public function get()
    {
        $result=Service::select('*')
        // ->where('id',$id)
        ->get();
       
        return response()->json($result);
    }

    public function delete($id)
    {
        $result = Plant::where('plant_id',$id)
        ->first();
        if (!$result) {
            return response()->json(['message' => 'plant row not found',"status"=>false], 404);
        }
        $result->delete();
        return response()->json(['message' => 'plant row deleted',"status"=>true]);
    }

}
