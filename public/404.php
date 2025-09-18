<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
http_response_code(404);

//caso o apache leia a pagina digitada e ela nao seja encontrada, o servidor encaminha para esta pagina 404

?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>404 — Página não encontrada</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <style>
    .error-404 { display:grid; place-items:center; min-height:60vh; gap:18px; }
    .error-404 h1 { font-size:48px; margin:0; }
    .error-404 p { color:hsl(var(--muted-foreground)); max-width:720px; text-align:center; margin:0; }
    @media (max-width:720px){ .error-404 h1{ font-size:32px } }
  </style>
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>
  <main class="section">
    <div class="container">
      <div class="card error-404">
        <h1>404 — Página não encontrada</h1>
        <p>Desculpe — a URL que você está tentando acessar não existe. Verifique se o endereço está correto ou use os links abaixo para continuar navegando.</p>
        <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:center;">
          <a class="btn btn-primary" href="/ProjetoMOD3-limpo/public/index.php">Ir para a página inicial</a>
          <a class="btn" href="/ProjetoMOD3-limpo/public/filmes.php">Ver filmes</a>
          <a class="btn btn-ghost" href="javascript:history.back()">Voltar</a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
