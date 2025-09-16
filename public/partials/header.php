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
// Página atual (nome do arquivo) para marcar item ativo
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<header class="site-header">
  <div class="inner container">
    <div class="brand"><a href="/ProjetoMOD3-limpo/public/index.php" aria-label="Home"><img src="/ProjetoMOD3-limpo/public/img/LogoStarRate.png" alt="Logo StarRate"/></a></div>
    <!-- botão hamburger para mobile -->
    <button class="nav-toggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobile-menu" type="button">
      <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
    </button>

    <nav class="nav" aria-label="Menu principal">
      <style>
        /* destaca item ativo igual ao hover (underline) */
        .site-header .nav > a.active { opacity: 1; }
        .site-header .nav > a.active::after { transform: scaleX(1); background: hsl(var(--primary)); }
        /* classe temporária enquanto o item é hoverado (visual igual ao active) */
        .site-header .nav > a.hover-active { opacity: 1; }
        .site-header .nav > a.hover-active::after { transform: scaleX(1); background: hsl(var(--primary)); }
        /* quando outro item está hoverado, o original pode ficar inativo visualmente */
        .site-header .nav > a.inactive { opacity: 0.6; }
        .site-header .nav > a.inactive::after { transform: scaleX(0); }
      </style>
      <a href="/ProjetoMOD3-limpo/public/filmes.php" class="<?= $currentPage === 'filmes.php' ? 'active' : '' ?>">Filmes</a>
      <a href="/ProjetoMOD3-limpo/public/lancamentos.php" class="<?= $currentPage === 'lancamentos.php' ? 'active' : '' ?>">Lançamentos</a>
    </nav>
    
    <!-- mobile menu (visível somente em telas pequenas quando aberto) -->
  <div id="mobile-menu" class="mobile-nav" aria-hidden="true">
      <div class="mobile-links">
        <a href="/ProjetoMOD3-limpo/public/filmes.php">Filmes</a>
        <a href="/ProjetoMOD3-limpo/public/lancamentos.php">Lançamentos</a>
      </div>
    </div>
    <script>
      (function(){
        var nav = document.currentScript && document.currentScript.previousElementSibling;
        if(!nav || !nav.classList.contains('nav')) return;
        var links = Array.from(nav.querySelectorAll('a'));
        var originalActive = links.find(function(a){ return a.classList.contains('active'); });
        links.forEach(function(a){
          a.addEventListener('mouseenter', function(){
            // acender hovered e escurecer o original sem remover a classe server-side
            if (originalActive && originalActive !== a) originalActive.classList.add('inactive');
            a.classList.add('hover-active');
          });
          a.addEventListener('mouseleave', function(){
            a.classList.remove('hover-active');
            if (originalActive && originalActive !== a) originalActive.classList.remove('inactive');
          });
          a.addEventListener('click', function(){
            // marca este como novo original (visual imediato); server-side manterá no reload
            links.forEach(function(x){ x.classList.remove('active'); x.classList.remove('inactive'); });
            a.classList.add('active');
            originalActive = a;
          });
        });
      })();
    </script>

    <div class="nav">
      <?php if (!$loggedIn): ?>
        <a class="btn btn-ghost" href="/ProjetoMOD3-limpo/public/auth.php?view=login">Entrar</a>
        <a class="btn btn-primary" href="/ProjetoMOD3-limpo/public/auth.php?view=signup">Cadastrar</a>
      <?php else: ?>
        <div class="user">
          <button type="button" class="btn btn-ghost user-btn" aria-haspopup="menu" aria-expanded="false" aria-label="Abrir menu do usuário">
            <!-- Avatar (foto de perfil se houver) -->
            <span class="avatar<?= $userRoleNorm === 'admin' ? ' admin' : '' ?>" aria-hidden="true" style="display:inline-flex; align-items:center; justify-content:center; border-radius:999px;">
              <?php
                $foto = $_SESSION['user_foto'] ?? null;
                if (!$foto) {
                  // Tenta buscar do banco uma vez e cachear
                  try {
                    require_once __DIR__ . '/../../vendor/autoload.php';
                    $em = \App\Core\Database::getEntityManager();
                    $u = $em->find(\App\Model\User::class, (int)$_SESSION['user_id']);
                    if ($u && $u->getFotoPerfil()) {
                      $foto = $u->getFotoPerfil();
                      $_SESSION['user_foto'] = $foto;
                    }
                  } catch (\Throwable $e) { /* ignore */ }
                }
              ?>
              <?php if ($foto): ?>
                <?php
                  $src = $foto;
                  if (str_starts_with($foto, '/ProjetoMOD3-limpo/public/img/fotoPerfil/')) {
                    $fs = __DIR__ . str_replace('/ProjetoMOD3-limpo/public', '', $foto);
                    if (is_file($fs)) { $src .= '?v=' . @filemtime($fs); }
                  }
                ?>
                <img src="<?= htmlspecialchars($src) ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" />
              <?php else: ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <circle cx="12" cy="8" r="4"/>
                  <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
                </svg>
              <?php endif; ?>
            </span>
            <span class="user-name"><?= htmlspecialchars($userName) ?></span>
          </button>
          <div class="user-menu" role="menu">
            <a href="/ProjetoMOD3-limpo/public/perfil.php" role="menuitem">Perfil</a>
            <a href="/ProjetoMOD3-limpo/public/minhas-avaliacoes.php" role="menuitem">Minhas avaliações</a>
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
  <style>
    /* responsividade local do header */
    .nav-toggle { display:none; background:transparent; border:0; color:inherit; padding:8px; border-radius:8px; }
  .mobile-nav { display:none; position:fixed; inset:60px 16px auto 16px; background:hsl(var(--card)); border:1px solid hsl(var(--border)); border-radius:12px; padding:12px; box-shadow:var(--shadow-elev); z-index:120; transform-origin: top right; opacity:0; transform: translateY(-6px) scale(0.98); transition: opacity .18s ease, transform .18s ease; }
  .mobile-nav.open, .mobile-nav[aria-hidden="false"] { display:block; opacity:1; transform: translateY(0) scale(1); }
  .mobile-nav a { display:block; padding:8px 10px; margin-bottom:8px; border-radius:8px; color: hsl(var(--foreground)); text-decoration:none; }
  .mobile-nav a:hover { background: hsl(var(--muted)); }
    .mobile-links { margin-bottom:8px; }
    @media (max-width: 880px) {
      .site-header .nav:first-of-type { display:none; }
      .nav-toggle { display:inline-flex; align-items:center; justify-content:center; }
    }
  </style>
    <script>
    (function(){
      var btn = document.querySelector('.nav-toggle');
      var menu = document.getElementById('mobile-menu');
      if(!btn || !menu) return;

      // Atualiza visibilidade via atributo e classe para facilitar CSS
      function setOpen(isOpen){
        menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if(isOpen){
          menu.classList.add('open');
        } else {
          menu.classList.remove('open');
        }
      }

      function toggle(e){
        if(e) e.stopPropagation();
        var isOpen = menu.getAttribute('aria-hidden') === 'false';
        setOpen(!isOpen);
      }

      // Fecha quando clica num link dentro do menu
      menu.addEventListener('click', function(e){
        var a = e.target.closest && e.target.closest('a');
        if(a){ setOpen(false); }
      });

      // Fecha ao clicar fora
      document.addEventListener('click', function(e){
        if(!menu.contains(e.target) && !btn.contains(e.target)){
          setOpen(false);
        }
      });

      // Fecha com ESC
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape' || e.key === 'Esc'){ setOpen(false); }});

      btn.addEventListener('click', toggle);

      // Sincroniza estado inicial (em caso atributos alterados server-side)
      setOpen(menu.getAttribute('aria-hidden') === 'false');
    })();
  </script>
</header>
