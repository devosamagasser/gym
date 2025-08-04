<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\AbstractApiRequest;

class UpdateCartRequest extends AbstractApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1',
        ];
    }
}
