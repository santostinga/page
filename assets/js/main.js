(function () {
  'use strict';

  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 700,
      easing: 'ease-out-cubic',
      once: true,
      offset: 50,
      disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    });
  }

  var yearEl = document.getElementById('footer-year');
  if (yearEl) {
    yearEl.textContent = String(new Date().getFullYear());
  }

  var nav = document.getElementById('site-nav');
  if (nav) {
    var onScroll = function () {
      nav.classList.toggle('is-scrolled', window.scrollY > 12);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  var mobileBtn = document.getElementById('mobile-menu-btn');
  var mobileNav = document.getElementById('mobile-nav');
  if (mobileBtn && mobileNav) {
    mobileBtn.addEventListener('click', function () {
      mobileNav.classList.toggle('hidden');
      var isOpen = !mobileNav.classList.contains('hidden');
      mobileBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  var signupModal = document.getElementById('signup-modal');
  var signupForm = document.getElementById('signup-form');
  if (false && signupModal && signupForm) {
    var step = 1;
    var resetSignup = function () {
      signupForm.reset();
      signupForm.querySelectorAll('.field-error').forEach(function (el) { el.textContent = ''; });
      var message = document.getElementById('signup-message');
      message.textContent = '';
      message.className = 'hidden mt-5 rounded-lg px-4 py-3 text-sm';
    };
    var fieldSteps = { name: 1, company_type: 1, company_type_other: 1, email: 1, nuit: 1, phone: 2, phone_alt: 2, address_country: 2, address_province: 2, address_street: 2, address_neighborhood: 2, address_house_number: 2, business_area: 3, plan_code: 3, billing_cycle: 3 };
    var errorSlot = function (name) {
      if (name === 'plan_code') {
        var summary = document.getElementById('signup-plan-summary');
        var planSlot = summary && summary.parentElement.querySelector('.plan-field-error');
        if (summary && !planSlot) { planSlot = document.createElement('span'); planSlot.className = 'plan-field-error mt-1 block text-xs text-red-600'; summary.insertAdjacentElement('afterend', planSlot); }
        return summary && planSlot ? { field: summary, slot: planSlot } : null;
      }
      var field = signupForm.querySelector('[name="' + name + '"]');
      if (!field) return null;
      var slot = field.parentElement.querySelector('.field-error');
      if (!slot) { slot = document.createElement('span'); slot.className = 'field-error mt-1 block text-xs text-red-600'; field.parentElement.appendChild(slot); }
      return { field: field, slot: slot };
    };
    var clearFieldError = function (name) { var item = errorSlot(name); if (item) { item.slot.textContent = ''; item.field.classList.remove('border-red-300', 'bg-red-50/30'); } };
    var setFieldError = function (name, text) { var item = errorSlot(name); if (item) { item.slot.textContent = text; item.field.classList.add('border-red-300', 'bg-red-50/30'); } };
    var validateStep = function (currentStep) {
      var errors = {}; var value = function (name) { return String(signupForm.elements[name] ? signupForm.elements[name].value : '').trim(); };
      var add = function (name, text) { errors[name] = text; setFieldError(name, text); };
      Object.keys(fieldSteps).forEach(function (name) { if (fieldSteps[name] === currentStep) clearFieldError(name); });
      if (currentStep === 1) { if (value('name').replace(/[^\p{L}\p{N}]/gu, '').length < 2) add('name', 'Indique um nome de empresa válido.'); if (!value('company_type')) add('company_type', 'Seleccione o tipo jurídico.'); if (value('company_type') === 'OTHER' && !value('company_type_other')) add('company_type_other', 'Indique o tipo jurídico.'); if (value('email') === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('email'))) add('email', 'Indique um e-mail válido.'); if (!value('nuit')) add('nuit', 'Indique o NUIT da empresa.'); else if (!/^\d{9}$/.test(value('nuit'))) add('nuit', 'Indique um NUIT válido com 9 dígitos.'); }
      if (currentStep === 2) { if (!value('address_province')) add('address_province', 'Indique a província ou cidade.'); ['phone','phone_alt'].forEach(function (name) { var phone = value(name); if (phone && !/^(?:\+258)?0?8\d{8}$/.test(phone.replace(/[\s-]/g, ''))) add(name, 'Indique um telefone válido.'); }); }
      if (currentStep === 3) { if (!value('plan_code')) add('plan_code', 'Seleccione um plano válido.'); if (String(value('plan_code')).toUpperCase() !== 'FREE' && !value('billing_cycle')) add('billing_cycle', 'Seleccione o ciclo de faturação.'); if (value('business_area').length > 160) add('business_area', 'A área de actividade é demasiado longa.'); }
      return errors;
    };
    var showApiErrors = function (errors) {
      var firstStep = 4; var firstField = null;
      Object.keys(errors || {}).forEach(function (name) { var text = errors[name] && errors[name][0] ? errors[name][0].message : 'Dados inválidos.'; setFieldError(name, text); if (fieldSteps[name] && fieldSteps[name] < firstStep) { firstStep = fieldSteps[name]; firstField = name; } });
      if (firstStep <= 3) { showStep(firstStep); setTimeout(function () { var field = signupForm.querySelector('[name="' + firstField + '"]'); if (field) { field.scrollIntoView({ behavior: 'smooth', block: 'center' }); field.focus(); } }, 80); }
    };
    signupForm.querySelectorAll('input, select').forEach(function (field) { field.addEventListener('input', function () { clearFieldError(field.name); }); field.addEventListener('change', function () { clearFieldError(field.name); }); });
    var companyType = signupForm.elements.company_type;
    var otherTypeWrap = document.getElementById('company-type-other-wrap');
    var syncOtherType = function () {
      if (!companyType || !otherTypeWrap) return;
      var option = companyType.options[companyType.selectedIndex];
      var required = option && option.dataset.requiresOther === '1';
      otherTypeWrap.classList.toggle('hidden', !required);
      otherTypeWrap.querySelector('input').required = required;
      if (!required) { otherTypeWrap.querySelector('input').value = ''; clearFieldError('company_type_other'); }
    };
    if (companyType) { companyType.addEventListener('change', syncOtherType); syncOtherType(); }
    var showStep = function (next) {
      step = next;
      signupForm.querySelectorAll('.signup-step').forEach(function (el) { el.classList.toggle('hidden', Number(el.dataset.step) !== step); });
      signupForm.querySelectorAll('[data-step-dot]').forEach(function (el) { el.className = 'signup-dot flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold ' + (Number(el.dataset.stepDot) <= step ? 'bg-slate-950 text-white' : 'bg-slate-200 text-slate-500'); });
      document.getElementById('signup-back').classList.toggle('hidden', step === 1);
      document.getElementById('signup-next').classList.toggle('hidden', step === 3);
      document.getElementById('signup-submit').classList.toggle('hidden', step !== 3);
    };
    document.querySelectorAll('[data-signup-open]').forEach(function (button) { button.addEventListener('click', function () {
      resetSignup(); syncOtherType(); showStep(1); signupForm.elements.plan_code.value = button.dataset.planCode;
      document.getElementById('signup-plan-summary').textContent = 'Plano escolhido *: ' + button.dataset.planName + ' — ' + button.dataset.planPrice;
      var cycle = signupForm.elements.billing_cycle; cycle.innerHTML = '';
      JSON.parse(button.dataset.planCycles || '["monthly"]').forEach(function (item) { var option = new Option(item.charAt(0).toUpperCase() + item.slice(1), item); cycle.add(option); });
      var isFree = String(button.dataset.planCode || '').toUpperCase() === 'FREE';
      var cycleWrap = document.getElementById('signup-billing-cycle-wrap');
      if (cycleWrap) cycleWrap.classList.toggle('hidden', isFree);
      if (isFree) cycle.value = 'monthly';
      signupModal.classList.add('is-open'); document.body.classList.add('modal-open');
    }); });
    document.querySelectorAll('[data-signup-close]').forEach(function (button) { button.addEventListener('click', function () { signupModal.classList.remove('is-open'); document.body.classList.remove('modal-open'); resetSignup(); }); });
    document.getElementById('signup-next').addEventListener('click', function () { var button = this; button.disabled = true; button.textContent = 'A validar…'; var errors = validateStep(step); setTimeout(function () { button.disabled = false; button.textContent = 'Continuar'; var names = Object.keys(errors); if (names.length) { var first = signupForm.querySelector('[name="' + names[0] + '"]'); if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); first.focus(); } return; } showStep(step + 1); }, 120); });
    document.getElementById('signup-back').addEventListener('click', function () { showStep(step - 1); });
    signupForm.addEventListener('submit', async function (event) { event.preventDefault(); var button = document.getElementById('signup-submit'); var message = document.getElementById('signup-message'); button.disabled = true; button.textContent = 'A processar…'; signupForm.querySelectorAll('.field-error').forEach(function (el) { el.textContent = ''; });
      try { var response = await fetch('subscricao.php', { method: 'POST', body: new FormData(signupForm) }); var data = await response.json(); if (response.status === 201) { message.innerHTML = '<strong>Cadastro concluído.</strong> A empresa foi criada. Verifique o seu e-mail para continuar.' + (data.company && data.company.access_url ? ' <a class="font-semibold underline" href="' + data.company.access_url + '">Aceder ao sistema</a>' : ''); message.className = 'mt-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800'; return; } if (data.errors) { showApiErrors(data.errors); message.className = 'hidden mt-5 rounded-lg px-4 py-3 text-sm'; } else { message.textContent = data.message || 'Não foi possível concluir o cadastro neste momento. Tente novamente.'; message.className = 'mt-5 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700'; } } catch (error) { message.textContent = 'Não foi possível concluir o cadastro neste momento. Tente novamente.'; message.className = 'mt-5 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700'; } finally { button.disabled = false; button.textContent = 'Concluir cadastro'; }
    });
  }

  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var id = this.getAttribute('href');
      if (!id || id.length < 2) return;
      var target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (mobileNav && !mobileNav.classList.contains('hidden')) {
        mobileNav.classList.add('hidden');
        if (mobileBtn) mobileBtn.setAttribute('aria-expanded', 'false');
      }
    });
  });

  /* Animated counters */
  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-count') || '0', 10);
    if (!target || el.dataset.counted === '1') return;
    el.dataset.counted = '1';

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      el.textContent = String(target);
      return;
    }

    var duration = 1400;
    var start = null;
    function frame(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = String(Math.round(target * eased));
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  var counters = document.querySelectorAll('.clients-count[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });
    counters.forEach(function (el) { io.observe(el); });
  } else {
    counters.forEach(animateCount);
  }

  /* Feature modal */
  var modal = document.getElementById('feature-modal');
  if (modal) {
    var modalTitle = document.getElementById('feature-modal-title');
    var modalDesc = document.getElementById('feature-modal-desc');
    var modalImg = document.getElementById('feature-modal-img');
    var closeBtns = modal.querySelectorAll('[data-modal-close]');

    function openModal(data) {
      if (modalTitle) modalTitle.textContent = data.title || '';
      if (modalDesc) modalDesc.textContent = data.detail || data.desc || '';
      if (modalImg) {
        modalImg.src = data.src || '';
        modalImg.alt = (data.title || 'Funcionalidade') + ' - Sizo Software';
      }
      modal.classList.add('is-open');
      document.body.classList.add('modal-open');
      modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
      modal.classList.remove('is-open');
      document.body.classList.remove('modal-open');
      modal.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('[data-feature-open]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        openModal({
          title: btn.getAttribute('data-title'),
          desc: btn.getAttribute('data-desc'),
          detail: btn.getAttribute('data-detail'),
          src: btn.getAttribute('data-src'),
        });
      });
    });

    closeBtns.forEach(function (btn) {
      btn.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('is-open')) {
        closeModal();
      }
    });
  }
})();
