<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Model\Avaliacao;
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

// Estatísticas do usuário: total de avaliações e média pessoal
$qb = $em->createQueryBuilder();
$qb->select('COUNT(a.id) as total, AVG(a.nota) as media')
   ->from(Avaliacao::class, 'a')
   ->where('a.usuario = :uid')
   ->setParameter('uid', $user->getId());
$stats = $qb->getQuery()->getSingleResult();
$total = (int)($stats['total'] ?? 0);
$media = $total > 0 ? (int) round((float)$stats['media']) : null;

function badgeTier(?int $score): string {
  if ($score === null) return 'score-mid';
  return $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low');
}

?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil | StarRate</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/filmes.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <style>
    .profile { display:grid; gap:16px; }
    .card { border:1px solid hsl(var(--border)); background:hsl(var(--card)); border-radius:12px; padding:16px; }
    .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
    .avatar.big { width:56px; height:56px; display:inline-flex; align-items:center; justify-content:center; border-radius:999px; background:hsl(var(--muted)); color:hsl(var(--muted-foreground)); }
    .stat { display:flex; flex-direction:column; }
    .stat .label { color:hsl(var(--muted-foreground)); font-size:12px; }
    .stat .value { font-weight:700; font-size:20px; }
    .badge { display:inline-flex; align-items:center; gap:6px; border-radius:999px; padding:4px 10px; font-weight:600; font-size:12px; }
    .badge .value { font-size:14px; }
    .badge.score-high { background:rgba(16,185,129,.15); color:#16a34a; }
    .badge.score-mid { background:rgba(234,179,8,.15); color:#b45309; }
    .badge.score-low { background:rgba(239,68,68,.15); color:#b91c1c; }
    .grid2 { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    @media (max-width: 720px){ .grid2 { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>
  <main class="section">
    <div class="container profile">
      <section class="card">
        <div class="row" style="justify-content:space-between; align-items:center;">
          <div class="row" style="align-items:center; gap:12px;">
            <span class="avatar big" aria-hidden="true" style="overflow:hidden;">
              <?php if ($user->getFotoPerfil()): ?>
                <?php
                  $fotoPerfil = $user->getFotoPerfil();
                  $srcPerfil = $fotoPerfil;
                  if (str_starts_with($fotoPerfil, '/ProjetoMOD3-limpo/public/img/fotoPerfil/')) {
                    $fsPerfil = __DIR__ . str_replace('/ProjetoMOD3-limpo/public', '', $fotoPerfil);
                    if (is_file($fsPerfil)) { $srcPerfil .= '?v=' . @filemtime($fsPerfil); }
                  }
                ?>
                <img src="<?= htmlspecialchars($srcPerfil) ?>" alt="Foto de perfil" style="width:100%; height:100%; object-fit:cover;" />
              <?php else: ?>
                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8"/></svg>
              <?php endif; ?>
            </span>
            <div>
              <h1 style="margin:0;"><?= htmlspecialchars($user->getNome()) ?></h1>
              <div style="color:hsl(var(--muted-foreground)); font-size:14px;"><?= htmlspecialchars($user->getEmail()) ?></div>
            </div>
          </div>
          <button class="btn btn-primary" type="button" onclick="openNameModal()">Editar</button>
        </div>
      </section>

      <section class="grid2">
        <div class="card">
          <div class="stat"><span class="label">Desde</span><span class="value"><?= htmlspecialchars($user->getDataCadastroBRComHora()) ?></span></div>
        </div>
        <div class="card">
          <div class="row" style="justify-content:space-between;">
            <div class="stat"><span class="label">Avaliações</span><span class="value"><?= $total ?></span></div>
            <span class="badge <?= badgeTier($media) ?>"><?php if ($media === null): ?>Sem notas<?php else: ?><span>MÉDIA PESSOAL</span> <span class="value"><?= $media ?></span><?php endif; ?></span>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="row" style="justify-content:space-between;">
          <div>
            <h2 style="margin:0 0 4px;">Ações</h2>
            <div style="color:hsl(var(--muted-foreground)); font-size:14px;">Gerencie sua conta</div>
          </div>
          <div class="row">
            <a class="btn btn-ghost" href="/ProjetoMOD3-limpo/public/minhas-avaliacoes.php">Minhas avaliações</a>
            <a class="btn btn-ghost" href="/ProjetoMOD3-limpo/public/logout.php">Sair</a>
          </div>
        </div>
      </section>

    </div>
  </main>
  <!-- Modal para editar perfil (nome e foto) -->
  <div id="edit-name-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-backdrop" data-close></div>
    <div class="modal-content" role="document">
      <button class="modal-close" aria-label="Fechar" data-close>&times;</button>
      <header>
        <h2 style="margin:0;">Editar perfil</h2>
      </header>
      <div style="display:grid; gap:16px; margin-top:8px;">
        <section style="display:grid; gap:12px;">
          <form method="post" action="/ProjetoMOD3-limpo/public/atualizarPerfil.php" style="display:grid; gap:12px;">
            <div>
              <label for="edit-nome" class="label">Nome</label>
              <input id="edit-nome" name="nome" type="text" value="<?= htmlspecialchars($user->getNome()) ?>" required style="width:100%; padding:10px; border:1px solid hsl(var(--border)); border-radius:8px; background:hsl(var(--background));" />
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
              <button class="btn btn-ghost" type="button" data-close>Cancelar</button>
              <button class="btn btn-primary" type="submit">Salvar</button>
            </div>
          </form>
        </section>
        <hr style="border:0; border-top:1px solid hsl(var(--border));" />
        <section style="display:grid; gap:12px;">
          <form method="post" action="/ProjetoMOD3-limpo/public/atualizarFotoPerfil.php" enctype="multipart/form-data" style="display:grid; gap:12px;">
            <div>
              <label class="label" for="foto">Foto de perfil</label>
              <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required />
              <small style="color:hsl(var(--muted-foreground));">Formatos: JPG, PNG, WEBP. Máx 2MB.</small>
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
              <button class="btn btn-primary" type="submit">Enviar</button>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
  <script>
  (function(){
    const modal = document.getElementById('edit-name-modal');
    if (!modal) return;
    const closeEls = modal.querySelectorAll('[data-close]');
    const close = () => { modal.setAttribute('aria-hidden','true'); };
    closeEls.forEach(el => el.addEventListener('click', close));
    window.openNameModal = function(){ modal.setAttribute('aria-hidden','false'); };
  })();
  </script>
</body>
</html>
