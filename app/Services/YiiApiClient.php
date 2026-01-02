<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class YiiApiClient
{
    protected Client $http;
    protected string $baseUrl;
    protected ?string $token;
    protected int $timeout;
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->baseUrl = rtrim((string) config('services.yii.base_url', ''), '/') ?: '';
        $this->token   = session('yii_api_token') ?: config('services.yii.token');
        $this->timeout = (int) config('services.yii.timeout', 10);
        $this->logger  = $logger;

        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => $this->timeout,
            'http_errors' => false,
        ]);
    }

    /**
     * Helper umum untuk request ke Yii.
     */
    protected function request(string $method, string $uri, array $options = []): array
    {
        if ($this->baseUrl === '') {
            // Backend Yii belum dikonfigurasi, jangan sampai aplikasi langsung error berat.
            $this->logger->warning('YiiApiClient dipanggil tetapi YII_API_BASE_URL belum diset.');
            return [
                'success' => false,
                'status'  => 503,
                'data'    => null,
                'error'   => 'Backend Yii belum dikonfigurasi.',
            ];
        }

        $headers = $options['headers'] ?? [];
        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        $options['headers'] = $headers;

        try {
            $response = $this->http->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            $this->logger->error('Gagal memanggil Yii API', [
                'method' => $method,
                'uri'    => $uri,
                'error'  => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => 500,
                'data'    => null,
                'error'   => 'Gagal terhubung ke backend Yii.',
            ];
        }

        $status = $response->getStatusCode();
        $body   = (string) $response->getBody();

        $decoded = null;
        if ($body !== '') {
            try {
                $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $decoded = null;
            }
        }

        return [
            'success' => $status >= 200 && $status < 300,
            'status'  => $status,
            'data'    => $decoded,
            'raw'     => $body,
        ];
    }

    public function get(string $uri, array $query = []): array
    {
        $options = [];
        if (!empty($query)) {
            $options['query'] = $query;
        }

        return $this->request('GET', $uri, $options);
    }

    public function postJson(string $uri, array $payload = []): array
    {
        $options = [];
        if (!empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->request('POST', $uri, $options);
    }

    public function putJson(string $uri, array $payload = []): array
    {
        $options = [];
        if (!empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->request('PUT', $uri, $options);
    }

    public function patchJson(string $uri, array $payload = []): array
    {
        $options = [];
        if (!empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->request('PATCH', $uri, $options);
    }

    public function postMultipart(string $uri, array $multipart = []): array
    {
        $options = [];
        if (!empty($multipart)) {
            $options['multipart'] = $multipart;
        }

        return $this->request('POST', $uri, $options);
    }

    public function delete(string $uri): array
    {
        return $this->request('DELETE', $uri);
    }
}
