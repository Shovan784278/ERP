<?php

namespace Modules\Lesson\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;

class SmLessonTopic extends Model
{
	protected $fillable = [];
	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope(new StatusAcademicSchoolScope);
	}
	public function class(){
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
	}
	public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
	}

	public function subject(){
		return $this->belongsTo('App\SmSubject', 'subject_id', 'id');
	}
	public function topics(){
		return $this->hasMany('Modules\Lesson\Entities\SmLessonTopicDetail', 'topic_id', 'id');
	}

	public function lesson(){
		return $this->belongsTo('Modules\Lesson\Entities\SmLesson', 'lesson_id', 'id');
	}

}
