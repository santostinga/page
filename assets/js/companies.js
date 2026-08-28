(function () {
  'use strict';

  var list = document.getElementById('companies-list');
  if (!list) return;

  fetch('api/sizotech/companies').then(function (response) {
    return response.json().catch(function () { return {}; }).then(function (body) { return { response: response, body: body }; });
  }).then(function (result) {
    var companies = result.body.data || [];
    document.getElementById('companies-loading').classList.add('hidden');
    if (!result.response.ok || !companies.length) return;
    companies.forEach(function (company) {
      var card = document.createElement('article');
      card.className = 'company-showcase-card flex min-h-[82px] items-center gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-card';
      var logo = document.createElement('div');
      logo.className = 'flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-1.5 text-sm font-bold text-slate-400';
      var fallback = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      fallback.setAttribute('viewBox', '0 0 24 24'); fallback.setAttribute('fill', 'none'); fallback.setAttribute('stroke', 'currentColor'); fallback.setAttribute('stroke-width', '1.8'); fallback.setAttribute('aria-hidden', 'true'); fallback.classList.add('h-5', 'w-5');
      fallback.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 13h.01M9 17h.01"/>';
      logo.appendChild(fallback);
      if (company.logo_url) {
        var logoUrl = String(company.logo_url);
        if (logoUrl.indexOf('http://') === 0) {
          logoUrl = 'https://' + logoUrl.slice(7);
        }
        var image = document.createElement('img');
        image.src = logoUrl; image.alt = 'Logótipo da ' + (company.name || 'empresa'); image.loading = 'lazy'; image.className = 'h-full w-full object-contain';
        image.addEventListener('load', function () { fallback.classList.add('hidden'); });
        image.addEventListener('error', function () { image.remove(); });
        logo.appendChild(image);
      }
      var title = document.createElement('h3');
      title.className = 'min-w-0 flex-1 truncate text-sm font-semibold leading-5 text-slate-800'; title.textContent = company.name || 'Empresa Sizotech'; title.title = title.textContent;
      card.appendChild(logo); card.appendChild(title); list.appendChild(card);
    });
    list.classList.remove('hidden');
    document.getElementById('companies-count').textContent = '+ de ' + companies.length + ' empresas';
    document.getElementById('companies-count-wrap').classList.remove('hidden');
    document.getElementById('companies-note').classList.remove('hidden');
  }).catch(function () { document.getElementById('companies-loading').classList.add('hidden'); });
})();
