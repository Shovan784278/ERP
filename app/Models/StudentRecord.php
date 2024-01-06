<?php

namespace App\Models;

use App\SmExam;
use App\SmClass;
use App\SmExamType;
use App\SmHomework;
use App\SmFeesAssign;
use App\SmOnlineExam;
use App\SmAssignSubject;
use App\SmStudentAttendance;
use App\SmFeesAssignDiscount;
use App\SmClassOptionalSubject;
use App\SmTeacherUploadContent;
use App\SmStudentTakeOnlineExam;
use Modules\Lms\Entities\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Modules\Zoom\Entities\VirtualClass;
use Modules\ExamPlan\Entities\AdmitCard;
use Modules\Fees\Entities\FmFeesInvoice;
use App\Scopes\StatusAcademicSchoolScope;
use Modules\BBB\Entities\BbbVirtualClass;
use Modules\Jitsi\Entities\JitsiVirtualClass;
use Modules\OnlineExam\Entities\InfixPdfExam;
use Modules\OnlineExam\Entities\InfixOnlineExam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton;
use Modules\OnlineExam\Entities\InfixStudentTakeOnlineExam;

class StudentRecord extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    

    public function class()
    {
        return $this->belongsTo('App\SmClass', 'class_id', 'id')->withDefault()->withoutGlobalScope(StatusAcademicSchoolScope::class);
    }

    public function admitcard()
    {
        return $this->belongsTo(AdmitCard::class,'student_record_id');
    }

    public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id')->withDefault()->withoutGlobalScope(StatusAcademicSchoolScope::class);
    }
    public function student()
    {
        return $this->hasOne('App\SmStudent', 'id', 'student_id');
    }
    public function school()
    {
        return $this->belongsTo('App\SmSchool', 'school_id', 'id')->withDefault();
    }
    public function academic()
    {
        return $this->belongsTo('App\SmAcademicYear', 'academic_id', 'id')->withDefault();
    }
    public function classes()
    {
        return $this->hasMany(SmClass::class, 'academic_id', 'academic_id');
    }
    
    public function studentDetail()
    {
        return $this->belongsTo('App\SmStudent', 'student_id', 'id')->withDefault();
    }

    public function fees()
    {
        return $this->hasMany(SmFeesAssign::class, 'record_id', 'id');
    }

    public function feesDiscounts()
    {
        return $this->hasMany(SmFeesAssignDiscount::class, 'record_id', 'id');
    }

    public function homework()
    {
        return $this->hasMany(SmHomework::class, 'record_id', 'id');
    }

    public function studentAttendance()
    {
        return $this->hasMany(SmStudentAttendance::class, 'student_record_id', 'id');
    }


    public function studentAttendanceByMonth($month, $year)
    {
        return $this->studentAttendance()->where('attendance_date', 'like', $year . '-' . $month . '%')->get();
    }
    
    public function getHomeWorkAttribute(){
        return SmHomework::with('classes', 'sections', 'subjects')->where('class_id', $this->class_id)->where('section_id', $this->section_id)
        ->where('sm_homeworks.academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
    }

   public function getUploadContent($type){

       $class = $this->class_id;
       $section = $this->section_id;
       $content = [];
        $content = SmTeacherUploadContent::where('content_type', $type)
        ->where(function($que) use ($class){
            return $que->where('class', $class)
            ->orWhereNull('class');
        })
        ->where(function($que) use ($section){
            return $que->where('section', $section)
            ->orWhereNull('section');
        })
        ->where('academic_id', getAcademicId())
        ->where('school_id', Auth::user()->school_id)
        ->get();

        return $content;
    }

    public function getExamAttribute()
    {
       return SmExam::with('examType')->where('class_id',$this->class_id)->where('section_id',$this->section_id)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->where('active_status', 1)->get();
    }

    public function getAssignSubjectAttribute()
    {
       return SmAssignSubject::where('class_id', $this->class_id)->where('section_id', $this->section_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
    }

    public function getOnlineExamAttribute()
    {
        $subjectIds = SmAssignSubject::where('class_id', $this->class_id)
        ->where('section_id', $this->section_id)->where('school_id', Auth::user()->school_id)
        ->where('academic_id', getAcademicId())
        ->pluck('subject_id')->unique();
        if (moduleStatusCheck('OnlineExam')==true) {
            return InfixOnlineExam::where('active_status', 1)->where('academic_id', getAcademicId())->where('status', 1)->where('class_id', $this->class_id)
            ->where('school_id', Auth::user()->school_id)
            ->get()->filter(function ($exam) {
                $exam->when($exam->section_id, function ($q) {
                    $q->where('section_id', $this->section_id);
                });
                return $exam;
            });
        }
        return SmOnlineExam::where('active_status', 1)->where('academic_id', getAcademicId())->where('status', 1)->where('class_id', $this->class_id)->where('section_id', $this->section_id)->where('school_id', Auth::user()->school_id)->get();
    }
    public function getInfixStudentTakeOnlineExamAttribute()
    {
        if (moduleStatusCheck('OnlineExam')==true && auth()->user()->role_id==2) {
          return  InfixStudentTakeOnlineExam::where('status', 2)
            ->where('student_id', auth()->user()->student->id)
            ->where('student_record_id', $this->id)->get();
        } 
    }
    public static function getInfixStudentTakeOnlineExamParent($student_id, $record_id)
    {
        if (moduleStatusCheck('OnlineExam')==true ) {
          return  InfixStudentTakeOnlineExam::where('status', 2)
            ->where('student_id', $student_id)
            ->where('student_record_id', $record_id)->get();
        }else{
            return  SmStudentTakeOnlineExam::where('active_status', 1)->where('status', 2)
            ->where('academic_id', getAcademicId())
            ->where('student_id', $student_id)
            ->where('record_id', $record_id)
            ->get();
        }
        
    }
    public function getStudentTeacherAttribute()
    {
        return SmAssignSubject::select('teacher_id')->where('class_id', $this->class_id)
        ->where('section_id', $this->section_id)->distinct('teacher_id')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
    }
    public function getStudentVirtualClassAttribute()
    {
        return VirtualClass::where('class_id', $this->class_id)
        ->where(function ($q) {
            return $q->where('section_id', $this->section_id)->orWhereNull('section_id');
        })
        ->where('school_id', Auth::user()->school_id)
        ->get();
    }
    public function getStudentBbbVirtualClassAttribute()
    {
        return BbbVirtualClass::where('class_id', $this->class_id)
        ->where(function ($q) {
            return $q->where('section_id', $this->section_id)->orWhereNull('section_id');
        })
        // ->where('school_id', Auth::user()->school_id)
        ->get();
    }
    public function getStudentBbbVirtualClassRecordAttribute()
    {
        $meetings = BbbVirtualClass::where('class_id', $this->class_id)
        ->where(function ($q) {
            return $q->where('section_id', $this->section_id)->orWhereNull('section_id');
        })
        // ->where('school_id', Auth::user()->school_id)
        ->get();
        $meeting_id = $meetings->pluck('meeting_id')->toArray();
        $recordList = Bigbluebutton::getRecordings(['meetingID' => $meeting_id]);
        return $recordList;
    }
    public function getStudentJitsiVirtualClassAttribute()
    {
        return JitsiVirtualClass::where('class_id', $this->class_id)
        ->where(function ($q) {
            return $q->where('section_id', $this->section_id)->orWhereNull('section_id');
        })
        ->get();
    }
    public function getOnlinePdfExamAttribute()
    {
        return InfixPdfExam::where('active_status', 1)->where('academic_id', getAcademicId())->where('status', 1)->where('class_id', $this->class_id)->where('section_id', $this->section_id)->where('school_id', Auth::user()->school_id)->get();
 
    }
    
    public function getStudentCoursesAttribute()
    {
        return Course::where( function ($q) {
            return $q->where('class_id', $this->class_id)->orWhere('class_id', 0);
        })->where( function ($q) {
            return $q->where('section_id', $this->section_id)->orWhere('section_id', null);

        })->withCount('chapters', 'lessons')->where('active_status', 1)->get();
    }
    public function feesInvoice()
    {
        return $this->hasMany('Modules\Fees\Entities\FmFeesInvoice', 'record_id', 'id');
    }

    public function getOptionalSubjectSetupAttribute()
    {
        return SmClassOptionalSubject::where('class_id', $this->class_id)->first();
    }

    public function optionalSubject()
    {
        return $this->belongsTo('App\SmOptionalSubjectAssign', 'id', 'record_id');
    }
}
