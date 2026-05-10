<?php
/**
 * Abre no browser: https://teu-dominio/probe.php
 * Se der 404 depois de enviar para public_html → o domínio NÃO aponta para essa app/pasta.
 * Apaga este ficheiro quando já não precisares.
 */
header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, no-cache');
echo "probe_ok\n";
echo 'dir=' . __DIR__ . "\n";
echo 'realpath=' . (realpath(__DIR__) ?: '?') . "\n";
echo 'script=' . ($_SERVER['SCRIPT_FILENAME'] ?? '?') . "\n";
echo 'time_utc=' . gmdate('Y-m-d\TH:i:s\Z') . "\n";
