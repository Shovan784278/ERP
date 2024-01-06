<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\SmClass;
use App\SmStudent;
use App\SmFeesAssign;
use App\SmFeesMaster;
use App\ApiBaseMethod;
use App\Http\Controllers\Admin\StudentInfo\SmStudentReportController;
use App\SmFeesPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

class SmFeesReportController extends Controller
{
    public function balanceFeesReport(Request $request)
    {
        try {
            $classes = SmClass::get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.feesCollection.balance_fees_report', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function balanceFeesSearch(Request $request)
    {
    
        $request->validate([
            'class' => 'required',
            'section' => 'required'
        ]);
     
        try {
            $student_ids = SmStudentReportController::classSectionStudent($request);
            $students = SmStudent::with('parents', 'feesAssign', 'feesAssign.feesGroupMaster', 'feesAssign.feesPayments', 'feesPayment')->whereIn('id', $student_ids)->get();
            $balance_students = [];

            $data = [];
            $fees_masters = SmFeesMaster::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            foreach ($students as $key => $student) {
                $total_balance = 0;
                $total_discount = 0;
                $total_amount = 0;
                foreach ($fees_masters as $fees_master) {

                    $due_date = strtotime($fees_master->date);
                    $now = strtotime(date('Y-m-d'));
                    if ($due_date > $now) {
                        continue;
                    }
                    
                    $total_discount += $student->feesPayment->where('active_status',1)->where('fees_type_id', $fees_master->fees_type_id)->sum('discount_amount');
                    $total_balance += $student->feesPayment->where('active_status',1)->where('fees_type_id', $fees_master->fees_type_id)->sum('amount');
                    $total_amount += $fees_master->amount;
                   
                    // $discount_amount = SmFeesPayment::where('active_status', 1)->where('student_id', $student->id)
                    //     ->where('fees_type_id', $fees_master->fees_type_id)->sum('discount_amount');
                    // $balance = SmFeesPayment::where('active_status', 1)->where('student_id', $student->id)
                    //     ->where('fees_type_id', $fees_master->fees_type_id)->sum('amount');
                    // $total_balance += $balance;
                    // $total_discount += $discount_amount;
                    // $total_amount += $fees_master->amount;

                }
                $total_paid = $total_balance + $total_discount;
                if ($total_amount > $total_paid) {
                    $balance_students[] = $student;
                    $data[$key]['student'] = $student;
                    
                    $data[$key]['totalDiscount'] = $student->feesAssign->sum('applied_discount');
                    

                    $totalFine = 0;
                    $totalDeposit = 0;
                    $totalFees = 0;
                    foreach ($student->feesAssign as $feesAssign) {
                        $totalFees += $feesAssign->feesGroupMaster->amount;
                        $totalFine += $feesAssign->feesPayments->where('active_status',1)->sum('fine');
                        $totalDeposit += $feesAssign->feesPayments->where('active_status',1)->sum('amount');
                    }

                    $data[$key]['totalFine'] = $totalFine;
                    $data[$key]['totalDeposit'] = $totalDeposit;
                    $data[$key]['totalFees'] = $totalFees;

                }


            }

         
            // return $master_ids;
            $class_id = $request->class;
            $section_id = $request->section;
            $classes = SmClass::get();
            //  return $balance_students;
            $clas = $classes->find($request->class);
            return view('backEnd.feesCollection.balance_fees_report', compact('classes', 'balance_students', 'class_id', 'clas', 'data', 'section_id'));

            // $students = SmStudent::with('parents')->where('class_id', $request->class)->where('section_id', $request->section)->get();
            // $balance_students = [];
            // $fees_masters = SmFeesMaster::where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            // foreach ($students as $student) {
            //     $total_balance = 0;
            //     $total_discount = 0;
            //     $total_amount = 0;
            //     $master_ids =[];
            //     foreach ($fees_masters as $fees_master) {
                    
            //         $due_date= strtotime($fees_master->date);
            //         $now =strtotime(date('Y-m-d'));
            //         if ($due_date > $now ) {
            //            continue;
            //         }
            //         $master_ids[]=$fees_master->id;
            //         $fees_assign = SmFeesAssign::where('student_id', $student->id)->where('fees_master_id', $fees_master->id)->first();
            //         if ($fees_assign != "") {
            //             $discount_amount = SmFeesPayment::where('active_status',1)->where('student_id', $student->id)
            //             ->where('fees_type_id', $fees_master->fees_type_id)->sum('discount_amount');
            //             $balance = SmFeesPayment::where('active_status',1)->where('student_id', $student->id)
            //                         ->where('fees_type_id', $fees_master->fees_type_id)->sum('amount');
            //             $total_balance += $balance;
            //             $total_discount += $discount_amount;
            //                $total_amount += $fees_master->amount;
                       
            //         }
            //     }
            //     $total_paid = $total_balance + $total_discount;
            //     if ($total_amount > $total_paid) {

            //          $balance_students[] = $student;
            //     }
            // }
            // // return $master_ids;
            // $class_id = $request->class;
            // $classes = SmClass::get();         
            // //  return $balance_students;
            // $clas = SmClass::find($request->class);
            // return view('backEnd.feesCollection.balance_fees_report', compact('classes', 'balance_students', 'class_id', 'clas'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
