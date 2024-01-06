<?php

namespace App\Http\Controllers\Admin\Dormitory;

use App\SmClass;
use App\SmStudent;
use App\YearCheck;
use App\ApiBaseMethod;
use App\Http\Controllers\Admin\StudentInfo\SmStudentReportController;
use App\SmDormitoryList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmDormitoryController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}


    public function studentDormitoryReport(Request $request)
    {
        try{
            $classes = SmClass::get();
            $dormitories = SmDormitoryList::get();
            $students = SmStudent::with('class','section','parents','dormitory','room')
                          ->where('dormitory_id', '!=', "")->limit(100)->get();
           
            return view('backEnd.dormitory.student_dormitory_report', compact('classes', 'students', 'dormitories'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }


    public function studentDormitoryReportSearch(Request $request)
    {
        try{
            $student_ids = SmStudentReportController::classSectionStudent($request);
            $students = SmStudent::query();
           
            if ($request->dormitory != "") {
                $students->where('dormitory_id', $request->dormitory);
            } else {
                $students->where('dormitory_id', '!=', '');
            }
            $students = $students->whereIn('id', $student_ids)->with('class','section','parents','dormitory','room')->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
            $classes = SmClass::get();
            $dormitories = SmDormitoryList::get();
            $class_id = $request->class;
            $section_id = $request->section;
            $dormitory_id = $request->dormitory;
            return view('backEnd.dormitory.student_dormitory_report', compact('classes', 'dormitories', 'students', 'class_id', 'dormitory_id','section_id'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
