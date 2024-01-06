<?php
use App\User;
use App\SmExam;
use App\SmStaff;
use App\SmStyle;
use App\SmParent;
use App\SmStudent;
use App\SmLanguage;
use App\SmAddIncome;
use App\SmExamSetup;
use App\SmMarkStore;
use App\SmsTemplate;
use App\SmDateFormat;
use App\SmFeesMaster;
use App\SmMarksGrade;
use App\SmSmsGateway;
use App\SmFeesPayment;
use App\SmResultStore;
use GuzzleHttp\Client;
use App\SmAcademicYear;
use App\SmClassTeacher;
use App\SmEmailSetting;
use App\SmExamSchedule;
use App\SmNotification;
use App\SmAssignSubject;
use App\SmExamAttendance;
use App\SmPaymentMethhod;
use App\SmGeneralSettings;
use App\InfixModuleManager;
use App\CustomResultSetting;
use App\SmExamAttendanceChild;
use App\SmClassOptionalSubject;
use App\SmOptionalSubjectAssign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\SmStudentRegistrationField;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Modules\Lms\Entities\CourseSetting;
use Modules\MenuManage\Entities\MenuManage;
use Modules\Fees\Entities\FmFeesInvoiceSettings;
use Modules\ParentRegistration\Entities\SmStudentRegistration;
use Illuminate\Support\Str;

const WEEK_DAYS = [
    3=>1,
    4=>2,
    5=>3,
    6=>4,
    7=>5,
    1=>6,
    2=>0,
];

const WEEK_DAYS_BY_NAME = [
    'Saturday' => 6,
        'Sunday' => 0,
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
];

function sendEmailBio($data, $to_name, $to_email, $email_sms_title)
{
    $systemSetting = DB::table('sm_general_settings')->select('school_name', 'email')->find(1);
    $systemEmail = DB::table('sm_email_settings')->find(1);
    $system_email = $systemEmail->from_email;
    $school_name = $systemSetting->school_name;
    if (!empty($system_email)) {
        $data['email_sms_title'] = $email_sms_title;
        $data['system_email'] = $system_email;
        $data['school_name'] = $school_name;
        $details = $to_email;
        dispatch(new \App\Jobs\SendEmailJob($data, $details));
        $error_data = [];
        return true;
    } else {
        $error_data[0] = 'success';
        $error_data[1] = 'Operation Failed, Please Updated System Mail';
        return $error_data;
    }

}
if (!function_exists('activeSmsGateway')) {
    function activeSmsGateway()
    {
        $activeSmsGateway = SmSmsGateway::where('school_id', Auth::user()->school_id)->where('active_status', '=', 1)->first();
        if(!$activeSmsGateway){
            $activeSmsGateway = SmSmsGateway::where('school_id', 1)->where('active_status', '=', 1)->first();
        }

        return $activeSmsGateway;

    }
}
if (!function_exists('youtubeVideo')) {
    function youtubeVideo($video_url)
    {
        if (Str::contains($video_url, 'youtu.be')) {
            $url = explode("/", $video_url);
            return 'https://www.youtube.com/watch?v=' . $url[3];
        }

        if (Str::contains($video_url, '&')) {
            return substr($video_url, 0, strpos($video_url, "&"));
        } else {
            return $video_url;
        }

    }
}
function showFileName($data)
{
    $name = explode('/', $data);
    $number = array_key_last($name);
    return $name[$number];
}
function sendSMSApi($to_mobile, $sms, $id)
{
    $activeSmsGateway = SmSmsGateway::find($id);
    if ($activeSmsGateway->gateway_name == 'Twilio') {
        $client = new \Twilio\Rest\Client($activeSmsGateway->twilio_account_sid, $activeSmsGateway->twilio_authentication_token);
        if (!empty($to_mobile)) {
            $result = $message = $client->messages->create($to_mobile, array('from' => $activeSmsGateway->twilio_registered_no, 'body' => $sms));
            return $result;
        }
    } //end Twilio
    else if ($activeSmsGateway->gateway_name == 'Clickatell') {

        // config(['clickatell.api_key' => $activeSmsGateway->clickatell_api_id]); //set a variale in config file(clickatell.php)

        $clickatell = new \Clickatell\Rest();
        $result = $clickatell->sendMessage(['to' => $to_mobile, 'content' => $sms]);
    } //end Clickatell

    //start Himalayasms

    else if ($activeSmsGateway->gateway_name == 'Himalayasms') {
        $client = new Client();
        $request = $client->get("https://sms.techhimalaya.com/base/smsapi/index.php", [
            'query' => [
                'key' => $activeSmsGateway->himalayasms_key,
                'senderid' => $activeSmsGateway->himalayasms_senderId,
                'campaign' => $activeSmsGateway->himalayasms_campaign,
                'routeid' => $activeSmsGateway->himalayasms_routeId,
                'contacts' => $to_mobile,
                'msg' => $sms,
                'type' => "text",
            ],
            'http_errors' => false,
        ]);

        $result = $request->getBody();

    } else if ($activeSmsGateway->gateway_name == 'Msg91') {
        $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
        $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
        $msg91_route = $activeSmsGateway->msg91_route;
        $msg91_country_code = $activeSmsGateway->msg91_country_code;

        $curl = curl_init();

        $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" . $to_mobile . "&authkey=" . $msg91_authentication_key_sid . "&route=" . $msg91_route . "&sender=" . $msg91_sender_id . "&message=" . $sms . "&country=91";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $result = "cURL Error #:" . $err;
        } else {
            $result = $response;
        }
    } //end Msg91
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        $result = "cURL Error #:" . $err;
    } else {
        $result = $response;
    }
    return $result;

} //end Msg91

function sendSMSBio($to_mobile, $sms)
{
    $activeSmsGateway = SmSmsGateway::where('school_id', Auth::user()->school_id)->where('active_status', '=', 1)->first();
    if ($activeSmsGateway->gateway_name == 'Twilio') {

        config(['TWILIO.SID' => $activeSmsGateway->twilio_account_sid]);
        config(['TWILIO.TOKEN' => $activeSmsGateway->twilio_authentication_token]);
        config(['TWILIO.FROM' => $activeSmsGateway->twilio_registered_no]);
        $account_id = $activeSmsGateway->twilio_account_sid; // Your Account SID from www.twilio.com/console
        $auth_token = $activeSmsGateway->twilio_authentication_token; // Your Auth Token from www.twilio.com/console
        $from_phone_number = $activeSmsGateway->twilio_registered_no;
        $client = new \Twilio\Rest\Client($account_id, $auth_token);
        if (!empty($to_mobile)) {
            $result = $message = $client->messages->create($to_mobile, array('from' => $from_phone_number, 'body' => $sms));
            return $result;
        }
    } //end Twilio
    else if ($activeSmsGateway->gateway_name == 'Clickatell') {

        // config(['clickatell.api_key' => $activeSmsGateway->clickatell_api_id]); //set a variale in config file(clickatell.php)

        $clickatell = new \Clickatell\Rest();
        $result = $clickatell->sendMessage(['to' => $to_mobile, 'content' => $sms]);
    } //end Clickatell

    else if ($activeSmsGateway->gateway_name == 'Msg91') {
        $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
        $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
        $msg91_route = $activeSmsGateway->msg91_route;
        $msg91_country_code = $activeSmsGateway->msg91_country_code;

        $curl = curl_init();

        $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" . $to_mobile . "&authkey=" . $msg91_authentication_key_sid . "&route=" . $msg91_route . "&sender=" . $msg91_sender_id . "&message=" . $sms . "&country=91";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $result = "cURL Error #:" . $err;
        } else {
            $result = $response;
        }
    } //end Msg91
    elseif ($activeSmsGateway->gateway_name == 'TextLocal') {

        // Account details
        // $apiKey = urlencode('Your apiKey');
        $apiKey = $activeSmsGateway->textlocal_hash;

        // Message details
        $numbers = $to_mobile;
        $sender = urlencode($activeSmsGateway->textlocal_sender);
        $message = rawurlencode($sms);

        // $numbers = implode(',', $numbers);

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // Send the POST request with cURL
        $ch = curl_init('https://api.txtlocal.com/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Process your response here
        $result = $response;

    }
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION =>
        CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        $result = "cURL Error #:" . $err;
    } else {
        $result = $response;
    }
    return $result;
} //end Msg91

