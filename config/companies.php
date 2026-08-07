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
        error_log('[sizo-page] DB not configured (DB_NAME/DB_USER vazios). Crie o ficheiro .env no servidor.');
        return null;
    }

    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
        $pdo = new PDO($dsn, $user, (string) $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Throwable $e) {
        $pdo = null;
        error_log('[sizo-page] DB connection failed: ' . $e->getMessage());
    }

    return $pdo;
}

/**
 * @return list<string>
 */
function sizo_company_columns(PDO $db): array
{
    static $cols = null;
    if ($cols !== null) {
        return $cols;
    }
    try {
        $cols = $db->query('SHOW COLUMNS FROM companies')->fetchAll(PDO::FETCH_COLUMN) ?: [];
        $cols = array_map('strval', $cols);
    } catch (Throwable $e) {
        $cols = [];
        error_log('[sizo-page] SHOW COLUMNS companies failed: ' . $e->getMessage());
    }
    return $cols;
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

    $cols = sizo_company_columns($db);
    if ($cols === [] || !in_array('id', $cols, true) || !in_array('name', $cols, true)) {
        error_log('[sizo-page] Tabela companies inacessível ou sem colunas esperadas.');
        return [];
    }

    $select = ['id', 'name'];
    foreach (['subdomain', 'logo', 'initials', 'business_area', 'status', 'type', 'is_demo'] as $col) {
        if (in_array($col, $cols, true)) {
            $select[] = $col;
        }
    }

    $where = ['1=1'];
    if (in_array('status', $cols, true)) {
        $where[] = "status = 'active'";
    }
    if (in_array('is_demo', $cols, true)) {
        $where[] = 'COALESCE(is_demo, 0) = 0';
    }
    if (in_array('type', $cols, true)) {
        // Inclui client e NULL/vazio; exclui apenas internal
        $where[] = "(type IS NULL OR type = '' OR type = 'client')";
    }
    if (in_array('subdomain', $cols, true)) {
        $where[] = "LOWER(COALESCE(subdomain, '')) NOT IN ('app', 'admin')";
    }

    $sql = sprintf(
        'SELECT %s FROM companies WHERE %s ORDER BY name ASC LIMIT %d',
        implode(', ', $select),
        implode(' AND ', $where),
        max(1, min(100, $limit))
    );

    try {
        $rows = $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log('[sizo-page] companies query failed: ' . $e->getMessage() . ' SQL=' . $sql);
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
            'business_area' => !empty($row['business_area']) ? (string) $row['business_area'] : null,
            'logo_url' => $logo !== '' ? ('company-logo.php?id=' . $id) : null,
        ];
    }

    return $out;
}
