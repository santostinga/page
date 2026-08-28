<?php
require_once __DIR__ . '/env.php';

function sizo_analytics_enabled(): bool
{
    return strtolower((string) sizo_env('ANALYTICS_ENABLED', 'true')) !== 'false';
}

function sizo_analytics_token(): string
{
    return (string) sizo_env('ANALYTICS_TOKEN', '');
}

function sizo_analytics_storage_path(): string
{
    $configured = trim((string) sizo_env('ANALYTICS_STORAGE_PATH', ''));
    if ($configured !== '') {
        if (!preg_match('#^([a-zA-Z]:[\\\\/]|/)#', $configured)) {
            $configured = dirname(__DIR__) . DIRECTORY_SEPARATOR . $configured;
        }

        return $configured;
    }

    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'analytics.sqlite';
}

function sizo_analytics_db(): ?PDO
{
    static $pdo = null;
    static $tried = false;
    if ($tried) {
        return $pdo;
    }
    $tried = true;

    if (!extension_loaded('pdo_sqlite')) {
        error_log('[sizo-page] PDO SQLite extension is not available.');
        return null;
    }

    $path = sizo_analytics_storage_path();
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        error_log('[sizo-page] Analytics storage directory is not writable: ' . $dir);
        return null;
    }

    try {
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA journal_mode = WAL;');
        $pdo->exec('PRAGMA synchronous = NORMAL;');
    } catch (Throwable $e) {
        $pdo = null;
        error_log('[sizo-page] Analytics SQLite failed: ' . $e->getMessage());
    }

    return $pdo;
}

function sizo_analytics_ensure_schema(?PDO $db = null): bool
{
    $db = $db ?? sizo_analytics_db();
    if (!$db) {
        return false;
    }

    static $ready = false;
    if ($ready) {
        return true;
    }

    $db->exec(
        'CREATE TABLE IF NOT EXISTS page_analytics_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_type TEXT NOT NULL,
            page_path TEXT NOT NULL,
            page_title TEXT NULL,
            element_label TEXT NULL,
            element_href TEXT NULL,
            section_id TEXT NULL,
            referrer TEXT NULL,
            referrer_domain TEXT NULL,
            utm_source TEXT NULL,
            utm_medium TEXT NULL,
            utm_campaign TEXT NULL,
            country_code TEXT NULL,
            device_type TEXT NULL,
            browser TEXT NULL,
            session_id TEXT NOT NULL,
            visitor_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );
    $db->exec('CREATE INDEX IF NOT EXISTS idx_created ON page_analytics_events (created_at)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_type_created ON page_analytics_events (event_type, created_at)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_page_created ON page_analytics_events (page_path, created_at)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_referrer_created ON page_analytics_events (referrer_domain, created_at)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_session ON page_analytics_events (session_id)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_visitor_created ON page_analytics_events (visitor_hash, created_at)');

    $ready = true;

    return true;
}

function sizo_analytics_parse_referrer_domain(?string $referrer): ?string
{
    $referrer = trim((string) $referrer);
    if ($referrer === '') {
        return null;
    }
    $host = parse_url($referrer, PHP_URL_HOST);
    if (!is_string($host) || $host === '') {
        return null;
    }
    $host = strtolower($host);
    if (str_starts_with($host, 'www.')) {
        $host = substr($host, 4);
    }

    return $host;
}

function sizo_analytics_detect_device(?string $userAgent): string
{
    $ua = strtolower((string) $userAgent);
    if ($ua === '') {
        return 'unknown';
    }
    if (preg_match('/bot|crawl|spider|slurp|facebookexternalhit|preview/i', $ua)) {
        return 'bot';
    }
    if (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
        return 'tablet';
    }
    if (preg_match('/mobile|iphone|ipod|android.*mobile|windows phone/i', $ua)) {
        return 'mobile';
    }

    return 'desktop';
}

function sizo_analytics_detect_browser(?string $userAgent): string
{
    $ua = (string) $userAgent;
    if ($ua === '') {
        return 'unknown';
    }
    $checks = [
        'Edge' => '/Edg\//',
        'Chrome' => '/Chrome\//',
        'Firefox' => '/Firefox\//',
        'Safari' => '/Safari\//',
        'Opera' => '/OPR\//',
    ];
    foreach ($checks as $name => $pattern) {
        if (preg_match($pattern, $ua)) {
            return $name;
        }
    }

    return 'other';
}

function sizo_analytics_visitor_hash(?string $ip, ?string $userAgent): string
{
    $salt = (string) sizo_env('ANALYTICS_SALT', 'sizo-page-analytics');
    $ip = trim((string) $ip);
    $ua = trim((string) $userAgent);

    return hash('sha256', $salt . '|' . $ip . '|' . $ua);
}

