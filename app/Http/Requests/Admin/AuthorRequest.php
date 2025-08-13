<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $authorId = $this->route('author')?->id;

        return [
            'name'    => ['required','string','max:255'],
            'slug'    => [
                'nullable','string','max:255','alpha_dash',
                Rule::unique('authors','slug')->ignore($authorId),
            ],
            'website' => ['nullable','url','max:255'],
            'bio'     => ['nullable','string'],
            'avatar'  => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
