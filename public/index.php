<!doctype html>
<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\Filme;
use App\Core\Database;
$fallback = 'img/fallback.png';



$em = Database::getEntityManager();
$filmeRepository = $em->getRepository(Filme::class);

// Carrosseis config
$filmesParaCarrossel1 = $filmeRepository->findBy([],['id' => 'DESC' ], 4);
$primeirosFilmesParaLoop1 = array_slice($filmesParaCarrossel1, 0, min(4, count($filmesParaCarrossel1)));
// unir arrays em sequencia
$filmesComLoop1 = array_merge($filmesParaCarrossel1, $primeirosFilmesParaLoop1);

$filmesParaCarrossel2 = $filmeRepository->findBy([],['id' => 'DESC' ], 8);
$filmesParaCarrossel2 = array_slice($filmesParaCarrossel2, 4, 4);
$primeirosFilmesParaLoop2 = array_slice($filmesParaCarrossel2, 0, min(4, count($filmesParaCarrossel2)));
$filmesComLoop2 = array_merge($filmesParaCarrossel2, $primeirosFilmesParaLoop2);

$filmesParaCarrossel3 = $filmeRepository->findBy([],['id' => 'DESC' ], 12);
$filmesParaCarrossel3 = array_slice($filmesParaCarrossel3, 8, 4);
$primeirosFilmesParaLoop3 = array_slice($filmesParaCarrossel3, 0, min(4, count($filmesParaCarrossel3)));
$filmesComLoop3 = array_merge($filmesParaCarrossel3, $primeirosFilmesParaLoop3);

// Destaques (2025)
$destaques2025 = $filmeRepository->findBy(['anoLancamento' => 2025], ['id' => 'DESC'], 6); ?>



<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>StarRate — Avaliação de Filmes e Séries</title>
  <meta name="description" content="Avalie filmes. Descubra tendências e compartilhe opiniões." />
  <meta name="robots" content="index,follow" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body data-page="landing">



<?php include __DIR__ . '/partials/header.php'; ?>

  <main class="hero">
    <div class="container hero-grid">
      <section>
        <h1>Descubra, avalie e compartilhe sua paixão por cinema</h1>
        <p>Notas de 0 a 100, comentários e tendências em um só lugar. Comece agora!</p>
        <!--pesquisar filme-->
        <form class="search js-search-form" aria-label="Buscar filmes" method="get" action="filmes.php">
          <input name="q" type="search" placeholder="Busque por títulos de filmes" aria-label="Pesquisar filmes" />
          <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
      </section>



      
      <aside aria-label="Carrosséis de pôsteres" class="reels">

        <div class="reel anim1">
          <?php
          foreach ($filmesComLoop1 as $filme): ?>

          <figure class="poster-wrap">
            <?php 
            $id = (int)$filme->getId(); 
            //$url p/linkar o filme
            $url = 'filme.php?id=' . $id; ?>

            <a class="poster-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
              <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" >
            </a>
          </figure>
          <?php
          endforeach; ?>
        </div>

        <div class="reel anim2">
          <?php
          foreach ($filmesComLoop2 as $filme): ?>
            
          <figure class="poster-wrap">
            <?php
            $id = (int)$filme->getId();
            $url = 'filme.php?id=' . $id; ?>

            <a class="poster-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
              <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" >
            </a>
          </figure>
          <?php 
          endforeach; ?>
        </div>

        <div class="reel anim3">
          <?php
          foreach ($filmesComLoop3 as $filme): ?>

          <figure class="poster-wrap">
            <?php
            $id = (int)$filme->getId();
            $url = 'filme.php?id=' . $id; ?>

            <a class="poster-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
              <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" >
            </a>
          </figure>
          <?php
          endforeach; ?>
        </div>
      </aside>
    </div>




    <section class="section">
      <div class="container">
        <h2>Destaques da semana</h2>

      <div class="grid cols-6 highlights">
          <?php
          $renderizados = 0;
          foreach ($destaques2025 as $filme):
              //guarda o caminho do filme em $src
              $src = trim((string)$filme->getCapa());
              $src = $src !== '' ? $src : $fallback;
              $alt = 'Capa do filme ' . $filme->getTitulo();
              $id  = (int)$filme->getId();
              //linkar
              $url = 'filme.php?id=' . $id; ?>

            <figure class="card">
              <a class="movie-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
                <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($alt); ?>" loading="lazy">
                <div class="movie-title"><?php echo htmlspecialchars($filme->getTitulo()); ?></div>
              </a>
            </figure>
          <?php
            $renderizados++;
          endforeach;

          // repetir 6 cards
          for (; $renderizados < 6; $renderizados++): ?>
            <figure class="card">
              <img src="<?php echo htmlspecialchars($fallback); ?>" alt="Capa de destaque" loading="lazy">
            </figure>
          <?php endfor; ?>
        </div>
      </div>
    </section>




    <section class="section" style="background:hsl(var(--card) / 0.3); border-block:1px solid hsl(var(--border));">
  <div class="container grid cols-3">
  <div class="card"><h3 style="margin:0 0 6px;">Avaliações confiáveis</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Atribua notas de 0 a 100 e comentários que ajudam a comunidade a decidir o que assistir.</p></div>
    <div class="card"><h3 style="margin:0 0 6px;">Perfil e conta</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Edite seus dados e atualize sua foto de perfil quando quiser.</p></div>
  <div class="card"><h3 style="margin:0 0 6px;">Busca de filmes</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Encontre títulos pelo nome usando a busca da página.</p></div>
      </div>
    </section>
  </main>


  

  <footer>
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <p style="color:hsl(var(--muted-foreground)); margin:0;">© <span id="year"></span> StarRate — Todos os direitos reservados.</p>
      <nav>
        <a href="#">Termos</a>
        <a href="#">Privacidade</a>
        <a href="#">Contato</a>
      </nav>
    </div>
  </footer>
  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
