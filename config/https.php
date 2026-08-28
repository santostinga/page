<?php
/**
 * Canonical HTTPS host and redirect helpers.
 */
function sizo_request_is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
        return true;
    }
    if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '')) === 'on') {
        return true;
    }
    if ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443) {
        return true;
    }

    return false;
}

function sizo_canonical_host(): string
{
    return 'www.sizotech.net';
}

function sizo_canonical_base_url(): string
{
    return 'https://' . sizo_canonical_host();
}

function sizo_force_canonical_https(): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host);

    // Só aplicar em produção (domínios públicos do site).
    if (!preg_match('/^(www\.)?sizotech\.net$/', $host)) {
        return;
    }

    $needsRedirect = $host !== sizo_canonical_host() || !sizo_request_is_https();
    if (!$needsRedirect) {
        return;
    }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: ' . sizo_canonical_base_url() . $uri, true, 301);
    exit;
}

function sizo_canonical_url(?string $path = null): string
{
    if ($path === null) {
        $path = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = strtok($path, '?') ?: '/';
    } elseif ($path !== '' && $path[0] !== '/') {
        $path = '/' . $path;
    }

    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host);
    if (!preg_match('/^(www\.)?sizotech\.net$/', $host)) {
        $scheme = sizo_request_is_https() ? 'https' : 'http';

        return $scheme . '://' . $host . $path;
    }

    return sizo_canonical_base_url() . $path;
}
