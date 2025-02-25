<?php

namespace App\Http\Requests;

use App\Models\Blog;
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
            'origin'       => 'nullable|in:'.implode(',', config('news.active_origins', ['none'])),
            'from'         => 'nullable|date',
            'to'           => 'nullable|date',
            'page'         => 'nullable|int',
            'per_page'     => 'nullable|int',
        ];
    }
}
