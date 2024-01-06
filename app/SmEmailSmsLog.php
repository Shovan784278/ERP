<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SmEmailSmsLog extends Model
{
    use HasFactory;

    public  static function saveEmailSmsLogData($request){

    	$selectTabb = '';
        if(empty($request->selectTab)){
            $selectTabb = 'G';
        }
        else{
            $selectTabb = $request->selectTab;
        }

        $emailSmsData = new SmEmailSmsLog();
        $emailSmsData->title = $request->email_sms_title;
        $emailSmsData->description = $request->description;
        $emailSmsData->send_through = $request->send_through;
        $emailSmsData->send_date = date('Y-m-d');
        $emailSmsData->send_to = $selectTabb;
        $emailSmsData->school_id =Auth::user()->school_id;
        $emailSmsData->academic_id =getAcademicId();
        $success = $emailSmsData->save();
    }
}
