<?php
// Debug: mostra o campo `capa` e testes de existência para filmes 4..7
use App\Model\Filme;
require_once __DIR__ . '/../vendor/autoload.php';

$idsDesejados = [4,5,6,7];
$lista = Filme::findAll();
$map = [];
foreach ($lista as $f) {
    $id = (int)$f->getId();
    if (in_array($id, $idsDesejados, true)) {
        $map[$id] = $f;
        if (count($map) === count($idsDesejados)) break;
    }
}
?><!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Debug capas 4..7</title>
  <style>body{font-family:Inter,Arial;background:#0b0b0b;color:#eee;padding:20px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #222;padding:8px;text-align:left}code{background:#111;padding:2px 6px;border-radius:4px;color:#8fd8ff}</style>
</head>
<body>
  <h1>Debug: campo <code>capa</code> para filmes 4..7</h1>
  <table>
    <thead><tr><th>ID</th><th>Título</th><th>getCapa()</th><th>isUrl</th><th>exists public/</th><th>exists public/img/</th><th>exists public/uploads/capas/</th></tr></thead>
    <tbody>
    <?php foreach ($idsDesejados as $id):
        if (!isset($map[$id])) {
            echo '<tr><td>' . $id . '</td><td colspan="6">Filme não encontrado</td></tr>';
            continue;
        }
        $f = $map[$id];
        $titulo = htmlspecialchars($f->getTitulo(), ENT_QUOTES, 'UTF-8');
        $capa = $f->getCapa();
        $capaDisp = $capa === null ? '<em>null</em>' : '<code>' . htmlspecialchars($capa, ENT_QUOTES, 'UTF-8') . '</code>';
        $capaNorm = is_string($capa) ? str_replace('\\','/',$capa) : '';
        $isUrl = is_string($capaNorm) && preg_match('#^https?://#i', $capaNorm) ? 'sim' : 'não';
        $cand1 = __DIR__ . '/' . ltrim($capaNorm, '/');
        $cand2 = __DIR__ . '/img/' . ltrim($capaNorm, '/');
        $cand3 = __DIR__ . '/uploads/capas/' . ltrim($capaNorm, '/');
        $e1 = is_string($capaNorm) && file_exists($cand1) ? 'sim' : 'não';
        $e2 = is_string($capaNorm) && file_exists($cand2) ? 'sim' : 'não';
        $e3 = is_string($capaNorm) && file_exists($cand3) ? 'sim' : 'não';
        echo "<tr><td>{$id}</td><td>{$titulo}</td><td>{$capaDisp}</td><td>{$isUrl}</td><td>{$e1}</td><td>{$e2}</td><td>{$e3}</td></tr>";
    endforeach; ?>
    </tbody>
  </table>
</body>
</html>
