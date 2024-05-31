<?php

namespace App\Http\Controllers;


use App\Models\FeesReceiptBook;
use App\Models\FmFeesReceiptBook;
use App\SmAcademicYear;
use App\SmClass;
use App\SmStudent;

use Illuminate\Support\Facades\DB;
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


        public function searchStudents(Request $request)
    {
        $studentId = $request->input('student_id');
        $academicYearId = $request->input('academic_id');

         // Fetch students along with their class and section
        $students = SmStudent::with(['Class', 'Section'])
        ->where('id', $studentId)
        ->where('academic_id', $academicYearId)
        ->get();

        $feesTypes = FmFeesType::all();



        $academicYears = SmAcademicYear::where('active_status', 1)->get();

        return view('fees::feesInvoice.feesStudentsAmountAssign', compact('students', 'academicYears','feesTypes'));
    }




    //     $student_id = $request->input('student_id');
    //     $academic_year = $request->input('academic_year');
    
    //     // Fetch the student and ensure they exist
    //     $student = SmStudent::with(['class', 'section'])
    //         ->where('id', $student_id)
    //         ->where('academic_id', $academic_year)
    //         ->first();

    
    //     if (!$student) {
    //         return redirect()->back()->withErrors(['student_not_found' => 'Student not found for the given ID and academic year.']);
    //     }
    
    //     // Fetch the fees based on the student's class and academic year
    //     $feesSummary = DB::table('fm_fees_type_amounts')
    //         ->join('sm_students', 'fm_fees_type_amounts.sm_class_id', '=', 'sm_students.class_id')
    //         ->where('sm_students.id', $student_id)
    //         ->where('fm_fees_type_amounts.academic_id', $academic_year)
    //         ->select('fm_fees_type_amounts.*')
    //         ->get();
    
    //     $totalPayable = $feesSummary->sum('amount');
    //     $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field
    
    //     // Fetch the fee types
    //     $feesTypes = DB::table('fm_fees_types')->get();
    
    //     $academicYears = SmAcademicYear::where('active_status', 1)->get();


        
    
    //     return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
    //         'student',  'feesSummary', 'totalPayable', 'totalPaid', 'academicYears', 'feesTypes'
    //     ));
    // }
    


    public function searchFees(Request $request)
    {
        $student_id = $request->input('student_id');
        $academic_year = $request->input('academic_year');

        // Fetch the student and ensure they exist
        // $student = SmStudent::with('')
        //     ->where('id', $student_id)
        //     ->where('academic_id', $academic_year)
        //     ->first();

        // if (!$student) {
        //     return redirect()->back()->withErrors(['student_not_found' => 'Student not found for the given ID and academic year.']);
        // }

        // Fetch the fees based on the student's ID
        $feesSummary = FmFeesReceiptBook::with( 'student','class', 'section')
            ->where('student_id', $student_id)
            ->get();
            
        //   dd($feesSummary);

        // For debugging
        // if ($feesSummary->isEmpty()) {
        //     dd('No fee records found for the given student ID.');
        // } else {
        //     dd($feesSummary->first()->class_id, $feesSummary->first()->section_id);
        // }

        $totalPayable = $feesSummary->sum('amount');
        $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

        // Fetch the fee types
        $feesTypes = DB::table('fm_fees_types')->get();

        // Fetch active academic years
        $academicYears = SmAcademicYear::where('active_status', 1)->get();

        return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
             'feesSummary', 'totalPayable', 'totalPaid', 'academicYears', 'feesTypes'
        ));
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


     



}
