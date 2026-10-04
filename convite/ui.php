<?php
declare(strict_types=1);

function convite_ui_head_style(): string
{
    return <<<'HTML'
<style id="convite-ui-css">
#app{
  background:radial-gradient(120% 80% at 50% 0%,#fff9f4 0%,#f3ebe3 45%,#ebe2d8 100%);
}
#book{
  padding:max(8px,env(safe-area-inset-top)) 12px 6.5rem;
  box-sizing:border-box;
}
.art{
  /* Largura a partir da altura disponível — mantém 941:1672 (evita achatar) */
  width:min(
    calc(100vw - 24px),
    calc((100dvh - 8.5rem) * 941 / 1672),
    56.28dvh
  );
  aspect-ratio:941/1672;
  height:auto;
  max-height:none;
  border-radius:14px;
  overflow:hidden;
  box-shadow:
    0 28px 56px rgba(55,22,28,.16),
    0 10px 24px rgba(117,19,49,.1),
    inset 0 0 0 1px rgba(255,255,255,.35);
}
.art img{
  object-fit:contain;
}
.page{
  transition:
    opacity .65s cubic-bezier(.4,0,.2,1),
    transform .65s cubic-bezier(.4,0,.2,1),
    filter .65s ease;
  transform:scale(.965) translateY(8px);
  filter:brightness(.94);
}
.page.active{
  transform:scale(1) translateY(0);
  filter:brightness(1);
}
.ui-top{
  top:max(14px,calc(env(safe-area-inset-top) + 10px));
  padding:8px 12px;
  border-radius:999px;
  background:rgba(255,250,246,.82);
  border:1px solid rgba(255,255,255,.85);
  box-shadow:0 8px 28px rgba(60,30,25,.1);
  backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);
}
.dot{
  width:8px;
  height:8px;
  background:rgba(139,23,56,.2);
  transition:width .35s ease,background .35s ease,transform .25s ease;
}
.dot.active{
  width:28px;
  background:linear-gradient(90deg,#c9a227,#8b1738);
  box-shadow:0 2px 8px rgba(139,23,56,.25);
}
.dot:hover{transform:scale(1.08)}
.ui-bottom{
  bottom:max(38px,calc(env(safe-area-inset-bottom) + 26px));
  padding:8px 10px;
  border-radius:999px;
  background:rgba(255,250,246,.94);
  border:1px solid rgba(255,255,255,.9);
  box-shadow:0 14px 40px rgba(55,22,28,.18);
  backdrop-filter:blur(12px);
  -webkit-backdrop-filter:blur(12px);
}
.btn{
  height:48px;
  min-width:48px;
  border:1px solid rgba(139,23,56,.12);
  background:linear-gradient(180deg,#fffdfa 0%,#fff4ee 100%);
  color:#751331;
  box-shadow:0 4px 16px rgba(117,19,49,.08);
  transition:transform .2s ease,box-shadow .2s ease,opacity .2s ease;
}
.btn:active{transform:scale(.96)}
.btn.next{
  padding:0 20px;
  gap:9px;
  font:600 12px/1 system-ui,sans-serif;
  letter-spacing:.05em;
  text-transform:uppercase;
  color:#fff;
  border-color:rgba(255,255,255,.28);
  background:linear-gradient(135deg,#a8284f 0%,#751331 52%,#5e0f28 100%);
  box-shadow:0 8px 22px rgba(117,19,49,.35);
}
.btn.next svg,.btn.next i{color:currentColor;stroke:currentColor}
.swipe{
  right:18px;
  color:rgba(117,19,49,.22);
  animation:convite-swipe-hint 2.4s ease-in-out infinite;
}
@keyframes convite-swipe-hint{
  0%,100%{transform:translateY(-50%) translateX(0);opacity:.22}
  50%{transform:translateY(-50%) translateX(4px);opacity:.45}
}
@media(max-width:640px){
  #book{padding-bottom:7rem}
  .art{
    width:min(
      calc(100vw - 20px),
      calc((100dvh - 9rem) * 941 / 1672),
      56.28dvh
    );
  }
  .ui-bottom{bottom:max(42px,calc(env(safe-area-inset-bottom) + 30px))}
}
@media(prefers-reduced-motion:reduce){
  .page{transition:opacity .3s ease;transform:none;filter:none}
  .page.active{transform:none}
  .swipe{animation:none}
}
</style>
HTML;
}

function convite_apply_ui(string $html): string
{
    if (str_contains($html, 'id="convite-ui-css"')) {
        return $html;
    }

    return preg_replace('/<\/head>/i', convite_ui_head_style() . "\n</head>", $html, 1) ?? $html;
}
