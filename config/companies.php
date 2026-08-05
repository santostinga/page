<?php
require_once __DIR__ . '/env.php';

/**
 * PDO partilhado (ou null se a ligação falhar).
 */
function sizo_db(): ?PDO
{
    static $pdo = null;
    static $tried = false;
    if ($tried) {
        return $pdo;
    }
    $tried = true;

    $host = sizo_env('DB_HOST', '127.0.0.1');
    $port = sizo_env('DB_PORT', '3306');
    $name = sizo_env('DB_NAME', '');
    $user = sizo_env('DB_USER', '');
    $pass = sizo_env('DB_PASS', '');

    if ($name === '' || $user === '') {
        return null;
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name),
            $user,
            $pass ?? '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 3,
            ]
        );
    } catch (Throwable $e) {
        $pdo = null;
        error_log('[sizo-page] DB connection failed: ' . $e->getMessage());
    }

    return $pdo;
}

/**
 * Empresas activas (clientes reais) para a landing.
 *
 * @return list<array{id:int,name:string,subdomain:string,logo:?string,initials:?string,business_area:?string,logo_url:?string}>
 */
function sizo_fetch_client_companies(int $limit = 48): array
{
    $db = sizo_db();
    if (!$db) {
        return [];
    }

    try {
        $stmt = $db->prepare(
            "SELECT id, name, subdomain, logo, initials, business_area
             FROM companies
             WHERE status = 'active'
               AND COALESCE(is_demo, 0) = 0
               AND COALESCE(type, 'client') = 'client'
               AND LOWER(subdomain) <> 'app'
             ORDER BY name ASC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log('[sizo-page] companies query failed: ' . $e->getMessage());
        return [];
    }

    $out = [];
    foreach ($rows as $row) {
        $id = (int) ($row['id'] ?? 0);
        $name = trim((string) ($row['name'] ?? ''));
        if ($id <= 0 || $name === '') {
            continue;
        }
        $logo = trim((string) ($row['logo'] ?? ''));
        $initials = trim((string) ($row['initials'] ?? ''));
        if ($initials === '') {
            $parts = preg_split('/\s+/u', $name) ?: [];
            $first = (string) ($parts[0] ?? $name);
            $second = (string) ($parts[1] ?? '');
            if (function_exists('mb_substr')) {
                $initials = mb_strtoupper(mb_substr($first, 0, 1));
                if ($second !== '') {
                    $initials .= mb_strtoupper(mb_substr($second, 0, 1));
                }
            } else {
                $initials = strtoupper(substr($first, 0, 1));
                if ($second !== '') {
                    $initials .= strtoupper(substr($second, 0, 1));
                }
            }
        }
        $out[] = [
            'id' => $id,
            'name' => $name,
            'subdomain' => (string) ($row['subdomain'] ?? ''),
            'logo' => $logo !== '' ? $logo : null,
            'initials' => $initials,
            'business_area' => ($row['business_area'] ?? null) ? (string) $row['business_area'] : null,
            'logo_url' => $logo !== '' ? ('company-logo.php?id=' . $id) : null,
        ];
    }

    return $out;
}
