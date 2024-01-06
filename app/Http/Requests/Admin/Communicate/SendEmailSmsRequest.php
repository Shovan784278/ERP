<?php

namespace App\Http\Requests\Admin\Communicate;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailSmsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'email_sms_title' => "required",
            'send_through' => "required",
            'description' => "required",
            'role'=>"required_if:selectTab,G|array",
            'role_id'=>"required_if:selectTab,I",
            'message_to_individual'=>"required_with:role_id|array",
            'class_id'=>"required_without:selectTab",
            'message_to_section'=>"required_with:class_id|array",
            'selectTab'=>'sometimes|nullable'
        ];
    }
}
