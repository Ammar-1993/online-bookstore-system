<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('update', $this->route('book')); }

    public function rules(): array
    {
        $book = $this->route('book');

        return [
            'title'        => ['required','string','max:255'],
            'slug'         => ['nullable','string','max:255', Rule::unique('books','slug')->ignore($book->id)],
            'isbn'         => ['required','string','max:20', Rule::unique('books','isbn')->ignore($book->id)],
            'description'  => ['nullable','string'],
            'price'        => ['required','numeric','min:0'],
            'currency'     => ['required','string','size:3'],
            'stock_qty'    => ['required','integer','min:0'],
            'status'       => ['required','in:draft,published'],
            'published_at' => ['nullable','date'],
            'category_id'  => ['nullable','exists:categories,id'],
            'publisher_id' => ['nullable','exists:publishers,id'],
            'authors'      => ['array'],
            'authors.*'    => ['exists:authors,id'],
            'cover'        => ['nullable','image','max:4096'],
        ];
    }
}
