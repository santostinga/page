<?php

declare(strict_types=1);

/**
 * Ilustrações vectoriais minimalistas por tema (SVG inline).
 */
function sizo_feature_art(string $slug): string
{
    return match ($slug) {
        'shell' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <rect x="28" y="40" width="144" height="112" rx="12" class="opacity-90"/>
  <path d="M28 58h144M52 40v18M148 40v18" stroke-linecap="round"/>
  <rect x="40" y="72" width="28" height="68" rx="4" class="opacity-70"/>
  <path d="M84 84h72M84 102h56M84 120h64M84 138h48" stroke-linecap="round" class="opacity-85"/>
  <circle cx="158" cy="138" r="10" class="opacity-80"/>
</svg>
SVG,
        'dashboard' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <rect x="36" y="44" width="128" height="96" rx="10"/>
  <path d="M52 140V92l24 28 20-36 16 24 24-36v68H52z" fill="currentColor" fill-opacity="0.15" stroke-linejoin="round"/>
  <path d="M52 152h96" stroke-linecap="round"/>
  <rect x="44" y="52" width="40" height="24" rx="4" class="opacity-6"/>
</svg>
SVG,
        'profile' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <circle cx="100" cy="72" r="28"/>
  <path d="M52 156c8-28 28-44 48-44s40 16 48 44" stroke-linecap="round"/>
  <circle cx="100" cy="100" r="52" class="opacity-30"/>
</svg>
SVG,
        'team' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <circle cx="72" cy="78" r="22"/><circle cx="128" cy="78" r="22"/>
  <path d="M44 154c6-22 22-34 36-34m40 0c14 0 30 12 36 34" stroke-linecap="round"/>
  <path d="M100 44v24M88 56l12 12 12-12" stroke-linecap="round" stroke-linejoin="round" class="opacity-70"/>
  <rect x="132" y="116" width="36" height="44" rx="6" class="opacity-40"/>
</svg>
SVG,
        'catalog' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <rect x="40" y="48" width="56" height="56" rx="8"/><rect x="104" y="48" width="56" height="56" rx="8"/>
  <rect x="40" y="112" width="56" height="56" rx="8"/><rect x="104" y="112" width="56" height="56" rx="8"/>
  <path d="M56 72h24M120 72h24M56 136h24M120 136h24" stroke-linecap="round" class="opacity-6"/>
</svg>
SVG,
        'people' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <path d="M52 56h96a8 8 0 018 8v96a8 8 0 01-8 8H52a8 8 0 01-8-8V64a8 8 0 018-8z"/>
  <path d="M68 88h64M68 108h48M68 128h56" stroke-linecap="round"/>
  <circle cx="88" cy="156" r="8" fill="currentColor" fill-opacity="0.25"/>
</svg>
SVG,
        'billing' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <path d="M56 48h88l16 20v96a12 12 0 01-12 12H56a12 12 0 01-12-12V60a12 12 0 0112-12z"/>
  <path d="M72 88h56M72 108h72M72 128h48" stroke-linecap="round"/>
  <path d="M120 152l16 16 32-32" stroke-linecap="round" stroke-linejoin="round" class="opacity-85"/>
</svg>
SVG,
        'finance' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <rect x="44" y="120" width="112" height="40" rx="8"/>
  <path d="M60 120V96c0-22 18-40 40-40s40 18 40 40v24" class="opacity-8"/>
  <circle cx="100" cy="76" r="20"/>
  <path d="M92 76h16M100 68v16" stroke-linecap="round"/>
</svg>
SVG,
        'reports' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <ellipse cx="100" cy="104" rx="52" ry="28"/>
  <path d="M100 76v56M76 104h48" stroke-linecap="round"/>
  <rect x="40" y="140" width="120" height="36" rx="6"/>
  <path d="M52 156h40M52 168h72" stroke-linecap="round" class="opacity-7"/>
</svg>
SVG,
        'settings' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-36 w-full max-w-[200px] mx-auto text-white/95" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
  <circle cx="100" cy="100" r="28"/>
  <path d="M100 56v16M100 128v16M56 100h16M128 100h16M72 72l12 12M116 116l12 12M116 84l12-12M72 128l12-12" stroke-linecap="round"/>
  <rect x="76" y="76" width="48" height="48" rx="10" class="opacity-25"/>
</svg>
SVG,
        default => '',
    };
}
