<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // عامة
    }

    public function rules(): array
    {
        return [
            'q'           => ['nullable', 'string', 'max:200'],
            'category'    => ['nullable', 'string'], // slug أو id كنص
            'publisher'   => ['nullable', 'string'], // slug أو id
            'author'      => ['nullable', 'string'], // slug أو id
            'authors'     => ['nullable', 'array'],
            'authors.*'   => ['string'],
            'price_min'   => ['nullable', 'numeric', 'gte:0'],
            'price_max'   => ['nullable', 'numeric', 'gte:0'],
            'sort'        => ['nullable', Rule::in(['relevance','newest','price_asc','price_desc','rating_desc'])],
            'per_page'    => ['nullable', 'integer', 'min:6', 'max:60'],
            'partial'     => ['nullable', 'boolean'], // لطلبات AJAX
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'q'         => trim((string) $this->q),
            'per_page'  => $this->per_page ?: 12,
            'sort'      => $this->sort ?: 'relevance',
        ]);
    }

    public function messages(): array
    {
        return [
            'sort.in' => 'قيمة الترتيب غير مدعومة.',
        ];
    }
}
