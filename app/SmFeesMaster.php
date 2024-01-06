<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;

class SmFeesMaster extends Model
{
    use HasFactory;
	protected static function boot()
    {
        Parent::boot();
  
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
    
    
    public function feesTypes()
    {
        return $this->belongsTo('App\SmFeesType', 'fees_type_id');
    }

    public function feesGroups()
    {
        return $this->belongsTo('App\SmFeesGroup', 'fees_group_id', 'id');
    }

    public function feesTypeIds()
    {
        return $this->hasMany('App\SmFeesMaster', 'fees_group_id', 'fees_group_id');
    }
}
