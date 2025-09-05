<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$code = (int)($_GET['code'] ?? 500);
$title = 'Ocorreu um erro';
$message = 'Algo deu errado.';
switch ($code) {
  case 403: $title = 'Acesso negado'; $message = 'Você não tem permissão para acessar esta página.'; http_response_code(403); break;
  case 404: $title = 'Página não encontrada'; $message = 'A página que você procura não existe.'; http_response_code(404); break;
  default: http_response_code(500); $code = 500; $title = 'Erro interno'; $message = 'Tente novamente mais tarde.'; break;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($code . ' — ' . $title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="section">
    <div class="container">
      <div class="card" style="padding:24px; text-align:center;">
        <h1 style="margin:0; font-size:32px;"><?= htmlspecialchars($title) ?></h1>
        <p style="color:hsl(var(--muted-foreground)); margin:8px 0 16px;">Código <?= (int)$code ?> — <?= htmlspecialchars($message) ?></p>
        <div style="display:flex; gap:10px; justify-content:center;">
          <a class="btn btn-ghost" href="javascript:history.back()">Voltar</a>
          <a class="btn btn-primary" href="index.php">Ir para a página inicial</a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
