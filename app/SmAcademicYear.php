<?php

namespace App;

use App\SmGeneralSettings;
use App\Scopes\ActiveStatusSchoolScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmAcademicYear extends Model
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();
  
        return static::addGlobalScope(new ActiveStatusSchoolScope);
    }
    public function scopeActive($query)
    {

        return $query->where('active_status', 1);
    }
    public static function API_ACADEMIC_YEAR($school_id)
    {
        try {
            return SmGeneralSettings::where('school_id', $school_id)->first('session_id')->session_id;
        } catch (\Exception $e) {
            return 1;
        }

    }
    public static function SINGLE_SCHOOL_API_ACADEMIC_YEAR()
    {
        try {
            return SmGeneralSettings::where('school_id', 1)->first('session_id')->session_id;
        } catch (\Exception $e) {
            return 1;
        }

    }
}
