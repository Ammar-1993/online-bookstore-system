<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PublisherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Admin') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('publisher')?->id;

        return [
            'name'   => ['required','string','max:255'],
            'slug'   => ['nullable','string','max:255','unique:publishers,slug,'.($id ?? 'NULL').',id'],
            'website'=> ['nullable','url','max:255'],
            'logo'   => ['nullable','image','max:2048'], // اختياري
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'slug.unique'   => 'قيمة الـ slug مستخدمة مسبقاً.',
            'website.url'   => 'رابط الموقع غير صالح.',
            'logo.image'    => 'الملف يجب أن يكون صورة.',
        ];
    }
}
