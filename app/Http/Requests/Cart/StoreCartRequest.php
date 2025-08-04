<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\AbstractApiRequest;

class StoreCartRequest extends AbstractApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
