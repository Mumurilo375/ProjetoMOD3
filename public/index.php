<!doctype html>
<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\Filme;
use App\Core\Database;




// fallback = imagem que vai aparecer como substituta caso o filme não seja encontrado
$fallback = 'https://lh3.googleusercontent.com/rd-gg-dl/AJfQ9KTq2mXwFdVHSumEhgQzq3lTqniYsPFYppib_c21rsJtGx7jIM8briKOFu5t4Rq309qK9dWdCooXb2CnzTLBiMF633cP1dQH1S9k0BMihZAC6e1zxIHemItK2WEV-fgv6lFSrl-q-x_YAqFXW8D9sQ1YgxQuhX4ujzIcnYajveMi9GhJZd2J7u5m2z7xUR_CWGbZs5w-Z6eAeuZo6Ptg-hQYgDW3wrvRb4IPPp1e8jUjzHIybcn_hbKq4WBDFa8u5ghHWbVqd1eotsa3BnxFaoTxQXU5SOql_te-KJeb8kgHxZuVRs7wzwvc3lzq7EjvUHcMe4N61x1lMnwFlzGhjfX2mD0zuagSEarc1xF3BwrL7zani-1x_0pYBJmh94TWfIjEHBBbl_OH_EwmBIcEgqilsjSgUux3qp7pZiwelqQ8mWOYML_Q3CV4fjabiA6a_-S9PApQCXnnGuYQEf758hakAvJa_fi5QBL5vdnQvscuSa4ccXLnSaHGrin2jLlQbDESQLVZnCXKgtzLGH2vBRJrvVgKbANYLo82-MmpiJP164fc6eXfilWmG6wxRsoXtVoyCsGLJhIDi0mS8RQOW-_Qp517uL0DxqxL63FCHJjae85OoJpxJRkgjvaWJUdoZPasN-6eEt-qeKa-77Q4T_5W3S9v5cL-yzw_FKN1GM1wLMMoDyYOfr97_38BV4S2YNNtN9G5BkwjMi1YfXgwLF8Zi6tBkWLspTROhEona-J-693r-KgGHgGXje3BzvzT0IAOsReyrTDH27twOMyZyydtW0IMm5FsxrtKxEH67unsVeBEWCFRwZUW5gwODbs_EJDKHS90HDJDVbOcKzKEyCvnAmYGJNxvF8CzN8TxsuMP5UCaUQRcJ2DsjjvijindTDziE4WGarQvlr4m2FYqeWvu3E8kVljIqHE7HXuEIRXSKb9nbEwyeo-kz2da6EJhrYFfqK-RYulIjtXWbZEmFV6eVC9coSmpICZymPznd9_MQz6bLNA66ZLoFe3gdYojss3rtND-LJl9K8wWUady55ep1EGdkQECCfj2HgIqpsEaX-uPAScnJH2mUQqehhB1kfKCGQoLpKBQadj7Kg7fqdQnhfeFmACn7ipmwxn7gwfA1_G9kiAALnynZgYWIl6zLoOPYlgxrfPjRk9gzJRVReBRrAriM4JIkZfB5cRMfK6xM4GDbYVg7pTIm-jH87_CrCO4QR7U0CEhcbryO8EO19kd0Oxr3A=s1024';




// busca todos os filmes (retorna array de objetos Filme)
$em = Database::getEntityManager();
$filmeRepository = $em->getRepository(Filme::class);




// Carrossel 1: filmes 1-4

// filtrando os filmes mais recentes pelo ID decrescente, e colocando um limit de 4 itens e adicionando estes filmes no array '$filmesParaCarrossel1'
$filmesParaCarrossel1 = $filmeRepository->findBy([],['id' => 'DESC' ], 4);

// este novo array '$primeirosFilmesParaLoop1' serve apenas para copiar os filmes do array de cima, para facilitar em fazer o efeito de loop no carrossel.
//array_slice é para copiar o array, começando do indice 0 e terminando em 4 itens (ou o valor máximo que tiver, se for menos de 4)
$primeirosFilmesParaLoop1 = array_slice($filmesParaCarrossel1, 0, min(4, count($filmesParaCarrossel1)));

// o array_merge ele une os dois arrays em sequencia, para fazer o efeito de loop infinito no carrossel
$filmesComLoop1 = array_merge($filmesParaCarrossel1, $primeirosFilmesParaLoop1);




// Carrossel 2:  (mesmo esquema do carrossel 1, mas pegando os filmes do 5 ao 8)

$filmesParaCarrossel2 = $filmeRepository->findBy([],['id' => 'DESC' ], 8);
$filmesParaCarrossel2 = array_slice($filmesParaCarrossel2, 4, 4);
$primeirosFilmesParaLoop2 = array_slice($filmesParaCarrossel2, 0, min(4, count($filmesParaCarrossel2)));
$filmesComLoop2 = array_merge($filmesParaCarrossel2, $primeirosFilmesParaLoop2);




