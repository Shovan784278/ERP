<?php

namespace App\Http\Controllers;

use App\SmClass;
use Modules\Fees\Entities\FmFeesTypeAmount;
use Modules\Fees\Entities\FmFeesTypeAmountGenerate;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FmFeesAmountGenerateController extends Controller
{


    //Fees Amount page function
    public function feesAssignGenerate(Request $requets){

        //dd('ok');

        $years = FmFeesTypeAmount::distinct()->pluck('year');

        return view('fees::feesInvoice.feesAssignGenerate', compact('years')); 

    }


    function feesAssignGenerateList(){

        return view('fees::feesInvoice.feesAssignGenerateList');

    }


    public function feesTypeAmountEntry(Request $request){

        // dd($request->all());
        $feeData = FmFeesTypeAmount::where('year', $request->year)->where('month',$request->month)->where('sm_class_id', $request->sm_class_id)->with('sm_class_name', 'fm_fees_type')->get(); 
        //dd($feeData);

        $feesTypes = FmFeesType::all();

        return view('fees::feesInvoice.feesTypeAmountAssign', compact('feeData', 'feesTypes'));

    }


    

    public function getClasses(Request $request)
{
    $year = $request->input('year');

    $classes = FmFeesTypeAmount::where('year', $year)
        ->distinct()
        ->pluck('sm_class_id');

    // Assuming SmClass model has 'class_name' attribute
    $classNames = SmClass::whereIn('id', $classes)->select('class_name', 'id')->get();

    return response()->json($classNames);
}


    

    public function getMonths(Request $request)
    {
        $year = $request->input('year');
        $class = $request->input('class');
        $months = FmFeesTypeAmount::where('year', $year)->where('sm_class_id', $class)->pluck('month');
        return response()->json($months);
    }


    public function getYears()
    {
        $years = FmFeesTypeAmount::distinct()->pluck('year');
        return response()->json($years);
    }

    // function feesGenerate(Request $request){

    //     $data = $request->all();

    //     try {
    //         // Save the data to the database
    //         FmFeesTypeAmountGenerate::create($data);

    //         return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    //     } catch (\Exception $e) {
    //         // Log the error or handle it as needed
    //         return response()->json(['success' => false, 'message' => 'Error saving data']);
    //     }
    //     //dd($request->all());

    // }


   

 

public function feesGenerate(Request $request)
{
    try {
        // Validate the request data

        // Assuming your model has mass-assignable attributes
        $data = $request->all();

        // Fetch data from fm_fees_types_amount based on the selected criteria
        $feesAmount = DB::table('fm_fees_types_amount')
            ->where('year', $data['year'])
            ->where('sm_class_id', $data['sm_class_id'])
            ->where('month', $data['month'])
            ->first();

           

        if (!$feesAmount) {
            return response()->json(['success' => false, 'message' => 'Fees amount not found for the selected criteria']);
        }

        // Insert data into fm_fees_type_amount_generate
        FmFeesTypeAmountGenerate::create([
            'date' => $data['date'],
            'year' => $data['year'],
            'sm_class_id' => $data['sm_class_id'],
            'month' => $data['month'],
            'fm_fees_type_amount_id' => $feesAmount->id, // Assuming 'id' is the primary key of fm_fees_types_amount
        ]);

        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e->getMessage());

        // Return the exception message in the response
        return response()->json(['success' => false, 'message' => 'Error saving data']);
    }
}


    




}



