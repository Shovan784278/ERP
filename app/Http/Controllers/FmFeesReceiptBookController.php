<?php

namespace App\Http\Controllers;

use App\Models\FeesReceiptBook;
use App\Models\StudentRecord;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Http\Request;

use App\SmClass;
use App\SmStudent;
use Modules\Fees\Entities\FmFeesTypeAmount;
use Modules\Fees\Entities\FmFeesTypeAmountGenerate;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FmFeesReceiptBookController extends Controller
{

    
    // public function FeesGenerate(){

    //     $studentInfo = DB::table("")
    // }


    // public function storeReceiptBook(Request $request) {

    //     $validatedData = $request->validate([

    //         'class_id' => 'required',
    //         'fm_fees_type_amount_id' => 'required',
    //         'amount' => 'required|numeric'

    //     ]);


    //     $students = SmStudent::where('class_id', $request->class_id)->get();

    //     foreach ($students as $student) {

    //         $feeReceipt = new FeesReceiptBook();

    //     }

    // }


    public function saveAllFeesForClass(Request $request)
{
    // $request->all();

    // dd($request->all());

    $feesYear = $request->feesYear;
    $feesMonth = $request->feesMonth;
    $feesClassId = $request->feesClassId;
    $feesId = $request->feesId;

    // Retrieve the class
    // $class = SmStudent::where('class_id', $feesClassId)->get();
    
    // $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->withoutGlobalScope(StatusAcademicSchoolScope::class)->get();

    $studentRecords = StudentRecord::where('class_id', $feesClassId)->get();

    //dd($studentRecords);

    foreach ($feesId as $feeId) {

        foreach ($studentRecords as $student) {

            FeesReceiptBook::insert([

                'record_id' => $student->id,
                'date' => date('Y-m-d'),
                'year' => $feesYear,
                'student_roll' => $student->student_roll,
                'student_id' => $student->student_id,
                'class_id' => $feesClassId, 
                'section_id' => $student->section_id, 
                'fm_fees_type_amount_id' => $feeId , 
                'user_id' => Auth::user()->id

            ]);

        }

    }



     // Flash a success message to the session
    session()->flash('message', 'Fees saved successfully');

     // Redirect back to the same page
     return redirect()->back();
}



}
