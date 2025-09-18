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
  .profile { display:flex; flex-direction:column; gap:38px; max-width:1104px; margin:28px auto; }
  .card { border:1px solid rgba(255,255,255,0.06); background:hsl(var(--card)); border-radius:14px; padding:24px; box-shadow: 0 8px 22px rgba(11,18,32,0.07); }
    .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
  .avatar.big { width:96px; height:96px; display:inline-flex; align-items:center; justify-content:center; border-radius:999px; background:hsl(var(--muted)); color:hsl(var(--muted-foreground)); overflow:hidden; border:2px solid rgba(255,255,255,0.06); }
    .avatar.big img { width:100%; height:100%; object-fit:cover; display:block; }
  h1 { font-size:26px; margin:0; letter-spacing:0.3px; }
  .email { color:#A0AEC0; font-size:17px; }
    .stat { display:flex; flex-direction:column; }
  .stat .label { color:hsl(var(--muted-foreground)); font-size:16px; }
  .stat .value { font-weight:800; font-size:24px; }
  .badge { display:inline-flex; align-items:center; gap:10px; border-radius:999px; padding:8px 14px; font-weight:700; font-size:16px; }
  .badge .value { font-size:19px; }
    .badge.score-high { background:rgba(16,185,129,.12); color:#16a34a; }
    .badge.score-mid { background:rgba(234,179,8,.12); color:#b45309; }
    .badge.score-low { background:rgba(239,68,68,.12); color:#b91c1c; }
  .grid2 { display:grid; grid-template-columns: 1fr 1fr; gap:19px; }
    @media (max-width: 720px){ .grid2 { grid-template-columns: 1fr; } }
    .actions-right { display:flex; gap:12px; align-items:center; }
  .btn { transition: background-color .18s ease, box-shadow .12s ease; padding:12px 17px; border-radius:12px; font-weight:700; font-size:17px; display:inline-flex; align-items:center; gap:10px; }
  .btn-ghost { border:1px solid rgba(255,255,255,0.06); background:transparent; color:var(--foreground); }
  .btn-ghost:hover { background: rgba(255,255,255,0.02); box-shadow:0 8px 18px rgba(11,18,32,0.05); }
    .btn-primary { background: linear-gradient(180deg, #FBBF24, #F59E0B); color:#0b1220; border:0; box-shadow:0 8px 24px rgba(245,158,11,0.12); }
  .btn-secondary { background: #86A7D9; color:#07203A; border:0; box-shadow:0 12px 34px rgba(134,167,217,0.18); transition:box-shadow .18s ease; }
  .btn-secondary:hover, .btn-secondary:focus { filter:brightness(.96); box-shadow:0 18px 44px rgba(134,167,217,0.22); }
  .btn-ghost.btn-secondary { border-color: rgba(134,167,217,0.22); color:#86A7D9; background:transparent; }
    .btn svg { width:16px; height:16px; vertical-align:middle; }
    .modal .modal-content { max-width:640px; }
    input[type="text"], input[type="file"] { padding:10px 12px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:hsl(var(--background)); color:hsl(var(--foreground)); }
    .profile section.card h2 { margin:0 0 4px; }
    .profile section.card div[style*="Gerencie sua conta"] { line-height:1.5; }
  .profile .btn, .profile .card, .profile .modal-content { transform: none !important; transition: none !important; }
  .profile .btn:hover, .profile .card:hover, .profile .modal-content:focus { transform: none !important; box-shadow: 0 10px 30px rgba(11,18,32,0.06) !important; }
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
              <h1><?= htmlspecialchars($user->getNome()) ?></h1>
              <div class="email"><?= htmlspecialchars($user->getEmail()) ?></div>
            </div>
          </div>
          <div class="actions-right">
            <button class="btn btn-primary" type="button" onclick="openNameModal()">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.41l-2.34-2.34a1.003 1.003 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
              Editar
            </button>
          </div>
        </div>
      </section>

      <section class="grid2">
        <div class="card">
          <div class="stat"><span class="label">Desde</span><span class="value"><?= htmlspecialchars($user->getDataCadastroBRComHora()) ?></span></div>
        </div>
        <div class="card">
          <div class="row" style="justify-content:space-between; align-items:center;">
              <div class="stat"><span class="label">Avaliações</span><span class="value"><?= $total ?></span></div>
              <span class="badge <?= badgeTier($media) ?>">
                <?php if ($media === null): ?>Sem notas<?php else: ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 .587l3.668 7.431L24 9.748l-6 5.847L19.335 24 12 20.201 4.665 24 6 15.595 0 9.748l8.332-1.73L12 .587z"/></svg> <span class="value"><?= $media ?></span><?php endif; ?>
              </span>
            </div>
        </div>
      </section>

      <section class="card">
        <div class="row" style="justify-content:space-between;">
          <div>
            <h2 style="margin:0 0 4px;">Ações</h2>
            <div style="color:hsl(var(--muted-foreground)); font-size:14px;">Gerencie sua conta</div>
          </div>
          <div class="row actions-right">
            <a class="btn btn-primary" href="/ProjetoMOD3-limpo/public/minhas-avaliacoes.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h2v-2H3v2zm0-4h2V7H3v2zm0 8h2v-2H3v2zM7 9h14V7H7v2zm0 4h14v-2H7v2zm0 4h14v-2H7v2z"/></svg>Minhas avaliações</a>
            <a class="btn btn-secondary" href="/ProjetoMOD3-limpo/public/logout.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>Sair</a>
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
        <!-- Formulário único: salva nome e opcionalmente foto se arquivo enviado -->
        <form method="post" action="/ProjetoMOD3-limpo/public/atualizarPerfil.php" enctype="multipart/form-data" style="display:grid; gap:12px;">
          <div>
            <label for="edit-nome" class="label">Nome</label>
            <input id="edit-nome" name="nome" type="text" value="<?= htmlspecialchars($user->getNome()) ?>" required style="width:100%; padding:10px; border:1px solid hsl(var(--border)); border-radius:8px; background:hsl(var(--background));" />
          </div>
          <div>
            <label class="label" for="foto">Foto de perfil (opcional)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" />
            <small style="color:hsl(var(--muted-foreground));">Formatos: JPG, PNG, WEBP. Máx 2MB. Se nenhum arquivo for enviado, só o nome será alterado.</small>
          </div>
          <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:6px;">
            <button class="btn btn-ghost" type="button" data-close>Cancelar</button>
            <button class="btn btn-primary" type="submit">Salvar</button>
          </div>
        </form>
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
