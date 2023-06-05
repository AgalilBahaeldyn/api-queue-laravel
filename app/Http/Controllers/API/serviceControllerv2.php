<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTimeZone;

date_default_timezone_set('Asia/Bangkok');

class serviceControllerv2 extends Controller
{
    //

    
    public function getreport($sdate,$edate)
    {
        $result=Service::select('*')
        ->whereBetween('servicedate', [$sdate, $edate])
        ->get();

        return $result;
    }
    


    public function serivceperday($date)
    {

        $result=Service::select('*')
        ->where('servicedate',$date)
        ->orderBy('id', 'desc')
        ->get();

        return response()->json($result);
    }


    public function searchbycid($cid,$age)
    {
        
       date_default_timezone_set('Asia/Bangkok');
        // $currentDateTime = date('Y-m-d H:i:s');
        // return $currentDateTime;
        
        if (!is_numeric($cid) && strpos($cid, '.') === false) {
            return ['status' => false,"message" =>"บัตรประชาชนต้องเป็นตัวเลขเท่านั้น"];
        } 

        if (strlen($cid) != 13) {
            return ['status' => false,"message" =>"เลขบัตรประชาชนต้องมี13หลัก"];
        }
        
        $agelength = strlen($age);
        if ($agelength>4) {
            $ageold = Carbon::parse($age)->age;
            if ($ageold<15){
                return ['status' => false,"message" =>"อายุน้อยยกว่า 15 ปีบริบูรณ์ไม่สามารถจองคิวได้"];
            }

        }else{
            if ($age<15) {
                return ['status' => false,"message" =>"อายุน้อยยกว่า 15 ปีบริบูรณ์ไม่สามารถจองคิวได้"];
            }
        }



        $day=DB::table('day')
            ->select('day')
            ->first();


        $datelastvisit=Service::select('servicedate','create_at')
        ->where('cid',$cid)
        ->orderBy('id', 'desc')
        ->first();
        // ถ้าไม่เคยมา เลยมาครั้งแรก

        if (empty((array) $datelastvisit)) {
            return ['status' => true,"message" =>"not visited in ".$day->day." วัน"];
        }

        $currentDate = Carbon::now()->toDateString();
        $datepay = Carbon::parse($datelastvisit['servicedate'])->format('Y-m-d');
        $carbonDatepay = Carbon::parse($datepay);
        
        $diffInDays = $carbonDatepay->diffInDays($currentDate);

        if ($diffInDays >= $day->day) {
            //"The time difference is more than 5 days.";
            return ['status' => true,"message" =>"คุณมาเมื่อ ".$diffInDays."วัน เวลา".$datelastvisit->create_at." วันที่แล้วสามารถจองคิวได้"];

        } else {
            //"The time difference is not more than 5 days.";
            if ($diffInDays==1) {
                return ['status' => false,"message" =>"คุณมาแล้ว".$diffInDays."วัน เวลา".$datelastvisit->create_at."ต้องมาในระยะไม่ติดต่อกัน ".$day->day." วัน"];
                // return $diffInDays;
            }else if($diffInDays==0){
                // return $datepay;
                return ['status' => false,"message" =>"คุณมาแล้ว".$diffInDays."วัน เวลา".$datelastvisit->create_at."ต้องมาในระยะไม่ติดต่อกัน ".$day->day." วัน"];
            }else{
                return ['status' => false,"message" =>"คุณมาแล้ว ".$diffInDays."วัน เวลา".$datelastvisit->create_at." ต้องมาในระยะไม่ติดต่อกัน ".$day->day." วัน"];
            }
        }


    }
    public function create_by_id($cid,$owner,$age,$keyin,$fullname=null)
    {
        date_default_timezone_set('Asia/Bangkok');

        
        if (intval($age)<15) {
            return ['status' => false,"message" =>"อายุน้อยยกว่า 15 ปีบริบูรณ์ไม่สามารถจองคิวได้"];
        }

        if (!is_numeric($cid) && strpos($cid, '.') === false) {
            return ['status' => false,"message" =>"บัตรประชาชนต้องเป็นตัวเลขเท่านั้น"];
        } 

        if (strlen($cid) != 13) {
            return ['status' => false,"message" =>"เลขบัตรประชาชนต้องมี13หลัก"];
        }
        // $sum = 0;
        // for ($i = 0; $i < 12; $i++) {
        //     $sum += intval($cid[$i]) * (13 - $i);
        // }
        // if ((11 - ($sum % 11)) % 10 == intval($cid[12])) {
        //     return ['status' => false];
        // }
        
        

       
          


        $agelength = strlen($age);
        if ($agelength>4) {
            $age = (substr($age,0,4) - 543);
           
            $ageold = (date("Y") - $age);
            // $ageold = Carbon::parse($age)->age;
        }else{
            $ageold=$age;
        }


        $bangkokTimeZone = new DateTimeZone('Asia/Bangkok');
        Carbon::setTestNow(Carbon::now($bangkokTimeZone));


        $checkhastrow=Owner::select('*')
        ->where('owner_cid',$owner)
        ->first();

        if (empty((array) $checkhastrow)) {
            return ['status' => false,"message" =>"unauthorized"];
        }

        else {
            $currentDate = Carbon::now()->toDateString();
            // เช็คว่าวันนี้เคยมาไหม
            $datatoday=Service::select('servicedate','catagory')
            ->where('servicedate',$currentDate)
            ->where('cid',$cid)
            ->get();
            // ถ้าเคยมาแล้วแสดงข้อมูลซ้ำ
            if (count($datatoday)>0) {
                $dataduplicate=Service::select('queue','catagory','fullname','age')
                ->where('servicedate',$currentDate)
                ->where('cid',$cid)
                ->orderBy('id', 'desc')
                ->first();

                if (!$fullname||$fullname==null||$fullname=='') {
                    return ['status' => false,"message" =>"คุณจองคิวไปล้วในวันนี้ คือคิวที่ ".$dataduplicate->queue,"queue"=>$dataduplicate->catagory.":".$dataduplicate->queue,
                    "age"=>$dataduplicate->age];
                }else{
                    return ['status' => false,"message" =>"คุณจองคิวไปล้วในวันนี้ คือคิวที่ ".$dataduplicate->queue,"queue"=>$dataduplicate->catagory.":".$dataduplicate->queue,
                "fullname"=>$dataduplicate->fullname,"age"=>$dataduplicate->age];
                }


            }




            $currentDate = Carbon::now()->toDateString();
            // เช็คว่าวันนี้มีคนมาคนแรกรึยัง
            $datatoday=Service::select('queue','catagory')
            ->where('servicedate',$currentDate)
            ->orderBy('id', 'desc')
            ->first();




            $result=new Service();
            $result->cid=$cid;
            

            if ($datatoday) {
                //วันนี้มีคนมาคนแแรกแล้ว
                $result->queue = ($datatoday->queue+1);


                $result->catagory=$datatoday->catagory;

            }else{
                $result->queue=1;
                $text = "ก ข ฃ ค ฅ ฆ ง จ ฉ ช ซ ฌ ญ ฎ ฏ ฐ ฑ ฒ ณ ด ต ถ ท ธ น บ ป ผ ฝ พ ฟ ภ ม ย ร ล ว ศ ษ ส ห ฬ อ ฮ";
                $characters = explode(' ', $text);
                $randomCharacters = collect($characters)->random(2)->implode('');
                $result->catagory=$randomCharacters;

            }

            $ages = ($keyin == 1 ?  ' ' :  $ageold);
            $result->age = $ages;
           
           
            $result->owner_cid=$owner;
            $result->servicedate=$currentDate;
            $result->fullname=str_replace("#","",$fullname);
            $result->create_at=date('Y-m-d H:i:s');
            $result=$result->save();

            // process queue

            $text = "ก ข ฃ ค ฅ ฆ ง จ ฉ ช ซ ฌ ญ ฎ ฏ ฐ ฑ ฒ ณ ด ต ถ ท ธ น บ ป ผ ฝ พ ฟ ภ ม ย ร ล ว ศ ษ ส ห ฬ อ ฮ";
            $characters = explode(' ', $text);
            $randomCharactersforfirstqueue = collect($characters)->random(2)->implode('');


            if ($datatoday) {
                if (!$fullname||$fullname==null||$fullname=='') {
                    return ['status' => true,"message" =>"จองคิวสำเร็จ","queue"=>$datatoday->catagory . ":".$datatoday->queue+1,"age"=>$ageold];
                }else{
                    return ['status' => true,"message" =>"จองคิวสำเร็จ","queue"=>$datatoday->catagory . ":".$datatoday->queue+1 ,"fullname"=>$fullname,"age"=>$ageold];
                }

            }else{
                if (!$fullname||$fullname==null||$fullname=='') {
                    return ['status' => true,"message" =>"จองคิวสำเร็จ","queue"=>$randomCharactersforfirstqueue.":1","age"=>$ageold];
                }else{
                    return ['status' => true,"message" =>"จองคิวสำเร็จ","queue"=>$randomCharactersforfirstqueue.":1","fullname"=>$fullname,"age"=>$ageold];
                }

            }

        }



        //insertสำเร็จรีเทินคิว true
        //else false ต้องปี




    }