function getValueByString($student_id, $string, $extra = null)
{
    $student = SmStudent::find($student_id);
    if ($extra != null) {
        return $student->$string->$extra;
    } else {
        return $student->$string;
    }
}

function getParentName($student_id, $string, $extra = null)
{
    $student = SmStudent::find($student_id);
    $parent = SmParent::where('id', $student->parent_id)->first();
    if ($extra != null) {
        return $student->$parent->$extra;
    } else {
        return $parent->fathers_name;
    }
}

function SMSBody($body, $s_id, $time)
{
    try {
        $original_message = $body;
        // $original_message= "Dear Parent [fathers_name], your child [class] came to the school at [section]";
        $chars = preg_split('/[\s,]+/', $original_message, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chars as $item) {
            if (strstr($item[0], "[")) {
                $str = str_replace('[', '', $item);
                $str = str_replace(']', '', $str);
                $str = str_replace('.', '', $str);
                if ($str == "class") {
                    $str = "class";
                    $extra = "class_name";
                    $custom_array[$item] = getValueByString($s_id, $str, $extra);
                } elseif ($str == "section") {
                    $str = "section";
                    $extra = "section_name";
                    $custom_array[$item] = getValueByString($s_id, $str, $extra);
                } elseif ($str == 'check_in_time') {
                    $custom_array[$item] = $time;
                } elseif ($str == 'fathers_name') {
                    $str = "parents";
                    $extra = "fathers_name";
                    $custom_array[$item] = getValueByString($s_id, $str, $extra);
                    // $custom_array[$item]= 'father';
                } else {
                    $custom_array[$item] = getValueByString($s_id, $str);
                }
            }
        }

        foreach ($custom_array as $key => $value) {
            $original_message = str_replace($key, $value, $original_message);
        }

        return $original_message;

    } catch (\Exception $e) {
        $data = [];
        return $data;
    }

}

function FeesDueSMSBody($body, $s_id, $time)
{
    try {
        $original_message = $body;
        $chars = preg_split('/[\s,]+/', $original_message, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chars as $item) {
            if (strstr($item[0], "|")) {
                $str = str_replace('|', '', $item);
                // return $str;
                $str = str_replace('|', '', $str);
                $str = str_replace('.', '', $str);
                if ($str == "StudentName") {
                    $str = "StudentName";
                    $extra = "full_name";
                    $custom_array[$item] = getValueByString($s_id, $str, $extra);

                } elseif ($str == 'fathers_name') {
                    $str = "parents";
                    $extra = "fathers_name";
                    $custom_array[$item] = getValueByString($s_id, $str, $extra);
                    // $custom_array[$item]= 'father';
                } else {
                    $custom_array[$item] = getValueByString($s_id, $str);
                }
            }
        }

        foreach ($custom_array as $key => $value) {
            $original_message = str_replace($key, $value, $original_message);
        }

        return $original_message;

    } catch (\Exception $e) {
        $data = [];
        return $data;
    }

}

