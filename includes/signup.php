<?php
if (empty($_SESSION['signup_csrf'])) {
    $_SESSION['signup_csrf'] = bin2hex(random_bytes(32));
}
?>
<div id="signup-view" class="signup-view hidden" aria-hidden="true">
  <header class="signup-view-bar">
    <a href="./" data-signup-home class="flex shrink-0 items-center" aria-label="Voltar ao site">
      <img src="<?= htmlspecialchars(sizo_asset('assets/img/LOGO ST.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Sizotech — Smart It Solutions" class="h-9 w-auto object-contain sm:h-11" width="190" height="64">
    </a>
  </header>
  <div class="signup-view-stage">
    <div class="signup-view-panel">
      <div class="signup-view-toolbar">
        <button type="button" data-signup-close class="signup-view-back" aria-label="Voltar ao site">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
          <span>Voltar</span>
        </button>
      </div>
      <h2 id="signup-title" class="mt-1 text-xl font-bold text-slate-950 sm:text-2xl">Crie a sua empresa</h2>
      <form id="signup-form" class="mt-8" novalidate autocomplete="off" data-csrf="<?= htmlspecialchars($_SESSION['signup_csrf'], ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['signup_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="plan_code">
        <div data-step="1" class="signup-step grid gap-4 sm:grid-cols-2"><p class="sm:col-span-2 text-sm text-slate-600">Dados principais da empresa.</p><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Nome da empresa <span class="text-red-600" aria-hidden="true">*</span><input required name="name" maxlength="150" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Tipo jurídico <span class="text-red-600" aria-hidden="true">*</span><select name="company_type" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="">A carregar tipos…</option></select><span class="field-error text-xs text-red-600"></span></label><label id="company-type-other-wrap" class="hidden text-sm font-semibold text-slate-700">Outro tipo jurídico <span class="text-red-600" aria-hidden="true">*</span><input name="company_type_other" maxlength="120" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><div class="sm:col-span-2"><div class="flex items-center justify-between gap-4"><span class="text-sm font-semibold text-slate-700">Mostrar designação jurídica no nome</span><label class="relative inline-flex shrink-0 cursor-pointer items-center"><input type="checkbox" name="show_legal_designation" id="company-show-legal-designation" value="1" checked class="peer sr-only"><span class="h-6 w-11 rounded-full bg-slate-200 transition after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand peer-checked:after:translate-x-full peer-focus:ring-4 peer-focus:ring-blue-100"></span></label></div><div id="company-name-preview-wrap" class="mt-3 hidden rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"><p id="company-name-preview" class="min-h-5 break-words text-sm font-medium text-blue-900"></p></div></div><label class="text-sm font-semibold text-slate-700">NUIT <span class="text-red-600" aria-hidden="true">*</span><input name="nuit" inputmode="numeric" maxlength="11" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal" placeholder="000 000 000" autocomplete="off"><span class="field-error text-xs text-red-600"></span></label><label class="sm:col-span-2 text-sm font-semibold text-slate-700">E-mail <span class="text-red-600" aria-hidden="true">*</span><input required type="email" name="email" maxlength="150" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label></div>
        <div data-step="2" class="signup-step hidden grid gap-4 sm:grid-cols-2"><p class="sm:col-span-2 text-sm text-slate-600">Contactos, morada e acesso.</p><label class="text-sm font-semibold text-slate-700">Telefone<input name="phone" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Telefone alternativo<input name="phone_alt" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">País <span class="text-red-600" aria-hidden="true">*</span><select name="address_country" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="MZ" selected>Moçambique</option><option value="ZA">África do Sul</option><option value="ZW">Zimbabué</option><option value="SZ">Essuatíni</option><option value="LS">Lesoto</option><option value="BW">Botsuana</option><option value="NA">Namíbia</option><option value="AO">Angola</option><option value="ZM">Zâmbia</option><option value="MW">Malawi</option><option value="TZ">Tanzânia</option><option value="KE">Quénia</option><option value="PT">Portugal</option><option value="BR">Brasil</option><option value="CN">China</option><option value="IN">Índia</option><option value="US">Estados Unidos</option><option value="GB">Reino Unido</option><option value="DE">Alemanha</option><option value="FR">França</option><option value="OTHER">Outro…</option></select><span class="field-error text-xs text-red-600"></span></label><label id="mozambique-province-wrap" class="text-sm font-semibold text-slate-700">Província <span class="text-red-600" aria-hidden="true">*</span><select name="address_province_mz" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><option value="Cidade de Maputo" selected>Cidade de Maputo</option><option value="Maputo">Maputo</option><option value="Gaza">Gaza</option><option value="Inhambane">Inhambane</option><option value="Sofala">Sofala</option><option value="Manica">Manica</option><option value="Tete">Tete</option><option value="Zambézia">Zambézia</option><option value="Nampula">Nampula</option><option value="Niassa">Niassa</option><option value="Cabo Delgado">Cabo Delgado</option></select><span class="field-error text-xs text-red-600"></span></label><label id="foreign-province-wrap" class="hidden text-sm font-semibold text-slate-700">Província ou cidade <span class="text-red-600" aria-hidden="true">*</span><input name="address_province_text" maxlength="160" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><input type="hidden" name="address_province" value="Cidade de Maputo"><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Rua / avenida<input name="address_street" maxlength="500" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Bairro<input name="address_neighborhood" maxlength="200" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="text-sm font-semibold text-slate-700">Número<input name="address_house_number" maxlength="60" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"><span class="field-error text-xs text-red-600"></span></label><label class="sm:col-span-2 text-sm font-semibold text-slate-700">Endereço da empresa <span class="text-red-600" aria-hidden="true">*</span><span class="mt-1.5 flex"><input name="subdomain" spellcheck="false" class="min-w-0 flex-1 rounded-l-lg border border-slate-300 px-3 py-2.5 font-normal"><span id="subdomain-suffix" class="rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-600">.sizotech.net</span></span><span id="subdomain-availability" class="mt-1 block text-xs"></span><span class="field-error text-xs text-red-600"></span></label></div>
        <div data-step="3" class="signup-step hidden grid gap-4">
          <div>
            <p class="text-sm font-semibold text-slate-700">Plano <span class="text-red-600" aria-hidden="true">*</span></p>
            <div id="signup-plan-cards" class="signup-plan-cards mt-2" role="radiogroup" aria-label="Escolher plano"></div>
          </div>
          <label id="signup-billing-cycle-wrap" class="text-sm font-semibold text-slate-700">Ciclo de faturação<select name="billing_cycle" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></select></label>
          <div id="signup-plan-total" class="hidden w-fit rounded-xl bg-brand-soft px-4 py-3 text-sm text-slate-700" aria-live="polite"></div>
          <label class="text-sm font-semibold text-slate-700">Área de actividade <span class="text-red-600" aria-hidden="true">*</span><input name="business_area" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        </div>
        <p id="signup-message" class="hidden mt-5 rounded-lg px-4 py-3 text-sm"></p><div class="mt-7 flex justify-between gap-3"><button type="button" id="signup-back" class="hidden inline-flex items-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100" aria-label="Passo anterior"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg><span>Voltar</span></button><span id="signup-spacer"></span><button type="button" id="signup-next" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Continuar</button><button type="button" id="signup-submit" class="hidden rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Concluir cadastro</button></div>
      </form>
    </div>
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
