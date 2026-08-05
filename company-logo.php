<?php
/**
 * Serve o logótipo de uma empresa a partir de COMPANY_UPLOADS_PATH.
 */
require_once __DIR__ . '/config/env.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    exit;
}

$base = sizo_env('COMPANY_UPLOADS_PATH', '');
if ($base === '') {
    http_response_code(404);
    exit;
}

if (!preg_match('#^([a-zA-Z]:[\\\\/]|/)#', $base)) {
    $base = __DIR__ . DIRECTORY_SEPARATOR . $base;
}
$base = realpath($base);
if ($base === false || !is_dir($base)) {
    http_response_code(404);
    exit;
}

$dir = realpath($base . DIRECTORY_SEPARATOR . $id);
if ($dir === false || !str_starts_with($dir, $base) || !is_dir($dir)) {
    http_response_code(404);
    exit;
}

// Preferir nome em BD via query opcional; senão primeiro ficheiro de imagem.
$file = isset($_GET['f']) ? basename((string) $_GET['f']) : '';
$path = null;

if ($file !== '' && preg_match('/^[a-zA-Z0-9._-]+$/', $file)) {
    $candidate = realpath($dir . DIRECTORY_SEPARATOR . $file);
    if ($candidate !== false && str_starts_with($candidate, $dir) && is_file($candidate)) {
        $path = $candidate;
    }
}

if ($path === null) {
    // Tentar ler logo da BD se possível
    require_once __DIR__ . '/config/companies.php';
    $db = sizo_db();
    if ($db) {
        try {
            $stmt = $db->prepare('SELECT logo FROM companies WHERE id = ? AND status = ? LIMIT 1');
            $stmt->execute([$id, 'active']);
            $logo = (string) ($stmt->fetchColumn() ?: '');
            if ($logo !== '' && preg_match('/^[a-zA-Z0-9._-]+$/', $logo)) {
                $candidate = realpath($dir . DIRECTORY_SEPARATOR . $logo);
                if ($candidate !== false && str_starts_with($candidate, $dir) && is_file($candidate)) {
                    $path = $candidate;
                }
            }
        } catch (Throwable $e) {
            // ignore
        }
    }
}

if ($path === null) {
    foreach (scandir($dir) ?: [] as $f) {
        if ($f === '.' || $f === '..') {
            continue;
        }
        if (preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f)) {
            $path = $dir . DIRECTORY_SEPARATOR . $f;
            break;
        }
    }
}

if ($path === null || !is_file($path)) {
    http_response_code(404);
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$types = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml',
];
$mime = $types[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
header('Content-Length: ' . (string) filesize($path));
readfile($path);
exit;
