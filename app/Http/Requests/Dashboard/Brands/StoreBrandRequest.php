<?php

namespace App\Http\Requests\Dashboard\Brands;

use App\Http\Requests\AbstractApiRequest;

class StoreBrandRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => 'nullable|boolean',
            'translations.en.name' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ar.name' => 'required|string|max:255',
            'translations.ar.description' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
        ];
    }
}
