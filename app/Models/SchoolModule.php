<?php

namespace App\Models;

use App\SmSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolModule extends Model
{
    use HasFactory;
    public function school()
    {
        return $this->belongsTo(SmSchool::class, 'school_id');
    }
}
