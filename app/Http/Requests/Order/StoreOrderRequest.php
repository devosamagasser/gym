<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\AbstractApiRequest;

class StoreOrderRequest extends AbstractApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method' => 'required|in:cash,visa,wallet',
        ];
    }
}