function sizo_analytics_client_ip(): string
{
    $candidates = [
        $_SERVER['HTTP_CF_CONNECTING_IP'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ];
    foreach ($candidates as $value) {
        $value = trim((string) $value);
        if ($value === '') {
            continue;
        }
        if (str_contains($value, ',')) {
            $value = trim(explode(',', $value)[0]);
        }
        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return $value;
        }
    }

    return '';
}

function sizo_analytics_is_bot(?string $userAgent): bool
{
    return sizo_analytics_detect_device($userAgent) === 'bot';
}

function sizo_analytics_period_bounds(string $period): array
{
    $period = strtolower(trim($period));
    $now = new DateTimeImmutable('now');
    $todayStart = $now->setTime(0, 0, 0);

    return match ($period) {
        '7d' => [$todayStart->modify('-6 days'), $now, '7d'],
        '30d' => [$todayStart->modify('-29 days'), $now, '30d'],
        default => [$todayStart, $now, 'today'],
    };
}

/** @param array<int, array<string, mixed>> $events */
function sizo_analytics_store_events(array $events): array
{
    if (!sizo_analytics_enabled()) {
        return ['ok' => false, 'stored' => 0, 'message' => 'Analytics disabled'];
    }

    $db = sizo_analytics_db();
    if (!$db || !sizo_analytics_ensure_schema($db)) {
        return ['ok' => false, 'stored' => 0, 'message' => 'Storage unavailable'];
    }

    $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (sizo_analytics_is_bot($userAgent)) {
        return ['ok' => true, 'stored' => 0, 'message' => 'Ignored bot'];
    }

    $ip = sizo_analytics_client_ip();
    $visitorHash = sizo_analytics_visitor_hash($ip, $userAgent);
    $device = sizo_analytics_detect_device($userAgent);
    $browser = sizo_analytics_detect_browser($userAgent);
    $country = strtoupper(substr(trim((string) ($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '')), 0, 2));
    if ($country === '' || $country === 'XX') {
        $country = null;
    }

    $allowedTypes = ['pageview', 'click', 'section_view'];
    $stmt = $db->prepare(
        'INSERT INTO page_analytics_events
        (event_type, page_path, page_title, element_label, element_href, section_id, referrer, referrer_domain, utm_source, utm_medium, utm_campaign, country_code, device_type, browser, session_id, visitor_hash, created_at)
        VALUES
        (:event_type, :page_path, :page_title, :element_label, :element_href, :section_id, :referrer, :referrer_domain, :utm_source, :utm_medium, :utm_campaign, :country_code, :device_type, :browser, :session_id, :visitor_hash, :created_at)'
    );

    $stored = 0;
    foreach ($events as $event) {
        if (!is_array($event)) {
            continue;
        }
        $type = strtolower(trim((string) ($event['type'] ?? '')));
        if (!in_array($type, $allowedTypes, true)) {
            continue;
        }

        $pagePath = substr(trim((string) ($event['path'] ?? '/')), 0, 500);
        if ($pagePath === '') {
            $pagePath = '/';
        }

        $sessionId = substr(trim((string) ($event['session_id'] ?? '')), 0, 36);
        if (!preg_match('/^[a-f0-9-]{16,36}$/i', $sessionId)) {
            continue;
        }

        $referrer = substr(trim((string) ($event['referrer'] ?? '')), 0, 500);
        $createdAt = new DateTimeImmutable('now');

        $stmt->execute([
            ':event_type' => $type,
            ':page_path' => $pagePath,
            ':page_title' => substr(trim((string) ($event['title'] ?? '')), 0, 255) ?: null,
            ':element_label' => substr(trim((string) ($event['label'] ?? '')), 0, 255) ?: null,
            ':element_href' => substr(trim((string) ($event['href'] ?? '')), 0, 500) ?: null,
            ':section_id' => substr(trim((string) ($event['section'] ?? '')), 0, 64) ?: null,
            ':referrer' => $referrer ?: null,
            ':referrer_domain' => sizo_analytics_parse_referrer_domain($referrer),
            ':utm_source' => substr(trim((string) ($event['utm_source'] ?? '')), 0, 100) ?: null,
            ':utm_medium' => substr(trim((string) ($event['utm_medium'] ?? '')), 0, 100) ?: null,
            ':utm_campaign' => substr(trim((string) ($event['utm_campaign'] ?? '')), 0, 100) ?: null,
            ':country_code' => $country,
            ':device_type' => $device,
            ':browser' => $browser,
            ':session_id' => $sessionId,
            ':visitor_hash' => $visitorHash,
            ':created_at' => $createdAt->format('Y-m-d H:i:s'),
        ]);
        $stored++;
    }

    return ['ok' => true, 'stored' => $stored];
}

