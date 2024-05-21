<?php

namespace App\Http\Controllers;


use App\SmAcademicYear;
use App\SmClass;
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

        public function getYears()
        {
            $years = FmFeesTypeAmount::distinct()->pluck('year');
            return response()->json($years);
        }

       


   



}
