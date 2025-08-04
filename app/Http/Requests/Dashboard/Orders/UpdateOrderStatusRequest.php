<?php

namespace App\Http\Requests\Dashboard\Orders;

use App\Http\Requests\AbstractApiRequest;

class UpdateOrderStatusRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,paid',
            'paid_at' => 'nullable|date',
        ];
    }
}
