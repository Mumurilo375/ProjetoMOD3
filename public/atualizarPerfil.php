<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Model\User;

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: /ProjetoMOD3-limpo/public/auth.php?view=login&err=auth');
    exit;
}

$name = trim((string)($_POST['nome'] ?? ''));
if ($name === '') {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=nome');
    exit;
}

try {
    $em = Database::getEntityManager();
    /** @var User|null $user */
    $user = $em->find(User::class, (int)$_SESSION['user_id']);
    if (!$user) {
        header('Location: /ProjetoMOD3-limpo/public/logout.php');
        exit;
    }

    $user->setNome($name);
    $em->persist($user);
    $em->flush();

    // Atualiza sessão
    $_SESSION['user_nome'] = $user->getNome();

    header('Location: /ProjetoMOD3-limpo/public/perfil.php?ok=1');
    exit;
} catch (Throwable $e) {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=save');
    exit;
}
