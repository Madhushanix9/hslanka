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

            $data = json_decode($response->getBody(), true);

            if (isset($data['code']) && $data['code'] == 200 && isset($data['accessToken'])) {
                Cache::put($cacheKey, $data['accessToken'], now()->addMinutes(50));
                return $data['accessToken'];
            }

            Log::error('HelaPOS getToken error: ' . json_encode($data));
            return null;

        } catch (\Exception $e) {
            // 2. If we hit a 429, enter a 5-minute cooling period to prevent IP bans
            if (strpos($e->getMessage(), '429') !== false) {
                Log::warning('HelaPOS: Rate limit hit on getToken. Entering cooling period.');
                Cache::put('helapos_auth_throttled', true, now()->addMinutes(5));
            }
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
        if (!$token) return null;

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

            $data = json_decode($response->getBody(), true);

            if (isset($data['statusCode']) && $data['statusCode'] == "200") {
                return $data;
            }

            Log::error('HelaPOS generateQR error: ' . json_encode($data));
            return null;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 401 && $retry) {
                // Token expired, clear cache and retry
                $cacheKey = 'helapos_access_token_' . md5($this->appId);
                Cache::forget($cacheKey);
                return $this->generateQR($reference, $amount, false);
            }
            if (strpos($e->getMessage(), '429') !== false) {
                Log::warning('HelaPOS: Rate limit hit on generateQR. Entering cooling period.');
                Cache::put('helapos_auth_throttled', true, now()->addMinutes(5));
            }
            Log::error('HelaPOS generateQR ClientException: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('HelaPOS generateQR exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check Payment Status
     */
    public function getPaymentStatus($reference, $qr_reference = null, $retry = true)
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
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

            $data = json_decode($response->getBody(), true);

            if (isset($data['statusCode']) && $data['statusCode'] == "200") {
                return $data;
            }

            return $data;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 401 && $retry) {
                // Token likely expired or invalid, clear cache and retry once
                $cacheKey = 'helapos_access_token_' . md5($this->appId);
                Cache::forget($cacheKey);
                return $this->getPaymentStatus($reference, $qr_reference, false);
            }
            Log::error('HelaPOS getPaymentStatus ClientException: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('HelaPOS getPaymentStatus exception: ' . $e->getMessage());
            return null;
        }
    }
}
