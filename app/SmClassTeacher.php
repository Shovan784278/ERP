<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmClassTeacher extends Model
{
    use HasFactory;
    public function teacher(){
    	return $this->belongsTo('App\SmStaff', 'teacher_id', 'id');
    }
}
