(function () {
  'use strict';

  var api = 'api/sizotech/';
  var form = document.getElementById('signup-form');
  var modal = document.getElementById('signup-modal');
  var progressModal = document.getElementById('signup-progress-modal');
  if (!form || !modal || !progressModal) return;
  form.elements.company_type.closest('label').classList.add('sm:col-span-2');
  form.elements.email.closest('label').classList.remove('sm:col-span-2');
  form.elements.nuit.maxLength = 11;
  form.elements.nuit.setAttribute('data-mz-nuit', '');

  var currentStep = 1;
  var loadedPlans = [];
  var plansPromise = null;
  var selectedBillingCycle = 'monthly';
  var subdomainAvailable = false;
  var suggestTimer = null;
  var checkTimer = null;
  var submitting = false;
  var fieldSteps = { name: 1, company_type: 1, company_type_other: 1, email: 1, nuit: 1, subdomain: 1, phone: 2, phone_alt: 2, address_country: 2, address_province: 2, address_street: 2, address_neighborhood: 2, address_house_number: 2, business_area: 3, business_area_other: 3, plan_code: 3, billing_cycle: 3 };
  var billingCycles = {
    monthly: { label: 'Mensal', period: '/mês', months: 1 },
    quarterly: { label: 'Trimestral', period: '/trimestre', months: 3 },
    semiannual: { label: 'Semestral', period: '/semestre', months: 6 },
    yearly: { label: 'Anual', period: '/ano', months: 12 }
  };

  function request(path, options) {
    return fetch(api + path, options || {}).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (body) { return { response: response, body: body }; });
    });
  }
  function formatMoney(value) {
    var parts = Number(value || 0).toFixed(2).split('.');
    return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ',' + parts[1];
  }
  function nuitDigits(value) { return String(value || '').replace(/\D+/g, '').slice(0, 9); }
  function formatNuit(value) {
    var digits = nuitDigits(value);
    if (digits.length <= 3) return digits;
    if (digits.length <= 6) return digits.slice(0, 3) + ' ' + digits.slice(3);
    return digits.slice(0, 3) + ' ' + digits.slice(3, 6) + ' ' + digits.slice(6);
  }
  function phoneDigits(value) { return String(value || '').replace(/\D+/g, '').replace(/^258/, '').replace(/^0(?=\d{9}$)/, '').slice(0, 9); }
  function formatPhone(value) {
    var digits = phoneDigits(value);
    if (digits.length <= 2) return digits;
    if (digits.length <= 4) return digits.slice(0, 2) + ' ' + digits.slice(2);
    if (digits.length <= 6) return digits.slice(0, 2) + ' ' + digits.slice(2, 4) + ' ' + digits.slice(4);
    return digits.slice(0, 2) + ' ' + digits.slice(2, 4) + ' ' + digits.slice(4, 6) + ' ' + digits.slice(6);
  }
  function setupPhoneField(name) {
    var visible = form.elements[name];
    visible.name = name + '_national'; visible.type = 'tel'; visible.inputMode = 'numeric'; visible.maxLength = 12;
    visible.placeholder = '87 00 00 000'; visible.autocomplete = 'off'; visible.classList.add('border-l-0', 'rounded-l-none', 'tracking-wider');
    var hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = name;
    var prefix = document.createElement('span'); prefix.className = 'mt-1.5 inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-3 text-sm font-semibold text-slate-700'; prefix.textContent = '+258';
    var row = document.createElement('span'); row.className = 'flex';
    visible.parentNode.insertBefore(row, visible); row.appendChild(prefix); row.appendChild(visible); row.parentNode.insertBefore(hidden, row.nextSibling);
    var sync = function () { var digits = phoneDigits(visible.value); visible.value = formatPhone(digits); hidden.value = digits ? '+258' + digits : ''; clearError(name); };
    visible.addEventListener('input', sync);
    visible.addEventListener('keydown', function (event) { if (event.key !== 'Backspace' || this.selectionStart !== this.selectionEnd || this.selectionEnd !== this.value.length) return; var digits = phoneDigits(this.value); if (!digits) return; event.preventDefault(); this.value = formatPhone(digits.slice(0, -1)); sync(); clearError(name); });
    sync(); return { visible: visible, hidden: hidden };
  }
  function setupSearchableSelect(select, placeholder) {
    var source = Array.prototype.map.call(select.options, function (option) { return { value: option.value, label: option.textContent.trim(), selected: option.selected, disabled: option.disabled, requiresOther: option.dataset.requiresOther || '', designation: option.dataset.designation || '' }; });
    var wrap = document.createElement('div'); wrap.className = 'relative mt-1.5';
    var input = document.createElement('input'); input.type = 'search'; input.placeholder = placeholder; input.autocomplete = 'off'; input.dataset.selectSearch = select.name; input.className = select.className.replace('mt-1.5', ''); input.setAttribute('role', 'combobox'); input.setAttribute('aria-expanded', 'false');
    var menu = document.createElement('div'); menu.className = 'absolute z-50 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-xl'; menu.setAttribute('role', 'listbox');
    wrap.appendChild(input); wrap.appendChild(menu); select.insertAdjacentElement('beforebegin', wrap); select.classList.add('hidden'); select.tabIndex = -1; select._searchInput = input; select._searchOptions = source;
    var closeMenu = function () { menu.classList.add('hidden'); input.setAttribute('aria-expanded', 'false'); };
    var choose = function (item) { select.value = item.value; input.value = item.label; closeMenu(); select.dispatchEvent(new Event('change', { bubbles: true })); };
    var renderMenu = function (term) {
      var query = String(term || '').trim().toLocaleLowerCase('pt'); menu.textContent = '';
      var matches = source.filter(function (item) { return !item.disabled && (!query || item.label.toLocaleLowerCase('pt').indexOf(query) !== -1); });
      matches.forEach(function (item) { var option = document.createElement('button'); option.type = 'button'; option.className = 'block w-full rounded-md px-3 py-2 text-left text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-800'; option.textContent = item.label; option.dataset.value = item.value; option.setAttribute('role', 'option'); option.addEventListener('mousedown', function (event) { event.preventDefault(); choose(item); }); menu.appendChild(option); });
      if (!matches.length) { var empty = document.createElement('p'); empty.className = 'px-3 py-2 text-sm text-slate-500'; empty.textContent = 'Nenhuma opção encontrada.'; menu.appendChild(empty); }
      menu.classList.remove('hidden'); input.setAttribute('aria-expanded', 'true');
    };
    select._renderSearchOptions = function (term, useDefault) { if (useDefault) select.value = ((source.filter(function (item) { return item.selected; })[0] || {}).value || ''); var current = source.filter(function (item) { return item.value === select.value; })[0]; input.value = current ? current.label : ''; if (term) renderMenu(term); else closeMenu(); };
    input.addEventListener('focus', function () { if (select.value === '') input.value = ''; renderMenu(''); });
    input.addEventListener('input', function () { renderMenu(input.value); clearError(select.name); updateNavigationState(); });
    input.addEventListener('keydown', function (event) { if (event.key === 'Enter') { var first = menu.querySelector('button'); if (first) { event.preventDefault(); event.stopPropagation(); first.dispatchEvent(new MouseEvent('mousedown', { bubbles: true })); } } else if (event.key === 'Escape') { closeMenu(); } });
    input.addEventListener('blur', function () { setTimeout(function () { var current = source.filter(function (item) { return item.value === select.value; })[0]; input.value = current ? current.label : ''; closeMenu(); updateNavigationState(); }, 100); });
    select._renderSearchOptions('', false);
    return input;
  }
  function syncSearchableSelect(select, useDefault) { if (select && select._searchInput) { select._searchInput.value = ''; select._renderSearchOptions('', !!useDefault); } }
  function searchableSelectionValid(select) {
    if (!select) return false;
    var option = select.options[select.selectedIndex];
    if (!option || !select.value) return false;
    if (!select._searchInput) return true;
    return select._searchInput.value.trim() === option.textContent.trim();
  }
  function currentCsrf() {
    return (form.elements.csrf && form.elements.csrf.value) || '';
  }
  function showSignupMessage(text, kind) {
    var message = document.getElementById('signup-message');
    if (!message) return;
    message.textContent = text || '';
    if (!text) {
      message.className = 'hidden mt-5 rounded-lg px-4 py-3 text-sm';
      return;
    }
    message.className = 'mt-5 rounded-lg px-4 py-3 text-sm ' + (kind === 'ok'
      ? 'bg-emerald-50 text-emerald-800'
      : 'bg-red-50 text-red-700');
  }
  function extractPlans(body) {
    if (Array.isArray(body && body.data)) return body.data;
    if (Array.isArray(body && body.plans)) return body.plans;
    if (Array.isArray(body)) return body;
    return [];
  }
  function syncPlanPickerUi(state) {
    var loading = document.getElementById('plan-picker-loading');
    var list = document.getElementById('plan-picker-list');
    var pageLoading = document.getElementById('plans-loading');
    var pageError = document.getElementById('plans-error');
    if (state === 'loading') {
      if (loading) { loading.textContent = 'A carregar planos…'; loading.classList.remove('hidden'); }
      if (list) list.classList.add('hidden');
      if (pageLoading) pageLoading.classList.remove('hidden');
      if (pageError) pageError.classList.add('hidden');
      return;
    }
    if (state === 'error') {
      if (loading) {
        loading.innerHTML = 'Não foi possível carregar os planos. <button type="button" data-retry-plans class="ml-1 font-semibold text-brand underline">Tentar novamente</button>';
        loading.classList.remove('hidden');
      }
      if (list) list.classList.add('hidden');
      if (pageLoading) pageLoading.classList.add('hidden');
      if (pageError) {
        pageError.textContent = 'Não foi possível carregar os planos neste momento. Tente novamente dentro de alguns minutos.';
        pageError.classList.remove('hidden');
      }
      return;
    }
    if (loading) loading.classList.add('hidden');
    if (pageLoading) pageLoading.classList.add('hidden');
    if (pageError) pageError.classList.add('hidden');
    if (list && document.getElementById('plan-picker-modal').classList.contains('is-open')) {
      renderPlans(loadedPlans, list);
      list.classList.remove('hidden');
    }
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
    form.reset(); currentStep = 1; subdomainAvailable = false; submitting = false;
    form.querySelectorAll('.field-error').forEach(function (item) { item.textContent = ''; });
    form.querySelectorAll('.border-red-300, .bg-red-50\\/30').forEach(function (field) { field.classList.remove('border-red-300', 'bg-red-50/30'); });
    showSignupMessage('', '');
    document.getElementById('subdomain-availability').textContent = '';
    syncOther(); updateAddressFields(); syncSubdomainState(); showStep(1);
  }
  function showStep(step) {
    currentStep = step;
    form.querySelectorAll('.signup-step').forEach(function (item) { item.classList.toggle('hidden', Number(item.dataset.step) !== step); });
    form.querySelectorAll('[data-step-dot]').forEach(function (item) { item.className = 'signup-dot flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold ' + (Number(item.dataset.stepDot) <= step ? 'bg-slate-950 text-white' : 'bg-slate-200 text-slate-500'); });
    document.getElementById('signup-back').classList.toggle('hidden', step === 1);
    document.getElementById('signup-next').classList.toggle('hidden', step === 3);
    document.getElementById('signup-submit').classList.toggle('hidden', step !== 3);
    updateNavigationState();
  }
  function stepIsReady(step) {
    var value = function (name) { return String(form.elements[name] ? form.elements[name].value : '').trim(); };
    if (step === 1) {
      return value('name').replace(/[^\p{L}\p{N}]/gu, '').length >= 2 &&
        !!value('company_type') && (value('company_type') !== 'OTHER' || !!value('company_type_other')) &&
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('email')) && nuitDigits(value('nuit')).length === 9 &&
        /^[a-z0-9-]+$/.test(value('subdomain')) && subdomainAvailable;
    }
    if (step === 2) {
      syncAddressProvince();
      var phoneOk = !form.elements.phone_national.value || phoneDigits(form.elements.phone_national.value).length === 9;
      var phoneAltOk = !form.elements.phone_alt_national.value || phoneDigits(form.elements.phone_alt_national.value).length === 9;
      var countryOk = !!value('address_country') && searchableSelectionValid(form.elements.address_country);
      var provinceOk = value('address_country') !== 'MZ' || (!!form.elements.address_province_mz.value && searchableSelectionValid(form.elements.address_province_mz));
      return phoneOk && phoneAltOk && countryOk && provinceOk && !!value('address_country') && !!value('address_province');
    }
    return !!value('plan_code') && !!value('billing_cycle') && !!value('business_area') && searchableSelectionValid(form.elements.business_area) && (value('business_area') !== 'OTHER' || !!value('business_area_other'));
  }
  function updateNavigationState() {
    var next = document.getElementById('signup-next');
    var submit = document.getElementById('signup-submit');
    if (currentStep === 3) {
      submit.disabled = submitting;
      submit.classList.toggle('cursor-not-allowed', submitting);
      submit.classList.toggle('opacity-50', submitting);
      return;
    }
    var ready = stepIsReady(currentStep);
    next.disabled = !ready;
    next.classList.toggle('cursor-not-allowed', !ready);
    next.classList.toggle('opacity-50', !ready);
  }
  function fieldSlot(name) {
    if (name === 'business_area' && form.elements.business_area._searchInput) return { field: form.elements.business_area._searchInput, slot: form.elements.business_area.closest('label').querySelector('.field-error') };
    if (name === 'address_country' && form.elements.address_country._searchInput) return { field: form.elements.address_country._searchInput, slot: form.elements.address_country.closest('label').querySelector('.field-error') };
    if (name === 'address_province') {
      var provinceField = form.elements.address_country.value === 'MZ' ? (form.elements.address_province_mz._searchInput || form.elements.address_province_mz) : form.elements.address_province_text;
      return { field: provinceField, slot: provinceField.closest('label').querySelector('.field-error') };
    }
    if ((name === 'phone' || name === 'phone_alt') && form.elements[name + '_national']) { var phoneField = form.elements[name + '_national']; return { field: phoneField, slot: phoneField.closest('label').querySelector('.field-error') }; }
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
    if (first) { showStep(fieldSteps[first] || 1); setTimeout(function () { var item = fieldSlot(first); if (item && item.field) item.field.focus(); }, 100); }
  }
  function updateAddressFields() {
    var isMozambique = form.elements.address_country.value === 'MZ';
    var provinceSelect = form.elements.address_province_mz;
    var provinceText = form.elements.address_province_text;
    document.getElementById('mozambique-province-wrap').classList.toggle('hidden', !isMozambique);
    document.getElementById('foreign-province-wrap').classList.toggle('hidden', isMozambique);
    provinceSelect.disabled = !isMozambique; provinceSelect.required = isMozambique;
    provinceText.disabled = isMozambique; provinceText.required = !isMozambique;
    if (isMozambique) { provinceText.value = ''; if (!provinceSelect.value) provinceSelect.value = 'Cidade de Maputo'; }
    else { provinceSelect.value = ''; }
    syncSearchableSelect(form.elements.address_country); syncSearchableSelect(provinceSelect);
    syncAddressProvince(); updateAddressPreview(); clearError('address_country'); clearError('address_province');
  }
  function syncAddressProvince() {
    form.elements.address_province.value = form.elements.address_country.value === 'MZ' ? form.elements.address_province_mz.value.trim() : form.elements.address_province_text.value.trim();
  }
  function ensureAddressPreview() {
    var preview = document.getElementById('platform_company_address_preview');
    if (preview) return preview;
    var wrap = document.createElement('div');
    wrap.id = 'platform_company_address_preview_wrap';
    wrap.className = 'sm:col-span-2 rounded-lg border border-dashed border-slate-200 bg-white px-3 py-2.5';
    var label = document.createElement('p');
    label.className = 'text-xs font-semibold text-slate-500';
    label.textContent = 'Pré-visualização:';
    preview = document.createElement('div');
    preview.id = 'platform_company_address_preview';
    preview.className = 'mt-1 min-h-10 whitespace-pre-line text-sm leading-snug text-slate-800';
    preview.setAttribute('aria-live', 'polite');
    wrap.appendChild(label); wrap.appendChild(preview);
    var houseNumberLabel = form.elements.address_house_number.closest('label');
    houseNumberLabel.parentNode.insertBefore(wrap, houseNumberLabel.nextSibling);
    return preview;
  }
  function updateAddressPreview() {
    var countrySelect = form.elements.address_country;
    var countryOption = countrySelect.options[countrySelect.selectedIndex];
    var countryName = countryOption ? countryOption.textContent.trim() : 'Moçambique';
    var province = form.elements.address_province.value.trim();
    var street = form.elements.address_street.value.trim();
    var neighborhood = form.elements.address_neighborhood.value.trim();
    var houseNumber = form.elements.address_house_number.value.trim();
    var line1Parts = [];
    if (neighborhood) line1Parts.push('Bairro ' + neighborhood);
    var streetLine = street;
    if (streetLine && houseNumber) streetLine += ' n° ' + houseNumber;
    else if (!streetLine && houseNumber) streetLine = 'n° ' + houseNumber;
    if (streetLine) line1Parts.push(streetLine);
    var line2Parts = [];
    if (province) line2Parts.push(province);
    if (countryName) line2Parts.push(countryName);
    var text = (line1Parts.join(', ') + '\n' + line2Parts.join(' - ')).trim();
    if (text.length > 1900) text = text.substring(0, 1897) + '...';
    var preview = ensureAddressPreview();
    preview.textContent = text || 'Preencha os campos acima para ver a morada formatada.';
    preview.classList.toggle('italic', !text);
    preview.classList.toggle('text-slate-400', !text);
    preview.classList.toggle('text-slate-800', !!text);
  }
  function syncOther() {
    var option = form.elements.company_type.options[form.elements.company_type.selectedIndex];
    var needed = !!(option && option.dataset.requiresOther === '1');
    var wrap = document.getElementById('company-type-other-wrap');
    wrap.classList.toggle('hidden', !needed); form.elements.company_type_other.required = needed;
    if (!needed) form.elements.company_type_other.value = '';
    updateCompanyNamePreview();
  }
  function setupBusinessAreaField() {
    var original = form.elements.business_area; var oldLabel = original.closest('label');
    var row = document.createElement('div'); row.id = 'business-area-row'; row.className = 'grid gap-4';
    var selectLabel = document.createElement('label'); selectLabel.className = 'text-sm font-semibold text-slate-700'; selectLabel.textContent = 'Área de actividade ';
    var required = document.createElement('span'); required.className = 'text-red-600'; required.textContent = '*'; selectLabel.appendChild(required);
    var select = document.createElement('select'); select.name = 'business_area'; select.required = true; select.className = original.className;
    select.appendChild(new Option('A carregar áreas…', '')); selectLabel.appendChild(select);
    var selectError = document.createElement('span'); selectError.className = 'field-error text-xs text-red-600'; selectLabel.appendChild(selectError);
    var otherLabel = document.createElement('label'); otherLabel.id = 'business-area-other-wrap'; otherLabel.className = 'hidden text-sm font-semibold text-slate-700'; otherLabel.textContent = 'Outra área de actividade ';
    var otherRequired = required.cloneNode(true); otherLabel.appendChild(otherRequired);
    var otherInput = document.createElement('input'); otherInput.name = 'business_area_other'; otherInput.maxLength = 150; otherInput.className = original.className; otherLabel.appendChild(otherInput);
    var otherError = selectError.cloneNode(false); otherLabel.appendChild(otherError);
    row.appendChild(selectLabel); row.appendChild(otherLabel); oldLabel.replaceWith(row);
    select.addEventListener('change', syncBusinessArea); otherInput.addEventListener('input', updateNavigationState);
  }
  function syncBusinessArea() {
    var isOther = form.elements.business_area.value === 'OTHER'; var wrap = document.getElementById('business-area-other-wrap');
    wrap.classList.toggle('hidden', !isOther); form.elements.business_area_other.required = isOther;
    if (!isOther) form.elements.business_area_other.value = '';
    clearError('business_area'); clearError('business_area_other'); updateNavigationState();
  }
  function companyTypeDesignation() {
    var option = form.elements.company_type.options[form.elements.company_type.selectedIndex];
    if (option && option.value === 'OTHER') return String(form.elements.company_type_other.value || '').trim();
    return option ? String(option.dataset.designation || '').trim() : '';
  }
  function baseEndsWithDesignation(base, designation) {
    var normalize = function (text) { return String(text || '').trim().toLocaleLowerCase('pt').replace(/[.,;:()\[\]{}]/g, '').replace(/\s+/g, ' '); };
    var normalizedBase = normalize(base); var normalizedDesignation = normalize(designation);
    return !!normalizedDesignation && (normalizedBase === normalizedDesignation || normalizedBase.endsWith(' ' + normalizedDesignation));
  }
  function updateCompanyNamePreview() {
    var base = String(form.elements.name.value || '').trim().replace(/\s+/g, ' ');
    var designation = companyTypeDesignation();
    var show = form.elements.show_legal_designation.checked;
    var preview = base;
    if (base && show && designation && !baseEndsWithDesignation(base, designation)) preview += ', ' + designation;
    var output = document.getElementById('company-name-preview');
    var wrap = document.getElementById('company-name-preview-wrap');
    output.textContent = preview;
    wrap.classList.toggle('hidden', preview === '');
  }
  function validateStep(step) {
    var value = function (name) { var raw = String(form.elements[name] ? form.elements[name].value : '').trim(); return name === 'nuit' ? nuitDigits(raw) : raw; };
    Object.keys(fieldSteps).forEach(function (name) { if (fieldSteps[name] === step) clearError(name); });
    var errors = {};
    function add(name, text) { errors[name] = [{ message: text }]; }
    if (step === 1) { if (value('name').replace(/[^\p{L}\p{N}]/gu, '').length < 2) add('name', 'Indique um nome de empresa válido.'); if (!value('company_type')) add('company_type', 'Seleccione o tipo jurídico.'); if (value('company_type') === 'OTHER' && !value('company_type_other')) add('company_type_other', 'Indique o tipo jurídico.'); if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('email'))) add('email', 'Indique um e-mail válido.'); if (!/^\d{9}$/.test(value('nuit'))) add('nuit', value('nuit') ? 'Indique um NUIT válido com 9 dígitos.' : 'Indique o NUIT da empresa.'); }
    if (step === 1 && (!/^[a-z0-9-]+$/.test(value('subdomain')) || !subdomainAvailable)) add('subdomain', 'Indique um endereço disponível para a empresa.');
    if (step === 2) {
      syncAddressProvince();
      if (form.elements.phone_national.value && phoneDigits(form.elements.phone_national.value).length !== 9) add('phone', 'Indique um telefone válido com 9 dígitos.');
      if (form.elements.phone_alt_national.value && phoneDigits(form.elements.phone_alt_national.value).length !== 9) add('phone_alt', 'Indique um telefone alternativo válido com 9 dígitos.');
      if (!value('address_country')) add('address_country', 'Seleccione um país válido.');
      if (!value('address_province')) add('address_province', 'Indique a província ou cidade e o país na morada.');
    }
    if (step === 3) { if (!value('plan_code') || !value('billing_cycle')) add('plan_code', 'Seleccione um plano válido.'); if (!value('business_area')) add('business_area', 'Seleccione a área de actividade.'); if (value('business_area') === 'OTHER' && !value('business_area_other')) add('business_area_other', 'Indique a área de actividade.'); }
    showErrors(errors); return Object.keys(errors).length === 0;
  }
  function renderPlans(plans, list) {
    list = list || document.getElementById('plans-list'); list.textContent = '';
    plans.forEach(function (plan) {
      var code = String(plan.code || '').toUpperCase(); var featured = code === 'STANDARD';
      var availableCycles = plan.billing_cycles || ['monthly'];
      var cardCycle = availableCycles.indexOf(selectedBillingCycle) !== -1 ? selectedBillingCycle : availableCycles[0];
      var cycleDetails = billingCycles[cardCycle] || { label: cardCycle, period: '/' + cardCycle, months: 1 };
      var tone = code === 'FREE' ? 'emerald' : code === 'PRO' ? 'violet' : 'blue';
      var article = document.createElement('article'); article.className = 'signup-plan-card relative flex flex-col rounded-xl border ' + (code === 'FREE' ? 'border-emerald-200 hover:border-emerald-400' : code === 'PRO' ? 'border-violet-200 hover:border-violet-400' : 'border-blue-300 hover:border-blue-400') + ' bg-white p-5 shadow-soft transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-xl'; article.tabIndex = 0; article.setAttribute('role', 'button'); article.setAttribute('aria-label', 'Escolher plano ' + (plan.name || code)); article.dataset.planCode = code; article.dataset.planName = plan.name || code;
      var price = plan.price || {}; var quotas = plan.quotas || {};
      article.innerHTML = (featured ? '<span class="pointer-events-none absolute right-4 top-4 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-700">Mais popular</span>' : '') + '<div' + (featured ? ' class="pr-24"' : '') + '><p data-plan-name class="text-sm font-medium text-' + tone + '-700"></p><div class="mt-2 flex flex-nowrap items-baseline gap-1 whitespace-nowrap"><span data-plan-price class="text-2xl font-semibold tracking-tight text-slate-950"></span><span data-plan-period class="shrink-0 whitespace-nowrap text-xs text-slate-500"></span></div></div><div class="mt-5 rounded-lg bg-slate-50 px-3 py-2.5"><div class="flex items-center gap-2"><span class="shrink-0 text-' + tone + '-600" aria-hidden="true">✓</span><span class="text-sm font-medium text-slate-900">Todos os recursos do sistema</span></div></div><dl class="mt-5 flex-1 space-y-3 text-sm"><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Cotações por mês</dt><dd class="shrink-0 whitespace-nowrap font-medium text-slate-900"></dd></div><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Facturas por mês</dt><dd class="shrink-0 whitespace-nowrap font-medium text-slate-900"></dd></div><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Vendas por mês</dt><dd class="shrink-0 whitespace-nowrap font-medium text-slate-900"></dd></div><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Utilizadores</dt><dd class="shrink-0 whitespace-nowrap font-medium text-slate-900"></dd></div></dl><button type="button" data-plan-code="" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">Escolher plano</button>';
      article.querySelector('[data-plan-name]').textContent = plan.name || code; var priceAmount = article.querySelector('[data-plan-price]'); var pricePeriod = article.querySelector('[data-plan-period]'); priceAmount.textContent = formatMoney(Number(price.amount || 0) * cycleDetails.months); pricePeriod.textContent = (price.currency || 'MZN') + cycleDetails.period; var quotaNodes = article.querySelectorAll('dd'); quotaNodes[0].textContent = quotas.ct_per_month == null ? 'Ilimitado' : quotas.ct_per_month; quotaNodes[1].textContent = quotas.ft_per_month == null ? 'Ilimitado' : quotas.ft_per_month; quotaNodes[2].textContent = quotas.vd_per_month == null ? 'Ilimitado' : quotas.vd_per_month; quotaNodes[3].textContent = quotas.users == null ? 'Ilimitado' : quotas.users;
      var button = article.querySelector('button'); article.dataset.planPrice = priceAmount.textContent + ' ' + (price.currency || 'MZN') + cycleDetails.period; article.dataset.planCycles = JSON.stringify(availableCycles); article.dataset.billingCycle = cardCycle; button.dataset.planCode = code; list.appendChild(article);
    });
  }
  function loadPlans(force) {
    if (!force && plansPromise) return plansPromise;
    syncPlanPickerUi('loading');
    plansPromise = request('plans').then(function (r) {
      var plans = extractPlans(r.body);
      if (!r.response.ok || !plans.length) throw new Error(r.body.message || 'plans_unavailable');
      loadedPlans = plans;
      document.getElementById('plans-loading').classList.add('hidden');
      renderPlans(plans);
      var freeButton = document.getElementById('choose-free-plan');
      if (freeButton) freeButton.disabled = !plans.some(function (plan) { return String(plan.code || '').toUpperCase() === 'FREE'; });
      syncPlanPickerUi('ready');
      return plans;
    }).catch(function () {
      loadedPlans = [];
      syncPlanPickerUi('error');
      plansPromise = null;
      return [];
    });
    return plansPromise;
  }
  function openPlanPicker() {
    var picker = document.getElementById('plan-picker-modal');
    picker.classList.add('is-open');
    document.body.classList.add('modal-open');
    if (loadedPlans.length) {
      syncPlanPickerUi('ready');
      return;
    }
    syncPlanPickerUi('loading');
    loadPlans(true);
  }
  function loadTypes() {
    request('registration-options').then(function (r) {
      var data = r.body.data || {}; var select = form.elements.company_type; select.textContent = '';
      (data.company_type_groups || []).forEach(function (group) { var og = document.createElement('optgroup'); og.label = group.label; (data.company_types || []).filter(function (type) { return type.group === group.code; }).forEach(function (type) { var opt = new Option(type.label, type.code, false, type.code === 'LDA'); opt.dataset.requiresOther = type.requires_other ? '1' : '0'; opt.dataset.designation = type.designation || ''; og.appendChild(opt); }); select.appendChild(og); });
      var businessSelect = form.elements.business_area; businessSelect.textContent = ''; var businessPlaceholder = new Option('Seleccione uma área…', ''); businessPlaceholder.disabled = true; businessPlaceholder.selected = true; businessSelect.add(businessPlaceholder);
      (data.business_areas || []).forEach(function (area) { var option = new Option(area.label, area.value); option.dataset.requiresOther = area.requires_other ? '1' : '0'; businessSelect.add(option); });
      setupSearchableSelect(businessSelect, 'Pesquisar área de actividade…');
      syncOther(); syncBusinessArea();
    }).catch(function () { form.elements.company_type.innerHTML = '<option value="">Não foi possível carregar os tipos</option>'; form.elements.business_area.innerHTML = '<option value="">Não foi possível carregar as áreas</option>'; syncBusinessArea(); });
  }
  function setAvailability(ok, text) { subdomainAvailable = ok; var out = document.getElementById('subdomain-availability'); out.textContent = text; out.className = 'mt-1 block text-xs ' + (ok ? 'text-emerald-600' : 'text-red-600'); updateNavigationState(); }
  function checkSubdomain() { var value = String(form.elements.subdomain.value || '').trim().toLowerCase(); form.elements.subdomain.value = value; if (!/^[a-z0-9-]+$/.test(value)) { setAvailability(false, value ? 'Use apenas letras minúsculas, números e hífen.' : ''); return Promise.resolve(false); } return request('subdomains/check?subdomain=' + encodeURIComponent(value)).then(function (r) { var data = r.body.data || {}; var ok = r.response.ok && !!data.valid && !!data.available; setAvailability(ok, ok ? 'Endereço disponível.' : 'Este endereço não está disponível.'); return ok; }).catch(function () { setAvailability(false, 'Não foi possível verificar o endereço.'); return false; }); }
  function syncSubdomainState() {
    var hasName = String(form.elements.name.value || '').replace(/[^\p{L}\p{N}]/gu, '').length >= 2;
    form.elements.subdomain.disabled = !hasName;
    form.elements.subdomain.classList.toggle('cursor-not-allowed', !hasName);
    form.elements.subdomain.classList.toggle('bg-slate-100', !hasName);
    form.elements.subdomain.classList.toggle('text-slate-400', !hasName);
    form.elements.subdomain.placeholder = 'endereco-da-empresa';
    if (!hasName) { form.elements.subdomain.value = ''; subdomainAvailable = false; document.getElementById('subdomain-availability').textContent = ''; }
    return hasName;
  }
  function suggestSubdomain() { var name = String(form.elements.name.value || '').trim(); if (!syncSubdomainState() || form.elements.subdomain.value.trim() !== '') return; request('subdomains/suggest?name=' + encodeURIComponent(name)).then(function (r) { var data = r.body.data || {}; if (!r.response.ok || !data.subdomain || form.elements.subdomain.disabled || String(form.elements.name.value || '').trim() !== name || form.elements.subdomain.value.trim() !== '') return; form.elements.subdomain.value = data.subdomain; if (data.host) document.getElementById('subdomain-suffix').textContent = '.' + data.host.replace(data.subdomain + '.', ''); return checkSubdomain(); }); }
  function updateProgress(state) { var stages = { pending: 0, validating: 0, provisioning_dns: 1, provisioning_cloudways: 3, creating_subscription: 4, creating_tenant: 4, sending_invite: 4, initializing_billing: 5, billing_initialized: 6, finalizing: 6, completed: 7 }; var active = stages[state.status] == null ? 0 : stages[state.status]; document.querySelectorAll('#signup-progress-steps li').forEach(function (item) { var done = Number(item.dataset.stage) < active; var now = Number(item.dataset.stage) === active; item.textContent = (done ? '✓ ' : now ? '• ' : '○ ') + item.textContent.replace(/^[✓•○] /, ''); item.className = done ? 'text-emerald-700' : now ? 'font-semibold text-slate-900' : 'text-slate-500'; }); document.getElementById('signup-progress-message').textContent = state.message || 'A preparar a sua subscrição…'; }
  function monitor(id) { request('provisionings/' + encodeURIComponent(id)).then(function (r) { var state = r.body.data || r.body; updateProgress(state); if (state.status === 'completed') { localStorage.removeItem('sizotech_provisioning_id'); sessionStorage.removeItem('sizotech_registration_key'); var result = state.result || {}; var box = document.getElementById('signup-progress-result'); box.innerHTML = ''; box.className = 'mt-7 rounded-xl bg-emerald-50 px-4 py-4 text-sm text-emerald-900'; box.textContent = 'Subscrição concluída com sucesso. '; if (result.access_url) { var link = document.createElement('a'); link.href = result.access_url; link.className = 'font-semibold underline'; link.textContent = 'Aceder ao sistema'; box.appendChild(link); } box.classList.remove('hidden'); return; } if (state.status === 'failed') { localStorage.removeItem('sizotech_provisioning_id'); var failure = document.getElementById('signup-progress-result'); failure.textContent = state.message || 'Não foi possível concluir o cadastro.'; failure.className = 'mt-7 rounded-xl bg-red-50 px-4 py-4 text-sm text-red-700'; failure.classList.remove('hidden'); return; } setTimeout(function () { monitor(id); }, 2000); }).catch(function () { setTimeout(function () { monitor(id); }, 3000); }); }
  function choosePlan(card) { var picker = document.getElementById('plan-picker-modal'); picker.classList.remove('is-open'); resetForm(); form.elements.plan_code.value = card.dataset.planCode; document.getElementById('signup-plan-summary').textContent = 'Plano escolhido *: ' + card.dataset.planName + ' — ' + card.dataset.planPrice; var cycle = form.elements.billing_cycle; cycle.textContent = ''; JSON.parse(card.dataset.planCycles || '["monthly"]').forEach(function (name) { var details = billingCycles[name]; cycle.add(new Option(details ? details.label : name, name, false, name === card.dataset.billingCycle)); }); cycle.value = card.dataset.billingCycle || 'monthly'; modal.classList.add('is-open'); document.body.classList.add('modal-open'); }
  document.addEventListener('click', function (event) { var card = event.target.closest('.signup-plan-card'); if (card) choosePlan(card); });
  document.addEventListener('keydown', function (event) { var card = event.target.closest && event.target.closest('.signup-plan-card'); if (card && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); choosePlan(card); } });
  document.querySelectorAll('[data-open-plan-picker]').forEach(function (button) { button.addEventListener('click', openPlanPicker); });
  document.getElementById('choose-free-plan').addEventListener('click', function () { var freeCard = document.querySelector('#plans-list .signup-plan-card[data-plan-code="FREE"]'); if (freeCard) choosePlan(freeCard); else openPlanPicker(); });
  document.addEventListener('click', function (event) {
    if (!event.target.closest('[data-retry-plans]')) return;
    event.preventDefault();
    loadPlans(true);
  });
  document.querySelectorAll('[data-billing-cycle]').forEach(function (button) { button.addEventListener('click', function () { selectedBillingCycle = button.dataset.billingCycle; document.querySelectorAll('[data-billing-cycle]').forEach(function (item) { var active = item === button; item.setAttribute('aria-pressed', active ? 'true' : 'false'); item.className = active ? 'rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm' : 'rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-slate-950'; }); if (loadedPlans.length) renderPlans(loadedPlans); }); });
  document.querySelectorAll('[data-close-plan-picker]').forEach(function (button) { button.addEventListener('click', function () { document.getElementById('plan-picker-modal').classList.remove('is-open'); document.body.classList.remove('modal-open'); }); });
  document.querySelectorAll('[data-signup-close]').forEach(function (button) { button.addEventListener('click', function () { modal.classList.remove('is-open'); document.body.classList.remove('modal-open'); resetForm(); }); });
  setupPhoneField('phone'); setupPhoneField('phone_alt');
  setupBusinessAreaField();
  setupSearchableSelect(form.elements.address_country, 'Pesquisar país…');
  setupSearchableSelect(form.elements.address_province_mz, 'Pesquisar província…');
  form.querySelector('[data-step="1"]').appendChild(form.elements.subdomain.closest('label'));
  syncSubdomainState();
  form.elements.company_type.addEventListener('change', syncOther);
  form.elements.name.addEventListener('input', updateCompanyNamePreview);
  form.elements.company_type_other.addEventListener('input', updateCompanyNamePreview);
  form.elements.show_legal_designation.addEventListener('change', updateCompanyNamePreview);
  form.elements.nuit.addEventListener('input', function () { var formatted = formatNuit(this.value); if (this.value !== formatted) this.value = formatted; });
  form.elements.nuit.addEventListener('keydown', function (event) { if (event.key !== 'Backspace' || this.selectionStart !== this.selectionEnd || this.selectionEnd !== this.value.length) return; var digits = nuitDigits(this.value); if (!digits) return; event.preventDefault(); this.value = formatNuit(digits.slice(0, -1)); this.setSelectionRange(this.value.length, this.value.length); clearError('nuit'); });
  form.addEventListener('formdata', function (event) { event.formData.set('nuit', nuitDigits(form.elements.nuit.value)); event.formData.set('phone', form.elements.phone.value); event.formData.set('phone_alt', form.elements.phone_alt.value); event.formData.delete('phone_national'); event.formData.delete('phone_alt_national'); });
  form.addEventListener('reset', function () { setTimeout(function () { form.elements.phone_national.dispatchEvent(new Event('input')); form.elements.phone_alt_national.dispatchEvent(new Event('input')); syncSearchableSelect(form.elements.address_country, true); syncSearchableSelect(form.elements.address_province_mz, true); syncSearchableSelect(form.elements.business_area, true); syncBusinessArea(); }, 0); });
  form.elements.address_country.addEventListener('change', updateAddressFields);
  form.elements.address_province_mz.addEventListener('change', function () { syncAddressProvince(); updateAddressPreview(); clearError('address_province'); });
  form.elements.address_province_text.addEventListener('input', function () { syncAddressProvince(); updateAddressPreview(); clearError('address_province'); });
  [form.elements.address_street, form.elements.address_neighborhood, form.elements.address_house_number].forEach(function (field) { field.addEventListener('input', updateAddressPreview); });
  form.elements.name.addEventListener('input', function () { clearTimeout(suggestTimer); if (!syncSubdomainState()) return; if (!form.elements.subdomain.value.trim()) suggestTimer = setTimeout(suggestSubdomain, 500); });
  form.elements.subdomain.addEventListener('input', function () { clearTimeout(checkTimer); subdomainAvailable = false; var status = document.getElementById('subdomain-availability'); status.textContent = this.value.trim() ? 'A verificar…' : ''; status.className = 'mt-1 block text-xs text-slate-500'; updateNavigationState(); checkTimer = setTimeout(checkSubdomain, 400); });
  form.querySelectorAll('input, select').forEach(function (field) { field.addEventListener('input', function () { clearError(field.name); updateNavigationState(); }); field.addEventListener('change', function () { clearError(field.name); updateNavigationState(); }); });
  form.addEventListener('keydown', function (event) {
    if (event.key !== 'Enter' || event.isComposing || event.target.closest('button')) return;
    if (event.target.matches('[data-select-search]')) { event.preventDefault(); return; }
    if (currentStep >= 3) return;
    if (event.target.matches('input[list]')) {
      var searchableSelect = event.target === form.elements.address_country._searchInput ? form.elements.address_country : form.elements.address_province_mz;
      var selectedOption = searchableSelect.options[searchableSelect.selectedIndex];
      if (!selectedOption || event.target.value.trim() !== selectedOption.textContent.trim()) { event.preventDefault(); return; }
    }
    event.preventDefault();
    document.getElementById('signup-next').click();
  });
  document.getElementById('signup-next').addEventListener('click', function () { if (validateStep(currentStep)) showStep(currentStep + 1); });
  document.getElementById('signup-back').addEventListener('click', function () { showStep(currentStep - 1); });
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (submitting) return;
    showSignupMessage('', '');
    syncAddressProvince();
    if (!validateStep(3)) {
      showSignupMessage('Verifique os campos assinalados antes de concluir o cadastro.', 'error');
      return;
    }
    var button = document.getElementById('signup-submit');
    submitting = true;
    button.disabled = true;
    button.textContent = 'A processar…';
    updateNavigationState();

    var data = {
      name: String(form.elements.name.value || '').trim(),
      company_type: String(form.elements.company_type.value || '').trim(),
      company_type_other: String(form.elements.company_type_other.value || '').trim(),
      show_legal_designation: !!form.elements.show_legal_designation.checked,
      email: String(form.elements.email.value || '').trim(),
      nuit: nuitDigits(form.elements.nuit.value),
      phone: String(form.elements.phone.value || '').trim(),
      phone_alt: String(form.elements.phone_alt.value || '').trim(),
      business_area: String(form.elements.business_area.value || '').trim(),
      business_area_other: String(form.elements.business_area_other.value || '').trim(),
      address_country: String(form.elements.address_country.value || '').trim(),
      address_province: String(form.elements.address_province.value || '').trim(),
      address_street: String(form.elements.address_street.value || '').trim(),
      address_neighborhood: String(form.elements.address_neighborhood.value || '').trim(),
      address_house_number: String(form.elements.address_house_number.value || '').trim(),
      plan_code: String(form.elements.plan_code.value || '').trim(),
      billing_cycle: String(form.elements.billing_cycle.value || '').trim(),
      subdomain: String(form.elements.subdomain.value || '').trim().toLowerCase()
    };

    request('registrations', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': currentCsrf(),
        'Idempotency-Key': getKey()
      },
      body: JSON.stringify(data)
    }).then(function (r) {
      submitting = false;
      button.disabled = false;
      button.textContent = 'Concluir cadastro';
      updateNavigationState();

      var body = r.body || {};
      var provisioningId = body.provisioning_id || (body.data && body.data.provisioning_id);
      if ((r.response.status === 202 || r.response.status === 201) && provisioningId) {
        localStorage.setItem('sizotech_provisioning_id', String(provisioningId));
        modal.classList.remove('is-open');
        document.body.classList.remove('modal-open');
        progressModal.classList.add('is-open');
        document.body.classList.add('modal-open');
        updateProgress(body.data || body);
        monitor(provisioningId);
        return;
      }
      if (r.response.status === 201 || r.response.ok) {
        var result = body.company || (body.data && body.data.company) || body.data || body;
        modal.classList.remove('is-open');
        progressModal.classList.add('is-open');
        document.body.classList.add('modal-open');
        document.getElementById('signup-progress-message').textContent = body.message || 'Cadastro concluído com sucesso.';
        var box = document.getElementById('signup-progress-result');
        box.innerHTML = '';
        box.className = 'mt-7 rounded-xl bg-emerald-50 px-4 py-4 text-sm text-emerald-900';
        box.textContent = 'A empresa foi criada. ';
        if (result && result.access_url) {
          var link = document.createElement('a');
          link.href = result.access_url;
          link.className = 'font-semibold underline';
          link.textContent = 'Aceder ao sistema';
          box.appendChild(link);
        }
        box.classList.remove('hidden');
        sessionStorage.removeItem('sizotech_registration_key');
        return;
      }
      if (body.errors) {
        showErrors(body.errors);
        showSignupMessage(body.message || 'Existem erros nos dados enviados. Corrija e tente novamente.', 'error');
        return;
      }
      showSignupMessage(body.message || 'Não foi possível concluir o cadastro neste momento. Tente novamente.', 'error');
    }).catch(function () {
      submitting = false;
      button.disabled = false;
      button.textContent = 'Concluir cadastro';
      updateNavigationState();
      showSignupMessage('Não foi possível comunicar com o servidor. Verifique a ligação e tente novamente.', 'error');
    });
  });
  var pending = localStorage.getItem('sizotech_provisioning_id'); if (pending) { progressModal.classList.add('is-open'); document.body.classList.add('modal-open'); monitor(pending); }
  updateNavigationState(); loadPlans(); loadTypes();
})();
