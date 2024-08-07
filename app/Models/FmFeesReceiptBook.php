<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Fees\Entities\FmFeesTypeAmount;

class FmFeesReceiptBook extends Model
{
    protected $table = 'fm_fees_reciept_book';

    
    protected $fillable  = [

        'record_id', 'date', 'year', 'student_id', 'student_roll', 
        'class_id', 'section_id', 'fm_fees_type_amount_id', 
        'pay_date', 'pay_Year_Month', 'paid_amount', 'user_id', 'fees_delete'

    ];

    // Define the relationship with SmClass
    public function class()
    {
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
    }

    // Define the relationship with SmSection
    public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
    }

    // Define the relationship with SmStudent
    public function student()
    {
        return $this->belongsTo('App\SmStudent', 'student_id', 'id');
    }

    public function feesType()
    {
        return $this->hasOne(FmFeesTypeAmount::class, 'id', 'fm_fees_type_amount_id');
    }

    public function feesTypeAmount()
    {
        return $this->belongsTo(FmFeesTypeAmount::class, 'fm_fees_type_amount_id');
    }

    use HasFactory;
}
