<?php

namespace App\Http\Controllers;


use Modules\Fees\Entities\FmFeesTypeAmount;
use Exception;
use Illuminate\Http\Request;

class FmFeesTypeAmountController extends Controller
{

    public function feesTypeAmountListPage(){

        return view('fees::feesInvoice.feesTypeAmountList'); 

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


}
