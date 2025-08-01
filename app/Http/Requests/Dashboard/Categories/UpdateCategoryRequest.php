<?php

namespace App\Http\Requests\Dashboard\Categories;

use App\Http\Requests\AbstractApiRequest;

class UpdateCategoryRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => 'sometimes|boolean',
            'translations.en.name' => 'sometimes|required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ar.name' => 'sometimes|required|string|max:255',
            'translations.ar.description' => 'nullable|string',
        ];
    }
}
