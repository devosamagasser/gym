<?php

namespace App\Http\Requests\Dashboard\Products;

use App\Http\Requests\AbstractApiRequest;

class UpdateProductRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => 'sometimes|required|numeric',
            'sale' => 'nullable|numeric|lt:price',
            'stock' => 'sometimes|required|integer',
            'is_active' => 'sometimes|boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'translations.en.name' => 'sometimes|required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ar.name' => 'sometimes|required|string|max:255',
            'translations.ar.description' => 'nullable|string',
            'cover' => 'sometimes|image|max:2048',
            'gallery' => 'sometimes|array',
            'gallery.*' => 'image|max:2048',
        ];
    }
}