    public function print_queue($cid)
    {
        $GetQueue = Service::select('*')
        ->where('cid',$cid)
        ->whereDate('servicedate',date("Y-m-d"))
        ->first();



        if($GetQueue != null){
            $data = [
                "status" => true,
                "system_queue_opd_id"=>$GetQueue->id ,
                "vn"=> $cid ,
                "hn"=> $GetQueue->age ,
                "qn"=> "" ,
                "main_dep"=> "" ,
                "queue"=> $GetQueue->catagory.' : '.$GetQueue->queue,
                "queue_text"=> "",
                "department"=> "" ,
                "fullname"=>  ($GetQueue->fullname == "empty" ? ""  : $GetQueue->fullname),
                // "fullname"=>  ($GetQueue->fullname == 'empty'?$GetQueue->fullname :''),
                "ptname"=> "" ,
                "pttype"=> "",
                "system_queue_opd_date"=> "" ,
                "system_queue_opd_status"=> "",
                "created_at"=> "" ,
                "updated_at"=> "" ,
                "qn_barcode"=> "",
                "is_money"=>""
            ];
            return ["status"=>true,"data"=>$data];
        }else{

            $data = [
                "status" => false,
                "system_queue_opd_id"=> "" ,
                "vn"=> $cid ,
                "hn"=> $GetQueue->age ,
                "qn"=> "" ,
                "main_dep"=> "" ,
                "queue"=> $GetQueue->catagory.' : '.$GetQueue->queue,
                "queue_text"=> "",
                "department"=> "" ,
                "fullname"=> "" ,
                "ptname"=> "" ,
                "pttype"=> "",
                "system_queue_opd_date"=> "" ,
                "system_queue_opd_status"=> "",
                "created_at"=> "" ,
                "updated_at"=> "" ,
                "qn_barcode"=> "",
                "is_money"=>""
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
