(function () {
  'use strict';

  var api = 'api/sizotech/';
  var form = document.getElementById('signup-form');
  var modal = document.getElementById('signup-modal');
  var progressModal = document.getElementById('signup-progress-modal');
  var verificationModal = document.getElementById('email-verification-modal');
  var verificationForm = document.getElementById('email-verification-form');
  if (!form || !modal || !progressModal || !verificationModal || !verificationForm) return;
  form.elements.company_type.closest('label').classList.add('sm:col-span-2');
  form.elements.email.closest('label').classList.remove('sm:col-span-2');
  form.elements.nuit.maxLength = 11;
  form.elements.nuit.setAttribute('data-mz-nuit', '');

  var currentStep = 1;
  var totalSteps = 3;
  var loadedPlans = [];
  var plansPromise = null;
  var selectedBillingCycle = 'monthly';
  var subdomainAvailable = false;
  var suggestTimer = null;
  var checkTimer = null;
  var submitting = false;
  var idempotencyRetry = false;
  var monitorTimer = null;
  var emailVerificationToken = '';
  var emailVerifiedFor = '';
  var emailCodeSentFor = '';
  var emailVerificationPending = false;
  var resendCooldownTimer = null;
  var pickerModal = document.getElementById('plan-picker-modal');
  var otpInputs = Array.prototype.slice.call(verificationModal.querySelectorAll('.otp-input'));
  var confirmButton = document.getElementById('confirmButton');
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
    var input = document.createElement('input'); input.type = 'text'; input.placeholder = placeholder; input.autocomplete = 'off'; input.dataset.selectSearch = select.name; input.className = select.className.replace('mt-1.5', ''); input.setAttribute('role', 'combobox'); input.setAttribute('aria-expanded', 'false');
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
    input.addEventListener('focus', function () { if (select.value === '') input.value = ''; });
    input.addEventListener('click', function () { renderMenu(input.value); });
    input.addEventListener('input', function () { renderMenu(input.value); clearError(select.name); updateNavigationState(); });
    input.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') { closeMenu(); return; }
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); renderMenu(input.value); }
    });
    input.addEventListener('blur', function () { setTimeout(function () { var current = source.filter(function (item) { return item.value === select.value; })[0]; input.value = current ? current.label : ''; closeMenu(); updateNavigationState(); }, 100); });
    select._renderSearchOptions('', false);
    return input;
  }
  function syncSearchableSelect(select, useDefault) { if (select && select._searchInput) { select._searchInput.value = ''; select._renderSearchOptions('', !!useDefault); } }
  function searchableSelectionValid(select) {
    if (!select || !select.value) return false;
    if (!select._searchInput) return true;
    var typed = select._searchInput.value.trim();
    var current = (select._searchOptions || []).filter(function (item) { return item.value === select.value; })[0];
    if (!typed) {
      if (current) select._searchInput.value = current.label;
      return !!current;
    }
    if (current && typed.toLocaleLowerCase('pt') === current.label.toLocaleLowerCase('pt')) return true;
    return (select._searchOptions || []).some(function (item) {
      return !item.disabled && item.label.toLocaleLowerCase('pt') === typed.toLocaleLowerCase('pt');
    });
  }
  function currentCsrf() {
    return (form.elements.csrf && form.elements.csrf.value) || '';
  }
  var modalCloseTimer = null;
  function hideModalInstant(el) {
    if (!el) return;
    el.classList.remove('is-open', 'is-closing');
    el.classList.add('hidden');
  }
  function closeModalAnimated(el, onDone) {
    if (!el || el.classList.contains('hidden')) {
      if (onDone) onDone();
      return;
    }
    if (!el.classList.contains('is-open') && !el.classList.contains('is-closing')) {
      el.classList.add('hidden');
      if (onDone) onDone();
      return;
    }
    el.classList.remove('is-open');
    el.classList.add('is-closing');
    var done = false;
    var finish = function () {
      if (done) return;
      done = true;
      el.classList.remove('is-closing');
      el.classList.add('hidden');
      if (onDone) onDone();
    };
    var onEnd = function (event) {
      if (event.target !== el) return;
      el.removeEventListener('transitionend', onEnd);
      finish();
    };
    el.addEventListener('transitionend', onEnd);
    setTimeout(finish, 340);
  }
  function closeAllModals(options) {
    var animate = !!(options && options.animate);
    var onDone = options && options.onDone;
    if (monitorTimer) {
      clearTimeout(monitorTimer);
      monitorTimer = null;
    }
    if (typeof clearProgressTimer === 'function') clearProgressTimer();
    if (modalCloseTimer) {
      clearTimeout(modalCloseTimer);
      modalCloseTimer = null;
    }
    var list = [pickerModal, modal, verificationModal, progressModal].filter(Boolean);
    if (!animate) {
      list.forEach(hideModalInstant);
      document.body.classList.remove('modal-open');
      if (onDone) onDone();
      return;
    }
    var pending = list.filter(function (el) {
      return el.classList.contains('is-open') || el.classList.contains('is-closing');
    });
    if (!pending.length) {
      list.forEach(hideModalInstant);
      document.body.classList.remove('modal-open');
      if (onDone) onDone();
      return;
    }
    var left = pending.length;
    pending.forEach(function (el) {
      closeModalAnimated(el, function () {
        left -= 1;
        if (left > 0) return;
        document.body.classList.remove('modal-open');
        if (onDone) onDone();
      });
    });
  }
  function openModal(target) {
    closeAllModals();
    if (!target) return;
    target.classList.remove('hidden', 'is-closing');
    void target.offsetWidth;
    target.classList.add('is-open');
    document.body.classList.add('modal-open');
  }
  function switchModal(from, to, onDone) {
    if (!to) {
      if (onDone) onDone();
      return;
    }
    var finish = function () {
      to.classList.remove('hidden', 'is-closing');
      void to.offsetWidth;
      to.classList.add('is-open');
      document.body.classList.add('modal-open');
      if (onDone) onDone();
    };
    if (!from || from.classList.contains('hidden')) {
      finish();
      return;
    }
    closeModalAnimated(from, finish);
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
  function newKey() {
    sessionStorage.removeItem('sizotech_registration_key');
    return getKey();
  }
  function isIdempotencyConflict(body) {
    var text = String((body && body.message) || '') + JSON.stringify((body && body.errors) || {});
    return /idempot/i.test(text);
  }
  function resetForm() {
    form.reset(); currentStep = 1; subdomainAvailable = false; submitting = false; idempotencyRetry = false;
    emailVerificationToken = ''; emailVerifiedFor = ''; emailCodeSentFor = '';
    emailVerificationPending = false;
    clearResendCountdown();
    clearOtpInputs();
    sessionStorage.removeItem('sizotech_registration_key');
    form.querySelectorAll('.field-error').forEach(function (item) { item.textContent = ''; });
    form.querySelectorAll('.border-red-300, .bg-red-50\\/30').forEach(function (field) { field.classList.remove('border-red-300', 'bg-red-50/30'); });
    showSignupMessage('', '');
    document.getElementById('subdomain-availability').textContent = '';
    var planTotal = document.getElementById('signup-plan-total');
    if (planTotal) planTotal.classList.add('hidden');
    syncOther(); updateAddressFields(); syncSubdomainState(); showStep(1);
  }
  function formApiStep(uiStep) {
    if (uiStep === 1 || uiStep === 2 || uiStep === 3) return uiStep;
    return 0;
  }
  function clearOtpInputs() {
    otpInputs.forEach(function (input) {
      input.value = '';
      input.classList.remove('error');
    });
    var err = document.getElementById('email-verification-error');
    if (err) {
      err.textContent = 'O código introduzido é inválido. Verifique e tente novamente.';
      err.classList.add('hidden');
    }
    updateConfirmButton();
  }
  function otpCode() {
    var digits = '';
    otpInputs.forEach(function (input) { digits += String(input.value || '').replace(/\D/g, ''); });
    return digits.slice(0, 4);
  }
  function updateConfirmButton() {
    if (!confirmButton) return;
    confirmButton.disabled = otpCode().length !== 4 || submitting;
  }
  function maskEmailLocal(email) {
    email = String(email || '').trim();
    var at = email.indexOf('@');
    if (at < 1) return email;
    var local = email.slice(0, at);
    var domain = email.slice(at + 1);
    if (local.length <= 1) return local + '***@' + domain;
    if (local.length === 2) return local.charAt(0) + '***' + local.charAt(1) + '@' + domain;
    return local.charAt(0) + '***' + local.charAt(local.length - 1) + '@' + domain;
  }
  function clearResendCountdown() {
    if (resendCooldownTimer) {
      clearInterval(resendCooldownTimer);
      resendCooldownTimer = null;
    }
    var countdown = document.getElementById('resendCountdown');
    var button = document.getElementById('resendButton');
    if (countdown) countdown.classList.add('hidden');
    if (button) {
      button.disabled = false;
      button.textContent = 'Reenviar código';
      button.classList.add('hidden');
    }
  }
  function startResendCountdown(seconds) {
    var countdown = document.getElementById('resendCountdown');
    var button = document.getElementById('resendButton');
    if (!countdown || !button) return;
    clearResendCountdown();
    var left = Math.max(1, Number(seconds) || 60);
    button.classList.add('hidden');
    countdown.classList.remove('hidden');
    countdown.textContent = 'Reenviar em ' + left + 's';
    resendCooldownTimer = setInterval(function () {
      left -= 1;
      if (left <= 0) {
        clearInterval(resendCooldownTimer);
        resendCooldownTimer = null;
        countdown.classList.add('hidden');
        button.classList.remove('hidden');
        return;
      }
      countdown.textContent = 'Reenviar em ' + left + 's';
    }, 1000);
  }
  function invalidateEmailVerification() {
    emailVerificationToken = '';
    emailVerifiedFor = '';
    emailCodeSentFor = '';
    clearOtpInputs();
  }
  function showVerificationError(message) {
    var err = document.getElementById('email-verification-error');
    if (err) {
      err.textContent = message || 'O código introduzido é inválido. Verifique e tente novamente.';
      err.classList.remove('hidden');
    }
    otpInputs.forEach(function (input) { input.classList.add('error'); });
    if (otpInputs[0]) otpInputs[0].focus();
  }
  function clearVerificationError() {
    var err = document.getElementById('email-verification-error');
    if (err) err.classList.add('hidden');
    otpInputs.forEach(function (input) { input.classList.remove('error'); });
  }
  function animateVerificationState(el) {
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.remove('state-enter');
    void el.offsetWidth;
    el.classList.add('state-enter');
  }
  function setEmailVerificationView(view, options) {
    options = options || {};
    var sending = document.getElementById('email-verification-sending');
    var sendError = document.getElementById('email-verification-send-error');
    var codeWrap = document.getElementById('email-verification-code');
    var verified = document.getElementById('email-verification-verified');
    var subtitle = document.getElementById('email-verification-subtitle');
    var title = document.getElementById('email-verification-title');
    var masked = options.masked || maskEmailLocal(form.elements.email.value);

    [sending, sendError, codeWrap, verified].forEach(function (el) {
      if (el) el.classList.add('hidden');
    });

    if (title) {
      if (view === 'verified') title.textContent = 'E-mail verificado';
      else if (view === 'error') title.textContent = 'Verificação do e-mail';
      else title.textContent = 'Verificação do e-mail';
    }

    if (subtitle) {
      if (view === 'sending') {
        subtitle.classList.remove('hidden');
        subtitle.textContent = 'Estamos a preparar a verificação do seu endereço de e-mail.';
      } else {
        subtitle.classList.add('hidden');
      }
    }

    if (view === 'sending') {
      var sendMask = document.getElementById('email-verification-sending-mask');
      if (sendMask) sendMask.textContent = masked;
      animateVerificationState(sending);
      return;
    }
    if (view === 'error') {
      var errText = document.getElementById('email-verification-send-error-text');
      if (errText) {
        errText.textContent = options.message || 'Não foi possível enviar o código de verificação. Tente novamente mais tarde.';
      }
      animateVerificationState(sendError);
      return;
    }
    if (view === 'form') {
      var mask = document.getElementById('maskedEmail');
      if (mask) mask.textContent = masked;
      animateVerificationState(codeWrap);
      return;
    }
    if (view === 'verified') {
      animateVerificationState(verified);
    }
  }
  function openEmailVerificationModal(options) {
    options = options || {};
    var view = options.view || 'sending';
    var masked = options.masked || maskEmailLocal(form.elements.email.value);
    clearOtpInputs();
    var resendMessage = document.getElementById('resendMessage');
    if (resendMessage) resendMessage.classList.add('hidden');
    if (confirmButton) {
      confirmButton.innerHTML = 'Confirmar código';
      confirmButton.disabled = true;
    }
    setEmailVerificationView(view, { masked: masked, message: options.message });
    if (verificationModal.classList.contains('is-open')) {
      if (view === 'form') {
        setTimeout(function () {
          if (otpInputs[0]) otpInputs[0].focus();
        }, 80);
      }
      return;
    }
    switchModal(modal, verificationModal, function () {
      if (view !== 'form') return;
      setTimeout(function () {
        if (otpInputs[0]) otpInputs[0].focus();
      }, 180);
    });
  }
  function showEmailVerificationForm(masked) {
    setEmailVerificationView('form', { masked: masked });
    if (!resendCooldownTimer) {
      startResendCountdown(60);
    }
    setTimeout(function () {
      if (otpInputs[0]) otpInputs[0].focus();
    }, 150);
  }
  function showEmailVerificationSendError(message) {
    setEmailVerificationView('error', { message: message });
  }
  function beginEmailVerificationFlow(force) {
    var email = String(form.elements.email.value || '').trim().toLowerCase();
    var masked = maskEmailLocal(email);
    emailVerificationPending = true;
    setActionBusy(true);
    openEmailVerificationModal({ view: 'sending', masked: masked });

    return request('registrations/validate', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': currentCsrf() },
      body: JSON.stringify(Object.assign({ step: 1 }, formPayload()))
    }).then(function (r) {
      if (!emailVerificationPending) {
        setActionBusy(false);
        return { ok: false, cancelled: true };
      }
      var body = r.body || {};
      if (!(r.response.ok && body.status === 'ok')) {
        emailVerificationPending = false;
        setActionBusy(false);
        switchModal(verificationModal, modal, function () {
          if (body.errors) {
            showErrors(body.errors);
            showSignupMessage('', '');
          } else {
            showSignupMessage(body.message || 'Não foi possível validar os dados. Tente novamente.', 'error');
          }
        });
        return { ok: false };
      }

      if (!force && emailCodeSentFor === email) {
        emailVerificationPending = false;
        setActionBusy(false);
        showEmailVerificationForm(masked);
        return { ok: true, masked: masked, already: true };
      }

      return sendEmailVerificationCode(true).then(function (result) {
        setActionBusy(false);
        if (!emailVerificationPending) return result;
        emailVerificationPending = false;
        if (result && result.ok) {
          showEmailVerificationForm(result.masked || masked);
        } else {
          showEmailVerificationSendError(result && result.message);
        }
        return result;
      });
    }).catch(function () {
      setActionBusy(false);
      if (!emailVerificationPending) return { ok: false };
      emailVerificationPending = false;
      showEmailVerificationSendError('Não foi possível comunicar com o servidor. Verifique a ligação e tente novamente.');
      return { ok: false };
    });
  }
  function resumeSignupAfterVerification() {
    emailVerificationPending = false;
    setEmailVerificationView('verified');
    setTimeout(function () {
      switchModal(verificationModal, modal, function () {
        showStep(2);
      });
    }, 700);
  }
  function cancelEmailVerification() {
    emailVerificationPending = false;
    closeAllModals({ animate: true, onDone: resetForm });
  }
  function showStep(step) {
    currentStep = step;
    form.querySelectorAll('.signup-step').forEach(function (item) { item.classList.toggle('hidden', Number(item.dataset.step) !== step); });
    form.querySelectorAll('[data-step-dot]').forEach(function (item) { item.className = 'signup-dot flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold ' + (Number(item.dataset.stepDot) <= step ? 'bg-slate-950 text-white' : 'bg-slate-200 text-slate-500'); });
    document.getElementById('signup-back').classList.toggle('hidden', step === 1);
    document.getElementById('signup-next').classList.toggle('hidden', step === totalSteps);
    document.getElementById('signup-submit').classList.toggle('hidden', step !== totalSteps);
    updateNavigationState();
  }
  function updateNavigationState() {
    var next = document.getElementById('signup-next');
    var submit = document.getElementById('signup-submit');
    next.disabled = false;
    submit.disabled = submitting && currentStep === totalSteps;
    next.classList.remove('cursor-not-allowed', 'opacity-50');
    submit.classList.remove('cursor-not-allowed', 'opacity-50');
    next.style.pointerEvents = submitting && currentStep !== totalSteps ? 'none' : '';
    submit.style.pointerEvents = submitting && currentStep === totalSteps ? 'none' : '';
  }
  function fieldSlot(name) {
    if (name === 'business_area' && form.elements.business_area && form.elements.business_area._searchInput) {
      var businessLabel = form.elements.business_area.closest('label');
      return { field: form.elements.business_area._searchInput, slot: businessLabel ? businessLabel.querySelector('.field-error') : null };
    }
    if (name === 'address_country' && form.elements.address_country && form.elements.address_country._searchInput) {
      var countryLabel = form.elements.address_country.closest('label');
      return { field: form.elements.address_country._searchInput, slot: countryLabel ? countryLabel.querySelector('.field-error') : null };
    }
    if (name === 'address_province') {
      var provinceField = form.elements.address_country.value === 'MZ' ? (form.elements.address_province_mz._searchInput || form.elements.address_province_mz) : form.elements.address_province_text;
      var provinceLabel = provinceField && provinceField.closest('label');
      return { field: provinceField, slot: provinceLabel ? provinceLabel.querySelector('.field-error') : null };
    }
    if ((name === 'phone' || name === 'phone_alt') && form.elements[name + '_national']) {
      var phoneField = form.elements[name + '_national'];
      var phoneLabel = phoneField.closest('label');
      return { field: phoneField, slot: phoneLabel ? phoneLabel.querySelector('.field-error') : null };
    }
    var field = form.querySelector('[name="' + name + '"]');
    if (!field) return null;
    var label = field.closest('label');
    return { field: field, slot: label ? label.querySelector('.field-error') : document.getElementById('signup-message') };
  }
  function clearError(name) { var item = fieldSlot(name); if (item && item.field) { item.field.classList.remove('border-red-300', 'bg-red-50/30'); if (item.slot && item.slot.id !== 'signup-message') item.slot.textContent = ''; } }
  function setError(name, text) { var item = fieldSlot(name); if (item && item.field) { item.field.classList.add('border-red-300', 'bg-red-50/30'); if (item.slot) item.slot.textContent = text; } }
  function fieldErrorMessage(name, entry) {
    var message = entry && entry.message ? String(entry.message) : 'Dados inválidos.';
    if (name === 'nuit' || /nuit/i.test(message)) {
      return 'O NUIT informado não parece ser válido.';
    }
    return message;
  }
  function showErrors(errors) {
    var first = null;
    Object.keys(errors || {}).forEach(function (name) {
      var entry = errors[name] && errors[name][0];
      setError(name, fieldErrorMessage(name, entry));
      if (!first || (fieldSteps[name] || 99) < (fieldSteps[first] || 99)) first = name;
    });
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
  function companyDisplayName() {
    var base = String(form.elements.name.value || '').trim().replace(/\s+/g, ' ');
    var designation = companyTypeDesignation();
    var show = form.elements.show_legal_designation && form.elements.show_legal_designation.checked;
    if (base && show && designation && !baseEndsWithDesignation(base, designation)) {
      return base + ', ' + designation;
    }
    return base;
  }
  function updateCompanyNamePreview() {
    var preview = companyDisplayName();
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
    if (step === 1) {
      if (value('name').replace(/[^\p{L}\p{N}]/gu, '').length < 2) add('name', 'Indique um nome de empresa válido.');
      if (!value('company_type')) add('company_type', 'Seleccione o tipo jurídico.');
      if (value('company_type') === 'OTHER' && !value('company_type_other')) add('company_type_other', 'Indique o tipo jurídico.');
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('email'))) add('email', 'Indique um e-mail válido.');
      if (!/^\d{9}$/.test(value('nuit'))) add('nuit', 'O NUIT informado não parece ser válido.');
      if (!/^[a-z0-9-]+$/.test(value('subdomain')) || !subdomainAvailable) add('subdomain', 'Indique um endereço disponível para a empresa.');
    }
    if (step === 2) {
      syncAddressProvince();
      if (form.elements.phone_national.value && phoneDigits(form.elements.phone_national.value).length !== 9) add('phone', 'Indique um telefone válido com 9 dígitos.');
      if (form.elements.phone_alt_national.value && phoneDigits(form.elements.phone_alt_national.value).length !== 9) add('phone_alt', 'Indique um telefone alternativo válido com 9 dígitos.');
      if (!value('address_country') || !searchableSelectionValid(form.elements.address_country)) add('address_country', 'Seleccione um país válido.');
      if (!value('address_province')) add('address_province', 'Indique a província ou cidade e o país na morada.');
      else if (value('address_country') === 'MZ' && !searchableSelectionValid(form.elements.address_province_mz)) add('address_province', 'Seleccione uma província válida.');
    }
    if (step === 3) {
      if (!value('plan_code')) add('plan_code', 'Seleccione um plano válido.');
      else if (String(value('plan_code')).toUpperCase() !== 'FREE' && !value('billing_cycle')) add('billing_cycle', 'Seleccione o ciclo de faturação.');
      if (!value('business_area') || !searchableSelectionValid(form.elements.business_area)) add('business_area', 'Seleccione a área de actividade.');
      if (value('business_area') === 'OTHER' && !value('business_area_other')) add('business_area_other', 'Indique a área de actividade.');
    }
    showErrors(errors);
    return Object.keys(errors).length === 0;
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
    openModal(pickerModal);
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
      updateTestModeLabel(!!data.production_test_mode);
    }).catch(function () { form.elements.company_type.innerHTML = '<option value="">Não foi possível carregar os tipos</option>'; form.elements.business_area.innerHTML = '<option value="">Não foi possível carregar as áreas</option>'; syncBusinessArea(); });
  }
  function updateTestModeLabel(active) {
    var label = document.getElementById('signup-subscription-label');
    if (label) {
      label.textContent = active ? 'Subscrição · Modo de teste activado' : 'Subscrição';
    }
    var notice = document.getElementById('signup-test-mode-notice');
    if (notice) {
      notice.classList.toggle('hidden', !active);
    }
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
  function suggestSubdomain() {
    var name = String(form.elements.name.value || '').trim();
    if (!syncSubdomainState() || form.elements.subdomain.value.trim() !== '') return;
    request('subdomains/suggest?name=' + encodeURIComponent(name)).then(function (r) {
      var data = r.body.data || {};
      var suggested = String(data.subdomain || '').trim().toLowerCase();
      if (!r.response.ok || !suggested || form.elements.subdomain.disabled) return;
      if (String(form.elements.name.value || '').trim() !== name || form.elements.subdomain.value.trim() !== '') return;
      // Nunca aplicar sugestões com sufixo numérico (sc2, marna1, …).
      if (/\d+$/.test(suggested)) return;
      form.elements.subdomain.value = suggested;
      if (data.host) document.getElementById('subdomain-suffix').textContent = '.' + String(data.host).replace(suggested + '.', '');
      return checkSubdomain();
    });
  }
  var progressSteps = [
    { title: 'A validar os dados', description: 'A verificar as informações fornecidas' },
    { title: 'A verificar o endereço da empresa', description: 'A confirmar os dados da empresa' },
    { title: 'A comunicar com o servidor', description: 'A estabelecer uma ligação segura' },
    { title: 'A configurar o acesso', description: 'A preparar os dados iniciais da conta' },
    { title: 'A activar a subscrição', description: 'A registar a subscrição seleccionada' },
    { title: 'A preparar a facturação', description: 'A configurar os dados iniciais de facturação' },
    { title: 'A concluir', description: 'A finalizar esta etapa' }
  ];
  var progressIndex = 0;
  var progressTarget = 0;
  var progressFinished = false;
  var progressFailed = false;
  var progressAwaitingComplete = false;
  var progressStepStartedAt = 0;
  var progressStepTimer = null;
  var MIN_STEP_MS = 2000;
  function clearProgressTimer() {
    if (progressStepTimer) {
      clearTimeout(progressStepTimer);
      progressStepTimer = null;
    }
  }
  function progressEls() {
    return {
      title: document.getElementById('signup-progress-title'),
      subtitle: document.getElementById('signup-progress-subtitle'),
      process: document.getElementById('signup-progress-process'),
      success: document.getElementById('signup-progress-success'),
      bar: document.getElementById('signup-progress-bar'),
      counter: document.getElementById('signup-progress-counter'),
      percent: document.getElementById('signup-progress-percent'),
      box: document.getElementById('signup-progress-step-box'),
      stepTitle: document.getElementById('signup-progress-step-title'),
      normal: document.getElementById('signup-progress-normal'),
      desc: document.getElementById('signup-progress-step-desc'),
      error: document.getElementById('signup-progress-error'),
      errorText: document.getElementById('signup-progress-error-text')
    };
  }
  function renderProgressStep(index) {
    var els = progressEls();
    var step = progressSteps[index] || progressSteps[0];
    var percentage = Math.round(((index + 1) / progressSteps.length) * 100);
    progressIndex = index;
    progressStepStartedAt = Date.now();
    els.bar.style.width = percentage + '%';
    els.percent.textContent = percentage + '%';
    els.counter.textContent = 'Etapa ' + (index + 1) + ' de ' + progressSteps.length;
    els.box.classList.remove('signup-step-enter');
    void els.box.offsetWidth;
    els.stepTitle.textContent = step.title;
    els.desc.textContent = step.description;
    els.normal.classList.remove('hidden');
    els.error.classList.add('hidden');
    els.bar.classList.remove('bg-red-500');
    els.bar.classList.add('bg-slate-900');
    els.counter.classList.remove('text-red-400');
    els.counter.classList.add('text-slate-400');
    els.percent.classList.remove('text-red-400');
    els.percent.classList.add('text-slate-400');
    els.box.classList.add('signup-step-enter');
  }
  function advanceProgressUi() {
    clearProgressTimer();
    if (progressFailed || progressFinished) return;
    var elapsed = Date.now() - progressStepStartedAt;
    var wait = Math.max(0, MIN_STEP_MS - elapsed);
    if (progressIndex < progressTarget) {
      progressStepTimer = setTimeout(function () {
        renderProgressStep(progressIndex + 1);
        advanceProgressUi();
      }, wait);
      return;
    }
    if (progressAwaitingComplete && progressIndex >= progressSteps.length - 1) {
      progressStepTimer = setTimeout(function () {
        finishProgress();
      }, wait);
    }
  }
  function resetProgressUi() {
    var els = progressEls();
    clearProgressTimer();
    progressFinished = false;
    progressFailed = false;
    progressAwaitingComplete = false;
    progressIndex = 0;
    progressTarget = 0;
    els.title.textContent = 'A preparar a sua subscrição';
    els.subtitle.classList.remove('hidden');
    els.subtitle.textContent = 'Aguarde enquanto concluímos esta etapa.';
    els.process.classList.remove('hidden', 'signup-process-leave');
    els.success.classList.add('hidden');
    els.success.classList.remove('signup-final-pop');
    renderProgressStep(0);
  }
  function failProgress(message) {
    if (progressFinished) return;
    progressFailed = true;
    progressAwaitingComplete = false;
    clearProgressTimer();
    var els = progressEls();
    els.normal.classList.add('hidden');
    els.error.classList.remove('hidden');
    els.errorText.textContent = message || 'Não foi possível concluir o cadastro. Tente novamente.';
    els.bar.classList.remove('bg-slate-900');
    els.bar.classList.add('bg-red-500');
    els.counter.classList.remove('text-slate-400');
    els.counter.classList.add('text-red-400');
    els.percent.classList.remove('text-slate-400');
    els.percent.classList.add('text-red-400');
  }
  function finishProgress() {
    if (progressFinished || progressFailed) return;
    progressFinished = true;
    clearProgressTimer();
    var els = progressEls();
    els.bar.style.width = '100%';
    els.percent.textContent = '100%';
    setTimeout(function () {
      els.process.classList.add('signup-process-leave');
      setTimeout(function () {
        els.process.classList.add('hidden');
        els.process.classList.remove('signup-process-leave');
        els.title.textContent = 'Preparação concluída';
        els.subtitle.classList.add('hidden');
        els.success.classList.remove('hidden');
        els.success.classList.remove('signup-final-pop');
        void els.success.offsetWidth;
        els.success.classList.add('signup-final-pop');
      }, 130);
    }, 200);
  }
  function updateProgress(state) {
    var stages = {
      pending: 0, validating: 0, provisioning_dns: 1, provisioning_cloudways: 2,
      creating_subscription: 3, creating_tenant: 4, initializing_billing: 5,
      billing_initialized: 5, finalizing: 6, sending_invite: 6, completed: 6
    };
    var active = stages[state && state.status];
    if (active == null) active = progressTarget;
    if (active > progressTarget) progressTarget = active;
    if (state && state.status === 'completed') {
      progressTarget = progressSteps.length - 1;
      progressAwaitingComplete = true;
    }
    advanceProgressUi();
  }
  function monitor(id) {
    if (monitorTimer) {
      clearTimeout(monitorTimer);
      monitorTimer = null;
    }
    request('provisionings/' + encodeURIComponent(id)).then(function (r) {
      if (!progressModal.classList.contains('is-open')) return;
      var state = r.body.data || r.body;
      if (!r.response.ok || r.response.status === 404 || state.status === 'not_found') {
        monitorTimer = setTimeout(function () { monitor(id); }, 2000);
        return;
      }
      updateProgress(state);
      if (state.status === 'completed') {
        localStorage.removeItem('sizotech_provisioning_id');
        sessionStorage.removeItem('sizotech_registration_key');
        updateProgress({ status: 'completed' });
        return;
      }
      if (state.status === 'failed') {
        localStorage.removeItem('sizotech_provisioning_id');
        failProgress(state.message || 'Não foi possível concluir o cadastro. Tente novamente.');
        return;
      }
      monitorTimer = setTimeout(function () { monitor(id); }, 2000);
    }).catch(function () {
      if (!progressModal.classList.contains('is-open')) return;
      monitorTimer = setTimeout(function () { monitor(id); }, 3000);
    });
  }
  function syncBillingCycleVisibility() {
    var wrap = document.getElementById('signup-billing-cycle-wrap');
    var cycle = form.elements.billing_cycle;
    if (!wrap || !cycle) return;
    var isFree = String(form.elements.plan_code.value || '').toUpperCase() === 'FREE';
    wrap.classList.toggle('hidden', isFree);
    if (isFree) {
      if (![].some.call(cycle.options, function (opt) { return opt.value === 'monthly'; })) {
        cycle.add(new Option('Mensal', 'monthly', true, true));
      }
      cycle.value = 'monthly';
    }
    updatePlanTotalBadge();
  }
  function findLoadedPlan(code) {
    code = String(code || '').toUpperCase();
    for (var i = 0; i < loadedPlans.length; i += 1) {
      if (String(loadedPlans[i].code || '').toUpperCase() === code) return loadedPlans[i];
    }
    return null;
  }
  function updatePlanTotalBadge() {
    var wrap = document.getElementById('signup-plan-total');
    if (!wrap) return;
    var planCode = String(form.elements.plan_code.value || '').toUpperCase();
    var cycleName = String(form.elements.billing_cycle.value || 'monthly');
    var cycle = billingCycles[cycleName] || billingCycles.monthly;
    var plan = findLoadedPlan(planCode);
    if (!plan || planCode === 'FREE' || cycleName === 'monthly' || cycle.months <= 1) {
      wrap.classList.add('hidden');
      wrap.textContent = '';
      return;
    }
    var price = plan.price || {};
    var currency = price.currency || 'MZN';
    var total = Number(price.amount || 0) * cycle.months;
    wrap.innerHTML = '<strong class="font-semibold text-slate-900">Total:</strong> ' + formatMoney(total) + ' ' + currency + cycle.period;
    wrap.classList.remove('hidden');
  }
  function setPlanSummary(planName, monthlyLabel) {
    var summary = document.getElementById('signup-plan-summary');
    if (!summary) return;
    summary.innerHTML = '<strong class="font-semibold text-slate-900">Plano escolhido *:</strong> ' + String(planName || '') + ' ' + String(monthlyLabel || '');
  }
  function choosePlan(card) {
    if (!card) return;
    var planCode = card.dataset.planCode;
    var planName = card.dataset.planName;
    var planCycles = card.dataset.planCycles;
    var cardCycle = card.dataset.billingCycle;
    var plan = findLoadedPlan(planCode);
    var price = (plan && plan.price) || {};
    var currency = price.currency || 'MZN';
    var monthlyLabel = formatMoney(Number(price.amount || 0)) + ' ' + currency + '/mês';
    resetForm();
    form.elements.plan_code.value = planCode;
    setPlanSummary(planName, monthlyLabel);
    var cycle = form.elements.billing_cycle;
    cycle.textContent = '';
    JSON.parse(planCycles || '["monthly"]').forEach(function (name) {
      var details = billingCycles[name];
      cycle.add(new Option(details ? details.label : name, name, false, name === cardCycle));
    });
    cycle.value = cardCycle || 'monthly';
    syncBillingCycleVisibility();
    openModal(modal);
  }
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
  document.querySelectorAll('[data-close-plan-picker]').forEach(function (button) {
    button.addEventListener('click', function () { closeAllModals({ animate: true }); });
  });
  document.querySelectorAll('[data-signup-close]').forEach(function (button) {
    button.addEventListener('click', function () {
      closeAllModals({ animate: true, onDone: resetForm });
    });
  });
  document.querySelectorAll('[data-close-progress]').forEach(function (button) {
    button.addEventListener('click', function () {
      localStorage.removeItem('sizotech_provisioning_id');
      closeAllModals({ animate: true });
    });
  });
  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    if (verificationModal.classList.contains('is-open')) {
      cancelEmailVerification();
      return;
    }
    if (!document.querySelector('.signup-modal.is-open')) return;
    localStorage.removeItem('sizotech_provisioning_id');
    closeAllModals({ animate: true, onDone: function () {
      if (modal && !modal.classList.contains('hidden')) return;
      resetForm();
    }});
  });
  setupPhoneField('phone'); setupPhoneField('phone_alt');
  setupBusinessAreaField();
  setupSearchableSelect(form.elements.address_country, 'Pesquisar país…');
  setupSearchableSelect(form.elements.address_province_mz, 'Pesquisar província…');
  form.querySelector('[data-step="1"]').appendChild(form.elements.subdomain.closest('label'));
  syncSubdomainState();
  form.elements.company_type.addEventListener('change', syncOther);
  form.elements.billing_cycle.addEventListener('change', updatePlanTotalBadge);
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
  function commitSearchableSelect(searchInput) {
    var select = form.elements[searchInput.dataset.selectSearch];
    if (!select || !select._searchOptions) return false;
    var menu = searchInput.parentNode.querySelector('[role="listbox"]');
    var typed = searchInput.value.trim().toLocaleLowerCase('pt');
    var source = select._searchOptions;
    var exact = source.filter(function (item) {
      return !item.disabled && item.label.toLocaleLowerCase('pt') === typed;
    })[0];
    var firstBtn = menu && !menu.classList.contains('hidden') && menu.querySelector('button');
    var firstItem = firstBtn && source.filter(function (item) { return String(item.value) === String(firstBtn.dataset.value); })[0];
    var current = source.filter(function (item) { return item.value === select.value; })[0];
    var chosen = exact || firstItem || current;
    if (!chosen) return false;
    var changed = select.value !== chosen.value;
    select.value = chosen.value;
    searchInput.value = chosen.label;
    if (menu) {
      menu.classList.add('hidden');
      searchInput.setAttribute('aria-expanded', 'false');
    }
    if (changed) select.dispatchEvent(new Event('change', { bubbles: true }));
    return true;
  }
  function isEnterKey(event) {
    return event.key === 'Enter' || event.code === 'Enter' || event.code === 'NumpadEnter' || event.which === 13;
  }
  function formPayload() {
    try { syncAddressProvince(); } catch (e) {}
    return {
      name: String(form.elements.name.value || '').trim(),
      company_type: String(form.elements.company_type.value || '').trim(),
      company_type_other: String(form.elements.company_type_other.value || '').trim(),
      show_legal_designation: !!form.elements.show_legal_designation.checked,
      email: String(form.elements.email.value || '').trim(),
      nuit: nuitDigits(form.elements.nuit.value),
      phone: String(form.elements.phone.value || '').trim(),
      phone_alt: String(form.elements.phone_alt.value || '').trim(),
      business_area: String(form.elements.business_area.value || '').trim(),
      business_area_other: String((form.elements.business_area_other && form.elements.business_area_other.value) || '').trim(),
      address_country: String(form.elements.address_country.value || '').trim(),
      address_province: String(form.elements.address_province.value || '').trim(),
      address_street: String(form.elements.address_street.value || '').trim(),
      address_neighborhood: String(form.elements.address_neighborhood.value || '').trim(),
      address_house_number: String(form.elements.address_house_number.value || '').trim(),
      plan_code: String(form.elements.plan_code.value || '').trim(),
      billing_cycle: String(form.elements.plan_code.value || '').toUpperCase() === 'FREE'
        ? 'monthly'
        : String(form.elements.billing_cycle.value || '').trim(),
      subdomain: String(form.elements.subdomain.value || '').trim().toLowerCase(),
      email_verification_token: emailVerificationToken
    };
  }
  function setActionBusy(busy) {
    submitting = !!busy;
    updateNavigationState();
  }
  function validateStepWithApi(step) {
    showSignupMessage('', '');
    if (!validateStep(step)) return Promise.resolve(false);
    var apiStep = formApiStep(step);
    if (!apiStep) return Promise.resolve(true);
    setActionBusy(true);
    return request('registrations/validate', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': currentCsrf() },
      body: JSON.stringify(Object.assign({ step: apiStep }, formPayload()))
    }).then(function (r) {
      var body = r.body || {};
      if (r.response.ok && body.status === 'ok') return true;
      setActionBusy(false);
      if (body.errors) {
        showErrors(body.errors);
        return false;
      }
      showSignupMessage(body.message || 'Não foi possível validar os dados. Tente novamente.', 'error');
      return false;
    }).catch(function () {
      setActionBusy(false);
      showSignupMessage('Não foi possível comunicar com o servidor. Verifique a ligação e tente novamente.', 'error');
      return false;
    });
  }
  function sendEmailVerificationCode(force) {
    var email = String(form.elements.email.value || '').trim().toLowerCase();
    if (!force && emailCodeSentFor === email) {
      return Promise.resolve({ ok: true, masked: maskEmailLocal(email), already: true });
    }
    setActionBusy(true);
    return request('registrations/email-code/send', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': currentCsrf() },
      body: JSON.stringify({ email: email, name: companyDisplayName() })
    }).then(function (r) {
      var body = r.body || {};
      setActionBusy(false);
      if (r.response.ok && body.status === 'ok') {
        emailCodeSentFor = email;
        var data = body.data || {};
        startResendCountdown(data.resend_after_seconds || 60);
        clearOtpInputs();
        return { ok: true, masked: data.masked_email || maskEmailLocal(email) };
      }
      if (r.response.status === 429) {
        startResendCountdown((body.retry_after || 60));
        return {
          ok: false,
          message: body.message || 'Aguarde alguns segundos antes de pedir um novo código.'
        };
      }
      if (body.errors) {
        var first = Object.keys(body.errors)[0];
        var errMsg = first && body.errors[first] && body.errors[first][0] && body.errors[first][0].message;
        return { ok: false, message: errMsg || 'Não foi possível enviar o código. Tente novamente.' };
      }
      return { ok: false, message: body.message || 'Não foi possível enviar o código. Tente novamente.' };
    }).catch(function () {
      setActionBusy(false);
      return { ok: false, message: 'Não foi possível comunicar com o servidor. Verifique a ligação e tente novamente.' };
    });
  }
  function verifyEmailCode() {
    var email = String(form.elements.email.value || '').trim().toLowerCase();
    var code = otpCode();
    if (!/^\d{4}$/.test(code)) {
      showVerificationError('Introduza o código de 4 dígitos enviado por e-mail.');
      return Promise.resolve(false);
    }
    setActionBusy(true);
    updateConfirmButton();
    if (confirmButton) confirmButton.innerHTML = '<span class="button-loader"></span>';
    return request('registrations/email-code/verify', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': currentCsrf() },
      body: JSON.stringify({ email: email, code: code })
    }).then(function (r) {
      var body = r.body || {};
      setActionBusy(false);
      if (confirmButton) confirmButton.innerHTML = 'Confirmar código';
      updateConfirmButton();
      if (r.response.ok && body.status === 'ok' && body.data && body.data.verification_token) {
        emailVerificationToken = String(body.data.verification_token);
        emailVerifiedFor = email;
        clearVerificationError();
        return true;
      }
      var message = (body.errors && body.errors.code && body.errors.code[0] && body.errors.code[0].message)
        || body.message
        || 'O código introduzido é inválido ou expirou.';
      showVerificationError(message);
      return false;
    }).catch(function () {
      setActionBusy(false);
      if (confirmButton) confirmButton.innerHTML = 'Confirmar código';
      updateConfirmButton();
      showVerificationError('Não foi possível comunicar com o servidor. Verifique a ligação e tente novamente.');
      return false;
    });
  }
  function goToNextStep() {
    if (currentStep >= totalSteps || submitting) return;
    var focusEl = document.activeElement;
    var searchInput = focusEl && focusEl.closest && focusEl.closest('[data-select-search]');
    if (searchInput && modal.contains(searchInput)) commitSearchableSelect(searchInput);

    if (currentStep === 1) {
      showSignupMessage('', '');
      if (!validateStep(1)) return;
      var email = String(form.elements.email.value || '').trim().toLowerCase();
      if (emailVerificationToken && emailVerifiedFor === email) {
        showStep(2);
        return;
      }
      beginEmailVerificationFlow(emailCodeSentFor !== email);
      return;
    }

    validateStepWithApi(currentStep).then(function (ok) {
      if (!ok) return;
      submitting = false;
      showStep(currentStep + 1);
    });
  }
  function handleSignupEnter(event) {
    if (!isEnterKey(event) || event.isComposing) return;
    if (!modal.classList.contains('is-open')) return;
    var target = event.target;
    var active = document.activeElement;
    var inModal = (target && modal.contains(target)) || (active && modal.contains(active));
    if (!inModal) return;
    if (target && target.closest && target.closest('[data-signup-close]')) return;
    if (target && (target.tagName === 'TEXTAREA' || target.isContentEditable)) return;

    event.preventDefault();
    event.stopPropagation();

    if (target && target.closest && target.closest('#signup-back')) {
      if (currentStep > 1) showStep(currentStep - 1);
      return;
    }

    var searchInput = (active && active.closest && active.closest('[data-select-search]'))
      || (target && target.closest && target.closest('[data-select-search]'));
    if (searchInput) commitSearchableSelect(searchInput);

    if (currentStep >= totalSteps) {
      submitSignup();
      return;
    }
    goToNextStep();
  }
  document.addEventListener('keydown', handleSignupEnter, true);
  document.getElementById('signup-next').addEventListener('click', function () { goToNextStep(); });
  document.getElementById('signup-back').addEventListener('click', function () { showStep(currentStep - 1); });
  form.elements.email.addEventListener('input', function () {
    var email = String(form.elements.email.value || '').trim().toLowerCase();
    if (emailVerifiedFor && email !== emailVerifiedFor) invalidateEmailVerification();
    if (emailCodeSentFor && email !== emailCodeSentFor) {
      emailCodeSentFor = '';
      clearOtpInputs();
    }
  });
  otpInputs.forEach(function (input, index) {
    input.addEventListener('input', function () {
      input.value = String(input.value || '').replace(/\D/g, '').slice(0, 1);
      clearVerificationError();
      if (input.value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
      updateConfirmButton();
    });
    input.addEventListener('keydown', function (event) {
      if (event.key === 'Backspace' && !input.value && index > 0) {
        otpInputs[index - 1].focus();
      }
      if (isEnterKey(event)) {
        event.preventDefault();
        if (otpCode().length === 4) verificationForm.requestSubmit();
      }
    });
    input.addEventListener('paste', function (event) {
      event.preventDefault();
      var pasted = String((event.clipboardData || window.clipboardData).getData('text') || '').replace(/\D/g, '').slice(0, 4);
      if (!pasted) return;
      pasted.split('').forEach(function (digit, i) {
        if (otpInputs[i]) otpInputs[i].value = digit;
      });
      otpInputs[Math.min(pasted.length, otpInputs.length) - 1].focus();
      clearVerificationError();
      updateConfirmButton();
    });
  });
  verificationForm.addEventListener('submit', function (event) {
    event.preventDefault();
    if (submitting) return;
    verifyEmailCode().then(function (ok) {
      if (!ok) return;
      resumeSignupAfterVerification();
    });
  });
  document.getElementById('resendButton').addEventListener('click', function () {
    if (submitting) return;
    var email = String(form.elements.email.value || '').trim().toLowerCase();
    var masked = maskEmailLocal(email);
    var resendMessage = document.getElementById('resendMessage');
    if (resendMessage) resendMessage.classList.add('hidden');
    emailVerificationToken = '';
    emailVerifiedFor = '';
    setEmailVerificationView('sending', { masked: masked });
    sendEmailVerificationCode(true).then(function (result) {
      if (!result || !result.ok) {
        showEmailVerificationSendError(result && result.message ? result.message : 'Não foi possível reenviar o código. Tente novamente.');
        return;
      }
      showEmailVerificationForm(result.masked || masked);
      if (resendMessage) {
        resendMessage.classList.remove('hidden');
        setTimeout(function () { resendMessage.classList.add('hidden'); }, 3500);
      }
    });
  });
  var retrySend = document.getElementById('email-verification-retry');
  if (retrySend) {
    retrySend.addEventListener('click', function () {
      if (submitting) return;
      beginEmailVerificationFlow(true);
    });
  }
  document.querySelectorAll('[data-close-email-verification]').forEach(function (button) {
    button.addEventListener('click', function () { cancelEmailVerification(); });
  });
  function restoreSubmitButton() {
    var button = document.getElementById('signup-submit');
    if (button) button.textContent = 'Concluir cadastro';
    setActionBusy(false);
  }
  function beginProvisioning(state, provisioningId) {
    submitting = false;
    resetProgressUi();
    openModal(progressModal);
    if (provisioningId) {
      localStorage.setItem('sizotech_provisioning_id', String(provisioningId));
      updateProgress(state || {});
      monitor(provisioningId);
      return;
    }
    updateProgress({ status: 'completed' });
  }
  function submitSignup() {
    if (submitting) return;
    if (currentStep < totalSteps) {
      goToNextStep();
      return;
    }
    var email = String(form.elements.email.value || '').trim().toLowerCase();
    if (!emailVerificationToken || emailVerifiedFor !== email) {
      showSignupMessage('', '');
      showStep(1);
      beginEmailVerificationFlow(true);
      return;
    }
    showSignupMessage('', '');
    var focusEl = document.activeElement;
    var searchInput = focusEl && focusEl.closest && focusEl.closest('[data-select-search]');
    if (searchInput && modal.contains(searchInput)) commitSearchableSelect(searchInput);
    validateStepWithApi(totalSteps).then(function (ok) {
      if (!ok) {
        showStep(totalSteps);
        return;
      }
      resetProgressUi();
      openModal(progressModal);

      request('registrations', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': currentCsrf(),
          'Idempotency-Key': getKey()
        },
        body: JSON.stringify(formPayload())
      }).then(function (r) {
      var body = r.body || {};
      if (isIdempotencyConflict(body) && !idempotencyRetry) {
        idempotencyRetry = true;
        newKey();
        submitting = false;
        submitSignup();
        return;
      }
      idempotencyRetry = false;
      var provisioningId = body.provisioning_id || (body.data && body.data.provisioning_id);
      if ((r.response.status === 202 || r.response.status === 201) && provisioningId) {
        beginProvisioning(body.data || body, provisioningId);
        return;
      }
      if (r.response.status === 201) {
        sessionStorage.removeItem('sizotech_registration_key');
        beginProvisioning({ status: 'completed' });
        return;
      }
      restoreSubmitButton();
      closeAllModals();
      openModal(modal);
      newKey();
      if (body.errors) {
        if (body.errors.email_verification_token) {
          invalidateEmailVerification();
          showStep(1);
          beginEmailVerificationFlow(true);
          return;
        }
        showErrors(body.errors);
        showSignupMessage('', '');
        return;
      }
      showSignupMessage(
        /nuit/i.test(String(body.message || ''))
          ? 'O NUIT informado não parece ser válido.'
          : (body.message || 'Não foi possível concluir o cadastro. Tente novamente.'),
        'error'
      );
    }).catch(function () {
      restoreSubmitButton();
      closeAllModals();
      openModal(modal);
      newKey();
      showSignupMessage('Não foi possível comunicar com o servidor. Verifique a ligação e tente novamente.', 'error');
    });
    });
  }
  document.getElementById('signup-submit').addEventListener('click', function (event) {
    event.preventDefault();
    submitSignup();
  });
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (currentStep < totalSteps) {
      goToNextStep();
      return;
    }
    submitSignup();
  });
  closeAllModals();
  try { localStorage.removeItem('sizotech_provisioning_id'); } catch (e) {}
  updateNavigationState(); loadPlans(); loadTypes();
})();
