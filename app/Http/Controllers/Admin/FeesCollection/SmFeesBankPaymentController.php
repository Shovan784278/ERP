<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\User;
use App\SmClass;
use App\SmParent;
use App\SmSection;
use App\SmStudent;
use App\SmAddIncome;
use App\SmsTemplate;
use App\SmFeesAssign;
use App\SmFeesMaster;
use App\SmBankAccount;
use App\SmFeesPayment;
use App\SmNotification;
use App\SmBankStatement;
use App\SmPaymentMethhod;
use App\SmBankPaymentSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FeesApprovedNotification;
use App\Http\Requests\Admin\FeesCollection\SmFeesBankPaymentRequest;
use App\Http\Requests\Admin\FeesCollection\SmRejectBankPaymentRequest;

class SmFeesBankPaymentController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function bankPaymentSlip()
    {
        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
            $bank_slips = SmBankPaymentSlip::where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->where('approve_status',0)->orderBy('id', 'desc')->get();
            return view('backEnd.feesCollection.bank_payment_slip', compact('classes','bank_slips'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function bankPaymentSlipSearch(SmFeesBankPaymentRequest $request)
    {
    
        
        try {
            $bank_slips = SmBankPaymentSlip::query();
            if ($request->class != "") {
                $bank_slips->where('class_id', $request->class);
            }
            if ($request->section != "") {
                $bank_slips->where('section_id', $request->section);
            }
            if ($request->payment_date != "") {
                $date = strtotime($request->payment_date);
                $new_format = date('Y-m-d', $date);
                $bank_slips->where('date', $new_format);
            }
            if ($request->approve_status != "") {
                $bank_slips->where('approve_status', $request->approve_status);
            }
            
            $all_bank_slips = $bank_slips->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->orderBy('id', 'desc')->get();
            
            $date = $request->payment_date;
            $class_id = $request->class;
            $approve_status = $request->approve_status;
            $section_id = $request->section;
            $classes = SmClass::get();
            $sections = SmSection::get();
            return view('backEnd.feesCollection.bank_payment_slip', compact('all_bank_slips','classes','sections','date','class_id','section_id','approve_status'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function rejectFeesPayment(SmRejectBankPaymentRequest $request){
      
       
        try{

            $bank_payment = SmBankPaymentSlip::find($request->id);        
            $student = SmStudent::find($bank_payment->student_id);
            $parent = SmParent::find($student->parent_id);

            if($bank_payment){
                
                $bank_payment->reason = $request->payment_reject_reason;
                $bank_payment->approve_status = 2;
                $result = $bank_payment->save();

                if($result){
                    $notification = new SmNotification();
                    $notification->role_id = 2;
                    $notification->message ="Bank Payment Rejected -" .'('.@$bank_payment->feesType->name.')';
                    $notification->date = date('Y-m-d');
                    $notification->user_id = $student->user_id;
                    $notification->url = "student-fees";
                    $notification->school_id = Auth::user()->school_id;
                    $notification->academic_id = getAcademicId();
                    $notification->save();

                    try{
                        $receiver_email =  $student->full_name;
                        $receiver_name =   $student->email;
                        $subject= 'Bank Payment Rejected';
                        $view ="backEnd.feesCollection.bank_payment_reject_student";
                        $compact['data'] =  array( 
                                'note' => $bank_payment->reason, 
                                'date' =>dateConvert($notification->created_at),
                                'student_name' =>$student->full_name,
                        ); 
                        send_mail($receiver_email, $receiver_name, $subject , $view , $compact);
                   }catch(\Exception $e){
                       Log::info($e->getMessage());
                   }

                    $notification = new SmNotification();
                    $notification->role_id = 3;
                    $notification->message ="Bank Payment Rejected -" .'('.@$bank_payment->feesType->name.')';
                    $notification->date = date('Y-m-d');
                    $notification->user_id = $parent->user_id;
                    $notification->url = "parent-fees/".$student->id;
                    $notification->school_id = Auth::user()->school_id;
                    $notification->academic_id = getAcademicId();
                    $notification->save();

                    try{
                        $receiver_email =  $student->email;
                        $receiver_name =   $student->full_name;
                        $subject= 'Bank Payment Rejected';
                        $view ="backEnd.feesCollection.bank_payment_reject_student";
                        $compact['data'] =  array( 
                                'note' => $bank_payment->reason, 
                                'date' =>dateConvert($notification->created_at),
                                'student_name' =>$student->full_name,
                        ); 
                        send_mail($receiver_email, $receiver_name, $subject , $view , $compact);
                   }catch(\Exception $e){
                       Log::info($e->getMessage());
                   }

                }

                Toastr::success('Operation successful', 'Success');
                return redirect()->back();

            }

        }
        catch (\Exception $e) {
           
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    
    public function approveFeesPayment(Request $request){
        try {
          if (checkAdmin()) {
                $bank_payment = SmBankPaymentSlip::find($request->id);
            }else{
                $bank_payment = SmBankPaymentSlip::where('id',$request->id)->where('school_id',Auth::user()->school_id)->first();
            }
            $get_master_id=SmFeesMaster::join('sm_fees_assigns','sm_fees_assigns.fees_master_id','=','sm_fees_masters.id')
            ->where('sm_fees_masters.fees_type_id',$bank_payment->fees_type_id)
            ->where('sm_fees_assigns.student_id',$bank_payment->student_id)->first();

            $fees_assign=SmFeesAssign::where('fees_master_id',$get_master_id->fees_master_id)
                        ->where('record_id',$bank_payment->record_id)
                        ->where('student_id',$bank_payment->student_id)
                        ->where('school_id',Auth::user()->school_id)
                        ->first();
                        
            if ($bank_payment->amount > $fees_assign->fees_amount) {
                Toastr::warning('Due amount less than bank payment', 'Warning');
                return redirect()->back();
            }

            $user = Auth::user();
            $fees_payment = new SmFeesPayment();
            $fees_payment->student_id = $bank_payment->student_id;
            $fees_payment->fees_type_id = $bank_payment->fees_type_id;
            $fees_payment->discount_amount = 0;
            $fees_payment->fine = 0;
            $fees_payment->amount = $bank_payment->amount;
            $fees_payment->assign_id = $bank_payment->assign_id;
            $fees_payment->payment_date = date('Y-m-d', strtotime($bank_payment->date));
            $fees_payment->payment_mode = $bank_payment->payment_mode;
            $fees_payment->bank_id= $bank_payment->payment_mode=='bank' ? $bank_payment->bank_id : null;
            $fees_payment->created_by = $user->id;
            $fees_payment->note = $bank_payment->note;
            $fees_payment->record_id = $bank_payment->record_id;
            $fees_payment->academic_id = getAcademicId();
            $fees_payment->school_id = Auth::user()->school_id;
            $fees_payment->save();
            $bank_payment->approve_status = 1;
            $bank_payment->save();


            $payment_mode_name=ucwords($bank_payment->payment_mode);
            $payment_method=SmPaymentMethhod::where('method',$payment_mode_name)->first();
            $income_head=generalSetting();

            $add_income = new SmAddIncome();
            $add_income->name = 'Fees Collect';
            $add_income->date = date('Y-m-d', strtotime($bank_payment->date));
            $add_income->amount = $bank_payment->amount;
            $add_income->fees_collection_id = $fees_payment->id;
            $add_income->active_status = 1;
            $add_income->income_head_id = $income_head->income_head_id;
            $add_income->payment_method_id = $payment_method->id;
            if($payment_method->id==3){
                $add_income->account_id = $bank_payment->bank_id;
            }
            $add_income->created_by = Auth()->user()->id;
            $add_income->school_id = Auth::user()->school_id;
            $add_income->academic_id = getAcademicId();
            $add_income->save();


            if($payment_method->id==3){
                $bank=SmBankAccount::where('id',$bank_payment->bank_id)
                ->where('school_id',Auth::user()->school_id)
                ->first();
                $after_balance= $bank->current_balance + $bank_payment->amount;

                $bank_statement= new SmBankStatement();
                $bank_statement->amount= $bank_payment->amount;
                $bank_statement->after_balance= $after_balance;
                $bank_statement->type= 1;
                $bank_statement->details= "Fees Payment";
                $bank_statement->payment_date= date('Y-m-d', strtotime($bank_payment->date));
                $bank_statement->bank_id= $bank_payment->bank_id;
                $bank_statement->school_id=Auth::user()->school_id;
                $bank_statement->payment_method= $payment_method->id;
                $bank_statement->save();

                $current_balance= SmBankAccount::find($bank_payment->bank_id);
                $current_balance->current_balance=$after_balance;
                $current_balance->update();
        }
            



            

            // $fees_assign=SmFeesAssign::where('fees_master_id',$get_master_id->fees_master_id)->where('student_id',$bank_payment->student_id)->first();
            $fees_assign->fees_amount-=$bank_payment->amount;
            $fees_assign->save();

            $bank_slips = SmBankPaymentSlip::query();
            $bank_slips->where('class_id', $request->class);
            if ($request->section != "") {
                $bank_slips->where('section_id', $request->section);
            }
            if ($request->payment_date != "") {
                $date = strtotime($request->payment_date);
                $new_format = date('Y-m-d', $date);

                $bank_slips->where('date', $new_format);
            }
            $bank_slips = $bank_slips->where('record_id',$bank_payment->record_id)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->orderBy('id', 'desc')
                            ->get();
            $date = $request->payment_date;
            $class_id = $request->class;
            $section_id = $request->section;
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
            $sections = SmSection::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

            $student = SmStudent::find($bank_payment->student_id);

                $notification = new SmNotification;
                $notification->user_id = $student->user_id;
                $notification->role_id = 2;
                $notification->date = date('Y-m-d');
                $notification->message = app('translator')->get('fees.fees_approved');
                $notification->school_id = Auth::user()->school_id;
                $notification->academic_id = getAcademicId();
                $notification->save();
                
                try{
                    $user=User::find($student->user_id);
                    Notification::send($user, new FeesApprovedNotification($notification));
                }catch (\Exception $e) {
                    Log::info($e->getMessage());
                }

                $parent = SmParent::find($student->parent_id);
                $notification = new SmNotification();
                $notification->role_id = 3;
                $notification->message = app('translator')->get('fees.fees_approved_for_child');
                $notification->date = date('Y-m-d');
                $notification->user_id = $parent->user_id;
                $notification->url = "";
                $notification->school_id = Auth::user()->school_id;
                $notification->academic_id = getAcademicId();
                $notification->save();

                try{
                    $user=User::find($parent->user_id);
                    Notification::send($user, new FeesApprovedNotification($notification));
                }catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
                
            Toastr::success('Operation successful', 'Success');
            return redirect('bank-payment-slip');
            // return view('backEnd.feesCollection.bank_payment_slip', compact('bank_slips','classes','sections','date','class_id','section_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
