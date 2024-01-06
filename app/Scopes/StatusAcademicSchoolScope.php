<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StatusAcademicSchoolScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();
    
        if (Auth::check()) {
            $academic = getAcademicId();
            if (app()->bound('school')) {
                if (moduleStatusCheck('Saas') == true && Auth::user()->is_administrator == "yes" && Session::get('isSchoolAdmin') == false && Auth::user()->role_id == 1) {
                    $builder->where($table . '.active_status', 1)->where($table . '.academic_id', getAcademicId());
                } else {
                    $builder->where($table . '.active_status', 1)->where($table . '.academic_id', getAcademicId())->where($table . '.school_id', Auth::user()->school_id);
                }
            } else {
                if (moduleStatusCheck('Saas') == true && Auth::user()->is_administrator == "yes" && Session::get('isSchoolAdmin') == false && Auth::user()->role_id == 1) {
                    $builder->where($table . '.active_status', 1)->where($table . '.academic_id', getAcademicId());
                } else {
                    $builder->where($table . '.active_status', 1)->where($table . '.academic_id', getAcademicId())->where($table . '.school_id', Auth::user()->school_id);
                }
            }
        } else {
            $builder->where($table . '.active_status', 1);
        }
    }
}
