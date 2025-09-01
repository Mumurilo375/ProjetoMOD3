<?php

use App\Model\User; //importando a classe User do namespace App\Model, para usar a classe User, que está definida em src/Model/User.php, sem isso, o código não funcionaria, pois a classe User não estaria disponível no escopo deste arquivo

require_once __DIR__ . '/../vendor/autoload.php';

//esse if verifica se o formulário foi submetido, ou seja, se há dados enviados via método POST, para então criar um novo usuário e salvá-lo no banco de dados
if ($_POST) { 
    $user = new User(
        name: $_POST['user_name'],
        email: $_POST['user_email'],
        password: $_POST['user_password']
    );
    
    $user->save(); //essa linha chama o método save() da classe User para salvar o novo usuário no banco de dados
}

$users = User::findAll(); //essa linha chama o método estático findAll() da classe User para buscar todos os usuários no banco de dados e armazená-los na variável $users, que será usada para exibir a lista de usuários na tabela abaixo

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
</head>
<body>
    <h1>Cadastro de Usuários</h1>
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
        <?php foreach($users as $user): ?>
            <tr>
                <td><?= $user->getId() ?></td>
                <td><?= $user->getName() ?></td>
                <td><?= $user->getEmail() ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</body>
</html>


