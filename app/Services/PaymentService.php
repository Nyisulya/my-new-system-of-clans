<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $passkey;
    protected $shortcode;
    protected $callbackUrl;

    public function __construct()
    {
        $env = config('services.mpesa.env');
        $this->baseUrl = $env == 'sandbox' 
            ? 'https://sandbox.safaricom.co.ke' 
            : 'https://api.safaricom.co.ke';
            
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->passkey = config('services.mpesa.passkey');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->callbackUrl = config('services.mpesa.callback_url');
    }

    /**
     * Get Access Token from Daraja API
     */
    public function getAccessToken()
    {
        $url = $this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials';
        
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($url);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('M-Pesa Token Error: ' . $response->body());
        return null;
    }

    /**
     * Initiate STK Push
     */
    public function initiatePayment(string $phone, float $amount): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to authenticate with M-Pesa'
            ];
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Format phone number (must be 255...)
        $phone = $this->formatPhoneNumber($phone);

        $url = $this->baseUrl . '/mpesa/stkpush/v1/processrequest';

        $response = Http::withToken($token)->post($url, [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => 'FamilyContribution',
            'TransactionDesc' => 'Contribution Payment'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'transaction_id' => $data['CheckoutRequestID'] ?? 'PENDING',
                'message' => 'STK Push sent. Please check your phone.',
                'data' => $data
            ];
        }

        Log::error('M-Pesa STK Error: ' . $response->body());
        
        return [
            'success' => false,
            'message' => 'Failed to initiate payment: ' . ($response->json()['errorMessage'] ?? 'Unknown error')
        ];
    }

    protected function formatPhoneNumber($phone)
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 07... to 2557...
        if (str_starts_with($phone, '0')) {
            return '255' . substr($phone, 1);
        }
        
        // Convert 255... to 255... (ensure it starts with 255)
        if (!str_starts_with($phone, '255')) {
             return '255' . $phone;
        }

        return $phone;
    }
}
