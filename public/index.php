<!doctype html>




<?php 


require_once $_SERVER['DOCUMENT_ROOT'] . '/ProjetoMOD3-limpo/bootstrap.php';
use App\Model\Filme;

// fallback = imagem que vai aparecer como substituta caso não haja imagem do filme
$fallback = 'https://i1.sndcdn.com/artworks-FnBdNXsN84HzazMs-ZPSsBw-t500x500.jpg';

// busca todos os filmes (retorna array de objetos Filme)
$filmeRepository = $entityManager->getRepository(Filme::class);

//buscando os ultimos 4 filmes adicionados no banco, para o carrossel
//(ordenando o id de forma decrescente)
$filmesParaCarrossel = $filmeRepository->findBy([],['id' => 'DESC' ], 4);

//agora a variavel $filmesParaCarrossel contém um array com 4 filmes

?>




<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>StarRate — Avaliação de Filmes e Séries</title>
  <meta name="description" content="Avalie filmes. Descubra tendências e compartilhe opiniões." />
  <link rel="canonical" href="/static/index.html" />
  <meta name="robots" content="index,follow" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>



<body data-page="landing">



  




<header class="site-header">
    <div class="inner container">
  <div class="brand"><a href="/static/index.html" aria-label="Home"><img src="img/LogoStarRate.png" alt="Logo StarRate"/></a></div>
      
      <nav class="nav">
        <a href="/static/movies.html">Filmes</a>
        <a href="/static/series.html">Lançamentos</a>
      </nav>
      <div class="nav">
        <a class="btn btn-ghost" href="auth.php">Login</a>
        <a class="btn btn-primary" href="auth.php?view=signup">Sign up</a>
      </div>
    </div>
  </header>














  <main class="hero">
    <div class="container hero-grid">
      <section>
        <h1>Descubra, avalie e compartilhe sua paixão por cinema e séries</h1>
        <p>Notas em estrelas, comentários e tendências em um só lugar. Comece agora.</p>
        <form class="search js-search-form" aria-label="Buscar filmes">
          <input name="q" type="search" placeholder="Busque por títulos de filmes" aria-label="Pesquisar filmes" />
          <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
      </section>






      <aside aria-label="Carrosséis de pôsteres" class="reels">
        <div class="reel anim1">
          <?php

          //loop atraves dos objetos Filme que buscamos no banco pelo 'find by'
          foreach ($filmesParaCarrossel as $filme):
          ?>

          <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" loading="lazy">

          <?php 
          endforeach;
          ?>

        </div>
              
          
      </aside>
    </div>

    <section class="section">
      <div class="container">
        <h2>Destaques da semana</h2>
        <div class="grid cols-6">
          <figure class="card"><img src="" alt="Destaque #1" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #1</figcaption></figure>
          <figure class="card"><img src="/static/posters/poster2.jpg" alt="Destaque #2" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #2</figcaption></figure>
          <figure class="card"><img src="/static/posters/poster3.jpg" alt="Destaque #3" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #3</figcaption></figure>
          <figure class="card"><img src="/static/posters/poster4.jpg" alt="Destaque #4" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #4</figcaption></figure>
          <figure class="card"><img src="/static/posters/poster5.jpg" alt="Destaque #5" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #5</figcaption></figure>
          <figure class="card"><img src="/static/posters/poster6.jpg" alt="Destaque #6" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #6</figcaption></figure>
        </div>
      </div>
    </section>

    <section class="section" style="background:hsl(var(--card) / 0.3); border-block:1px solid hsl(var(--border));">
      <div class="container grid cols-3">
        <div class="card"><h3 style="margin:0 0 6px;">Avaliações confiáveis</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Atribua notas em estrelas e comentários que ajudam a comunidade a decidir o que assistir.</p></div>
        <div class="card"><h3 style="margin:0 0 6px;">Listas e favoritos</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Organize sua experiência com listas personalizadas e favoritos.</p></div>
  <div class="card"><h3 style="margin:0 0 6px;">Exploração inteligente</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Use a busca para encontrar filmes que importam pra você.</p></div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <p style="color:hsl(var(--muted-foreground)); margin:0;">© <span id="year"></span> AvaliaFlix — Todos os direitos reservados.</p>
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
  <script defer src="/static/js/app.js"></script>
</body>
</html>