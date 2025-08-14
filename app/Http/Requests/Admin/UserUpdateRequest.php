<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // محمي عبر Middleware role:Admin على مستوى الراوتر
    }

    public function rules(): array
    {
        $id = $this->route('user')->id ?? null;

        return [
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','email','max:255', Rule::unique('users','email')->ignore($id)],
            'password'              => ['nullable','confirmed','min:8'],
            'roles'                 => ['required','array','min:1'],
            'roles.*'               => ['string','in:Admin,Seller'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'الاسم',
            'email' => 'البريد',
            'password' => 'كلمة المرور',
            'roles' => 'الأدوار',
        ];
    }
}
