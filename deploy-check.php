<?php
/**
 * Diagnóstico no servidor. Abre: https://teu-dominio/deploy-check.php
 * Apaga este ficheiro depois de resolver o deploy.
 */
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/plain; charset=UTF-8');

$root = __DIR__;
$rootReal = @realpath($root) ?: $root;

echo "deploy-check ok\n";
echo 'php=' . PHP_VERSION . "\n";
echo 'doc_root=' . $rootReal . "\n";
echo 'script=' . (isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : 'n/a') . "\n";
echo 'index_php_mtime_utc=' . gmdate('Y-m-d\TH:i:s\Z', @filemtime($root . '/index.php')) . "\n";

// Se existir index.html, muitos servidores servem esse primeiro — o site parece "congelado".
$idxHtml = $root . '/index.html';
echo 'index_html_exists=' . (is_file($idxHtml) ? 'YES_PROBLEMA_apaga_renomeia_no_servidor' : 'no') . "\n";

// Cabeçalhos enviados pelo index.php (útil se não corres PHP novo)
$sizoHdr = @file_get_contents($root . '/index.php', false, null, 0, 400);
if ($sizoHdr !== false && strpos($sizoHdr, 'Cache-Control') !== false) {
    echo "index_php_tem_headers_cache=no-store_sim\n";
} else {
    echo "index_php_tem_headers_cache=nao_verificado_ou_ficheiro_antigo\n";
}

$gitHead = $root . '/.git/HEAD';
if (!is_readable($gitHead)) {
    echo "git_folder=no\n";
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
echo "---\n";
echo "Se git_commit bate com GitHub mas o site parece velho:\n";
echo "1) Painel Cloudways: Purge Varnish/cache da app + Restart PHP-FPM.\n";
echo "2) Se index_html_exists=YES: apaga ou renomeia index.html nessa pasta.\n";
echo "3) Confirma em SSH: pwd = doc_root acima; git pull na MESMA pasta.\n";
