<?php
/**
 * Abre no browser: https://teu-dominio.tld/deploy-check.php
 * Remove este ficheiro depois de resolver o deploy (evita expor detalhes).
 */
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/plain; charset=UTF-8');

$root = __DIR__;
echo "deploy-check ok\n";
echo 'php_version=' . PHP_VERSION . "\n";
echo 'index_mtime_utc=' . gmdate('Y-m-d\TH:i:s\Z', @filemtime($root . '/index.php')) . "\n";

$gitHead = $root . '/.git/HEAD';
if (!is_readable($gitHead)) {
    echo "git_folder=no (esta pasta não é um clone git ou o deploy não trouxe .git)\n";
    exit;
}

echo "git_folder=yes\n";
$head = trim((string) file_get_contents($gitHead));
$sha = '';
if (strpos($head, 'ref:') === 0) {
    $refRel = trim(substr($head, 4));
    $refPath = $root . '/.git/' . str_replace('/', DIRECTORY_SEPARATOR, $refRel);
    if (is_readable($refPath)) {
        $sha = substr(trim((string) file_get_contents($refPath)), 0, 12);
    }
} elseif ($head !== '') {
    $sha = substr($head, 0, 12);
}

echo 'git_commit=' . ($sha !== '' ? $sha : '(unknown)') . "\n";
echo "hint=Este hash deve coincidir com o ultimo commit em github.com na branch main.\n";
