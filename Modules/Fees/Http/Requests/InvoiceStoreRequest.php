<?php

namespace Modules\Fees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'class' => 'required',
            'student' => 'required',
            'create_date' => 'required',
            'due_date' => 'required',
            'payment_status' => 'required',
            'payment_method' => 'required_if:payment_status,partial|required_if:payment_status,full',
            'bank' => 'required_if:payment_method,Bank',
            // 'fees_type' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
