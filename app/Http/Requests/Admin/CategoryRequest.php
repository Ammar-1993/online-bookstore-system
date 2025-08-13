<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // الوصول محمي عبر middleware role:Admin
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;

        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'alpha_dash:ascii', Rule::unique('categories','slug')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:2048'], // اختياري
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم التصنيف مطلوب.',
            'slug.alpha_dash' => 'الـ slug يجب أن يحتوي أحرف/أرقام وشرطات فقط.',
            'image.image' => 'الملف يجب أن يكون صورة صالحة.',
        ];
    }
}
