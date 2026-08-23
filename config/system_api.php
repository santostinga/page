<?php
require_once __DIR__ . '/env.php';

function sizo_system_api(string $method, string $path, ?array $payload = null, ?string $idempotencyKey = null): array
{
    $base = rtrim((string) sizo_env('SIZO_SYSTEM_URL', ''), '/');
    $token = (string) sizo_env('SIZO_SYSTEM_API_TOKEN', '');
    if ($base === '' || $token === '') {
        error_log('[sizo-page] Sizo system API is not configured.');
        return ['ok' => false, 'status' => 503, 'data' => []];
    }
    $body = $payload === null ? '' : (json_encode($payload, JSON_UNESCAPED_UNICODE) ?: '{}');
    $headers = ["Authorization: Bearer {$token}", 'Accept: application/json'];
    if ($payload !== null) { $headers[] = 'Content-Type: application/json'; }
    if ($idempotencyKey !== null) { $headers[] = "Idempotency-Key: {$idempotencyKey}"; }
    $options = ['http' => ['method' => strtoupper($method), 'header' => implode("\r\n", $headers), 'content' => $body, 'ignore_errors' => true, 'timeout' => 15]];
    $raw = @file_get_contents($base . $path, false, stream_context_create($options));
    $status = 503;
    foreach ($http_response_header ?? [] as $header) { if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $m)) { $status = (int) $m[1]; } }
    $data = is_string($raw) ? json_decode($raw, true) : null;
    return ['ok' => $status >= 200 && $status < 300 && is_array($data), 'status' => $status, 'data' => is_array($data) ? $data : []];
}