if (!function_exists('userPermission')) {
    function userPermission($assignId, $role_id = null, $purpose = null)
    {
        $role_id = Auth::user()->role_id;

        $permissions = app('permission');
        if ($role_id == 1 && Auth::user()->is_administrator == "yes") {
            return true;
        }
        if ((!empty($permissions)) && ($role_id != 1)) {
            if (@in_array($assignId, $permissions)) {
                return true;
            } else {
                return false;
            }
        } elseif (moduleStatusCheck('Saas') == true) {
            $saas_status_ids = app('saasSettings');

            if (@in_array($assignId, $saas_status_ids)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }

    }
}

if (!function_exists('moduleStatusCheck')) {
    function moduleStatusCheck($module)
    {
        try {
            // get all module from session;
            $all_module = session()->get('all_module');

            $modulestatus = Module::find($module)->isDisabled();
           
            //if session exist and non empty
            if (!empty($all_module)) {

                //
                if ((in_array($module, $all_module)) && $modulestatus == false) {
                    return true;
                } else {
                    return false;
                }

            } //if session failed or empty data then hit database
            else {

                // is available Modules / FeesCollection1 / Providers / FeesCollectionServiceProvider . php
                $is_module_available = 'Modules/' . $module . '/Providers/' . $module . 'ServiceProvider.php';

                if (file_exists($is_module_available)) {

                    $modulestatus = Module::find($module)->isDisabled();
                    
                    if ($modulestatus == false) {
                        $is_verify = InfixModuleManager::where('name', $module)->first();
                       
                        if (!empty($is_verify->purchase_code)) {
                            return true;

                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            return false;
        }

    }
}

if (!function_exists('dateConvert')) {

    function dateConvert($input_date)
    {
        try {
            $system_date_format = session()->get('system_date_format');
            if (empty($system_date_format)) {
                $date_format_id = SmGeneralSettings::where('id', 1)->first(['date_format_id'])->date_format_id;
                $system_date_format = SmDateFormat::where('id', $date_format_id)->first(['format'])->format;
                session()->put('system_date_format', $system_date_format);

            }

                return \Carbon\Carbon::parse($input_date)->format($system_date_format);

        } catch (\Throwable $th) {
            return $input_date;
        }
    }
}

if (!function_exists('getAcademicId')) {
    function getAcademicId()
    {

        if (session()->has('sessionId')) {
            return session()->get('sessionId');
        } else {
            $session_id = generalSetting()->session_id;
            if(!$session_id){
                $session_id = SmAcademicYear::where('school_id', Auth::user()->school_id)->where('active_status', 1)->first()->id;
            }
            session()->put('sessionId', $session_id);
            return session()->get('sessionId');
        }
    }
}

if (!function_exists('timeZone')) {
    function timeZone()
    {
        $time_zone_setup = session()->get('time_zone_setup');
        if (is_null($time_zone_setup)) {
            $time_zone = SmGeneralSettings::join('sm_time_zones', 'sm_time_zones.id', '=', 'sm_general_settings.time_zone_id')
                ->where('school_id', 1)->first('time_zone');
            session()->put('time_zone_setup', $time_zone);
            $time_zone_setup = session()->get('time_zone_setup');
        }
        return $time_zone_setup->time_zone;
    }
}
if (!function_exists('schoolTimeZone')) {
    function schoolTimeZone()
    {
        $time_zone_setup = session()->get('time_zone_setup');
        if (is_null($time_zone_setup)) {
            $time_zone = SmGeneralSettings::join('sm_time_zones', 'sm_time_zones.id', '=', 'sm_general_settings.time_zone_id')
                ->where('school_id', Auth::user()->school_id)->first('time_zone');
            session()->put('time_zone_setup', $time_zone);
            $time_zone_setup = session()->get('time_zone_setup');
        }
        return $time_zone_setup->time_zone;
    }
}

if (!function_exists('getUserLanguage')) {
    function getUserLanguage()
    {
        if (Auth::check()) {
            return userLanguage();
        } else {

            $school_id = app()->bound('school') ? app('school')->id : 1;
            $user= User::where('role_id',1)->where('school_id', $school_id)->first();
            
           return $user ? $user->language : 'en';
        }
    }
}

if (!function_exists('checkAdmin')) {
    function checkAdmin()
    {
        if (Auth::check()) {
            if (Auth::user()->is_administrator == "yes") {
                return true;
            } elseif (Auth::user()->is_saas == 1) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if (!function_exists('getTempleteDetails')) {
    function getTempleteDetails($purpose)
    {
        $data = SmsTemplate::query()->where('purpose',$purpose)->where('status',1);
        
        if(Auth::check()){
            $details= $data->where('school_id',Auth::user()->school_id)->first();
            if(!$details){
                $details = SmsTemplate::query()->where('purpose',$purpose)->where('status',1)->where('school_id',1)->first();
            }
        }else{
            $details= $data->first();
        }
        return $details;
    }
}

if (!function_exists('send_mail')) {
    function send_mail($reciver_email, $receiver_name, $purpose, $data = [])
    {
        $templete= getTempleteDetails($purpose);
        if(!$templete){
            return ;
        }

        if(!$reciver_email){
            return ;
        }


        if(Auth::check()){
            $setting = SmEmailSetting::where('school_id',Auth::user()->school_id)->where('active_status',1)->first();  
        }else{
            $setting = SmEmailSetting::where('active_status',1)->first();
        }

        $sender_email = $setting->from_email;
        $sender_name = $setting->from_name;
        $email_driver = $setting->mail_driver;

        $subject = $templete->subject;

        $body = App\SmsTemplate::emailTempleteToBody($templete->body,$data);
        $view = view('backEnd.email.emailBody',compact('body'));
        
        try {
            if ($email_driver == "smtp") {
                if (Schema::hasTable('sm_email_settings')){
                    $config = Auth::check() ? DB::table('sm_email_settings')
                            ->where('school_id',Auth::user()->school_id)
                            ->where('mail_driver','smtp')
                            ->first(): 
                            DB::table('sm_email_settings')
                            ->where('mail_driver','smtp')
                            ->first();
                    if ($config){
                        Config::set('mail.driver', $config->mail_driver);
                        Config::set('mail.from', $config->mail_username);
                        Config::set('mail.name', $config->from_name);
                        Config::set('mail.host', $config->mail_host);
                        Config::set('mail.port', $config->mail_port);
                        Config::set('mail.username', $config->mail_username);
                        Config::set('mail.password', $config->mail_password);
                        Config::set('mail.encryption', $config->mail_encryption);
                    }
                }
                Mail::send('backEnd.email.emailBody', compact('body'), function ($message) use ($reciver_email, $receiver_name, $sender_name, $sender_email, $subject) {
                    $message->to($reciver_email, $receiver_name)->subject($subject);
                    $message->from($sender_email, $sender_name);
                });
            }if($email_driver == "php"){
                $message = (string)$view;
                $headers = "From: <$sender_email> \r\n";
                $headers .= "Reply-To: $receiver_name <$reciver_email> \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                @mail($reciver_email, $subject, $message, $headers);
            }
        }catch(\Exception $e) {
            Log::info($e);
        }
    }
}



if (!function_exists('getFileName')) {
    function getFileName($data)
    {
        if ($data) {
            $name = explode('/', $data);
            return $name[4] ?? $name[0];
        } else {
            return '';
        }
    }
}


// Get File Path From HELPER

if (!function_exists('getFilePath3')) {
    function getFilePath3($data)
    {
        
        if ($data) {
            $name = explode('/', $data);
            return $name[3] ?? $name[0];
        } else {
            return '';
        }
    }
}

if (!function_exists('getFilePath4')) {
    function getFilePath4($data)
    {
        if ($data) {
            $name = explode('/', $data);
            if ($name[4]) {
                return $name[3];
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
}

if (!function_exists('showPicName')) {
    function showPicName($data)
    {
        try{
            if ($data) {
                $name = explode('/', $data);
                if ($name[4]) {
                    return $name[4];
                } else {
                    return '';
                }
            } else {
                return '';
            }
        }
        catch (\Exception $e) {
         return null;
        }
    }
}

if (!function_exists('showJoiningLetter')) {
    function showJoiningLetter($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }
}

if (!function_exists('showResume')) {
    function showResume($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }
}

if (!function_exists('showDocument')) {
    function showDocument($data)
    {
        @$name = explode('/', @$data);
        if (!empty(@$name[4])) {

            return $name[4];
        } else {
            return '';
        }
    }
}
// end get file path from helpers

if (!function_exists('termResult')) {
    function termResult($exam_id, $class_id, $section_id, $student_id, $subject_count)
    {
        try {
            $assigned_subject = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->get();
            $mark_store = DB::table('sm_mark_stores')->where([['class_id', $class_id], ['section_id', $section_id], ['exam_term_id', $exam_id], ['student_id', $student_id]])->first();
            $subject_marks = [];
            $subject_gpas = [];
            foreach ($assigned_subject as $subject) {
                $subject_mark = DB::table('sm_mark_stores')->where([['class_id', $class_id], ['section_id', $section_id], ['exam_term_id', $exam_id], ['student_id', $student_id], ['subject_id', $subject->subject_id]])->first();
                $custom_result = new CustomResultSetting; // correct

                $subject_gpa = $custom_result->getGpa($subject_mark->total_marks);
                // return $subject_mark;
                $subject_marks[$subject->subject_id][0] = $subject_mark->total_marks;
                $subject_marks[$subject->subject_id][1] = $subject_gpa;
                $subject_gpas[$subject->subject_id] = $subject_gpa;
            }
            $total_gpa = array_sum($subject_gpas);
            $term_result = $total_gpa / $subject_count;
            return $term_result;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('getFinalResult')) {
    function getFinalResult($exam_id, $class_id, $section_id, $student_id, $percentage)
    {
        try {
            $system_setting = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();
            $system_setting = $system_setting->session_id;
            $custom_result_setup = CustomResultSetting::where('academic_year', $system_setting)->first();

            $assigned_subject = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->get();

            $all_subjects_gpa = [];
            foreach ($assigned_subject as $subject) {
                $custom_result = new CustomResultSetting;
                $subject_gpa = $custom_result->getSubjectGpa($exam_id, $class_id, $section_id, $student_id, $subject->subject_id);
                $all_subjects_gpa[] = $subject_gpa[$subject->subject_id][1];
            }
            $percentage = $custom_result_setup->$percentage;
            $term_gpa = array_sum($all_subjects_gpa) / $assigned_subject->count();
            $percentage = number_format((float) $percentage, 2, '.', '');
            $new_width = ($percentage / 100) * $term_gpa;
            return $new_width;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('getSubjectGpa')) {
    function getSubjectGpa($class_id, $section_id, $exam_id, $student_id, $subject)
    {
        try {
            $subject_marks = [];
            $subject_mark = DB::table('sm_mark_stores')->where('student_id', $student_id)->where('exam_term_id', '=', $exam_id)->first();

            $custom_result = new CustomResultSetting;
            $subject_gpa = $custom_result->getGpa($subject_mark->total_marks);

            $subject_marks[$subject][0] = $subject_mark->total_marks;
            $subject_marks[$subject][1] = $subject_gpa;

            // return $subject_mark->total_marks;
            return $subject_marks;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('getGrade')) {
    function getGrade($marks)
    {
        try {
            $marks_gpa = DB::table('sm_marks_grades')->where('percent_from', '<=', $marks)->where('percent_upto', '>=', $marks)
                ->where('academic_id', getAcademicId())->first();
            return $marks_gpa->grade_name;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('getNumberOfPart')) {
    function getNumberOfPart($subject_id, $class_id, $section_id, $exam_term_id)
    {
        try {
            $results = SmExamSetup::where([
                ['class_id', $class_id],
                ['subject_id', $subject_id],
                ['section_id', $section_id],
                ['exam_term_id', $exam_term_id],
            ])->get();
            return $results;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('GetResultBySubjectId')) {
    function GetResultBySubjectId($class_id, $section_id, $subject_id, $exam_id, $student_id)
    {

        try {
            $data = SmMarkStore::where([
                ['class_id', $class_id],
                ['section_id', $section_id],
                ['exam_term_id', $exam_id],
                ['student_id', $student_id],
                ['subject_id', $subject_id],
            ])->get();
            return $data;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('GetFinalResultBySubjectId')) {
    function GetFinalResultBySubjectId($class_id, $section_id, $subject_id, $exam_id, $student_id)
    {

        try {
            $data = SmResultStore::where([
                ['class_id', $class_id],
                ['section_id', $section_id],
                ['exam_type_id', $exam_id],
                ['student_id', $student_id],
                ['subject_id', $subject_id],
            ])->first();

            return $data;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('markGpa')) {
    function markGpa($marks)
    {

        $mark = SmMarksGrade::where([['percent_from', '<=', floor($marks)], ['percent_upto', '>=', floor($marks)]])->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->first();


        
        if ($mark){
            return $mark;
        }else{
            $fail_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->min('gpa');

            $mark = SmMarksGrade::where('active_status',1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->where('gpa',$fail_grade)
                        ->first();

            return $mark;
        }

    }
}
if (!function_exists('getGrade')) {
    function getGrade($grade)
    {
        $mark = SmMarksGrade::where('from', '<=', $grade)->where('up', '>=', $grade)->where('academic_id', getAcademicId())->first();
        if ($mark){
            return $mark;
        }else{
            $fail_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->min('gpa');

            $mark = SmMarksGrade::where('active_status',1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->where('gpa',$fail_grade)
                        ->first();

            return $mark;
        }
    }
}

if (!function_exists('is_optional_subject')) {
    function is_optional_subject($student_id, $subject_id)
    {
        try {
            $result = SmOptionalSubjectAssign::where('student_id', $student_id)->where('subject_id', $subject_id)->first();
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getMarksOfPart')) {
    function getMarksOfPart($student_id, $subject_id, $class_id, $section_id, $exam_term_id)
    {
        try {
            $results = SmMarkStore::where([
                ['student_id', $student_id],
                ['class_id', $class_id],
                ['subject_id', $subject_id],
                ['section_id', $section_id],
                ['exam_term_id', $exam_term_id],
            ])->get();
            return $results;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('getExamResult')) {
    function getExamResult($exam_id, $student)
    {
        $eligible_subjects = SmAssignSubject::where('class_id', $student->class_id)->where('section_id', $student->section_id)->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)->get();

        foreach ($eligible_subjects as $subject) {

            $getMark = SmResultStore::where([
                ['exam_type_id', $exam_id],
                ['class_id', $student->class_id],
                ['section_id', $student->section_id],
                ['student_id', $student->id],
                ['subject_id', $subject->subject_id],
            ])->first();

            if ($getMark == "") {
                return false;
            }

            $result = SmResultStore::where([
                ['exam_type_id', $exam_id],
                ['class_id', $student->class_id],
                ['section_id', $student->section_id],
                ['student_id', $student->id],
            ])->get();

            return $result;
        }
    }
}

if (!function_exists('teacherAssignedClass')) {
    function teacherAssignedClass()
    {
        try {
            $class_id = [];
            $role_id = Auth::user()->role_id;
            if ($role_id == 4) {
                $classes = SmClassTeacher::where('teacher_id', Auth::user()->id)->get(['id']);
                foreach ($classes as $class) {
                    $class_id[] = $class->module_id;
                }
            } else {

                $general_setting = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();
                return @$general_setting->school_name;
            }
        } catch (\Exception $e) {
            return $class_id = [];
        }
    }
}

if (!function_exists('getValueByStringTestRegistration')) {
    function getValueByStringTestRegistration($data, $str)
    {
        if ($str == 'password') {
            return '123456';
        } elseif ($str == 'school_name') {
            if (moduleStatusCheck('Saas') == true) {
                $student_info = SmStudentRegistration::find(@$data['id']);
                return @$student_info->school->school_name;
            } else {
                $general_setting = SmGeneralSettings::find(1);
                return @$general_setting->school_name;
            }
        }

        if ($data['slug'] == 'student') {
            $student_info = SmStudentRegistration::find(@$data['id']);
            if ($str == 'name') {
                return @$student_info->first_name . ' ' . @$student_info->last_name;
            } elseif ($str == 'guardian_name') {
                return @$student_info->guardian_name;
            } elseif ($str == 'class') {
                return @$student_info->class->class_name;
            } elseif ($str == 'section') {
                return @$student_info->section->section_name;
            }
        } elseif ($data['slug'] == 'parent') {
            $parent_info = SmStudentRegistration::find(@$data['id']);
            if ($str == 'name') {
                return @$parent_info->guardian_name;
            } elseif ($str == 'student_name') {
                return @$parent_info->first_name . ' ' . @$parent_info->last_name;
            }
        }
    }
}
if (!function_exists('getValueByStringTestReset')) {
    function getValueByStringTestReset($data, $str)
    {
        if ($str == 'school_name') {

            $general_setting = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();
            return @$general_setting->school_name;
        } elseif ($str == 'name') {
            $user = User::where('email', $data['email'])->first();
            return @$user->full_name;
        }
    }
}

if (!function_exists('subjectPosition')) {
    function subjectPosition($subject_id, $class_id, $custom_result)
    {

        $students = SmStudent::where('class_id', $class_id)->get();

        $subject_mark_array = [];
        foreach ($students as $student) {
            $subject_marks = 0;

            $first_exam_mark = SmMarkStore::where('student_id', $student->id)->where('class_id', $class_id)->where('subject_id', $subject_id)->where('exam_term_id', $custom_result->exam_term_id1)->sum('total_marks');

            $subject_marks = $subject_marks + $first_exam_mark / 100 * $custom_result->percentage1;

            $second_exam_mark = SmMarkStore::where('student_id', $student->id)->where('class_id', $class_id)->where('subject_id', $subject_id)->where('exam_term_id', $custom_result->exam_term_id2)->sum('total_marks');

            $subject_marks = $subject_marks + $second_exam_mark / 100 * $custom_result->percentage2;

            $third_exam_mark = SmMarkStore::where('student_id', $student->id)->where('class_id', $class_id)->where('subject_id', $subject_id)->where('exam_term_id', $custom_result->exam_term_id3)->sum('total_marks');

            $subject_marks = $subject_marks + $third_exam_mark / 100 * $custom_result->percentage3;

            $subject_mark_array[] = round($subject_marks);

        }

        arsort($subject_mark_array);

        $position_array = [];
        foreach ($subject_mark_array as $position_mark) {
            $position_array[] = $position_mark;
        }

        return $position_array;

    }
}

if (!function_exists('getValueByStringDuesFees')) {
    function getValueByStringDuesFees($student_detail, $str, $fees_info)
    {

        if ($str == 'student_name') {

            return @$student_detail->full_name;

        } elseif ($str == 'parent_name') {

            $parent_info = SmParent::find($student_detail->parent_id);
            return @$parent_info->fathers_name;

        } elseif ($str == 'due_amount') {

            return @$fees_info['dues_fees'];

        } elseif ($str == 'due_date') {

            $fees_master = SmFeesMaster::find($fees_info['fees_master']);
            return @$fees_master->date;

        } elseif ($str == 'school_name') {

            return @Auth::user()->school->school_name;

        } elseif ($str == 'fees_name') {

            $fees_master = SmFeesMaster::find($fees_info['fees_master']);
            return $fees_master->feesTypes->name;
        }
    }
}
if (!function_exists('assignedRoutineSubject')) {

    function assignedRoutineSubject($class_id, $section_id, $exam_id, $subject_id)
    {

        try {
            return SmExamSchedule::where('class_id', $class_id)->where('section_id', $section_id)->where('exam_term_id', $exam_id)->where('subject_id', $subject_id)->first();
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

}

if (!function_exists('assignedRoutine')) {

    function assignedRoutine($class_id, $section_id, $exam_id, $subject_id, $exam_period_id)
    {
        try {
            return SmExamSchedule::where('class_id', $class_id)->where('section_id', $section_id)->where('exam_term_id', $exam_id)->where('subject_id', $subject_id)
                ->where('exam_period_id', $exam_period_id)->first();
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

}

if (!function_exists('is_absent_check')) {

    function is_absent_check($exam_id, $class_id, $section_id, $subject_id, $student_id)
    {
        try {
            $exam_attendance = SmExamAttendance::where('exam_id', $exam_id)->where('class_id', $class_id)->where('section_id', $section_id)->where('subject_id', $subject_id)->first();
            $exam_attendance_child = SmExamAttendanceChild::where('exam_attendance_id', $exam_attendance->id)->where('student_id', $student_id)->first();
            return $exam_attendance_child;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

}

if (!function_exists('feesPayment')) {
    function feesPayment($type_id, $student_id)
    {
        try {
            return SmFeesPayment::where('active_status', 1)->where('fees_type_id', $type_id)->where('student_id', $student_id)->get();
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}

if (!function_exists('generalSetting')) {
    function generalSetting()
    {
        // session()->forget('generalSetting');
        if (session()->has('generalSetting')) {
            return session()->get('generalSetting');
        } else {
            if (app()->bound('school')) {
                $generalSetting = SmGeneralSettings::where('school_id', app('school')->id)->first();
            } else {
                $generalSetting = Auth::check() ? SmGeneralSettings::where('school_id', Auth::user()->school_id)->first() : SmGeneralSettings::first();
            }
        }

        session()->put('generalSetting', $generalSetting);

        return session()->get('generalSetting');
    }
}

if (!function_exists('activeStyle')) {
    function activeStyle()
    {
        if (session()->has('active_style')) {
            $active_style = session()->get('active_style');
            return $active_style;
        } else {
            $active_style = Auth::check() ? SmStyle::where('school_id', Auth::user()->school_id)->where('id', Auth::user()->style_id)->first() :
            SmStyle::where('school_id', 1)->where('is_active', 1)->first();
            if ($active_style==null) {
                $active_style= SmStyle::where('school_id', 1)->where('is_active', 1)->first();
            }
          
            session()->put('active_style', $active_style);
            return session()->get('active_style');
        }
    }
}

if (!function_exists('systemDateFormat')) {
    function systemDateFormat()
    {
        if (session()->has('system_date_format')) {
            return session()->get('system_date_format');
        } else {
            $system_date_format = SmDateFormat::find(DB::table('sm_general_settings')->first()->date_format_id);
            session()->put('system_date_foramt', $system_date_format);

            return session()->get('system_date_foramt');
        }
    }
}
if (!function_exists('emailTemplate')) {
    function emailTemplate()
    {
        if (session()->has('email_template')) {
            return session()->get('email_template');
        } else {
            $email_template = SmsTemplate::where('school_id', Auth::user()->school_id)->first();
            session()->put('email_template', $email_template);

            return session()->get('email_template');
        }
    }
}

if (!function_exists('dashboardBackground')) {
    function dashboardBackground()
    {
        return app('dashboard_bg');
    }
}

if (!function_exists('allStyles')) {
    function allStyles()
    {

        if (session()->has('all_styles')) {
            return session()->get('all_styles');
        } else {
            $all_styles = SmStyle::where('school_id', 1)->where('active_status', 1)->get();
            session()->put('all_styles', $all_styles);

            return session()->get('all_styles');
        }
    }
}

if (!function_exists('textDirection')) {
    function textDirection()
    {
        

        if (session()->has('text_direction')) {
            return session()->get('text_direction');
        } else {
            $ttl_rtl = Auth::user()->rtl_ltl;
            session()->put('text_direction', $ttl_rtl);
// return $ttl_rtl;
            return session()->get('text_direction');
        }
    }
}
if (!function_exists('userRtlLtl')) {
    function userRtlLtl()
    {
        // return 1;

        if (session()->has('user_text_direction')) {
            return session()->get('user_text_direction');
        } else {
            $school_id = app()->bound('school') ? app('school')->id : 1;
            $user= User::where('role_id', 1)->where('school_id', $school_id)->first();
            
            $ttl_rtl = $user ? $user->rtl_ltl : 2;
            session()->put('user_text_direction', $ttl_rtl);

            return session()->get('user_text_direction');
        }
    }
}
if (!function_exists('userLanguage')) {
    function userLanguage()
    {

        if (session()->has('user_language')) {
            return session()->get('user_language');
        } else {
            $language = Auth::user()->language;
            session()->put('user_language', $language);

            return session()->get('user_language');
        }
    }
}

if (!function_exists('schoolConfig')) {
    function schoolConfig()
    {
        return app('school_info');
    }
}
if (!function_exists('selectedLanguage')) {
    function selectedLanguage()
    {
        if (session()->has('selected_language')) {
            return session()->get('selected_language');
        } else {
            $selected_language = Auth::check() ? SmGeneralSettings::where('school_id', Auth::user()->school_id)->first() :
            DB::table('sm_general_settings')->where('school_id', 1)->first();
            session()->put('selected_language', $selected_language);

            return session()->get('selected_language');
        }
    }
}

if (!function_exists('profile')) {
    function profile()
    {

        return auth()->user()->profile;

    }
}

if (!function_exists('getSession')) {
    function getSession()
    {
        if (session()->has('session')) {
            return session()->get('session');
        } else {
            $selected_language = Auth::check() ? SmGeneralSettings::where('school_id', Auth::user()->school_id)->first() :
            DB::table('sm_general_settings')->where('school_id', 1)->first();
            $session = DB::table('sm_academic_years')->where('id', $selected_language->session_id)->first();
            session()->put('session', $session);

            return session()->get('session');
        }
    }
}

if (!function_exists('systemLanguage')) {
    function systemLanguage()
    {
        session()->forget('systemLanguage');
        if (session()->has('systemLanguage')) {
            return session()->get('systemLanguage');
        } else {
            $systemLanguage = SmLanguage::where('school_id', auth()->user()->school_id)->get();
            session()->put('systemLanguage', $systemLanguage);
            return session()->get('systemLanguage');
        }
    }
}

if (!function_exists('academicYears')) {
    function academicYears()
    {
        if (session()->has('academic_years')) {
            return session()->get('academic_years');
        } else {
            $academic_years = Auth::check() ? SmAcademicYear::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get() : '';
            session()->put('academic_years', $academic_years);
            return session()->get('academic_years');
        }
    }
}

if (!function_exists('subjectFullMark')) {
    function subjectFullMark($examtype, $subject)
    {
        try {
            $full_mark = 0;
            $full_mark = SmExam::where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->where('exam_type_id', $examtype)->where('subject_id', $subject)->first('exam_mark')->exam_mark;
            return $full_mark;
        } catch (\Exception $e) {
            return '';
        }

    }
}

if (!function_exists('teacherAccess')) {
    function teacherAccess()
    {
        try {
            $user = Auth::user();
            if ($user->role_id == 4) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('subjectPercentageMark')) {
    function subjectPercentageMark($obtained_mark, $full_nark)
    {
        try {
            $percent = ($obtained_mark / $full_nark) * 100;
            return $percent;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('termWiseFullMark')) {
    function termWiseFullMark($type_ids, $student_id)
    {
        try {
            $average_gpa = 0;
            foreach ($type_ids as $type_id) {
                $total_gpa = SmResultStore::where('student_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $total_subject = SmResultStore::where('student_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->count('subject_id');

                $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first('exam_percentage')->exam_percentage;

                if(!$total_subject){
                    return $average_gpa;
                }

                $average_gpa += ($total_gpa / $total_subject) * ($percentage / 100);
            }
            return $average_gpa;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
if (!function_exists('termWiseGpa')) {
    function termWiseGpa($type_id, $student_id, $with_optional_subject_mark = null)
    {
        try {
            $average_gpa = 0;
            if ($with_optional_subject_mark == null) {
                $studentInfo = StudentRecord::find($student_id);
                $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $studentInfo->class_id)->first();

                if(@$optional_subject_setup){
                    $average_gpa = 0;
                    $optional_subject='';

                    $get_optional_subject= SmOptionalSubjectAssign::where('record_id', $studentInfo->id)
                                            ->where('session_id', $studentInfo->studentDetail->session_id)
                                            ->pluck('subject_id')
                                            ->toArray();

                    $total_gpa = SmResultStore::where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->where('subject_id', '!=', $get_optional_subject)
                        ->sum('total_gpa_point');

                    $total_subject = SmResultStore::where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('subject_id', '!=', $get_optional_subject)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->count('subject_id');
                        if(!$total_subject){
                            return $average_gpa;
                        }

                        $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                                    ->where('academic_id', getAcademicId())
                                    ->where('school_id', Auth::user()->school_id)
                                    ->first('exam_percentage')->exam_percentage;

                        $average_gpa += ($total_gpa / $total_subject) * ($percentage / 100);
                        return $average_gpa;
                }else{
                    $total_gpa = SmResultStore::select('total_gpa_point')->where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->sum('total_gpa_point');

                    $total_subject = SmResultStore::select('subject_id')->where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->count('subject_id');

                    if(!$total_subject){
                        return $average_gpa;
                    }

                    $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->first('exam_percentage')->exam_percentage;

                    $average_gpa += ($total_gpa / $total_subject) * ($percentage / 100);
                    return $average_gpa;
                }
            } elseif ($with_optional_subject_mark != null) {
                $maxgpa = SmMarksGrade::where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->max('gpa');

                $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first('exam_percentage')->exam_percentage;

                $average_gpa += $with_optional_subject_mark * ($percentage / 100);
                if ($maxgpa < $average_gpa) {
                    return $maxgpa;
                } else {
                    return $average_gpa;
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('termWiseMark')) {
    function termWiseTotalMark($type_id, $student_id, $optional_subject = null)
    {
        try {
            if ($optional_subject == null) {
                $studentInfo = StudentRecord::find($student_id);
                $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $studentInfo->class_id)->first();

                if(@$optional_subject_setup){
                    $average_gpa = 0;
                    $optional_subject='';

                    $get_optional_subject= SmOptionalSubjectAssign::where('record_id', $studentInfo->id)
                                            ->where('session_id', $studentInfo->studentDetail->session_id)
                                            ->pluck('subject_id')
                                            ->toArray();

                    $total_gpa = SmResultStore::where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->where('subject_id', '!=', $get_optional_subject)
                        ->sum('total_gpa_point');

                    $total_subject = SmResultStore::where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('subject_id', '!=', $get_optional_subject)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->count('subject_id');
                        if(!$total_subject){
                            return $average_gpa;
                        }
                        $average_gpa += $total_gpa / $total_subject;
                        return $average_gpa;
                }else{
                    $average_gpa = 0;
                    $total_gpa = SmResultStore::where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->sum('total_gpa_point');

                    $total_subject = SmResultStore::where('student_record_id', $student_id)
                        ->where('exam_type_id', $type_id)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->count('subject_id');
                        if(!$total_subject){
                            return $average_gpa;
                        }
                    $average_gpa += $total_gpa / $total_subject;

                    return $average_gpa;
                }

            } elseif ($optional_subject != null) {
                $average_gpa = 0;
                $optional_subject_extra_gpa = 0;

                $class_id = StudentRecord::find($student_id)->class_id;

                $optional_subject_above = SmClassOptionalSubject::where('class_id', $class_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->first('gpa_above')->gpa_above;

                $subject_ids = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->get('subject_id');

                $optional_subject_id = SmOptionalSubjectAssign::whereIn('subject_id', $subject_ids)
                    ->where('record_id', $student_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first('subject_id')->subject_id;

                $without_optional_subject_gpa = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', '!=', $optional_subject_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $optional_subject_gpa = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $optional_subject_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $maxgpa = SmMarksGrade::where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->max('gpa');

                    
                if ($optional_subject_gpa > $optional_subject_above) {
                    $optional_subject_extra_gpa = $optional_subject_gpa - $optional_subject_above;
                }

                $with_optional_subject_extra_gpa = $without_optional_subject_gpa + $optional_subject_extra_gpa;

                $final_gpa_with_optional_subject = $with_optional_subject_extra_gpa / (count($subject_ids) - 1);
                

                if ($maxgpa < $final_gpa_with_optional_subject) {
                    return $maxgpa;
                } else {
                    return $final_gpa_with_optional_subject;
                }
            }

        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('optionalSubjectFullMark')) {
    function optionalSubjectFullMark($type_id, $student_id, $above_gpa, $purpose = null)
    {
        try {
            $subject_ids = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get('subject_id');

            $additional_subject_id = SmOptionalSubjectAssign::whereIn('subject_id', $subject_ids)
                ->where('record_id', $student_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->first('subject_id')->subject_id;

            if ($purpose == "optional_sub_gpa") {
                $total_mark = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $additional_subject_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                return $total_mark;

            } elseif ($purpose == "with_optional_sub_gpa") {
                $total_mark = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $additional_subject_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $exam_type_id = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $additional_subject_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->count('exam_type_id');

                $total = ($total_mark - $above_gpa) * $exam_type_id;
                return $total;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('termWiseAddOptionalMark')) {
    function termWiseAddOptionalMark($type_id, $student_id, $above_gpa)
    {
        try {
            $subject_ids = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get('subject_id');

            $additional_subject_id = SmOptionalSubjectAssign::whereIn('subject_id', $subject_ids)
                ->where('record_id', $student_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->first('subject_id')->subject_id;

            $additional_subject_mark = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', $additional_subject_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->sum('total_gpa_point');

            $additional_single_subject_mark = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', $additional_subject_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->first('total_gpa_point')->total_gpa_point;

            $additional_mark_reduction = $additional_single_subject_mark - $above_gpa;
            if ($additional_mark_reduction > 0) {

            }
            $all_subject_mark = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', '!=', $additional_subject_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->sum('total_gpa_point');

            $without_additional_total_subject = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', '!=', $additional_subject_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->count('subject_id');

            $with_additional_full_gpa = $all_subject_mark + ($additional_subject_mark - $above_gpa);

            $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->first('exam_percentage')->exam_percentage;

            $with_additional_average_gpa = ($with_additional_full_gpa / $without_additional_total_subject) * ($percentage / 100);

            return $with_additional_average_gpa;

        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('gradeName')) {
    function gradeName($total_gpa)
    {
        try {
            $grade_name = SmMarksGrade::where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where('from', '<=', $total_gpa)
                ->where('up', '>=', $total_gpa)
                ->first('grade_name')->grade_name;
            return $grade_name;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('remarks')) {
    function remarks($total_gpa)
    {
        try {
            $description = SmMarksGrade::where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where('from', '<=', $total_gpa)
                ->where('up', '>=', $total_gpa)
                ->first('description')->description;
            return $description;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('subjectHighestMark')) {
    function subjectHighestMark($exam_id, $subject_id, $class_id, $section_id)
    {
        try {
            $highest_mark = SmResultStore::where([['class_id', $class_id], ['exam_type_id', $exam_id], ['section_id', $section_id]])
                ->where('subject_id', $subject_id)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->max('total_marks');
            return $highest_mark;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('getAllUserForChatBasedOnCondition')) {
    function getAllUserForChatBasedOnCondition()
    {
        try {
            $users = User::with('roles')->where('id', '!=', auth()->id())->get();
            if (app('general_settings')->get('chat_can_teacher_chat_with_parents') == 'no') {
                if (auth()->user()->roles->id == 4) {
                    foreach ($users as $index => $user) {
                        $user->roles->id === 3 ? $users->forget($index) : '';
                    }
                }
            }
            return $users;
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('chatOpen')) {
    function chatOpen()
    {
        return app('general_settings')->get('chat_open') == 'yes';
    }
}

// Jitsi Module Start
if (!function_exists('getDomainName')) {
    function getDomainName($url)
    {
        $url_domain = preg_replace("(^https?://)", "", $url);
        $url_domain = preg_replace("(^http?://)", "", $url_domain);
        $url_domain = str_replace("/", "", $url_domain);
        return $url_domain;

    }
}
// Jitsi Module End

if (!function_exists('invitationRequired')) {
    function invitationRequired()
    {
        return app('general_settings')->get('chat_invitation_requirement') == 'required';
    }
}

if (!function_exists('intallMdouleMenu')) {
    function intallMdouleMenu($module_id, $module_name)
    {
        if (Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            $menu_manage_module_id = MenuManage::where('active_status', 1)
                ->where('user_id', Auth::user()->id)
                ->where('role_id', Auth::user()->role_id)
                ->where('module_id', $module_id)
                ->first();
        } else {
            $menu_manage_module_id = MenuManage::where('active_status', 1)
                ->where('user_id', Auth::user()->id)
                ->where('role_id', Auth::user()->role_id)
                ->where('module_addons', $module_id)
                ->first();
        }
        if (moduleStatusCheck($module_name) == true && is_null($menu_manage_module_id)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('customFieldValue')) {
    function customFieldValue($student_id, $labelName, $formName)
    {
        $custom_field_values = [];
        if ($formName == "student_registration") {
            $custom_field_data = SmStudent::where('id', $student_id)->first();
            $value = $custom_field_data->custom_field;
        } elseif ($formName == "staff_registration") {
            $custom_field_data = SmStaff::where('id', $student_id)->first();
            $value = $custom_field_data->custom_field;
        } else {
            $value = null;
        }

        if ($value != null) {
            $custom_field_values = json_decode($custom_field_data->custom_field, true);
            if (array_key_exists($labelName, $custom_field_values)) {
                return $custom_field_values[$labelName];
            } else {
                return null;
            }
        }
    }
}
if (!function_exists('paymentMethodName')) {
    function paymentMethodName($payment_method_id)
    {
        $paymentMethodName = SmPaymentMethhod::where('id', $payment_method_id)
            ->where('school_id', Auth::user()->school_id)
            ->first('method')->method;
        if ($paymentMethodName == "Bank") {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('moduleVersion')) {
    function moduleVersion($module_name)
    {
        $dataPath = 'Modules/' . $module_name . '/' . $module_name . '.json';
        $strJsonFileContents = file_get_contents($dataPath);
        $array = json_decode($strJsonFileContents, true);
        $version = $array[$module_name]['versions'][0];
        return $version;
    }
}

if (!function_exists('menuPosition')) {
    function menuPosition($id)
    {

        $is_have = count(app('sidebar_news')) > 0;
        if ($id == 'is_submit') {
            return $is_have ? 1 : 0;
        }

        if ($is_have) {
            $sidebar = app('sidebar_news')->where('active_status', 1)->where('infix_module_id', $id)->first();

            return $sidebar ? $sidebar->parent_position_no : $id;
        } else {
            return false;
        }

    }
}

if (!function_exists('menuStatus')) {
    function menuStatus($id)
    {
        $is_have = count(app('sidebar_news')) > 0;
        if (($is_have)) {
            $is_have_id = app('sidebar_news')->where('infix_module_id', $id)->first();
            if ($is_have_id) {
                return $is_have_id->active_status == 1 ? true : false;
            } else {
                return auth()->user()->role_id == 1 ? true : userPermission($id);

            }
        }
        return true;
    }
}

if (!function_exists('customFieldValue')) {
    function customFieldValue($id, $labelName, $formName)
    {
        $custom_field_values = [];
        if ($formName == "student_registration") {
            $custom_field_data = SmStudent::where('id', $id)->first();
            $value = $custom_field_data->custom_field;
        } elseif ($formName == "staff_registration") {
            $custom_field_data = SmStaff::where('id', $id)->first();
            $value = $custom_field_data->custom_field;
        } else {
            $value = null;
        }

        if($value != NULL){
            $custom_field_values = json_decode($custom_field_data->custom_field,true);
            if (is_array($custom_field_values) && array_key_exists($labelName, $custom_field_values)) {
                return $custom_field_values[$labelName];
            } else {
                return null;
            }
        }
    }
}

if (!function_exists('courseSetting')) {
    function courseSetting()
    {
        return CourseSetting::first();
    }
}

if (!function_exists('fileUpload')) {
    function fileUpload($file, $destination)
    {

        $fileName = "";

        if (!$file) {
            return $fileName;
        }

        $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $file->move($destination, $fileName);
        $fileName = $destination . $fileName;
        return $fileName;

    }
}

if (!function_exists('fileUpdate')) {
    function fileUpdate($databaseFile, $file, $destination)
    {

        $fileName = "";

        if ($file) {
            $fileName = fileUpload($file, $destination);

            if ($databaseFile && file_exists($databaseFile)) {

                unlink($databaseFile);

            }
        } elseif (!$file and $databaseFile) {
            $fileName = $databaseFile;
        }

        return $fileName;

    }
}

if (!function_exists('putEnvConfigration')) {
    function putEnvConfigration($envKey, $envValue)
    {

        $value = '"' . $envValue . '"';
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n";
        $keyPosition = strpos($str, "{$envKey}=");

        if (is_bool($keyPosition)) {

            $str .= $envKey . '="' . $envValue . '"';

        } else {
            $endOfLinePosition = strpos($str, "\n", $keyPosition);
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $str = str_replace($oldLine, "{$envKey}={$value}", $str);

            $str = substr($str, 0, -1);
        }

        if (!file_put_contents($envFile, $str)) {
            return false;
        } else {
            return true;
        }

    }
}


if (!function_exists('feesInvoiceSettings')) {
    function feesInvoiceSettings()
    {
        $invoiceSettings = FmFeesInvoiceSettings::where('school_id',Auth::user()->school_id)->first();
        return $invoiceSettings;
    }
}

if (!function_exists('feesInvoiceNumber')) {
    function feesInvoiceNumber($invoice)
    {
        $settings = feesInvoiceSettings();
        $positions = json_decode($settings->invoice_positions);
        $format = '';
        foreach($positions as $position){
            if($format){
                $format .= '-';
            }
            $format .= $position->id;
        }
        
        if($format){
            $format .= '-';
        }
        
        $format .= 'uniq_id_start';

        $key = [
            'prefix', 
            'admission_no',
            'class', 
            'section',
            'uniq_id_start'
        ];

        $value = [];
        
        if($settings->prefix){
            $value[] = $settings->prefix;
        }
        if($settings->admission_limit){
            $value[] = Str::limit($invoice->studentInfo->admission_no,$settings->admission_limit, '');
        }
        if($settings->class_limit){
            $value[] = Str::limit($invoice->recordDetail->class->class_name,$settings->class_limit, '');
        }
        if($settings->section_limit){
            $value[] = Str::limit($invoice->recordDetail->section->section_name,$settings->section_limit, '');
        }
        $value[] = $settings->uniq_id_start + $invoice->id;
        
        $formated = str_replace($key, $value, $format);

        return trim($formated, '-');
    }
}

if (!function_exists('send_sms')) {
    function send_sms($reciver_number, $purpose, $data, $schoolId = null)
    {
        $templete= getTempleteDetails($purpose);
        if(!$templete){
            return ;
        }

        if(!$reciver_number){
            return ;
        }

        if($schoolId){
            $settings = SmGeneralSettings::where('school_id', $schoolId)->first();
            $data['school_name'] = $settings? $settings->school_name : null;
            $activeSmsGateway = SmSmsGateway::where('school_id', $schoolId)->where('active_status', 1)->first();
        }else{
            if(Auth::check()){
                $activeSmsGateway = SmSmsGateway::where('school_id',Auth::user()->school_id)->where('active_status', 1)->first();
            }else{
                $activeSmsGateway = SmSmsGateway::where('active_status',1)->first();
            }
        }
        
        if(!$activeSmsGateway){
            $activeSmsGateway = SmSmsGateway::where('school_id', 1)->where('active_status', 1)->first();
        }

        
        $body = App\SmsTemplate::smsTempleteToBody($templete->body, $data);
        
        try {
            if($activeSmsGateway->gateway_name == 'Twilio'){
                $account_id = $activeSmsGateway->twilio_account_sid;
                $auth_token = $activeSmsGateway->twilio_authentication_token;
                $from_phone_number = $activeSmsGateway->twilio_registered_no;

                $client = new \Twilio\Rest\Client($account_id, $auth_token);
                $result = $message = $client->messages->create($reciver_number, array('from' => $from_phone_number, 'body' => $body));

            }else if($activeSmsGateway->gateway_name == 'Msg91'){
                $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
                $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
                $msg91_route = $activeSmsGateway->msg91_route;
                $msg91_country_code = $activeSmsGateway->msg91_country_code;

                if($reciver_number != ""){
                    $curl = curl_init();
                    $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" .
                    $reciver_number . "&authkey=" .
                    $msg91_authentication_key_sid . "&route=" .
                    $msg91_route . "&sender=" .
                    $msg91_sender_id . "&message=" .
                    $body . "&country=91";

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                }
            
            }elseif ($activeSmsGateway->gateway_name == 'TextLocal'){
                // Config variables. Consult http://api.txtlocal.com/docs for more info.
                $test = "0";
                $sender = $activeSmsGateway->textlocal_sender; // This is who the message appears to be from.
                $message = urlencode($body);
                $data = "username=".$activeSmsGateway->textlocal_username.
                "&hash=".$activeSmsGateway->textlocal_hash.
                "&message=".$message.
                "&sender=".$sender.
                "&numbers=".$reciver_number.
                "&test=".$test;
                $ch = curl_init('http://api.txtlocal.com/send/?');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch); // This is the result from the API
                curl_close($ch);

            }else if($activeSmsGateway->gateway_name == 'AfricaTalking'){
                $username = $activeSmsGateway->africatalking_username;
                $apiKey = $activeSmsGateway->africatalking_api_key;
                $AT = new AfricasTalking($username, $apiKey);
    
                $sms_Send = $AT->sms();
                $sms_Send->send(['to' => $reciver_number,'message' => $body]);

            }else if($activeSmsGateway->gateway_name == 'Himalayasms'){
                if($reciver_number != ""){
                    $client = new Client();
                    $request = $client->get( "https://sms.techhimalaya.com/base/smsapi/index.php", [
                        'query' => [
                            'key' => $activeSmsGateway->himalayasms_key,
                            'senderid' => $activeSmsGateway->himalayasms_senderId,
                            'campaign' => $activeSmsGateway->himalayasms_campaign,
                            'routeid' => $activeSmsGateway->himalayasms_routeId,
                            'contacts' => $reciver_number,
                            'msg' => $body,
                            'type' => "text"
                        ],
                        'http_errors' => false
                    ]);
                    $request->getBody();
                }
            }
        }catch(\Exception $e) {
            Log::info($e);
        }
      
    }
}

// time format 2 hours 30 min
if (!function_exists('timeCalculation')) {
    function timeCalculation($time) : string
    {
        $minutes = floor(($time / (60)) % 60);
        $hours = floor(($time / (60 * 60)));

        $hours = ($hours < 10) ? "0" . $hours : $hours;
        $minutes = ($minutes < 10) ? "0" . $minutes : $minutes;
        if ($hours == 0) {
            return $minutes . " minutes ";
        }
        return $hours . " hours " . $minutes . " minutes ";
    }
}

function spn_active_link($route_or_path, $class = 'active')
{
    if (is_array($route_or_path)) {
        foreach ($route_or_path as $route) {
            if (request()->is($route)) {
                return $class;
            }
        }
        return in_array(request()->route()->getName(), $route_or_path) ? $class : false;
    } else {
        if (request()->route()->getName() == $route_or_path) {
            return $class;
        }

        if (request()->is($route_or_path)) {
            return $class;
        }
    }

    return false;
}


function spn_nav_item_open($data, $default_class = 'active')
{
    foreach ($data as $d) {
        if (spn_active_link($d, true)) {
            return $default_class;
        }
    }

    return false;
}


                                                                                            
if (!function_exists('addIncome')) {
    function addIncome($payment_method, $name, $amount, $fees_colection_id ,$user_id)
    {
        $payment_method=SmPaymentMethhod::where('method',$payment_method)->first();
        $income_head=generalSetting();

        $add_income = new SmAddIncome();
        $add_income->name = $name;
        $add_income->date = date('Y-m-d');
        $add_income->amount = $amount;
        $add_income->fees_collection_id = $fees_colection_id;
        $add_income->active_status = 1;
        $add_income->income_head_id = $income_head->income_head_id;
        $add_income->payment_method_id = $payment_method ? $payment_method->id : null;
        $add_income->created_by = $user_id;
        $add_income->school_id = Auth::user()->school_id;
        $add_income->academic_id = getAcademicId();
        $result=$add_income->save();

        if($result){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('sendNotification')) {
    function sendNotification($message, $url, $user_id, $role_id)
    {
        $notification = new SmNotification;
        $notification->date = date('Y-m-d');
        $notification->message = $message;
        $notification->url = $url;
        $notification->user_id = $user_id;
        $notification->role_id = $role_id;
        $notification->school_id = Auth::user()->school_id;
        $notification->academic_id = getAcademicId();
        $result= $notification->save();

        if($result){
            return true;
        }else{
            return false;
        }
    }
}


if(!function_exists('apk_secret')){
    function apk_secret(){
        return \Illuminate\Support\Facades\Storage::exists('.apk_secret') ? Storage::get('.apk_secret') : false;
    }
}

function studentFieldLabel($fields, $name) {
    $field=$fields->where('field_name', $name)->first();
    if ($field && $field->label_name) {
        return $field->label_name;
    }

    return __('student.'.$name);
}
if (!function_exists('is_required')) {
    function is_required($field_name) {
        if (session()->has('SmStudentRegistrationField')) {
            $fields = session()->get('SmStudentRegistrationField');
        } else {
            $fields = SmStudentRegistrationField::where('school_id', auth()->user()->school_id)->get();
            session()->put('SmStudentRegistrationField', $fields);
        }

        $field = $fields->where('field_name', $field_name)->first();
        if ($field && $field->is_required==1) {
            return true;
        }

        return false;
    }
}
if (!function_exists('has_permission')) {
    function has_permission($field_name) {
          
        $fields = SmStudentRegistrationField::where('school_id', auth()->user()->school_id)
                    ->when(auth()->user()->role_id == 2, function ($query) {
                        $query->where('student_edit', 1);
                    })
                    ->when(auth()->user()->role_id == 3, function ($query) {
                        $query->where('parent_edit', 1);
                    })
                    ->pluck('field_name')->toArray();
        if (in_array($field_name, $fields)) {
            return true;
        }
        return false;
    }   
}

if (!function_exists('studentRecords')) {
    function studentRecords($request = null, $student_id = null, $school_id = null) {
        $studentRecord = StudentRecord::query()->with('classes');
        if ($student_id != null) {
            $studentRecord->where('student_id', $student_id);
        }
        if ($school_id != null) {
            $studentRecord->where('school_id', $school_id);
        } else {
            $studentRecord->where('school_id', auth()->user()->school_id);
        }
        if ($request != null) {
            $studentRecord->when($request->class, function ($query) use ($request) {
                $query->where('class_id', $request->class);
            })
            ->when($request->section, function ($query) use ($request) {
                $query->where('section_id', $request->section);
            });
        }
        $studentRecord = $studentRecord->where('academic_id', getAcademicId())
                        ->where('is_promote', 0);
        return $studentRecord;
    }
}



if (!function_exists('SaasSchool')) {
    function SaasSchool()
    {
        $request = request();
        $domain = $request->subdomain;
        $host = $request->getHttpHost();
        $school = null;
        $short_url = preg_replace('#^https?://#', '', rtrim(env('APP_URL', 'http://localhost'), '/'));
        if(!$domain){
            $domain = str_replace('.' . $short_url, '', $host);
        }
        
        if($domain == $host){
            $domain = null;
        }
        
        $saas_module = 'Modules/Saas/Providers/SaasServiceProvider.php';
        if (file_exists($saas_module)) {
            $module_status = json_decode(file_get_contents('modules_statuses.json'), true);
            if (isset($module_status['Saas']) && $module_status['Saas']) {
                
                if ($domain) {
                    $school = \App\SmSchool::where(['domain' => $domain, 'active_status' => 1])->firstOrFail();
                    $request->route()->forgetParameter('subdomain');
                } else if ($host == $short_url) {
                    $school = \App\SmSchool::findOrFail(1);
                } else if ($host != $short_url and config('app.allow_custom_domain')) {
                    $school = \App\SmSchool::where(['custom_domain' => $host, 'active_status' => 1])->firstOrFail();
                }  elseif(Auth::check()){
                    $school = \App\SmSchool::findOrFail(Auth::user()->school_id);
                } else {
                     $school = \App\SmSchool::where(['domain' => $domain, 'active_status' => 1])->firstOrFail();
                    $request->route()->forgetParameter('subdomain');
                }
            }
        }

        if(!$school){
            $school = \App\SmSchool::findOrFail(1);
        } 
        

        return $school;
    }
}

if (!function_exists('SaasDomain')) {
    function SaasDomain()
    {
        return SaasSchool()->domain;
    }
}

if (!function_exists('saasEnv')) {
    function saasEnv($value, $default = null)
    {
       
        try {
           
            $domain = SaasDomain();
            $settings_prefix = Str::lower(str_replace(' ', '_', $domain));
            $path = storage_path('app/chat/' . $settings_prefix . '_settings.json');
            if (!file_exists($path)) {
                copy(storage_path('app/chat/default_settings.json'), $path);
            }
    
    
            $data = json_decode(file_get_contents($path), true);
       
            $settings = [];
            if (!empty($data)) {
                foreach ($data as $key => $property) {
                    $settings[$key] = $property;
                }
            }
            
            $env = $settings[$value] ?? '';
        } catch (\Throwable $th) {
            $env = null;
        }

        if (empty($env)) {
            $env = $default;
        }
        return $env;
    }
}
