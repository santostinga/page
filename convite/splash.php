<?php
declare(strict_types=1);

/**
 * HTML/CSS/JS do ecrã «Abrindo o seu convite» (injectado por convite.php).
 */
function convite_splash_head_style(): string
{
    return <<<'HTML'
<style id="convite-splash-css">
body.convite-loading{overflow:hidden}
#convite-splash{position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;background:#f8f4ef;color:#751331;font-family:Georgia,"Times New Roman",serif;transition:opacity .45s ease,visibility .45s ease}
#convite-splash.is-hiding{opacity:0;visibility:hidden;pointer-events:none}
.convite-splash-inner{text-align:center;padding:1.5rem;max-width:90vw}
.convite-splash-loader{width:28px;height:28px;margin:0 auto 1.1rem;border:2px solid rgba(117,19,49,.18);border-top-color:#8b1738;border-radius:50%;animation:convite-splash-spin .75s linear infinite}
.convite-splash-text{margin:0;font-size:clamp(1rem,4.2vw,1.25rem);font-weight:600;letter-spacing:.04em}
@keyframes convite-splash-spin{to{transform:rotate(360deg)}}
@media (prefers-reduced-motion:reduce){
  .convite-splash-loader{animation:none;border-top-color:#8b1738}
  #convite-splash{transition:none}
}
</style>
HTML;
}

function convite_splash_body_markup(): string
{
    return <<<'HTML'
<div id="convite-splash" role="status" aria-live="polite" aria-busy="true">
  <div class="convite-splash-inner">
    <div class="convite-splash-loader" aria-hidden="true"></div>
    <p class="convite-splash-text">Abrindo o seu convite</p>
  </div>
</div>
HTML;
}

function convite_splash_body_script(): string
{
    return <<<'HTML'
<script id="convite-splash-js">
(function(){
  var splash=document.getElementById('convite-splash');
  if(!splash)return;
  var shownAt=Date.now(),minMs=650,maxMs=90000;
  function hide(){
    document.body.classList.remove('convite-loading');
    splash.setAttribute('aria-busy','false');
    var wait=Math.max(0,minMs-(Date.now()-shownAt));
    setTimeout(function(){
      splash.classList.add('is-hiding');
      setTimeout(function(){splash.remove();},500);
    },wait);
  }
  function waitImages(){
    var imgs=document.querySelectorAll('#book .page img');
    if(!imgs.length){hide();return;}
    var left=imgs.length;
    function one(){if(--left<=0)hide();}
    imgs.forEach(function(img){
      if(img.complete&&img.naturalWidth>0)one();
      else{
        img.addEventListener('load',one,{once:true});
        img.addEventListener('error',one,{once:true});
      }
    });
  }
  waitImages();
  setTimeout(hide,maxMs);
})();
</script>
HTML;
}

function convite_apply_splash(string $html): string
{
    if (str_contains($html, 'id="convite-splash"')) {
        return $html;
    }

    $html = preg_replace('/<\/head>/i', convite_splash_head_style() . "\n</head>", $html, 1) ?? $html;

    $html = preg_replace(
        '/<body>/i',
        '<body class="convite-loading">' . convite_splash_body_markup(),
        $html,
        1
    ) ?? $html;

    $html = preg_replace(
        '/<\/body>/i',
        convite_splash_body_script() . "\n</body>",
        $html,
        1
    ) ?? $html;

    return $html;
}
