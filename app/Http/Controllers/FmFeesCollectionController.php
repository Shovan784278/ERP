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


        public function dateSearchPage(){

            
            return view('fees::feesInvoice.feesStudentsDuePayDateSearchResult');


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




    // public function searchFees(Request $request)
    // {
    //     $student_id = $request->input('student_id');
    //     $academic_year = $request->input('academic_year');


    //     // Fetch the fees based on the student's ID
    //     $feesSummary = FmFeesReceiptBook::with( 'student','class', 'section','feesType')
    //         ->where('student_id', $student_id)
    //         ->get();

    //     // dd($feesSummary);
            
     
    //     $totalPayable = $feesSummary->sum('amount');
    //     $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

    //     // Fetch the fee types
    //     $feesTypes = DB::table('fm_fees_types')->get();

    //     // Fetch active academic years
    //     $academicYears = SmAcademicYear::where('active_status', 1)->get();

    //     return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
    //          'feesSummary',  'totalPayable', 'totalPaid', 'academicYears', 'feesTypes'
    //     ));

           
    // }





//     public function searchFees(Request $request)
// {
//     $student_id = $request->input('student_id');
//     $class_id = $request->input('class_id');
//     $academic_year = $request->input('academic_year');

//     // Build the query to fetch the fees based on student ID and class ID
//     $query = FmFeesReceiptBook::with('student', 'class', 'section', 'feesType');

//     if ($student_id) {
//         $query->where('student_id', $student_id);
//     }

//     if ($class_id) {
//         $query->where('class_id', $class_id);
//     }

//     if ($academic_year) {
//         $query->whereHas('student', function($q) use ($academic_year) {
//             $q->where('academic_year_id', $academic_year);
//         });
//     }

//     $feesSummary = $query->get();

//     $totalPayable = $feesSummary->sum('amount');
//     $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

//     // Fetch the distinct student IDs
//     $studentIds = FmFeesReceiptBook::distinct()->pluck('student_id');

//     // Fetch the fee types
//     $feesTypes = DB::table('fm_fees_types')->get();

//     // Fetch active academic years
//     $academicYears = SmAcademicYear::where('active_status', 1)->get();

//     return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
//         'feesSummary', 'totalPayable', 'totalPaid', 'academicYears', 'feesTypes', 'studentIds'
//     ));
// } updatesss



// public function searchFees(Request $request)
// {
//     $class_id = $request->input('class_id');
//     $student_id = $request->input('student_id');
//     $section_id = $request->input('section_id');
//     $academic_year = $request->input('academic_year');

//     // Fetch the class names and IDs
//     $classes = DB::table('sm_classes')->select('id', 'class_name')->get();

//     // Fetch students based on class ID if class is selected
//     $studentIds = [];
//     if ($class_id) {
//         $studentIds = FmFeesReceiptBook::where('class_id', $class_id)->pluck('student_id');
//     }

//     // Get the current academic year (assuming 'is_current' is a field in your sm_academic_years table)
//     $currentAcademicYear = DB::table('sm_academic_years')->where('year', 1)->first();

//     // If no academic year is selected, use the current one
//     if (!$academic_year && $currentAcademicYear) {
//         $academic_year = $currentAcademicYear->id;
//     }

//     // Build the query to fetch the fees based on student ID and class ID
//     $query = FmFeesReceiptBook::with('student', 'class', 'section', 'feesType');

//     if ($student_id) {
//         $query->where('student_id', $student_id);
//     }

//     if ($class_id) {
//         $query->where('class_id', $class_id);
//     }

//     if ($academic_year) {
//         $query->whereHas('student', function($q) use ($academic_year) {
//             $q->where('academic_year_id', $academic_year);
//         });
//     }

//     $feesSummary = $query->get();

//     $totalPayable = $feesSummary->sum('amount');
//     $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

//     // Fetch the fee types
//     $feesTypes = DB::table('fm_fees_types')->get();

//     // Fetch active academic years
//     $academicYears = DB::table('sm_academic_years')->where('active_status', 1)->get();

//     return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
//         'feesSummary', 'totalPayable', 'totalPaid', 'academicYears', 'feesTypes', 'classes', 'studentIds', 'academic_year'
//     ));
// } updatessssss


public function searchFees(Request $request)
{
    $class_id = $request->input('class_id');  // Capture the class_id from the request
    $section_id = $request->input('section_id');
    $student_id = $request->input('student_id');
    $academic_year = $request->input('academic_year');

    // Fetch the class names and IDs
    $classes = DB::table('sm_classes')->select('id', 'class_name')->get();  // Fetch class list

    // Fetch students based on class ID if class is selected
    $studentIds = [];
    if ($class_id) {
        $studentIds = FmFeesReceiptBook::where('class_id', $class_id)->pluck('student_id');
    }

    // Get the current academic year
    $currentAcademicYear = DB::table('sm_academic_years')->where('year', 1)->first();

    // If no academic year is selected, use the current one
    if (!$academic_year && $currentAcademicYear) {
        $academic_year = $currentAcademicYear->id;
    }

    // Build the query to fetch the fees based on student ID and class ID
    $query = FmFeesReceiptBook::with('student', 'class', 'section', 'feesType');

    if ($student_id) {
        $query->where('student_id', $student_id);
    }

    if ($class_id) {
        $query->where('class_id', $class_id);  // Filter by class
    }

    if ($section_id) {
        $query->where('section_id', $section_id);  // Filter by section
    }

    if ($academic_year) {
        $query->whereHas('student', function($q) use ($academic_year) {
            $q->where('academic_year_id', $academic_year);
        });
    }

    $feesSummary = $query->get();

    $totalPayable = $feesSummary->sum('amount');
    $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

    // Fetch the fee types
    $feesTypes = DB::table('fm_fees_types')->get();

    // Fetch active academic years
    $academicYears = DB::table('sm_academic_years')->where('active_status', 1)->get();

    // Pass class_id and other necessary variables to the view
    return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
        'feesSummary', 'totalPayable', 'totalPaid', 'academicYears', 'feesTypes', 'classes', 'studentIds', 'academic_year', 'class_id'  // Add class_id here
    ));
}




