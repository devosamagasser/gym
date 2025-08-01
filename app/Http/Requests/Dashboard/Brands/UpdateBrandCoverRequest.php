<?php

namespace App\Http\Requests\Dashboard\Brands;

use App\Http\Requests\AbstractApiRequest;

class UpdateBrandCoverRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cover' => 'required|image|max:2048',
        ];
    }
}
