<?php
$pageTitle = $pageTitle ?? 'Sizo Software | Gestão e Facturação Empresarial';
$pageDesc = $pageDesc ?? 'ERP cloud para vendas, inventário, clientes, caixa, documentos fiscais e relatórios numa única plataforma.';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Manrope', 'system-ui', 'sans-serif'] },
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
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-sans antialiased text-slate-900 bg-white">
