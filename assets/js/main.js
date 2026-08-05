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
