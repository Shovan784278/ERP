<?php

namespace Modules\Lesson\Http\Controllers;

use App\SmAssignSubject;
use App\SmClass;
use App\SmSection;
use App\SmStaff;
use App\SmSubject;
use App\YearCheck;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Lesson\Entities\LessonPlanner;
use Modules\Lesson\Entities\SmLesson;
use Modules\Lesson\Entities\SmLessonTopic;
use Modules\Lesson\Entities\SmLessonTopicDetail;
use Modules\Lesson\Http\Requests\SmLessonTopicRequest;

class SmTopicController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('PM');
    }
    public function index()
    {
        try {
            $data = $this->loadTopic();
            return view('lesson::topic.topic', $data);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function store(Request $request)
    {
        // return $request->all();
        $request->validate(
            [
                'class' => 'required',
                'subject' => 'required',
                'section' => 'required',
                'lesson' => 'required',
            ],
        );
        DB::beginTransaction();
        $is_duplicate = SmLessonTopic::where('school_id', Auth::user()->school_id)->where('class_id', $request->class)->where('lesson_id', $request->lesson)->where('section_id', $request->section)->where('subject_id', $request->subject)->where('academic_id', getAcademicId())->first();
        if ($is_duplicate) {
            $length = count($request->topic);
            for ($i = 0; $i < $length; $i++) {
                $topicDetail = new SmLessonTopicDetail;
                $topic_title = $request->topic[$i];
                $topicDetail->topic_id = $is_duplicate->id;
                $topicDetail->topic_title = $topic_title;
                $topicDetail->lesson_id = $request->lesson;
                $topicDetail->school_id = Auth::user()->school_id;
                $topicDetail->academic_id = getAcademicId();
                $topicDetail->save();
            }
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } else {
            try {
                $smTopic = new SmLessonTopic;
                $smTopic->class_id = $request->class;
                $smTopic->section_id = $request->section;
                $smTopic->subject_id = $request->subject;
                $smTopic->lesson_id = $request->lesson;
                $smTopic->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                $smTopic->school_id = Auth::user()->school_id;
                $smTopic->academic_id = getAcademicId();
                $smTopic->save();
                $smTopic_id = $smTopic->id;
                $length = count($request->topic);
                for ($i = 0; $i < $length; $i++) {
                    $topicDetail = new SmLessonTopicDetail;
                    $topic_title = $request->topic[$i];
                    $topicDetail->topic_id = $smTopic_id;
                    $topicDetail->topic_title = $topic_title;
                    $topicDetail->lesson_id = $request->lesson;
                    $topicDetail->school_id = Auth::user()->school_id;
                    $topicDetail->academic_id = getAcademicId();
                    $topicDetail->save();
                }
                DB::commit();
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();

            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    }
    public function edit($id)
    {

        try {
            $data = $this->loadTopic();
            $data['topic'] = SmLessonTopic::where('academic_id', getAcademicId())->where('id', $id)->where('school_id', Auth::user()->school_id)->first();
            $data['lessons'] = SmLesson::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $data['topicDetails'] = SmLessonTopicDetail::where('topic_id', $data['topic']->id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            return view('lesson::topic.editTopic', $data);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function updateTopic(Request $request)
    {
        try {
            $length = count($request->topic);
            for ($i = 0; $i < $length; $i++) {
                $topicDetail = SmLessonTopicDetail::find($request->topic_detail_id[$i]);
                $topic_title = $request->topic[$i];
                $topicDetail->topic_title = $topic_title;
                $topicDetail->school_id = Auth::user()->school_id;
                $topicDetail->academic_id = getAcademicId();
                $topicDetail->save();
            }

            Toastr::success('Operation successful', 'Success');
            return redirect('/lesson/topic');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function topicdelete($id)
    {
        $topic = SmLessonTopic::find($id);
        $topic->delete();
        $topicDetail = SmLessonTopicDetail::where('topic_id', $id)->get();
        if ($topicDetail) {
            foreach ($topicDetail as $data) {
                SmLessonTopicDetail::destroy($data->id);
                LessonPlanner::where('topic_detail_id', $data->id)->get();
            }
        }

        $topicLessonPlan = LessonPlanner::where('topic_id', $id)->get();
        if ($topicLessonPlan) {
            foreach ($topicLessonPlan as $topic_data) {
                LessonPlanner::destroy($topic_data->id);
            }
        }

        Toastr::success('Operation successful', 'Success');
        return redirect()->back();

    }
    public function deleteTopicTitle($id)
    {
        SmLessonTopicDetail::destroy($id);
        $topicDetail = LessonPlanner::where('topic_detail_id', $id)->get();
        if ($topicDetail) {
            foreach ($topicDetail as $data) {

                LessonPlanner::destroy($data->id);
            }
        }

        Toastr::success('Operation successful', 'Success');
        return redirect()->back();
    }
    public function loadTopic()
    {
        $teacher_info = SmStaff::where('user_id', Auth::user()->id)->first();
        if (Auth::user()->role_id == 4) {
            $subjects = SmAssignSubject::select('subject_id')->where('teacher_id', $teacher_info->id)->get();
            $data['topics'] = SmLessonTopic::with('lesson', 'class', 'section', 'subject')->whereIn('subject_id', $subjects)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

        } else {
            $data['topics'] = SmLessonTopic::with('lesson', 'class', 'section', 'subject')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
        }

        if (Auth::user()->role_id == 1) {
            $data['classes'] = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
        } else {
            $data['classes'] = SmAssignSubject::where('teacher_id', $teacher_info->id)
                ->join('sm_classes', 'sm_classes.id', 'sm_assign_subjects.class_id')
                ->where('sm_assign_subjects.active_status', 1)
                ->where('sm_assign_subjects.school_id', Auth::user()->school_id)
                ->where('sm_assign_subjects.academic_id', getAcademicId())
                ->select('sm_classes.id', 'class_name')
                ->get();
        }
        $data['subjects'] = SmSubject::get();
        $data['sections'] = SmSection::get();
        return $data;
    }
}
