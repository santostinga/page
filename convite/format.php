<?php
declare(strict_types=1);

/** Ex.: «Celeste e Elsinha» — primeira letra maiúscula; «e», «de», «da»… minúsculas. */
function convite_format_guest_display(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        return '';
    }

    $lower = mb_strtolower($name, 'UTF-8');
    $particles = ['e', 'de', 'da', 'do', 'dos', 'das', 'o', 'a'];
    $words = preg_split('/\s+/u', $lower, -1, PREG_SPLIT_NO_EMPTY);
    $out = [];

    foreach ($words as $i => $word) {
        if ($i > 0 && in_array($word, $particles, true)) {
            $out[] = $word;
            continue;
        }
        $first = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
        $rest = mb_substr($word, 1, null, 'UTF-8');
        $out[] = $first . $rest;
    }

    return implode(' ', $out);
}
