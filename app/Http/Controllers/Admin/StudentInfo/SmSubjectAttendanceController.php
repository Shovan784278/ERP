<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\SmSubject;
use App\SmBaseSetup;
use App\ApiBaseMethod;
use App\SmClassSection;
use App\SmAssignSubject;
use App\SmStudentCategory;
use App\SmStudentAttendance;
use App\SmSubjectAttendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendanceStoreRequest;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendancSearchRequest;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendanceSearchRequest;
use App\Http\Requests\Admin\StudentInfo\subjectAttendanceAverageReportSearchRequest;
use App\Models\StudentRecord;
use App\Notifications\StudentAttendanceSetNotification;
use App\SmNotification;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SmSubjectAttendanceController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function index(Request $request)
    {
        try{
            
                $classes = SmClass::get();
                return view('backEnd.studentInformation.subject_attendance', compact('classes')); 
           
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(StudentSubjectWiseAttendancSearchRequest $request)
    {

        try{

            $input['attendance_date']= $request->attendance_date;
            $input['class']= $request->class;
            $input['subject']= $request->subject;
            $input['section']= $request->section;

            $classes = SmClass::get();
            $sections = SmClassSection::with('sectionName')->where('class_id', $input['class'])->get();
            $subjects = SmAssignSubject::with('subject')->where('class_id', $input['class'])->where('section_id', $input['section'])
                ->groupBy('subject_id')->get();

          $students = StudentRecord::with(['studentDetail' => function($q){
              return $q->where('active_status', 1);
          } , 'studentDetail.DateSubjectWiseAttendances'])
              ->whereHas('studentDetail', function($q){
                  return $q->where('active_status', 1);
              })
              ->where('class_id', $input['class'])
              ->where('section_id', $input['section'])
              ->where('academic_id', getAcademicId())
              ->where('school_id', Auth::user()->school_id)
              ->where('is_promote', 0)
              ->get();

            if ($students->isEmpty()) {
                Toastr::error('No Result Found', 'Failed');
                return redirect('subject-wise-attendance');
            }

            $attendance_type= $students[0]['studentDetail']['DateSubjectWiseAttendances'] != null  ? $students[0]['studentDetail']['DateSubjectWiseAttendances']['attendance_type']:'';

            


            $search_info['class_name'] = SmClass::find($request->class)->class_name;
            $search_info['section_name'] = SmSection::find($request->section)->section_name;
            $search_info['subject_name'] = SmSubject::find($request->subject)->subject_name;
            $search_info['date'] = $input['attendance_date'];
            

 
            if (generalSetting()->attendance_layout==1) {
                return view('backEnd.studentInformation.subject_attendance_list', compact('classes','subjects','sections','students', 'attendance_type', 'search_info', 'input'));
            } else {
                return view('backEnd.studentInformation.subject_attendance_list2', compact('classes','subjects','sections','students', 'attendance_type', 'search_info', 'input'));
            }

            
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function storeAttendance(StudentSubjectWiseAttendanceStoreRequest $request)
    {
    //    return $request->all();
        try {
            foreach ($request->attendance as $record_id => $student) {
                $attendance = SmSubjectAttendance::where('student_id', gv($student, 'student'))
                            ->where('subject_id', $request->subject)
                            ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                            ->where('class_id', gv($student, 'class'))
                            ->where('section_id', gv($student, 'section'))
                            ->where('student_record_id', $record_id)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id', Auth::user()->school_id)
                            ->first();

                if ($attendance != "") 
                {
                    $attendance->delete(); 
                }

                $attendance = new SmSubjectAttendance();
                $attendance->student_record_id = $record_id;
                $attendance->subject_id = $request->subject;
                $attendance->student_id = gv($student, 'student');
                $attendance->class_id = gv($student, 'class');
                $attendance->section_id = gv($student, 'section');
                $attendance->attendance_type = gv($student, 'attendance_type');
                $attendance->notes = gv($student, 'note');
                $attendance->school_id = Auth::user()->school_id;
                $attendance->academic_id = getAcademicId();               
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $r= $attendance->save();

                if(gv($student, 'student')){

                    $student = SmStudent::find(gv($student, 'student'));
                    $subject = SmSubject::find($request->subject);
                    if($student){
                        $notification = new SmNotification();
                        // $notification->user_id = $leave_request_data->id;
                        $notification->user_id = $student->user_id;
                        $notification->role_id = 3;
                        $notification->date = date('Y-m-d');
                        $notification->message = app('translator')->get('student.attendace_set_for_as_subject', ['date' => date('Y-m-d'), 'status' => $attendance->attendance_type, 'subject' => $subject ? $subject->subject_name : '']);
                        $notification->school_id = Auth::user()->school_id;
                        $notification->academic_id = getAcademicId();
                        $notification->save();
            
                        try{
                            $user=User::find($notification->user_id);
                            Notification::send($user, new StudentAttendanceSetNotification($notification));
                        }
                        catch (\Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                    
                }
                
            
            }
            Toastr::success('Operation successful', 'Success');
            return redirect('subject-wise-attendance');
        } catch (\Exception $e) {
           
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function storeAttendanceSecond(Request $request)
    {
       
        try {
            foreach ($request->attendance as $record_id => $student) {
               
                $attendance_type = gv($student, 'attendance_type') ? gv($student, 'attendance_type') : 'A' ;
                $attendance = SmSubjectAttendance::where('student_id', gv($student, 'student'))
                            ->where('subject_id', $request->subject)
                            ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                            ->where('class_id', gv($student, 'class'))
                            ->where('section_id', gv($student, 'section'))
                            ->where('student_record_id', $record_id)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id', Auth::user()->school_id)
                            ->first();
                if ($attendance !="") {
                    $attendance->delete();
                }

                $attendance = new SmSubjectAttendance();
                $attendance->student_record_id = $record_id;
                $attendance->subject_id = $request->subject;
                $attendance->student_id = gv($student, 'student');
                $attendance->class_id = gv($student, 'class');
                $attendance->section_id = gv($student, 'section');
                $attendance->attendance_type = $attendance_type;
                $attendance->notes = gv($student, 'note');
                $attendance->school_id = Auth::user()->school_id;
                $attendance->academic_id = getAcademicId();
                $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                $r= $attendance->save();
            }
            return response()->json('success');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function subjectHolidayStore(Request $request)
    {   
        $active_students = SmStudent::where('active_status', 1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->get()->pluck('id')->toArray();
        $students = StudentRecord::where('class_id', $request->class_id)
                                ->where('section_id', $request->section_id)
                                ->whereIn('student_id', $active_students)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id', Auth::user()->school_id)
                                ->get();

        if ($students->isEmpty()) {
            Toastr::error('No Result Found', 'Failed');
            return redirect('subject-wise-attendance');
        }
        if ($request->purpose == "mark") {
            foreach ($students as $record) {
                $attendance = SmSubjectAttendance::where('student_id', $record->student_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                    ->where('class_id', $request->class_id)->where('section_id', $request->section_id)
                    ->where('record_id', $record->id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
                if (!empty($attendance)) {
                    $attendance->delete();

                    $attendance = new SmSubjectAttendance();
                    $attendance->attendance_type= "H";
                    $attendance->notes= "Holiday";
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                    $attendance->student_id = $record->student_id;
                    $attendance->subject_id = $request->subject_id;
                    $attendance->student_record_id = $record->id;
                    $attendance->class_id = $record->class_id;
                    $attendance->section_id = $record->section_id;
                    $attendance->academic_id = getAcademicId();
                    $attendance->school_id = Auth::user()->school_id;
                    $attendance->save();

                } else {
                    $attendance = new SmSubjectAttendance();
                    $attendance->attendance_type= "H";
                    $attendance->notes= "Holiday";
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                    $attendance->student_id = $record->student_id;
                    $attendance->subject_id = $request->subject_id;

                    $attendance->student_record_id = $record->id;
                    $attendance->class_id = $record->class_id;
                    $attendance->section_id = $record->section_id;

                    $attendance->academic_id = getAcademicId();
                    $attendance->school_id = Auth::user()->school_id;
                    $attendance->save();
                }
            }
        } elseif ($request->purpose == "unmark") {
            foreach ($students as $record) {
                $attendance = SmSubjectAttendance::where('student_id', $record->student_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                    ->where('class_id', $request->class_id)->where('section_id', $request->section_id)
                    ->where('record_id', $record->id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
                if (!empty($attendance)) {
                    $attendance->delete();
                }
            }
        } 
        Toastr::success('Operation successful', 'Success');
        return redirect('subject-wise-attendance');
    }

    public function subjectAttendanceReport(Request $request)
    {
        try{
          
            $classes = SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id',Auth::user()->school_id)
            ->get();

            $types = SmStudentCategory::where('school_id',Auth::user()->school_id)->get();

            $genders = SmBaseSetup::where('active_status', '=', '1')
            ->where('base_group_id', '=', '1')
            ->where('school_id',Auth::user()->school_id)
            ->get();

            return view('backEnd.studentInformation.subject_attendance_report_view', compact('classes', 'types', 'genders'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function subjectAttendanceReportSearch(StudentSubjectWiseAttendanceSearchRequest $request)
    {      

        try{
            $year = $request->year;
            $month = $request->month;
            $class_id = $request->class;
            $section_id = $request->section;
            $assign_subjects = SmAssignSubject::where('class_id',$class_id)
                                ->where('section_id',$section_id)
                                ->first();

            $subject_id = $assign_subjects->subject_id;
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
            $classes = SmClass::where('active_status', 1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->get();

            $students = SmStudent::where('class_id', $request->class)
                        ->where('section_id', $request->section)
                        ->where('active_status', 1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->get();

            $attendances = [];

            foreach ($students as $student) {
                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $student->id)
                ->join('sm_students','sm_students.id','=','sm_subject_attendances.student_id')
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)
                ->get();

                if ($attendance) {
                    $attendances[] = $attendance;
                }
            }

            return view('backEnd.studentInformation.subject_attendance_report_view', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id','subject_id'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
            }
    }
    public function subjectAttendanceAverageReport(Request $request)

    {
        
        try{

            $classes = SmClass::get();

            $types = SmStudentCategory::withoutGlobalScope(AcademicSchoolScope::class)->where('school_id',Auth::user()->school_id)->get();

            $genders = SmBaseSetup::where('base_group_id', '=', '1')->get();

            return view('backEnd.studentInformation.subject_attendance_report_average_view', compact('classes', 'types', 'genders'));

        }catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }

    }
    public function subjectAttendanceAverageReportSearch(subjectAttendanceAverageReportSearchRequest $request)

    {

        // return $request->all();

        try{

            $year = $request->year;

            $month = $request->month;

            $class_id = $request->class;

            $section_id = $request->section;

            $assign_subjects=SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->first();

            if(!$assign_subjects){

                Toastr::error('No Subject Assign ', 'Failed');

                return redirect()->back();
            }
            $subject_id = $assign_subjects->subject_id;

            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

            $classes = SmClass::get();
            $activeStudentIds = SmStudentAttendanceController::activeStudent()->pluck('id')->toArray();
            $students = StudentRecord::where('class_id', $request->class)->where('section_id', $request->section)->whereIn('student_id', $activeStudentIds)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

            $attendances = [];

            foreach ($students as $record) {

                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $record->student_id)

                //  ->join('student_records','student_records.student_id','=','sm_subject_attendances.student_id')

                // // ->where('subject_id', $subject_id)

                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.student_record_id', $record->id)
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id', Auth::user()->school_id)

                ->get();

                if ($attendance) {

                    $attendances[] = $attendance;

                }

            }

            //   return $attendances;
            return view('backEnd.studentInformation.subject_attendance_report_average_view', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id', 'subject_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }

    }


    public function studentAttendanceReportPrint($class_id, $section_id, $month, $year)
    {
        try{
            $current_day = date('d');
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $classes = SmClass::where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            $activeStudentIds = SmStudentAttendanceController::activeStudent()->pluck('id')->toArray();
            $students = StudentRecord::where('class_id', $class_id)->where('section_id', $section_id)->whereIn('student_id', $activeStudentIds)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

            $attendances = [];
            foreach ($students as $record) {
                $attendance = SmStudentAttendance::where('student_id', $record->student_id)->where('attendance_date', 'like', $year . '-' . $month . '%')->where('school_id',Auth::user()->school_id)
                ->where('student_record_id', $record->id)
                ->get();
                if (count($attendance) != 0) {
                    $attendances[] = $attendance;
                }
            }
            
            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function subjectAttendanceReportAveragePrint($class_id, $section_id, $month, $year){
            set_time_limit(2700);
        try{
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $activeStudentIds = SmStudentAttendanceController::activeStudent()->pluck('id')->toArray();
            $students = StudentRecord::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->whereIn('student_id', $activeStudentIds)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();

            $attendances = [];

            foreach ($students as $record) {
                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $record->student_id)
                // ->join('student_records','student_records.student_id','=','sm_subject_attendances.student_id')
                ->where('sm_subject_attendances.student_record_id', $record->id)
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)
                ->get();

                if ($attendance) {
                    $attendances[] = $attendance;
                }
            }

        return view('backEnd.studentInformation.student_subject_attendance',compact('attendances','days' , 'year'  , 'month','class_id'  ,'section_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function subjectAttendanceReportPrint($class_id, $section_id, $month, $year)
    {
             set_time_limit(2700);
        try{
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $students = SmStudent::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id',Auth::user()->school_id)
            ->get();

            $attendances = [];

            foreach ($students as $record) {
                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $record->student_id)
                ->join('sm_students','sm_students.id','=','sm_subject_attendances.student_id')
                ->where('sm_subject_attendances.student_record_id', $record->id)
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)
                ->get();

                if ($attendance) {
                    $attendances[] = $attendance;
                }
            }

        return view('backEnd.studentInformation.student_subject_attendance',compact('attendances','days' , 'year'  , 'month','class_id'  ,'section_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}