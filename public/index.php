<!doctype html>
<!--
  Página principal (frontend): mostra hero, carrosséis e links para login/signup.
  Este arquivo é apenas a camada de apresentação (HTML/CSS/JS). A autenticação
  é tratada em `auth.php` quando o usuário envia os formulários.
  Observe que os recursos estáticos (img, css, js) ficam em `public/`.
-->




<?php 
// carrega autoload do Composer para que as classes em src/ possam ser encontradas
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Filme;

// fallback = imagem que vai aparecer como substituta caso não haja imagem do filme
$fallback = 'https://i1.sndcdn.com/artworks-FnBdNXsN84HzazMs-ZPSsBw-t500x500.jpg';

// busca todos os filmes (retorna array de objetos Filme)
$filmes = Filme::findAll();

// Mostrar apenas filmes com id entre 4 e 7 (inclusivo).
// Assumo que a entidade Filme tem o método getId(). Se não, a checagem tentará acessar uma propriedade pública id.
$filmes = array_filter($filmes, function($f) {
  $id = null;
  if (is_object($f) && method_exists($f, 'getId')) {
    $id = $f->getId();
  } elseif (is_object($f) && isset($f->id)) {
    $id = $f->id;
  }
  return is_int($id) && $id >= 4 && $id <= 7;
});

// Reindexa o array para índices 0..n-1, facilita o uso em loops.
$filmes = array_values($filmes);

// Observação: não imprimimos imagens aqui para evitar mostrar fallbacks no topo.
// Usaremos a variável $filmes mais abaixo dentro da área do carrossel para renderizar as capas.





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
        <?php
        // Se não houver filmes, exibimos placeholders estáticos (mantém layout)


        //-> verifica se a variável $filmes está "vazia". empty() é true quando $filmes é null, string vazia, 0, false ou um array sem elementos. No seu caso serve para saber se não há filmes carregados.
        if (empty($filmes)) { 

          //-> laço que repete 3 vezes. A sintaxe com ":" e depois endfor; é apenas a forma alternativa à chave { } — aqui você cria 3 "reels" de placeholder.
          for ($r = 0; $r < 3; $r++):

            //ACHO QUE AGORA SÓ FALTA ADICIONAR OS FILMES NO BANCO DE DADOS, E APOS ISSO, A CADA IMG SRC, EU COLOCAR $R + 1, $R + 2, $R + 3, ...., quero testar logo pq é facil, porem é bom comitar isso antes!
    
        ?>
          <div class="reel anim<?= $r + 1 ?>">
            <img src="img/10coisasQueOdeioEmVoce.webp" alt="Placeholder" loading="lazy" />
            <img src="img/f1.webp" alt="Placeholder" loading="lazy" />
            <img src="img/itACoisa.jpg" alt="Placeholder" loading="lazy" />
            <img src="img/interestelar.jpg" alt="Placeholder" loading="lazy" />
            <img src="img/10coisasQueOdeioEmVoce.webp" alt="Placeholder" loading="lazy" />
            <img src="img/f1.webp" alt="Placeholder" loading="lazy" />
            <img src="img/itACoisa.jpg" alt="Placeholder" loading="lazy" />
            <img src="img/interestelar.jpg" alt="Placeholder" loading="lazy" />
          </div>
        <?php
          endfor;
        } else {
          // Distribui os filmes alternadamente entre 3 reels
          $reels = [[], [], []];
          foreach ($filmes as $i => $f) {
            $reels[$i % 3][] = $f;
          }

          // Gera cada reel imprimindo cada lista duas vezes para efeito de loop
          for ($r = 0; $r < 3; $r++):
        ?>
            <div class="reel anim<?= $r + 1 ?>">
              <?php
                $list = $reels[$r];
                // imprime duas vezes para o loop visual
                for ($loop = 0; $loop < 2; $loop++) {
                  foreach ($list as $fil) {
                    // Normaliza e valida o caminho da capa
                    $capaRaw = $fil->getCapa();
                    $titulo = $fil->getTitulo() ?: 'Poster';
                    $src = $fallback; // padrão

                    if (!empty($capaRaw)) {
                      // uniformiza barras
                      $capaRaw = str_replace('\\', '/', $capaRaw);
                      // Remove um possível prefixo "public/" vindo do banco
                      $capaRaw = preg_replace('#^/??public/#i', '', $capaRaw);
                      $isUrl = (bool) preg_match('#^https?://#i', $capaRaw);

                      if ($isUrl) {
                        // URL remota — usa direto
                        $src = $capaRaw;
                      } else {
                        // tenta caminhos comuns relativos à pasta public/
                        $candidates = [
                          __DIR__ . '/' . ltrim($capaRaw, '/'),
                          __DIR__ . '/img/' . ltrim($capaRaw, '/'),
                          __DIR__ . '/uploads/capas/' . ltrim($capaRaw, '/'),
                        ];

                        foreach ($candidates as $cand) {
                          if (file_exists($cand)) {
                            // transforma em src relativo ao public/
                            $rel = str_replace('\\', '/', substr($cand, strlen(__DIR__) + 1));
                            $src = $rel ?: $fallback;
                            break;
                          }
                        }
                      }
                    }

                    echo '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '" loading="lazy" />';
                  }
                }
                // se a lista estiver vazia, mostra placeholders curtos
                if (empty($list)) {
                  for ($k = 0; $k < 4; $k++) {
                    echo '<img src="img/10coisasQueOdeioEmVoce.webp" alt="Placeholder" loading="lazy" />';
                  }
                }
              ?>
            </div>
        <?php
          endfor;
        }
        ?>
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