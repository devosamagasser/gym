<?php

namespace App\Http\Requests\Dashboard\Products;

use App\Http\Requests\AbstractApiRequest;

class UpdateProductGalleryRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gallery' => 'required|array',
            'gallery.*' => 'image|max:2048',
        ];
    }
}
