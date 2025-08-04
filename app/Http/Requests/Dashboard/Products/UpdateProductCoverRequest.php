<?php

namespace App\Http\Requests\Dashboard\Products;

use App\Http\Requests\AbstractApiRequest;

class UpdateProductCoverRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cover' => 'required|image|max:2048', // Ensure the cover is required, an image, and not larger than 2MB
        ];
    }
}
