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
            'price' => 'required|numeric',
            'sale' => 'nullable|numeric|lt:price',
            'stock' => 'required|integer',
            'is_active' => 'required|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'translations.en.name' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ar.name' => 'required|string|max:255',
            'translations.ar.description' => 'nullable|string',
        ];
    }
}

