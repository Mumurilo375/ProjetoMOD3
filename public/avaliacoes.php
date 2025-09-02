<?php
// Página simples para cadastrar e listar usuários (apenas exemplo)
use App\Model\User;

require_once __DIR__ . '/../vendor/autoload.php'; // carrega Composer e o autoload do projeto

// Quando o formulário é enviado (via POST), criamos um usuário e salvamos.
// Aqui usamos um exemplo simples sem validação detalhada.
if ($_POST) {
    // usa a factory para criar um usuário com campos mínimos (nome, email, senha)
    $user = User::createFromSignup(trim($_POST['user_name'] ?? ''), trim($_POST['user_email'] ?? ''), $_POST['user_password'] ?? '');
    $user->save();
}

// Busca todos os usuários para mostrar na tabela abaixo
$users = User::findAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
    <style>table{border-collapse:collapse}td,th{border:1px solid #ddd;padding:6px}</style>
</head>
<body>
    <h1>Cadastro de Usuários (exemplo)</h1>
    <!-- Formulário simples: nome, email e senha -->
    <form method="post">
        <label>Nome</label>
        <input type="text" name="user_name">
        <label>Email</label>
        <input type="text" name="user_email">
        <label>Senha</label>
        <input type="text" name="user_password">
        <input type="submit" value="Salvar">
    </form>

    <h3>Lista de Usuários</h3>
    <table>
        <tr><th>Id</th><th>Nome</th><th>Email</th></tr>
        <?php
        // Percorre o array de usuários retornado pelo repositório
        foreach ($users as $user): ?>
            <tr>
                <td><?= $user->getId() ?></td>
                <td><?= $user->getName() ?></td>
                <td><?= $user->getEmail() ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</body>
</html>


