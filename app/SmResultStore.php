<?php

namespace App;

use App\SmMarkStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SmResultStore extends Model
{
    use HasFactory;
    public function studentInfo(){
    	return $this->belongsTo('App\SmStudent', 'student_id', 'id');
    }
    public function exam(){
        return $this->belongsTo(SmExamType::class, 'exam_type_id');
    }

    public function subject(){
        return $this->belongsTo('App\SmSubject', 'subject_id', 'id');
    }
    public function class(){
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
    }
     public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
    }
    
    public static function remarks($gpa){
    try{
        $mark = SmMarksGrade::where([
            ['from', '<=', $gpa], 
            ['up', '>=', $gpa]]
            )
            ->where('school_id',Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->first();
            return $mark;
    } catch (\Exception $e) {
        $mark=[];
        return $mark;
    }


    }
    public static function  GetResultBySubjectId($class_id, $section_id, $subject_id,$exam_id,$student_id){
    	
        try {
            $data = SmMarkStore::where([
                ['class_id',$class_id],
                ['section_id',$section_id],
                ['exam_term_id',$exam_id],
                ['student_id',$student_id],
                ['subject_id',$subject_id]
            ])->get();
            return $data;
        } catch (\Exception $e) {
            $data=[];
            return $data;
        }
    }

    public static function  GetFinalResultBySubjectId($class_id, $section_id, $subject_id,$exam_id,$student_id){
        
        try {
            $data = SmResultStore::where([
                ['class_id',$class_id],
                ['section_id',$section_id],
                ['exam_type_id',$exam_id],
                ['student_id',$student_id],
                ['subject_id',$subject_id]
                ])->first();

                return $data;
        } catch (\Exception $e) {
            $data=[];
            return $data;
        }
    }

    public static function termBaseMark($class_id, $section_id, $subject_id,$exam_id,$student_id){
        $data = SmResultStore::where([
            ['class_id',$class_id],
            ['section_id',$section_id],
            ['exam_type_id',$exam_id],
            ['student_id',$student_id],
            ['subject_id',$subject_id]
            ])
            ->groupBy('exam_type_id')
            ->sum('total_gpa_point');

            return $data;

    }

}
