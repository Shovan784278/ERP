<?php

namespace App\Http\Controllers;

use App\Models\FmFeesReceiptBook;
use App\SmClass;
use Modules\Fees\Entities\FmFeesTypeAmount;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Exception;
use Illuminate\Http\Request;

class FmFeesTypeAmountController extends Controller
{

    public function feesTypeAmountListPage(){

        return view('fees::feesInvoice.feesTypeAmountList');   

    }

    public function feesTypeAmountEntry(Request $request){

        // dd($request->all());
        $feeData = FmFeesTypeAmount::where('academic_id', $request->academic_id)->where('month',$request->month)->where('sm_class_id', $request->sm_class_id)->with('sm_class_name', 'fm_fees_type')->get(); 
        //dd($feeData);

        $feesTypes = FmFeesType::all();

        return view('fees::feesInvoice.feesTypeAmountAssign', compact('feeData', 'feesTypes'));

    }



    public function feesTypeAmount(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'sm_class_id' => 'required|exists:your_class_table,id',
            // Add validation rules for other fields if needed
        ]);
    
        $year = $request->input('year');
        $month = $request->input('month');
        $smClassId = $request->input('sm_class_id');


        $existingRecord = FmFeesTypeAmount::where('year', $year)
        ->where('month', $month)
        ->where('sm_class_id', $smClassId)
        ->first();

        if ($existingRecord) {
            // Record already exists, return it
            return response()->json([$existingRecord]);
        }
    
        // Your logic to fetch data based on search parameters
        $feesData = FmFeesTypeAmount::where('year', $year)
            ->where('month', $month)
            ->where('sm_class_id', $smClassId)
            ->get();
    
        return response()->json($feesData);
    }


    
    public function feesTypeAmountList(Request $request){
        //dd("ok");
        return FmFeesTypeAmount::all(); 

    }

//     public function searchFees(Request $request)
// {
//     $requestData = $request->all();
//     // Output the received data for debugging
//     dd($requestData);

//     // Rest of your code
// }



    public function searchFees(Request $request){
        $requestData = $request->all();
        // Output the received data for debugging
        // dd($requestData);

        $year = $request->input('year');
        $month = $request->input('month');
        $sm_class_id = $request->input('sm_class_id');

        $feesData = FmFeesTypeAmount::where('year', $year)
                ->where('month', $month)
                ->where('sm_class_id', $sm_class_id)
                ->get();
        
        return response()->json($feesData);

    }



        public function deleteFeesTypeAmount($id)
        {
            try {
                // Find the record by ID and delete it
                $feesTypeAmount = FmFeesTypeAmount::findOrFail($id);
                $feesTypeAmount->delete();

                return response()->json(['status' => 'success']);
            } catch (\Exception $e) {
                // Handle any exceptions
                return response()->json(['status' => 'error', 'message' => 'Failed to delete record.']);
            }
        }

        public function updateFeesTypeAmount(Request $request, $id)
        {
            try {
                // Fetch the record by ID
                $feesTypeAmount = FmFeesTypeAmount::findOrFail($id);

                // Update the record with the new data
                $feesTypeAmount->update($request->all());

                return redirect()->url('fees/feesTypeAmountList')
                    ->json([

                        'status'=>"success",
                        'message'=>"Updated Successfully!"
                ]);
            } catch (\Exception $e) {
                // Handle any exceptions
                return redirect()->route('fees.feesTypeAmountList')->with('error', 'Failed to update record.');
            }
        }





        public function searchFeesDueDate(Request $request)
        {
            // Validate the date inputs
            $request->validate([
                'form_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:form_date',
                'student_id' => 'nullable|integer',
            ]);
        
            $fromDate = $request->input('form_date');
            $toDate = $request->input('to_date');
            $studentId = $request->input('student_id');
        
            // Query the fees within the date range and filter by student_id if provided
            $query = FmFeesReceiptBook::whereBetween('date', [$fromDate, $toDate])
                        ->whereNotNull('pay_date')
                        ->where('paid_amount', '>', 0);
        
            if (!empty($studentId)) {
                $query->where('student_id', $studentId);
            }
        
            $fees = $query->get();
        
            return view('fees::feesInvoice.feesStudentsDuePayDateSearchResult', compact('fees', 'fromDate', 'toDate'));
        }
        
        


}
