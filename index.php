<?php
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$pageTitle = 'Sizo Software | Gestão e Facturação Empresarial';
$pageDesc = 'Controle vendas, inventário, clientes, serviços, caixa, documentos fiscais e relatórios numa única plataforma.';
$sizoCfg = require __DIR__ . '/config/planos.php';
$planos = $sizoCfg['planos'];
$sizoContacto = $sizoCfg['contacto'];
$sizoNotaLicenca = $sizoCfg['nota_licenca'];
$sizoMailtoBase = 'mailto:' . rawurlencode($sizoContacto['email']);
$sizoWhatsAppUrl = $sizoContacto['whatsapp_url'];
$sizoAppUrl = $sizoContacto['app_url'] ?? 'https://app.sizotech.net';
$sizoFaq = require __DIR__ . '/config/faq.php';
$sizoFunc = require __DIR__ . '/config/funcionalidades.php';
require_once __DIR__ . '/config/companies.php';
$sizoEmpresas = sizo_fetch_client_companies();

$demoMailto = $sizoMailtoBase . '?subject=' . rawurlencode('Demonstração - Sizo Software');
$startMailto = $sizoMailtoBase . '?subject=' . rawurlencode('Começar gratuitamente - Sizo Software');

$featureIcons = [
    'dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'facturacao' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'venda-rapida' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
    'inventario' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    'clientes' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    'fornecedores' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    'servicos' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'operacoes' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    'automacao' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
    'relatorios' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'caixa' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    'permissoes' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'utilizadores' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
];

require __DIR__ . '/includes/head.php';
?>

<header id="site-nav" class="site-nav fixed top-0 left-0 right-0 z-50 w-full">
  <div class="flex w-full items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8 xl:px-10">
    <a href="#" class="flex shrink-0 items-center" aria-label="Sizo Software - início">
      <img src="assets/img/logo.png" alt="Sizo Software" class="h-8 w-auto object-contain sm:h-9" width="180" height="40" />
    </a>

    <nav class="hidden items-center gap-8 md:flex" aria-label="Principal">
      <a href="#funcionalidades" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Funcionalidades</a>
      <a href="#planos" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Planos</a>
      <a href="#cta" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Demonstração</a>
    </nav>

    <div class="flex items-center gap-2 sm:gap-3">
      <a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="hidden rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-blue-600 sm:inline-flex">
        Começar gratuitamente
      </a>
      <button type="button" id="mobile-menu-btn" class="inline-flex rounded-lg border border-slate-200 p-2 text-slate-700 md:hidden" aria-expanded="false" aria-controls="mobile-nav" aria-label="Abrir menu">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>

  <div id="mobile-nav" class="hidden border-t border-slate-100 bg-white px-4 py-4 md:hidden">
    <div class="flex flex-col gap-3">
      <a href="#funcionalidades" class="text-sm font-medium text-slate-700">Funcionalidades</a>
      <a href="#planos" class="text-sm font-medium text-slate-700">Planos</a>
      <a href="#cta" class="text-sm font-medium text-slate-700">Demonstração</a>
      <a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="mt-1 inline-flex items-center justify-center rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white">Começar gratuitamente</a>
    </div>
  </div>
</header>

