<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

final class SizotechApiClient
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $base = (string) sizo_env('SIZOTECH_API_BASE_URL', sizo_env('SIZO_SYSTEM_URL', ''));
        $base = rtrim($base, '/');
        $this->baseUrl = substr($base, -7) === '/api/v1' ? $base : $base . '/api/v1';
        $this->apiKey = (string) sizo_env('SIZOTECH_API_KEY', sizo_env('SIZO_SYSTEM_API_TOKEN', sizo_env('SIZO_SYSTEM_API_KEY', '')));
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '/api/v1' && $this->apiKey !== '';
    }

    /** @param array<string,string>|null $extraHeaders
     *  @return array{status:int,body:array<string,mixed>} */
    public function request(string $method, string $path, ?array $payload = null, ?string $idempotencyKey = null, ?array $extraHeaders = null): array
    {
        if (!$this->isConfigured() || !function_exists('curl_init')) {
            throw new RuntimeException('Não foi possível comunicar com o Sizotech.');
        }

        $headers = ['Accept: application/json', 'Authorization: Bearer ' . $this->apiKey];
        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
        }
        if ($idempotencyKey !== null) {
            $headers[] = 'Idempotency-Key: ' . $idempotencyKey;
        }
        if (is_array($extraHeaders)) {
            foreach ($extraHeaders as $name => $value) {
                $name = trim((string) $name);
                $value = trim((string) $value);
                if ($name === '' || $value === '') {
                    continue;
                }
                $headers[] = $name . ': ' . $value;
            }
        }

        $curl = curl_init($this->baseUrl . '/' . ltrim($path, '/'));
        if ($curl === false) {
            throw new RuntimeException('Não foi possível comunicar com o Sizotech.');
        }
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($payload !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($body === false || $error !== '') {
            throw new RuntimeException('Não foi possível comunicar com o Sizotech.');
        }
        $json = json_decode((string) $body, true);
        if (!is_array($json)) {
            throw new RuntimeException('O Sizotech devolveu uma resposta inválida.');
        }
        return ['status' => $status, 'body' => $json];
    }
}
