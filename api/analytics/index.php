<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/https.php';
require_once dirname(__DIR__, 2) . '/config/analytics.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function sizo_analytics_reply(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function sizo_analytics_bearer_token(): string
{
    $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
        return trim($matches[1]);
    }

    return trim((string) ($_GET['token'] ?? ''));
}

$route = trim((string) ($_GET['route'] ?? ''), '/');
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

try {
    if ($method === 'POST' && $route === 'track') {
        $input = json_decode((string) file_get_contents('php://input'), true);
        $events = is_array($input['events'] ?? null) ? $input['events'] : [];
        if (!$events) {
            sizo_analytics_reply(422, ['ok' => false, 'message' => 'Nenhum evento recebido.']);
        }
        if (count($events) > 20) {
            sizo_analytics_reply(422, ['ok' => false, 'message' => 'Demasiados eventos num único pedido.']);
        }
        $result = sizo_analytics_store_events($events);
        sizo_analytics_reply($result['ok'] ? 202 : 503, $result);
    }

    if ($method === 'GET' && ($route === 'stats' || $route === '')) {
        if (!sizo_analytics_token_valid(sizo_analytics_bearer_token())) {
            sizo_analytics_reply(401, ['ok' => false, 'message' => 'Token inválido.']);
        }
        $period = (string) ($_GET['period'] ?? 'today');
        $stats = sizo_analytics_stats($period);
        sizo_analytics_reply($stats['ok'] ? 200 : 503, $stats);
    }

    sizo_analytics_reply(404, ['ok' => false, 'message' => 'Rota não encontrada.']);
} catch (Throwable $e) {
    error_log('[sizo-page] analytics API failure: ' . $e->getMessage());
    sizo_analytics_reply(503, ['ok' => false, 'message' => 'Analytics indisponível.']);
}
