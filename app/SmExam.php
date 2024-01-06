<?php

namespace App;

use App\SmExamType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;

class SmExam extends Model
{ 
    use HasFactory;
    protected $guarded = ['id'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }

    public function class(){
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
    }

    public function getClassName()
    {
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
    }

    public function GetSectionName()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
    }
    public function GetSubjectName()
    {
        return $this->belongsTo('App\SmSubject', 'subject_id', 'id');
    }
    public function GetExamTitle()
    {
        return $this->belongsTo('App\SmExamType', 'exam_type_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo('App\SmSubject', 'subject_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
    }

    public function GetExamSetup()
    {
        return $this->hasMany('App\SmExamSetup', 'exam_id', 'id');
    }
    public function examType()
    {
        return $this->hasOne(SmExamType::class, 'id', 'exam_type_id');
    }

    public function markRegistered()
    {
        return $this->hasOne(SmMarkStore::class, 'exam_term_id', 'exam_type_id')
        ->where('class_id', $this->class_id)->where('section_id', $this->section_id);
    }
    public function marks()
    {
        return $this->hasMany('App\SmExamSetup', 'exam_id', 'id');
    }

    public function markDistributions()
    {
        return $this->marks();
    }


    public static function getMarkDistributions($ex_id, $class_id, $section_id, $subject_id)
    {
        try {
            $data = SmExamSetup::where([
                ['exam_term_id', $ex_id],
                ['class_id', $class_id],
                ['section_id', $section_id],
                ['subject_id', $subject_id],
            ])->get();

            return $data;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

    public static function getMarkREgistered($ex_id, $class_id, $section_id, $subject_id)
    {
        try {
            $data = SmMarkStore::where([
                ['exam_term_id', $ex_id],
                ['class_id', $class_id],
                ['section_id', $section_id],
                ['subject_id', $subject_id],
            ])->first();

            return $data;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

    public function markStore()
    {
        return $this->hasOne(SmMarkStore::class, 'exam_term_id', 'exam_type_id')
            ->where('class_id', $this->class_id)->where('section_id', $this->section_id)->where('subject_id', $this->subject_id)
            ->where('school_id', Auth::user()->school_id);
    }

}
