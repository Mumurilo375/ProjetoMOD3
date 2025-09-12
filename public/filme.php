<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Model\Filme;
use App\Model\Avaliacao;
use App\Model\User;

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$em = Database::getEntityManager();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  http_response_code(404);
  echo '<!doctype html><meta charset="utf-8"><title>Filme não encontrado</title><p>Filme não encontrado.</p>';
  exit;
}

/** @var Filme|null $filme */
$filme = $em->find(Filme::class, $id);
if (!$filme) {
  http_response_code(404);
  echo '<!doctype html><meta charset="utf-8"><title>Filme não encontrado</title><p>Filme não encontrado.</p>';
  exit;
}

// Média e contagem
$media = Avaliacao::getMediaPorFilmeId($filme->getId());
$total = Avaliacao::getContagemPorFilmeId($filme->getId());
$mediaInt = $media !== null ? (int)round($media) : null;

// Avaliações (mais recentes primeiro)
$qb = $em->createQueryBuilder();
$qb->select('a', 'u')
   ->from(Avaliacao::class, 'a')
   ->leftJoin('a.usuario', 'u')
   ->where('a.filme = :fid')
   ->setParameter('fid', $filme->getId())
   ->orderBy('a.dataAvaliacao', 'DESC');
$avaliacoes = $qb->getQuery()->getResult();

function badgeTier(?int $score): string {
  if ($score === null) return 'score-mid';
  return $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low');
}

function srcPublic(string $path): string {
  // Normaliza caminhos que já venham absolutos
  if (str_starts_with($path, '/')) return $path;
  // Remove prefixo 'public/' se vier do banco assim
  if (str_starts_with($path, 'public/')) {
    $path = substr($path, 7);
  }
  return '/ProjetoMOD3-limpo/public/' . ltrim($path, '/');
}

