<?php
session_start();
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$pageTitle = 'Sizo Software | Gestão empresarial simples';
$pageDesc = 'Software simples, funcional e adaptável para gerir a sua empresa.';
$sizoCfg = require __DIR__ . '/config/planos.php';
$planos = [];
$sizoContacto = $sizoCfg['contacto'];
$sizoMailtoBase = 'mailto:' . rawurlencode($sizoContacto['email']);
$sizoWhatsAppUrl = $sizoContacto['whatsapp_url'];
$startMailto = $sizoMailtoBase . '?subject=' . rawurlencode('Iniciar gratuitamente - Sizo Software');
$_SESSION['signup_csrf'] = bin2hex(random_bytes(32));
$_SESSION['signup_idempotency'] = 'signup-' . bin2hex(random_bytes(16));

require_once __DIR__ . '/config/system_api.php';
$planosResponse = sizo_system_api('GET', '/api/v1/plans');
if (!empty($planosResponse['ok'])) {
    $planos = $planosResponse['data']['data'] ?? [];
}
$registrationOptionsResponse = sizo_system_api('GET', '/api/v1/registration-options');
$companyTypeGroups = !empty($registrationOptionsResponse['ok']) ? ($registrationOptionsResponse['data']['data']['company_type_groups'] ?? []) : [];
$companyTypes = !empty($registrationOptionsResponse['ok']) ? ($registrationOptionsResponse['data']['data']['company_types'] ?? []) : [];

function sizo_plan_price(array $plan): string
{
    if (array_key_exists('code', $plan)) {
        return number_format((float) ($plan['price']['amount'] ?? 0), 2, ',', ' ');
    }
    return (string) ($plan['preco_mt'] ?? '0,00');
}

function sizo_plan_limit(mixed $value): string
{
    return $value === null ? 'Ilimitado' : number_format((int) $value, 0, ',', ' ');
}

require __DIR__ . '/includes/head.php';
?>

