<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
// Bloqueia acesso se não estiver logado ou não for admin
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?view=login&err=auth');
    exit;
}

// Em ambientes simples vamos consultar o banco para garantir papel atualizado
require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Database;
use App\Model\User;
try {
    $em = Database::getEntityManager();
    $user = $em->find(User::class, (int)$_SESSION['user_id']);
    if (!$user || !$user->isAdmin()) {
        header('Location: error.php?code=403');
        exit;
    }
} catch (Throwable $e) {
    header('Location: error.php?code=403');
    exit;
}
// Página de testes para criar filmes e listar (exemplo de POST/Redirect/GET)
// PRG: evita reenvio de formulário quando o usuário recarrega a página.

use App\Model\Filme;

$caminhoDaCapa = '';
$_trailer = null;



// Se recebeu POST, cria a entidade e salva no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- NOVA LÓGICA DE UPLOAD DA IMAGEM ---

    // 1. Verifica se o campo 'film_capa' foi enviado e se não houve erro no upload.
    if (isset($_FILES['film_capa']) && $_FILES['film_capa']['error'] === UPLOAD_ERR_OK) {

        // 2. Define o diretório de destino. Crie esta pasta no seu projeto!
        $diretorioUpload = __DIR__ . '/../public/img/capas/';

        // 3. Pega o nome temporário do arquivo no servidor.
        $arquivoTemporario = $_FILES['film_capa']['tmp_name'];

        // 4. Cria um nome de arquivo único e seguro para evitar conflitos.
        $nomeOriginal = basename($_FILES['film_capa']['name']);
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $nomeUnico = uniqid('capa_', true) . '.' . $extensao;

        // 5. Monta o caminho completo onde o arquivo será salvo.
        $caminhoFinal = $diretorioUpload . $nomeUnico;

        // 6. Move o arquivo da pasta temporária para o destino final.
        if (move_uploaded_file($arquivoTemporario, $caminhoFinal)) {
            // Se o upload deu certo, guarda o caminho relativo para salvar no banco.
            $caminhoDaCapa = 'img/capas/' . $nomeUnico;
        } else {
            // Se falhar, você pode adicionar uma mensagem de erro aqui.
            $caminhoDaCapa = ''; // Garante que fique vazio se o upload falhar.
        }
    }

    // normaliza e sanitiza valores do POST
    $titulo = trim($_POST['film_titulo'] ?? '');
    $sinopse = trim($_POST['film_sinopse'] ?? '');
    $ano = intval($_POST['film_anoLancamento'] ?? 0);
    $diretor = trim($_POST['film_diretor'] ?? '');
    $genero = trim($_POST['film_genero'] ?? '');
    $_trailer = isset($_POST['film_trailer']) ? trim((string)$_POST['film_trailer']) : null;
    if ($_trailer === '') { $_trailer = null; }

    // construtor de Filme aceita (titulo, sinopse, ano, capa)
    $filme = new Filme(
        $titulo,
        $sinopse,
        $ano,
        diretor: $diretor,
        genero: $genero,
        capa: $caminhoDaCapa, //aqui usamos o nome do arquivo enviado via upload
        trailer: $_trailer
    );

    
    $filme->save();

    // redireciona para evitar reenvio do POST (PRG)
    $self = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $self . '?ok=1');
    exit;
}

// Em GET: busca todos os filmes para exibir
$filmes = Filme::findAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Filme</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/adicionar.css">
</head>
<body class="adicionar">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <div class="container-add">
        <div class="card-add">
            <h1>Adicionar filme</h1>

    <?php // Exibe mensagem simples após salvar (depois do redirect do PRG)
    if (isset($_GET['ok'])): ?>
        <p style="color: #12c25f; font-weight: 600;">Filme adicionado com sucesso.</p>
    <?php endif; ?>



    <!-- action explícita para enviar para esta mesma página -->
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-row">
                <label>Título</label>
                <input type="text" name="film_titulo" required autocomplete="off">
            </div>
            <div class="form-row">
                <label>Ano de Lançamento</label>
                <input type="number" name="film_anoLancamento" required>
            </div>

            <div class="form-row full">
                <label>Sinopse</label>
                <textarea name="film_sinopse" rows="6"></textarea>
            </div>

            <div class="form-row">
                <label>Diretor</label>
                <input type="text" name="film_diretor">
            </div>
            <div class="form-row">
                <label>Gênero</label>
                <select name="film_genero" class="form-select">
                    <option value="">Escolha...</option>
                    <option value="Ação">Ação</option>
                    <option value="Comédia">Comédia</option>
                    <option value="Terror">Terror</option>
                    <option value="Ficção Científica">Ficção Científica</option>
                    <option value="Animação">Animação</option>
                    <option value="Suspense">Suspense</option>
                    <option value="Românce">Românce</option>
                    <option value="Drama">Drama</option>
                    <option value="Guerra">Guerra</option>
                    <option value="Guerra">Crime</option>
                </select>
            </div>

            <div class="form-row full">
                <label>Link do Trailer (YouTube)</label>
                <input type="url" name="film_trailer" autocomplete="off">
            </div>

            <div class="form-row full">
                <label>Capa do Filme</label>
                <input type="file" name="film_capa" accept="image/png, image/jpeg, image/webp">
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn-save">Salvar</button>
        </div>
    </form>
    </div>
    </div>
    <hr>
    <h2 class="lista-title">Lista de filmes</h2>
    <table class="lista-filmes"> <thead>
            <tr>
                <th>Id</th>
                <th>Capa</th>
                <th>Titulo</th>
                <th>Ano</th>
                <th>Gênero</th>
                <th>Diretor</th>
                <th>Trailer</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filmes as $filme): ?>
            <tr>
                <td><?= htmlspecialchars($filme->getId()) ?></td>
                <td>
                    <?php if ($filme->getCapa()): // Só mostra a imagem se houver uma capa ?>
                        <img src="<?= htmlspecialchars($filme->getCapa()) ?>" alt="Capa" width="50">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($filme->getTitulo()) ?></td>
                <td><?= htmlspecialchars($filme->getAnoLancamento()) ?></td>
                <td><?= htmlspecialchars($filme->getGenero()) ?></td>
                <td><?= htmlspecialchars($filme->getDiretor()) ?></td>
                <td><?= htmlspecialchars((string)($filme->getTrailer() ?? '')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>