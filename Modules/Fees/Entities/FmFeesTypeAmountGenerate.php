<?php

namespace Modules\Fees\Entities;
use App\SmClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmFeesTypeAmountGenerate extends Model
{
    protected $fillable = ['date','year', 'sm_class_id', 'month', 'fm_fees_type_amount_id']; 

    public function sm_class_name(){

        return $this->belongsTo(SmClass::class, 'sm_class_id');

    }

    use HasFactory;
}
