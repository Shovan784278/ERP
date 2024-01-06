<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\User;
use App\SmClass;
use App\SmStaff;
use App\SmSection;
use App\SmStudent;
use App\SmUserLog;
use Carbon\Carbon;
use App\SmBaseSetup;
use App\ApiBaseMethod;
use Barryvdh\DomPDF\PDF;
use App\SmGeneralSettings;
use App\SmStudentCategory;
use App\InfixModuleManager;
use App\SmStudentAttendance;
use Illuminate\Http\Request;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmStudentReportController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('PM');

    }
        //studentReport modified by jmrashed
        public function studentReport(Request $request)
        {
            try {
                $classes = SmClass::get();
                $types = SmStudentCategory::get();
                $genders = SmBaseSetup::where('base_group_id', '=', '1')->get();
                
                return view('backEnd.studentInformation.student_report', compact('classes', 'types', 'genders'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
        //student report search modified by jmrashed
        public function studentReportSearch(Request $request)
        {
            //  return $request->all();
            $request->validate([
                'class' => 'required'
            ]);
            try {
                $student_ids = $this->classSectionStudent($request);
                $students = SmStudent::query();
    
                $students->where('active_status', 1);    
                //if no student is category selected
                if ($request->type != "") {
                    $students->where('student_category_id', $request->type);
                }
    
                //if no gender is selected
                if ($request->gender != "") {
                    $students->where('gender_id', $request->gender);
                }
                $students = $students->whereIn('id', $student_ids)->with('parents','gender','category','section','class','recordClass', 'studentRecords', 'recordClasses')->where('school_id', Auth::user()->school_id)->get();
    
                $classes = SmClass::get();
                $types = SmStudentCategory::get();
                $genders = SmBaseSetup::where('base_group_id', '=', '1')->get();
    
                $class_id = $request->class;
                $section_id = $request->section;
                $type_id = $request->type;
                $gender_id = $request->gender;
                //   return $students;
                $clas = SmClass::find($request->class);
                return view('backEnd.studentInformation.student_report', compact('students', 'classes', 'types', 'genders', 'class_id', 'type_id', 'gender_id', 'section_id'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
        public function studentAttendanceReport(Request $request)
        {
            try {
                if (teacherAccess()) {
                    $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                   $classes=$teacher_info->classes;
                } else {
                    $classes = SmClass::get();
                }
                $types = SmStudentCategory::get();
                $genders = SmBaseSetup::where('base_group_id', '=', '1')->get();    

                return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'types', 'genders'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
        public function studentAttendanceReportSearch(Request $request)
        {
    
            $input = $request->all();
            $validator = Validator::make($input, [
                'class' => 'required',
                'section' => 'required',
                'month' => 'required',
                'year' => 'required'
            ]);
    
            if ($validator->fails()) {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            try {
                $year = $request->year;
                $month = $request->month;
                $class_id = $request->class;
                $section_id = $request->section;
                $current_day = date('d');
                $clas = SmClass::findOrFail($request->class);
                $sec = SmSection::findOrFail($request->section);
                $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
                if (teacherAccess()) {
                    $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                    $classes=$teacher_info->classes;
                } else {
                    $classes = SmClass::get();
                }
                $students = SmStudent::where('class_id', $request->class)
                            ->where('section_id', $request->section)->get();
    
                $attendances = [];
                foreach ($students as $student) {
                    $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', 'like', $request->year . '-' . $request->month . '%')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                    if (count($attendance) != 0) {
                        $attendances[] = $attendance;
                    }
                }
    
               
                return view('backEnd.studentInformation.student_attendance_report', compact('classes','attendances','students', 'days', 'year', 'month', 'current_day',
                    'class_id', 'section_id', 'clas', 'sec'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
    
        public function studentAttendanceReportPrint($class_id, $section_id, $month, $year)
        {
            set_time_limit(2700);
            try {
                $current_day = date('d');
                $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
                $students = DB::table('sm_students')
                ->where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->get();
    
                $attendances = [];
                foreach ($students as $student) {
                    $attendance = SmStudentAttendance::where('student_id', $student->id)
                    ->where('attendance_date', 'like', $year . '-' . $month . '%')
                    ->get();
    
                    if ($attendance) {
                        $attendances[] = $attendance;
                    }
                }
    
                // $pdf = PDF::loadView(
                //     'backEnd.studentInformation.student_attendance_print',
                //     [
                //         'attendances' => $attendances,
                //         'days' => $days,
                //         'year' => $year,
                //         'month' => $month,
                //         'class_id' => $class_id,
                //         'section_id' => $section_id,
                //         'class' => SmClass::find($class_id),
                //         'section' => SmSection::find($section_id),
                //     ]
                // )->setPaper('A4', 'landscape');
                // return $pdf->stream('student_attendance.pdf');
    
                $class = SmClass::find($class_id);
                $section = SmSection::find($section_id);
                return view('backEnd.studentInformation.student_attendance_print', compact('class', 'section', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }

        public function guardianReport(Request $request)
        {
            try {
              
                $classes = SmClass::get();
                return view('backEnd.studentInformation.guardian_report', compact('classes'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
        public function guardianReportSearch(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class' => 'required'
            ]);
    
            if ($validator->fails()) {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            try {
                $student_ids = $this->classSectionStudent($request);

                $students = SmStudent::query();
                $students->where('academic_id', getAcademicId())->where('active_status', 1);
                $students = $students->whereIn('id', $student_ids)->with('parents', 'section', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
    
                $classes = SmClass::get();
    
                $class_id = $request->class;
                $section_id = $request->section;
                $clas = SmClass::find($request->class);
                return view('backEnd.studentInformation.guardian_report', compact('students', 'classes', 'class_id', 'clas', 'section_id'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
        public function studentLoginReport(Request $request)
        {
            try {
        
                $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
    
                return view('backEnd.studentInformation.login_info', compact('classes'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
    
        public function studentLoginSearch(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class' => 'required'
            ]);
    
            if ($validator->fails()) {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            try {
                $student_ids = $this->classSectionStudent($request);
                $students = SmStudent::query();
                $students->where('academic_id', getAcademicId())->where('active_status', 1);
               
                $students = $students->whereIn('id', $student_ids)->with('user','parents')
              ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
    
                $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $class_id = $request->class;
                $section_id = $request->section;
 
                $clas = SmClass::find($request->class);
                return view('backEnd.studentInformation.login_info', compact('students', 'classes', 'class_id', 'clas','section_id'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }

        public function studentHistory(Request $request)
        {
            try {
                $classes = SmClass::get();
    
               
    
                $years = SmStudent::select('admission_date')->where('active_status', 1)
                    ->where('academic_id', getAcademicId())->get()
                    ->groupBy(function ($val) {
                         return Carbon::parse($val->admission_date)->format('Y');
                    });
                    

    
                return view('backEnd.studentInformation.student_history', compact('classes', 'years'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    
        public function studentHistorySearch(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class' => 'required'
            ]);
    
            if ($validator->fails()) {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            try {
                $student_ids = $this->classSectionStudent($request);
                $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $students = SmStudent::query();
                $students->where('academic_id', getAcademicId())->where('active_status', 1);
                if ($request->admission_year != "") {
                    $students->where('admission_date', 'like',  $request->admission_year . '%');
                }
    
                $students = $students->whereIn('id', $student_ids)->with('parents','section','class','promotion','session')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
               
                $years = SmStudent::select('admission_date')->where('active_status', 1)
                    ->where('academic_id', getAcademicId())
                    ->get()
                    ->groupBy(function ($val) {
                        return Carbon::parse($val->admission_date)->format('Y');
                    });
                $class_id = $request->class;
                $year = $request->admission_year;
                $student_id=null;   

                $clas = SmClass::find($request->class);
                return view('backEnd.studentInformation.student_history', compact('students', 'classes', 'years', 'class_id', 'year', 'clas', 'student_id'));
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    // this function call others 
    public static function classSectionStudent($request)
    {
        $student_ids = StudentRecord::when($request->academic_year, function ($query) use ($request) {
            $query->where('academic_id', $request->academic_year);
        })
        ->when($request->class, function ($query) use ($request) {
            $query->where('class_id', $request->class);
        })
        ->when($request->section, function ($query) use ($request) {
            $query->where('section_id', $request->section);
        })
        ->when(!$request->academic_year, function ($query) use ($request) {
            $query->where('academic_id', getAcademicId());
        })->where('school_id', auth()->user()->school_id)
        ->where('is_promote', 0)
        ->pluck('student_id')->unique();
        return $student_ids;
    }
    
}