<?php

/**
 * URL de asset com versão automática (filemtime) para invalidar cache do browser.
 */
function sizo_asset(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $full = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
    $version = is_file($full) ? (string) filemtime($full) : '1';

    return $path . '?v=' . rawurlencode($version);
}
