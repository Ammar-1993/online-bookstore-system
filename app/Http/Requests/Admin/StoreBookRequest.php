<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('create', \App\Models\Book::class); }

    public function rules(): array
    {
        return [
            'title'        => ['required','string','max:255'],
            'slug'         => ['nullable','string','max:255','unique:books,slug'],
            'isbn'         => ['required','string','max:20','unique:books,isbn'],
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