function sizo_analytics_stats(string $period = 'today'): array
{
    $db = sizo_analytics_db();
    if (!$db || !sizo_analytics_ensure_schema($db)) {
        return ['ok' => false, 'message' => 'Storage unavailable'];
    }

    [$from, $to, $label] = sizo_analytics_period_bounds($period);
    $fromSql = $from->format('Y-m-d H:i:s');
    $toSql = $to->format('Y-m-d H:i:s');

    $summaryStmt = $db->prepare(
        "SELECT
            SUM(CASE WHEN event_type = 'pageview' THEN 1 ELSE 0 END) AS pageviews,
            COUNT(DISTINCT CASE WHEN event_type = 'pageview' THEN visitor_hash END) AS unique_visitors,
            COUNT(DISTINCT session_id) AS sessions,
            SUM(CASE WHEN event_type = 'click' THEN 1 ELSE 0 END) AS clicks
         FROM page_analytics_events
         WHERE created_at BETWEEN :from AND :to"
    );
    $summaryStmt->execute([':from' => $fromSql, ':to' => $toSql]);
    $summary = $summaryStmt->fetch() ?: [];

    $pagesStmt = $db->prepare(
        "SELECT page_path AS label, COUNT(*) AS total
         FROM page_analytics_events
         WHERE event_type = 'pageview' AND created_at BETWEEN :from AND :to
         GROUP BY page_path
         ORDER BY total DESC
         LIMIT 10"
    );
    $pagesStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    $referrersStmt = $db->prepare(
        "SELECT COALESCE(referrer_domain, '(directo)') AS label, COUNT(*) AS total
         FROM page_analytics_events
         WHERE event_type = 'pageview' AND created_at BETWEEN :from AND :to
         GROUP BY referrer_domain
         ORDER BY total DESC
         LIMIT 10"
    );
    $referrersStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    $clicksStmt = $db->prepare(
        "SELECT COALESCE(NULLIF(element_label, ''), '(sem rótulo)') AS label, COUNT(*) AS total
         FROM page_analytics_events
         WHERE event_type = 'click' AND created_at BETWEEN :from AND :to
         GROUP BY element_label
         ORDER BY total DESC
         LIMIT 15"
    );
    $clicksStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    $countriesStmt = $db->prepare(
        "SELECT COALESCE(country_code, '??') AS label, COUNT(*) AS total
         FROM page_analytics_events
         WHERE event_type = 'pageview' AND created_at BETWEEN :from AND :to
         GROUP BY country_code
         ORDER BY total DESC
         LIMIT 10"
    );
    $countriesStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    $devicesStmt = $db->prepare(
        "SELECT device_type AS label, COUNT(*) AS total
         FROM page_analytics_events
         WHERE event_type = 'pageview' AND created_at BETWEEN :from AND :to
         GROUP BY device_type
         ORDER BY total DESC"
    );
    $devicesStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    $dailyStmt = $db->prepare(
        "SELECT DATE(created_at) AS day,
            SUM(CASE WHEN event_type = 'pageview' THEN 1 ELSE 0 END) AS pageviews,
            COUNT(DISTINCT visitor_hash) AS unique_visitors
         FROM page_analytics_events
         WHERE created_at BETWEEN :from AND :to
         GROUP BY DATE(created_at)
         ORDER BY day ASC"
    );
    $dailyStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    $sectionsStmt = $db->prepare(
        "SELECT COALESCE(section_id, '(desconhecida)') AS label, COUNT(*) AS total
         FROM page_analytics_events
         WHERE event_type = 'section_view' AND created_at BETWEEN :from AND :to
         GROUP BY section_id
         ORDER BY total DESC
         LIMIT 10"
    );
    $sectionsStmt->execute([':from' => $fromSql, ':to' => $toSql]);

    return [
        'ok' => true,
        'period' => $label,
        'from' => $from->format(DateTimeInterface::ATOM),
        'to' => $to->format(DateTimeInterface::ATOM),
        'summary' => [
            'pageviews' => (int) ($summary['pageviews'] ?? 0),
            'unique_visitors' => (int) ($summary['unique_visitors'] ?? 0),
            'sessions' => (int) ($summary['sessions'] ?? 0),
            'clicks' => (int) ($summary['clicks'] ?? 0),
        ],
        'pages' => $pagesStmt->fetchAll(),
        'referrers' => $referrersStmt->fetchAll(),
        'clicks' => $clicksStmt->fetchAll(),
        'countries' => $countriesStmt->fetchAll(),
        'devices' => $devicesStmt->fetchAll(),
        'sections' => $sectionsStmt->fetchAll(),
        'daily' => $dailyStmt->fetchAll(),
    ];
}

function sizo_analytics_token_valid(?string $token): bool
{
    $expected = sizo_analytics_token();
    if ($expected === '') {
        return false;
    }

    return hash_equals($expected, (string) $token);
}
