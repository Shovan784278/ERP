<?php

namespace App;

use App\SmClassRoutineUpdate;
use App\SmStaff;
use App\SmStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope;

class SmWeekend extends Model
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SchoolScope);
    }

    public function classRoutine()
    {
        return $this->hasMany('App\SmClassRoutineUpdate', 'day', 'id');
    }

    public function studentClassRoutine()
    {
        $student = SmStudent::where('user_id', auth()->user()->id)->first();
         
        return $this->hasMany('App\SmClassRoutineUpdate', 'day', 'id')
        ->where('class_id', $student->class_id)
        ->where('section_id', $student->section_id)
        ->where('academic_id', getAcademicId())
        ->where('school_id', auth()->user()->school_id);
    }
    public static function studentClassRoutineFromRecord($class_id, $section_id, $day_id)
    {
         
        $routine = SmClassRoutineUpdate::where('day', $day_id)
                                    ->where('class_id', $class_id)
                                    ->where('section_id', $section_id)
                                    ->where('academic_id', getAcademicId())
                                    ->where('school_id', auth()->user()->school_id)->get();
        return  $routine;
    }

    public function teacherClassRoutine()
    {
        $teacher_id = SmStaff::where('user_id', auth()->user()->id)->where('role_id', 4)->first()->id;
         
        return $this->hasMany('App\SmClassRoutineUpdate', 'day', 'id')
        ->where('teacher_id', $teacher_id)
        ->where('academic_id', getAcademicId())
        ->where('school_id', auth()->user()->school_id);
    }

    public function teacherClassRoutineAdmin()
    {
        return $this->hasMany('App\SmClassRoutineUpdate', 'day', 'id')
        ->where('teacher_id', request()->teacher)
        ->where('academic_id', getAcademicId())
        ->where('school_id', auth()->user()->school_id);
    }

    public static function teacherClassRoutineById($day, $teacher_id)
    {

        return SmClassRoutineUpdate::where('day', $day)->where('teacher_id', $teacher_id)
            ->where('academic_id', getAcademicId())
            ->where('school_id', auth()->user()->school_id)->get();
    }

    public static function parentClassRoutine($day, $student_id)
    {
        $student = SmStudent::find($student_id);

        return SmClassRoutineUpdate::where('day', $day)->where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('academic_id', getAcademicId())
            ->where('school_id', auth()->user()->school_id)->get();
    }
}