public function fetchStudentsByClass(Request $request)
{
    // $students = FmFeesReceiptBook::where('class_id', $request->class_id)->pluck('student_id');
    // return response()->json($students);


    $class_id = $request->input('class_id');

    // Fetch student details along with section name and roll number from sm_students table
    $students = DB::table('sm_students')
        ->join('sm_sections', 'sm_students.section_id', '=', 'sm_sections.id')
        ->where('sm_students.class_id', $class_id)
        ->get(['sm_students.id', 'sm_students.full_name', 'sm_sections.section_name', 'sm_students.roll_no']);

    return response()->json($students);
}


// public function getStudentsByClass(Request $request)
// {
//     $students = FmFeesReceiptBook::where('class_id', $request->class_id)->pluck('student_id');
//     return response()->json($students);
// }



public function getStudentsByClass(Request $request)
{
    // Fetch records from FmFeesReceiptBook for the given class_id
    $students = FmFeesReceiptBook::with(['student', 'section']) // Use relationships to fetch student and section data
        ->where('class_id', $request->class_id)
        ->get()
        ->map(function ($receipt) {
            return [
                'student_id' => $receipt->student->id,           // Student ID from SmStudent model
                'full_name' => $receipt->student->full_name,     // Student name from SmStudent model
                'student_roll' => $receipt->student->id,  // Student roll from SmStudent model
                'section_name' => $receipt->section->section_name,  // Section name from SmSection model
            ];
        });

    // Return the result as JSON
    return response()->json($students);
}




// FeesController.php

// FeesController.php

// FeesController.php

// public function getStudentsByClass(Request $request)
// {
//     // Debug: Check if class_id is received
//     Log::info('Class ID received: ' . $request->class_id);

//     // Check if class_id is not empty
//     if (!$request->class_id) {
//         return response()->json(['error' => 'Class ID is required'], 400);
//     }

//     try {
//         $students = DB::table('fm_fees_receipt_book')
//             ->where('class_id', $request->class_id)
//             ->join('sm_students', 'fm_fees_receipt_book.student_id', '=', 'sm_students.id')
//             ->join('sm_sections', 'fm_fees_receipt_book.section_id', '=', 'sm_sections.id')
//             ->select('sm_students.id as student_id', 'sm_students.full_name', 'fm_fees_receipt_book.student_roll', 'sm_sections.section_name')
//             ->get();

//         Log::info('Students fetched: ' . $students);  // Debug fetched students
//         return response()->json($students);
//     } catch (\Exception $e) {
//         Log::error('Error fetching students: ' . $e->getMessage());
//         return response()->json(['error' => 'Server Error'], 500);
//     }
// }








// FmFeesCollectionController.php

// public function getSectionsByClass($class_id)
// {
//     $sections = DB::table('sm_class_sections')
//         ->where('class_id', $class_id)
//         ->join('sm_sections', 'sm_class_sections.section_id', '=', 'sm_sections.id')
//         ->select('sm_sections.id', 'sm_sections.section_name')
//         ->get();

