<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Interfaces\PaymentGatewayInterface;

class PaymobService implements PaymentGatewayInterface
{
    protected $apiKey;
    protected $iframeId;
    protected $integrationId;
    protected $hmac;
    protected $base_url;

    public function __construct()
    {
        $this->apiKey = config('services.payment.paymob.api_key');
        $this->iframeId = config('services.payment.paymob.iframe_id');
        $this->hmac = config('services.payment.paymob.hmac');
        $this->base_url = "https://accept.paymob.com/api/";
    }

    public function getPaymentUrl(array $data): string
    {

        $token = $this->authenticate();

        $orderId = $this->createOrder($data['amount'], $token);

        $integrationId = $this->integrationId;
        if (isset($data['payment_type']) && array_key_exists($data['payment_type'], config('services.payment.paymob.integration_ids'))) {
            $integrationId = config('services.payment.paymob.integration_ids')[$data['payment_type']];
        }

        $paymentToken = $this->generatePaymentKey($token, $orderId, $data['amount'], $data, $integrationId);

        return $this->base_url."acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";
    }

    public function handleCallback(Request $request): bool
    {
        // هنا نستخدم HMAC للتحقق من الـ Callback
        $hmac = $request->query('hmac');

        $calculatedHmac = $this->calculateHmac($request->all());

        return $hmac === $calculatedHmac;
    }

    private function calculateHmac(array $data)
    {
        $concatenated = $data['amount_cents'] . $data['created_at'] . $data['currency'] . $data['id'] . $data['order'] . $data['success'];
        return hash_hmac('sha512', $concatenated, $this->hmac);
    }


    private function authenticate()
    {
        $response = Http::post($this->base_url.'auth/tokens', [
            'api_key' => $this->apiKey,
        ]);

        return $response['token'];
    }

    private function createOrder($amountCents, $authToken)
    {
        $response = Http::post($this->base_url.'ecommerce/orders', [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => $amountCents * 100,
            'currency' => 'EGP',
            'items' => [],
        ]);

        return $response['id'];
    }

    private function generatePaymentKey($authToken, $orderId, $amountCents, $billingData, $integrationId)
    {
        $response = Http::post($this->base_url.'acceptance/payment_keys', [
            'auth_token' => $authToken,
            'amount_cents' => $amountCents * 100,
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => $billingData,
            'currency' => 'EGP',
            'integration_id' => $integrationId,
        ]);

        return $response['token'];
    }
}
