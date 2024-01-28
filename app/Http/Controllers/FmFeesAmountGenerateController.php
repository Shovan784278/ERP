<?php

namespace App\Http\Controllers;

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

class FmFeesAmountGenerateController extends Controller
{


    //Fees Amount page function
    public function feesAssignGenerate(Request $requets){

        //dd('ok');

        $years = FmFeesTypeAmount::distinct()->pluck('year');

        return view('fees::feesInvoice.feesAssignGenerate', compact('years')); 

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

    function feesGenerate(Request $request){

        dd($request->all());

    }

}
