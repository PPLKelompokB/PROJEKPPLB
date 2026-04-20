<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|max:50'
        ];
    }
}