//     return response()->json($sections);
// }


// Fetch sections based on class_id
// FmFeesCollectionController.php

// FmFeesCollectionController.php

public function getSectionsByClass(Request $request)
{
    // Fetch class_id from the query string
    $class_id = $request->input('class_id');

    if (!$class_id) {
        return response()->json(['error' => 'Class ID is required'], 400);
    }

    // Fetch sections based on the class_id from fm_fees_reciept_book table
    $sections = DB::table('fm_fees_reciept_book')
        ->where('class_id', $class_id)
        ->join('sm_sections', 'fm_fees_reciept_book.section_id', '=', 'sm_sections.id')
        ->select('sm_sections.id', 'sm_sections.section_name')
        ->distinct()  // Add this to avoid duplicate sections
        ->get();

    return response()->json($sections);
}







// FmFeesCollectionController.php

public function getStudentsByClassAndSection(Request $request)
{
    // Get the class_id and section_id from the request
    $class_id = $request->input('class_id');
    $section_id = $request->input('section_id');

    // Validate that class_id and section_id are provided
    if (!$class_id || !$section_id) {
        return response()->json(['error' => 'Class ID and Section ID are required'], 400);
    }

    // Fetch students based on class_id and section_id
    $students = DB::table('sm_students')
        ->where('class_id', $class_id)
        ->where('section_id', $section_id)
        ->select('id', 'full_name', 'student_roll')
        ->get();

    // Return the list of students as JSON
    return response()->json($students);
}



// Fetch students based on class_id and section_id
// public function getStudentsByClassAndSection($class_id, $section_id)
// {
//     $students = DB::table('sm_students')
//         ->where('class_id', $class_id)
//         ->where('section_id', $section_id)
//         ->select('id', 'first_name', 'last_name')
//         ->get();

//     return response()->json($students);
// }



    public function getStudentsBySection($section_id)
    {
        $students = DB::table('sm_students')
            ->where('section_id', $section_id)
            ->select('id', 'full_name', 'roll_no')
            ->get();

        return response()->json($students);
    }






public function addFees(Request $request)
{
    //dd($request->all());
 

    $student_id = $request->input('student_id');
    
    $fees_type = $request->input('fees_type');
    $amount = $request->input('amount');
    $months = $request->input('months');
    // $paid_amount = $request->input('paid_amount');
    $academic_year = $request->input('academic_year');



    FeesReceiptBook::updateOrCreate([ 

        'year' => $academic_year,
        'record_id' => $request->record_id,
        'fm_fees_type_amount_id' => $fees_type, 
        'class_id' => $request->class_id,
        'student_id' => $student_id,

    ],[

        'record_id' => $request->record_id,
        'date' => date('Y-m-d'),
        'year' => $academic_year,   
        'student_id' => $student_id,
        'class_id' => $request->class_id, 
        'section_id' => $request->section_id, 
        'paid_amount' => $amount,
        'fm_fees_type_amount_id' => $fees_type, 
        'user_id' => Auth::user()->id

    ]);



    // return redirect()->back();
     return response()->json(['success' => true, 'message' => 'Fees added successfully.']);
}




public function showAddFeesForm($studentId)
{
    // Get all fees types
    $allFeesTypes = DB::table('fm_fees_type_amounts')->get();

    // Get fees types that have already been added for the student
    $addedFeesTypes = DB::table('fm_fees_receipt_books')
                        ->where('student_id', $studentId)
                        ->pluck('fm_fees_type_amount_id')
                        ->toArray();

    // Filter out the added fees types
    $availableFeesTypes = $allFeesTypes->filter(function ($feesType) use ($addedFeesTypes) {
        return !in_array($feesType->id, $addedFeesTypes);
    });

    // Get the fees summary data
    $feesSummary = DB::table('fees_summary')
                     ->where('student_id', $studentId)
                     ->get();

    return view('add_fees', [
        'studentId' => $studentId,
        'feesTypes' => $availableFeesTypes,
        'feesSummary' => $feesSummary,
       
    ]);
}



// public function showFeesForm($studentId)
// {
//     // Fetch necessary details
//     $feesSummary = FmFeesReceiptBook::where('student_id', $studentId)->get();
//     $academicYears = SmAcademicYear::all();
//     $feesTypes = SmFeesType::all();

