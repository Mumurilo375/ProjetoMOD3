<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Database;
use App\Model\Filme;
use App\Model\Avaliacao;
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }


$em = Database::getEntityManager();
$q = trim($_GET['q'] ?? '');

$page = max(1, (int)($_GET['page'] ?? 1));
$pageSize = 7;

$qb = $em->createQueryBuilder();
$qb->select('f')
   ->from(Filme::class, 'f')
   ->leftJoin(\App\Model\Avaliacao::class, 'a', 'WITH', 'a.filme = f')
   ->where('f.anoLancamento = :ano')
   ->setParameter('ano', 2025)
   ->groupBy('f.id');

if ($q !== '') {
    $qb->andWhere($qb->expr()->like('LOWER(f.titulo)', ':q'))
       ->setParameter('q', '%' . strtolower($q) . '%');
}

// ordem por média
$qb->orderBy('AVG(a.nota)', 'DESC');

$countQb = $em->createQueryBuilder();
$countQb->select('COUNT(f.id)')
  ->from(Filme::class, 'f')
  ->where('f.anoLancamento = :ano')
  ->setParameter('ano', 2025);
if ($q !== '') {
    $countQb->andWhere($countQb->expr()->like('LOWER(f.titulo)', ':q'))
      ->setParameter('q', '%' . strtolower($q) . '%');
}
$total = (int)$countQb->getQuery()->getSingleScalarResult();

$qb->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize);
$filmes = $qb->getQuery()->getResult();

function scoreTier(int $score): string { return $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low'); }
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lançamentos (2025) | StarRate</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/filmes.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="section">
    <div class="container">
      <h1 class="page-title">Lançamentos (2025)</h1>

      <form class="movie-search" method="get" action="">
        <input name="q" type="search" placeholder="Buscar por título" value="<?= htmlspecialchars($q) ?>" aria-label="Buscar por título" />
        <button class="btn btn-primary" type="submit">Buscar</button>
      </form>

      <ul class="movie-list">
        <?php foreach ($filmes as $filme): ?>
          <?php 
            $mid = \App\Model\Avaliacao::getMediaPorFilmeId($filme->getId());
            $score = $mid !== null ? (int)round($mid) : null;
            $tier = $score !== null ? scoreTier($score) : '';
          ?>
          <li class="movie-item" data-href="/ProjetoMOD3-limpo/public/filme.php?id=<?= (int)$filme->getId() ?>" tabindex="0" aria-label="Ver detalhes de <?= htmlspecialchars($filme->getTitulo()) ?>">
            <a class="poster" href="/ProjetoMOD3-limpo/public/filme.php?id=<?= (int)$filme->getId() ?>">
              <img src="<?= htmlspecialchars($filme->getCapa()) ?>" alt="Capa de <?= htmlspecialchars($filme->getTitulo()) ?>" />
            </a>
            <div class="content">
              <h3>
                <a class="movie-title-link" href="/ProjetoMOD3-limpo/public/filme.php?id=<?= (int)$filme->getId() ?>">
                  <strong><?= htmlspecialchars($filme->getTitulo()) ?></strong>
                </a>
                - <span class="genre"><?= htmlspecialchars($filme->getGenero()) ?></span>
              </h3>
              <p class="overview"><?= nl2br(htmlspecialchars($filme->getSinopse())) ?></p>
              <div class="meta-year"><?= (int)$filme->getAnoLancamento() ?></div>
            </div>
    <div class="score-col">
              <?php if ($score !== null): ?>
                <div class="score-card <?= $tier ?>">
                  <div class="score-badge <?= $tier ?>">
                    <span class="label">MÉDIA</span>
                    <span class="value"><?= $score ?></span>
                  </div>
      <button class="btn rate-btn" type="button" onclick='openRateModal({id: <?= (int)$filme->getId() ?>, titulo: <?= json_encode($filme->getTitulo()) ?>, capa: <?= json_encode($filme->getCapa()) ?>, ano: <?= (int)$filme->getAnoLancamento() ?>, genero: <?= json_encode($filme->getGenero()) ?>})'>Avaliar</button>
                </div>
              <?php else: ?>
                <div class="score-card no-score">
                  <span class="no-score-text">Sem notas</span>
      <button class="btn rate-btn" type="button" onclick='openRateModal({id: <?= (int)$filme->getId() ?>, titulo: <?= json_encode($filme->getTitulo()) ?>, capa: <?= json_encode($filme->getCapa()) ?>, ano: <?= (int)$filme->getAnoLancamento() ?>, genero: <?= json_encode($filme->getGenero()) ?>})'>Avaliar</button>
                </div>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if (count($filmes) === 0): ?>
        <div class="card" style="margin-top:18px; text-align:center;">
          <h3 style="margin:0 0 8px;">Nenhum filme encontrado</h3>
          <?php if ($q !== ''): ?>
            <p style="margin:0; color:hsl(var(--muted-foreground));">Não encontramos resultados para "<?= htmlspecialchars($q) ?>". Tente outra palavra-chave ou volte à lista completa.</p>
            <div style="margin-top:12px;"><a class="btn" href="/ProjetoMOD3-limpo/public/lancamentos.php">Ver todos os lançamentos</a></div>
          <?php else: ?>
            <p style="margin:0; color:hsl(var(--muted-foreground));">Ainda não há lançamentos cadastrados para este ano.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($total > 0 && count($filmes) > 0): ?>
        <div class="pagination" style="display:flex; gap:8px; margin-top:18px; align-items:center;">
          <?php if ($page > 1): ?>
            <a class="btn btn-ghost" href="?q=<?= urlencode($q) ?>&page=<?= $page - 1 ?>">Anterior</a>
          <?php endif; ?>
          <div style="color:hsl(var(--muted-foreground));">Página <?= $page ?> de <?= max(1, ceil($total / $pageSize)) ?></div>
          <?php if ($page * $pageSize < $total): ?>
            <a class="btn btn-ghost" href="?q=<?= urlencode($q) ?>&page=<?= $page + 1 ?>">Próximos</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </main>
  <?php include __DIR__ . '/partials/rate_modal.php'; ?>
  <script>
    (function(){
      function isInteractive(el){
        if(!el) return false;
        const tag = el.tagName && el.tagName.toLowerCase();
        if(!tag) return false;
        return ['a','button','input','select','textarea','label'].includes(tag) || el.closest('a, button, [role="button"]');
      }
      document.addEventListener('click', function(e){
        const item = e.target.closest('.movie-item');
        if(!item) return;
        if(isInteractive(e.target)) return;
        const href = item.getAttribute('data-href');
        if(href) location.href = href;
      });
      document.addEventListener('keydown', function(e){
        if(e.key !== 'Enter') return;
        const active = document.activeElement;
        if(active && active.classList && active.classList.contains('movie-item')){
          const href = active.getAttribute('data-href');
          if(href) location.href = href;
        }
      });
    })();
  </script>
</body>
</html>
