<?php
require_once __DIR__ . '/config/https.php';
sizo_force_canonical_https();

session_start();
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$pageTitle = 'Sizo Software | Gestão empresarial simples';
$pageDesc = 'Software simples, funcional e adaptável para gerir a sua empresa.';
$sizoCfg = require __DIR__ . '/config/planos.php';
$sizoContacto = $sizoCfg['contacto'];
$sizoMailtoBase = 'mailto:' . rawurlencode($sizoContacto['email']);
$sizoWhatsAppUrl = $sizoContacto['whatsapp_url'];
$startMailto = $sizoMailtoBase . '?subject=' . rawurlencode('Iniciar gratuitamente - Sizo Software');
$_SESSION['signup_csrf'] = bin2hex(random_bytes(32));

require __DIR__ . '/includes/head.php';
?>

<header id="site-nav" class="site-nav fixed top-0 left-0 right-0 z-50 w-full">
  <div class="relative flex w-full items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8 xl:px-10">
    <a href="#inicio" class="flex shrink-0 items-center" aria-label="Sizo Software - início"><img src="assets/img/LOGO%20Sizotech.png" alt="Sizo Tech — Smart IT Solutions" class="h-10 w-auto object-contain sm:h-12" width="180" height="101"></a>
    <nav class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-8 md:flex" aria-label="Principal">
      <a href="#sobre" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Sobre</a>
      <a href="#funcionalidades" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Funcionalidades</a>
      <a href="#planos" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Planos</a>
    </nav>
    <div class="flex items-center gap-3">
      <button type="button" data-open-plan-picker class="hidden rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 md:inline-flex">Inicie gratuitamente</button>
      <button type="button" id="mobile-menu-btn" class="inline-flex rounded-lg border border-slate-200 p-2 text-slate-700 md:hidden" aria-expanded="false" aria-controls="mobile-nav" aria-label="Abrir menu"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    </div>
  </div>
  <div id="mobile-nav" class="hidden border-t border-slate-100 bg-white px-4 py-4 md:hidden"><nav class="flex flex-col gap-3" aria-label="Principal móvel"><a href="#sobre" class="text-sm font-medium text-slate-700">Sobre</a><a href="#funcionalidades" class="text-sm font-medium text-slate-700">Funcionalidades</a><a href="#planos" class="text-sm font-medium text-slate-700">Planos</a></nav></div>
</header>

