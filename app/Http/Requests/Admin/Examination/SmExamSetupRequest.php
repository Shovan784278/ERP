<?php

namespace App\Http\Requests\Admin\Examination;

use Illuminate\Foundation\Http\FormRequest;

class SmExamSetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'class_ids' => 'required',
            'subjects_ids' => 'required|array',
            'exams_types' => 'required|array',
            'exam_marks' => 'required|numeric|min:0',
        ];
    }
    public function attributes()
    {
        return [
                'class_ids'=>"class",
                'subjects_ids' =>"subject",
        ];
    }
}
