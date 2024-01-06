<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmSection extends Model
{
   //
   use HasFactory;
   protected static function boot()
   {
       parent::boot();
 
       static::addGlobalScope(new StatusAcademicSchoolScope);
   }

   public function students()
   {
       return $this->hasMany('App\SmStudent', 'section_id', 'id');
   }
}
