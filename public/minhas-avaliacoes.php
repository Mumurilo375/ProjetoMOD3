<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Model\Avaliacao;
use App\Model\Filme;
use App\Model\User;

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    header('Location: /ProjetoMOD3-limpo/public/auth.php?view=login&err=auth');
    exit;
}

$em = Database::getEntityManager();
/** @var User|null $user */
$user = $em->find(User::class, (int)$_SESSION['user_id']);
if (!$user) {
    header('Location: /ProjetoMOD3-limpo/public/logout.php');
    exit;
}

// Buscar avaliações do usuário, mais recentes primeiro (lazy-load do filme)
$page = max(1, (int)($_GET['page'] ?? 1));
$pageSize = 3; // 3 avaliações por página

// total de avaliações do usuário (para paginação)
$countQb = $em->createQueryBuilder();
$countQb->select('COUNT(a.id)')
  ->from(Avaliacao::class, 'a')
  ->where('a.usuario = :user')
  ->setParameter('user', $user->getId());
$total = (int)$countQb->getQuery()->getSingleScalarResult();

// buscar avaliações paginadas do usuário
$qb = $em->createQueryBuilder();
$qb->select('a')
  ->from(Avaliacao::class, 'a')
  ->where('a.usuario = :user')
  ->setParameter('user', $user->getId())
  ->orderBy('a.dataAvaliacao', 'DESC')
  ->setFirstResult(($page - 1) * $pageSize)
  ->setMaxResults($pageSize);
$avaliacoes = $qb->getQuery()->getResult();

function fmtDate(DateTime $dt): string { return $dt->format('d/m/Y H:i'); }
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Minhas avaliações | StarRate</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/filmes.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <style>
    .my-reviews { display:grid; gap:12px; }
    .review-card { display:grid; grid-template-columns: 92px 1fr; gap:12px; padding:12px; border:1px solid hsl(var(--border)); border-radius:12px; background:hsl(var(--card)); }
    .review-card img { width:92px; height:138px; object-fit:cover; border-radius:8px; }
    .review-head { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .review-title { margin:0; font-size:16px; }
    .badge { display:inline-flex; align-items:center; gap:6px; border-radius:999px; padding:4px 10px; font-weight:600; font-size:12px; }
    .badge .value { font-size:14px; }
    .badge.score-high { background:rgba(16,185,129,.15); color:#16a34a; }
    .badge.score-mid { background:rgba(234,179,8,.15); color:#b45309; }
    .badge.score-low { background:rgba(239,68,68,.15); color:#b91c1c; }
    .review-meta { color:hsl(var(--muted-foreground)); font-size:12px; }
    .review-body { margin-top:6px; white-space:pre-wrap; }
  /* back-circle (same look as filme.php) */
  /* reduced by ~10% to match requested size */
  .back-circle { width:36px; height:36px; border-radius:50%; background:#ffd400; border:2px solid #000; display:inline-grid; place-items:center; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.12); }
  .back-circle svg { width:16px; height:16px; fill:#000; transform: rotate(0deg); }
  /* layout for title + back button */
  .title-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
  .title-row .page-title { margin:0; line-height:1.1; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="section">
    <div class="container">
      <div class="title-row">
        <button type="button" class="back-circle" id="btn-go-back-reviews" title="Voltar" aria-label="Voltar">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 6.5c-.28 0-.53.11-.72.29L8.7 10.58a1 1 0 0 0 0 1.41l4.08 3.79c.39.36 1.02.34 1.4-.05.36-.36.36-.95 0-1.31L11.6 12l3.28-3.15c.36-.34.36-.92 0-1.28-.19-.19-.44-.29-.72-.29z"/></svg>
        </button>
        <h1 class="page-title">Minhas avaliações</h1>
      </div>

      <?php if (empty($avaliacoes)): ?>
        <p style="color:hsl(var(--muted-foreground));">Você ainda não avaliou nenhum filme.</p>
      <?php else: ?>
        <div class="my-reviews">
          <?php foreach ($avaliacoes as $a): /** @var \App\Model\Avaliacao $a */ ?>
            <?php $f = $a->getFilme(); $score = (int)$a->getNota(); $tier = $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low'); ?>
            <article class="review-card" data-href="/ProjetoMOD3-limpo/public/filme.php?id=<?= (int)$f->getId() ?>" tabindex="0" aria-label="Ver detalhes de <?= htmlspecialchars($f->getTitulo()) ?>">
              <img src="<?= htmlspecialchars($f->getCapa()) ?>" alt="Capa de <?= htmlspecialchars($f->getTitulo()) ?>" />
              <div>
                <div class="review-head">
                  <h3 class="review-title"><?= htmlspecialchars($f->getTitulo()) ?> <span class="review-meta">(<?= (int)$f->getAnoLancamento() ?> · <?= htmlspecialchars($f->getGenero()) ?>)</span></h3>
                  <span class="badge <?= $tier ?>"><span>NOTA</span> <span class="value"><?= $score ?></span></span>
                  <span class="review-meta">em <?= fmtDate($a->getDataAvaliacao()) ?></span>
                </div>
                <?php if ($a->getComentario()): ?>
                  <div class="review-body"><?= htmlspecialchars($a->getComentario()) ?></div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if ($total > 0 && count($avaliacoes) > 0): ?>
        <div class="pagination" style="display:flex; gap:8px; margin-top:18px; align-items:center;">
          <?php if ($page > 1): ?>
            <a class="btn btn-ghost" href="?page=<?= $page - 1 ?>">Anterior</a>
          <?php endif; ?>
          <div style="color:hsl(var(--muted-foreground));">Página <?= $page ?> de <?= max(1, ceil($total / $pageSize)) ?></div>
          <?php if ($page * $pageSize < $total): ?>
            <a class="btn btn-ghost" href="?page=<?= $page + 1 ?>">Próximos</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
  <script>
    (function(){
      var back = document.getElementById('btn-go-back-reviews');
      if(!back) return;
      back.addEventListener('click', function(e){
        e.preventDefault();
        // prefer history.back when possible, fallback to index
        if(window.history && window.history.length > 1){
          window.history.back();
        } else {
          window.location.href = '/ProjetoMOD3-limpo/public/index.php';
        }
      });
    })();

    // Make review cards clickable (click or Enter key)
    (function(){
      var cards = document.querySelectorAll('.review-card[data-href]');
      if(!cards.length) return;
      cards.forEach(function(c){
        c.style.cursor = 'pointer';
        c.addEventListener('click', function(e){
          var href = c.getAttribute('data-href');
          if(href) window.location.href = href;
        });
        c.addEventListener('keydown', function(e){
          if(e.key === 'Enter' || e.keyCode === 13){
            var href = c.getAttribute('data-href');
            if(href) window.location.href = href;
          }
        });
      });
    })();
  </script>
</body>
</html>
