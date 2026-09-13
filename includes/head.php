<?php
require_once __DIR__ . '/../config/https.php';
require_once __DIR__ . '/../config/assets.php';

$pageTitle = $pageTitle ?? 'Sizo Software | Gestão e Facturação Empresarial';
$pageDesc = $pageDesc ?? 'ERP cloud para vendas, inventário, clientes, caixa, documentos fiscais e relatórios numa única plataforma.';
$canonicalUrl = sizo_canonical_url();
$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$host = preg_replace('/:\d+$/', '', $host);
$isPublicSite = (bool) preg_match('/^(www\.)?sizotech\.net$/', $host);
?>
<!DOCTYPE html>
<html lang="pt"<?= !empty($isSignupPage) ? ' class="signup-boot"' : '' ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    (function () {
      try {
        var path = String(location.pathname || '');
        var onRegister = /\/(register|cadastro)\/?$/i.test(path);
        if (!onRegister && (location.hash === '#cadastro' || location.hash === '#register')) {
          var q = '';
          try {
            var raw = sessionStorage.getItem('sizotech_signup_session');
            if (raw) {
              var data = JSON.parse(raw);
              if (data && data.plan_code) q = '?plan=' + encodeURIComponent(data.plan_code);
            }
          } catch (e) {}
          var base = path.replace(/\/?$/, '/');
          location.replace(base + 'register' + q);
          return;
        }
        if (onRegister) document.documentElement.classList.add('signup-boot');
      } catch (e) {}
    })();
  </script>
  <style id="signup-boot-css">
    html.signup-boot,
    html.signup-boot body { overflow: hidden; background: #fff; }
    html.signup-boot #signup-view { display: flex !important; visibility: visible !important; }
  </style>
<?php if ($isPublicSite): ?>
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<?php endif; ?>
  <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="icon" type="image/png" href="<?= htmlspecialchars(sizo_asset('assets/img/fav.png'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="shortcut icon" type="image/png" href="<?= htmlspecialchars(sizo_asset('assets/img/fav.png'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="apple-touch-icon" href="<?= htmlspecialchars(sizo_asset('assets/img/fav.png'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Montserrat', 'system-ui', 'sans-serif'] },
          colors: {
            brand: { DEFAULT: '#2563EB', soft: '#EFF6FF', muted: '#DBEAFE' }
          },
          boxShadow: {
            'soft': '0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.06)',
            'lift': '0 12px 40px rgba(37, 99, 235, 0.12)',
            'card': '0 4px 24px rgba(15, 23, 42, 0.06)',
          }
        }
      }
    };
  </script>
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(sizo_asset('assets/css/style.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="font-sans antialiased text-slate-900 bg-white<?= !empty($isSignupPage) ? ' signup-page' : '' ?>"<?= !empty($isSignupPage) ? ' data-page="register"' : '' ?>>
