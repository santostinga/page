<?php
require_once __DIR__ . '/config/https.php';
sizo_force_canonical_https();

session_start();
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$pageTitle = 'Criar empresa | Sizo Software';
$pageDesc = 'Crie a sua empresa no Sizo Software e comece a gerir o negócio em poucos minutos.';
$isSignupPage = true;
$_SESSION['signup_csrf'] = $_SESSION['signup_csrf'] ?? bin2hex(random_bytes(32));

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/signup.php';
require __DIR__ . '/includes/footer.php';
