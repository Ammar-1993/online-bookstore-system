<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'  => ['required','string','max:255'],
            'email' => [
                'required','email','max:255',
                Rule::unique('users','email')->ignore($userId),
            ],
            // تعديل كلمة المرور اختياري
            'password' => ['nullable','string','min:8','confirmed'],

            // أدوار Spatie (أسماء الأدوار)
            'roles' => ['nullable','array'],
            'roles.*' => ['string'],

            // تعليمات إضافية
            'mark_verified' => ['sometimes','boolean'],   // وضع علامة تحقق للبريد
        ];
    }
}