<main>
  <!-- 1. HERO -->
  <section class="hero-surface relative overflow-hidden pt-28 pb-16 sm:pt-32 sm:pb-20 lg:pb-24">
    <div class="grid w-full items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-12 lg:px-8 xl:gap-16 xl:px-10">
      <div class="max-w-xl" data-aos="fade-up">
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-[3.1rem] lg:leading-[1.12]">
          Gestão e Facturação Empresarial sem complicações.
        </h1>
        <p class="mt-5 text-lg leading-relaxed text-slate-600">
          Controle vendas, inventário, clientes, serviços, caixa, documentos fiscais e relatórios numa única plataforma.
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-3">
          <a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-xl bg-brand px-6 py-3.5 text-sm font-semibold text-white shadow-lift transition hover:bg-blue-600">
            Começar gratuitamente
          </a>
          <a href="<?= htmlspecialchars($demoMailto, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-semibold text-slate-800 shadow-soft transition hover:border-slate-300 hover:bg-slate-50">
            Ver demonstração
          </a>
        </div>
      </div>

      <div class="hero-shot-wrap relative w-full" data-aos="fade-left" data-aos-delay="80">
        <div class="absolute -inset-3 rounded-[1.5rem] bg-blue-500/10 blur-2xl"></div>
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lift ring-1 ring-slate-900/5">
          <div class="browser-bar flex items-center gap-2 px-4 py-2.5">
            <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
            <span class="ml-3 flex-1 truncate rounded-md bg-white px-3 py-1 text-xs text-slate-400 ring-1 ring-slate-200">app.sizotech.net</span>
          </div>
          <div class="hero-shot-frame overflow-hidden bg-slate-50">
            <img src="assets/screenshots/hero-dashboard.png" alt="Dashboard Sizo Software" class="hero-shot block h-full w-full object-cover object-top" width="1200" height="760" fetchpriority="high">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. FUNCIONALIDADES -->
  <section id="funcionalidades" class="section-band section-band--white scroll-mt-24 py-20 sm:py-28">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
      <div class="w-full text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Funcionalidades</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
          <?= htmlspecialchars($sizoFunc['lead']['titulo'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="mt-4 text-lg text-slate-600">
          <?= htmlspecialchars($sizoFunc['lead']['texto'], ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php foreach ($sizoFunc['cartoes'] as $i => $card):
          $src = 'assets/screenshots/' . rawurlencode($card['screenshot']);
          $path = __DIR__ . '/assets/screenshots/' . $card['screenshot'];
          $exists = is_file($path);
        ?>
        <button
          type="button"
          class="feature-tile group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-soft"
          data-aos="fade-up"
          data-aos-delay="<?= min($i * 40, 280) ?>"
          data-feature-open
          data-title="<?= htmlspecialchars($card['titulo'], ENT_QUOTES, 'UTF-8') ?>"
          data-desc="<?= htmlspecialchars($card['descricao'], ENT_QUOTES, 'UTF-8') ?>"
          data-detail="<?= htmlspecialchars($card['detalhe'], ENT_QUOTES, 'UTF-8') ?>"
          data-src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
        >
          <div class="aspect-[16/10] overflow-hidden bg-slate-100">
            <?php if ($exists): ?>
            <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($card['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="h-full w-full object-cover object-top" loading="lazy" width="640" height="400">
            <?php else: ?>
            <div class="flex h-full items-center justify-center text-sm text-slate-400">Screenshot</div>
            <?php endif; ?>
          </div>
          <div class="flex flex-1 flex-col p-5">
            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-brand-soft text-brand">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= htmlspecialchars($featureIcons[$card['id']] ?? $featureIcons['dashboard'], ENT_QUOTES, 'UTF-8') ?>"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900"><?= htmlspecialchars($card['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="mt-1.5 flex-1 text-sm leading-relaxed text-slate-600"><?= htmlspecialchars($card['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand">
              Ver detalhes
              <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
          </div>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 3. TUDO O QUE O SIZO FAZ -->
  <section id="capacidades" class="section-band section-band--muted scroll-mt-24 py-20 sm:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Cobertura</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
          Tudo o que o Sizo faz
        </h2>
        <p class="mt-4 text-lg text-slate-600">
          Capacidade completa da plataforma, item a item.
        </p>
      </div>
      <ul class="mt-12 grid gap-3 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up">
        <?php foreach ($sizoFunc['checklist'] as $item):
          $titulo = is_array($item) ? ($item['titulo'] ?? '') : (string) $item;
          $texto = is_array($item) ? ($item['texto'] ?? '') : '';
        ?>
        <li class="check-item flex gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-4 shadow-soft sm:px-5">
          <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
          </span>
          <div class="min-w-0">
            <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($texto !== ''): ?>
            <p class="mt-1 text-sm leading-relaxed text-slate-600"><?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- 5. BENEFÍCIOS -->
  <section id="beneficios" class="section-band section-band--white scroll-mt-24 py-20 sm:py-28">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
      <div class="w-full text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Benefícios</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
          Feito para operar com confiança
        </h2>
      </div>
      <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($sizoFunc['beneficios'] as $bi => $ben): ?>
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft" data-aos="fade-up" data-aos-delay="<?= min($bi * 50, 280) ?>">
          <div class="benefit-icon mb-4 flex h-10 w-10 items-center justify-center rounded-xl">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
          <h3 class="text-base font-bold text-slate-900"><?= htmlspecialchars($ben['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="mt-2 text-sm leading-relaxed text-slate-600"><?= htmlspecialchars($ben['texto'], ENT_QUOTES, 'UTF-8') ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 6. PLANOS -->
  <section id="planos" class="section-band section-band--soft scroll-mt-24 py-20 sm:py-28">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
      <div class="w-full text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Planos</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
          Escolha o plano certo para crescer
        </h2>
        <p class="mt-4 text-lg text-slate-600">
          Starter, Business ou Enterprise: mesma plataforma, cobertura diferente.
        </p>
      </div>

      <p class="mt-8 w-full rounded-xl border border-blue-100 bg-brand-soft px-4 py-3 text-center text-sm text-slate-700" data-aos="fade-up">
        <?= htmlspecialchars($sizoNotaLicenca, ENT_QUOTES, 'UTF-8') ?>
      </p>

      <div class="mt-12 grid gap-6 lg:grid-cols-3 lg:items-stretch">
        <?php foreach ($planos as $pi => $plan):
          $isFeatured = !empty($plan['destaque']);
          $mailto = $sizoMailtoBase . '?subject=' . rawurlencode('Interesse no plano ' . $plan['nome'] . ' - Sizo Software');
        ?>
        <article class="plan-card relative flex flex-col rounded-2xl border p-8 <?= $isFeatured ? 'is-featured border-brand bg-gradient-to-b from-brand-soft to-white ring-2 ring-brand/20' : 'border-slate-200 bg-white shadow-soft' ?>" data-aos="fade-up" data-aos-delay="<?= $pi * 80 ?>">
          <?php if ($isFeatured): ?>
          <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-brand px-3.5 py-1 text-xs font-bold uppercase tracking-wide text-white shadow-soft">Mais popular</span>
          <?php endif; ?>
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($plan['tipo'], ENT_QUOTES, 'UTF-8') ?></p>
              <h3 class="mt-1 text-2xl font-extrabold text-slate-900"><?= htmlspecialchars($plan['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="mt-1 text-sm font-medium text-brand"><?= htmlspecialchars($plan['titulo_card'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>
          <div class="mt-6 flex flex-wrap items-baseline gap-1">
            <span class="text-4xl font-extrabold tracking-tight text-slate-900"><?= htmlspecialchars($plan['preco_mt'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="text-lg font-semibold text-slate-500">MT</span>
            <span class="text-sm text-slate-400"><?= htmlspecialchars($plan['preco_periodo'] ?? '/ mês', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <p class="mt-4 text-sm leading-relaxed text-slate-600"><?= htmlspecialchars($plan['resumo'], ENT_QUOTES, 'UTF-8') ?></p>
          <ul class="mt-6 space-y-3 border-t border-slate-100 pt-6 text-sm text-slate-700">
            <?php foreach ($plan['bullets'] as $bullet): ?>
            <li class="flex gap-2.5">
              <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
              </span>
              <span><?= htmlspecialchars($bullet, ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <div class="mt-auto pt-8">
            <a href="<?= htmlspecialchars($mailto, ENT_QUOTES, 'UTF-8') ?>" class="flex w-full items-center justify-center rounded-xl px-5 py-3.5 text-sm font-semibold transition <?= $isFeatured ? 'bg-brand text-white shadow-lift hover:bg-blue-600' : 'bg-slate-900 text-white hover:bg-slate-800' ?>">
              Começar com <?= htmlspecialchars($plan['nome'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- EMPRESAS -->
  <?php if (!empty($sizoEmpresas)):
    $empresasCount = count($sizoEmpresas);
    // Repetir para o marquee ficar cheio mesmo com poucas empresas
    $marqueeTrack = $sizoEmpresas;
    while (count($marqueeTrack) < 8) {
      $marqueeTrack = array_merge($marqueeTrack, $sizoEmpresas);
    }
  ?>
  <section id="empresas" class="clients-section section-band section-band--white scroll-mt-24 py-20 sm:py-28">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
      <div class="w-full text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Clientes</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
          Empresas que usam o Sizo
        </h2>
        <p class="mt-6 inline-flex flex-wrap items-baseline justify-center gap-x-2 gap-y-1">
          <span class="text-5xl font-extrabold tracking-tight text-brand sm:text-6xl">+</span>
          <span class="clients-count text-5xl font-extrabold tracking-tight text-slate-900 sm:text-6xl" data-count="<?= (int) $empresasCount ?>" data-suffix="">0</span>
          <span class="text-lg font-medium text-slate-500 sm:text-xl">empresas a crescer com o Sizo</span>
        </p>
      </div>
    </div>

    <div class="clients-marquee mt-12" data-aos="fade-up" aria-label="Logótipos de empresas clientes">
      <div class="clients-marquee-fade clients-marquee-fade--left" aria-hidden="true"></div>
      <div class="clients-marquee-fade clients-marquee-fade--right" aria-hidden="true"></div>
      <div class="clients-marquee-track">
        <?php for ($loop = 0; $loop < 2; $loop++): ?>
        <ul class="clients-marquee-group" <?= $loop === 1 ? 'aria-hidden="true"' : '' ?>>
          <?php foreach ($marqueeTrack as $empresa): ?>
          <li class="clients-card">
            <?php if (!empty($empresa['logo_url'])): ?>
            <span class="clients-card-logo">
              <img src="<?= htmlspecialchars($empresa['logo_url'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-full w-full object-contain" loading="lazy" width="48" height="48">
            </span>
            <?php else: ?>
            <span class="clients-card-initials"><?= htmlspecialchars($empresa['initials'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <span class="clients-card-meta">
              <span class="clients-card-name"><?= htmlspecialchars($empresa['name'], ENT_QUOTES, 'UTF-8') ?></span>
              <?php if (!empty($empresa['business_area'])): ?>
              <span class="clients-card-area"><?= htmlspecialchars($empresa['business_area'], ENT_QUOTES, 'UTF-8') ?></span>
              <?php endif; ?>
            </span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endfor; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- FAQ -->
  <section id="faq" class="section-band section-band--muted scroll-mt-24 py-20 sm:py-28">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
      <div class="text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">FAQ</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
          Perguntas frequentes
        </h2>
      </div>
      <div class="mt-12 grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-4 md:items-start">
        <?php foreach ($sizoFaq as $fi => $item): ?>
        <details class="faq-item rounded-2xl border border-slate-200 bg-white shadow-soft" data-aos="fade-up" data-aos-delay="<?= min($fi * 30, 200) ?>">
          <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 sm:px-6 sm:py-5">
            <span class="pr-2 text-left text-base font-semibold text-slate-900"><?= htmlspecialchars($item['pergunta'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="faq-chevron flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
              <svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
          </summary>
          <div class="border-t border-slate-100 px-5 pb-5 pt-4 text-sm leading-relaxed text-slate-600 sm:px-6">
            <?= htmlspecialchars($item['resposta'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 8. CTA FINAL -->
  <section id="cta" class="section-band section-band--white scroll-mt-24 py-20 sm:py-28">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
      <div class="cta-band relative overflow-hidden rounded-[1.75rem] px-8 py-16 text-center shadow-lift sm:px-16 sm:py-20" data-aos="fade-up">
        <h2 class="relative text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
          Pronto para modernizar a gestão da sua empresa?
        </h2>
        <p class="relative mt-5 text-lg text-blue-100">
          Comece agora ou peça uma demonstração. Em minutos percebe o valor do Sizo.
        </p>
        <div class="relative mt-10 flex flex-wrap justify-center gap-3">
          <a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-xl bg-white px-7 py-3.5 text-sm font-semibold text-brand shadow-soft transition hover:bg-blue-50">
            Começar agora
          </a>
          <a href="<?= htmlspecialchars($demoMailto, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-7 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
            Solicitar demonstração
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="border-t border-slate-200 bg-slate-50">
  <div class="w-full px-4 py-14 sm:px-6 lg:px-8 xl:px-10">
    <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
      <div class="sm:col-span-2 lg:col-span-1">
        <img src="assets/img/logo.png" alt="Sizo Software" class="h-8 w-auto object-contain" width="160" height="36" />
        <p class="mt-4 text-sm leading-relaxed text-slate-600">
          ERP cloud para gestão e facturação empresarial.
        </p>
      </div>
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Produto</h3>
        <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
          <li><a href="#funcionalidades" class="transition hover:text-slate-900">Funcionalidades</a></li>
          <li><a href="#planos" class="transition hover:text-slate-900">Planos</a></li>
            <li><a href="#cta" class="transition hover:text-slate-900">Demonstração</a></li>
          <li><a href="#" class="transition hover:text-slate-900">Documentação</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Legal</h3>
        <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
          <li><a href="#" class="transition hover:text-slate-900">Política de Privacidade</a></li>
          <li><a href="#" class="transition hover:text-slate-900">Termos</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contacto</h3>
        <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
          <li>
            <a href="<?= htmlspecialchars($sizoMailtoBase, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-slate-900">
              <?= htmlspecialchars($sizoContacto['email'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
          <li>
            <a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-slate-900" target="_blank" rel="noopener noreferrer">
              <?= htmlspecialchars($sizoContacto['telefone_display'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-slate-200 pt-8 text-sm text-slate-500 sm:flex-row sm:items-center">
      <p>© <span id="footer-year"></span> Sizo Software</p>
      <p>Gestão e facturação sem complicações.</p>
    </div>
  </div>
</footer>

<!-- Feature modal -->
<div id="feature-modal" class="feature-modal fixed inset-0 z-[60] flex items-end justify-center p-4 sm:items-center" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="feature-modal-title">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-modal-close></div>
  <div class="feature-modal-panel relative z-10 max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl">
    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white/95 px-5 py-4 backdrop-blur sm:px-6">
      <h3 id="feature-modal-title" class="text-lg font-bold text-slate-900">Funcionalidade</h3>
      <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50" data-modal-close aria-label="Fechar">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-5 sm:p-6">
      <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
        <img id="feature-modal-img" src="" alt="" class="block w-full object-cover object-top">
      </div>
      <p id="feature-modal-desc" class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base"></p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600">Começar gratuitamente</a>
        <button type="button" class="inline-flex rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" data-modal-close>Fechar</button>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
