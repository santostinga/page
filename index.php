<?php
// Evita que proxies/CDN/browser sirva HTML antigo após deploy (Cloudways, Breeze, etc.).
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$pageTitle = 'Sizo Technology — Sizo Software';
$pageDesc = 'Soluções modernas para gestão empresarial, facturação, stock e operações comerciais.';
$sizoCfg = require __DIR__ . '/config/planos.php';
$planos = $sizoCfg['planos'];
$sizoContacto = $sizoCfg['contacto'];
$sizoNotaLicenca = $sizoCfg['nota_licenca'];
$sizoComparacao = $sizoCfg['comparacao'];
$sizoMailtoBase = 'mailto:' . rawurlencode($sizoContacto['email']);
$sizoWhatsAppUrl = $sizoContacto['whatsapp_url'];
$sizoFaq = require __DIR__ . '/config/faq.php';
$sizoFuncionalidades = require __DIR__ . '/config/funcionalidades.php';
require_once __DIR__ . '/includes/feature-art.php';

require __DIR__ . '/includes/head.php';
?>

<!-- Navigation -->
<header class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 bg-slate-950/75 nav-blur">
  <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
    <a href="#" class="flex items-center" aria-label="Sizo TECH — início">
      <span class="inline-flex items-center rounded-xl bg-white px-3 py-1.5 shadow-sm ring-1 ring-white/20">
        <img src="assets/img/logo.png" alt="Sizo TECH — Smart IT Solutions" class="h-7 w-auto max-h-8 object-contain object-left sm:h-8 sm:max-h-9" width="200" height="48" />
      </span>
    </a>
    <nav class="hidden items-center gap-7 lg:gap-8 md:flex" aria-label="Principal">
      <a href="#sobre" class="text-sm font-medium text-slate-300 transition hover:text-white">Sobre</a>
      <a href="#funcionalidades" class="text-sm font-medium text-slate-300 transition hover:text-white">Serviços</a>
      <a href="#planos" class="text-sm font-medium text-slate-300 transition hover:text-white">Planos</a>
      <a href="#comparacao-planos" class="text-sm font-medium text-slate-300 transition hover:text-white">Comparar</a>
      <a href="#screenshots" class="text-sm font-medium text-slate-300 transition hover:text-white">Plataforma</a>
      <a href="#faq" class="text-sm font-medium text-slate-300 transition hover:text-white">FAQ</a>
      <a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="text-sm font-medium text-slate-300 transition hover:text-white" target="_blank" rel="noopener noreferrer">Contacto</a>
    </nav>
    <div class="flex items-center gap-3">
      <a href="#cta" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
        Demonstração
      </a>
      <button type="button" id="mobile-menu-btn" class="inline-flex rounded-lg border border-white/10 p-2 text-white md:hidden" aria-expanded="false" aria-controls="mobile-nav" aria-label="Abrir menu">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>
  <div id="mobile-nav" class="hidden border-t border-white/10 bg-slate-950/95 px-4 py-4 md:hidden">
    <div class="flex flex-col gap-3">
      <a href="#sobre" class="text-sm font-medium text-slate-300">Sobre</a>
      <a href="#funcionalidades" class="text-sm font-medium text-slate-300">Serviços</a>
      <a href="#planos" class="text-sm font-medium text-slate-300">Planos</a>
      <a href="#screenshots" class="text-sm font-medium text-slate-300">Plataforma</a>
      <a href="#comparacao-planos" class="text-sm font-medium text-slate-300">Comparar</a>
      <a href="#faq" class="text-sm font-medium text-slate-300">FAQ</a>
      <a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="text-sm font-medium text-slate-300" target="_blank" rel="noopener noreferrer">Contacto</a>
    </div>
  </div>
</header>

