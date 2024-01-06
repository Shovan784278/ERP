<?php

namespace Modules\Lesson\Http\Controllers;

use App\SmClass;
use App\SmStaff;
use App\SmSection;
use App\SmSubject;
use App\SmWeekend;
use Carbon\Carbon;
use App\SmClassRoom;
use App\SmClassTime;
use App\SmClassSection;
use App\SmAssignSubject;
use Carbon\CarbonPeriod;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use App\SmClassRoutineUpdate;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Lesson\Entities\SmLesson;
use Illuminate\Support\Facades\Validator;
use Modules\Lesson\Entities\LessonPlanner;
use Modules\Lesson\Entities\SmLessonTopic;
use Modules\Lesson\Entities\LessonPlanTopic;
use Modules\Lesson\Entities\SmLessonTopicDetail;

class LessonPlanController extends Controller
{

    public function index(Request $request)
    {

        try {
            $data = $this->loadDefault();

            return view('lesson::lessonPlan.lesson_planner', $data);
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function searchTeacherLessonPlan($id)
    {
        try {
            $data = $this->loadDefault();
            $data['class_times'] = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->orderBy('period', 'ASC')->get();
            $data['teacher_id'] = $id;

            return view('lesson::lessonPlan.lesson_planner', $data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function lessonPlannerSearch(Request $request)
    {
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'teacher' => 'required',

            ]);

            if ($validator->fails()) {

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            try {

                $teacher_id = $request->teacher;

                return redirect()->route('lesson.lesson-planner-teacher-search', [$teacher_id]);

                // $data = $this->loadDefault();
                // $data['class_times'] = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->orderBy('period', 'ASC')->get();
                // $data['teacher_id'] = $request->teacher;

                // return view('lesson::lessonPlan.lesson_planner', $data);
            } catch (\Exception $e) {

                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }

        }
    }

    public function lessonPlannerLesson($day, $teacher_id, $routine_id, $lesson_date)
    {

        try {
            $routine = SmClassRoutineUpdate::where('id', $routine_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->first();
            $class_id = $routine->class_id;
            $section_id = $routine->section_id;
            $subject_id = $routine->subject_id;
            $lesson_date = $lesson_date;
            $lessons = SmLesson::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('subject_id', $subject_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $teacher_detail = SmStaff::select('id', 'full_name')->where('id', $teacher_id)->first();

            return view('lesson::lessonPlan.add_new_lesson_planner_form', compact('teacher_id', 'lesson_date', 'day', 'class_id', 'section_id', 'subject_id', 'teacher_detail', 'lessons', 'routine_id'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function addNewLessonPlan(Request $request)
    {

        try {
            //  return  $request->all();
            $path = "Modules/Lesson/Resources/assets/document/";          

            $lesson = SmLesson::find($request->lesson);
            $lesson_id = $lesson->id;
            
            $lessonPlanner = new LessonPlanner;
            $lessonPlanner->day = $request->day;
            $lessonPlanner->lesson_id = $lesson_id;
            $lessonPlanner->lesson_detail_id = $request->lesson;
            if ($request->customize !="customize") {
                $lessonPlanner->topic_id = $request->topic;
                $lessonPlanner->sub_topic = $request->sub_topic;
                $lessonPlanner->topic_detail_id = $request->topic;
            }
            $lessonPlanner->lecture_youube_link = $request->youtube_link;
            $lessonPlanner->attachment = fileUpload($request->file('photo'), $path);           
            $lessonPlanner->teaching_method = $request->teaching_method;
            $lessonPlanner->general_objectives = $request->general_Objectives;
            $lessonPlanner->previous_knowlege = $request->previous_knowledge;
            $lessonPlanner->comp_question = $request->comprehensive_Questions;
            $lessonPlanner->zoom_setup = $request->zoom_setup;
            $lessonPlanner->presentation = $request->presentation;
            $lessonPlanner->note = $request->note;
            $lessonPlanner->lesson_date = $request->lesson_date;
            $lessonPlanner->teacher_id = $request->teacher_id;
            $lessonPlanner->subject_id = $request->subject_id;
            $lessonPlanner->class_id = $request->class_id;
            $lessonPlanner->section_id = $request->section_id;
            $lessonPlanner->routine_id = $request->routine_id;
            $lessonPlanner->created_by = Auth::user()->id;
            $lessonPlanner->school_id = Auth::user()->school_id;
            $lessonPlanner->academic_id = getAcademicId();
            $lessonPlanner->save();

            if ($request->customize=="customize") {
                foreach ($request->topic as $key=>$topic) {
                    if ($topic !='') {
                        $LessonPlanTopic  = new LessonPlanTopic;
                        $LessonPlanTopic->topic_id = $topic;
                        $LessonPlanTopic->sub_topic_title = $request->sub_topic[$key] ?? '';
                        $LessonPlanTopic->lesson_planner_id = $lessonPlanner->id;
                        $LessonPlanTopic->save();
                    }
                }
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function ViewlessonPlannerLesson($lessonPlan_id)
    {
        try {
            $lessonPlanDetail = LessonPlanner::find($lessonPlan_id);

            $class_id = $lessonPlanDetail->class_id;
            $section_id = $lessonPlanDetail->section_id;
            $subject_id = $lessonPlanDetail->subject_id;
            $lesson_date = $lessonPlanDetail->lesson_date;
            $teacher_id = $lessonPlanDetail->teacher_id;
            $day = $lessonPlanDetail->day;

            $teacher_detail = SmStaff::select('id', 'full_name')->where('id', $teacher_id)->first();

            return view('lesson::lessonPlan.view_lesson_plan', compact('lessonPlanDetail', 'teacher_detail'));
            $room_id = 401;
            $lessons = SmLesson::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('subject_id', $subject_id)
                ->get();
            $assinged_subjects = SmClassRoutineUpdate::select('subject_id')->where('class_id', $class_id)->where('section_id', $section_id)->where('day', $day)->where('subject_id', '!=', $subject_id)->where('school_id', Auth::user()->school_id)->get();

            $assinged_subject = [];
            foreach ($assinged_subjects as $value) {
                $assinged_subject[] = $value->subject_id;
            }

            $assinged_rooms = SmClassRoutineUpdate::select('room_id')->where('room_id', '!=', $room_id)->where('class_period_id', $class_time_id)->where('day', $day)->where('school_id', Auth::user()->school_id)->get();

            $assinged_room = [];
            foreach ($assinged_rooms as $value) {
                $assinged_room[] = $value->room_id;
            }
            $rooms = SmClassRoom::get();
            $teacher_detail = SmStaff::select('id', 'full_name')->where('id', $teacher_id)->first();

            $subjects = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->get();
            return view('lesson::lessonPlan.view_lesson_plan', compact('lessonPlanDetail', 'lesson_date', 'rooms', 'lessons', 'subjects', 'day', 'class_time_id', 'class_id', 'section_id', 'assinged_subject', 'assinged_room', 'subject_id', 'room_id', 'assigned_id', 'teacher_detail'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function EditlessonPlannerLesson($lessonPlan_id)
    {
        try {

            $lessonPlanDetail = LessonPlanner::find($lessonPlan_id);

            $class_id = $lessonPlanDetail->class_id;
            $section_id = $lessonPlanDetail->section_id;
            $subject_id = $lessonPlanDetail->subject_id;
            $lesson_date = $lessonPlanDetail->lesson_date;
            $teacher_id = $lessonPlanDetail->teacher_id;
            $day = $lessonPlanDetail->day;

            $topic = SmLessonTopicDetail::where('lesson_id', $lessonPlanDetail->lesson_detail_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();
            $lessons = SmLesson::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('subject_id', $subject_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $lesson_date = $lesson_date;
            $lessonPlanDetail = LessonPlanner::find($lessonPlan_id);
            $topic = SmLessonTopicDetail::where('lesson_id', $lessonPlanDetail->lesson_detail_id)
                ->get();
           
            $lessons = SmLesson::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('subject_id', $subject_id)
                ->get();
            $assinged_subjects = SmClassRoutineUpdate::select('subject_id')->where('class_id', $class_id)->where('section_id', $section_id)->where('day', $day)->where('subject_id', '!=', $subject_id)->where('school_id', Auth::user()->school_id)->get();

            $assinged_subject = [];
            foreach ($assinged_subjects as $value) {
                $assinged_subject[] = $value->subject_id;
            }

            $teacher_detail = SmStaff::select('id', 'full_name')->where('id', $teacher_id)->first();

            return view('lesson::lessonPlan.edit_lesson_planner_form', compact('lessonPlanDetail', 'topic', 'lesson_date', 'lessons', 'day', 'class_id', 'section_id', 'subject_id', 'teacher_detail'));
            $assinged_room = [];
            foreach ($assinged_rooms as $value) {
                $assinged_room[] = $value->room_id;
            }
            $rooms = SmClassRoom::get();
            $teacher_detail = SmStaff::select('id', 'full_name')->where('id', $teacher_id)->first();

            $subjects = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->get();
            return view('lesson::lessonPlan.edit_lesson_planner_form', compact('lessonPlanDetail', 'topic', 'lesson_date', 'rooms', 'lessons', 'subjects', 'day', 'class_time_id', 'class_id', 'section_id', 'assinged_subject', 'assinged_room', 'subject_id', 'room_id', 'assigned_id', 'teacher_detail'));
        } catch (\Exception $e) {
          
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function updateLessonPlan(Request $request)
    {
        //   return $request->all();

        try {

            $path = 'Modules/Lesson/Resources/assets/document/';

            $lessonPlanner = LessonPlanner::find($request->lessonPlan_id);

            $lessonPlanner->lesson_id = $request->lesson;
            if ($request->customize != 'customize') {
                $lessonPlanner->topic_id = $request->topic;
                $lessonPlanner->lesson_detail_id = $request->lesson;
                $lessonPlanner->topic_detail_id = $request->topic;
                $lessonPlanner->sub_topic = $request->sub_topic;
            }
            $lessonPlanner->lecture_youube_link = $request->youtube_link;
            if ($request->file('photo') != "") {
                $lessonPlanner->attachment = fileUpdate($lessonPlanner->attachment, $request->file('photo'),$path);
            }

            $lessonPlanner->teaching_method = $request->teaching_method;
            $lessonPlanner->general_objectives = $request->general_Objectives;
            $lessonPlanner->previous_knowlege = $request->previous_knowledge;
            $lessonPlanner->comp_question = $request->comprehensive_Questions;
            $lessonPlanner->zoom_setup = $request->zoom_setup;
            $lessonPlanner->presentation = $request->presentation;
            $lessonPlanner->note = $request->note;
            $lessonPlanner->updated_by = Auth::user()->id;
            $lessonPlanner->school_id = Auth::user()->school_id;
            $lessonPlanner->academic_id = getAcademicId();
            $lessonPlanner->save();
            LessonPlanTopic::where('lesson_planner_id', $request->lessonPlan_id)->delete();
            if ($request->customize=="customize") {
                foreach ($request->topic as $key=>$topic) {
                    if ($topic !='') {
                        $LessonPlanTopic  = new LessonPlanTopic;
                        $LessonPlanTopic->topic_id = $topic;
                        $LessonPlanTopic->sub_topic_title = $request->sub_topic[$key] ?? '';
                        $LessonPlanTopic->lesson_planner_id = $lessonPlanner->id;
                        $LessonPlanTopic->save();
                    }
                }
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function changeWeek(Request $request, $teacher_id, $next_date)
    {

        $start_date = Carbon::parse($next_date)->addDay(1);
        $date = Carbon::parse($next_date)->addDay(1);

        $end_date = Carbon::parse($start_date)->addDay(7);
        $this_week = $week_number = $end_date->weekOfYear;

        $period = CarbonPeriod::create($start_date, $end_date);

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');

        }

        $user = Auth::user();
        $class_times = SmClassTime::where('type', 'class')->orderBy('period', 'ASC')->get();
        if ($user->role_id == 4) {
            $teacher_id = SmStaff::where('user_id', $user->id)->first('id')->id;
        } else {
            $teacher_id = $teacher_id;
        }
        $sm_weekends = SmWeekend::orderBy('order', 'ASC')->get();
        $teachers = SmStaff::where('role_id', 4)->get();
        return view('lesson::lessonPlan.lesson_planner', compact('period', 'dates', 'week_number', 'this_week', 'class_times', 'teacher_id', 'sm_weekends', 'teachers'));

    }
    public function discreaseChangeWeek(Request $request, $teacher_id, $pre_date)
    {

        $end_date = Carbon::parse($pre_date)->subDays(1);

        $start_date = Carbon::parse($end_date)->subDays(6);

        $this_week = $week_number = $end_date->weekOfYear;

        $period = CarbonPeriod::create($start_date, $end_date);

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');

        }

        $user = Auth::user();
        $class_times = SmClassTime::where('type', 'class')->orderBy('period', 'ASC')->get();
        $teacher_id = $teacher_id;
        $sm_weekends = SmWeekend::orderBy('order', 'ASC')->get();
        $teachers = SmStaff::where('role_id', 4)->get();
        return view('lesson::lessonPlan.lesson_planner', compact('period', 'dates', 'week_number', 'this_week', 'class_times', 'teacher_id', 'sm_weekends', 'teachers'));
    }
    public function topicOverview()
    {
        try {
            $lessons = SmLesson::groupBy('lesson_title')->get();

            $topics_detail = SmLessonTopicDetail::with('lesson_title', 'lessonPlan','lessonPlan.lessonDetail')->get();

            return view('lesson::lessonPlan.manage_lesson', compact('lessons', 'topics_detail'));
        } catch (\Exception $e) {

        }

    }
    public function topicOverviewSearch(Request $request)
    {
        try {

            $request->validate([
                'lesson' => 'required',
            ]);
            $lessons = SmLesson::groupBy('lesson_title')->get();
            $topics_detail = SmLessonTopicDetail::where('lesson_id', $request->lesson)->get();
            $lesson_id = $request->lesson;
            return view('lesson::lessonPlan.manage_lesson', compact('lesson_id', 'lessons', 'topics_detail'));

        } catch (\Exception $e) {

        }
    }
    public function manageLessonPlanner()
    {

        try {
            $classes = SmClass::get();
            $teachers = SmStaff::where('role_id', 4)->get();
            $lessonPlanDetail = LessonPlanner::get();
            $lessons = SmLesson::get();
            $topics = SmLessonTopic::get();

            return view('lesson::lessonPlan.manage_lesson_planner', compact('lessonPlanDetail', 'lessons', 'topics', 'classes', 'teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function searchLessonPlan(Request $request)
    {
        $request->validate([
            'class' => 'required',
            'teacher' => 'required',
            'section' => 'required',
            'subject' => 'required',

        ]);
        try {
            $total = LessonPlanner::where('teacher_id', $request->teacher)
                ->where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('subject_id', $request->subject)
                ->get()->count();
            $completed_total = LessonPlanner::where('completed_status', 'completed')
                ->where('teacher_id', $request->teacher)
                ->where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('subject_id', $request->subject)
                ->get()
                ->count();
            if ($total > 0) {
                $percentage = $completed_total / $total * 100;
            } else {
                $percentage = 0;
            }

            if ($request->teacher != "" && $request->class != "" && $request->section != "" && $request->subject != "") {
                $lessonPlanner = LessonPlanner::where('teacher_id', $request->teacher)
                    ->where('class_id', $request->class)
                    ->where('section_id', $request->section)
                    ->where('subject_id', $request->subject)
                    ->get();
                $alllessonPlanner = LessonPlanner::where('teacher_id', $request->teacher)
                    ->where('class_id', $request->class)
                    ->where('section_id', $request->section)
                    ->where('subject_id', $request->subject)
                    ->get();

            }

            $class_id = $request->class;
            $teacher_id = $request->teacher;
            $section_id = $request->section;
            $subject_id = $request->subject;

            $classes = SmClass::get();

            $sectionIds = SmClassSection::where('class_id', '=', $class_id)->get();
            $sections = [];
            foreach ($sectionIds as $sectionId) {
                $sections[] = SmSection::find($sectionId->section_id);
            }

            $subjects = SmAssignSubject::with('subject')->where('class_id', $class_id)->where('section_id', $section_id)->get();

            $teachers = SmStaff::where('role_id', 4)->get();

            return view('lesson::lessonPlan.manage_lesson_planner', compact('total', 'completed_total', 'alllessonPlanner', 'lessonPlanner', 'classes', 'teachers', 'percentage', 'subjects', 'sections', 'class_id', 'section_id', 'subject_id', 'teacher_id'));

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function completeStatus(Request $request)
    {

        try {

            $topic_id = $request->topic_id;
            $statusUpdate = SmLessonTopicDetail::where('id', $topic_id)->first();
            if ($request->cancel == 'incomplete') {
                $statusUpdate->competed_date = null;
                $statusUpdate->completed_status = null;
            } else {
                $statusUpdate->competed_date = date('Y-m-d', strtotime($request->complete_date));
                $statusUpdate->completed_status = "completed";
            }
            $statusUpdate->save();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function LessonPlanStatus(Request $request)
    {

        try {
            $statusUpdateLessonPlan = LessonPlanner::find($request->lessonplan_id);
            if ($request->has('cancel')) {
                $statusUpdateLessonPlan->competed_date = null;
                $statusUpdateLessonPlan->completed_status = null;
            } else {
                $statusUpdateLessonPlan->competed_date = date('Y-m-d', strtotime($request->complete_date));
                $statusUpdateLessonPlan->completed_status = "completed";
            }
            $statusUpdateLessonPlan->save();

            Toastr::success('Operation successful', 'Success');

            return redirect()->back();
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function lessonPlanstatusAjax(Request $request)
    {

        try {
            $statusUpdateLessonPlan = LessonPlanner::find($request->lessonplan_id);
            $statusUpdateLessonPlan->competed_date = $request->complete_date;
            $statusUpdateLessonPlan->completed_status = $request->status;
            $statusUpdateLessonPlan->save();

            return response(["done"]);

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function teacherLessonPlanOverview()
    {
        if (Auth::user()->role_id == 4) {
            try {
                $this_week = $weekNumber = date("W");
                $period = CarbonPeriod::create(Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d'), Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d'));
                $dates = [];
                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');

                }
                $user = Auth::user();
                $class_times = SmClassTime::where('type', 'class')->orderBy('id', 'ASC')->get();
                $teacher_id = Auth::user()->id;
                $sm_weekends = SmWeekend::orderBy('order', 'ASC')->get();
                $teachers = SmStaff::where('role_id', 4)->get();

                return view('lesson::teacher.teacher_lesson_plan_overview', compact('dates', 'this_week', 'class_times', 'teacher_id', 'sm_weekends', 'teachers'));
            } catch (\Exception $e) {

                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }

    }
    public function lessonPlanReport()
    {

        try {

            $teachers = SmStaff::where('role_id', 4)->get();
            $lessonPlanDetail = LessonPlanner::get();
            $lessons = SmLesson::groupBy('lesson_title')->get();
            $topics = SmLessonTopic::get();

            return view('lesson::lessonPlan.report_lesson_plan', compact('lessonPlanDetail', 'lessons', 'topics', 'teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function searchlessonPlanReport(Request $request)
    {
        try {
            $staff_info = SmStaff::where('user_id', Auth::user()->id)->first();

            if (Auth::user()->role_id == '1') {
                $subject_all = SmAssignSubject::where('teacher_id', '=', $request->teacher)->distinct('subject_id')->get();
            } else {
                $subject_all = SmAssignSubject::where('teacher_id', $staff_info->id)->distinct('subject_id')->get();
            }
            $students = [];
            foreach ($subject_all as $allSubject) {
                $students[] = SmSubject::find($allSubject->subject_id);
            }

            $request->validate([

                'teacher' => 'required',

                'subject' => 'required',

            ]);

            $total = LessonPlanner::where('teacher_id', $request->teacher)
                ->where('subject_id', $request->subject)
                ->get()->count();
            $completed_total = LessonPlanner::where('completed_status', 'completed')
                ->where('teacher_id', $request->teacher)
                ->where('subject_id', $request->subject)
                ->get()
                ->count();
            if ($total > 0) {
                $percentage = $completed_total / $total * 100;
            } else {
                $percentage = 0;
            }

            if ($request->teacher != "" && $request->subject != "") {
                $lessonPlanner = LessonPlanner::where('teacher_id', $request->teacher)

                    ->where('subject_id', $request->subject)
                    ->groupBy('lesson_detail_id')
                    ->get();
                $alllessonPlanner = LessonPlanner::where('teacher_id', $request->teacher)

                    ->where('subject_id', $request->subject)
                    ->get();

            }

            $teacher_id = $request->teacher;

            $subject_id = $request->subject;

            $subjectIds = SmAssignSubject::where('teacher_id', $teacher_id)->get();
            $subjects = [];
            foreach ($subjectIds as $subjectId) {
                $subjects[] = SmSubject::find($subjectId->subject_id);
            }
            $teachers = SmStaff::where('role_id', 4)->get();

            return view('lesson::lessonPlan.report_lesson_plan', compact('total', 'completed_total', 'alllessonPlanner', 'lessonPlanner', 'teachers', 'percentage', 'subjects', 'subject_id', 'teacher_id'));

        } catch (\Exception $e) {

        }
    }

    public function ajaxSelectSubject(Request $request)
    {

        try {
            $staff_info = SmStaff::where('user_id', Auth::user()->id)->first();

            if (Auth::user()->role_id == '1') {
                $subject_all = SmAssignSubject::where('teacher_id', '=', $request->teacher)->distinct('subject_id')->groupBy('subject_id')->get();
            } else {
                $subject_all = SmAssignSubject::where('teacher_id', $staff_info->id)->groupBy('subject_id')->distinct('subject_id')->get();
            }
            $students = [];
            foreach ($subject_all as $allSubject) {
                $students[] = SmSubject::find($allSubject->subject_id);
            }
            return response()->json([$students]);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function loadDefault()
    {
        $data['this_week'] = $weekNumber = date("W");
        $week_end = SmWeekend::where('id',generalSetting()->week_start_id)->value('name');
  
        $start_day = WEEK_DAYS_BY_NAME[$week_end ?? 'Saturday'];
        $end_day = $start_day==0 ? 6 : $start_day-1;
        $end_day = $start_day==0 ? 6 : $start_day-1;
        $data['period'] = CarbonPeriod::create(Carbon::now()->startOfWeek($start_day)->format('Y-m-d'), Carbon::now()->endOfWeek($end_day)->format('Y-m-d'));
        $data['dates'] = [];
        foreach ($data['period'] as $date) {
            $data['dates'][] = $date->format('Y-m-d');
        }
        $data['sm_weekends'] = SmWeekend::with('teacherClassRoutineAdmin')->where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
        $data['teachers'] = SmStaff::where('active_status', 1)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->get();

        return $data;

    }

    public function deleteLessonPlanModal($Plan_id)
    {
        try {

            $id = $Plan_id;
            return view('lesson::lessonPlan.delete_lesson_planner_form', compact('id'));
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function deleteLessonPlan($id)
    {
        try {
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 5) {
                $lessonPlan = LessonPlanner::find($id)->delete();
            } else {
                $user_id = Auth::user()->id;
                $lessonPlan = LessonPlanner::find($id);
                if ($user_id == $lessonPlan->created_by) {
                    $lessonPlan->delete();
                } else {
                    Toastr::error('This Lesson Created By Admin ,You Have No Right!', 'Failed');
                    return redirect()->back();
                }
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();

        }
    }

    public function deleteLessonTopic(Request $request)
    {
        $id = $request->lessonplantopic_id;
        if ($request->filled('lessonplantopic_id') && $request->filled('lessonPlan_id')) {
            LessonPlanTopic::where('id', $id)->delete();
            return response()->json(['success'=>'Operation Success']);
        }
        return response()->json(['failed'=>'Operation Failed']);
    }

    public function setting()
    {

        try {
            return view('lesson::lessonPlan.setting');
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function postSetting(Request $request)
    {
        try {
            $general_settings = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();
            $general_settings->sub_topic_enable = $request->sub_topic_enable;
            $general_settings->save();
            session()->forget('generalSetting');
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

}
