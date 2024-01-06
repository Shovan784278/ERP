<?php

namespace App;


use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StatusAcademicSchoolScope;

class SmStudentCertificate extends Model
{   use HasFactory;
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new StatusAcademicSchoolScope);
    }


    public static function certificateBody($body, $s_id){
        try {

            $record = StudentRecord::with(['student.parents','student.category','student.religion', 'class', 'section'])->find($s_id);
            $certificate_body = str_replace("]","] ",$body);



            $values = explode(' ', $certificate_body);
            $body = '';

            for($i = 0; $i < count($values); $i++){
                if($values[$i]  == '[name]'){
                    $body .= ' '.$record->student->full_name;
                }elseif($values[$i]  == '[present_address]'){
                    $body .= ' '.$record->student->current_address;

                }
                elseif($values[$i]  == '[dob]'){
                    $body .= ' '.$record->student->date_of_birth;

                }
                elseif($values[$i]  == '[guardian]'){
                    $body .= ' '.$record->student->parents->guardians_name;

                }elseif($values[$i]  == '[created_at]'){
                    $body .= ' '.$record->student->created_at;

                }elseif($values[$i]  == '[admission_no]'){
                    $body .= ' '.$record->student->admission_no;
                }elseif($values[$i]  == '[roll_no]'){
                    $body .= ' '.$record->student->roll_no;
                }elseif($values[$i]  == '[class]'){

                    $body .= ' '.@$record->student->class->class_name;
                }elseif($values[$i]  == '[section]'){
                    $body .= ' '.@$record->student->section->section_name;
                }elseif($values[$i]  == '[gender]'){
                    $body .= ' '.@$record->student->gender->base_setup_name;
                }elseif($values[$i]  == '[admission_date]'){
                    $body .= ' '.@$record->student->admission_date;

                }elseif($values[$i]  == '[category]'){
                    if(!empty($record->student->student_category_id)){
                        $body .= ' '.@$record->student->category->category_name ?? '';
                    }

                }elseif($values[$i]  == '[cast]'){
                    $body .= ' '.$record->student->caste;


                }elseif($values[$i]  == '[father_name]'){
                    $body .= ' '.$record->student->parents->fathers_name;


                }elseif($values[$i]  == '[mother_name]'){

                    $body .= ' '.$record->student->parents->mothers_name;
                }elseif($values[$i]  == '[religion]'){
                    if(!empty($student->religion_id)){
                        $body .= ' '.$record->student->religion->base_setup_name;
                    }


                }elseif($values[$i]  == '[email]'){
                    $body .= ' '.$record->student->email;
                }elseif($values[$i]  == '[phone]'){
                    $body .= ' '.$record->student->mobile;
                }elseif($values[$i]  == ','){
                    $body .= $values[$i];
                }elseif($values[$i]  == '.'){
                    $body .= $values[$i];
                }else{
                    $body .= ' '.$values[$i];
                }
            }


            return $body;
        } catch (\Exception $e) {
            $data= '';
            return $data;
        }

    }


}
