<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Database;
use App\Model\Avaliacao;
use App\Model\Filme;
use App\Model\User;

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    header('Location: /ProjetoMOD3-limpo/public/auth.php?view=login&err=auth');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /ProjetoMOD3-limpo/public/index.php');
    exit;
}

$filmeId = (int)($_POST['filme_id'] ?? 0);
$nota    = isset($_POST['nota']) ? (int)$_POST['nota'] : null;
$coment  = trim((string)($_POST['comentario'] ?? ''));

if ($filmeId <= 0 || $nota === null) {
    header('Location: /ProjetoMOD3-limpo/public/index.php?err=badreq');
    exit;
}

try {
    $em = Database::getEntityManager();
    $filme = $em->find(Filme::class, $filmeId);
    $user  = $em->find(User::class, (int)$_SESSION['user_id']);
    if (!$filme || !$user) {
        header('Location: /ProjetoMOD3-limpo/public/index.php?err=notfound');
        exit;
    }

    if (Avaliacao::findOneByUserAndFilme($user, $filme)) {
        $back = $_SERVER['HTTP_REFERER'] ?? '/ProjetoMOD3-limpo/public/index.php';
        header('Location: ' . $back . (str_contains($back, '?') ? '&' : '?') . 'err=dupe');
        exit;
    }

    $avaliacao = new Avaliacao($user, $filme, (int)$nota, $coment !== '' ? $coment : null);
    $avaliacao->save();

    $back = $_SERVER['HTTP_REFERER'] ?? '/ProjetoMOD3-limpo/public/index.php';
    header('Location: ' . $back . (str_contains($back, '?') ? '&' : '?') . 'ok=1');
    exit;
} catch (Throwable $e) {
    header('Location: /ProjetoMOD3-limpo/public/index.php?err=save');
    exit;
}
