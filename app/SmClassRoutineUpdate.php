<?php

namespace App;

use App\YearCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;

class SmClassRoutineUpdate extends Model
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
    
    public static function assingedClassRoutine($class_time, $day, $class_id, $section_id)
    {
        try {

            return SmClassRoutineUpdate::where('class_period_id', $class_time)
            ->where('day', $day)
            ->where('class_id', $class_id)->where('section_id', $section_id)
            ->first();
        } catch (\Exception $e) {
            $data=[];
            return $data;
        }
    }

    public static function teacherAssingedClassRoutine($class_time, $day, $teacher_id)
    {
        try {
            return SmClassRoutineUpdate::where('class_period_id', $class_time)->where('day', $day)->where('class_period_id', $class_time)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->where('teacher_id', $teacher_id)->first();
        } catch (\Exception $e) {
            $data=[];
            return $data;
        }
    }

    public function subject()
    {
        return $this->belongsTo('App\SmSubject', 'subject_id', 'id')->withDefault();
    }
    public function class(){
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
    }

    public function classRoom()
    {
        return $this->belongsTo('App\SmClassRoom', 'room_id', 'id')->withDefault();
    }

    public function teacherDetail()
    {
        return $this->belongsTo('App\SmStaff', 'teacher_id', 'id')->withDefault();
    }

    public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
    }
    public function classTime()
    {
        return $this->belongsTo(SmClassTime::class, 'class_period_id');
    }
    public function weekend()
    {
        return $this->belongsTo(SmWeekend::class, 'day');
    }

}
