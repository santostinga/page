<?php
declare(strict_types=1);

function convite_splash_head_style(): string
{
    return <<<'HTML'
<style id="convite-splash-css">
body.convite-loading{overflow:hidden}
#convite-splash{
  position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;
  background:radial-gradient(120% 90% at 50% 0%,#fff9f4 0%,#f3ebe3 55%,#ebe2d8 100%);
  color:#751331;font-family:Georgia,"Times New Roman",serif;
  transition:opacity .5s ease,visibility .5s ease;
}
#convite-splash.is-hiding{opacity:0;visibility:hidden;pointer-events:none}
.convite-splash-inner{text-align:center;padding:1.75rem 1.5rem;max-width:min(92vw,22rem)}
.convite-splash-title{margin:0 0 1rem;font-size:clamp(.95rem,3.8vw,1.1rem);font-weight:600;letter-spacing:.06em;color:#751331}
.convite-splash-names{
  min-height:1.6em;margin:0 0 1.25rem;
  font-size:clamp(.78rem,3vw,.92rem);font-weight:400;font-style:italic;
  letter-spacing:.14em;text-transform:none;color:rgba(117,19,49,.38);
}
.convite-splash-bar{
  height:3px;width:min(220px,70vw);margin:0 auto .65rem;border-radius:999px;
  background:rgba(139,23,56,.12);overflow:hidden;
}
.convite-splash-bar-fill{
  height:100%;width:0;border-radius:inherit;
  background:linear-gradient(90deg,#c9a227,#8b1738);
  transition:width .25s ease;
}
.convite-splash-pct{margin:0;font:600 11px/1.2 system-ui,sans-serif;letter-spacing:.12em;color:rgba(117,19,49,.55)}
@media (prefers-reduced-motion:reduce){
  #convite-splash{transition:none}
  .convite-splash-bar-fill{transition:none}
}
</style>
HTML;
}

function convite_splash_body_markup(): string
{
    return <<<'HTML'
<div id="convite-splash" role="status" aria-live="polite" aria-busy="true">
  <div class="convite-splash-inner">
    <p class="convite-splash-title">Abrindo o seu convite</p>
    <p class="convite-splash-names" id="convite-splash-names" aria-hidden="true"></p>
    <div class="convite-splash-bar" aria-hidden="true"><div class="convite-splash-bar-fill" id="convite-splash-bar"></div></div>
    <p class="convite-splash-pct" id="convite-splash-pct">0%</p>
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
  var namesEl=document.getElementById('convite-splash-names');
  var barEl=document.getElementById('convite-splash-bar');
  var pctEl=document.getElementById('convite-splash-pct');
  var shownAt=Date.now(),minMs=2200,maxMs=120000;
  var fullName='Marciano & Marta';
  var typed=0,typeTimer=null;
  var loadPct=0,typeDone=false,loadDone=false;

  function setPct(n){
    loadPct=Math.max(0,Math.min(100,Math.round(n)));
    if(barEl)barEl.style.width=loadPct+'%';
    if(pctEl)pctEl.textContent=loadPct+'%';
    tryFinish();
  }

  function typeStep(){
    if(!namesEl||typed>=fullName.length){
      typeDone=true;
      tryFinish();
      return;
    }
    typed++;
    namesEl.textContent=fullName.slice(0,typed);
    var delay=fullName.charAt(typed-1)===' ' ? 120 : (fullName.charAt(typed-1)==='&' ? 200 : 85);
    typeTimer=setTimeout(typeStep,delay);
  }

  function tryFinish(){
    if(!loadDone||!typeDone)return;
    var wait=Math.max(0,minMs-(Date.now()-shownAt));
    setTimeout(function(){
      document.body.classList.remove('convite-loading');
      splash.setAttribute('aria-busy','false');
      splash.classList.add('is-hiding');
      setTimeout(function(){splash.remove();},520);
    },wait);
  }

  function preloadImages(urls){
    urls=urls||window.CONVITE_IMAGES||[];
    if(!urls.length){setPct(100);loadDone=true;tryFinish();return;}
    var done=0,total=urls.length;
    function bump(ok){
      done++;
      setPct((done/total)*100);
      if(done>=total){loadDone=true;tryFinish();}
    }
    urls.forEach(function(url){
      var img=new Image();
      img.onload=function(){bump(true);};
      img.onerror=function(){bump(false);};
      img.src=url;
    });
  }

  typeStep();
  preloadImages(window.CONVITE_IMAGES);
  setTimeout(function(){
    loadDone=true;
    typeDone=true;
    setPct(100);
    if(namesEl&&!namesEl.textContent)namesEl.textContent=fullName;
    tryFinish();
  },maxMs);
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
