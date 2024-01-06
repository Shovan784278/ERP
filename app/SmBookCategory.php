<?php

namespace App;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmBookCategory extends Model
{
    use HasFactory;
    public function scopeStatus($query){
        return $query->where('school_id',auth()->user()->school_id);
    }
   
}
