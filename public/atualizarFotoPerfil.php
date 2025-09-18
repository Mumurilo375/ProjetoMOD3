<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Database;
use App\Model\User;

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: /ProjetoMOD3-limpo/public/auth.php?view=login&err=auth');
    exit;
}

if (!isset($_FILES['foto']) || !is_uploaded_file($_FILES['foto']['tmp_name'])) {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=nofile');
    exit;
}

$maxSize = 2 * 1024 * 1024; // 2MB
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$file = $_FILES['foto'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=upload');
    exit;
}
if ($file['size'] > $maxSize) {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=size');
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowed[$mime])) {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=type');
    exit;
}

$ext = $allowed[$mime];
$baseDir = __DIR__ . '/img/fotoPerfil';
if (!is_dir($baseDir)) { @mkdir($baseDir, 0777, true); }

$slugify = function (string $str): string {
    $s = $str;
    if (function_exists('iconv')) {
        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($conv !== false) { $s = $conv; }
    }
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
    $s = trim($s, '-');
    if ($s === '') { $s = 'usuario'; }
    if (strlen($s) > 40) { $s = substr($s, 0, 40); }
    return $s;
};

try {
    $em = Database::getEntityManager();
    /** @var User|null $user */
    $user = $em->find(User::class, (int)$_SESSION['user_id']);
    if (!$user) {
        header('Location: /ProjetoMOD3-limpo/public/logout.php');
        exit;
    }

    // Monta nome
    $nameSlug = $slugify($user->getNome() ?? 'usuario');
    // Evita caracteres inválidos no Windows, como ':'
    $filename = 'perfil_' . $nameSlug . '_id-' . $user->getId() . '.' . $ext;
    $dest = $baseDir . '/' . $filename;
    $publicPath = '/ProjetoMOD3-limpo/public/img/fotoPerfil/' . $filename;

    // remove foto antiga
    $old = $user->getFotoPerfil();
    if ($old && str_starts_with($old, '/ProjetoMOD3-limpo/public/img/fotoPerfil/')) {
        $oldFs = __DIR__ . str_replace('/ProjetoMOD3-limpo/public', '', $old);
        if (realpath($oldFs) !== realpath($dest) && is_file($oldFs)) {
            @unlink($oldFs);
        }
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=move');
        exit;
    }

    $user->setFotoPerfil($publicPath);
    $em->persist($user);
    $em->flush();

    $_SESSION['user_foto'] = $publicPath;

    header('Location: /ProjetoMOD3-limpo/public/perfil.php?ok=1');
    exit;
} catch (Throwable $e) {
    header('Location: /ProjetoMOD3-limpo/public/perfil.php?err=save');
    exit;
}
