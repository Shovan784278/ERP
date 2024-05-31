<?php

namespace App\Models;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Fees\Entities\FmFeesType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Scopes\StatusAcademicSchoolScope;
use Modules\OnlineExam\Entities\InfixPdfExam;
use Modules\OnlineExam\Entities\InfixOnlineExam;
use Modules\FeesCollection\Entities\InfixFeesMaster;
use Modules\FeesCollection\Entities\InfixFeesPayment;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\FeesCollection\Entities\InfixAssignDiscount;
use Modules\OnlineExam\Entities\InfixStudentTakeOnlineExam;

class FeesReceiptBook extends Model
{

    protected $table = 'fm_fees_reciept_book';


    protected $fillable  = [

        'record_id', 'date', 'year', 'student_id', 'student_roll', 
        'class_id', 'section_id', 'fm_fees_type_amount_id', 
        'pay_date', 'pay_Year_Month', 'paid_amount', 'user_id'

    ];

    public function student() {

        return $this->belongsTo(SmStudent::class, 'id');
    }

    public function class()
    {
        return $this->belongsTo(SmClass::class, 'class_id', 'class_name');
    }

    public function section()
    {
        return $this->belongsTo(SmSection::class, 'section_id', 'id');
    }

    // public function fm_fees_type_amount() {

    //     return $this->belongsTo(FmFeesType::class, 'fm_fees_type_amount_id', 'id');
    // }

    // public function feesReceiptBookStore()
    // {
    //     return $this->hasOne(FeesReceiptBook::class, 'class_id', 'id');
    // }

    use HasFactory;
}
