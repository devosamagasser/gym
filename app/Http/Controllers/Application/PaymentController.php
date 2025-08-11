<?php
namespace App\Http\Controllers\Application;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Factory\PaymentGatewayFactory;

class PaymentController extends Controller
{
    public function __construct(public PaymentGatewayFactory $paymentGatewayFactory)
    {
        
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'order_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'payment_type' => 'required|in:card,wallet',
        ]);

        $gateway = PaymentGatewayFactory::make('paymob');

        $paymentUrl = $gateway->getPaymentUrl([
            'amount' => $validated['amount'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'payment_type' => $validated['payment_type'],
            // باقي بيانات Paymob المطلوبة مثل address ثابت أو dummy
            'apartment' => 'NA',
            'floor' => 'NA',
            'street' => 'NA',
            'building' => 'NA',
            'city' => 'Cairo',
            'country' => 'EG',
            'state' => 'Cairo',
        ]);

        return response()->json([
            'payment_url' => $paymentUrl
        ]);
    }

}