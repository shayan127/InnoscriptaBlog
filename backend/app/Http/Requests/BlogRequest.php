<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'snippet'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'url'          => 'required|url|max:500',
            'published_at' => 'nullable|date',
            'source'       => 'required|string|max:255',
            'origin'       => 'nullable|string|max:255',
            'authors'      => 'required|array|min:1',
            'authors.*'    => 'required|string|max:255',
            'image'        => 'nullable|url|max:500',
            'categories'   => 'nullable|array|min:1',
            'categories.*' => 'exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'authors.required'      => 'At least one author must be specified.',
            'authors.array'         => 'The authors field must be an array.',
            'authors.min'           => 'At least one author is required.',
            'authors.*.required'    => 'Each author name must not be empty.',
            'authors.*.string'      => 'Each author name must be a valid string.',
            'authors.*.max'         => 'Each author name must not exceed 255 characters.',

            'categories.array'      => 'The categories field must be an array.',
            'categories.min'        => 'At least one category must be selected.',
            'categories.*.exists'   => 'One or more selected categories do not exist.',
        ];
    }
}
