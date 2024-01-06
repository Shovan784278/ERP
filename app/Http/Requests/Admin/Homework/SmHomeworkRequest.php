<?php

namespace App\Http\Requests\Admin\Homework;

use Illuminate\Foundation\Http\FormRequest;

class SmHomeworkRequest extends FormRequest
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
        $maxFileSize=generalSetting()->file_size*1024;
        return [
            'class_id' => "required",
            'section_id' => "required|array",
            'subject_id' => "required",
            'homework_date' => "required|date",
            'submission_date' => "required|date",
            'marks' => "required|numeric|min:0",
            'description' => "required",
            'homework_file' => "sometimes|nullable|mimes:pdf,doc,docx,txt,jpg,jpeg,png,mp4,ogx,oga,ogv,ogg,webm,mp3|max:".$maxFileSize,
        ];
    }
}
