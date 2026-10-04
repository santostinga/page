<?php
declare(strict_types=1);

require_once __DIR__ . '/config/https.php';
sizo_force_canonical_https();

/** @var array<string, string> $guests */
$guests = require __DIR__ . '/config/convite_guests.php';

$slug = isset($_GET['slug']) ? strtolower(trim((string) $_GET['slug'])) : '';
$slug = preg_replace('/[^a-z0-9-]/', '', $slug) ?? '';

$reserved = [
    'register',
    'cadastro',
    'api',
    'assets',
    'storage',
    'admin',
    'convite',
    'index',
    'subscricao',
];

if ($slug === '' || !isset($guests[$slug]) || in_array($slug, $reserved, true)) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Convite não encontrado</title></head><body style="font-family:system-ui,sans-serif;padding:2rem;text-align:center"><h1>Convite não encontrado</h1><p>Este link não é válido ou expirou.</p></body></html>';
    exit;
}

$guestName = $guests[$slug];
$templatePath = __DIR__ . '/convite/template.html';

if (!is_readable($templatePath)) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Convite indisponível.';
    exit;
}

$html = file_get_contents($templatePath);
if ($html === false) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Convite indisponível.';
    exit;
}

require_once __DIR__ . '/convite/images.php';
$html = convite_inject_images($html);

$guestHtml = htmlspecialchars($guestName, ENT_QUOTES | ENT_HTML5, 'UTF-8');

$html = preg_replace(
    '/(<div class="guest">)(.*?)(<\/div>)/s',
    '$1' . $guestHtml . '$3',
    $html,
    1
);

if (!str_contains($html, $guestHtml)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Erro ao personalizar convite.';
    exit;
}

if (!str_contains($html, 'name="robots"')) {
    $html = preg_replace(
        '/<head>/i',
        '<head><meta name="robots" content="noindex, nofollow">',
        $html,
        1
    );
}

require_once __DIR__ . '/convite/splash.php';
require_once __DIR__ . '/convite/ui.php';
$html = convite_apply_splash($html);
$html = convite_apply_ui($html);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

echo $html;
