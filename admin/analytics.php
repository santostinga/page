<?php
require_once __DIR__ . '/../config/https.php';
require_once __DIR__ . '/../config/analytics.php';

session_start();

$token = sizo_analytics_token();
$authenticated = !empty($_SESSION['analytics_auth']) && sizo_analytics_token_valid($token);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = trim((string) ($_POST['token'] ?? ''));
    if (sizo_analytics_token_valid($submitted)) {
        $_SESSION['analytics_auth'] = true;
        $authenticated = true;
    }
}

if (!$authenticated) {
    $pageTitle = 'Analytics | Sizo Software';
    $skipAnalytics = true;
    require __DIR__ . '/../includes/head.php';
    ?>
    <main class="min-h-screen bg-slate-50 px-4 py-16">
      <div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <h1 class="text-2xl font-bold text-slate-950">Analytics do website</h1>
        <p class="mt-2 text-sm text-slate-600">Introduza o token definido em <code class="rounded bg-slate-100 px-1">ANALYTICS_TOKEN</code>.</p>
        <form method="post" class="mt-6 space-y-4">
          <label class="block text-sm font-semibold text-slate-700">Token
            <input type="password" name="token" required autocomplete="current-password" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5">
          </label>
          <button type="submit" class="w-full rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">Entrar</button>
        </form>
      </div>
    </main>
    <?php require __DIR__ . '/../includes/footer.php'; exit;
}

$period = (string) ($_GET['period'] ?? 'today');
$stats = sizo_analytics_stats($period);
$pageTitle = 'Analytics | Sizo Software';
$skipAnalytics = true;
require __DIR__ . '/../includes/head.php';

function sizo_analytics_fmt_int(int $value): string
{
    return number_format($value, 0, ',', ' ');
}

$summary = $stats['summary'] ?? ['pageviews' => 0, 'unique_visitors' => 0, 'sessions' => 0, 'clicks' => 0];
$periods = ['today' => 'Hoje', '7d' => '7 dias', '30d' => '30 dias'];
?>
<main class="min-h-screen bg-slate-50 px-4 py-10 sm:px-6">
  <div class="mx-auto max-w-6xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold text-brand">Monitorização</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Analytics do website</h1>
        <p class="mt-2 text-sm text-slate-600">Acessos, páginas, cliques, origens e dispositivos.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($periods as $key => $label): ?>
          <a href="?period=<?= urlencode($key) ?>" class="rounded-lg px-4 py-2 text-sm font-semibold <?= $period === $key ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-100' ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (empty($stats['ok'])): ?>
      <div class="mt-8 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
        Não foi possível carregar estatísticas. Verifique a ligação à base de dados no servidor.
      </div>
    <?php else: ?>
      <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php
        $cards = [
          ['Pageviews', (int) $summary['pageviews']],
          ['Visitantes únicos', (int) $summary['unique_visitors']],
          ['Sessões', (int) $summary['sessions']],
          ['Cliques', (int) $summary['clicks']],
        ];
        foreach ($cards as [$title, $value]):
        ?>
          <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-soft">
            <p class="text-sm font-medium text-slate-500"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-950"><?= sizo_analytics_fmt_int($value) ?></p>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($stats['daily'])): ?>
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
          <h2 class="text-lg font-bold text-slate-950">Evolução diária</h2>
          <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="text-xs uppercase tracking-wide text-slate-400"><tr><th class="py-2 pr-4">Dia</th><th class="py-2 pr-4">Pageviews</th><th class="py-2">Visitantes</th></tr></thead>
              <tbody class="text-slate-700">
                <?php foreach ($stats['daily'] as $row): ?>
                  <tr class="border-t border-slate-100"><td class="py-2.5 pr-4"><?= htmlspecialchars((string) $row['day'], ENT_QUOTES, 'UTF-8') ?></td><td class="py-2.5 pr-4"><?= sizo_analytics_fmt_int((int) $row['pageviews']) ?></td><td class="py-2.5"><?= sizo_analytics_fmt_int((int) $row['unique_visitors']) ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      <?php endif; ?>

      <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <?php
        $lists = [
          ['Páginas mais vistas', $stats['pages'] ?? []],
          ['Origens de tráfego', $stats['referrers'] ?? []],
          ['Cliques mais frequentes', $stats['clicks'] ?? []],
          ['Secções visualizadas', $stats['sections'] ?? []],
          ['Países', $stats['countries'] ?? []],
          ['Dispositivos', $stats['devices'] ?? []],
        ];
        foreach ($lists as [$title, $rows]):
        ?>
          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
            <h2 class="text-lg font-bold text-slate-950"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
            <?php if (!$rows): ?>
              <p class="mt-4 text-sm text-slate-500">Sem dados neste período.</p>
            <?php else: ?>
              <ul class="mt-4 space-y-3">
                <?php foreach ($rows as $row): ?>
                  <li class="flex items-center justify-between gap-4 text-sm">
                    <span class="min-w-0 truncate text-slate-700" title="<?= htmlspecialchars((string) $row['label'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $row['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700"><?= sizo_analytics_fmt_int((int) $row['total']) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </section>
        <?php endforeach; ?>
      </div>

      <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
        <h2 class="text-lg font-bold text-slate-950">API JSON</h2>
        <p class="mt-2 text-sm text-slate-600">Consulta programática com o mesmo token:</p>
        <pre class="mt-4 overflow-x-auto rounded-xl bg-slate-950 p-4 text-xs text-slate-100">GET /api/analytics/stats?period=today&amp;token=SEU_TOKEN</pre>
      </section>
    <?php endif; ?>
  </div>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
