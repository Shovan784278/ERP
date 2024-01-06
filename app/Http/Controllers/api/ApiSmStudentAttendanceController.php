<?php

namespace App\Http\Controllers\api;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\SmBaseSetup;
use App\SmSmsGateway;
use App\ApiBaseMethod;
use App\SmAcademicYear;
use App\SmStudentCategory;
use App\SmStudentAttendance;
use Illuminate\Http\Request;
use App\Models\StudentRecord;
use App\Scopes\AcademicSchoolScope;
use App\Http\Controllers\Controller;
use App\SmNotification;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use App\Notifications\StudentAttendanceSetNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ApiSmStudentAttendanceController extends Controller
{
    public function studentAttendanceCheck(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'date' => "required",
            'class' => "required",
            'section' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
        }
        $students = StudentRecord::with(['studentDetail' => function($q){
                return $q->where('active_status', 1);
            } , 'studentDetail.DateWiseAttendances'])->when($request->class, function ($query) use ($request) {
                $query->where('class_id', $request->class);
            })->whereHas('studentDetail', function($q){
                return $q->where('active_status', 1);
            })
            ->when($request->section, function ($query) use ($request) {
                $query->where('section_id', $request->section);
            })
            ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
            ->where('school_id', $request->user()->school_id)
                ->where('is_promote', 0)
            ->get();
     
        $studentAttendances = SmStudentAttendance::whereIn('student_id', $students->pluck('student_id')->unique())
            ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
            ->where('class_id', $request->class)
            ->where('section_id', $request->section)
            ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
            ->orderby('student_id', 'ASC')
            ->get();

        $student_attendance = [];
        $no_attendance = [];
        if (count($studentAttendances) == 0) {
            foreach ($students as $student) {
                $d['id'] = $student->id;
                $d['record_id'] = $student->id;
                $d['student_id'] = $student->student_id;
                $d['student_photo'] = $student->studentDetail->student_photo;
                $d['full_name'] = $student->studentDetail->full_name;
                $d['roll_no'] = $student->roll_no;
                $d['class_id'] = $student->class->id;
                $d['section_id'] = $student->section->id;
                $d['class_name'] = $student->class->class_name;
                $d['section_name'] = $student->section->section_name;
                $d['attendance_type'] = null;
                $d['user_id'] = $student->studentDetail->user_id;
                $no_attendance[] = $d;
            }
        } else {
            foreach ($students as $record) {
                $studentAttendanceFirst = SmStudentAttendance::where('student_id', $record->student_id)
                                            ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                                            ->where('student_record_id', $record->id)
                                            ->where('class_id', $request->class)
                                            ->where('section_id', $request->section)
                                            ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
                                            ->first();

                $d['id'] = $record->id;
                $d['record_id'] = $record->id;
                $d['student_id'] = $record->student_id;
                $d['student_photo'] = $record->studentDetail->student_photo;
                $d['full_name'] = $record->studentDetail->full_name;
                $d['roll_no'] = $record->roll_no;
                $d['class_id'] = $record->class->id;
                $d['section_id'] = $record->section->id;
                $d['class_name'] = $record->class->class_name;
                $d['section_name'] = $record->section->section_name;
                $d['attendance_type'] = $studentAttendanceFirst ? $studentAttendanceFirst->attendance_type : null;
                $d['user_id'] = $record->studentDetail->user_id;
                $student_attendance[] = $d;
            }
        }
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if (count($studentAttendances) > 0) {
                return ApiBaseMethod::sendResponse($student_attendance, null);
            } else {
                return ApiBaseMethod::sendResponse($no_attendance, 'Student attendance not done yet');
            }
        }

        // if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //     return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
        // }
    }

    public function saas_studentAttendanceCheck(Request $request, $school_id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'date' => "required",
            'class' => "required",
            'section' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
        }

        $students = StudentRecord::with(['studentDetail' => function($q){
            return $q->where('active_status', 1);
        } , 'studentDetail.DateWiseAttendances'])->when($request->class, function ($query) use ($request) {
            $query->where('class_id', $request->class);
        })->whereHas('studentDetail', function($q){
            return $q->where('active_status', 1);
        })
        ->when($request->section, function ($query) use ($request) {
            $query->where('section_id', $request->section);
        })
        ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
        ->where('school_id', $request->user()->school_id)
            ->where('is_promote', 0)
        ->get();
        $studentAttendance = SmStudentAttendance::whereIn('student_id', $students->pluck('student_id')->unique())
        ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
        ->orderby('student_id', 'ASC')
        ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
        ->where('school_id', $school_id)
        ->withOutGlobalScope(AcademicSchoolScope::class)
        ->get();

        $student_attendance = [];
        $no_attendance = [];
        if (count($studentAttendance) == 0) {

            foreach ($students as $student) {

                $d['id'] = $student->id;
                $d['record_id'] = $student->id;
                $d['student_id'] = $student->student_id;
                $d['student_photo'] = $student->studentDetail->student_photo;
                $d['full_name'] = $student->studentDetail->full_name;
                $d['roll_no'] = $student->roll_no;
                $d['class_id'] = $student->class->id;
                $d['section_id'] = $student->section->id;
                $d['class_name'] = $student->class->class_name;
                $d['section_name'] = $student->section->section_name;
                $d['attendance_type'] = null;
                $d['user_id'] = $student->studentDetail->user_id;

                $no_attendance[] = $d;
            }
        } else {
            foreach ($students as $record) {
                $studentAttendanceFirst = SmStudentAttendance::where('student_id', $record->student_id)
                ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                ->where('student_record_id', $record->id)
                ->where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
                ->withOutGlobalScope(AcademicSchoolScope::class)
                ->where('school_id', $school_id)
                ->first();

                $d['id'] = $record->id;
                $d['record_id'] = $record->id;
                $d['student_id'] = $record->student_id;
                $d['student_photo'] = $record->studentDetail->student_photo;
                $d['full_name'] = $record->studentDetail->full_name;
                $d['roll_no'] = $record->roll_no;
                $d['class_id'] = $record->class->id;
                $d['section_id'] = $record->section->id;
                $d['class_name'] = $record->class->class_name;
                $d['section_name'] = $record->section->section_name;
                $d['attendance_type'] = $studentAttendanceFirst ? $studentAttendanceFirst->attendance_type : null;
                $d['user_id'] = $record->studentDetail->user_id;
                $student_attendance[] = $d;
            }
        }
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if (count($studentAttendance) > 0) {
                return ApiBaseMethod::sendResponse($student_attendance, null);
            } else {
                return ApiBaseMethod::sendResponse($no_attendance, 'Student attendance not done yet');
            }
        }

 
    }
    public function studentAttendanceStoreFirst(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'date' => "required",
            'class' => "required",
            'section' => "required",
            'record_id' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $student_ids = studentRecords($request, null, null)->get()->pluck('student_id')->unique();
        $students = SmStudent::whereIn('id', $student_ids)->select('id')->get();
        $attendance = SmStudentAttendance::where('student_id', $request->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->first();
        if (empty($attendance)) {
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->first();
                if ($attendance != "") {
                    $attendance->delete();
                } else {
                    $attendance = new SmStudentAttendance();
                    $attendance->student_id = $student->id;
                    $attendance->student_record_id = $request->record_id;
                    $attendance->class_id = $request->class;
                    $attendance->section_id = $request->section;
                    $attendance->attendance_type = "P";
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                    $attendance->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
                    $attendance->save();
                }
            }
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
        }
    }
    public function saas_studentAttendanceStoreFirst(Request $request, $school_id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'date' => "required",
            'class' => "required",
            'section' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $student_ids = studentRecords($request, null, null)->get()->pluck('student_id')->unique();
        $students = SmStudent::whereIn('id', $student_ids)->where('school_id', $school_id)->select('id')->get();

        $attendance = SmStudentAttendance::where('student_id', $request->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('school_id', $school_id)->first();
        if (empty($attendance)) {
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('school_id', $school_id)->first();
                if ($attendance != "") {
                    $attendance->delete();
                } else {
                    $attendance = new SmStudentAttendance();
                    $attendance->student_id = $student->id;
                    $attendance->attendance_type = "P";
                    $attendance->student_record_id = $request->record_id;
                    $attendance->class_id = $request->class;
                    $attendance->section_id = $request->section;
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                    $attendance->academic_id = SmAcademicYear::API_ACADEMIC_YEAR($school_id);
                    $attendance->save();
                }
            }
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
        }
    }
    public function studentAttendanceStoreSecond(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => "required",
            'date' => "required",
            'attendance' => "required",
            'class' => "required",
            'section' => "required",
            'record_id' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student_ids = studentRecords($request, null, null)->get()->pluck('student_id')->unique();
        $students = SmStudent::whereIn('id', $student_ids)->select('id')->get();
        $attendance = SmStudentAttendance::where('student_id', $request->id)->where('student_record_id', $request->record_id)
            ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
            ->first();
        // if (empty($attendance)) {
        //     foreach ($students as $student) {
        //         $attendance = SmStudentAttendance::where('student_id', $student->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('school_id', auth()->user()->school_id)->first();
        //         if ($attendance != "") {
        //             $attendance->delete();
        //         }

        //         $attendance = new SmStudentAttendance();
        //         $attendance->student_id = $student->id;
        //         $attendance->attendance_type = $request->id==$student->id ? $request->attendance :null;
        //         $attendance->student_record_id = $request->record_id;
        //         $attendance->class_id = $request->class;
        //         $attendance->section_id = $request->section;
        //         $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
        //         $attendance->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        //         $attendance->school_id = auth()->user()->school_id;
        //         $attendance->save();
        //     }
        // }
        $attendance = SmStudentAttendance::where('student_id', $request->id)->where('student_record_id', $request->record_id)
            ->where('attendance_date', date('Y-m-d', strtotime($request->date)))->first();
        if ($attendance != "") {
            $attendance->delete();
        }
        $attendance = new SmStudentAttendance();
        $attendance->student_id = $request->id;
        $attendance->attendance_type = $request->attendance;
        $attendance->student_record_id = $request->record_id;
        $attendance->class_id = $request->class;
        $attendance->section_id = $request->section;
        $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
        $attendance->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        $attendance->school_id = auth()->user()->school_id;
        $attendance->save();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
        }
    }
    public function saas_studentAttendanceStoreSecond(Request $request, $school_id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => "required",
            'date' => "required",
            'attendance' => "required",
            'class' => "required",
            'section' => "required",
            'record_id' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $student_ids = studentRecords($request, null, null)->get()->pluck('student_id')->unique();
        $students = SmStudent::whereIn('id', $student_ids)->select('id')->where('school_id', $school_id)->get();
        $attendance = SmStudentAttendance::where('student_id', $request->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('school_id', $school_id)->first();
        // if (empty($attendance)) {
        //     foreach ($students as $student) {
        //         $attendance = SmStudentAttendance::where('student_id', $student->id)->where('student_record_id', $request->record_id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('school_id', $school_id)->first();
        //         if ($attendance != "") {
        //             $attendance->delete();
        //         }

        //         $attendance = new SmStudentAttendance();
        //         $attendance->student_id = $student->id;
        //         $attendance->attendance_type = "P";
        //         $attendance->student_record_id = $request->record_id;
        //         $attendance->class_id = $request->class;
        //         $attendance->section_id = $request->section;
        //         $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
        //         $attendance->school_id = $school_id;
        //         $attendance->save();
        //     }
        // }
        $attendance = SmStudentAttendance::where('student_id', $request->id)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('school_id', $school_id)->first();
        if ($attendance != "") {
            $attendance->delete();
        }
        $attendance = new SmStudentAttendance();
        $attendance->student_id = $request->id;
        $attendance->attendance_type = $request->attendance;
        $attendance->student_record_id = $request->record_id;
        $attendance->class_id = $request->class;
        $attendance->section_id = $request->section;
        $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
        $attendance->school_id = $school_id;
        $attendance->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        $attendance->save();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
        }
    }
    public function student_attendance_index(Request $request)
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_student_attendance_index(Request $request, $school_id)
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function studentSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'attendance_date' => 'required',
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
            $date = $request->attendance_date;
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            if ($students->isEmpty()) {
                Toastr::error('No Result Found', 'Failed');
                return redirect('student-attendance');
            }

            $already_assigned_students = [];
            $new_students = [];
            $attendance_type = "";
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->first();

                if ($attendance != "") {
                    $already_assigned_students[] = $attendance;
                    $attendance_type = $attendance->attendance_type;
                } else {
                    $new_students[] = $student;
                }
            }

            $class_id = $request->class;
            $class_info = SmClass::find($request->class);
            $section_info = SmSection::find($request->section);

            $search_info['class_name'] = $class_info->class_name;
            $search_info['section_name'] = $section_info->section_name;
            $search_info['date'] = $request->attendance_date;

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['date'] = $date;
                $data['class_id'] = $class_id;
                $data['already_assigned_students'] = $already_assigned_students;
                $data['new_students'] = $new_students;
                $data['attendance_type'] = $attendance_type;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes', 'date', 'class_id', 'date', 'already_assigned_students', 'new_students', 'attendance_type', 'search_info'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_studentSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'attendance_date' => 'required',
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
            $date = $request->attendance_date;
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            if ($students->isEmpty()) {
                Toastr::error('No Result Found', 'Failed');
                return redirect('student-attendance');
            }

            $already_assigned_students = [];
            $new_students = [];
            $attendance_type = "";
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->first();

                if ($attendance != "") {
                    $already_assigned_students[] = $attendance;
                    $attendance_type = $attendance->attendance_type;
                } else {
                    $new_students[] = $student;
                }
            }

            $class_id = $request->class;
            $class_info = SmClass::find($request->class);
            $section_info = SmSection::find($request->section);

            $search_info['class_name'] = $class_info->class_name;
            $search_info['section_name'] = $section_info->section_name;
            $search_info['date'] = $request->attendance_date;

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['date'] = $date;
                $data['class_id'] = $class_id;
                $data['already_assigned_students'] = $already_assigned_students;
                $data['new_students'] = $new_students;
                $data['attendance_type'] = $attendance_type;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes', 'date', 'class_id', 'date', 'already_assigned_students', 'new_students', 'attendance_type', 'search_info'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function student_search_index(Request $request)
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_student_search_index(Request $request, $school_id)
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }

    public function studentAttendanceStore(Request $request)
    {

        try {
            foreach ($request->id as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->first();

                if ($attendance != "") {
                    $attendance->delete();
                }

                $attendance = new SmStudentAttendance();
                $attendance->student_id = $student;
                if (isset($request->mark_holiday)) {
                    $attendance->attendance_type = "H";
                } else {
                    $attendance->attendance_type = $request->attendance[$student];
                    $attendance->notes = $request->note[$student];
                }
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $attendance->save();
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
            }
            Toastr::success('Operation successful', 'Success');
            return redirect('student-attendance');
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_studentAttendanceStore(Request $request)
    {

        try {
            foreach ($request->id as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->first();

                if ($attendance != "") {
                    $attendance->delete();
                }

                $attendance = new SmStudentAttendance();
                $attendance->student_id = $student;
                if (isset($request->mark_holiday)) {
                    $attendance->attendance_type = "H";
                } else {
                    $attendance->attendance_type = $request->attendance[$student];
                    $attendance->notes = $request->note[$student];
                }
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $attendance->school_id = $request->school_id;
                $attendance->save();
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
            }
            Toastr::success('Operation successful', 'Success');
            return redirect('student-attendance');
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function studentAttendanceReport(Request $request)
    {
        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();
            $types = SmStudentCategory::all();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['types'] = $types->toArray();
                $data['genders'] = $genders->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'types', 'genders'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_studentAttendanceReport(Request $request, $school_id)
    {
        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();
            $types = SmStudentCategory::where('school_id', $school_id)->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['types'] = $types->toArray();
                $data['genders'] = $genders->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'types', 'genders'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }

    public function studentAttendanceReportSearch(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'month' => 'required',
            'year' => 'required',
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
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();
            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            $attendances = [];
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', 'like', $request->year . '-' . $request->month . '%')->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();
                if (count($attendance) != 0) {
                    $attendances[] = $attendance;
                }
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['attendances'] = $attendances;
                $data['days'] = $days;
                $data['year'] = $year;
                $data['month'] = $month;
                $data['current_day'] = $current_day;
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id', 'clas', 'sec'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_studentAttendanceReportSearch(Request $request, $school_id)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'month' => 'required',
            'year' => 'required',
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
            $clas = SmClass::where('school_id', $school_id)->findOrFail($request->class);
            $sec = SmSection::where('school_id', $school_id)->findOrFail($request->section);
            $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();
            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();

            $attendances = [];
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', 'like', $request->year . '-' . $request->month . '%')->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();
                if (count($attendance) != 0) {
                    $attendances[] = $attendance;
                }
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['attendances'] = $attendances;
                $data['days'] = $days;
                $data['year'] = $year;
                $data['month'] = $month;
                $data['current_day'] = $current_day;
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id', 'clas', 'sec'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function studentAttendanceReport_search(Request $request)
    {
        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();
            $types = SmStudentCategory::all();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['types'] = $types->toArray();
                $data['genders'] = $genders->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'types', 'genders'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function saas_studentAttendanceReport_search(Request $request, $school_id)
    {
        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();
            $types = SmStudentCategory::where('school_id', $school_id)->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())->where('school_id', $school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['types'] = $types->toArray();
                $data['genders'] = $genders->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'types', 'genders'));
        } catch (\Exception$e) {
            return ApiBaseMethod::sendError('Error.', $e->getMessage());
        }
    }
    public function studentMyAttendanceSearchAPI(Request $request, $id = null, $record_id)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'month' => "required",
            'year' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $student_detail = SmStudent::where('user_id', $id)->first();

        $year = $request->year;
        $month = $request->month;
        if ($month < 10) {
            $month = '0' . $month;
        }
        $current_day = date('d');

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $request->year);
        $days2 = '';
        if ($month != 1) {
            $days2 = cal_days_in_month(CAL_GREGORIAN, $month - 1, $request->year);
        } else {
            $days2 = cal_days_in_month(CAL_GREGORIAN, $month, $request->year);
        }

        $previous_month = $month - 1;
        $previous_date = $year . '-' . $previous_month . '-' . $days2;

        $previousMonthDetails['date'] = $previous_date;
        $previousMonthDetails['day'] = $days2;
        $previousMonthDetails['week_name'] = date('D', strtotime($previous_date));

        $attendances = SmStudentAttendance::where('student_id', $student_detail->id)
            ->where('attendance_date', 'like', '%' . $request->year . '-' . $month . '%')
            ->where('student_record_id', $record_id)
            ->select('attendance_type', 'attendance_date')
            ->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data['attendances'] = $attendances;
            $data['previousMonthDetails'] = $previousMonthDetails;
            $data['days'] = $days;
            $data['year'] = $year;
            $data['month'] = $month;
            $data['current_day'] = $current_day;
            $data['status'] = 'Present: P, Late: L, Absent: A, Holiday: H, Half Day: F';
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.studentPanel.student_attendance', compact('attendances', 'days', 'year', 'month', 'current_day'));
    }
    public function saas_studentMyAttendanceSearchAPI(Request $request, $school_id, $id = null, $record_id)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'month' => "required",
            'year' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $student_detail = SmStudent::where('user_id', $id)->first();

        $year = $request->year;
        $month = $request->month;
        if ($month < 10) {
            $month = '0' . $month;
        }
        $current_day = date('d');

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $request->year);
        $days2 = '';
        if ($month != 1) {
            $days2 = cal_days_in_month(CAL_GREGORIAN, $month - 1, $request->year);
        } else {
            $days2 = cal_days_in_month(CAL_GREGORIAN, $month, $request->year);
        }

        $previous_month = $month - 1;
        $previous_date = $year . '-' . $previous_month . '-' . $days2;

        $previousMonthDetails['date'] = $previous_date;
        $previousMonthDetails['day'] = $days2;
        $previousMonthDetails['week_name'] = date('D', strtotime($previous_date));

        $attendances = SmStudentAttendance::where('student_id', $student_detail->id)
            ->where('attendance_date', 'like', '%' . $request->year . '-' . $month . '%')
            ->where('student_record_id', $record_id)
            ->where('school_id', $school_id)
            ->select('attendance_type', 'attendance_date')           
            ->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data['attendances'] = $attendances;
            $data['previousMonthDetails'] = $previousMonthDetails;
            $data['days'] = $days;
            $data['year'] = $year;
            $data['month'] = $month;
            $data['current_day'] = $current_day;
            $data['status'] = 'Present: P, Late: L, Absent: A, Holiday: H, Half Day: F';
            return ApiBaseMethod::sendResponse($data, null);
        }
        //Test
        return view('backEnd.studentPanel.student_attendance', compact('attendances', 'days', 'year', 'month', 'current_day'));
    }
    

    // Route::post('student-attendance-store-all', 'api\ApiSmStudentAttendanceController@studentStoreAttendanceAllApi');
    // 22/04/22
    public function studentStoreAttendanceAllApi(Request $request)
    {
        try {
            // return $request->all();

            foreach ($request->attendance as $record_id => $student) {
                    $attendance = SmStudentAttendance::where('student_id', gv($student, 'student'))
                    ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                   
                    ->when(!moduleStatusCheck('University'), function ($query) use ($student) {
                        $query->where('class_id', gv($student, 'class'));
                    })->when(!moduleStatusCheck('University'), function ($query) use ($student) {
                        $query->where('section_id', gv($student, 'section'));
                    })
                    ->where('student_record_id', $record_id)
                    ->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id))
                    ->where('school_id', $request->user()->school_id)->first();
                if ($attendance) {
                    $attendance->delete();
                }
                $attendance = new SmStudentAttendance();
                $attendance->student_record_id = $record_id;
                $attendance->student_id = gv($student, 'student');
                $attendance->class_id = gv($student, 'class');
                $attendance->section_id = gv($student, 'section');
                if (isset($request->mark_holiday)) {
                    $attendance->attendance_type = "H";
                } else {
                    $attendance->attendance_type = gv($student, 'attendance_type');
                    $attendance->notes = gv($student, 'note');
                }
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $attendance->school_id = $request->user()->school_id;
                $attendance->academic_id = SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id);
                
                $attendance->save();

                $studentInfo = StudentRecord::find($record_id);
                $compact['attendance_date'] = date('Y-m-d', strtotime($request->date));
                if(gv($student, 'attendance_type') == "P"){
                    $compact['user_email'] = $studentInfo->studentDetail->email;
                    $compact['student_name'] = $studentInfo->studentDetail->full_name;
                    @send_sms($studentInfo->studentDetail->mobile, 'student_attendance', $compact);

                    $compact['user_email'] = $studentInfo->studentDetail->parents->guardians_email;
                    $compact['parent_name'] = $studentInfo->studentDetail->parents->guardians_name;
                    @send_sms($studentInfo->studentDetail->parents->guardians_mobile, 'student_attendance_for_parent', $compact);
                }elseif(gv($student, 'attendance_type') == "L"){
                    $compact['user_email'] = $studentInfo->studentDetail->email;
                    $compact['student_name'] = $studentInfo->studentDetail->full_name;
                    @send_sms($studentInfo->studentDetail->mobile, 'student_late', $compact);

                    $compact['user_email'] = $studentInfo->studentDetail->parents->guardians_email;
                    $compact['parent_name'] = $studentInfo->studentDetail->parents->guardians_name;
                    @send_sms($studentInfo->studentDetail->parents->guardians_mobile, 'student_late_for_parent', $compact);
                }elseif(gv($student, 'attendance_type') == "A"){
                    $compact['user_email'] = $studentInfo->studentDetail->email;
                    $compact['student_name'] = $studentInfo->studentDetail->full_name;
                    @send_sms($studentInfo->studentDetail->mobile, 'student_absent', $compact);

                    $compact['user_email'] = $studentInfo->studentDetail->parents->guardians_email;
                    $compact['parent_name'] = $studentInfo->studentDetail->parents->guardians_name;
                    @send_sms($studentInfo->studentDetail->parents->guardians_mobile, 'student_absent_for_parent', $compact);
                }


                if(gv($student, 'student')){

                    $student = SmStudent::with('user')->find(gv($student, 'student'));
                    if($student){
                        $notification = new SmNotification();
                        // $notification->user_id = $leave_request_data->id;
                        $notification->user_id = $student->user_id;
                        $notification->role_id = 3;
                        $notification->date = date('Y-m-d');
                        $notification->message = app('translator')->get('student.attendace_set_for_as', ['date' => date('Y-m-d'), 'status' => $attendance->attendance_type]);
                        $notification->school_id = $student->school_id;
                        $notification->academic_id = SmAcademicYear::API_ACADEMIC_YEAR($request->user()->school_id);
                        $notification->save();
                        if($user = $student->user){
                            try{
                                Notification::send($user, new StudentAttendanceSetNotification($notification));
                            }
                            catch (\Exception $e) {
                                Log::info($e->getMessage());
                            }
                        }
                       
                    }
                }
            }
           return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
        } catch (\Exception $e) {
            return ApiBaseMethod::sendResponse('error'.$e->getMessage(), null);
        }
    }
    
    public function deviceInfo(Request $request)
    {
        $sms = SmSmsGateway::where('gateway_name', 'Mobile SMS')->first();
        if ($sms) {
            $sms->device_info = json_encode($request->all());
            $result = $sms->save();
            if ($result) {
                return ApiBaseMethod::sendResponse('success', null);
            }
        } else {
            return ApiBaseMethod::sendResponse('error', null);
        }
    }
}