//     // Define the months array
//     $months = [
//         'January', 'February', 'March', 'April', 'May', 
//         'June', 'July', 'August', 'September', 'October', 
//         'November', 'December'
//     ];

    
//     return view('feesInvoice.feesStudentsAmountAssign', compact('feesSummary', 'academicYears', 'feesTypes', 'months'));
// }

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
            ->whereNull('pay_date')
            ->where('fees_delete', 0)
            ->get();

        // dd($feesSummary);
        
        // $studentFees = FmFeesReceiptBook::with('student', 'class', 'section')
        //     ->where('student_id', $student_id)
        //     ->where('year', $academic_year)
        //     ->orderBy('created_at', 'desc')
        //     ->first();
            
     
       
      

        // Fetch the fee types
        $feesTypes = DB::table('fm_fees_types')->get();

        // Fetch active academic years
        $academicYears = SmAcademicYear::where('active_status', 1)->get();



      

        return view('fees::feesInvoice.feesStudentsAmountAssignResult', compact(
            'feesSummary',  'academicYears', 'feesTypes'
        ));
    }


    // FeesController.php

      public function makePayment(Request $request)
{
    // dd($request->all());
    $selected_fees = $request->input('selected_fees');
    $selected_amount = $request->input('selected_amount');

    // Validate selected fees
    if (empty($selected_fees)) {
        return redirect()->back()->with('error', 'No fees selected for payment.');
    }

    // Process payment for each selected fee
    foreach ($selected_fees as $feeId) {
        // dd($feeId);
        $fee = FmFeesReceiptBook::find( $feeId);

        if ($fee) {
           
            // Update the paid amount and pay_date
            $fee->paid_amount = $selected_amount[$feeId];
            $fee->pay_date = now(); // Update with the current date
            $fee->save();
        }
    }

    return redirect()->back()->with('success', 'Payment processed successfully.');
}
        
        public function delete($id)
        {
            
            $fee = FmFeesReceiptBook::find($id);
            if ($fee) {
                $fee->fees_delete = 1; // Assuming 'fees_delete' is the flag for soft deletion
                $fee->save();
                return response()->json(['message' => 'Fee soft deleted successfully.']);
            }
            return response()->json(['message' => 'Fee not found.'], 404);
        }
        


        public function edit($id)
        {
            $fee = DB::table('fm_fees_reciept_book')
                ->where('id', $id)
                ->first();
        
            if ($fee) {
                return response()->json(['success' => true, 'fee' => $fee]);
            } else {
                return response()->json(['success' => false, 'message' => 'Fee not found.'], 404);
            }
        }




public function updateAmount(Request $request)
{
    // dd($request->all());

    $request->validate([
        'paid_amount' => 'required|numeric',
    ]);

    $fee = FmFeesReceiptBook::find($request->fee_id);

    if (!$fee) {
        return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
    }

    $fee->paid_amount = $request->paid_amount;
    $fee->save();

    return response()->json(['success' => true, 'message' => 'Fee amount updated successfully']);
}




// public function searchFeesDueDate(Request $request)
// {
//     // Validate the date inputs
//     $request->validate([
//         'form_date' => 'nullable|date',
//         'to_date' => 'nullable|date|after_or_equal:form_date',
//     ]);

//     $fromDate = $request->input('form_date');
//     $toDate = $request->input('to_date');

//     // Query the fees within the date range and filter by pay_date and paid_amount
//     $fees = FmFeesReceiptBook::whereBetween('pay_date', [$fromDate, $toDate])
//                 ->where('paid_amount', '>', 0)
//                 ->select('student_id', 'pay_date', 'class_id', 'paid_amount')
//                 ->get();
                

//     return view('fees::feesInvoice.feesStudentsDuePayDateSearchResult', compact('fees', 'fromDate', 'toDate'));
// }



public function searchFeesDueDate(Request $request)
{
    // Validate the date inputs
    $request->validate([
        'form_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:form_date',
    ]);

    $fromDate = $request->input('form_date');
    $toDate = $request->input('to_date');

    // Query the fees within the date range and include student and class details
    $fees = FmFeesReceiptBook::whereBetween('pay_date', [$fromDate, $toDate])
                ->where('paid_amount', '>', 0)
                ->with(['student' => function($query) {
                    $query->select('id', 'full_name', 'roll_no', 'class_id', 'section_id');
                }, 'student.class', 'student.section'])
                ->select('student_id', 'pay_date', 'class_id', 'section_id', 'paid_amount')
                ->get();

    return view('fees::feesInvoice.feesStudentsDuePayDateSearchResult', compact('fees', 'fromDate', 'toDate'));
}




}
