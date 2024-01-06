<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;

class SmFeesGroup extends Model
{
    use HasFactory;
	protected static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
    public function feesMasters(){
		return $this->hasmany('App\SmFeesMaster', 'fees_group_id');
	}


}
