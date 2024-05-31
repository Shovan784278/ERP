<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmFeesReceiptBook extends Model
{
    protected $table = 'fm_fees_reciept_book';

    
    protected $fillable  = [

        'record_id', 'date', 'year', 'student_id', 'student_roll', 
        'class_id', 'section_id', 'fm_fees_type_amount_id', 
        'pay_date', 'pay_Year_Month', 'paid_amount', 'user_id'

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
    use HasFactory;
}
