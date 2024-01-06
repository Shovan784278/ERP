<?php

namespace App\Http\Requests\Admin\AdminSection;

use Illuminate\Foundation\Http\FormRequest;

class SmAdmissionQueryRequest extends FormRequest
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
            'name' =>'nullable',
            'phone' =>'nullable',
            'email' =>'nullable',
            'address' => 'nullable',
            'description' => 'nullable',
            'date' => 'required|date',
            'next_follow_up_date' => 'required|date|after:date',
            'assigned'=>'required',
            'reference' =>'required',
            'source' => 'required',
            'class' => 'required',
            'no_of_child' =>'required',
        ];
    }
}
