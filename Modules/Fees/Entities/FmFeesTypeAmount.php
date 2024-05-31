<?php

namespace Modules\Fees\Entities;
use Modules\Fees\Entities\FmFeesType;
use App\SmClass;
use Modules\Fees\Entities\FmFeesGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FmFeesTypeAmount extends Model
{
    use HasFactory;

    protected $fillable = ["student_id","academic_id","month","amount","sm_class_id","fm_fees_type_id"]; 

    

    
   
    public function fessGroup(){
        return $this->belongsTo(FmFeesGroup::class,'fees_group_id','id');
    }

    public function sm_class_name(){

        return $this->belongsTo(SmClass::class, 'sm_class_id');

    }

    public function fm_fees_type(){

        return $this->belongsTo(FmFeesType::class, 'fm_fees_type_id');

    }

    
}
