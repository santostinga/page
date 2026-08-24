(function () {
  'use strict';

  var api = 'api/sizotech/';
  var form = document.getElementById('signup-form');
  var modal = document.getElementById('signup-modal');
  var progressModal = document.getElementById('signup-progress-modal');
  if (!form || !modal || !progressModal) return;

  var currentStep = 1;
  var loadedPlans = [];
  var subdomainAvailable = false;
  var suggestTimer = null;
  var checkTimer = null;
  var csrf = (form.elements.csrf && form.elements.csrf.value) || '';
  var fieldSteps = { name: 1, company_type: 1, company_type_other: 1, email: 1, nuit: 1, phone: 2, phone_alt: 2, address_country: 2, address_province: 2, address_street: 2, address_neighborhood: 2, address_house_number: 2, subdomain: 2, business_area: 3, plan_code: 3, billing_cycle: 3 };

  function request(path, options) {
    return fetch(api + path, options || {}).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (body) { return { response: response, body: body }; });
    });
  }
  function getKey() {
    var key = sessionStorage.getItem('sizotech_registration_key');
    if (!key) {
      var id = window.crypto && crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + '-' + Math.random().toString(36).slice(2);
      key = 'signup-' + id;
      sessionStorage.setItem('sizotech_registration_key', key);
    }
    return key;
  }
  function resetForm() {
    form.reset(); currentStep = 1; subdomainAvailable = false;
    form.querySelectorAll('.field-error').forEach(function (item) { item.textContent = ''; });
    document.getElementById('signup-message').className = 'hidden mt-5 rounded-lg px-4 py-3 text-sm';
    document.getElementById('subdomain-availability').textContent = '';
    syncOther(); showStep(1);
  }
  function showStep(step) {
    currentStep = step;
    form.querySelectorAll('.signup-step').forEach(function (item) { item.classList.toggle('hidden', Number(item.dataset.step) !== step); });
    form.querySelectorAll('[data-step-dot]').forEach(function (item) { item.className = 'signup-dot flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold ' + (Number(item.dataset.stepDot) <= step ? 'bg-slate-950 text-white' : 'bg-slate-200 text-slate-500'); });
    document.getElementById('signup-back').classList.toggle('hidden', step === 1);
    document.getElementById('signup-next').classList.toggle('hidden', step === 3);
    document.getElementById('signup-submit').classList.toggle('hidden', step !== 3);
  }
  function fieldSlot(name) {
    var field = form.querySelector('[name="' + name + '"]');
    if (!field) return null;
    var slot = field.closest('label').querySelector('.field-error');
    return { field: field, slot: slot };
  }
  function clearError(name) { var item = fieldSlot(name); if (item) { item.field.classList.remove('border-red-300', 'bg-red-50/30'); if (item.slot) item.slot.textContent = ''; } }
  function setError(name, text) { var item = fieldSlot(name); if (item) { item.field.classList.add('border-red-300', 'bg-red-50/30'); if (item.slot) item.slot.textContent = text; } }
  function showErrors(errors) {
    var first = null;
    Object.keys(errors || {}).forEach(function (name) { var entry = errors[name] && errors[name][0]; var message = entry && entry.message ? entry.message : 'Dados inválidos.'; setError(name, message); if (!first || (fieldSteps[name] || 99) < (fieldSteps[first] || 99)) first = name; });
    if (first) { showStep(fieldSteps[first] || 1); setTimeout(function () { var field = form.querySelector('[name="' + first + '"]'); if (field) field.focus(); }, 100); }
  }
  function syncOther() {
    var option = form.elements.company_type.options[form.elements.company_type.selectedIndex];
    var needed = !!(option && option.dataset.requiresOther === '1');
    var wrap = document.getElementById('company-type-other-wrap');
    wrap.classList.toggle('hidden', !needed); form.elements.company_type_other.required = needed;
    if (!needed) form.elements.company_type_other.value = '';
  }
  function validateStep(step) {
    var value = function (name) { return String(form.elements[name] ? form.elements[name].value : '').trim(); };
    Object.keys(fieldSteps).forEach(function (name) { if (fieldSteps[name] === step) clearError(name); });
    var errors = {};
    function add(name, text) { errors[name] = [{ message: text }]; }
    if (step === 1) { if (value('name').replace(/[^\p{L}\p{N}]/gu, '').length < 2) add('name', 'Indique um nome de empresa válido.'); if (!value('company_type')) add('company_type', 'Seleccione o tipo jurídico.'); if (value('company_type') === 'OTHER' && !value('company_type_other')) add('company_type_other', 'Indique o tipo jurídico.'); if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('email'))) add('email', 'Indique um e-mail válido.'); if (!/^\d{9}$/.test(value('nuit'))) add('nuit', value('nuit') ? 'Indique um NUIT válido com 9 dígitos.' : 'Indique o NUIT da empresa.'); }
    if (step === 2) { if (!value('address_province')) add('address_province', 'Indique a província ou cidade.'); if (!/^[a-z0-9-]+$/.test(value('subdomain')) || !subdomainAvailable) add('subdomain', 'Indique um endereço disponível para a empresa.'); }
    if (step === 3 && (!value('plan_code') || !value('billing_cycle'))) add('plan_code', 'Seleccione um plano válido.');
    showErrors(errors); return Object.keys(errors).length === 0;
  }
  function renderPlans(plans, list) {
    list = list || document.getElementById('plans-list'); list.textContent = '';
    plans.forEach(function (plan) {
      var code = String(plan.code || '').toUpperCase(); var featured = code === 'STANDARD';
      var article = document.createElement('article'); article.className = 'signup-plan-card relative flex flex-col rounded-xl border ' + (code === 'FREE' ? 'border-emerald-200' : code === 'PRO' ? 'border-violet-200' : 'border-blue-300') + ' bg-white p-6 shadow-soft';
      var price = plan.price || {}; var quotas = plan.quotas || {};
      article.innerHTML = (featured ? '<span class="absolute right-5 top-5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-brand">Mais popular</span>' : '') + '<div' + (featured ? ' class="pr-24"' : '') + '><p class="text-sm font-semibold text-brand"></p><div class="mt-2 flex items-baseline gap-1"><span class="text-3xl font-semibold tracking-tight text-slate-950"></span><span class="text-sm text-slate-500"></span></div></div><dl class="mt-5 space-y-3 text-sm"><div class="flex justify-between gap-4"><dt class="text-slate-500">Cotações por mês</dt><dd class="font-medium text-slate-900"></dd></div><div class="flex justify-between gap-4"><dt class="text-slate-500">Facturas por mês</dt><dd class="font-medium text-slate-900"></dd></div><div class="flex justify-between gap-4"><dt class="text-slate-500">Vendas por mês</dt><dd class="font-medium text-slate-900"></dd></div></dl><button type="button" data-plan-code="" class="mt-7 inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Escolher plano</button>';
      article.querySelector('p').textContent = plan.name || code; var priceNodes = article.querySelectorAll('.mt-2 span'); priceNodes[0].textContent = Number(price.amount || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); priceNodes[1].textContent = (price.currency || 'MZN') + '/mês'; var quotaNodes = article.querySelectorAll('dd'); quotaNodes[0].textContent = quotas.ct_per_month == null ? 'Ilimitado' : quotas.ct_per_month; quotaNodes[1].textContent = quotas.ft_per_month == null ? 'Ilimitado' : quotas.ft_per_month; quotaNodes[2].textContent = quotas.vd_per_month == null ? 'Ilimitado' : quotas.vd_per_month;
      var button = article.querySelector('button'); button.dataset.planCode = code; button.dataset.planName = plan.name || code; button.dataset.planPrice = priceNodes[0].textContent + ' ' + (price.currency || 'MZN'); button.dataset.planCycles = JSON.stringify(plan.billing_cycles || ['monthly']); list.appendChild(article);
    });
  }
  function loadPlans() { request('plans').then(function (r) { var plans = r.body.data || []; if (!r.response.ok || !plans.length) throw new Error(); loadedPlans = plans; document.getElementById('plans-loading').classList.add('hidden'); renderPlans(plans); }).catch(function () { document.getElementById('plans-loading').classList.add('hidden'); var error = document.getElementById('plans-error'); error.textContent = 'Não foi possível carregar os planos neste momento. Tente novamente dentro de alguns minutos.'; error.classList.remove('hidden'); }); }
  function loadTypes() { request('registration-options').then(function (r) { var data = r.body.data || {}; var select = form.elements.company_type; select.textContent = ''; (data.company_type_groups || []).forEach(function (group) { var og = document.createElement('optgroup'); og.label = group.label; (data.company_types || []).filter(function (type) { return type.group === group.code; }).forEach(function (type) { var opt = new Option(type.label, type.code, false, type.code === 'LDA'); opt.dataset.requiresOther = type.requires_other ? '1' : '0'; og.appendChild(opt); }); select.appendChild(og); }); syncOther(); }).catch(function () { var select = form.elements.company_type; select.innerHTML = '<option value="">Não foi possível carregar os tipos</option>'; }); }
  function setAvailability(ok, text) { subdomainAvailable = ok; var out = document.getElementById('subdomain-availability'); out.textContent = text; out.className = 'mt-1 block text-xs ' + (ok ? 'text-emerald-600' : 'text-red-600'); }
  function checkSubdomain() { var value = String(form.elements.subdomain.value || '').trim().toLowerCase(); form.elements.subdomain.value = value; if (!/^[a-z0-9-]+$/.test(value)) { setAvailability(false, value ? 'Use apenas letras minúsculas, números e hífen.' : ''); return Promise.resolve(false); } return request('subdomains/check?subdomain=' + encodeURIComponent(value)).then(function (r) { var data = r.body.data || {}; var ok = r.response.ok && !!data.valid && !!data.available; setAvailability(ok, ok ? 'Endereço disponível.' : 'Este endereço não está disponível.'); return ok; }).catch(function () { setAvailability(false, 'Não foi possível verificar o endereço.'); return false; }); }
  function suggestSubdomain() { var name = String(form.elements.name.value || '').trim(); if (name.length < 2) return; request('subdomains/suggest?name=' + encodeURIComponent(name)).then(function (r) { var data = r.body.data || {}; if (!r.response.ok || !data.subdomain) return; form.elements.subdomain.value = data.subdomain; if (data.host) document.getElementById('subdomain-suffix').textContent = '.' + data.host.replace(data.subdomain + '.', ''); return checkSubdomain(); }); }
  function updateProgress(state) { var stages = { pending: 0, validating: 0, provisioning_dns: 1, provisioning_cloudways: 3, creating_subscription: 4, creating_tenant: 4, sending_invite: 4, initializing_billing: 5, billing_initialized: 6, finalizing: 6, completed: 7 }; var active = stages[state.status] == null ? 0 : stages[state.status]; document.querySelectorAll('#signup-progress-steps li').forEach(function (item) { var done = Number(item.dataset.stage) < active; var now = Number(item.dataset.stage) === active; item.textContent = (done ? '✓ ' : now ? '• ' : '○ ') + item.textContent.replace(/^[✓•○] /, ''); item.className = done ? 'text-emerald-700' : now ? 'font-semibold text-slate-900' : 'text-slate-500'; }); document.getElementById('signup-progress-message').textContent = state.message || 'A preparar a sua subscrição…'; }
  function monitor(id) { request('provisionings/' + encodeURIComponent(id)).then(function (r) { var state = r.body.data || r.body; updateProgress(state); if (state.status === 'completed') { localStorage.removeItem('sizotech_provisioning_id'); sessionStorage.removeItem('sizotech_registration_key'); var result = state.result || {}; var box = document.getElementById('signup-progress-result'); box.innerHTML = ''; box.className = 'mt-7 rounded-xl bg-emerald-50 px-4 py-4 text-sm text-emerald-900'; box.textContent = 'Subscrição concluída com sucesso. '; if (result.access_url) { var link = document.createElement('a'); link.href = result.access_url; link.className = 'font-semibold underline'; link.textContent = 'Aceder ao sistema'; box.appendChild(link); } box.classList.remove('hidden'); return; } if (state.status === 'failed') { localStorage.removeItem('sizotech_provisioning_id'); var failure = document.getElementById('signup-progress-result'); failure.textContent = state.message || 'Não foi possível concluir o cadastro.'; failure.className = 'mt-7 rounded-xl bg-red-50 px-4 py-4 text-sm text-red-700'; failure.classList.remove('hidden'); return; } setTimeout(function () { monitor(id); }, 2000); }).catch(function () { setTimeout(function () { monitor(id); }, 3000); }); }
  document.addEventListener('click', function (event) { var button = event.target.closest('[data-plan-code]'); if (!button) return; var picker = document.getElementById('plan-picker-modal'); picker.classList.remove('is-open'); resetForm(); form.elements.plan_code.value = button.dataset.planCode; document.getElementById('signup-plan-summary').textContent = 'Plano escolhido *: ' + button.dataset.planName + ' — ' + button.dataset.planPrice; var cycle = form.elements.billing_cycle; cycle.textContent = ''; JSON.parse(button.dataset.planCycles || '["monthly"]').forEach(function (name) { cycle.add(new Option(name.charAt(0).toUpperCase() + name.slice(1), name)); }); modal.classList.add('is-open'); document.body.classList.add('modal-open'); });
  document.querySelectorAll('[data-open-plan-picker]').forEach(function (button) { button.addEventListener('click', function () { var picker = document.getElementById('plan-picker-modal'); var list = document.getElementById('plan-picker-list'); if (loadedPlans.length) { renderPlans(loadedPlans, list); list.classList.remove('hidden'); document.getElementById('plan-picker-loading').classList.add('hidden'); } else { list.classList.add('hidden'); document.getElementById('plan-picker-loading').classList.remove('hidden'); } picker.classList.add('is-open'); document.body.classList.add('modal-open'); }); });
  document.querySelectorAll('[data-close-plan-picker]').forEach(function (button) { button.addEventListener('click', function () { document.getElementById('plan-picker-modal').classList.remove('is-open'); document.body.classList.remove('modal-open'); }); });
  document.querySelectorAll('[data-signup-close]').forEach(function (button) { button.addEventListener('click', function () { modal.classList.remove('is-open'); document.body.classList.remove('modal-open'); resetForm(); }); });
  form.elements.company_type.addEventListener('change', syncOther);
  form.elements.name.addEventListener('input', function () { clearTimeout(suggestTimer); suggestTimer = setTimeout(suggestSubdomain, 500); });
  form.elements.subdomain.addEventListener('input', function () { clearTimeout(checkTimer); checkTimer = setTimeout(checkSubdomain, 400); });
  form.querySelectorAll('input, select').forEach(function (field) { field.addEventListener('input', function () { clearError(field.name); }); field.addEventListener('change', function () { clearError(field.name); }); });
  document.getElementById('signup-next').addEventListener('click', function () { if (validateStep(currentStep)) showStep(currentStep + 1); });
  document.getElementById('signup-back').addEventListener('click', function () { showStep(currentStep - 1); });
  form.addEventListener('submit', function (event) { event.preventDefault(); if (!validateStep(3)) return; var button = document.getElementById('signup-submit'); button.disabled = true; button.textContent = 'A processar…'; var data = {}; new FormData(form).forEach(function (value, key) { data[key] = value; }); data.show_legal_designation = true; request('registrations', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf, 'Idempotency-Key': getKey() }, body: JSON.stringify(data) }).then(function (r) { button.disabled = false; button.textContent = 'Concluir cadastro'; if (r.response.status === 202 && r.body.provisioning_id) { localStorage.setItem('sizotech_provisioning_id', String(r.body.provisioning_id)); modal.classList.remove('is-open'); document.body.classList.remove('modal-open'); progressModal.classList.add('is-open'); updateProgress(r.body); monitor(r.body.provisioning_id); return; } if (r.body.errors) { showErrors(r.body.errors); return; } var message = document.getElementById('signup-message'); message.textContent = r.body.message || 'Não foi possível concluir o cadastro neste momento.'; message.className = 'mt-5 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700'; }).catch(function () { button.disabled = false; button.textContent = 'Concluir cadastro'; }); });
  var pending = localStorage.getItem('sizotech_provisioning_id'); if (pending) { progressModal.classList.add('is-open'); monitor(pending); }
  loadPlans(); loadTypes();
})();
