<?php declare(strict_types=1); use App\Core\Database; use App\Model\User;
require_once __DIR__ . '/../vendor/autoload.php';

session_start();


// Estrutura MVC (Controler)
// Alterna entre as views via ?view=login|signup (default login)
$view = $_GET['view'] ?? 'login';
if (!in_array($view, ['login', 'signup'], true)) { $view = 'login'; }

$errors = [];
// Verifica se há uma mensagem de sucesso na URL (ex: ?ok=1).
$success = $_GET['ok'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega a ação do formulário ('login' ou 'signup').
    $action = $_POST['action'] ?? '';

    try {
        $em = Database::getEntityManager();
        // Obtém o repositório da entidade User para fazer buscas no banco.
        $userRepo = $em->getRepository(User::class);

        if ($action === 'signup') {
            // Pega e limpa os dados enviados pelo formulário de cadastro.
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

            // Se não houver erros de validação até agora, verifica se o e-mail já existe.
            if (!$errors) {
                // Procura por um usuário com o mesmo e-mail no banco.
                $existing = $userRepo->findOneBy(['email' => $email]);
                // Se encontrar, adiciona um erro.
                if ($existing) {
                    $errors[] = 'Já existe um usuário cadastrado com este e-mail.';
                }
            }

            // Se, após todas as verificações, não houver erros.
            if (!$errors) {
                // Cria um novo objeto User (a senha é criptografada no construtor).
                $user = new User($nome, $email, $senha);
                $user->save();

                // Inicia a sessão para o novo usuário (autologin).
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_nome'] = $user->getNome();
                $_SESSION['user_role'] = strtolower($user->getNivelAcesso());
                // Redireciona o usuário para a página inicial.
                header('Location: index.php');
                exit;
            }

            // Se houveram erros, garante que a view de signup seja exibida novamente.
            $view = 'signup';
        }



        // Se a ação for 'login' (entrar na conta).
        if ($action === 'login') {
            // Pega e limpa os dados enviados pelo formulário de login.
            $email = trim($_POST['email'] ?? '');
            $senha = (string)($_POST['senha'] ?? '');

            if ($email === '' || $senha === '') {
                $errors[] = 'Informe e-mail e senha.';

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Informe um e-mail válido.';
            } else {
                // Busca o usuário pelo e-mail no banco de dados.
                $user = $userRepo->findOneBy(['email' => $email]);
                // Se o usuário não for encontrado OU a senha estiver incorreta...
                if (!$user || !$user->verificaSenha($senha)) {
                    $errors[] = 'E-mail ou senha inválidos.';
                } else {
                    // Se o login for bem-sucedido, armazena os dados do usuário na sessão.
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_nome'] = $user->getNome();
                    $_SESSION['user_role'] = strtolower($user->getNivelAcesso());
                    header('Location: index.php');
                    exit;
                }
            }

            // Se houver erros, garante que a view de login seja exibida novamente (mvc).
            $view = 'login';
        }
    // Captura qualquer erro geral que possa ocorrer durante o processo.
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

      <!-- Abas para alternar entre login e cadastro -->
      <div class="segmented" role="tablist">
        <a class="seg<?= $view === 'login' ? ' active' : '' ?>" href="?view=login" role="tab" aria-selected="<?= $view === 'login' ? 'true' : 'false' ?>">Entrar</a>
        <a class="seg<?= $view === 'signup' ? ' active' : '' ?>" href="?view=signup" role="tab" aria-selected="<?= $view === 'signup' ? 'true' : 'false' ?>">Criar conta</a>
      </div>

      <?php if ($errors): // printa o erro ?>
        <div class="card" style="background:hsl(var(--card)); margin:0 0 12px; border:1px solid hsl(0 72% 50% / .35);">
          <ul style="margin:8px 12px; padding-left: 18px; color:hsl(0 72% 70%);">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($view === 'signup'): ?>
        <form method="post" autocomplete="off" novalidate>
          <input type="hidden" name="action" value="signup" />
          <div class="field">
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" required />
            <div class="field-error" aria-live="polite"></div>
          </div>
          <div class="field">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required />
            <div class="field-error" aria-live="polite"></div>
          </div>
          <div class="field">
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" minlength="6" required />
            <div class="field-error" aria-live="polite"></div>
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end; margin-top: 8px;">
            <a class="btn btn-ghost" href="?view=login">Já tenho conta</a>
            <button class="btn btn-primary" type="submit">Criar conta</button>
          </div>
        </form>

      <?php else: ?>
    <form method="post" autocomplete="off" novalidate>
          <input type="hidden" name="action" value="login" />
          <div class="field">
            <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required />
      <div class="field-error" aria-live="polite"></div>
          </div>
          <div class="field">
            <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" required />
      <div class="field-error" aria-live="polite"></div>
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end; margin-top: 8px;">
            <a class="btn btn-ghost" href="?view=signup">Criar conta</a>
            <button class="btn btn-primary" type="submit">Entrar</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </main>




  <script>
  // Script para mensagens de validação customizadas (substitui tooltips nativos)
  (function(){
    function setError(field, message){
      const container = field.closest('.field');
      if (!container) return;
      container.classList.add('error');
      const errEl = container.querySelector('.field-error');
      if (errEl) errEl.textContent = message || '';
    }
    function clearError(field){
      const container = field.closest('.field');
      if (!container) return;
      container.classList.remove('error');
      const errEl = container.querySelector('.field-error');
      if (errEl) errEl.textContent = '';
    }

    function validateField(field){
      clearError(field);
      if (!field.checkValidity()){
        if (field.validity.valueMissing) return 'Campo obrigatório.';
        if (field.validity.typeMismatch) return field.type === 'email' ? 'Informe um e-mail válido.' : 'Valor inválido.';
        if (field.validity.tooShort) return 'Valor muito curto.';
        return 'Valor inválido.';
      }
      return '';
    }

    document.querySelectorAll('.auth-card form').forEach(form => {
      //  Verifica todos os campos quando o usuário clica em "Entrar" ou "Criar conta".
      form.addEventListener('submit', function(e){
        let hasError = false;
        const fields = form.querySelectorAll('input[required], input[minlength]');
        fields.forEach(f => {
          const msg = validateField(f);
          if (msg){ setError(f, msg); hasError = true; }
        });
        //  Se encontrar algum erro, impede o envio do formulário para o servidor.
        if (hasError){ e.preventDefault(); return false; }
        return true;
      });

      //  Valida o campo em tempo real, assim que o usuário termina de digitar.
      form.querySelectorAll('input').forEach(inp => {
        inp.addEventListener('input', () => { clearError(inp); });
        inp.addEventListener('blur', () => {
          const msg = validateField(inp);
          if (msg) setError(inp, msg);
        });
      });
    });
  })();
  </script>

</body>
</html>
