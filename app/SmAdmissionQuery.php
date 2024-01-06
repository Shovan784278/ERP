<?php

namespace App;

use App\Scopes\AcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmAdmissionQuery extends Model
{

  protected static function boot()
  {
      parent::boot();

      static::addGlobalScope(new AcademicSchoolScope);
  }
  use HasFactory;
    public function class(){
		return $this->belongsTo('App\SmClass', 'class', 'id');
	}

	public function user(){
    	return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function referenceSetup(){
    	return $this->belongsTo('App\SmSetupAdmin', 'reference', 'id');
    }
    public function sourceSetup(){
    	return $this->belongsTo('App\SmSetupAdmin', 'source', 'id');
    }
}
