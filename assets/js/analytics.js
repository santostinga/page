(function () {
  'use strict';

  var ENDPOINT = 'api/analytics/track';
  var SESSION_KEY = 'sizo_analytics_session';
  var queue = [];
  var flushTimer = null;
  var sentSections = {};

  function sessionId() {
    try {
      var existing = sessionStorage.getItem(SESSION_KEY);
      if (existing) return existing;
      var id = (window.crypto && crypto.randomUUID)
        ? crypto.randomUUID()
        : 's-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
      sessionStorage.setItem(SESSION_KEY, id);
      return id;
    } catch (error) {
      return 's-fallback-' + Date.now();
    }
  }

  function utmParams() {
    var params = new URLSearchParams(window.location.search);
    return {
      utm_source: params.get('utm_source') || '',
      utm_medium: params.get('utm_medium') || '',
      utm_campaign: params.get('utm_campaign') || ''
    };
  }

  function pagePath() {
    return window.location.pathname + window.location.search + window.location.hash;
  }

  function elementLabel(node) {
    if (!node) return '';
    var text = (node.getAttribute('aria-label') || node.textContent || '').replace(/\s+/g, ' ').trim();
    if (text.length > 120) text = text.slice(0, 117) + '...';
    if (text) return text;
    if (node.id) return '#' + node.id;
    if (node.name) return node.name;
    return node.tagName ? node.tagName.toLowerCase() : '';
  }

  function pushEvent(event) {
    queue.push(event);
    if (flushTimer) clearTimeout(flushTimer);
    flushTimer = setTimeout(flush, 1200);
    if (queue.length >= 8) flush();
  }

  function flush() {
    if (!queue.length) return;
    var batch = queue.splice(0, 20);
    var payload = JSON.stringify({ events: batch });
    if (navigator.sendBeacon) {
      try {
        var blob = new Blob([payload], { type: 'application/json' });
        if (navigator.sendBeacon(ENDPOINT, blob)) return;
      } catch (error) {
        /* fallback below */
      }
    }
    fetch(ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: payload,
      keepalive: true
    }).catch(function () { /* silencioso */ });
  }

  function trackPageview() {
    var utm = utmParams();
    pushEvent(Object.assign({
      type: 'pageview',
      session_id: sessionId(),
      path: pagePath(),
      title: document.title || '',
      referrer: document.referrer || ''
    }, utm));
  }

  function trackClick(target) {
    var clickable = target.closest('a, button, [data-open-plan-picker], [data-plan-code], [role="button"]');
    if (!clickable) return;
    var utm = utmParams();
    pushEvent(Object.assign({
      type: 'click',
      session_id: sessionId(),
      path: pagePath(),
      title: document.title || '',
      label: elementLabel(clickable),
      href: clickable.getAttribute('href') || clickable.dataset.planCode || '',
      referrer: document.referrer || ''
    }, utm));
  }

  function observeSections() {
    var sections = document.querySelectorAll('main section[id], section[id]');
    if (!sections.length || !('IntersectionObserver' in window)) return;
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting || entry.intersectionRatio < 0.35) return;
        var id = entry.target.id;
        if (!id || sentSections[id]) return;
        sentSections[id] = true;
        pushEvent({
          type: 'section_view',
          session_id: sessionId(),
          path: pagePath(),
          section: id,
          referrer: document.referrer || ''
        });
      });
    }, { threshold: [0.35] });
    sections.forEach(function (section) { observer.observe(section); });
  }

  document.addEventListener('click', function (event) {
    trackClick(event.target);
  }, true);

  window.addEventListener('pagehide', flush);
  window.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') flush();
  });

  trackPageview();
  observeSections();
})();
