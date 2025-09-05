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
$avaliacoes = $em->getRepository(Avaliacao::class)->findBy(
  ['usuario' => $user],
  ['dataAvaliacao' => 'DESC']
);

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
  </style>
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="section">
    <div class="container">
      <h1 class="page-title">Minhas avaliações</h1>

      <?php if (empty($avaliacoes)): ?>
        <p style="color:hsl(var(--muted-foreground));">Você ainda não avaliou nenhum filme.</p>
      <?php else: ?>
        <div class="my-reviews">
          <?php foreach ($avaliacoes as $a): /** @var \App\Model\Avaliacao $a */ ?>
            <?php $f = $a->getFilme(); $score = (int)$a->getNota(); $tier = $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low'); ?>
            <article class="review-card">
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

    </div>
  </main>
</body>
</html>
