<?php
$pageTitle = $pageTitle ?? 'Sizo Technology — Sizo Software';
$pageDesc = $pageDesc ?? 'Soluções modernas para gestão empresarial, facturação, stock e operações comerciais.';

// Comentário em `<head>`: no site em produção usa «Ver código-fonte» e confere `sizo-deploy` com `git log -1 --oneline`.
$sizoGitRef = 'no-git';
$gitHeadFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR . 'HEAD';
if (is_readable($gitHeadFile)) {
    $gitHead = trim((string) file_get_contents($gitHeadFile));
    if (strpos($gitHead, 'ref:') === 0) {
        $refRel = trim(substr($gitHead, 4));
        $refPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $refRel);
        if (is_readable($refPath)) {
            $sizoGitRef = substr(trim((string) file_get_contents($refPath)), 0, 7);
        }
    } elseif ($gitHead !== '') {
        $sizoGitRef = substr($gitHead, 0, 7);
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <!-- sizo-deploy <?php echo htmlspecialchars($sizoGitRef, ENT_QUOTES, 'UTF-8'); ?> -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          colors: {
            brand: { blue: '#2563eb', orange: '#f97316', dark: '#0a0f1a' }
          },
          boxShadow: {
            'glow': '0 0 80px -12px rgba(37, 99, 235, 0.55)',
            'glow-sm': '0 0 40px -8px rgba(37, 99, 235, 0.4)',
            'card': '0 25px 50px -12px rgba(0, 0, 0, 0.35)',
          },
          animation: { 'float': 'float 6s ease-in-out infinite' },
          keyframes: {
            float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-12px)' } }
          }
        }
      }
    };
  </script>
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-sans antialiased text-slate-900 bg-white">
