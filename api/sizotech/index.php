<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/https.php';
sizo_force_canonical_https();

session_start();
require_once dirname(__DIR__, 2) . '/config/SizotechApiClient.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function sizo_api_reply(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function sizo_api_csrf_valid(): bool
{
    $token = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return $token !== '' && hash_equals((string) ($_SESSION['signup_csrf'] ?? ''), $token);
}

$route = trim((string) ($_GET['route'] ?? ''), '/');
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$client = new SizotechApiClient();

try {
    if ($method === 'GET' && $route === 'plans') {
        $result = $client->request('GET', '/plans');
        sizo_api_reply($result['status'], $result['body']);
    }
    if ($method === 'GET' && $route === 'registration-options') {
        $result = $client->request('GET', '/registration-options');
        sizo_api_reply($result['status'], $result['body']);
    }
    if ($method === 'GET' && $route === 'companies') {
        $companies = [];
        $page = 1;
        $totalPages = 1;
        do {
            $result = $client->request('GET', '/companies?page=' . $page . '&per_page=100');
            if ($result['status'] < 200 || $result['status'] >= 300) {
                sizo_api_reply($result['status'], $result['body']);
            }
            foreach (($result['body']['data'] ?? []) as $company) {
                $companies[] = [
                    'name' => (string) ($company['name'] ?? ''),
                    'logo_url' => isset($company['logo_url']) ? (string) $company['logo_url'] : null,
                ];
            }
            $totalPages = max(1, (int) ($result['body']['meta']['total_pages'] ?? 1));
            $page++;
        } while ($page <= $totalPages && $page <= 100);
        sizo_api_reply(200, ['status' => 'ok', 'data' => $companies, 'meta' => ['total' => count($companies)]]);
    }
    if ($method === 'GET' && $route === 'subdomains/suggest') {
        $name = trim((string) ($_GET['name'] ?? ''));
        $result = $client->request('GET', '/subdomains/suggest?name=' . rawurlencode($name));
        sizo_api_reply($result['status'], $result['body']);
    }
    if ($method === 'GET' && $route === 'subdomains/check') {
        $subdomain = trim((string) ($_GET['subdomain'] ?? ''));
        $result = $client->request('GET', '/subdomains/check?subdomain=' . rawurlencode($subdomain));
        sizo_api_reply($result['status'], $result['body']);
    }
    if ($method === 'GET' && preg_match('#^provisionings/(\d+)$#', $route, $matches)) {
        $id = (int) $matches[1];
        if (empty($_SESSION['signup_provisionings'][$id])) {
            sizo_api_reply(404, ['status' => 'not_found', 'message' => 'Processo não encontrado.']);
        }
        $result = $client->request('GET', '/provisionings/' . $id);
        sizo_api_reply($result['status'], $result['body']);
    }
    if ($method === 'POST' && $route === 'registrations') {
        if (!sizo_api_csrf_valid()) {
            sizo_api_reply(403, ['status' => 'invalid_request', 'message' => 'Pedido inválido. Atualize a página e tente novamente.']);
        }
        $key = trim((string) ($_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? ''));
        if (!preg_match('/^signup-[A-Za-z0-9-]{16,120}$/', $key)) {
            sizo_api_reply(422, ['status' => 'validation_error', 'message' => 'Não foi possível validar o pedido.']);
        }
        $input = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($input)) {
            sizo_api_reply(400, ['status' => 'invalid_request', 'message' => 'Dados inválidos.']);
        }
        $fields = ['name', 'company_type', 'company_type_other', 'show_legal_designation', 'email', 'nuit', 'phone', 'phone_alt', 'business_area', 'business_area_other', 'address_country', 'address_province', 'address_street', 'address_neighborhood', 'address_house_number', 'plan_code', 'billing_cycle', 'subdomain'];
        $payload = [];
        foreach ($fields as $field) {
            $payload[$field] = is_bool($input[$field] ?? null) ? $input[$field] : trim((string) ($input[$field] ?? ''));
        }
        $payload['show_legal_designation'] = !empty($input['show_legal_designation']);
        $result = $client->request('POST', '/registrations', $payload, $key);
        $body = $result['body'];
        if ($result['status'] === 202 && !empty($body['provisioning_id'])) {
            $_SESSION['signup_provisionings'][(int) $body['provisioning_id']] = $key;
        }
        sizo_api_reply($result['status'], $body);
    }
    sizo_api_reply(404, ['status' => 'not_found', 'message' => 'Rota não encontrada.']);
} catch (Throwable $e) {
    error_log('[sizo-page] API proxy failure: ' . $e->getMessage());
    sizo_api_reply(503, ['status' => 'unavailable', 'message' => 'Não foi possível comunicar com o Sizotech neste momento.']);
}
