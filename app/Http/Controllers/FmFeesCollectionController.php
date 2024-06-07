<?php

namespace App\Http\Controllers;


use App\Models\FeesReceiptBook;
use App\Models\FmFeesReceiptBook;
use App\Models\StudentRecord;
use App\SmAcademicYear;
use App\SmClass;
use App\SmFeesType;
use App\SmStudent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Fees\Entities\FmFeesTypeAmount;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Http\Request;

class FmFeesCollectionController extends Controller
{
    
    

        public function reportPage()
        {
            $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $academicYears = SmAcademicYear::where('active_status', 1)->get();

            $feesTypes = FmFeesType::all();
            
            return view('fees::feesInvoice.feesStudentsAmountAssign', compact('feesGroups', 'academicYears', 'feesTypes'));
        }


   




public function searchStudent(Request $request)
{
    $validated = $request->validate([
        'student_id' => 'required|integer',
        'academic_year' => 'required|string',
    ]);

    $student_id = $request->input('student_id');
    $academic_year = $request->input('academic_year');

    // Retrieve student information along with the latest class and section
    $latestFeeEntry = FmFeesReceiptBook::where('student_id', $student_id)
        ->where('year', $academic_year)
        ->orderBy('created_at', 'desc')
        ->with('class', 'section')
        ->first();

    if (!$latestFeeEntry) {
        return redirect()->back()->withErrors(['student_not_found' => 'Student not found in the fees receipt book for the given year.']);
    }

    // Pass the retrieved data to the view
    return view('feesInvoice.feesStudentsAmountAssign', [
        'student' => $latestFeeEntry,
        'feesTypes' => FmFeesTypeAmount::all(), // Assuming you have a model for fee types
        'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    ]);
}




    public function searchFees(Request $request)
    {
        $student_id = $request->input('student_id');
        $academic_year = $request->input('academic_year');


        // Fetch the fees based on the student's ID
        $feesSummary = FmFeesReceiptBook::with( 'student','class', 'section','feesType')
            ->where('student_id', $student_id)
            ->get();

        // dd($feesSummary);
        
        // $studentFees = FmFeesReceiptBook::with('student', 'class', 'section')
        //     ->where('student_id', $student_id)
        //     ->where('year', $academic_year)
        //     ->orderBy('created_at', 'desc')
        //     ->first();
            
     
        $totalPayable = $feesSummary->sum('amount');
        $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

        // Fetch the fee types
        $feesTypes = DB::table('fm_fees_types')->get();

        // Fetch active academic years
        $academicYears = SmAcademicYear::where('active_status', 1)->get();

        return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
             'feesSummary',  'totalPayable', 'totalPaid', 'academicYears', 'feesTypes'
        ));

           // Store data in session
        // session([
        //     'feesSummary' => $feesSummary,
        //     'totalPayable' => $totalPayable,
        //     'totalPaid' => $totalPaid,
        //     'academicYears' => $academicYears,
        //     'feesTypes' => $feesTypes,
        // ]);

        // return redirect()->route('fees.search');
    }



//     public function addFeesSearch(Request $request)
// {
//     // Handle adding fees logic

//     return redirect()->route('fees.search')->with('message', 'Fees added successfully');
// }







public function addFees(Request $request)
{
    //dd($request->all());
 

    $student_id = $request->input('student_id');
    
    $fees_type = $request->input('fees_type');
    $amount = $request->input('amount');
    $months = $request->input('months');
    $academic_year = $request->input('academic_year');



    // FmFeesReceiptBook::create([
    //     'student_id' => $student_id,
    //     'record_id' => $request->record_id,  // Fetching record_id from latest fee entry
    //     'student_roll' => $request->student_roll,  // Fetching student_roll from latest fee entry
    //     'fm_fees_type_amount_id' => $fees_type,
    //     'class_id' => $request->class_id,
    //     'section_id' => $request->section_id,
    //     'year' => $academic_year,
    //     'date' => now(),  // Adding current date for date field
    //     'user_id' => auth()->id(),
    // ]);


    FeesReceiptBook::updateOrCreate([ 

        'year' => $academic_year,
        'record_id' => $request->record_id,
        'fm_fees_type_amount_id' => $fees_type, 
        'class_id' => $request->class_id,

    ],[

        'record_id' => $request->record_id,
        'date' => date('Y-m-d'),
        'year' => $academic_year,   
        'student_id' => $student_id,
        'class_id' => $request->class_id, 
        'section_id' => $request->section_id, 
        'fm_fees_type_amount_id' => $fees_type, 
        'user_id' => Auth::user()->id

    ]);



    return redirect()->back();
}




public function showFeesForm($studentId)
{
    // Fetch necessary details
    $feesSummary = FmFeesReceiptBook::where('student_id', $studentId)->get();
    $academicYears = SmAcademicYear::all();
    $feesTypes = SmFeesType::all();

    // Define the months array
    $months = [
        'January', 'February', 'March', 'April', 'May', 
        'June', 'July', 'August', 'September', 'October', 
        'November', 'December'
    ];

    
    return view('feesInvoice.feesStudentsAmountAssign', compact('feesSummary', 'academicYears', 'feesTypes', 'months'));
}




    


        public function getYears()
        {
            $years = FmFeesTypeAmount::distinct()->pluck('year');
            return response()->json($years);
        }

        public function show($id)
        {
            // Fetch the fees receipt along with student, class, and section relationships
            $feesReceipt = FeesReceiptBook::with([
                'student', 'class', 'section'
            ])->findOrFail($id);
    
            // Pass the data to the view
            return view('fees.collection.show', compact('feesReceipt'));
        }


        public function getFeesSummary(Request $request)
        {
            $student_id = $request->input('student_id');
            $academic_year = $request->input('academic_year');
        
            $feesSummary = FmFeesReceiptBook::with('feesType')
                ->where('student_id', $student_id)
                ->where('year', $academic_year)
                ->get();
        
            $feesSummary->each(function($fee) {
                $fee->fees_type_name = $fee->feesType->name;
                $fee->months = $fee->pay_Year_Month;
            });
        
            return response()->json(['feesSummary' => $feesSummary]);
        }



            public function showResult(Request $request)
    {

        $student_id = $request->input('student_id');
        $academic_year = $request->input('academic_year');


        // Fetch the fees based on the student's ID
        $feesSummary = FmFeesReceiptBook::with( 'student','class', 'section','feesType')
            ->where('student_id', $student_id)
            ->get();

        // dd($feesSummary);
        
        // $studentFees = FmFeesReceiptBook::with('student', 'class', 'section')
        //     ->where('student_id', $student_id)
        //     ->where('year', $academic_year)
        //     ->orderBy('created_at', 'desc')
        //     ->first();
            
     
        $totalPayable = $feesSummary->sum('amount');
        $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

        // Fetch the fee types
        $feesTypes = DB::table('fm_fees_types')->get();

        // Fetch active academic years
        $academicYears = SmAcademicYear::where('active_status', 1)->get();



      

        return view('fees::feesInvoice.feesStudentsAmountAssignResult', compact(
            'feesSummary', 'totalPayable', 'totalPaid', 'academicYears', 'feesTypes'
        ));
    }



}