<main>
  <section id="inicio" class="hero-surface relative isolate min-h-[650px] scroll-mt-24 overflow-hidden pt-32 pb-16 sm:min-h-[700px] sm:pt-40 lg:min-h-[760px] lg:pt-48">
    <img src="assets/img/hero.png" alt="Sizo Software em computador, tablet e telemóvel" class="pointer-events-none absolute inset-0 z-0 h-full w-full object-cover object-[68%_center]" width="1672" height="941" fetchpriority="high">
    <div class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-r from-white/95 via-white/55 to-transparent lg:from-white/88 lg:via-white/30" aria-hidden="true"></div>
    <div class="relative z-20 mx-auto max-w-6xl px-5 sm:px-8 lg:px-8"><div class="max-w-xl" data-aos="fade-up">
      <h1 class="text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl lg:text-[3.45rem] lg:leading-[1.08]">Sizo Software</h1>
      <p class="mt-6 max-w-lg text-base font-medium leading-relaxed text-slate-700 sm:text-lg">Gerencie a sua empresa com maior facilidade usando um software <span class="font-bold text-brand">simples</span>, <span class="font-bold text-brand">funcional</span> e que <span class="font-bold text-brand">se adapta à sua empresa</span>.</p>
      <div class="mt-8"><button type="button" data-open-plan-picker class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:bg-slate-800">Inicie gratuitamente</button></div>
      <a href="#planos" class="group mt-8 inline-flex items-center gap-3 text-sm font-bold text-slate-950 transition hover:text-brand"><span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-950 text-white transition group-hover:bg-brand" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></span>Veja preços e planos</a>
    </div></div>
  </section>

  <section id="sobre" class="scroll-mt-24 overflow-hidden border-y border-slate-100 bg-white py-20 sm:py-28">
    <div class="mx-auto max-w-6xl px-5 sm:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <span class="inline-flex rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-600">Sobre o Sizotech</span>
        <h2 class="mt-7 text-3xl font-bold tracking-tight text-slate-950 sm:text-5xl">Tecnologia para simplificar <span class="text-brand">a gestão do seu negócio.</span></h2>
        <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-slate-600 sm:text-lg">O Sizotech é uma plataforma de gestão empresarial criada para centralizar operações, reduzir tarefas manuais e dar às empresas uma visão mais simples e organizada do seu negócio.</p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row"><a href="#funcionalidades" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Conhecer a plataforma</a><a href="#planos" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Ver planos</a></div>
      </div>
      <div class="mt-14 overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-card" data-aos="fade-up"><div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div class="flex gap-2"><i class="h-2.5 w-2.5 rounded-full bg-slate-200"></i><i class="h-2.5 w-2.5 rounded-full bg-slate-200"></i><i class="h-2.5 w-2.5 rounded-full bg-slate-200"></i></div><span class="text-xs font-medium text-slate-400">sizotech</span><i class="w-12"></i></div><img src="assets/img/1%20-%20dashboard.png" alt="Dashboard do Sizotech" class="block w-full" loading="lazy"></div>
    </div>
  </section>

  <section id="funcionalidades" class="scroll-mt-24 bg-slate-50 py-20 sm:py-28">
    <div class="mx-auto max-w-6xl px-5 sm:px-8">
      <div class="grid gap-10 lg:grid-cols-[.85fr_1.15fr] lg:gap-20"><div data-aos="fade-up"><p class="text-sm font-semibold text-brand">O que fazemos</p><h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Tudo o que precisa para gerir melhor, num só lugar.</h2></div><div class="text-base leading-relaxed text-slate-600 sm:text-lg" data-aos="fade-up"><p>Gerir uma empresa envolve diferentes áreas, documentos e processos. O Sizotech centraliza essas actividades numa única plataforma, permitindo acompanhar o negócio com mais clareza e menos complexidade.</p><p class="mt-5">Da emissão de uma cotação ao controlo de stock, pagamentos, despesas, clientes ou relatórios, toda a informação permanece organizada e acessível num ambiente único.</p></div></div>
      <div class="mt-16 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <?php $aboutFeatures = [['Facturação e vendas','Crie facturas, cotações, vendas a dinheiro e recibos de forma organizada.'],['Clientes e contactos','Mantenha clientes, fornecedores e outros contactos sempre disponíveis.'],['Produtos e stock','Organize produtos, serviços, categorias e movimentos de stock.'],['Caixa e despesas','Acompanhe movimentos de caixa e operações financeiras diárias.'],['Relatórios','Consulte informação consolidada para acompanhar resultados.'],['Automações','Reduza tarefas repetitivas na rotina administrativa.']]; foreach ($aboutFeatures as $feature): ?>
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:shadow-card" data-aos="fade-up"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-950 text-lg font-bold text-white">✓</span><h3 class="mt-6 text-lg font-bold text-slate-900"><?= htmlspecialchars($feature[0], ENT_QUOTES, 'UTF-8') ?></h3><p class="mt-3 text-sm leading-6 text-slate-600"><?= htmlspecialchars($feature[1], ENT_QUOTES, 'UTF-8') ?></p></article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="empresas" class="scroll-mt-24 border-y border-slate-100 bg-white py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up"><span class="text-sm font-semibold text-brand">Empresas na plataforma</span><h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Empresas que já utilizam o Sizotech</h2><p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">Empresas utilizam o Sizotech para simplificar processos, organizar operações e acompanhar a gestão do seu negócio.</p></div>
      <p id="companies-loading" class="mt-12 text-center text-sm text-slate-500">A carregar empresas…</p><div id="companies-list" class="hidden mx-auto mt-14 grid max-w-5xl gap-3 sm:grid-cols-2 lg:grid-cols-4"></div>
      <div id="companies-count-wrap" class="hidden mt-12 flex justify-center" data-aos="fade-up"><div class="inline-flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-brand shadow-sm"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 13h.01M9 17h.01"/></svg></span><div class="text-left"><div id="companies-count" class="text-lg font-semibold tracking-tight text-slate-950"></div><div class="mt-0.5 text-sm text-slate-500">já fazem parte do Sizotech</div></div></div></div>
      <p id="companies-note" class="hidden mx-auto mt-10 max-w-2xl text-center text-sm leading-6 text-slate-500">Uma plataforma criada para acompanhar empresas em diferentes fases do seu crescimento.</p>
    </div>
  </section>

  <section id="planos" class="section-band section-band--white scroll-mt-24 py-20 sm:py-28">
    <div class="mx-auto max-w-6xl px-5 sm:px-8">
      <div class="mx-auto max-w-2xl text-center" data-aos="fade-up"><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Planos</p><h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Escolha o plano ideal para si</h2><p class="mt-4 text-base leading-relaxed text-slate-600">Todos os planos incluem o sistema completo. Escolha de acordo com o ritmo da sua empresa.</p><button type="button" id="choose-free-plan" disabled class="mt-6 inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-transparent px-5 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-200 hover:text-emerald-950 disabled:cursor-wait disabled:opacity-60">Inicie agora, é grátis</button></div>
      <div id="billing-cycle-picker" class="mx-auto mt-8 flex w-fit max-w-full flex-wrap justify-center gap-1 rounded-xl border border-slate-200 bg-slate-50 p-1.5" aria-label="Periodicidade de faturação">
        <button type="button" data-billing-cycle="monthly" aria-pressed="true" class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm">Mensal</button>
        <button type="button" data-billing-cycle="quarterly" aria-pressed="false" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-slate-950">Trimestral</button>
        <button type="button" data-billing-cycle="semiannual" aria-pressed="false" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-slate-950">Semestral</button>
        <button type="button" data-billing-cycle="yearly" aria-pressed="false" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-slate-950">Anual</button>
      </div>
      <p id="plans-loading" class="mx-auto mt-10 max-w-xl text-center text-sm text-slate-500">A carregar planos…</p>
      <p id="plans-error" class="hidden mx-auto mt-10 max-w-xl rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-center text-sm text-amber-800"></p>
      <div id="plans-list" class="mt-14 grid gap-5 md:grid-cols-3"></div>
    </div>
  </section>
