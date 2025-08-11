<?php
namespace App\Factory;

use InvalidArgumentException;
use App\Interfaces\PaymentGatewayInterface;
use App\Services\PaymobService;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'paymob' => new PaymobService(),
            default => throw new InvalidArgumentException("Unsupported gateway [$gateway]"),
        };
    }
}