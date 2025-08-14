<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'rating'  => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string','max:2000'],
        ];
    }
}
