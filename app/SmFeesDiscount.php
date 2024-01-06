<?php

namespace App;

use App\SmFeesMaster;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmFeesDiscount extends Model
{
    use HasFactory;
    public static function CheckAppliedDiscount($discount_id, $student_id, $record_id)
    {
        $check = SmFeesAssign::where('fees_discount_id', $discount_id)->where('record_id',$record_id)->where('student_id', $student_id)->first();
        if ($check) {
            # code...
            $assigned_fees_amount = $check->fees_amount + $check->applied_discount;
            $main_fees_amount = SmFeesMaster::find($check->fees_master_id);
            if (floatval($main_fees_amount->amount) < floatval($assigned_fees_amount)) {
                return 'true';
            } else if ($main_fees_amount->amount > $assigned_fees_amount) {
                return 'false';
            } else {
                return 'true';
            }
        }

    }
    public static function CheckAppliedYearlyDiscount($discount_id, $student_id)
    {
        $check = SmFeesAssignDiscount::where('fees_discount_id', $discount_id)->where('student_id', $student_id)->first();
        if ($check) {
            return 'false';
        } else {
            return 'true';
        }
    }
}
