<?php
require_once __DIR__ . '/config/https.php';
sizo_force_canonical_https();

session_start();
require_once __DIR__ . '/config/system_api.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $csrf = (string) ($_POST['csrf'] ?? '');
    if ($csrf === '' || !hash_equals((string) ($_SESSION['signup_csrf'] ?? ''), $csrf)) {
        http_response_code(419); echo json_encode(['message' => 'Pedido inválido. Atualize a página e tente novamente.']); exit;
    }
    $key = (string) ($_SESSION['signup_idempotency'] ?? '');
    if ($key === '') { $key = 'signup-' . bin2hex(random_bytes(16)); $_SESSION['signup_idempotency'] = $key; }
    $fields = ['name','company_type','show_legal_designation','email','nuit','phone','phone_alt','business_area','address_country','address_province','address_street','address_neighborhood','address_house_number','plan_code','billing_cycle'];
    $payload = [];
    foreach ($fields as $field) { $payload[$field] = trim((string) ($_POST[$field] ?? '')); }
    $payload['show_legal_designation'] = (int) ($payload['show_legal_designation'] ?: 1);
    $response = sizo_system_api('POST', '/api/v1/registrations', $payload, $key);
    $data = $response['data'];
    if (in_array($response['status'], [201, 422], true)) { unset($_SESSION['signup_idempotency']); }
    if (!$response['ok'] && $data === []) { $data = ['message' => 'Não foi possível concluir o cadastro neste momento. Tente novamente.']; }
    http_response_code($response['status']); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit;
}

$code = strtoupper(trim((string) ($_GET['plan'] ?? '')));
$plansResponse = sizo_system_api('GET', '/api/v1/plans');
$plans = !empty($plansResponse['ok']) ? ($plansResponse['data']['data'] ?? []) : [];
$plan = null; foreach ($plans as $item) { if (($item['code'] ?? '') === $code) { $plan = $item; break; } }
$_SESSION['signup_csrf'] = bin2hex(random_bytes(32));
$_SESSION['signup_idempotency'] = 'signup-' . bin2hex(random_bytes(16));
$pageTitle = 'Subscrição | Sizo Software'; require __DIR__ . '/includes/head.php';
?>
<main class="min-h-screen bg-slate-50 px-4 py-12 sm:py-20"><div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-card sm:p-10">
<?php if ($plan === null): ?><h1 class="text-2xl font-bold text-slate-950">Plano indisponível</h1><p class="mt-3 text-slate-600">Volte à página de planos e tente novamente.</p><a href="index.php#planos" class="mt-6 inline-flex text-sm font-semibold text-brand">Ver planos</a>
<?php else: $cycles = $plan['billing_cycles'] ?? ['monthly']; ?>
<p class="text-sm font-semibold uppercase tracking-wider text-brand">Subscrição</p><h1 class="mt-2 text-3xl font-bold text-slate-950">Crie a sua empresa</h1><p class="mt-4 rounded-xl bg-brand-soft px-4 py-3 text-sm text-slate-700">Plano selecionado: <strong><?= htmlspecialchars((string) $plan['name'], ENT_QUOTES, 'UTF-8') ?></strong> — <?= htmlspecialchars((string) ($plan['price']['amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) ($plan['price']['currency'] ?? 'MZN'), ENT_QUOTES, 'UTF-8') ?></p>
<form id="signup-form" class="mt-7 grid gap-4 sm:grid-cols-2" novalidate><input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['signup_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="plan_code" value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="show_legal_designation" value="1">
<?php $inputs=['name'=>'Nome da empresa','email'=>'E-mail','nuit'=>'NUIT','phone'=>'Telefone','phone_alt'=>'Telefone alternativo','business_area'=>'Área de atividade','address_province'=>'Província/cidade','address_street'=>'Rua / avenida','address_neighborhood'=>'Bairro','address_house_number'=>'Número']; foreach($inputs as $name=>$label): ?><label class="text-sm font-semibold text-slate-700 <?= in_array($name,['name','business_area','address_street'])?'sm:col-span-2':'' ?>"><?= $label ?><input name="<?= $name ?>" <?= in_array($name,['name','email','address_province'])?'required':'' ?> <?= $name==='email'?'type="email"':'' ?> class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal outline-none focus:border-brand focus:ring-2 focus:ring-blue-100"><span class="field-error mt-1 block text-xs text-red-600"></span></label><?php endforeach; ?>
<label class="text-sm font-semibold text-slate-700">Tipo de empresa<select name="company_type" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="LDA">Sociedade por Quotas (Lda)</option><option value="EI">Empresa Individual (EI)</option><option value="ONG">Organização Não-Governamental</option></select></label><label class="text-sm font-semibold text-slate-700">País<input name="address_country" value="MZ" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label><label class="text-sm font-semibold text-slate-700 sm:col-span-2">Ciclo de faturação<select name="billing_cycle" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><?php foreach($cycles as $cycle): ?><option value="<?= htmlspecialchars($cycle, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($cycle), ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></label>
<p id="form-message" class="hidden sm:col-span-2 rounded-lg px-4 py-3 text-sm"></p><button id="submit-button" class="sm:col-span-2 rounded-lg bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800">Concluir cadastro</button></form>
<script>document.getElementById('signup-form').addEventListener('submit',async e=>{e.preventDefault();let f=e.currentTarget,b=document.getElementById('submit-button'),m=document.getElementById('form-message');document.querySelectorAll('.field-error').forEach(x=>x.textContent='');b.disabled=true;b.textContent='A processar…';try{let r=await fetch('subscricao.php',{method:'POST',body:new FormData(f)}),d=await r.json();if(r.status===201){f.innerHTML='<div class="rounded-xl bg-emerald-50 p-6 text-emerald-900"><h2 class="text-xl font-bold">Cadastro concluído</h2><p class="mt-2">A empresa foi criada. Verifique o seu e-mail para continuar.</p>' +(d.company?.access_url?'<a class="mt-5 inline-flex rounded-lg bg-emerald-700 px-4 py-2 text-white" href="'+d.company.access_url+'">Aceder ao sistema</a>':'')+'</div>';return}if(d.errors){Object.entries(d.errors).forEach(([k,v])=>{let e=f.querySelector('[name="'+k+'"]')?.parentElement.querySelector('.field-error');if(e)e.textContent=v?.[0]?.message||'Dados inválidos';});}m.textContent=d.message||'Não foi possível concluir o cadastro neste momento. Tente novamente.';m.className='sm:col-span-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700'}catch(x){m.textContent='Não foi possível concluir o cadastro neste momento. Tente novamente.';m.className='sm:col-span-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700'}finally{b.disabled=false;b.textContent='Concluir cadastro'}})</script>
<?php endif; ?></div></main><?php require __DIR__ . '/includes/footer.php'; ?>
