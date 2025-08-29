<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AvaliaFlix — Avaliação de Filmes e Séries</title>
  <meta name="description" content="Avalie filmes, séries e celebridades. Descubra tendências e compartilhe opiniões." />
  <link rel="canonical" href="/static/index.html" />
  <meta name="robots" content="index,follow" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body data-page="landing">
  <header class="site-header">
    <div class="inner container">
      <div class="brand"><a href="/static/index.html" aria-label="AvaliaFlix, recarregar">AvaliaFlix</a></div>
      <nav class="nav">
        <a href="/static/movies.html">Filmes</a>
        <a href="/static/series.html">Séries</a>
        <a href="/static/celebrities.html">Celebridades</a>
      </nav>
      <div class="nav">
        <a class="btn btn-ghost" href="#">Login</a>
        <a class="btn btn-primary" href="#">Sign up</a>
      </div>
    </div>
  </header>

  <main class="hero">
    <div class="container hero-grid">
      <section>
        <h1>Descubra, avalie e compartilhe sua paixão por cinema e séries</h1>
        <p>Notas em estrelas, comentários e tendências em um só lugar. Comece agora.</p>
        <form class="search js-search-form" aria-label="Buscar títulos">
          <select name="c" aria-label="Categoria">
            <option value="todos">Todos</option>
            <option value="filmes">Filmes</option>
            <option value="series">Séries</option>
            <option value="celebridades">Celebridades</option>
          </select>
          <input name="q" type="search" placeholder="Busque por títulos, séries ou celebridades" aria-label="Pesquisar" />
          <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
      </section>
      <aside aria-label="Carrosséis de pôsteres" class="reels">
        <div class="reel anim1">
          <img src="/static/posters/poster1.jpg" alt="Pôster em destaque 1" loading="lazy" />
          <img src="/static/posters/poster2.jpg" alt="Pôster em destaque 2" loading="lazy" />
          <img src="/static/posters/poster3.jpg" alt="Pôster em destaque 3" loading="lazy" />
          <img src="/static/posters/poster4.jpg" alt="Pôster em destaque 4" loading="lazy" />
          <img src="/static/posters/poster1.jpg" alt="Pôster em destaque 1 (loop)" loading="lazy" />
          <img src="/static/posters/poster2.jpg" alt="Pôster em destaque 2 (loop)" loading="lazy" />
        </div>
        <div class="reel anim2">
          <img src="/static/posters/poster5.jpg" alt="Pôster em destaque 5" loading="lazy" />
          <img src="/static/posters/poster6.jpg" alt="Pôster em destaque 6" loading="lazy" />
          <img src="/static/posters/poster7.jpg" alt="Pôster em destaque 7" loading="lazy" />
          <img src="/static/posters/poster8.jpg" alt="Pôster em destaque 8" loading="lazy" />
          <img src="/static/posters/poster5.jpg" alt="Pôster em destaque 5 (loop)" loading="lazy" />
          <img src="/static/posters/poster6.jpg" alt="Pôster em destaque 6 (loop)" loading="lazy" />
        </div>
        <div class="reel anim3">
          <img src="/static/posters/poster9.jpg" alt="Pôster em destaque 9" loading="lazy" />
          <img src="/static/posters/poster10.jpg" alt="Pôster em destaque 10" loading="lazy" />
          <img src="/static/posters/poster11.jpg" alt="Pôster em destaque 11" loading="lazy" />
          <img src="/static/posters/poster12.jpg" alt="Pôster em destaque 12" loading="lazy" />
          <img src="/static/posters/poster9.jpg" alt="Pôster em destaque 9 (loop)" loading="lazy" />
          <img src="/static/posters/poster10.jpg" alt="Pôster em destaque 10 (loop)" loading="lazy" />
        </div>
      </aside>
    </div>

    <section class="section">
      <div class="container">
        <h2>Destaques da semana</h2>
        <div class="grid cols-6">
          <figure class="card"><img src="/static/posters/poster1.jpg" alt="Destaque #1" loading="lazy"><figcaption style="margin-top:8px; color:hsl(var(--muted-foreground)); text-align:center;">Em alta #1</figcaption></figure>
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
        <div class="card"><h3 style="margin:0 0 6px;">Exploração inteligente</h3><p style="margin:0; color:hsl(var(--muted-foreground));">Use filtros por filmes, séries e celebridades para encontrar o que importa pra você.</p></div>
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