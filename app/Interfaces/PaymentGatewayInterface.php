<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function getPaymentUrl(array $data): string;

    public function handleCallback(Request $request): bool;
}

