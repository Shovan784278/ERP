<?php

namespace App\Http\Controllers;


use Modules\Fees\Entities\FmFeesTypeAmount;
use Exception;
use Illuminate\Http\Request;

class FmFeesTypeAmountController extends Controller
{
    public function feesTypeAmount(Request $request)
    {
        try {
            $feesAmount = FmFeesTypeAmount::create([
                'year' => $request->input('year'),
                'month' => $request->input('month'),
                'amount' => $request->input('amount'),
                'sm_class_id' => $request->input('sm_class_id'),
                'fm_fees_type_id' => $request->input('fm_fees_type_id'),
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Fees amount entry successfully!',
                'data' => $feesAmount, 
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions here
            return response()->json([
                'status' => 'Error',
                'message' => 'Failed to create fees amount entry',
                'error' => $e->getMessage(),
            ], 500); 
        }
    }


    


    public function feesTypeAmountList(Request $request){
        //dd("ok");
        return FmFeesTypeAmount::all(); 

    }
}
