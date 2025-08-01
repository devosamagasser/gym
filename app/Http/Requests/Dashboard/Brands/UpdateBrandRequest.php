<?php

namespace App\Http\Requests\Dashboard\Brands;

use App\Http\Requests\AbstractApiRequest;

class UpdateBrandRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => 'sometimes|boolean',
            'name' => 'sometimes|required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ar.description' => 'nullable|string',
        ];
    }
}
