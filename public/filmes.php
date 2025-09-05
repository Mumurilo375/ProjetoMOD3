<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Model\Filme;
use App\Model\Avaliacao; // import mantido, porém usaremos FQN no loop por segurança

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$em = Database::getEntityManager();
$repo = $em->getRepository(Filme::class);

$q = trim($_GET['q'] ?? '');

// Busca simples por título quando houver q; caso contrário, lista todos, mais recentes primeiro
if ($q !== '') {
    $qb = $em->createQueryBuilder();
    $qb->select('f')
       ->from(Filme::class, 'f')
       ->where($qb->expr()->like('LOWER(f.titulo)', ':q'))
       ->setParameter('q', '%' . strtolower($q) . '%')
       ->orderBy('f.id', 'DESC');
    $filmes = $qb->getQuery()->getResult();
} else {
    $filmes = $repo->findBy([], ['id' => 'DESC']);
}

function scoreTier(int $score): string { return $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low'); }
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Avalie Filmes | StarRate</title>
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
      <h1 class="page-title">Avalie Filmes</h1>

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
          <li class="movie-item">
            <div class="poster">
              <img src="<?= htmlspecialchars($filme->getCapa()) ?>" alt="Capa de <?= htmlspecialchars($filme->getTitulo()) ?>" />
            </div>
            <div class="content">
              <h3>
                <strong><?= htmlspecialchars($filme->getTitulo()) ?></strong>
                (<?= (int)$filme->getAnoLancamento() ?>)
                - <span class="genre"><?= htmlspecialchars($filme->getGenero()) ?></span>
              </h3>
              <p class="overview"><?= nl2br(htmlspecialchars($filme->getSinopse())) ?></p>
            </div>
            <div class="score-col">
              <?php if ($score !== null): ?>
                <div class="score-card <?= $tier ?>">
                  <div class="score-badge <?= $tier ?>">
                    <span class="label">MÉDIA</span>
                    <span class="value"><?= $score ?></span>
                  </div>
                  <a class="btn rate-btn" href="#">Avaliar</a>
                </div>
              <?php else: ?>
                <div class="score-card no-score">
                  <span class="no-score-text">Sem notas</span>
                  <a class="btn rate-btn" href="#">Avaliar</a>
                </div>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

    </div>
  </main>
</body>
</html>
