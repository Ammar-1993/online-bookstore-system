<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الكل يستطيع إضافة للسلة
    }

    public function rules(): array
    {
        return [
            'qty' => ['required','integer','min:1','max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'qty.required' => 'الكمية مطلوبة',
            'qty.integer'  => 'الكمية يجب أن تكون رقمًا صحيحًا',
            'qty.min'      => 'أقل كمية هي 1',
            'qty.max'      => 'أقصى كمية مسموحة 100',
        ];
    }
}