<header id="site-nav" class="site-nav fixed top-0 left-0 right-0 z-50 w-full">
  <div class="relative flex w-full items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8 xl:px-10">
    <a href="#inicio" class="flex shrink-0 items-center" aria-label="Sizo Software - início"><img src="assets/img/logo.png" alt="Sizo Software" class="h-8 w-auto object-contain sm:h-9" width="180" height="40"></a>
    <nav class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-8 md:flex" aria-label="Principal">
      <a href="#sobre" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Sobre</a>
      <a href="#funcionalidades" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Funcionalidades</a>
      <a href="#planos" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Planos</a>
    </nav>
    <div class="flex items-center gap-3">
      <a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="hidden rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 md:inline-flex">Inicie gratuitamente</a>
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
      <div class="mt-8"><a href="<?= htmlspecialchars($startMailto, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:bg-slate-800">Inicie gratuitamente</a></div>
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

  <section id="planos" class="section-band section-band--white scroll-mt-24 py-20 sm:py-28">
    <div class="mx-auto max-w-6xl px-5 sm:px-8">
      <div class="mx-auto max-w-2xl text-center" data-aos="fade-up"><p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand">Planos</p><h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Escolha o plano ideal para si</h2><p class="mt-4 text-base leading-relaxed text-slate-600">Todos os planos incluem o sistema completo. Escolha de acordo com o ritmo da sua empresa.</p></div>
      <?php if ($planos === []): ?>
      <p class="mx-auto mt-10 max-w-xl rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-center text-sm text-amber-800">Não foi possível carregar os planos neste momento. Tente novamente dentro de alguns minutos.</p>
      <?php else: ?>
      <div class="mt-14 grid gap-5 md:grid-cols-3">
        <?php foreach ($planos as $index => $plan):
          $code = strtoupper((string) ($plan['code'] ?? $plan['tipo'] ?? ''));
          $isStandard = $code === 'STANDARD' || $code === 'BUSINESS';
          $theme = match ($code) {
              'FREE', 'STARTER', 'LITE' => ['name' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'text-emerald-600', 'button' => 'border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'],
              'PRO', 'ENTERPRISE' => ['name' => 'text-violet-700', 'border' => 'border-violet-200', 'icon' => 'text-violet-600', 'button' => 'border border-violet-200 bg-violet-50 text-violet-700 hover:bg-violet-100'],
              default => ['name' => 'text-brand', 'border' => 'border-blue-300', 'icon' => 'text-brand', 'button' => 'bg-brand text-white hover:bg-blue-600'],
          };
          $isDynamic = array_key_exists('code', $plan);
          $planName = (string) ($plan['name'] ?? $plan['nome'] ?? '');
          $signupUrl = 'subscricao.php?plan=' . rawurlencode($code);
        ?>
        <article class="relative flex flex-col rounded-xl border <?= $theme['border'] ?> bg-white p-6 shadow-soft transition duration-200 hover:-translate-y-1 hover:shadow-xl" data-aos="fade-up" data-aos-delay="<?= $index * 80 ?>">
          <?php if ($isStandard): ?><span class="absolute right-5 top-5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-brand">Mais popular</span><?php endif; ?>
          <div class="<?= $isStandard ? 'pr-24' : '' ?>"><p class="text-sm font-semibold <?= $theme['name'] ?>"><?= htmlspecialchars($planName, ENT_QUOTES, 'UTF-8') ?></p><div class="mt-2 flex items-baseline gap-1"><span class="text-3xl font-semibold tracking-tight text-slate-950"><?= htmlspecialchars(sizo_plan_price($plan), ENT_QUOTES, 'UTF-8') ?></span><span class="text-sm text-slate-500"><?= htmlspecialchars((string) ($plan['price']['currency'] ?? 'MZN'), ENT_QUOTES, 'UTF-8') ?>/mês</span></div></div>
          <div class="mt-5 flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-3 text-sm font-medium text-slate-900"><svg class="h-4 w-4 shrink-0 <?= $theme['icon'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7"/></svg>Sistema completo incluído</div>
          <?php if ($isDynamic): ?>
          <dl class="mt-5 space-y-3 text-sm"><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Cotações por mês</dt><dd class="font-medium text-slate-900"><?= sizo_plan_limit($plan['quotas']['ct_per_month'] ?? null) ?></dd></div><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Facturas por mês</dt><dd class="font-medium text-slate-900"><?= sizo_plan_limit($plan['quotas']['ft_per_month'] ?? null) ?></dd></div><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Vendas por mês</dt><dd class="font-medium text-slate-900"><?= sizo_plan_limit($plan['quotas']['vd_per_month'] ?? null) ?></dd></div></dl>
          <?php else: ?>
          <ul class="mt-5 space-y-3 text-sm text-slate-600"><?php foreach (($plan['bullets'] ?? []) as $bullet): ?><li class="flex gap-2"><span class="<?= $theme['icon'] ?>">✓</span><?= htmlspecialchars($bullet, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
          <?php endif; ?>
          <button type="button" data-signup-open data-plan-code="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" data-plan-name="<?= htmlspecialchars($planName, ENT_QUOTES, 'UTF-8') ?>" data-plan-price="<?= htmlspecialchars(sizo_plan_price($plan), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) ($plan['price']['currency'] ?? 'MZN'), ENT_QUOTES, 'UTF-8') ?>" data-plan-cycles='<?= htmlspecialchars(json_encode($plan['billing_cycles'] ?? ['monthly']), ENT_QUOTES, 'UTF-8') ?>' class="mt-7 inline-flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-semibold transition <?= $theme['button'] ?>">Escolher plano</button>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<div id="signup-modal" class="signup-modal fixed inset-0 z-[70] overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="signup-title">
  <div class="mx-auto my-6 w-full max-w-2xl rounded-2xl bg-white shadow-2xl sm:my-10">
    <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5"><div><p class="text-sm font-semibold text-brand">Subscrição</p><h2 id="signup-title" class="mt-1 text-xl font-bold text-slate-950">Crie a sua empresa</h2></div><button type="button" data-signup-close class="rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Fechar">✕</button></div>
    <form id="signup-form" class="p-6 sm:p-8" novalidate autocomplete="off">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['signup_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="plan_code"><input type="hidden" name="show_legal_designation" value="1">
      <div class="mb-7"><div class="flex items-center gap-2 text-xs font-semibold"><span class="signup-dot flex h-7 w-7 items-center justify-center rounded-full bg-slate-950 text-white" data-step-dot="1">1</span><i class="h-px flex-1 bg-slate-200"></i><span class="signup-dot flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-slate-500" data-step-dot="2">2</span><i class="h-px flex-1 bg-slate-200"></i><span class="signup-dot flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-slate-500" data-step-dot="3">3</span></div></div>
      <div data-step="1" class="signup-step grid gap-4 sm:grid-cols-2"><p class="sm:col-span-2 text-sm text-slate-600">Dados principais da empresa.</p><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Nome da empresa <span class="text-red-600" aria-hidden="true">*</span><input required name="name" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Tipo jurídico <span class="text-red-600" aria-hidden="true">*</span><select name="company_type" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><?php foreach ($companyTypeGroups as $group): ?><optgroup label="<?= htmlspecialchars((string) ($group['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><?php foreach ($companyTypes as $type): if (($type['group'] ?? '') !== ($group['code'] ?? '')) continue; ?><option value="<?= htmlspecialchars((string) $type['code'], ENT_QUOTES, 'UTF-8') ?>" data-requires-other="<?= !empty($type['requires_other']) ? '1' : '0' ?>" <?= ($type['code'] ?? '') === 'LDA' ? 'selected' : '' ?>><?= htmlspecialchars((string) $type['label'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select><span class="field-error text-xs text-red-600"></span></label><label id="company-type-other-wrap" class="hidden text-sm font-semibold text-slate-700">Outro tipo jurídico <span class="text-red-600" aria-hidden="true">*</span><input name="company_type_other" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">NUIT <span class="text-red-600" aria-hidden="true">*</span><input name="nuit" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="sm:col-span-2 text-sm font-semibold text-slate-700">E-mail <span class="text-red-600" aria-hidden="true">*</span><input required type="email" name="email" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label></div>
      <div data-step="2" class="signup-step hidden grid gap-4 sm:grid-cols-2"><p class="sm:col-span-2 text-sm text-slate-600">Contactos e morada.</p><label class="text-sm font-semibold text-slate-700">Telefone<input name="phone" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700">Telefone alternativo<input name="phone_alt" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700">País<input name="address_country" value="MZ" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700">Província/cidade <span class="text-red-600" aria-hidden="true">*</span><input required name="address_province" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Rua / avenida<input name="address_street" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700">Bairro<input name="address_neighborhood" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700">Número<input name="address_house_number" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label></div>
      <div data-step="3" class="signup-step hidden grid gap-4"><div id="signup-plan-summary" class="rounded-xl bg-brand-soft px-4 py-3 text-sm text-slate-700" aria-label="Plano escolhido, obrigatório"></div><label class="text-sm font-semibold text-slate-700">Área de atividade<input name="business_area" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700">Ciclo de faturação<select name="billing_cycle" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></select></label></div>
      <p id="signup-message" class="hidden mt-5 rounded-lg px-4 py-3 text-sm"></p><div class="mt-7 flex justify-between gap-3"><button type="button" id="signup-back" class="hidden rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100">Voltar</button><span id="signup-spacer"></span><button type="button" id="signup-next" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Continuar</button><button type="submit" id="signup-submit" class="hidden rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Concluir cadastro</button></div>
    </form>
  </div>
</div>

<footer class="border-t border-slate-200 bg-slate-50">
  <div class="w-full px-5 py-14 sm:px-8 lg:px-10 xl:px-12"><div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
    <div class="sm:col-span-2 lg:col-span-1"><img src="assets/img/logo.png" alt="Sizo Software" class="h-8 w-auto object-contain" width="160" height="36"></div>
    <div><h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Produto</h3><ul class="mt-4 space-y-2.5 text-sm text-slate-600"><li><a href="#sobre" class="transition hover:text-slate-900">Sobre</a></li><li><a href="#funcionalidades" class="transition hover:text-slate-900">Funcionalidades</a></li><li><a href="#planos" class="transition hover:text-slate-900">Planos</a></li></ul></div>
    <div><h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Legal</h3><ul class="mt-4 space-y-2.5 text-sm text-slate-600"><li><a href="#" class="transition hover:text-slate-900">Política de Privacidade</a></li><li><a href="#" class="transition hover:text-slate-900">Termos</a></li></ul></div>
    <div><h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contacto</h3><ul class="mt-4 space-y-2.5 text-sm text-slate-600"><li><a href="<?= htmlspecialchars($sizoMailtoBase, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-slate-900"><?= htmlspecialchars($sizoContacto['email'], ENT_QUOTES, 'UTF-8') ?></a></li><li><a href="<?= htmlspecialchars($sizoWhatsAppUrl, ENT_QUOTES, 'UTF-8') ?>" class="transition hover:text-slate-900" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($sizoContacto['telefone_display'], ENT_QUOTES, 'UTF-8') ?></a></li></ul></div>
  </div><div class="mt-12 border-t border-slate-200 pt-8 text-sm text-slate-500"><p>© <span id="footer-year"></span> Sizo Software</p></div></div>
</footer>

<?php require __DIR__ . '/includes/footer.php'; ?>
