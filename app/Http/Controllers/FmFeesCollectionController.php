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
            
     
        $totalPayable = $feesSummary->sum('amount');
        $totalPaid = $feesSummary->sum('paid_amount');  // Assuming there's a 'paid_amount' field

        // Fetch the fee types
        $feesTypes = DB::table('fm_fees_types')->get();

        // Fetch active academic years
        $academicYears = SmAcademicYear::where('active_status', 1)->get();

        return view('fees::feesInvoice.feesStudentsAmountAssign', compact(
             'feesSummary',  'totalPayable', 'totalPaid', 'academicYears', 'feesTypes'
        ));

           
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


        // public function editFee($feeId)
        // {
        //     $fee = FmFeesReceiptBook::find($feeId);

        //     if (!$fee) {
        //         return response()->json(['message' => 'Fee not found.'], 404);
        //     }

        //     return response()->json(['fee' => $fee]);
        // }



        // public function update(Request $request, $id)
        // {
        //     $fee = DB::table('fm_fees_type_amounts')
        //         ->where('id', $id)
        //         ->first();
        
        //     if ($fee) {
        //         DB::table('fm_fees_type_amounts')
        //             ->where('id', $id)
        //             ->update(['amount' => $request->input('amount')]);
        
        //         return response()->json(['success' => true, 'message' => 'Fee updated successfully.']);
        //     } else {
        //         return response()->json(['success' => false, 'message' => 'Fee not found.'], 404);
        //     }
        // }


    //     public function updateFeeAmount(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->validate([
    //         'fee_id' => 'required|integer|exists:fm_fees_receipt_books,id',
    //         'amount' => 'required|numeric|min:0',
    //     ]);

    //     try {
    //         // Find the fee record by ID
    //         $fee = FmFeesReceiptBook::find($request->fee_id);

    //         // If the fee record is not found, return an error response
    //         if (!$fee) {
    //             return response()->json(['message' => 'Fee not found.'], 404);
    //         }

    //         // Update the paid amount and save the fee record
    //         $fee->paid_amount = $request->amount;
    //         $fee->save();

    //         // Return a success response
    //         return response()->json(['message' => 'Fee amount updated successfully.']);
    //     } catch (\Exception $e) {
    //         // Log the error and return a server error response
    //         Log::error('Error updating fee amount: ' . $e->getMessage());
    //         return response()->json(['message' => 'Server error. Please try again later.'], 500);
    //     }
    // }


//     public function updateAmount(Request $request)
// {
    

//     $fee = FmFeesReceiptBook::find($request->fm_fees_type_amount_id);
    
//     if (!$fee) {
//         return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
//     }

//     $fee->paid_amount = $request->amount;
//     $fee->save();

//     return response()->json(['success' => true, 'message' => 'Fee amount updated successfully']);
// }



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



}
