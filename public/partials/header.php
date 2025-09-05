<?php
// Header compartilhado: inicia sessão se necessário e renderiza menu conforme login
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? ($_SESSION['user_nome'] ?? 'Usuário') : null;

// Garante que tenhamos a role na sessão; se faltar, tenta buscar do banco (fallback)
$userRole = 'user';
if ($loggedIn) {
  $userRole = $_SESSION['user_role'] ?? 'user';
  if (!isset($_SESSION['user_role'])) {
    // Busca do banco apenas neste caso para evitar custo constante
    try {
      require_once __DIR__ . '/../../vendor/autoload.php';
      $emClass = \App\Core\Database::class;
      if (class_exists($emClass)) {
        $em = \App\Core\Database::getEntityManager();
        $user = $em->find(\App\Model\User::class, (int)$_SESSION['user_id']);
        if ($user) {
          $userRole = $user->getNivelAcesso();
          $_SESSION['user_role'] = $userRole; // cache em sessão
          // Atualiza nome se mudou
          $_SESSION['user_nome'] = $_SESSION['user_nome'] ?? $user->getNome();
        }
      }
    } catch (\Throwable $e) {
      // Silencia fallback; mantém 'user'
    }
  }
}

// Normaliza para comparação case-insensitive
$userRoleNorm = strtolower($userRole);
?>
<header class="site-header">
  <div class="inner container">
    <div class="brand"><a href="/ProjetoMOD3-limpo/public/index.php" aria-label="Home"><img src="/ProjetoMOD3-limpo/public/img/LogoStarRate.png" alt="Logo StarRate"/></a></div>

    <nav class="nav">
  <a href="/ProjetoMOD3-limpo/public/filmes.php">Filmes</a>
  <a href="/ProjetoMOD3-limpo/public/lancamentos.php">Lançamentos</a>
    </nav>

    <div class="nav">
      <?php if (!$loggedIn): ?>
        <a class="btn btn-ghost" href="/ProjetoMOD3-limpo/public/auth.php?view=login">Login</a>
        <a class="btn btn-primary" href="/ProjetoMOD3-limpo/public/auth.php?view=signup">Sign up</a>
      <?php else: ?>
        <div class="user">
          <button type="button" class="btn btn-ghost user-btn" aria-haspopup="menu" aria-expanded="false" aria-label="Abrir menu do usuário">
            <!-- Avatar com anel especial para admin -->
            <span class="avatar<?= $userRoleNorm === 'admin' ? ' admin' : '' ?>" aria-hidden="true">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
              </svg>
            </span>
            <span class="user-name"><?= htmlspecialchars($userName) ?></span>
          </button>
          <div class="user-menu" role="menu">
            <a href="/ProjetoMOD3-limpo/public/perfil.php" role="menuitem">Perfil</a>
            <a href="/ProjetoMOD3-limpo/public/minhas-avaliacoes.php" role="menuitem">Minhas avaliações</a>
            <a href="#" role="menuitem">Coleções</a>
            <?php if ($userRoleNorm === 'admin'): ?>
              <a href="/ProjetoMOD3-limpo/public/adicionarFilme.php" role="menuitem">Adicionar</a>
            <?php endif; ?>
            <hr style="border:0; border-top:1px solid hsl(var(--border)); margin:6px 0;">
            <a href="/ProjetoMOD3-limpo/public/logout.php" role="menuitem">Sair</a>
          </div>
        </div>
        <script>
          // Toggle simples do menu do usuário
          (function(){
            var root = document.currentScript && document.currentScript.previousElementSibling;
            root = root && root.classList.contains('user') ? root : document.querySelector('.user');
            if(!root) return;
            var btn = root.querySelector('.user-btn');
            var menu = root.querySelector('.user-menu');
            function outside(e){ if(!root.contains(e.target)){ root.classList.remove('open'); document.removeEventListener('click', outside);} }
            btn && btn.addEventListener('click', function(e){ e.stopPropagation(); root.classList.toggle('open'); if(root.classList.contains('open')){ document.addEventListener('click', outside);} });
          })();
        </script>
      <?php endif; ?>
    </div>
  </div>
</header>
