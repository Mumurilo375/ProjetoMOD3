<?php
declare(strict_types=1);

use App\Core\Database;
use App\Model\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Alterna entre as views via ?view=login|signup (default login)
$view = $_GET['view'] ?? 'login';
if (!in_array($view, ['login', 'signup'], true)) { $view = 'login'; }

$errors = [];
$success = $_GET['ok'] ?? null;

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $em = Database::getEntityManager();
        $userRepo = $em->getRepository(User::class);

        if ($action === 'signup') {
            $nome  = trim($_POST['nome']  ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = (string)($_POST['senha'] ?? '');

            if ($nome === '' || $email === '' || $senha === '') {
                $errors[] = 'Preencha nome, e-mail e senha.';
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Informe um e-mail válido.';
            }
            if ($senha !== '' && strlen($senha) < 6) {
                $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
            }

            // Verifica e-mail duplicado
            if (!$errors) {
                $existing = $userRepo->findOneBy(['email' => $email]);
                if ($existing) {
                    $errors[] = 'Já existe um usuário cadastrado com este e-mail.';
                }
            }

            if (!$errors) {
                $user = new User($nome, $email, $senha); // hash dentro do construtor
                $user->save();

                // Autologin simples
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_nome'] = $user->getNome();
                  $_SESSION['user_role'] = strtolower($user->getNivelAcesso());
                header('Location: index.php');
                exit;
            }

            $view = 'signup';
        }

        if ($action === 'login') {
            $email = trim($_POST['email'] ?? '');
            $senha = (string)($_POST['senha'] ?? '');

            if ($email === '' || $senha === '') {
                $errors[] = 'Informe e-mail e senha.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Informe um e-mail válido.';
            } else {
                $user = $userRepo->findOneBy(['email' => $email]);
                if (!$user || !$user->verificaSenha($senha)) {
                    $errors[] = 'E-mail ou senha inválidos.';
                } else {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_nome'] = $user->getNome();
                      $_SESSION['user_role'] = strtolower($user->getNivelAcesso());
                    header('Location: index.php');
                    exit;
                }
            }

            $view = 'login';
        }
    } catch (Throwable $e) {
        $errors[] = 'Erro ao processar a solicitação.';
    }
}

?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Entrar | StarRate</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/auth.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>

  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="auth-wrap">
    <div class="card auth-card">
      <h1 class="title">Bem-vindo</h1>
      <p class="subtitle">Acesse sua conta ou crie uma nova para avaliar filmes.</p>

      <div class="segmented" role="tablist">
        <a class="seg<?= $view === 'login' ? ' active' : '' ?>" href="?view=login" role="tab" aria-selected="<?= $view === 'login' ? 'true' : 'false' ?>">Entrar</a>
        <a class="seg<?= $view === 'signup' ? ' active' : '' ?>" href="?view=signup" role="tab" aria-selected="<?= $view === 'signup' ? 'true' : 'false' ?>">Criar conta</a>
      </div>

      <?php if ($errors): ?>
        <div class="card" style="background:hsl(var(--card)); margin:0 0 12px; border:1px solid hsl(0 72% 50% / .35);">
          <ul style="margin:8px 12px; padding-left: 18px; color:hsl(0 72% 70%);">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($view === 'signup'): ?>
        <form method="post" autocomplete="off">
          <input type="hidden" name="action" value="signup" />
          <div class="field">
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" required />
          </div>
          <div class="field">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required />
          </div>
          <div class="field">
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" minlength="6" required />
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end; margin-top: 8px;">
            <a class="btn btn-ghost" href="?view=login">Já tenho conta</a>
            <button class="btn btn-primary" type="submit">Criar conta</button>
          </div>
          <p class="note">Sua senha será protegida com hash forte (password_hash).</p>
        </form>
      <?php else: ?>
        <form method="post" autocomplete="off">
          <input type="hidden" name="action" value="login" />
          <div class="field">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required />
          </div>
          <div class="field">
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" required />
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end; margin-top: 8px;">
            <a class="btn btn-ghost" href="?view=signup">Criar conta</a>
            <button class="btn btn-primary" type="submit">Entrar</button>
          </div>
          <p class="note">Usamos verificação segura de senha (password_verify).</p>
        </form>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
