<?php
// Página de testes para criar filmes e listar (exemplo de POST/Redirect/GET)
// PRG: evita reenvio de formulário quando o usuário recarrega a página.

use App\Model\Filme;

require_once __DIR__ . '/../vendor/autoload.php';




// Se recebeu POST, cria a entidade e salva no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // normaliza e sanitiza valores do POST
    $titulo = trim($_POST['film_titulo'] ?? '');
    $sinopse = trim($_POST['film_sinopse'] ?? '');
    $ano = intval($_POST['film_anoLancamento'] ?? 0);
    $diretor = trim($_POST['film_diretor'] ?? '');
    $genero = trim($_POST['film_genero'] ?? '');
    // garante que capa seja sempre uma string (evita TypeError no construtor)
    $capa = trim($_POST['film_capa'] ?? '');

    // construtor de Filme aceita (titulo, sinopse, ano, capa)
    $filme = new Filme(
        $titulo,
        $sinopse,
        $ano,
        diretor: $diretor,
        genero: $genero,
        capa: $capa
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
    <div class="container-add">
        <div class="card-add">
            <h1>Adicionar filme</h1>

    <?php // Exibe mensagem simples após salvar (depois do redirect do PRG)
    if (isset($_GET['ok'])): ?>
        <p style="color: #12c25f; font-weight: 600;">Filme adicionado com sucesso.</p>
    <?php endif; ?>



    <!-- action explícita para enviar para esta mesma página -->
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-grid">
            <div class="form-row">
                <label>Título</label>
                <input type="text" name="film_titulo" value="<?= htmlspecialchars($_POST['film_titulo'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-row">
                <label>Ano de Lançamento</label>
                <input type="text" name="film_anoLancamento" value="<?= htmlspecialchars($_POST['film_anoLancamento'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div class="form-row full">
                <label>Sinopse</label>
                <textarea name="film_sinopse" rows="6"><?= htmlspecialchars($_POST['film_sinopse'] ?? '', ENT_QUOTES) ?></textarea>
            </div>

            <div class="form-row">
                <label>Diretor</label>
                <input type="text" name="film_diretor" value="<?= htmlspecialchars($_POST['film_diretor'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-row">
                <label>Gênero</label>
                <select name="film_genero" class="form-select">
                    <?php $selectedGenre = $_POST['film_genero'] ?? ''; ?>
                    <option value="">Escolha...</option>
                    <option value="Ação" <?= $selectedGenre === 'Ação' ? 'selected' : '' ?>>Ação</option>
                    <option value="Comédia" <?= $selectedGenre === 'Comédia' ? 'selected' : '' ?>>Comédia</option>
                    <option value="Terror" <?= $selectedGenre === 'Terror' ? 'selected' : '' ?>>Terror</option>
                    <option value="Ficção Científica" <?= $selectedGenre === 'Ficção Científica' ? 'selected' : '' ?>>Ficção Científica</option>
                    <option value="Animação" <?= $selectedGenre === 'Animação' ? 'selected' : '' ?>>Animação</option>
                    <option value="Suspense" <?= $selectedGenre === 'Suspense' ? 'selected' : '' ?>>Suspense</option>
                    <option value="Românce" <?= $selectedGenre === 'Românce' ? 'selected' : '' ?>>Românce</option>
                </select>
            </div>

            <div class="form-row full">
                <label>Caminho da Capa</label>
                <input type="text" name="film_capa" value="<?= htmlspecialchars($_POST['film_capa'] ?? '', ENT_QUOTES) ?>">
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
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Titulo</th>
                <th>Sinopse</th>
                <th>Ano de Lançamento</th>
                <th>Diretor</th>
                <th>Gênero</th>
                <th>Capa</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filmes as $filme): ?>
            <tr>
                <td> <?= $filme->getId() ?> </td>
                <td> <?= $filme->getTitulo() ?> </td>
                <td> <?= $filme->getSinopse() ?> </td>
                <td> <?= $filme->getAnoLancamento() ?> </td>
                <td> <?= $filme->getDiretor() ?> </td>
                <td> <?= $filme->getGenero() ?> </td>
                <td> <?= $filme->getCapa() ?> </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
</body>
</html>