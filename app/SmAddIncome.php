<?php

namespace App;

use App\SmAddExpense;
use Illuminate\Support\Facades\Auth;
use App\Scopes\ActiveStatusSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmAddIncome extends Model
{
    use HasFactory;
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new ActiveStatusSchoolScope);

    }    
    
    public function incomeHeads()
    {
        return $this->belongsTo('App\SmIncomeHead', 'income_head_id', 'id');
    }

    public function ACHead()
    {
        return $this->belongsTo('App\SmChartOfAccount', 'income_head_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo('App\SmBankAccount', 'account_id', 'id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\SmPaymentMethhod', 'payment_method_id', 'id');
    }

    public function scopeAddIncome($query, $date_from, $date_to, $payment_method)
    {
        return $query->where('date', '>=', $date_from)
            ->where('date', '<=', $date_to)
            ->where('active_status', 1)
            ->where('school_id', Auth::user()->school_id)
            ->where('payment_method_id', $payment_method);
    }

    public static function monthlyIncome($i)
    {
        try {
            $m_add_incomes = SmAddIncome::where('academic_id', getAcademicId())
                ->where('name', '!=', 'Fund Transfer')
                ->where('school_id', Auth::user()->school_id)
                ->where('active_status', 1)
                ->where('date', 'like', date('Y-m-') . $i)
                ->sum('amount');

            $m_total_income = $m_add_incomes;

            return $m_total_income;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

    public static function monthlyExpense($i)
    {
        try {
            $m_add_expenses = SmAddExpense::where('academic_id', getAcademicId())
                ->where('name', '!=', 'Fund Transfer')
                ->where('school_id', Auth::user()->school_id)
                ->where('active_status', 1)
                ->where('date', 'like', date('Y-m-') . $i)
                ->sum('amount');
            $m_total_expense = $m_add_expenses;
            return $m_total_expense;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

    public static function yearlyIncome($i)
    {
        try {
            $y_add_incomes = SmAddIncome::where('academic_id', getAcademicId())
                ->where('name', '!=', 'Fund Transfer')
                ->where('school_id', Auth::user()->school_id)
                ->where('active_status', 1)
                ->where('date', 'like', date('Y-' . $i) . '%')
                ->sum('amount');

            $y_total_income = $y_add_incomes;

            return $y_total_income;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

    public static function yearlyExpense($i)
    {
        try {
            $m_add_expenses = SmAddExpense::where('academic_id', getAcademicId())
                ->where('name', '!=', 'Fund Transfer')
                ->where('school_id', Auth::user()->school_id)
                ->where('active_status', 1)
                ->where('date', 'like', date('Y-' . $i) . '%')
                ->sum('amount');
            $m_total_expense = $m_add_expenses;
            return $m_total_expense;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }
}
