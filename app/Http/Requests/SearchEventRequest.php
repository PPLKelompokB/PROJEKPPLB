<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchEventRequest extends FormRequest
{
    /**
     * Tentukan apakah user memiliki izin untuk melakukan request ini.
     * Karena ini pencarian publik, kita set ke true.
     */
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // Keyword bersifat opsional, berupa teks, dan maksimal 100 karakter
            'keyword' => 'nullable|string|max:100',
            // Per_page (jumlah data per halaman) bersifat opsional, maksimal 50 data
            'per_page' => 'nullable|integer|max:50'
        ];
    }
}