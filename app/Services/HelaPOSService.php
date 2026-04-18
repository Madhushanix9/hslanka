<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HelaPOSService
{
    protected $client;
    protected $baseUrl;
    protected $appId;
    protected $appSecret;
    protected $businessId;

    public function __construct($appId = null, $appSecret = null, $businessId = null)
    {
        $this->baseUrl = config('helapos.base_url');
        $this->appId = $appId ?: config('helapos.app_id');
        $this->appSecret = $appSecret ?: config('helapos.app_secret');
        $this->businessId = $businessId ?: config('helapos.business_id');

        $this->client = new Client([
            'timeout'  => 30.0,
            'http_errors' => false, // We will handle HTTP errors manually to read response bodies
        ]);
    }

    /**
     * Get Access Token from HelaPOS
     */
    public function getAccessToken()
    {
        $cacheKey = 'helapos_access_token_' . md5($this->appId);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        if (!$this->appId || !$this->appSecret) {
            Log::error('HelaPOS credentials missing');
            return null;
        }

        try {
            $authCode = base64_encode($this->appId . ':' . $this->appSecret);
            $url = rtrim($this->baseUrl, '/') . '/merchant/api/v1/getToken';

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Basic ' . $authCode,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody(), true);

            if ($statusCode == 200 && isset($data['code']) && $data['code'] == 200 && isset($data['accessToken'])) {
                Cache::put($cacheKey, $data['accessToken'], now()->addMinutes(50));
                return $data['accessToken'];
            }

            Log::error('HelaPOS getToken error. Status: ' . $statusCode . ' Body: ' . json_encode($data));
            return null;

        } catch (\Exception $e) {
            Log::error('HelaPOS getToken exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate Dynamic QR Code
     */
    public function generateQR($reference, $amount, $retry = true)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['error' => 'Authentication Failed'];

        try {
            $url = rtrim($this->baseUrl, '/') . '/merchant/api/helapos/qr/generate';
            
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'b' => $this->businessId,
                    'r' => (string) $reference,
                    'am' => (double) $amount,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody(), true);

            if ($statusCode == 401 && $retry) {
                // Token expired, clear cache and retry
                $cacheKey = 'helapos_access_token_' . md5($this->appId);
                Cache::forget($cacheKey);
                return $this->generateQR($reference, $amount, false);
            }

            if ($statusCode == 429) {
                Log::warning('HelaPOS: Rate limit hit on generateQR.');
                return ['statusCode' => '429', 'error' => 'Rate limit exceeded'];
            }

            if (is_array($data) && isset($data['statusCode']) && $data['statusCode'] == "200") {
                return $data;
            }

            Log::error('HelaPOS generateQR error: ' . json_encode($data));
            return ['error' => 'Failed to generate QR'];

        } catch (\Exception $e) {
            Log::error('HelaPOS generateQR exception: ' . $e->getMessage());
            return ['error' => 'API Connection Error'];
        }
    }

    /**
     * Check Payment Status
     */
    public function getPaymentStatus($reference, $qr_reference = null, $retry = true)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['error' => 'Authentication Failed'];

        try {
            // According to API docs, only one of these might be needed. 
            // We'll pass both like they suggest in the example.
            $jsonBody = ['reference' => (string) $reference];
            if ($qr_reference) {
                $jsonBody['qr_reference'] = (string) $qr_reference;
            }

            $url = rtrim($this->baseUrl, '/') . '/merchant/api/helapos/sales/getSaleStatus';

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $jsonBody,
            ]);

            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody(), true);

            if ($statusCode == 401 && $retry) {
                // Token likely expired, clear cache and retry
                $cacheKey = 'helapos_access_token_' . md5($this->appId);
                Cache::forget($cacheKey);
                return $this->getPaymentStatus($reference, $qr_reference, false);
            }

            if ($statusCode == 429) {
                return ['statusCode' => '429', 'error' => 'Rate limit exceeded'];
            }

            // Log detailed successful parsing instances for debugging
            if (is_array($data)) {
                 if (isset($data['statusCode']) && $data['statusCode'] == "200") {
                      Log::info('HelaPOS Status Response SUCCESS for ' . $reference . ': ' . json_encode($data));
                 }
                 return $data;
            }

            return ['error' => 'Invalid Response'];

        } catch (\Exception $e) {
            Log::error('HelaPOS getPaymentStatus exception: ' . $e->getMessage());
            return ['error' => 'API Connection Error'];
        }
    }
}
