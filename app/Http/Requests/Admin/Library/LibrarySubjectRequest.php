<?php

namespace App\Http\Requests\Admin\Library;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class LibrarySubjectRequest extends FormRequest
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
        $school_id=auth()->user()->school_id;
        return [
            'subject_name' =>['required','max:30', Rule::unique('library_subjects')->where('school_id', $school_id)->ignore($this->id) ],
            'category' => "required",
            'subject_code' => ['required','max:30', Rule::unique('library_subjects')->where('school_id', $school_id)->ignore($this->id) ],
        ];
    }
}
