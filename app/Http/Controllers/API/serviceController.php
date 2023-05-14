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
            return ['status' => true,"message" =>"คุณมาเมื่อ ".$diffInDays." วันที่แล้ว"];

        } else {
            //"The time difference is not more than 5 days.";
            if ($diffInDays==2) {
                // return ['status' => false,"message" =>"คุณมาเมื่อวานนี้แล้ว ต้องมาในระยะไม่ติดต่อกัน 5 วัน"];
                return $diffInDays;
            }else if($diffInDays==1){
                return ['status' => false,"message" =>"คุณมาแล้วในวันนี้ ต้องมาในระยะไม่ติดต่อกัน 5 วัน"];
            }else{
                return ['status' => false,"message" =>"คุณมาเมื่อ ".$diffInDays." วันที่แล้ว ต้องมาในระยะไม่ติดต่อกัน 5 วัน"];
            }

        }


    }
    public function create_by_id($cid,$owner)
    {
        $checkhastrow=Owner::select('*')
        ->where('owner_cid',$owner)
        ->first();

        if (empty((array) $checkhastrow)) {
            return ['status' => false,"message" =>"unauthorized"];
        }

        else {
            $currentDate = Carbon::now()->toDateString();
            // เช็คว่าวันนี้เคยมาไหม
            $datatoday=Service::select('servicedate')
            ->where('servicedate',$currentDate)
            ->where('cid',$cid)
            ->get();
            // ถ้าเคยมาแล้วแสดงข้อมูลซ้ำ
            if (count($datatoday)>0) {
                $dataduplicate=Service::select('queue')
                ->where('servicedate',$currentDate)
                ->where('cid',$cid)
                ->orderBy('id', 'desc')
                ->first();
                return ['status' => false,"message" =>"คุณจองคิวไปล้วในวันนี้ คือคิวที่ ".$dataduplicate->queue,"queue"=>$dataduplicate->queue];
            }


            $currentDate = Carbon::now()->toDateString();
            // เช็คว่าวันนี้เคยมาไหม
            $datatoday=Service::select('queue')
            ->where('servicedate',$currentDate)
            ->orderBy('id', 'desc')
            ->first();


            $result=new Service();
            $result->cid=$cid;
            $result->queue=$datatoday->queue+1;
            $result->owner_cid=$owner;
            $result->servicedate=$currentDate;
            $result->create_at=date('Y-m-d H:i:s');
            $result=$result->save();

            // process queue



            return ['status' => true,"message" =>"จองคิวสำเร็จ","queue"=>$datatoday->queue+1];
        }



        //insertสำเร็จรีเทินคิว true
        //else false ต้องปี




    }


    public function print_queue($cid)
    {
        $GetQueue = Service::select('id','queue')
        ->where('cid',$cid)
        ->whereDate('servicedate',date("Y-m-d"))
        ->first();



        if($GetQueue != null){
            $data = [
                "status" => true,
                "system_queue_opd_id"=>$GetQueue->id ,
                "vn"=> $cid ,
                "hn"=> "" ,
                "qn"=> "" ,
                "main_dep"=> "" ,
                "queue"=> $GetQueue->queue,
                "queue_text"=> "",
                "department"=> "" ,
                "fullname"=> "" ,
                "ptname"=> "" ,
                "pttype"=> "",
                "system_queue_opd_date"=> "" ,
                "system_queue_opd_status"=> "",
                "created_at"=> "" ,
                "updated_at"=> "" ,
                "qn_barcode"=> ""
            ];
            return ["status"=>true,"data"=>$data];
        }else{

            $data = [
                "status" => false,
                "system_queue_opd_id"=> "" ,
                "vn"=> $cid ,
                "hn"=> "" ,
                "qn"=> "" ,
                "main_dep"=> "" ,
                "queue"=> $GetQueue->queue,
                "queue_text"=> "",
                "department"=> "" ,
                "fullname"=> "" ,
                "ptname"=> "" ,
                "pttype"=> "",
                "system_queue_opd_date"=> "" ,
                "system_queue_opd_status"=> "",
                "created_at"=> "" ,
                "updated_at"=> "" ,
                "qn_barcode"=> ""
            ];
            return ["status"=>false,"data"=>$data];
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
