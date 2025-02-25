<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'       => 'nullable|string|max:255',
            'category'     => 'nullable|int',
            'source'       => 'nullable|string|max:255',
            'date'         => 'nullable|date',
        ];
    }
}