?><!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($filme->getTitulo()) ?> | StarRate</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/filmes.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <style>
    .movie-page { display:grid; grid-template-columns: 300px 1fr; gap: 20px; }
    .poster { border-radius: 12px; overflow:hidden; border:1px solid hsl(var(--border)); box-shadow: var(--shadow-soft); }
    .poster img { width:100%; height:auto; display:block; }
    .side { display:grid; gap:12px; align-content:start; }
  .btn-trailer { display:flex; align-items:center; justify-content:center; gap:10px; padding:12px 14px; border-radius:12px; border:1px solid hsl(var(--border)); background:hsl(var(--muted)); color:hsl(var(--foreground)); cursor:pointer; font-size:16px; transition: transform .16s ease, box-shadow .16s ease, filter .16s ease; width:100%; max-width:300px; }
  .btn-trailer:hover { transform: translateY(-1px); filter: brightness(1.05); box-shadow: var(--shadow-soft); }
  .btn-trailer:active { transform: translateY(0); filter: brightness(1); box-shadow: none; }
  /* wrapper para o botão de trailer e o botão de voltar circular abaixo (centralizado) */
  .trailer-wrap { display:flex; flex-direction:column; align-items:center; gap:10px; }
  .back-circle { width:40px; height:40px; border-radius:50%; background:#ffd400; border:2px solid #000; display:inline-grid; place-items:center; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.12); }
  .back-circle svg { width:18px; height:18px; fill:#000; transform: rotate(0deg); }

    .movie-header { display:grid; gap:8px; }
    .movie-title { margin:0; font-size: 44px; line-height:1.08; font-weight:800; }
    .movie-meta { color:hsl(var(--muted-foreground)); font-size:16px; display:flex; gap:12px; flex-wrap:wrap; }
    .movie-syn { margin-top: 8px; font-size:16px; line-height:1.6; }

    .rating-box { margin-left:auto; text-align:center; border:1px solid hsl(var(--border)); background:hsl(var(--card)); padding:12px 14px; border-radius:14px; min-width:130px; }
    .rating-box .num { font-size:34px; font-weight:800; }
    .rating-box .label { color:hsl(var(--muted-foreground)); font-size:13px; }

    .header-row { display:flex; align-items:flex-start; gap:16px; }

    .reviews { margin-top: 20px; display:grid; gap:14px; }
    .review-item { border:1px solid hsl(var(--border)); background:hsl(var(--card)); border-radius:12px; padding:14px; display:grid; grid-template-columns: 54px 1fr; gap:12px; }
    .review-item .avatar { width:48px; height:48px; }
    .review-head { display:flex; align-items:center; gap:10px; justify-content:space-between; }
    .review-name { font-weight:700; font-size:16px; }
    .review-note { font-weight:800; font-size:18px; }
    .review-date { color:hsl(var(--muted-foreground)); font-size:13px; }
  .score-high { color: hsl(var(--rating-high)); }
  .score-mid { color: hsl(var(--rating-mid)); }
  .score-low { color: hsl(var(--rating-low)); }

  @media (max-width: 800px){ .movie-page { grid-template-columns: 1fr; } .rating-box { min-width:auto; } .movie-title { font-size:36px; } }
  </style>
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>
  <main class="section">
    <div class="container movie-page">
      <aside class="side">
        <figure class="poster">
          <?php
            $capa = $filme->getCapa();
            $srcCapa = srcPublic($capa);
          ?>
          <img src="<?= htmlspecialchars($srcCapa) ?>" alt="Capa de <?= htmlspecialchars($filme->getTitulo()) ?>">
        </figure>
  <div class="trailer-wrap">
    <button type="button" class="btn-trailer" id="open-trailer">Ver trailer</button>
    <button type="button" class="back-circle" id="btn-go-back" title="Voltar" aria-label="Voltar">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 6.5c-.28 0-.53.11-.72.29L8.7 10.58a1 1 0 0 0 0 1.41l4.08 3.79c.39.36 1.02.34 1.4-.05.36-.36.36-.95 0-1.31L11.6 12l3.28-3.15c.36-.34.36-.92 0-1.28-.19-.19-.44-.29-.72-.29z"/></svg>
    </button>
  </div>
      </aside>
      <section>
        <div class="header-row">
          <div class="movie-header">
            <h1 class="movie-title"><?= htmlspecialchars($filme->getTitulo()) ?></h1>
            <div class="movie-meta">
              <span><?= htmlspecialchars((string)$filme->getAnoLancamento()) ?></span>
              <span>•</span>
              <span><?= htmlspecialchars($filme->getGenero()) ?></span>
              <span>•</span>
              <span>Diretor(a): <?= htmlspecialchars($filme->getDiretor()) ?></span>
            </div>
            <p class="movie-syn"><?= nl2br(htmlspecialchars($filme->getSinopse())) ?></p>
          </div>
          <div class="rating-box">
            <div class="label">Média</div>
            <div class="num <?= badgeTier($mediaInt) ?>"><?= $mediaInt !== null ? $mediaInt : '—' ?></div>
            <div class="label"><?= (int)$total ?> avaliações</div>
            <?php
              // Determina se o usuário logado já avaliou este filme
              $userHasReviewed = false;
              if (isset($_SESSION['user_id'])) {
                $currentUser = $em->find(\App\Model\User::class, (int)$_SESSION['user_id']);
                if ($currentUser) {
                  $existing = \App\Model\Avaliacao::findOneByUserAndFilme($currentUser, $filme);
                  $userHasReviewed = $existing !== null;
                }
              }
            ?>
            <?php if ($userHasReviewed): ?>
              <div style="margin-top:10px;">
                <span style="color:hsl(var(--muted-foreground)); padding:8px 10px; border-radius:8px; display:inline-block;">Avaliado</span>
              </div>
            <?php else: ?>
              <div style="margin-top:10px;">
                <button class="btn btn-primary" type="button" onclick='openRateModal({id: <?= (int)$filme->getId() ?>, titulo: <?= json_encode($filme->getTitulo()) ?>, capa: <?= json_encode($srcCapa) ?>, ano: <?= (int)$filme->getAnoLancamento() ?>, genero: <?= json_encode($filme->getGenero()) ?>})' aria-label="Avaliar <?= htmlspecialchars($filme->getTitulo()) ?>">Avaliar</button>
              </div>
            <?php endif; ?>
          </div>
        </div>

  <h2 style="margin:18px 0 8px;">Avaliações</h2>
        <div class="reviews">
          <?php if (!$avaliacoes): ?>
            <div style="padding:10px 0; color:hsl(var(--muted-foreground));">Nenhuma avaliação ainda</div>
          <?php else: ?>
            <?php foreach ($avaliacoes as $av): /** @var Avaliacao $av */ ?>
              <?php $u = $av->getUsuario(); /** @var User $u */ ?>
              <article class="review-item">
                <span class="avatar<?= strtolower($u->getNivelAcesso()) === 'admin' ? ' admin' : '' ?>" aria-hidden="true" style="overflow:hidden;">
                  <?php
                    $foto = $u->getFotoPerfil();
                    if ($foto && str_starts_with($foto, '/ProjetoMOD3-limpo/public/img/fotoPerfil/')) {
                      $fs = __DIR__ . str_replace('/ProjetoMOD3-limpo/public', '', $foto);
                      if (is_file($fs)) { $foto .= '?v=' . @filemtime($fs); }
                    }
                  ?>
                  <?php if ($foto): ?>
                    <img src="<?= htmlspecialchars($foto) ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" />
                  <?php else: ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8"/></svg>
                  <?php endif; ?>
                </span>
                <div>
                  <div class="review-head">
                    <div>
                      <div class="review-name"><?= htmlspecialchars($u->getNome()) ?></div>
                      <div class="review-date"><?= htmlspecialchars($av->getDataAvaliacao()->format('d/m/Y H:i')) ?></div>
                    </div>
                    <div class="review-note <?= badgeTier($av->getNota()) ?>"><?= (int)$av->getNota() ?></div>
                  </div>
                  <?php if ($av->getComentario()): ?>
                  <p style="margin:8px 0 0;"><?= nl2br(htmlspecialchars($av->getComentario())) ?></p>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </main>
  <?php
    // Extrai ID do YouTube (aceita URLs comuns) ou usa URL inteira no embed
    $yt = $filme->getTrailer();
    $embedSrc = null;
    if ($yt) {
      $url = $yt;
      $vid = null;
      if (preg_match('~(?:youtu\.be/|v=|embed/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
        $vid = $m[1];
      }
      if ($vid) {
        $embedSrc = 'https://www.youtube.com/embed/' . $vid . '?autoplay=1&rel=0';
      } else {
        // fallback direto
        $embedSrc = $url;
      }
    }
  ?>
  <div id="trailer-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-backdrop" data-close></div>
  <div class="modal-content" role="document" style="width:min(1100px,96vw);">
      <button class="modal-close" aria-label="Fechar" data-close>&times;</button>
      <header class="rate-header"><h2 class="rate-title" style="margin:0;">Trailer</h2></header>
      <div style="padding:12px;">
        <?php if ($embedSrc): ?>
          <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:12px; border:1px solid hsl(var(--border));">
            <iframe id="yt-frame" data-src="<?= htmlspecialchars($embedSrc) ?>" title="Trailer" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"></iframe>
          </div>
        <?php else: ?>
          <p style="margin:0;">Nenhum trailer cadastrado para este filme.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <script>
    (function(){
      var btn = document.getElementById('open-trailer');
      var modal = document.getElementById('trailer-modal');
      if(!btn || !modal) return;
      var closeEls = modal.querySelectorAll('[data-close]');
      function open(){
        modal.setAttribute('aria-hidden','false');
        // carrega o vídeo apenas ao abrir
        var f = document.getElementById('yt-frame');
        if(f && !f.getAttribute('src')){
          var s = f.getAttribute('data-src');
          if(s) f.setAttribute('src', s);
        }
      }
      function close(){
        modal.setAttribute('aria-hidden','true');
        // descarrega o iframe para pausar áudio/vídeo
        var f = document.getElementById('yt-frame');
        if(f && f.getAttribute('src')){
          f.setAttribute('src', '');
        }
      }
      btn.addEventListener('click', open);
      closeEls.forEach(function(el){ el.addEventListener('click', close); });
    })();
  </script>
  <script>
    (function(){
      var back = document.getElementById('btn-go-back');
      if(!back) return;
      back.addEventListener('click', function(e){
        e.preventDefault();
        if (window.history && window.history.length > 1) {
          window.history.back();
          return;
        }
        if (document.referrer) {
          window.location.href = document.referrer;
          return;
        }
        window.location.href = '/ProjetoMOD3-limpo/public/filmes.php';
      });
    })();
  </script>
  <?php include __DIR__ . '/partials/rate_modal.php'; ?>
</body>
</html>
