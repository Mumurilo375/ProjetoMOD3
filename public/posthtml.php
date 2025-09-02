<?php
// Página de testes para criar filmes e listar (exemplo de POST/Redirect/GET)
// PRG: evita reenvio de formulário quando o usuário recarrega a página.
use App\Model\Filme;

require_once __DIR__ . '/../vendor/autoload.php';

// Se recebeu POST, cria a entidade e salva no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // construtor de Filme aceita (titulo, sinopse, ano, capa)
    $filme = new Filme(
        $_POST['film_titulo'] ?? '',
        $_POST['film_sinopse'] ?? '',
        intval($_POST['film_ano'] ?? 0),
        // campo film_poster no formulário (caminho relativo ou null)
        $_POST['film_poster'] ?? null
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
    <title>Document</title>
</head>
<body>
    <h1>este arquivo foi criado apenas para tentar fazer um envio de formulario para salvar no banco de dados</h1>

    <?php // Exibe mensagem simples após salvar (depois do redirect do PRG)
    if (isset($_GET['ok'])): ?>
        <p style="color: #12c25f; font-weight: 600;">Filme salvo com sucesso.</p>
    <?php endif; ?>

    <!-- action explícita para enviar para esta mesma página -->
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <label>Titulo</label>
        <input type="text" name="film_titulo">
        <label>Sinopse</label>
        <input type="text" name="film_sinopse">
        <label>Ano</label>
        <input type="text" name="film_ano">
    <label>Caminho do Poster</label>
    <input type="text" name="film_poster" value="<?= htmlspecialchars($_POST['film_poster'] ?? '', ENT_QUOTES) ?>">
        <input type="submit" value="Salvar">
    </form>
    <hr>
    <h2>Obs: este arquivo foi criado apenas com intuito de testes</h2>
    <h3>Lista de filmes</h3>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Titulo</th>
                <th>Sinopse</th>
                <th>Ano</th>
                <th>Caminho da Capa</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filmes as $filme): ?>
            <tr>
                <td> <?= $filme->getId() ?> </td>
                <td> <?= $filme->getTitulo() ?> </td>
                <td> <?= $filme->getSinopse() ?> </td>
                <td> <?= $filme->getAno() ?> </td>
                <td> <?= $filme->getCapa() ?> </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
</body>
</html>