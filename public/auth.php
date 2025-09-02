<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\User;

/*
 * Bloco: processamento do formulário
 * - Quando a requisição for POST e action=signup: cria novo usuário.
 * - Quando a requisição for POST e action=login: tenta autenticar.
 * Comentários abaixo explicam cada passo de forma simples.
 */

// --- Cadastro (signup)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'signup') {
    // coleta campos do formulário
    $nome = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $sexo = $_POST['sexo'] ?? 'ND'; // 'ND' = prefiro não dizer

    // segurança: força nível padrão como 'user'
    $nivel = 'user';

    // data gerada pelo servidor (horário atual)
    $dataDeCadastro = date('Y-m-d H:i:s');

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // validação mínima: nome, email e confirmação de senha
    if ($nome && $email && $password && $password === $confirm) {
        try {
            // cria hash da senha (demo)
            $hashed = hash('sha256', $password);

            // instancia a entidade e salva no banco
            $u = new User($nome, $sobrenome, $sexo, $nivel, $email, $dataDeCadastro, $hashed);
            $u->save();

            $_SESSION['auth_message'] = 'Conta criada com sucesso (demo).';
            header('Location: auth.php?view=login');
            exit;
        } catch (\Throwable $e) {
            error_log('[auth][signup] ' . $e->getMessage());
            $_SESSION['auth_error'] = 'Erro ao criar conta: ' . $e->getMessage();
        }
    } else {
        $_SESSION['auth_error'] = 'Preencha todos os campos corretamente e confirme a senha.';
    }
}

// --- Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $found = null;
    try {
        // busca todos os usuários e compara (método simples para demo)
        foreach (User::findAll() as $usr) {
            if ($usr->getEmail() === $email && $usr->validatePassword($password)) {
                $found = $usr;
                break;
            }
        }
    } catch (\Throwable $e) {
        error_log('[auth][login] ' . $e->getMessage());
        $_SESSION['auth_error'] = 'Erro durante o login.';
    }

    if ($found) {
        $_SESSION['user_id'] = $found->getId();
        $_SESSION['user_name'] = method_exists($found, 'getNome') ? $found->getNome() : ($found->getName() ?? '');
        $_SESSION['auth_message'] = 'Login realizado (demo).';
        header('Location: index.php');
        exit;
    } else {
        if (!isset($_SESSION['auth_error'])) {
            $_SESSION['auth_error'] = 'Credenciais inválidas.';
        }
    }
}

// mensagens para a interface
$info = $_SESSION['auth_message'] ?? null;
$error = $_SESSION['auth_error'] ?? null;
unset($_SESSION['auth_message'], $_SESSION['auth_error']);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>StarRate — Entrar ou Criar conta</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/auth.css" />
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>
  <header class="site-header">
    <div class="inner container">
      <div class="brand"><a href="index.php" aria-label="Home"><img src="img/LogoStarRate.png" alt="Logo StarRate" style="height:32px; display:block;"/></a></div>
      <nav class="nav"><a href="index.php">Início</a></nav>
    </div>
  </header>

  <main class="auth-wrap">
    <section class="card auth-card" data-view="login" aria-live="polite">
      <div class="segmented" role="tablist" aria-label="Escolha uma opção">
        <button class="seg js-tab active" role="tab" aria-selected="true" aria-controls="panel-login" id="tab-login" data-target="login">Entrar</button>
        <button class="seg js-tab" role="tab" aria-selected="false" aria-controls="panel-signup" id="tab-signup" data-target="signup">Criar conta</button>
      </div>

      <?php if ($info): ?>
        <div class="card" style="margin:12px 0; padding:8px; border-radius:8px; background:#e6ffed; color:#064e2a;"><?= htmlspecialchars($info) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="card" style="margin:12px 0; padding:8px; border-radius:8px; background:#ffecec; color:#641212;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="panel" id="panel-login" role="tabpanel" aria-labelledby="tab-login">
        <h1 class="title">Entrar</h1>
        <p class="subtitle">Acesse sua conta para continuar.</p>
        <form id="form-login" method="post" action="auth.php?action=login" novalidate>
          <div class="field"><label for="lemail">E-mail</label><input id="lemail" name="email" type="email" required placeholder="voce@exemplo.com"></div>
          <div class="field"><label for="lpassword">Senha</label><input id="lpassword" name="password" type="password" required placeholder="••••••••"></div>
          <button class="btn btn-primary" type="submit">Entrar</button>
        </form>
      </div>

      <div class="panel" id="panel-signup" role="tabpanel" aria-labelledby="tab-signup" hidden>
        <h1 class="title">Criar conta</h1>
        <p class="subtitle">Preencha seus dados para começar.</p>
        <form id="form-signup" method="post" action="auth.php?action=signup" novalidate>
          <div class="field"><label for="nome">Nome</label><input id="nome" name="nome" required placeholder="Seu nome"></div>
          <div class="field"><label for="sobrenome">Sobrenome</label><input id="sobrenome" name="sobrenome" placeholder="Seu sobrenome (opcional)"></div>
          <div class="field"><label for="sexo">Sexo</label>
            <select id="sexo" name="sexo">
              <option value="ND">Prefiro não dizer</option>
              <option value="M">Masculino</option>
              <option value="F">Feminino</option>
              <option value="O">Outro</option>
            </select>
          </div>
          <div class="field"><label for="email">E-mail</label><input id="email" name="email" type="email" required placeholder="voce@exemplo.com"></div>
          <div class="field"><label for="password">Senha</label><input id="password" name="password" type="password" minlength="6" required placeholder="Mínimo 6 caracteres"></div>
          <div class="field"><label for="confirm">Confirmar senha</label><input id="confirm" name="confirm" type="password" minlength="6" required placeholder="Repita a senha"></div>
          <button class="btn btn-primary" type="submit">Criar conta</button>
        </form>
      </div>

      <p class="note">As operações aqui são de demonstração. Em produção: valide e proteja melhor.</p>
    </section>
  </main>

  <script>
    (function() {
      const card = document.querySelector('.auth-card');
      const tabs = document.querySelectorAll('.js-tab');
      const panels = {
        login: document.getElementById('panel-login'),
        signup: document.getElementById('panel-signup')
      };

      // controla quais painéis estão visíveis e atualiza a URL sem recarregar
      function setView(view) {
        const v = view === 'signup' ? 'signup' : 'login';
        card.setAttribute('data-view', v);
        panels.login.hidden = v !== 'login';
        panels.signup.hidden = v !== 'signup';
        tabs.forEach(t => {
          const active = t.dataset.target === v;
          t.classList.toggle('active', active);
          t.setAttribute('aria-selected', String(active));
        });
        const url = new URL(window.location);
        url.searchParams.set('view', v);
        history.replaceState({}, '', url);
      }

      const params = new URLSearchParams(location.search);
      setView(params.get('view'));
      tabs.forEach(btn => btn.addEventListener('click', () => setView(btn.dataset.target)));

      // comportamento para debug: se o form não tem action, evita submit real
      function mockSubmit(name) {
        return (ev) => {
          if (!ev.currentTarget.action) {
            ev.preventDefault();
            alert('Somente frontend. Conecte com seu endpoint depois.');
          }
        };
      }

      document.getElementById('form-login')?.addEventListener('submit', mockSubmit('login'));
      document.getElementById('form-signup')?.addEventListener('submit', mockSubmit('signup'));
    })();
  </script>
</body>
</html>