</main>

<div id="plan-picker-modal" class="signup-modal hidden fixed inset-0 z-[65] items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="plan-picker-title">
  <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl"><div class="flex items-start justify-between border-b border-slate-100 px-6 py-5"><div><p class="text-sm font-semibold text-brand">Comece agora</p><h2 id="plan-picker-title" class="mt-1 text-xl font-bold text-slate-950">Escolha o plano ideal para si</h2></div><button type="button" data-close-plan-picker class="rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Fechar">✕</button></div><div id="plan-picker-loading" class="p-10 text-center text-sm text-slate-500">A carregar planos…</div><div id="plan-picker-list" class="hidden grid gap-5 p-6 md:grid-cols-3"></div></div>
</div>

<div id="signup-modal" class="signup-modal hidden fixed inset-0 z-[70] overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="signup-title">
  <div class="mx-auto my-6 w-full max-w-2xl self-start rounded-2xl bg-white shadow-2xl sm:my-8">
    <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5"><div><p id="signup-subscription-label" class="text-sm font-semibold text-brand">Subscrição</p><h2 id="signup-title" class="mt-1 text-xl font-bold text-slate-950">Crie a sua empresa</h2><p id="signup-test-mode-notice" class="hidden mt-1 text-xs text-orange-700">Cadastro em modo de teste neste navegador.</p></div><button type="button" data-signup-close class="rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Fechar">✕</button></div>
    <form id="signup-form" class="p-6 sm:p-8" novalidate autocomplete="off">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['signup_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="plan_code">
      <div class="mb-7"><div class="flex items-center gap-2 text-xs font-semibold"><span class="signup-dot flex h-7 w-7 items-center justify-center rounded-full bg-slate-950 text-white" data-step-dot="1">1</span><i class="h-px flex-1 bg-slate-200"></i><span class="signup-dot flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-slate-500" data-step-dot="2">2</span><i class="h-px flex-1 bg-slate-200"></i><span class="signup-dot flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-slate-500" data-step-dot="3">3</span></div></div>
      <div data-step="1" class="signup-step grid gap-4 sm:grid-cols-2"><p class="sm:col-span-2 text-sm text-slate-600">Dados principais da empresa.</p><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Nome da empresa <span class="text-red-600" aria-hidden="true">*</span><input required name="name" maxlength="150" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Tipo jurídico <span class="text-red-600" aria-hidden="true">*</span><select name="company_type" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="">A carregar tipos…</option></select><span class="field-error text-xs text-red-600"></span></label><label id="company-type-other-wrap" class="hidden text-sm font-semibold text-slate-700">Outro tipo jurídico <span class="text-red-600" aria-hidden="true">*</span><input name="company_type_other" maxlength="120" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><div class="sm:col-span-2"><div class="flex items-center justify-between gap-4"><span class="text-sm font-semibold text-slate-700">Mostrar designação jurídica no nome</span><label class="relative inline-flex shrink-0 cursor-pointer items-center"><input type="checkbox" name="show_legal_designation" id="company-show-legal-designation" value="1" checked class="peer sr-only"><span class="h-6 w-11 rounded-full bg-slate-200 transition after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand peer-checked:after:translate-x-full peer-focus:ring-4 peer-focus:ring-blue-100"></span></label></div><div id="company-name-preview-wrap" class="mt-3 hidden rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"><p id="company-name-preview" class="min-h-5 break-words text-sm font-medium text-blue-900"></p></div></div><label class="text-sm font-semibold text-slate-700">NUIT <span class="text-red-600" aria-hidden="true">*</span><input name="nuit" inputmode="numeric" maxlength="9" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="sm:col-span-2 text-sm font-semibold text-slate-700">E-mail <span class="text-red-600" aria-hidden="true">*</span><input required type="email" name="email" maxlength="150" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label></div>
      <div data-step="2" class="signup-step hidden grid gap-4 sm:grid-cols-2"><p class="sm:col-span-2 text-sm text-slate-600">Contactos, morada e acesso.</p><label class="text-sm font-semibold text-slate-700">Telefone<input name="phone" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Telefone alternativo<input name="phone_alt" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">País <span class="text-red-600" aria-hidden="true">*</span><select name="address_country" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="MZ" selected>Moçambique</option><option value="ZA">África do Sul</option><option value="ZW">Zimbabué</option><option value="SZ">Essuatíni</option><option value="LS">Lesoto</option><option value="BW">Botsuana</option><option value="NA">Namíbia</option><option value="AO">Angola</option><option value="ZM">Zâmbia</option><option value="MW">Malawi</option><option value="TZ">Tanzânia</option><option value="KE">Quénia</option><option value="PT">Portugal</option><option value="BR">Brasil</option><option value="CN">China</option><option value="IN">Índia</option><option value="US">Estados Unidos</option><option value="GB">Reino Unido</option><option value="DE">Alemanha</option><option value="FR">França</option><option value="OTHER">Outro…</option></select><span class="field-error text-xs text-red-600"></span></label><label id="mozambique-province-wrap" class="text-sm font-semibold text-slate-700">Província <span class="text-red-600" aria-hidden="true">*</span><select name="address_province_mz" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="Cidade de Maputo" selected>Cidade de Maputo</option><option value="Maputo">Maputo</option><option value="Gaza">Gaza</option><option value="Inhambane">Inhambane</option><option value="Sofala">Sofala</option><option value="Manica">Manica</option><option value="Tete">Tete</option><option value="Zambézia">Zambézia</option><option value="Nampula">Nampula</option><option value="Niassa">Niassa</option><option value="Cabo Delgado">Cabo Delgado</option></select><span class="field-error text-xs text-red-600"></span></label><label id="foreign-province-wrap" class="hidden text-sm font-semibold text-slate-700">Província ou cidade <span class="text-red-600" aria-hidden="true">*</span><input name="address_province_text" maxlength="160" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><input type="hidden" name="address_province" value="Cidade de Maputo"><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Rua / avenida<input name="address_street" maxlength="500" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Bairro<input name="address_neighborhood" maxlength="200" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Número<input name="address_house_number" maxlength="60" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Endereço da empresa <span class="text-red-600" aria-hidden="true">*</span><span class="mt-1.5 flex"><input name="subdomain" spellcheck="false" class="min-w-0 flex-1 rounded-l-lg border border-slate-300 px-3 py-2.5 font-normal"><span id="subdomain-suffix" class="rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-600">.sizotech.net</span></span><span id="subdomain-availability" class="mt-1 block text-xs"></span><span class="field-error text-xs text-red-600"></span></label></div>
      <div data-step="3" class="signup-step hidden grid gap-4">
        <div id="signup-plan-summary" class="rounded-xl bg-brand-soft px-4 py-3 text-sm text-slate-700" aria-label="Plano escolhido, obrigatório"></div>
        <label class="text-sm font-semibold text-slate-700">Área de atividade<input name="business_area" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        <label id="signup-billing-cycle-wrap" class="text-sm font-semibold text-slate-700">Ciclo de faturação<select name="billing_cycle" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></select></label>
        <div id="signup-plan-total" class="hidden w-fit rounded-xl bg-brand-soft px-4 py-3 text-sm text-slate-700" aria-live="polite"></div>
      </div>
      <p id="signup-message" class="hidden mt-5 rounded-lg px-4 py-3 text-sm"></p><div class="mt-7 flex justify-between gap-3"><button type="button" id="signup-back" class="hidden inline-flex items-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100" aria-label="Voltar"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg><span>Voltar</span></button><span id="signup-spacer"></span><button type="button" id="signup-next" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Continuar</button><button type="button" id="signup-submit" class="hidden rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Concluir cadastro</button></div>
    </form>
  </div>
</div>

<div id="email-verification-modal" class="signup-modal hidden fixed inset-0 z-[75] items-center justify-center bg-slate-950/50 p-6 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="email-verification-title">
  <div class="w-full max-w-[500px] rounded-[18px] bg-white px-8 py-8 shadow-xl">
    <div class="flex items-start justify-between gap-6">
      <div>
        <h2 id="email-verification-title" class="text-[20px] font-semibold tracking-[-0.02em] text-slate-950">Verificação do e-mail</h2>
        <p id="email-verification-subtitle" class="mt-2 text-[13px] leading-6 text-slate-500">Estamos a preparar a verificação do seu endereço de e-mail.</p>
      </div>
      <button type="button" data-close-email-verification aria-label="Fechar" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-50 hover:text-slate-700">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>

    <div id="email-verification-sending" class="mt-9 hidden state-enter">
      <h3 class="text-[14px] font-medium text-slate-900">A enviar o código de verificação</h3>
      <p class="mt-1.5 text-[12px] leading-5 text-slate-400">
        A enviar para <span id="email-verification-sending-mask">o seu e-mail</span>
        <span class="signup-animated-dots" aria-hidden="true"><span>.</span><span>.</span><span>.</span></span>
      </p>
      <div class="email-sending-bar mt-6 h-[3px] w-full rounded-full bg-slate-100"></div>
    </div>

    <div id="email-verification-send-error" class="mt-9 hidden state-enter">
      <h3 class="text-[14px] font-medium text-slate-900">Não foi possível enviar o código</h3>
      <p id="email-verification-send-error-text" class="mt-1.5 text-[12px] leading-5 text-red-500">Não foi possível enviar o código de verificação. Tente novamente mais tarde.</p>
      <div class="mt-6 flex justify-end gap-2">
        <button type="button" data-close-email-verification class="inline-flex h-9 items-center justify-center rounded-lg px-4 text-[12px] font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-800">Fechar</button>
        <button type="button" id="email-verification-retry" class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-900 px-5 text-[12px] font-medium text-white transition hover:bg-slate-800">Tentar novamente</button>
      </div>
    </div>

    <div id="email-verification-code" class="mt-8 hidden state-enter">
      <p class="text-[13px] leading-6 text-slate-500">
        Enviámos um código de 4 dígitos para
        <strong id="maskedEmail" class="font-semibold text-slate-700">o seu e-mail</strong>.
        Introduza-o para continuar.
      </p>

      <form id="email-verification-form" class="mt-7" autocomplete="off">
        <label class="block text-[12px] font-medium text-slate-700">Código de verificação</label>
        <div id="otpContainer" class="mt-3 flex items-center gap-2.5" role="group" aria-label="Código de verificação">
          <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Primeiro dígito" class="otp-input h-[48px] w-[48px] rounded-[10px] border border-slate-200 text-center text-[18px] font-semibold text-slate-900">
          <input type="text" inputmode="numeric" maxlength="1" aria-label="Segundo dígito" class="otp-input h-[48px] w-[48px] rounded-[10px] border border-slate-200 text-center text-[18px] font-semibold text-slate-900">
          <input type="text" inputmode="numeric" maxlength="1" aria-label="Terceiro dígito" class="otp-input h-[48px] w-[48px] rounded-[10px] border border-slate-200 text-center text-[18px] font-semibold text-slate-900">
          <input type="text" inputmode="numeric" maxlength="1" aria-label="Quarto dígito" class="otp-input h-[48px] w-[48px] rounded-[10px] border border-slate-200 text-center text-[18px] font-semibold text-slate-900">
        </div>

        <p id="email-verification-error" class="hidden mt-3 text-[11px] font-medium leading-5 text-red-500">O código introduzido é inválido ou expirou.</p>

        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
          <span class="text-[11px] text-slate-400">O código é válido por 24 horas.</span>
          <span id="resendCountdown" class="text-[11px] font-medium text-slate-400">Reenviar em 30s</span>
          <button id="resendButton" type="button" class="hidden text-[11px] font-semibold text-slate-700 transition hover:text-slate-950">Reenviar código</button>
        </div>

        <div id="resendMessage" class="hidden mt-5 rounded-lg bg-amber-50 px-4 py-3 text-[11px] leading-5 text-amber-800 ring-1 ring-inset ring-amber-200">
          Enviámos um novo código de verificação para o seu e-mail.
        </div>

        <div class="mt-7 flex justify-end gap-2">
          <button type="button" data-close-email-verification class="inline-flex h-9 items-center justify-center rounded-lg px-4 text-[12px] font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-800">Fechar</button>
          <button id="confirmButton" type="submit" disabled class="inline-flex h-9 min-w-[126px] items-center justify-center rounded-lg bg-slate-900 px-5 text-[12px] font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400">Confirmar código</button>
        </div>
      </form>
    </div>

    <div id="email-verification-verified" class="mt-8 hidden state-enter">
      <div class="rounded-lg bg-emerald-50 px-4 py-3 text-[12px] leading-5 text-emerald-700">O endereço de e-mail foi verificado com sucesso.</div>
    </div>
  </div>
</div>

<div id="signup-progress-modal" class="signup-modal hidden fixed inset-0 z-[80] items-center justify-center bg-slate-950/80 p-6 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="signup-progress-title">
  <div class="w-full max-w-[500px] rounded-[18px] bg-white px-8 py-8 shadow-xl">
    <div class="flex items-start justify-between gap-6">
      <div>
        <h2 id="signup-progress-title" class="text-[20px] font-semibold tracking-[-0.02em] text-slate-950">A preparar a sua subscrição</h2>
        <p id="signup-progress-subtitle" class="mt-2 text-[13px] leading-6 text-slate-500">Aguarde enquanto concluímos esta etapa.</p>
      </div>
      <button type="button" data-close-progress aria-label="Fechar" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-slate-400 transition duration-150 hover:bg-slate-50 hover:text-slate-700">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
    <div id="signup-progress-process" class="mt-9">
      <div class="h-[3px] w-full overflow-hidden rounded-full bg-slate-100">
        <div id="signup-progress-bar" class="signup-progress-fill h-full rounded-full bg-slate-900" style="width: 14%"></div>
      </div>
      <div class="mt-3 flex items-center justify-between">
        <span id="signup-progress-counter" class="text-[11px] font-medium text-slate-400">Etapa 1 de 7</span>
        <span id="signup-progress-percent" class="text-[11px] font-medium text-slate-400">14%</span>
      </div>
      <div id="signup-progress-step-box" class="signup-step-enter mt-8 min-h-[110px]">
        <h3 id="signup-progress-step-title" class="text-[14px] font-medium tracking-[-0.01em] text-slate-900">A validar os dados</h3>
        <p id="signup-progress-normal" class="mt-1.5 text-[12px] leading-5 text-slate-400">
          <span id="signup-progress-step-desc">A verificar as informações fornecidas</span>
          <span class="signup-animated-dots" aria-hidden="true"><span>.</span><span>.</span><span>.</span></span>
        </p>
        <div id="signup-progress-error" class="hidden signup-error-enter">
          <p id="signup-progress-error-text" class="mt-1.5 text-[12px] font-medium leading-5 text-red-500">Não foi possível concluir o cadastro. Tente novamente.</p>
          <div class="mt-6 flex justify-end">
            <button type="button" data-close-progress class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-900 px-5 text-[12px] font-medium text-white transition duration-150 hover:bg-slate-800">Fechar</button>
          </div>
        </div>
      </div>
    </div>
    <div id="signup-progress-success" class="hidden mt-7">
      <div class="w-full rounded-lg bg-amber-50 px-4 py-3 text-[12px] leading-5 text-amber-800 ring-1 ring-inset ring-amber-200">Enviámos uma mensagem para o seu e-mail. Verifique a sua caixa de entrada para continuar.</div>
      <div class="mt-6 flex justify-end">
        <button type="button" data-close-progress class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-900 px-5 text-[12px] font-medium text-white transition duration-150 hover:bg-slate-800">Fechar</button>
      </div>
    </div>
  </div>
</div>

<footer class="border-t border-slate-200 bg-slate-50">
  <div class="w-full px-5 py-14 sm:px-8 lg:px-10 xl:px-12"><div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
    <div class="sm:col-span-2 lg:col-span-1"><img src="assets/img/LOGO%20Sizotech.png" alt="Sizo Tech — Smart IT Solutions" class="h-14 w-auto object-contain" width="180" height="101"></div>
    <div><h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Produto</h3><ul class="mt-4 space-y-2.5 text-sm text-slate-600"><li><a href="#sobre" class="transition hover:text-slate-900">Sobre</a></li><li><a href="#funcionalidades" class="transition hover:text-slate-900">Funcionalidades</a></li><li><a href="#planos" class="transition hover:text-slate-900">Planos</a></li></ul></div>
    <div><h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Legal</h3><ul class="mt-4 space-y-2.5 text-sm text-slate-600"><li><a href="#" class="transition hover:text-slate-900">Política de Privacidade</a></li><li><a href="#" class="transition hover:text-slate-900">Termos</a></li></ul></div>
    <div><h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contacto</h3><ul class="mt-4 space-y-2.5 text-sm text-slate-600"><li><a href="<?= htmlspecialchars($sizoMailtoBase, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-slate-900"><?= htmlspecialchars($sizoContacto['email'], ENT_QUOTES, 'UTF-8') ?></a></li><li><a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-slate-900" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($sizoContacto['telefone_display'], ENT_QUOTES, 'UTF-8') ?></a></li></ul></div>
  </div><div class="mt-12 border-t border-slate-200 pt-8 text-sm text-slate-500"><p>© <span id="footer-year"></span> Sizo Software</p></div></div>
</footer>

<?php require __DIR__ . '/includes/footer.php'; ?>