// Carrossel 3:  (mesmo esquema do carrossel 1 e 2, mas pegando os filmes do 9 ao 12)

$filmesParaCarrossel3 = $filmeRepository->findBy([],['id' => 'DESC' ], 12);
$filmesParaCarrossel3 = array_slice($filmesParaCarrossel3, 8, 4);
$primeirosFilmesParaLoop3 = array_slice($filmesParaCarrossel3, 0, min(4, count($filmesParaCarrossel3)));
$filmesComLoop3 = array_merge($filmesParaCarrossel3, $primeirosFilmesParaLoop3);




// Destaques (somente ano 2025)

// Aqui guardamos no array '$destaques2025' apenas os 6 ultimos filmes (adicionados no site) filtrados por ano de lançamento == 2025
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



<?php
// puxando o header pronto
include __DIR__ . '/partials/header.php'; ?>




  <main class="hero">
    <div class="container hero-grid">
      <section>
        <h1>Descubra, avalie e compartilhe sua paixão por cinema</h1>
  <p>Notas de 0 a 100, comentários e tendências em um só lugar. Comece agora!</p>
        <!--      formulário de pesquisa de filme        -->
        <form class="search js-search-form" aria-label="Buscar filmes" method="get" action="filmes.php">
          <!--      joga o resultado para 'filmes.php'        -->
                
          <!--      Campo onde o user digita o filme      -->
          <input name="q" type="search" placeholder="Busque por títulos de filmes" aria-label="Pesquisar filmes" />
          <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
      </section>




      <!--   Carrosséis de pôsteres  -->
      <aside aria-label="Carrosséis de pôsteres" class="reels">
        <div class="reel anim1">

        <!--  Carrossel 1  -->
          <?php
          //loop atraves 4 primeiros filmes que buscamos no banco pelo 'find by'
          foreach ($filmesComLoop1 as $filme): ?>

          <figure class="poster-wrap">

            <?php 
            $id = (int)$filme->getId(); 
            //cria a variavel url para cada filme baseado no ID (para caso clique no poster e redirecione para a pagina do filme especifico)
            $url = 'filme.php?id=' . $id; ?>

            <a class="poster-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
              <!--  aqui puxamos a string da capa do filme e colocamos no src da tag img de html pelo GetCapa
              e para o alt definimos o nome do filme com um getTitulo    -->
              <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" >
            </a>
          </figure>


          <?php 
          endforeach;
          ?>

        </div>
        <div class="reel anim2">
          <?php

          //loop atraves dos objetos Filme que buscamos no banco pelo 'find by'
          foreach ($filmesComLoop2 as $filme):
          ?>

          <figure class="poster-wrap">
            <?php $id = (int)$filme->getId(); $url = 'filme.php?id=' . $id; ?>
            <a class="poster-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
              <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" >
            </a>
          </figure>


          <?php 
          endforeach;
          ?>

        </div>
        <div class="reel anim3">
          <?php

          //loop atraves dos objetos Filme que buscamos no banco pelo 'find by'
          foreach ($filmesComLoop3 as $filme):
          ?>

          <figure class="poster-wrap">
            <?php $id = (int)$filme->getId(); $url = 'filme.php?id=' . $id; ?>
            <a class="poster-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
              <img src="<?php echo htmlspecialchars($filme->getCapa()); ?>" alt="<?php echo htmlspecialchars($filme->getTitulo()); ?>" >
            </a>
          </figure>


          <?php
          endforeach;
          ?>

        </div>
      </aside>

    </div>






    <section class="section">
      <div class="container">
        <h2>Destaques da semana</h2>
  <div class="grid cols-6 highlights">
          <?php
          // Renderizamos até 6 cards. Se vier menos do banco, completamos com fallback.
          // Segurança: sempre use htmlspecialchars em dados vindos do banco para evitar XSS.
          $renderizados = 0;
          foreach ($destaques2025 as $filme):
              // Obtém a capa do filme; se não houver, usa a imagem fallback definida no topo do arquivo
              $src = trim((string)$filme->getCapa());
              $src = $src !== '' ? $src : $fallback;
              // Alt acessível: descreve minimamente o conteúdo
              $alt = 'Capa do filme ' . $filme->getTitulo();
              $id  = (int)$filme->getId();
              $url = 'filme.php?id=' . $id;
          ?>
            <figure class="card">
              <a class="movie-link" href="<?php echo htmlspecialchars($url); ?>" aria-label="Abrir <?php echo htmlspecialchars($filme->getTitulo()); ?>">
                <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($alt); ?>" loading="lazy">
                <div class="movie-title"><?php echo htmlspecialchars($filme->getTitulo()); ?></div>
              </a>
            </figure>
          <?php
            $renderizados++;
          endforeach;

          // Completa até 6 itens mantendo o layout uniforme
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
</body>
</html>