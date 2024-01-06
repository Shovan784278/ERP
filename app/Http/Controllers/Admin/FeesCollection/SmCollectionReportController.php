<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\SmClass;
use App\SmStudent;
use App\ApiBaseMethod;
use App\SmFeesPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudentRecord;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmCollectionReportController extends Controller
{
    public function transactionReport(Request $request)
    {
        try {
            $classes = SmClass::get();
            
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, null);
            }
            return view('backEnd.feesCollection.transaction_report',compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function transactionReportSearch(Request $request)
    {
        $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";
        try {
            $classes = SmClass::get();
            if($request->date_range){
                $date_from = new \DateTime(trim($rangeArr[0]));
                $date_to =  new \DateTime(trim($rangeArr[1]));
            }
            if($request->date_range ){
                if($request->class){
                    $students=StudentRecord::where('class_id',$request->class)
                                        ->get();

                    $fees_payments = SmFeesPayment::where('active_status',1)
                                    ->whereIn('student_id', $students->pluck('student_id'))
                                    ->where('payment_date', '>=', $date_from)
                                    ->where('payment_date', '<=', $date_to)
                                    ->where('school_id',Auth::user()->school_id)
                                    ->get();
                    $fees_payments = $fees_payments->groupBy('student_id');
                }else{
                    $fees_payments = SmFeesPayment::where('active_status',1)
                                ->where('payment_date', '>=', $date_from)
                                ->where('payment_date', '<=', $date_to)
                                ->where('school_id',Auth::user()->school_id)
                                ->get();
                    $fees_payments = $fees_payments->groupBy('student_id');
                }
            }
            if($request->class && $request->section){
                $students=StudentRecord::where('class_id',$request->class)
                        ->where('section_id',$request->section)
                        ->where('school_id',Auth::user()->school_id)
                        ->where('academic_id', getAcademicId())
                        ->get();

                $fees_payments = SmFeesPayment::where('active_status',1)
                                ->whereIn('student_id', $students->pluck('student_id'))
                                ->where('payment_date', '>=', $date_from)
                                ->where('payment_date', '<=', $date_to)
                                ->where('school_id',Auth::user()->school_id)
                                ->get();
                $fees_payments = $fees_payments->groupBy('student_id');
            }
            return view('backEnd.feesCollection.transaction_report', compact('fees_payments','classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