<main>
  <!-- Hero -->
  <section class="relative min-h-screen overflow-hidden hero-gradient pt-24 pb-20 sm:pt-28 lg:pt-32">
    <div class="glow-orb -left-32 top-20 h-96 w-96 bg-blue-600/40 opacity-70"></div>
    <div class="glow-orb right-0 top-1/3 h-80 w-80 bg-orange-500/25 opacity-60"></div>
    <div class="glow-orb bottom-0 left-1/3 h-72 w-72 bg-blue-500/30 opacity-50"></div>

    <div class="relative mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8">
      <div data-aos="fade-up">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-medium uppercase tracking-wider text-blue-300 glass-panel">
          <span class="h-1.5 w-1.5 rounded-full bg-green-400 shadow-[0_0_8px_#4ade80]"></span>
          Sizo Software · SaaS empresarial
        </div>
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl xl:text-[3.5rem] xl:leading-[1.08]">
          <span class="text-gradient">Tecnologia inteligente</span><br>
          <span class="text-gradient-accent">para empresas modernas.</span>
        </h1>
        <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-400">
          A Sizo Technology desenvolve soluções modernas para gestão empresarial, controlo de stock, facturação e operações comerciais através da plataforma <strong class="font-semibold text-slate-200">Sizo Software</strong>.
        </p>
        <div class="mt-10 flex flex-wrap items-center gap-4">
          <a href="#cta" class="inline-flex items-center justify-center rounded-full bg-blue-600 px-8 py-3.5 text-base font-semibold text-white shadow-xl shadow-blue-600/35 transition hover:bg-blue-500 hover:shadow-glow">
            Solicitar demonstração
          </a>
          <a href="#planos" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-8 py-3.5 text-base font-semibold text-white backdrop-blur transition hover:border-white/30 hover:bg-white/10">
            Ver planos
          </a>
          <a href="#funcionalidades" class="inline-flex items-center gap-2 text-sm font-medium text-slate-400 transition hover:text-white">
            Conhecer a plataforma
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
        <dl class="mt-14 grid grid-cols-2 gap-6 sm:grid-cols-3">
          <div class="glass-panel rounded-2xl p-5 text-center sm:text-left">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Disponibilidade</dt>
            <dd class="mt-1 text-2xl font-bold text-white">Cloud 24/7</dd>
          </div>
          <div class="glass-panel rounded-2xl p-5 text-center sm:text-left">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Multiempresa</dt>
            <dd class="mt-1 text-2xl font-bold text-white">Ilimitado</dd>
          </div>
          <div class="col-span-2 glass-panel rounded-2xl p-5 text-center sm:text-left sm:col-span-1">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Segurança</dt>
            <dd class="mt-1 text-2xl font-bold text-white">Enterprise</dd>
          </div>
        </dl>
      </div>

      <div class="mockup-wrap relative lg:pl-4" data-aos="fade-left" data-aos-delay="100">
        <div class="absolute -inset-4 rounded-3xl bg-blue-500/20 blur-3xl"></div>
        <div class="relative mockup-frame animate-float">
          <div class="browser-chrome flex items-center gap-2 px-4 py-3">
            <span class="h-3 w-3 rounded-full bg-red-500/80"></span>
            <span class="h-3 w-3 rounded-full bg-amber-400/80"></span>
            <span class="h-3 w-3 rounded-full bg-green-500/80"></span>
            <span class="ml-4 flex-1 truncate rounded-md bg-slate-800/80 px-3 py-1 text-xs text-slate-500">app.sizo.technology</span>
          </div>
          <?php $heroDashboard = __DIR__ . '/assets/screenshots/hero-dashboard.png'; ?>
          <div class="relative overflow-hidden rounded-b-2xl border border-t-0 border-white/10 bg-slate-100 shadow-card ring-1 ring-black/20">
            <?php if (is_file($heroDashboard)): ?>
            <img src="assets/screenshots/hero-dashboard.png" alt="Dashboard Sizo Software" class="block min-h-[280px] w-full object-cover object-top sm:min-h-[380px]" loading="lazy" width="1200" height="760">
            <?php else: ?>
            <!-- Mini dashboard mockup (substitua por hero-dashboard.png em assets/screenshots/) -->
            <div class="flex min-h-[320px] sm:min-h-[380px]">
              <aside class="hidden w-14 shrink-0 flex-col gap-2 border-r border-slate-200 bg-slate-50 p-2 sm:flex">
                <div class="mx-auto h-8 w-8 rounded-lg bg-gradient-to-br from-blue-600 to-orange-500"></div>
                <div class="mt-2 space-y-1">
                  <div class="h-8 rounded-lg bg-blue-100"></div>
                  <div class="h-8 rounded-lg bg-transparent"></div>
                  <div class="h-8 rounded-lg bg-transparent"></div>
                  <div class="h-8 rounded-lg bg-transparent"></div>
                </div>
              </aside>
              <div class="flex-1 space-y-3 p-3 sm:p-4">
                <div class="flex items-center justify-between gap-2">
                  <div>
                    <div class="h-4 w-40 rounded bg-slate-300"></div>
                    <div class="mt-2 h-3 w-56 rounded bg-slate-200"></div>
                  </div>
                  <div class="hidden h-9 w-28 rounded-lg bg-blue-600 sm:block"></div>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                  <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100">
                    <div class="h-2 w-16 rounded bg-slate-200"></div>
                    <div class="mt-2 h-6 w-24 rounded bg-slate-800"></div>
                  </div>
                  <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100">
                    <div class="h-2 w-14 rounded bg-slate-200"></div>
                    <div class="mt-2 h-6 w-12 rounded bg-slate-800"></div>
                  </div>
                  <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100">
                    <div class="h-2 w-12 rounded bg-slate-200"></div>
                    <div class="mt-2 h-6 w-8 rounded bg-slate-800"></div>
                  </div>
                  <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100">
                    <div class="h-2 w-14 rounded bg-slate-200"></div>
                    <div class="mt-2 h-6 w-20 rounded bg-slate-800"></div>
                  </div>
                </div>
                <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100">
                  <div class="mb-3 flex justify-between">
                    <div class="h-3 w-36 rounded bg-slate-200"></div>
                    <div class="h-3 w-20 rounded bg-slate-100"></div>
                  </div>
                  <div class="flex h-24 items-end gap-1">
                    <?php for ($i = 0; $i < 12; $i++): ?>
                    <div class="flex-1 rounded-t bg-blue-500/30" style="height: <?= 20 + ($i % 5) * 12 ?>%"></div>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats strip (estilo agência IT) -->
  <section class="relative border-y border-white/5 bg-gradient-to-r from-slate-950 via-blue-950/80 to-slate-950 py-10">
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,transparent,rgba(59,130,246,0.08),transparent)]"></div>
    <div class="relative mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 sm:px-6 md:grid-cols-4 lg:px-8">
      <div class="stats-item text-center md:text-left" data-aos="fade-up">
        <div class="flex justify-center md:justify-start">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/20 text-blue-400 ring-1 ring-blue-400/30">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 01-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 01-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 01-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-3xl font-extrabold tracking-tight text-white">Cloud SaaS</p>
        <p class="mt-1 text-sm text-slate-400">Actualizações contínuas · sempre online</p>
      </div>
      <div class="stats-item text-center md:text-left" data-aos="fade-up" data-aos-delay="80">
        <div class="flex justify-center md:justify-start">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-400/30">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-3xl font-extrabold tracking-tight text-white">Multiempresa</p>
        <p class="mt-1 text-sm text-slate-400">Várias empresas · permissões claras</p>
      </div>
      <div class="stats-item text-center md:text-left" data-aos="fade-up" data-aos-delay="160">
        <div class="flex justify-center md:justify-start">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-violet-500/20 text-violet-300 ring-1 ring-violet-400/30">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-3xl font-extrabold tracking-tight text-white">Relatórios</p>
        <p class="mt-1 text-sm text-slate-400">Decisões com dados · exportação</p>
      </div>
      <div class="stats-item text-center md:text-left" data-aos="fade-up" data-aos-delay="240">
        <div class="flex justify-center md:justify-start">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-orange-500/20 text-orange-300 ring-1 ring-orange-400/30">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-3xl font-extrabold tracking-tight text-white">Segurança</p>
        <p class="mt-1 text-sm text-slate-400">Controlo de acesso · auditoria</p>
      </div>
    </div>
  </section>

  <!-- Sobre -->
  <section id="sobre" class="relative border-t border-slate-100 bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Sobre nós</p>
        <p class="section-title-bold mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.75rem] md:leading-tight">
          Lideramos o potencial<br class="hidden sm:inline"> da <span class="text-blue-600">tecnologia</span> para o seu negócio
        </p>
        <p class="mt-4 text-lg text-slate-600">
          A Sizo Technology cria soluções modernas que simplificam processos, unificam dados e aceleram a transformação digital das empresas em Moçambique e além.
        </p>
      </div>
      <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php
        $aboutCards = [
          ['title' => 'Segurança', 'desc' => 'Controlo de acesso, boas práticas e infraestrutura preparada para ambientes empresariais.', 'icon' => 'shield'],
          ['title' => 'Cloud SaaS', 'desc' => 'Acesso em qualquer lugar, atualizações contínuas e sem preocupações com servidores locais.', 'icon' => 'cloud'],
          ['title' => 'Multiempresa', 'desc' => 'Gestão centralizada de várias empresas com contexto e permissões bem definidas.', 'icon' => 'building'],
          ['title' => 'Facturação', 'desc' => 'Documentos comerciais e fluxos de venda alinhados à operação real do seu negócio.', 'icon' => 'file'],
          ['title' => 'Relatórios', 'desc' => 'Indicadores e relatórios para decisões rápidas e fundamentadas.', 'icon' => 'chart'],
          ['title' => 'Escalabilidade', 'desc' => 'Cresça equipas, filiais e volume de operações sem perder performance.', 'icon' => 'trend'],
        ];
        $delay = 0;
        foreach ($aboutCards as $card):
          $delay += 50;
        ?>
        <article class="feature-card rounded-3xl border border-slate-200/80 bg-slate-50/50 p-8" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/25">
            <?php if ($card['icon'] === 'shield'): ?>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <?php elseif ($card['icon'] === 'cloud'): ?>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
            <?php elseif ($card['icon'] === 'building'): ?>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <?php elseif ($card['icon'] === 'file'): ?>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <?php elseif ($card['icon'] === 'chart'): ?>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <?php else: ?>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <?php endif; ?>
          </div>
          <h3 class="mt-6 text-xl font-bold text-slate-900"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="mt-2 text-slate-600 leading-relaxed"><?= htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8') ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Funcionalidades -->
  <section id="funcionalidades" class="relative overflow-hidden border-t border-slate-100 bg-gradient-to-b from-slate-50 via-white to-slate-50 py-24 sm:py-32">
    <div class="pointer-events-none absolute -right-40 top-20 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-20 bottom-40 h-72 w-72 rounded-full bg-indigo-400/10 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">SizoTech ERP</p>
        <p class="section-title-bold mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.65rem] md:leading-tight">
          <?= htmlspecialchars($sizoFuncionalidades['lead']['titulo'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p class="mt-4 text-lg text-slate-600">
          <?= htmlspecialchars($sizoFuncionalidades['lead']['texto'], ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <?php
      $funcPreviewLimit = 3;
      ?>
      <div class="mt-16 grid grid-cols-1 gap-5 md:grid-cols-3 md:gap-6 lg:gap-8">
        <?php foreach ($sizoFuncionalidades['pilares'] as $pi => $pillar):
          $pillarItems = $pillar['itens'];
          $pillarPreview = array_slice($pillarItems, 0, $funcPreviewLimit);
          $pillarExtra = array_slice($pillarItems, $funcPreviewLimit);
          $pillarHasMore = count($pillarExtra) > 0;
          ?>
        <article class="feature-pillar group flex h-full min-h-[14rem] flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-md shadow-slate-900/[0.04] ring-1 ring-slate-900/[0.03] transition hover:shadow-lg hover:ring-blue-500/10 sm:min-h-[15rem]" data-aos="fade-up" data-aos-delay="<?= min($pi * 40, 200) ?>">
          <div class="relative flex h-28 shrink-0 items-center justify-center bg-gradient-to-br <?= htmlspecialchars($pillar['gradient'], ENT_QUOTES, 'UTF-8') ?> px-3">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_25%,rgba(255,255,255,0.22),transparent_60%)]"></div>
            <div class="relative flex h-full w-full max-w-[152px] items-center justify-center drop-shadow-[0_8px_28px_rgba(0,0,0,0.16)] [&_svg]:max-h-[80px] [&_svg]:w-auto">
              <?= sizo_feature_art($pillar['art']) ?>
            </div>
          </div>
          <div class="feature-pillar-body flex flex-1 flex-col px-4 pb-3 pt-3 sm:px-5 sm:pb-4 sm:pt-4">
            <div class="flex flex-wrap items-start gap-2 sm:gap-3">
              <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-blue-700 sm:px-3 sm:text-xs"><?= sprintf('%02d', $pi + 1) ?></span>
              <h3 class="text-sm font-bold leading-snug tracking-tight text-slate-900 sm:text-base"><?= htmlspecialchars($pillar['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
            </div>
            <?php if (!empty($pillar['subtitulo'])): ?>
            <p class="mt-1.5 text-[11px] font-medium leading-snug text-blue-600 sm:text-xs"><?= htmlspecialchars($pillar['subtitulo'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <ul class="feature-pillar-list mt-2 space-y-1.5 text-[11px] leading-snug text-slate-600 sm:mt-3 sm:space-y-2 sm:text-xs sm:leading-relaxed">
              <?php foreach ($pillarPreview as $bullet): ?>
              <li class="flex gap-2 sm:gap-2.5">
                <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 sm:h-5 sm:w-5">
                  <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span><?= htmlspecialchars($bullet, ENT_QUOTES, 'UTF-8') ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php if ($pillarHasMore): ?>
            <ul class="feature-pillar-extra mt-2 hidden space-y-2 border-t border-dashed border-slate-200 pt-2 text-[11px] leading-snug text-slate-600 sm:text-xs sm:leading-relaxed" id="feature-extra-<?= (int) $pi ?>">
              <?php foreach ($pillarExtra as $bullet): ?>
              <li class="flex gap-2 sm:gap-2.5">
                <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 sm:h-5 sm:w-5">
                  <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span><?= htmlspecialchars($bullet, ENT_QUOTES, 'UTF-8') ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
            <button type="button" class="feature-pillar-toggle mt-auto inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-200/90 bg-blue-50/90 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:border-blue-300 hover:bg-blue-100 sm:text-sm" data-expanded="false" aria-expanded="false" aria-controls="feature-extra-<?= (int) $pi ?>">
              <span class="feature-pillar-toggle-label">Mostrar tudo</span>
              <svg class="feature-pillar-toggle-icon h-3.5 w-3.5 shrink-0 transition-transform duration-200 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <?php else: ?>
            <div class="mt-auto flex min-h-[1rem] flex-1"></div>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="mx-auto mt-14 max-w-4xl rounded-2xl border border-slate-200 bg-slate-50/80 px-6 py-5 text-center text-sm leading-relaxed text-slate-600 shadow-inner sm:px-8" data-aos="fade-up">
        <p class="font-medium text-slate-700">Nota sobre disponibilidade</p>
        <p class="mt-2"><?= htmlspecialchars($sizoFuncionalidades['nota_transversal'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
    </div>
  </section>

  <!-- Planos -->
  <section id="planos" class="relative border-t border-slate-200 bg-white py-24 sm:py-32">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/40 to-transparent"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Planos &amp; licenciamento</p>
        <p class="section-title-bold mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.75rem] md:leading-tight">
          Um só produto,<br class="hidden sm:inline"> três formas de <span class="text-blue-600">crescer</span>
        </p>
        <p class="mt-4 text-lg text-slate-600">
          <strong class="font-semibold text-slate-800">LITE</strong> inclui <strong class="font-semibold text-slate-800">apenas um</strong> módulo — produtos, serviços em catálogo ou serviço livre (escolhe um).
          <strong class="font-semibold text-slate-800">STANDARD</strong> permite <strong class="font-semibold text-slate-800">dois</strong> módulos em simultâneo.
          <strong class="font-semibold text-slate-800">PRO</strong> desbloqueia <strong class="font-semibold text-slate-800">tudo</strong>.
        </p>
      </div>

      <div class="mx-auto mt-10 max-w-4xl rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-orange-50/50 px-5 py-4 text-center shadow-sm ring-1 ring-amber-100" data-aos="fade-up">
        <p class="text-sm font-semibold text-amber-900">
          <svg class="mr-1.5 inline-block h-4 w-4 -translate-y-px text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <?= htmlspecialchars($sizoNotaLicenca, ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="mt-14 grid gap-8 lg:grid-cols-3">
        <?php
        $pi = 0;
        foreach ($planos as $plan):
          $pi++;
          $isFeatured = !empty($plan['destaque']);
          $mailto = $sizoMailtoBase . '?subject=' . rawurlencode('Interesse no plano ' . $plan['nome'] . ' — Sizo Software');
          $badgeTipo = match ($plan['tipo']) {
              'PRO' => 'bg-violet-100 text-violet-800 ring-violet-200/80',
              'STANDARD' => 'bg-indigo-100 text-indigo-700 ring-indigo-200/80',
              default => 'bg-slate-100 text-slate-700 ring-slate-200/80',
          };
        ?>
        <article class="pricing-card group relative flex flex-col rounded-3xl border <?= $isFeatured ? 'border-violet-400/70 bg-gradient-to-b from-violet-50/90 to-white shadow-xl shadow-violet-600/15 ring-2 ring-violet-400/30' : 'border-slate-200 bg-white shadow-lg shadow-slate-900/5' ?> p-8 transition duration-300 hover:shadow-2xl" data-aos="fade-up" data-aos-delay="<?= min(($pi - 1) * 80, 240) ?>">
          <?php if ($isFeatured): ?>
          <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-violet-600 to-blue-600 px-4 py-1 text-xs font-bold uppercase tracking-wide text-white shadow-lg">Suite completa</span>
          <?php endif; ?>
          <div class="flex items-start justify-between gap-3">
            <div>
              <span class="inline-flex rounded-lg <?= $badgeTipo ?> px-2.5 py-1 text-xs font-bold uppercase tracking-wider ring-1"><?= htmlspecialchars($plan['tipo'], ENT_QUOTES, 'UTF-8') ?></span>
              <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900"><?= htmlspecialchars($plan['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="mt-1 text-sm font-medium text-blue-600"><?= htmlspecialchars($plan['titulo_card'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php if (!empty($plan['ativo'])): ?>
            <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">Disponível</span>
            <?php endif; ?>
          </div>
          <div class="mt-6 flex flex-wrap items-baseline gap-1">
            <span class="text-4xl font-extrabold tracking-tight text-slate-900"><?= htmlspecialchars($plan['preco_mt'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="text-lg font-semibold text-slate-500">MT</span>
            <span class="text-sm text-slate-400"><?= htmlspecialchars($plan['preco_periodo'] ?? '/ mês', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <p class="mt-4 text-sm leading-relaxed text-slate-600"><?= htmlspecialchars($plan['resumo'], ENT_QUOTES, 'UTF-8') ?></p>
          <ul class="mt-6 space-y-3 border-t border-slate-100 pt-6 text-sm text-slate-700">
            <?php foreach ($plan['bullets'] as $bullet): ?>
            <li class="flex gap-3">
              <span class="mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
              </span>
              <span><?= htmlspecialchars($bullet, ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <div class="mt-auto pt-8">
            <a href="<?= htmlspecialchars($mailto, ENT_QUOTES, 'UTF-8') ?>" class="flex w-full items-center justify-center gap-2 rounded-2xl <?= $isFeatured ? 'bg-gradient-to-r from-violet-600 to-blue-600 text-white shadow-lg shadow-violet-600/35 hover:from-violet-500 hover:to-blue-500' : 'bg-slate-900 text-white hover:bg-slate-800' ?> px-5 py-3.5 text-sm font-semibold transition">
              Falar connosco — <?= htmlspecialchars($plan['nome'], ENT_QUOTES, 'UTF-8') ?>
              <svg class="h-4 w-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <!-- Comparativo -->
      <div id="comparacao-planos" class="scroll-mt-28 pt-20" data-aos="fade-up">
        <div class="text-center">
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Comparar planos</p>
          <p class="section-title-bold mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
            Resumo lado a lado
          </p>
          <p class="mx-auto mt-3 max-w-2xl text-slate-600">
            Mesmos três eixos de produto em todos os níveis — o que muda é quantos pode activar ao mesmo tempo.
          </p>
        </div>

        <div class="mt-10 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5 ring-1 ring-slate-900/5">
          <table class="min-w-[720px] w-full border-collapse text-left text-sm">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/95">
                <th class="sticky left-0 z-10 min-w-[200px] bg-slate-50/95 px-4 py-4 font-semibold text-slate-900 backdrop-blur sm:px-6">Critério</th>
                <th class="px-4 py-4 font-bold text-slate-800 sm:px-6">LITE</th>
                <th class="px-4 py-4 font-bold text-indigo-700 sm:px-6">STANDARD</th>
                <th class="px-4 py-4 font-bold text-violet-700 sm:px-6">PRO</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($sizoComparacao as $row): ?>
              <tr class="<?= !empty($row['destaque']) ? 'bg-blue-50/50' : 'hover:bg-slate-50/80' ?>">
                <td class="sticky left-0 z-10 bg-white/95 px-4 py-4 font-medium text-slate-800 backdrop-blur sm:px-6 <?= !empty($row['destaque']) ? 'text-blue-900' : '' ?>">
                  <?= htmlspecialchars($row['criterio'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td class="px-4 py-4 text-slate-600 sm:px-6"><?= htmlspecialchars($row['lite'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="px-4 py-4 text-slate-600 sm:px-6"><?= htmlspecialchars($row['standard'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="px-4 py-4 text-slate-600 sm:px-6"><?= htmlspecialchars($row['pro'], ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p class="mx-auto mt-6 max-w-3xl text-center text-xs leading-relaxed text-slate-500">
          Informação meramente indicativa para apresentação comercial; contrato e condições finais são confirmados com a equipa Sizo Technology.
        </p>
      </div>

      <p class="mx-auto mt-14 max-w-2xl text-center text-sm text-slate-600" data-aos="fade-up">
        Dúvidas sobre qual módulo escolher ou precisa de condições especiais?
        <a href="<?= htmlspecialchars($sizoMailtoBase . '?subject=' . rawurlencode('Consulta — planos Sizo Software'), ENT_QUOTES, 'UTF-8') ?>" class="font-semibold text-blue-600 underline decoration-blue-600/30 underline-offset-2 hover:text-blue-800">Escreva-nos</a>
        ou <a href="#cta" class="font-semibold text-blue-600 underline decoration-blue-600/30 underline-offset-2 hover:text-blue-800">veja os contactos</a>.
      </p>
    </div>
  </section>

  <!-- Processo -->
  <section id="processo" class="relative border-t border-slate-100 bg-slate-50 py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Como trabalhamos</p>
        <p class="section-title-bold mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.75rem] md:leading-tight">
          Processo simples,<br class="hidden sm:inline"> resultados <span class="text-blue-600">mensuráveis</span>
        </p>
      </div>
      <div class="mt-16 grid gap-10 md:grid-cols-3">
        <div class="process-card text-center md:text-left" data-aos="fade-up">
          <span class="process-step-num mx-auto md:mx-0">01</span>
          <h3 class="mt-6 text-xl font-bold text-slate-900">Diagnóstico</h3>
          <p class="mt-3 text-slate-600 leading-relaxed">Entendemos processos, equipas e objectivos para configurar o Sizo Software ao seu contexto.</p>
        </div>
        <div class="process-card text-center md:text-left" data-aos="fade-up" data-aos-delay="100">
          <span class="process-step-num mx-auto md:mx-0">02</span>
          <h3 class="mt-6 text-xl font-bold text-slate-900">Implementação</h3>
          <p class="mt-3 text-slate-600 leading-relaxed">Onboarding planeado: dados, utilizadores, permissões e integração com a operação do dia-a-dia.</p>
        </div>
        <div class="process-card text-center md:text-left" data-aos="fade-up" data-aos-delay="200">
          <span class="process-step-num mx-auto md:mx-0">03</span>
          <h3 class="mt-6 text-xl font-bold text-slate-900">Escala &amp; suporte</h3>
          <p class="mt-3 text-slate-600 leading-relaxed">Formação contínua, relatórios e evolução do plano à medida que a empresa cresce.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Screenshots -->
  <section id="screenshots" class="relative overflow-hidden border-t border-slate-100 bg-white py-24 sm:py-32">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(37,99,235,0.06),_transparent_50%)]"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Portefólio</p>
        <p class="section-title-bold mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.75rem] md:leading-tight">
          Últimos <span class="text-blue-600">ecrãs</span> da plataforma
        </p>
        <p class="mt-4 text-lg text-slate-600">
          Interface limpa e profissional — do dashboard aos relatórios e configurações.
        </p>
      </div>
      <div class="mt-16 grid gap-8 sm:grid-cols-2">
        <?php
        $shots = [
          ['file' => 'dashboard.png', 'title' => 'Dashboard', 'desc' => 'Visão geral de vendas, documentos e operações.'],
          ['file' => 'relatorios.png', 'title' => 'Relatórios', 'desc' => 'Relatórios financeiros, stock e comerciais.'],
          ['file' => 'produtos.png', 'title' => 'Produtos', 'desc' => 'Catálogo, categorias e gestão de inventário.'],
          ['file' => 'configuracoes.png', 'title' => 'Configurações', 'desc' => 'Empresa, preferências e activações do sistema.'],
        ];
        $sd = 0;
        foreach ($shots as $shot):
          $sd += 80;
          $path = __DIR__ . '/assets/screenshots/' . $shot['file'];
          $src = 'assets/screenshots/' . rawurlencode($shot['file']);
          $exists = is_file($path);
        ?>
        <figure class="screenshot-card group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-slate-100 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5" data-aos="zoom-in-up" data-aos-delay="<?= min($sd, 250) ?>">
          <div class="aspect-[16/10] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
            <?php if ($exists): ?>
            <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($shot['title'] . ' — Sizo Software', ENT_QUOTES, 'UTF-8') ?>" class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.03]" loading="lazy" width="1280" height="800">
            <?php else: ?>
            <div class="flex h-full min-h-[200px] flex-col items-center justify-center gap-3 p-8 text-center">
              <span class="rounded-2xl bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm"><?= htmlspecialchars($shot['title'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="max-w-xs text-sm text-slate-500">Coloque <code class="rounded bg-white px-1.5 py-0.5 text-xs"><?= htmlspecialchars($shot['file'], ENT_QUOTES, 'UTF-8') ?></code> em <code class="rounded bg-white px-1.5 py-0.5 text-xs">assets/screenshots/</code></span>
            </div>
            <?php endif; ?>
          </div>
          <figcaption class="border-t border-slate-200 bg-white px-6 py-5">
            <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($shot['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="mt-1 text-sm text-slate-600"><?= htmlspecialchars($shot['desc'], ENT_QUOTES, 'UTF-8') ?></p>
          </figcaption>
        </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="scroll-mt-28 border-t border-slate-100 bg-slate-50 py-24 sm:py-32">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="text-center" data-aos="fade-up">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Ajuda</p>
        <p class="section-title-bold mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.75rem] md:leading-tight">
          Perguntas <span class="text-blue-600">frequentes</span>
        </p>
        <p class="mt-4 text-lg text-slate-600">
          Respostas rápidas sobre o Sizo Software, planos e licenciamento.
        </p>
      </div>
      <div class="mt-12 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5 md:items-start">
        <?php foreach ($sizoFaq as $fi => $item): ?>
        <details class="faq-item h-full rounded-2xl border border-slate-200/90 bg-white shadow-sm transition hover:border-slate-300 hover:shadow-md" data-aos="fade-up" data-aos-delay="<?= min($fi * 35, 210) ?>">
          <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 text-left sm:px-6 sm:py-5">
            <span class="pr-2 text-base font-semibold text-slate-900"><?= htmlspecialchars($item['pergunta'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="faq-chevron flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 ring-1 ring-slate-200/80">
              <svg class="h-5 w-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
          </summary>
          <div class="border-t border-slate-100 px-5 pb-5 pt-0 text-slate-600 sm:px-6">
            <p class="pt-4 text-sm leading-relaxed sm:text-base"><?= htmlspecialchars($item['resposta'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
      <p class="mt-10 text-center text-sm text-slate-500" data-aos="fade-up">
        Não encontrou o que procura?
        <a href="<?= htmlspecialchars($sizoMailtoBase . '?subject=' . rawurlencode('Dúvida — FAQ Sizo Software'), ENT_QUOTES, 'UTF-8') ?>" class="font-semibold text-blue-600 underline decoration-blue-600/30 underline-offset-2 hover:text-blue-800">Envie-nos uma mensagem</a>
        ou <a href="#cta" class="font-semibold text-blue-600 underline decoration-blue-600/30 underline-offset-2 hover:text-blue-800">fale connosco</a>.
      </p>
    </div>
  </section>

  <!-- CTA -->
  <section id="cta" class="relative py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="cta-gradient relative overflow-hidden rounded-[2rem] px-8 py-16 text-center shadow-2xl shadow-blue-900/40 sm:px-16 sm:py-24" data-aos="fade-up">
        <div class="pointer-events-none absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-60"></div>
        <div class="relative">
          <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
            Modernize a gestão da sua empresa.
          </h2>
          <p class="mx-auto mt-6 max-w-2xl text-lg text-blue-100">
            Simplifique processos, controle operações e impulsione o crescimento da sua empresa com o Sizo Software.
          </p>
          <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="<?= htmlspecialchars($sizoMailtoBase . '?subject=' . rawurlencode('Demonstração — Sizo Software'), ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-full bg-white px-8 py-4 text-base font-semibold text-blue-700 shadow-xl transition hover:bg-blue-50">
              Solicitar demonstração
            </a>
          </div>
          <div class="mt-10 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 border-t border-white/15 pt-10 text-sm">
            <a href="<?= htmlspecialchars($sizoMailtoBase, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 text-blue-100 transition hover:text-white">
              <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <?= htmlspecialchars($sizoContacto['email'], ENT_QUOTES, 'UTF-8') ?>
            </a>
            <a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 text-blue-100 transition hover:text-white" target="_blank" rel="noopener noreferrer">
              <svg class="h-5 w-5 shrink-0 text-emerald-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.883 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              <?= htmlspecialchars($sizoContacto['telefone_display'], ENT_QUOTES, 'UTF-8') ?> · WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Footer -->
<footer class="border-t border-slate-200 bg-slate-950 text-slate-400">
  <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 gap-10 sm:grid-cols-4">
        <div class="col-span-2 max-w-md sm:col-span-2">
        <a href="#" class="inline-flex items-center rounded-xl bg-white p-2 ring-1 ring-white/10" aria-label="Sizo TECH">
          <img src="assets/img/logo.png" alt="Sizo TECH — Smart IT Solutions" class="h-8 w-auto max-w-[200px] object-contain sm:h-9" width="200" height="48" />
        </a>
        <p class="mt-4 text-sm leading-relaxed">
          Smart IT solutions para empresas que exigem controlo, clareza e crescimento.
        </p>
        <div class="mt-6 space-y-2 text-sm">
          <a href="<?= htmlspecialchars($sizoMailtoBase, ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2 text-slate-300 transition hover:text-white">
            <svg class="h-4 w-4 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <?= htmlspecialchars($sizoContacto['email'], ENT_QUOTES, 'UTF-8') ?>
          </a>
          <a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2 text-slate-300 transition hover:text-white" target="_blank" rel="noopener noreferrer">
            <svg class="h-4 w-4 shrink-0 text-emerald-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.883 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <?= htmlspecialchars($sizoContacto['telefone_display'], ENT_QUOTES, 'UTF-8') ?> · WhatsApp
          </a>
        </div>
        </div>
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Produto</h3>
          <ul class="mt-4 space-y-3 text-sm">
            <li><a href="#funcionalidades" class="transition hover:text-white">Serviços</a></li>
            <li><a href="#planos" class="transition hover:text-white">Planos</a></li>
            <li><a href="#comparacao-planos" class="transition hover:text-white">Comparar planos</a></li>
            <li><a href="#screenshots" class="transition hover:text-white">Plataforma</a></li>
            <li><a href="#faq" class="transition hover:text-white">FAQ</a></li>
            <li><a href="#cta" class="transition hover:text-white">Demonstração</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Empresa</h3>
          <ul class="mt-4 space-y-3 text-sm">
            <li><a href="#sobre" class="transition hover:text-white">Sobre</a></li>
            <li><a href="#processo" class="transition hover:text-white">Processo</a></li>
            <li><a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-white" target="_blank" rel="noopener noreferrer">Contacto</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Legal</h3>
          <ul class="mt-4 space-y-3 text-sm">
            <li><a href="#" class="transition hover:text-white">Privacidade</a></li>
            <li><a href="#" class="transition hover:text-white">Termos</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-sm sm:flex-row">
      <p>© <span id="footer-year"></span> Sizo Technology · Sizotech Limitada</p>
      <p class="text-slate-500">Tecnologia inteligente para empresas modernas.</p>
      </div>
    </div>
</footer>

<?php require __DIR__ . '/includes/footer.php'; ?>
